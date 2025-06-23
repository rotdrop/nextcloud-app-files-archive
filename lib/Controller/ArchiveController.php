<?php
/**
 * Archive Manager for Nextcloud
 *
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022-2025 Claus-Justus Heine <himself@claus-justus-heine.de>
 * @license AGPL-3.0-or-later
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *"
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace OCA\FilesArchive\Controller;

use Throwable;

use Psr\Log\LoggerInterface;
use OCP\IRequest;
use OCP\IConfig;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\Response;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\IAppContainer;
use OCP\Files\File;
use OCP\Files\Folder;
use OCP\Files\IRootFolder;
use OCP\Files\NotFoundException as FileNotFoundException;
use OCP\Lock\ILockingProvider;
use OCP\IL10N;
use OCP\IPreview;

use OCA\FilesArchive\Toolkit\Exceptions as ToolkitExceptions;

use OCA\FilesArchive\Service\ArchiveServiceFactory;
use OCA\FilesArchive\Toolkit\Service\ArchiveService;
use OCA\FilesArchive\Storage\ArchiveStorage;
use OCA\FilesArchive\Constants;

/**
 * AJAX endpoint for archive operations and info.
 */
class ArchiveController extends Controller
{
  use \OCA\FilesArchive\Toolkit\Traits\UtilTrait;
  use \OCA\FilesArchive\Toolkit\Traits\ResponseTrait;
  use \OCA\FilesArchive\Toolkit\Traits\LoggerTrait;
  use \OCA\FilesArchive\Toolkit\Traits\NodeTrait;
  use TargetPathTrait;
  use ArchiveSizeLimitTrait;

  public const ARCHIVE_STATUS_OK = 0;
  public const ARCHIVE_STATUS_TOO_LARGE = (1 << 0);
  public const ARCHIVE_STATUS_BOMB = (1 << 1);

  public const ARCHIVE_INFO_DEFAULT_MOUNT_POINT = ArchiveService::ARCHIVE_INFO_DEFAULT_MOUNT_POINT;
  public const ARCHIVE_INFO_DEFAULT_TARGET_BASE_NAME = 'defaultTargetBaseName';

  /** @var string */
  private string $targetBaseNameTemplate;

  /** @var string */
  private string $mountPointTemplate;

  /** @var bool */
  private bool $autoRenameExtractTarget = false;

  /** @var null|int */
  private ?int $archiveSizeLimit = null;

  /** @var int */
  private int $archiveBombLimit = Constants::DEFAULT_ADMIN_ARCHIVE_SIZE_LIMIT;

  // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
  public function __construct(
    ?string $appName,
    IRequest $request,
    IConfig $cloudConfig,
    private ArchiveServiceFactory $archiveServiceFactory,
    protected IAppContainer $appContainer,
    protected IL10N $l,
    protected IPreview $previewManager,
    protected IRootFolder $rootFolder,
    protected LoggerInterface $logger,
    protected string $userId,
  ) {
    parent::__construct($appName, $request);

    $this->archiveBombLimit = $cloudConfig->getAppValue(
      $this->appName, SettingsController::ARCHIVE_SIZE_LIMIT, Constants::DEFAULT_ADMIN_ARCHIVE_SIZE_LIMIT);
    $this->archiveSizeLimit = $cloudConfig->getUserValue(
      $this->userId, $this->appName, SettingsController::ARCHIVE_SIZE_LIMIT, null);

    $this->targetBaseNameTemplate = $cloudConfig->getUserValue(
      $this->userId, $this->appName, SettingsController::EXTRACT_TARGET_TEMPLATE, SettingsController::FOLDER_TEMPLATE_DEFAULT);

    $this->mountPointTemplate = $cloudConfig->getUserValue(
      $this->userId, $this->appName, SettingsController::MOUNT_POINT_TEMPLATE, SettingsController::FOLDER_TEMPLATE_DEFAULT);

    $this->autoRenameExtractTarget = (bool)$cloudConfig->getUserValue(
      $this->userId, $this->appName, SettingsController::EXTRACT_TARGET_AUTO_RENAME, false);
  }
  // phpcs:enable

