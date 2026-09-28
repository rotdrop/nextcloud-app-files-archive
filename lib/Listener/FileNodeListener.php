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
use OCP\EventDispatcher\IEventDispatcher;
use OCP\EventDispatcher\IEventListener;
use OCP\Files\Events\Node\BeforeNodeDeletedEvent;
use OCP\Files\Events\Node\NodeDeletedEvent;
use OCP\Files\Events\InvalidateMountCacheEvent;
use OCP\IUser;
use Psr\Log\LoggerInterface;
use OCP\IUserSession;
use OCP\Files\Node;
use OCP\Files\NotFoundException;
use OCP\Files\File;
use OCP\Files\FileInfo;
use OCP\Files\IRootFolder;
use OCP\Files\Mount\IMountManager;
use Psr\Container\ContainerInterface;

use OCA\FilesArchive\Constants;
use OCA\FilesArchive\Db\ArchiveMount;
use OCA\FilesArchive\Db\ArchiveMountMapper;
use OCA\FilesArchive\Service\MimeTypeService;

/**
 * This listeners has the task to look out for deleted archive files and
 * unmount all associated mounts. There are some pathological cases where
 * multiple instances of the source archive file are mounted into the user
 * folder -- as long as one instance is readable, everyting is just ok.
 *
 * The framework provided by NC is a little bit challenging here:
 *
 * - the final NodeDeletedEvent has no information about the original file id
 * - the BeforeNodeDeletedEvent may be used to cancel the deletion by other listeners
 */
class FileNodeListener implements IEventListener
{
  use \OCA\FilesArchive\Toolkit\Traits\LoggerTrait;
  use \OCA\FilesArchive\Traits\GetArchiveFileTrait;

  const EVENT = [ BeforeNodeDeletedEvent::class, NodeDeletedEvent::class ];

  /** @var string */
  protected $appName;

  protected $removalCandidates = [];

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

    /** @var Node $sourceNode */
    switch ($eventClass) {
      case BeforeNodeDeletedEvent::class:
        // Just update the candidates list. The deletion may still be
        // cancelled by other listeners, therefore the notion "candidates".
        /** @var BeforeNodeDeletedEvent $event */
        $this->removalCandidates[] = $event->getNode()->getId();
        return;
      case NodeDeletedEvent::class:
        // We no longer record the path of the archive file, and the
        // NodeDeletedEvent has really zarry information except for the
        // deleted file-path ... nothing we can work with. This is just a
        // trigger with no valuable data for us.
        break;
    }

    if (count($this->removalCandidates) == 0) {
      return;
    }

    $userId = $user->getUID();
    $this->logger = $this->appContainer->get(LoggerInterface::class);

    // So what is next: we have a list of deletion candidates, but Nextcloud
    // provides no way to find out if this actual NodeDeletedEvent really
    // refers to one of the candidates (NonExistingNode Dingsbums).
    //
    // Strategy: just check all recorded candidates in turn, if any of those
    // is gone, do the cleanup and remove the candidate.

    /** @var IMountManager $mountManager */
    $mountManager = $this->appContainer->get(IMountManager::class);

    /** @var ArchiveMountMapper $mountMapper */
    $mountMapper = $this->appContainer->get(ArchiveMountMapper::class);

    $userFolderPrefix = Constants::PATH_SEPARATOR . $userId . Constants::PATH_SEPARATOR . 'files';

    $userFolder = $this->appContainer->get(IRootFolder::class)->getUserFolder($userId);

    $mountsRemoved = false;

    // iterate over the recorded candidates ...
    foreach ($this->removalCandidates as $key => $archiveFileId) {

      $mounts = $mountMapper->findByArchiveFileId($userId, $archiveFileId);

      if (empty($mounts)) {
        unset($this->removalCandidates[$key]);
        continue; // nothing to do
      }

      /** @var ArchiveMount $mountEntity */
      foreach ($mounts as $mountEntity) {
        $archiveFile = $this->getArchiveFile($userFolder, $mountEntity);
        if ($archiveFile === null) {
          // This means that this user has no longer any readable copy of the
          // archive file available. Hence the mount will here be deleted.
          $mountManager->removeMount($userFolderPrefix . Constants::PATH_SEPARATOR . $mountEntity->getMountPointPath());
          $mountMapper->delete($mountEntity);
          $mountsRemoved = true;
        }
      }
      unset($this->removalCandidates[$key]);
    }

    if ($mountsRemoved) {
      $this->appContainer->get(IEventDispatcher::class)->dispatchTyped(new InvalidateMountCacheEvent($user));
    }
  }
}
