<?php
/**
 * Archive Manager for Nextcloud
 *
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022, 2024, 2025, 2026 Claus-Justus Heine <himself@claus-justus-heine.de>
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

namespace OCA\FilesArchive\Listener;

use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Files\Events\Node\NodeDeletedEvent;
use OCP\Files\Events\Node\NodeRenamedEvent;
use OCP\IUser;
use Psr\Log\LoggerInterface;
use OCP\IUserSession;
use OCP\Files\Node;
use OCP\Files\NotFoundException;
use OCP\Files\File;
use OCP\Files\FileInfo;
use OCP\Files\Mount\IMountManager;
use Psr\Container\ContainerInterface;

use OCA\FilesArchive\Db\ArchiveMount;
use OCA\FilesArchive\Db\ArchiveMountMapper;
use OCA\FilesArchive\Service\ArchiveService;
use OCA\FilesArchive\Constants;

/**
 * Listen to renamed and deleted events in order to keep mount-point table
 * synchronized with the cloud file system.
 */
class FileNodeListener implements IEventListener
{
  use \OCA\FilesArchive\Toolkit\Traits\LoggerTrait;

  const EVENT = [ NodeDeletedEvent::class, NodeRenamedEvent::class ];

  /** @var string */
  protected $appName;

  /**
   * @param ContainerInterface $appContainer
   */
  public function __construct(protected ContainerInterface $appContainer)
  {
  }

  /** {@inheritdoc} */
  public function handle(Event $event):void
  {
    $eventClass = get_class($event);
    if (array_search($eventClass, self::EVENT) === false) {
      return;
    }

    /** @var IUserSession $userSession */
    $userSession = $this->appContainer->get(IUserSession::class);
    $user = $userSession->getUser();
    if (empty($user)) {
      return;
    }
    $userId = $user->getUID();
    $this->logger = $this->appContainer->get(LoggerInterface::class);

    /** @var Node $sourceNode */
    switch ($eventClass) {
      case NodeDeletedEvent::class:
        /** @var NodeDeletedEvent $event */
        $sourceNode = $event->getNode();
        break;
      case NodeRenamedEvent::class:
        /** @var NodeRenamedEvent $event */
        $sourceNode = $event->getSource();
        // The source of a rename does not exist any more at this point, so
        // asking it for its type would throw. The target is the same node
        // after the rename and can be asked instead.
        $typeNode = $event->getTarget();
        break;
    }

    try {
      if ($typeNode->getType() != FileInfo::TYPE_FILE) {
        // could perhaps remove the success information
        return;
      }
    } catch (NotFoundException $e) {
      // Not knowing the type is no reason to skip the clean-up below, which
      // only needs the path of the node.
    }

    // The following cannot work as we only get NonExistingFile nodes here
    // /** @var File $sourceNode */
    // $supportedMimeTypes = ArchiveService::getSupportedMimeTypes();
    // if (array_search($sourceNode->getMimeType(), $supportedMimeTypes) === false) {
    //   return;
    // }

    /** @var ArchiveMountMapper $mountMapper */
    $mountMapper = $this->appContainer->get(ArchiveMountMapper::class);

    $userFolderPrefix = Constants::PATH_SEPARATOR . $userId . Constants::PATH_SEPARATOR . 'files';
    $userFolderPrefixLength = strlen($userFolderPrefix);
    $sourcePath = substr($sourceNode->getPath(), $userFolderPrefixLength);

    $mounts = $mountMapper->findByArchivePath($user->getUID(), $sourcePath);
    if (empty($mounts)) {
      return;
    }

    $shouldDelete = $eventClass == NodeDeletedEvent::class;
    if (!$shouldDelete) { // i.e. renamed
      $targetNode = $event->getTarget();
      if ($targetNode->getType() != FileInfo::TYPE_FILE) {
        // could happen with missed rename events after the app temporarily
        // has been disabled
        $shouldDelete = true;
      }
      $supportedMimeTypes = ArchiveService::getSupportedMimeTypes();
      if (array_search($targetNode->getMimeType(), $supportedMimeTypes) === false) {
        // if the mounted archive is (no longer) supported there is no point
        // in keeping it mounted.
        $shouldDelete = true;
      }
      // anything else ????? PLEASE FIXME
    }

    if ($shouldDelete) {
      // @todo: removing mounted archives from an event handler should emit a
      // user notification

      /** @var IMountManager $mountManager */
      $mountManager = $this->appContainer->get(IMountManager::class);
      /** @var ArchiveMount $mount */
      foreach ($mounts as $mount) {
        $mountManager->removeMount($userFolderPrefix . Constants::PATH_SEPARATOR . $mount->getMountPointPath());
        $mountMapper->delete($mount);
      }
      return;
    }

    // this piece of code can only be hit if it was a rename event and above
    // consistency checks did not bail out.

    $targetPath = substr($targetNode->getPath(), $userFolderPrefixLength);
    /** @var ArchiveMount $mount */
    foreach ($mounts as $mount) {
      $mount->setArchiveFilePath($targetPath);
      $mountMapper->update($mount);
    }
  }
}