  /**
   * @param string $archivePath
   *
   * @param null|string $passPhrase
   *
   * @return DataResponse
   *
   * @NoAdminRequired
   */
  public function info(string $archivePath, ?string $passPhrase = null):DataResponse
  {
    $archivePath = urldecode($archivePath);

    $userFolder = $this->rootFolder->getUserFolder($this->userId);
    if (empty($userFolder)) {
      return self::grumble($this->l->t('The user folder for user "%s" could not be opened.', $this->userId));
    }
    try {
      /** @var File $archiveFile */
      $archiveFile = $userFolder->get($archivePath);
    } catch (FileNotFoundException $e) {
      return self::grumble($this->l->t('The archive file "%s" could not be found on the server.', $archivePath));
    }

    $e = null;
    $archiveStatus = self::ARCHIVE_STATUS_OK;
    $httpStatus = Http::STATUS_BAD_REQUEST;
    $messages = [];
    $archiveInfo = [];
    /** @var ArchiveService $archiveService */
    try {
      $archiveService = $this->archiveServiceFactory->get($archiveFile);
      $archiveService->setSizeLimit($this->actualArchiveSizeLimit());
      $archiveService->open($archiveFile, password: $passPhrase);
      $archiveInfo = $archiveService->getArchiveInfo();
      $httpStatus = Http::STATUS_OK;
    } catch (ToolkitExceptions\ArchiveTooLargeException $e) {
      $this->logException($e);
      $archiveStatus = self::ARCHIVE_STATUS_TOO_LARGE;
      $uncompressedSize = $e->getActualSize();
      if ($uncompressedSize > $this->archiveBombLimit) {
        $archiveStatus |= self::ARCHIVE_STATUS_BOMB;
      }
    } catch (Throwable $e) {
      $this->logException($e);
    }

    // tweak the mount point proposal according to the user preferences
    $archiveInfo[ArchiveService::ARCHIVE_INFO_DEFAULT_MOUNT_POINT] = $this->defaultMountPointName($archiveFile->getName());
    $archiveInfo['defaultTargetBaseName'] = $this->defaultTargetBaseName($archiveFile->getName());

    if (!empty($e)) {
      $exceptionMessage = $e->getMessage();
      if (empty($exceptionMessage)) {
        $messages[] = $this->l->t('Unable to open the archive file "%s": %s.', [
          $archivePath, get_class($e)
        ]);
      } else {
        $messages[] = $this->l->t('Error: %s', $exceptionMessage);
      }
    }

    return self::dataResponse([
      'messages' => $messages,
      'archiveStatus' => $archiveStatus,
      'archiveInfo' => $archiveInfo,
    ], $httpStatus);
  }

