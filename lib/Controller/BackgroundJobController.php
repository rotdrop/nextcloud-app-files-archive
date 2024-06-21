<?php
/**
 * Archive Manager for Nextcloud
 *
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022, 2023, 2024 Claus-Justus Heine <himself@claus-justus-heine.de>
 * @license AGPL-3.0-or-later
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
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

use OC\Files\Storage\Wrapper\Wrapper as WrapperStorage;

use Psr\Log\LoggerInterface;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\Response;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;
use OCP\IConfig;
use OCP\IL10N;
use OCP\BackgroundJob\IJobList;
use OCP\Files\IRootFolder;
use OCP\Files\File;

use OCA\FilesArchive\Toolkit\Service\UserScopeService;

use OCA\FilesArchive\BackgroundJob\ArchiveJob;
use OCA\FilesArchive\Service\NotificationService;
use OCA\FilesArchive\Constants;

/**
 * Schedule background jobs for archive mounting or extraction.
 */
class BackgroundJobController extends Controller
{
  use \OCA\FilesArchive\Toolkit\Traits\UtilTrait;
  use \OCA\FilesArchive\Toolkit\Traits\ResponseTrait;
  use \OCA\FilesArchive\Toolkit\Traits\LoggerTrait;
  use \OCA\FilesArchive\Toolkit\Traits\UserRootFolderTrait;
  use TargetPathTrait;

  const OPERATION_MOUNT = ArchiveJob::TARGET_MOUNT;
  const OPERATION_EXTRACT = ArchiveJob::TARGET_EXTRACT;

  /** @var string */
  private string $mountPointTemplate;

  // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
  public function __construct(
    ?string $appName,
    IRequest $request,
    protected string $userId,
    protected LoggerInterface $logger,
    protected IL10N $l,
    private IConfig $cloudConfig,
    protected IRootFolder $rootFolder,
    private IJobList $jobList,
    private NotificationService $notificationService,
    private UserScopeService $userScopeService,
  ) {
    parent::__construct($appName, $request);

    $this->mountPointTemplate = $cloudConfig->getUserValue(
      $this->userId, $this->appName, SettingsController::MOUNT_POINT_TEMPLATE, SettingsController::FOLDER_TEMPLATE_DEFAULT);
  }
  // phpcs:enable

  /**
   * @param string $operation
   *
   * @param string $archivePath
   *
   * @param null|string $destinationPath
   *
   * @param null|string $passPhrase
   *
   * @param null|bool $stripCommonPathPrefix
   *
   * @return DataResponse
   *
   * @NoAdminRequired
   */
  public function schedule(
    string $operation,
    string $archivePath,
    ?string $destinationPath = null,
    ?string $passPhrase = null,
    ?bool $stripCommonPathPrefix = null,
  ):DataResponse {
    $archivePath = urldecode($archivePath);
    if ($destinationPath) {
      $destinationPath = urldecode($destinationPath);
    }

    /** @var File $archiveNode */
    $archiveNode = $this->getUserFolder()->get($archivePath);

    $destinationPathInfo = $this->targetPathInfo($destinationPath, $archivePath, $operation);
    $destinationParentPath = $destinationPathInfo['dirName'];
    $destinationParentFolder = $this->getUserFolder()->get($destinationParentPath);
    $destinationPath = $destinationPathInfo['path'];

    $needsAuthentication = $archiveNode->getMountPoint()->getOption('authenticated', false)
      || $destinationParentFolder->getMountPoint()->getOption('authenticated', false);

    if ($needsAuthentication) {
      list('passphrase' => $tokenSecret) = $this->userScopeService->getAuthToken();
    }

    $this->jobList->add(ArchiveJob::class, [
      ArchiveJob::TARGET_KEY => $operation,
      ArchiveJob::USER_ID_KEY => $this->userId,
      ArchiveJob::SOURCE_PATH_KEY => $archivePath,
      ArchiveJob::SOURCE_ID_KEY => $archiveNode->getId(),
      ArchiveJob::DESTINATION_PATH_KEY => $destinationPath,
      ArchiveJob::ARCHIVE_PASSPHRASE_KEY => $passPhrase,
      ArchiveJob::STRIP_COMMON_PATH_PREFIX_KEY => $stripCommonPathPrefix,
      ArchiveJob::NEEDS_AUTHENTICATION_KEY => $needsAuthentication ?? false,
      ArchiveJob::AUTH_TOKEN_KEY => $tokenSecret ?? null
    ]);

    $this->notificationService->sendNotificationOnPending($this->userId, $archiveNode, $destinationPathInfo['path'], $operation);

    return self::dataResponse([
      'jobType' => $operation,
      'targetPath' => $destinationPath,
      'messages' => [
        $operation == self::OPERATION_MOUNT
        ? $this->l->t('Archive background mount job scheduled successfully.')
        : $this->l->t('Archive background extraction job scheduled successfully.'),
      ],
    ]);
  }

