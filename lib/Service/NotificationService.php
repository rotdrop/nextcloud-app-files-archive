<?php
/**
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright Copyright (c) 2022, 2024 Claus-Justus Heine
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
use InvalidArgumentException;

use OCP\Files\Node;
use OCP\Files\Folder;
use OCP\Notification\IManager;
use OCP\Notification\INotification;
use Psr\Log\LoggerInterface as ILogger;

use OCA\FilesArchive\BackgroundJob\ArchiveJob;
use OCA\FilesArchive\Notification\Notifier;
use OCA\FilesArchive\Constants;

/** Service class for notification management. */
class NotificationService
{
  use \OCA\FilesArchive\Toolkit\Traits\LoggerTrait;

  // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
  public function __construct(
    protected $appName,
    private IManager $notificationManager,
    protected ILogger $logger
  ) {
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
    $sourcePath = $sourceNode->getPath();
    $notification = $this->buildNotification(
      Notifier::TYPE_SCHEDULED,
      $target,
      $userId,
      sourcePath: $sourcePath,
      sourceId: $sourceNode->getId(),
      destinationPath: $destinationPath,
    );

    $this->notificationManager->notify($notification);
  }

  /**
   * @param ArchiveJob $job
   *
   * @param Folder $folder
   *
   * @return void
   */
  public function sendNotificationOnSuccess(ArchiveJob $job):void
  {
    $userId = $job->getUserId();
    $destinationId = $job->getDestinationId();
    $destinationPath = $job->getDestinationPath();
    $sourcePath = $job->getSourcePath();
    $sourceId = $job->getSourceId();
    $target = $job->getTarget();

    $this->deleteScheduledJobNotification(
      $userId,
      target: $target,
      sourceId: $sourceId,
    );

    $notification = $this->buildNotification(
      Notifier::TYPE_SUCCESS,
      $target,
      $userId,
      sourcePath: $sourcePath,
      sourceId: $sourceId,
      destinationPath: $destinationPath,
      destinationId: $destinationId,
    );

    $this->notificationManager->notify($notification);
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
    $destinationPath = $job->getDestinationPath();
    $sourcePath = $job->getSourcePath();
    $sourceId = $job->getSourceId();
    $target = $job->getTarget();

    $this->deleteNotification(
      Notifier::TYPE_SCHEDULED,
      $userId,
      sourceId: $sourceId,
      target: $target,
    );

    $notification = $this->buildNotification(
      Notifier::TYPE_FAILURE,
      $target,
      $userId,
      sourcePath: $sourcePath,
      sourceId: $sourceId,
      destinationPath: $destinationPath,
      errorMessage: $errorMessage,
    );
    $notification->setObject('job', (string)$job->getId());

    $this->notificationManager->notify($notification);
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
   * @param int $destinationId
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
    int $destinationId = -1,
    ?string $errorMessage = null,
  ):INotification {
    $type |= ($target == ArchiveJob::TARGET_MOUNT ? Notifier::TYPE_MOUNT : Notifier::TYPE_EXTRACT);

    if ($type & (Notifier::TYPE_SCHEDULED|Notifier::TYPE_FAILURE|Notifier::TYPE_CANCELLED)) {
      $objectType = 'sourceId';
      $objectId = $sourceId;
    } else {
      $objectType = 'destinationId';
      $objectId = $destinationId;
    }

    /** @var INotification $notification */
    $notification = $this->notificationManager->createNotification();
    $notification->setUser($userId)
      ->setApp($this->appName)
      ->setObject($objectType, (string)$objectId);

    $parameters = [
      'sourceId' => $sourceId,
      'sourcePath' => $sourcePath,
      'sourceDirectory' => dirname($sourcePath),
      'sourceDirectoryName' => basename(dirname($sourcePath)),
      'sourceBaseName' => basename($sourcePath),
      'destinationPath' => $destinationPath,
      'destinationDirectory' => dirname($destinationPath),
      'destinationDirectoryName' => basename(dirname($destinationPath)),
      'destinationBaseName' => basename($destinationPath),
      'destinationId' => $destinationId,
      'errorMessage' => $errorMessage,
    ];

    $notification
      ->setSubject((string)$type, $parameters)
      ->setMessage((string)$type, $parameters)
      ->setDateTime(new DateTime());

    return $notification;
  }

  /**
   * Request the removal of notifications matching the given criteria.
   *
   * @param int $type
   *
   * @param string $userId
   *
   * @param int $sourceId
   *
   * @param int $destinationId
   *
   * @param null|string $target
   *
   * @return void
   *
   * @throws InvalidArgumentException
   */
  public function deleteNotification(
    int $type,
    string $userId,
    int $sourceId = -1,
    int $destinationId = -1,
    ?string $target = null,
  ):void {
    if ($type == Notifier::TYPE_ANY) {
      if (($sourceId <= 0) == ($destinationId <= 0)) {
        throw new InvalidArgumentException('Exactly on of "sourceid" and "destination" id must be specified for notification subject = ' . $type);
      }
      if ($sourceId > 0) {
        $objectType = 'sourceId';
        $objectId = $sourceId;
      } else {
        $objectType = 'destinationId';
        $objectId = $destinationId;
      }
    } else {
      if ($target !== null) {
        $type &= ~(Notifier::TYPE_MOUNT|Notifier::TYPE_EXTRACT);
        $type |= ($target == ArchiveJob::TARGET_MOUNT ? Notifier::TYPE_MOUNT : Notifier::TYPE_EXTRACT);
      }
      if ($type & (Notifier::TYPE_SCHEDULED|Notifier::TYPE_FAILURE|Notifier::TYPE_CANCELLED)) {
        if ($sourceId <= 0) {
          throw new InvalidArgumentException('Source-id must be given for notification subject = ' . $type);
        }
        $objectType = 'sourceId';
        $objectId = $sourceId;
      } else {
        if ($destinationId <= 0) {
          throw new InvalidArgumentException('Destination-id must be given for notification subject = ' . $type);
        }
        $objectType = 'destinationId';
        $objectId = $destinationId;
      }
    }
    $notification = $this->notificationManager->createNotification();
    $notification
      ->setUser($userId)
      ->setApp($this->appName)
      ->setObject($objectType, (string)$objectId);
    if ($type != Notifier::TYPE_ANY) {
      $notification->setSubject((string)$type);
    }
    $this->logInfo('REQUEST CLEAN OF ' . $type . ' ' . $target . ' ' . $userId);
    $this->notificationManager->markProcessed($notification);
  }

  /**
   * Delete a notification of a successful mount, e.g. after unmounting.
   *
   * @param string $userId
   *
   * @param int $mountPointId The file-id of the mount point.
   *
   * @return void
   */
  public function deleteMountSuccessNotification(
    string $userId,
    int $mountPointId,
  ):void {
    $this->deleteNotification(
      Notifier::TYPE_SUCCESS,
      userId: $userId,
      target: ArchiveJob::TARGET_MOUNT,
      destinationId: $mountPointId,
    );
  }

  /**
   * Delete a notification about pending operations, e.g. after the operation
   * has been cancelled.
   *
   * @param string $userId
   *
   * @param string $target
   *
   * @param int $sourceId The file id of the archive file.
   *
   * @return void
   */
  public function deleteScheduledJobNotification(
    string $userId,
    string $target,
    int $sourceId,
  ):void {
    $this->deleteNotification(
      Notifier::TYPE_SCHEDULED,
      userId: $userId,
      target: $target,
      sourceId: $sourceId,
    );
  }
}
