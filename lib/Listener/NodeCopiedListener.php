<?php
/**
 * Archive Manager for Nextcloud
 *
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2025 Claus-Justus Heine <himself@claus-justus-heine.de>
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

use OCP\AppFramework\IAppContainer;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Files\Events\Node\NodeCopiedEvent;
use OCP\Files\File;
use OCP\Files\FileInfo;
use OCP\Files\Mount\IMountManager;
use OCP\Files\Node;
use OCP\IUser;
use OCP\IUserSession;
use Psr\Log\LoggerInterface;

use OCA\FilesArchive\Mount\MountProvider;
use OCA\FilesArchive\Db\ArchiveMount;
use OCA\FilesArchive\Db\ArchiveMountMapper;
use OCA\FilesArchive\Service\ArchiveService;
use OCA\FilesArchive\Constants;

/**
 * Unfortunately Nextcloud as of version 32 and below sets the permissions of
 * the target of a copy operation to the permissions of the source node. This
 * means: if the source is read-only then the target node will also be
 * read-only, even if it resides in writable storage.
 */
class NodeCopiedListener implements IEventListener
{
  use \OCA\FilesArchive\Toolkit\Traits\LoggerTrait;

  const EVENT = NodeCopiedEvent::class;

  /** @var string */
  protected $appName;

  /**
   * @param IAppContainer $appContainer
   */
  public function __construct(protected IAppContainer $appContainer)
  {
  }

  /** {@inheritdoc} */
  public function handle(Event $event):void
  {
    $eventClass = get_class($event);
    if ($eventClass != self::EVENT) {
      return;
    }
    /** @var NodeCopiedEvent $event */

    /** @var IUserSession $userSession */
    $userSession = $this->appContainer->get(IUserSession::class);
    if (!$userSession->isLoggedIn()) {
      return;
    }
    $this->logger = $this->appContainer->get(LoggerInterface::class);

    /** @var Node $sourceNode */
    $sourceNode = $event->getSource();

    if ($sourceNode->getMountPoint()->getMountProvider() != MountProvider::class) {
      return;
    }

    /** @var Node $targetNode */
    $target = $event->getTarget();

    $targetStorage = $target->getStorage();
    $targetInternalPath = $target->getInternalPath();

    $this->logWarn('Fixing "Nextcloud core permissions bug" by scanning the destination "' . $targetInternalPath . '".');
    $targetStorage->getScanner()->scan($targetInternalPath);
  }
}
