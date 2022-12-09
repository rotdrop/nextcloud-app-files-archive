<?php
/**
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright Copyright (c) 2022 Claus-Justus Heine
 * @license GNU AGPL version 3 or any later version
 *
 * "stolen" from files_zip Copyright (c) 2021 Julius Härtl <jus@bitgrid.net>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace OCA\FilesArchive\Service;

use DateTime;

use OCP\Files\Node;
use OCP\Files\Folder;
use OCP\Notification\IManager;
use OCP\Notification\INotification;
use Psr\Log\LoggerInterface as ILogger;
use OCP\IUserSession;

use OCA\FilesArchive\BackgroundJob\ArchiveJob;
use OCA\FilesArchive\Notification\Notifier;
use OCA\FilesArchive\Constants;

/** Service class for notification management. */
class NotificationService
{
  use \OCA\RotDrop\Toolkit\Traits\LoggerTrait;
  use \OCA\RotDrop\Toolkit\Traits\UserRootFolderTrait;

  /** @var string */
  protected $appName;

  /** @var null|string */
  protected $userId = null;

  /** @var IManager */
  private $notificationManager;

  // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
  public function __construct(
    string $appName,
    IManager $notificationManager,
    ILogger $logger,
    IUserSession $userSession,
  ) {
    $this->appName = $appName;
    $this->notificationManager = $notificationManager;
    $this->logger = $logger;
    $user = $userSession->getUser();
    if (!empty($user)) {
      $this->userId = $user->getUID();
    }
  }
  // phpcs:enable

  /**
   * @param string $userId
   *
   * @param Node $sourceNode
   *
   * @param string $destinationPath
   *
   * @param string $target ArchiveJob::TARGET_MOUNT or
   * ArchiveJob::TARGET_EXTRACT.
   *
   * @return void
   */
  public function sendNotificationOnPending(
    string $userId,
    Node $sourceNode,
    string $destinationPath,
    string $target,
  ):void {
    $this->userId = $userId;
    $sourcePath = $sourceNode->getPath();
    $notification = $this->buildNotification(
      Notifier::TYPE_SCHEDULED,
      $target,
      $userId,
      $sourcePath,
      $sourceNode->getId(),
      $destinationPath,
    )
      ->setDateTime(new DateTime());
    $this->notificationManager->notify($notification);
    $this->logInfo('NOTIFY PENDING');
  }

  /**
   * @param ArchiveJob $job
   *
   * @param Folder $folder
   *
   * @return void
   */
  public function sendNotificationOnSuccess(ArchiveJob $job, Folder $folder):void
  {
    $userId = $job->getUserId();
    $this->userId = $userId;
    $destinationPath = $job->getDestinationPath();
    $sourcePath = $job->getSourcePath();
    $sourceId = $job->getSourceId();
    $target = $job->getTarget();

    $this->notificationManager->markProcessed($this->buildNotification(
      Notifier::TYPE_SCHEDULED,
      $target,
      $userId,
      $sourcePath,
      $sourceId,
      $destinationPath,
    ));

    $notification = $this->buildNotification(
      Notifier::TYPE_SUCCESS,
      $target,
      $userId,
      $sourcePath,
      $sourceId,
      $destinationPath,
    );
    $subject = $notification->getSubject();
    $subjectParameters = $notification->getSubjectParameters();
    $subjectParameters['destinationId'] = $folder->getId();

    $notification
      ->setDateTime(new DateTime())
      ->setSubject($subject, $subjectParameters);
    $this->notificationManager->notify($notification);
    $this->logInfo('NOTIFY SUCCESS');
  }

  /**
   * @param ArchiveJob $job
   *
   * @param null|string $errorMessage Optional error message.
   *
   * @return void
   */
  public function sendNotificationOnFailure(
    ArchiveJob $job,
    ?string $errorMessage = null,
  ):void {
    $userId = $job->getUserId();
    $this->userId = $userId;
    $destinationPath = $job->getDestinationPath();
    $sourcePath = $job->getSourcePath();
    $sourceId = $job->getSourceId();
    $target = $job->getTarget();

    $this->notificationManager->markProcessed($this->buildNotification(
      Notifier::TYPE_SCHEDULED,
      $target,
      $userId,
      $sourcePath,
      $sourceId,
      $destinationPath,
    ));

    $notification = $this->buildNotification(
      Notifier::TYPE_FAILURE,
      $target,
      $userId,
      $sourcePath,
      $sourceId,
      $destinationPath,
      $errorMessage,
    );
    $notification
      ->setDateTime(new DateTime())
      ->setObject('job', (string)$job->getId());
    $this->notificationManager->notify($notification);
    $this->logInfo('NOTIFY FAILURE');
  }

  /**
   * @param int $type
   *
   * @param string $target
   *
   * @param string $userId
   *
   * @param string $sourcePath
   *
   * @param int $sourceId
   *
   * @param string $destinationPath
   *
   * @param null|string $errorMessage Optional error message when emitting
   * failure notifications.
   *
   * @return INotification
   */
  private function buildNotification(
    int $type,
    string $target,
    string $userId,
    string $sourcePath,
    int $sourceId,
    string $destinationPath,
    ?string $errorMessage = null,
  ):INotification {
    $type |= ($target == ArchiveJob::TARGET_MOUNT ? Notifier::TYPE_MOUNT : Notifier::TYPE_EXTRACT);
    $notification = $this->notificationManager->createNotification();
    $notification->setUser($userId)
      ->setApp($this->appName)
      ->setObject('target', md5($destinationPath))
      ->setSubject((string)$type, [
        'sourceId' => $sourceId,
        'sourcePath' => $sourcePath,
        'sourceDirectory' => dirname($sourcePath),
        'sourceDirectoryName' => basename(dirname($sourcePath)),
        'sourceBaseName' => basename($sourcePath),
        'destinationPath' => $destinationPath,
        'destinationDirectory' => dirname($destinationPath),
        'destinationDirectoryName' => basename(dirname($destinationPath)),
        'destinationBaseName' => basename($destinationPath),
        'errorMessage' => $errorMessage,
      ]);
    return $notification;
  }
}