  /**
   * @param string $archivePath
   *
   * @param null|string $targetPath
   *
   * @param null|string $passPhrase
   *
   * @param bool $stripCommonPathPrefix
   *
   * @return DataResponse
   *
   * @NoAdminRequired
   */
  public function extract(string $archivePath, ?string $targetPath, ?string $passPhrase = null, bool $stripCommonPathPrefix = false):DataResponse
  {
    $archivePath = urldecode($archivePath);
    if ($targetPath) {
      $targetPath = urldecode($targetPath);
    }

    $userFolder = $this->rootFolder->getUserFolder($this->userId);
    try {
      /** @var File $archiveFile */
      $archiveFile = $userFolder->get($archivePath);
    } catch (FileNotFoundException $e) {
      return self::grumble($this->l->t('Unable to open the archive file "%s".', $archivePath));
    }

    try {
      $archiveStorage = new ArchiveStorage([
        ArchiveStorage::PARAMETER_ARCHIVE_FILE => $archiveFile,
        ArchiveStorage::PARAMETER_ARCHIVE_PASS_PHRASE => $passPhrase,
        ArchiveStorage::PARAMETER_APP_CONTAINER => $this->appContainer,
        ArchiveStorage::PARAMETER_ARCHIVE_SIZE_LIMIT => $this->actualArchiveSizeLimit(),
        ArchiveStorage::PARAMETER_STRIP_COMMON_PATH_PREFIX => $stripCommonPathPrefix,

      ]);
    } catch (ToolkitExceptions\ArchiveTooLargeException $e) {
      $uncompressedSize = $e->getActualSize();
      if ($uncompressedSize > $this->archiveBombLimit) {
        return self::grumble($this->l->t('The archive file "%1$s" appears to be a zip-bomb: uncompressed size %2$s > admin limit %3$s.', [
          $archivePath, $this->formatStorageValue($uncompressedSize), $this->formatStorageValue($this->archiveBombLimit)
        ]));
      } else {
        return self::grumble($this->l->t('The archive file "%1$s" is too large: uncompressed size %2$s > user limit %3$s.', [
          $archivePath, $this->formatStorageValue($uncompressedSize), $this->formatStorageValue($this->archiveSizeLimit)
        ]));
      }
    }

    list(
      'path' => $targetPath,
      'baseName' => $targetBaseName,
      'dirName' => $targetDirName,
    ) = $this->targetPathInfo($targetPath, $archivePath, 'extract');

    try {
      /** @var Folder $targetParent */
      $targetParent = $userFolder->get($targetDirName);
    } catch (Throwable $t) {
      $this->logException($e);
      return self::grumble($this->l->t('Unable to open the target parent folder "%s".', $targetDirName));
    }

    $nonExistingTarget = $targetParent->getNonExistingName($targetBaseName);
    if ($nonExistingTarget != $targetBaseName) {
      if (!$this->autoRenameExtractTarget) {
        return self::grumble($this->l->t('The target folder "%s" already exists and auto-rename is not enabled.', $targetPath));
      }
      $targetPath = $targetDirName . Constants::PATH_SEPARATOR . $nonExistingTarget;
      $targetBaseName = $nonExistingTarget;
    }

    $targetInternalPath = $targetParent->getInternalPath() . Constants::PATH_SEPARATOR . $targetBaseName;
    $targetStorage = $targetParent->getStorage();

    /** @var ILockingProvider $lockingProvider */
    $lockingProvider = $this->appContainer->get(ILockingProvider::class);

    $locked = false;
    try {
      $targetStorage->acquireLock($targetInternalPath, ILockingProvider::LOCK_EXCLUSIVE, $lockingProvider);
      $locked = true;
      $targetStorage->copyFromStorage($archiveStorage, '/', $targetInternalPath);
      $targetStorage->releaseLock($targetInternalPath, ILockingProvider::LOCK_EXCLUSIVE, $lockingProvider);
      $targetStorage->getScanner()->scan($targetInternalPath);
    } catch (Throwable $t) {
      $this->logException($t);
      if ($locked) {
        try {
          $targetStorage->releaseLock($targetInternalPath, ILockingProvider::LOCK_EXCLUSIVE, $lockingProvider);
        } catch (Throwable $t) {
          $this->logException($t, 'Unable to unlock ' . $targetInternalPath);
        }
      }
      try {
        // try to cleanup if possible
        $targetStorage->getScanner()->scan($targetInternalPath);
        $targetFolder = $userFolder->get($targetPath);
        $targetFolder->delete();
      } catch (FileNotFoundException $e) {
        // really ignore this one: nothing to be cleaned up
      } catch (Throwable $t) {
        $this->logException($t, 'Unable to cleanup target path.');
        // otherwise ignore
      }

      return self::grumble($this->l->t('Unable to extract "%1$s" to "%2$s": "%3$s".', [
        $archivePath, $targetPath, $t->getMessage()
      ]));
    }

    /** @var Folder $targetFolder */
    $targetFolder = $userFolder->get($targetPath);

    return self::dataResponse([
      'archivePath' => $archivePath,
      'targetFileId' => $targetFolder->getId(),
      'targetPath' => $targetPath,
      'targetFolder' => $this->formatNode($targetFolder),
      'messages' => [ $this->l->t('Extracting "%1$s" to "%2$s" succeeded.', [ $archivePath, $targetPath ]) ],
    ]);
  }
}
