<?php
/**
 * Archive Manager for Nextcloud
 *
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022 Claus-Justus Heine <himself@claus-justus-heine.de>
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

use OCA\FilesArchive\Storage\ArchiveStorage;
use OCA\FilesArchive\Exceptions;
use OCA\FilesArchive\Service\ArchiveService;
use OCA\FilesArchive\Constants;

/**
 * AJAX end-point for archive operations and info.
 */
class ArchiveController extends Controller
{
  use \OCA\FilesArchive\Traits\ResponseTrait;
  use \OCA\FilesArchive\Traits\LoggerTrait;
  use \OCA\FilesArchive\Traits\UtilTrait;

  public const ARCHIVE_STATUS_OK = 0;
  public const ARCHIVE_STATUS_TOO_LARGE = (1 << 0);
  public const ARCHIVE_STATUS_BOMB = (1 << 1);

  /** @var string */
  private $userId;

  /** @var IRootFolder */
  private $rootFolder;

  /** @var ArchiveService */
  private $archiveService;

  /** @var IAppContainer */
  private $appContainer;

  /** @var null|int */
  private $archiveSizeLimit = null;

  /** @var int */
  private $archiveBombLimit = Constants::DEFAULT_ADMIN_ARCHIVE_SIZE_LIMIT;

  // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
  public function __construct(
    ?string $appName,
    IRequest $request,
    ?string $userId,
    IConfig $cloudConfig,
    LoggerInterface $logger,
    IL10N $l10n,
    IRootFolder $rootFolder,
    IAppContainer $appContainer,
    ArchiveService $archiveService,
  ) {
    parent::__construct($appName, $request);
    $this->logger = $logger;
    $this->l = $l10n;
    $this->userId = $userId;
    $this->rootFolder = $rootFolder;
    $this->appContainer = $appContainer;
    $this->archiveService = $archiveService;

    $this->archiveBombLimit = $cloudConfig->getAppValue(
      $this->appName, SettingsController::ARCHIVE_SIZE_LIMIT, Constants::DEFAULT_ADMIN_ARCHIVE_SIZE_LIMIT);
    $this->archiveSizeLimit = $cloudConfig->getUserValue(
      $this->userId, $this->appName, SettingsController::ARCHIVE_SIZE_LIMIT, null);

    $this->archiveService->setSizeLimit($this->actualArchiveSizeLimit());
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
    try {
      $this->archiveService->open($archiveFile, password: $passPhrase);
      $archiveInfo = $this->archiveService->getArchiveInfo();
      $httpStatus = Http::STATUS_OK;
    } catch (Exceptions\ArchiveTooLargeException $e) {
      $this->logException($e);
      $archiveStatus = self::ARCHIVE_STATUS_TOO_LARGE;
      $archiveInfo = $e->getArchiveInfo();
      if ($archiveInfo[ArchiveService::ARCHIVE_INFO_ORIGINAL_SIZE] > $this->archiveBombLimit) {
        $archiveStatus |= self::ARCHIVE_STATUS_BOMB;
      }
    } catch (Throwable $e) {
      $this->logException($e);
    }

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
   * @param string $targetPath
   *
   * @param null|string $passPhrase
   *
   * @param bool $stripCommonPathPrefix
   *
   * @return DataResponse
   *
   * @NoAdminRequired
   */
  public function extract(string $archivePath, string $targetPath, ?string $passPhrase = null, bool $stripCommonPathPrefix = false):DataResponse
  {
    $archivePath = urldecode($archivePath);
    $targetPath = urldecode($targetPath);

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
    } catch (Exceptions\ArchiveTooLargeException $e) {
      $uncompressedSize = $e->getActualSize();
      if ($uncompressedSize > $this->archiveBombLimit) {
        return self::grumble($this->l->t('The archive-file "%1$s" appears to be a zip-bomb: uncompressed size %2$s > admin limit %3$s.', [
          $archivePath, $this->formatStorageValue($uncompressedSize), $this->formatStorageValue($this->archiveBombLimit)
        ]));
      } else {
        return self::grumble($this->l->t('The archive-file "%1$s" is too large: uncompressed size %2$s > user limit %3$s.', [
          $archivePath, $this->formatStorageValue($uncompressedSize), $this->formatStorageValue($this->archiveSizeLimit)
        ]));
      }
    }

    $targetInfo = pathinfo($targetPath);
    try {
      /** @var Folder $targetParent */
      $targetParent = $userFolder->get($targetInfo['dirname']);
    } catch (FileNotFoundException $e) {
      return self::grumble($this->l->t('Unable to open the target parent-folder "%s".', $targetInfo['dirname']));
    }
    $targetInternalPath = $targetParent->getInternalPath() . Constants::PATH_SEPARATOR . $targetInfo['filename'];
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
      return self::grumble($this->l->t('Unable to extract "%1$s" to "%2$s": "%3$s".', [
        $archivePath, $targetPath, $t->getMessage()
      ]));
    }

    return self::dataResponse([
      'messages' => [ $this->l->t('Extracting "%1$s" to "%2$s" succeeded.', [ $archivePath, $targetPath ]) ],
    ]);
  }

  /** @return int */
  private function actualArchiveSizeLimit():int
  {
    return min($this->archiveBombLimit, $this->archiveSizeLimit ?? PHP_INT_MAX);
  }
}