  /**
   * Cancel any pending background job matching the given criteria.
   *
   * @param string $operation
   *
   * @param string $archivePath
   *
   * @param null|string $destinationPath
   *
   * @return DataResponse
   *
   * @NoAdminRequired
   */
  public function cancel(
    string $operation,
    string $archivePath,
    ?string $destinationPath = null,
  ):DataResponse {
    $archivePath = urldecode($archivePath);
    if ($destinationPath) {
      $destinationPath = urldecode($destinationPath);
    }
    $failed = [];
    $removed = [];
    $messages = [];
    /** @var ArchiveJob $job */
    $jobs = $this->jobList->getJobsIterator(ArchiveJob::class, limit: null, offset: 0);
    foreach ($jobs as $job) {
      $this->logInfo('PATH ' . $archivePath . ' DST ' . $destinationPath . ' DATA ' . print_r($job->getArgument(), true));
      if ($job->getUserId() == $this->userId
          && $job->getSourcePath() == $archivePath
          && $job->getTarget() == $operation
          && ($destinationPath === null || $job->getDestinationPath() === $destinationPath)) {
        $jobArguments = $job->getArgument();
        $this->jobList->remove($job, $jobArguments);
        if ($this->jobList->has($job, $jobArguments)) {
          $failed[] = $job;
          $messages[] = $this->l->t('Cancelling %s-job for archive file "%s" failed.', [
            $operation,
            $archivePath,
          ]);
        } else {
          $removed[] = $jobArguments;
          $this->notificationService->deleteScheduledJobNotification(
            $job->getUserId(),
            $job->getTarget(),
            $job->getSourceId(),
          );
        }
      }
    }
    $status = count($failed) > 0 ? Http::STATUS_EXPECTATION_FAILED : Http::STATUS_OK;
    return self::dataResponse([
      'removed' => $removed,
      'failed' => $failed,
      'messages' => $messages,
    ], $status);
  }

  /**
   * Return the list of pending mounts for the user.
   *
   * @param string $archivePath
   *
   * @param null|string $destinationPath
   *
   * @return DataResponse
   *
   * @NoAdminRequired
   */
  public function status(
    string $archivePath,
    ?string $destinationPath = null,
  ):DataResponse {
    $archivePath = urldecode($archivePath);
    if ($destinationPath) {
      $destinationPath = urldecode($destinationPath);
    }
    $result = [];
    /** @var ArchiveJob $job */
    $jobs = $this->jobList->getJobsIterator(ArchiveJob::class, limit: null, offset: 0);
    foreach ($jobs as $job) {
      $this->logInfo('PATH ' . $archivePath . ' DST ' . $destinationPath . ' DATA ' . print_r($job->getArgument(), true));
      if ($job->getUserId() == $this->userId
          && $job->getSourcePath() == $archivePath
          && ($destinationPath === null || $job->getDestinationPath() === $destinationPath)) {
        $result[] = $job->getArgument();
      }
    }

    return self::dataResponse($result);
  }
}
