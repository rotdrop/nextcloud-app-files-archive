<?php
/**
 * Recursive PDF Downloader App for Nextcloud
 *
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022, 2024, 2025 Claus-Justus Heine <himself@claus-justus-heine.de>
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

namespace OCA\FilesArchive\Notification;

use Throwable;
use InvalidArgumentException;

use Psr\Log\LoggerInterface as ILogger;
use OCP\IURLGenerator;
use OCP\L10N\IFactory as IL10NFactory;
use OCP\Notification\INotification;
use OCP\Notification\INotifier;
use OCP\IUserSession;
use OCP\IPreview;
use OCP\Files\IRootFolder;
use OCP\Files\Folder;
use OCP\Files\NotFoundException;

use OCA\FilesArchive\BackgroundJob\ArchiveJob;
use OCA\FilesArchive\Db\ArchiveMount;
use OCA\FilesArchive\Db\ArchiveMountMapper;

/**
 * Notifier for the status of the background jobs.
 */
class Notifier implements INotifier
{
  use \OCA\FilesArchive\Toolkit\Traits\LoggerTrait;
  use \OCA\FilesArchive\Toolkit\Traits\NodeTrait;
  use \OCA\FilesArchive\Toolkit\Traits\UserRootFolderTrait;

  public const TYPE_ANY = 0;
  public const TYPE_MOUNT = (1 << 0);
  public const TYPE_EXTRACT = (1 << 1);
  public const TYPE_SCHEDULED = (1 << 2);
  public const TYPE_SUCCESS = (1 << 3);
  public const TYPE_FAILURE = (1 << 4);
  public const TYPE_CANCELLED = (1 << 5);
  public const TYPE_REMOVED = (1 << 6);
  public const TYPE_STAGES = self::TYPE_SCHEDULED|self::TYPE_FAILURE|self::TYPE_SUCCESS|self::TYPE_CANCELLED|self::TYPE_REMOVED;
  public const TYPE_TARGETS = self::TYPE_MOUNT|self::TYPE_EXTRACT;

  // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
  public function __construct(
    protected $appName,
    protected ILogger $logger,
    protected IL10NFactory $l10nFactory,
    protected IURLGenerator $urlGenerator,
    protected IRootFolder $rootFolder,
    protected IPreview $previewManager,
    private ArchiveMountMapper $mountMapper,
    IUserSession $userSession,
  ) {
    $user = $userSession->getUser();
    if (!empty($user)) {
      $this->userId = $user->getUID();
    }
  }
  // phpcs:enable

  /** {@inheritdoc} */
  public function getID(): string
  {
    return $this->appName;
  }

  /** {@inheritdoc} */
  public function getName():string
  {
    return $this->l10nFactory->get($this->appName)->t('Archive Manager');
  }

  /** {@inheritdoc} */
  public function prepare(INotification $notification, string $languageCode):INotification
  {
    if ($notification->getApp() !== $this->appName) {
      throw new InvalidArgumentException('Application should be ' . $this->appName . ' instead of ' . $notification->getApp());
    }

    $l = $this->l10nFactory->get($this->appName, $languageCode);

    $this->userId = $notification->getUser();

    $subjectType = (int)$notification->getSubject();

    $parameters = $notification->getSubjectParameters();
    $richSubstitutions = [
      'source' => [
        'type' => 'file',
        'id' => (string)$parameters['sourceId'],
        'name' => $parameters['sourceBaseName'],
        'path' => $parameters['sourcePath'],
        'link' => $this->urlGenerator->linkToRouteAbsolute('files.viewcontroller.showFile', [
          'fileid' => $parameters['sourceId'],
        ]),
      ],
    ];

    $messageTemplate = null; // default, no detailed message

    switch ($subjectType & self::TYPE_STAGES) {
      case self::TYPE_SCHEDULED:
        $richSubstitutions['destination'] = [
          'type' => 'highlight',
          'id' => $notification->getObjectId(),
          'name' => $parameters['destinationBaseName'],
        ];
        if ($subjectType & self::TYPE_MOUNT) {
          $subjectTemplate = $l->t('The archive file {source} will be mounted as a virtual folder at {destination}.');
        } else {
          $subjectTemplate = $l->t('The archive file {source} will be extracted to {destination}.');
        }
        break;
      case self::TYPE_SUCCESS:
        // Strange things happens: this is really called to often, and with
        // success status despite the fact that nothing has happened yet. What
        // the heck. getById() seems to work though.

        // $this->logInfo('PARAMETERS ' . print_r($parameters, true));

        $richSubstitutions['destination'] = [
          'type' => 'file',
          'id' => (string)$parameters['destinationId'],
          'name' => $parameters['destinationBaseName'],
          'path' => $parameters['destinationPath'],
          'link' => $this->urlGenerator->linkToRouteAbsolute('files.viewcontroller.showFile', [
            'fileid' => $parameters['destinationId'],
          ]),
        ];

        try {
          $destinations = $this->getUserFolder()->getById($parameters['destinationId']);

          /** @var Folder $destination */
          foreach ($destinations as $destination) {
            $relativePath = $this->getUserFolder()->getRelativePath($destination->getPath());
            if ($relativePath == $parameters['destinationPath']) {
              $richSubstitutions['destination']['folder'] = json_encode($this->formatNode($destination));
              if ($subjectType & self::TYPE_MOUNT) {
                $richSubstitutions['destination']['status'] = 'mount';
                // this was the folder, if we had an async mount request, we
                // also supply the mount-table entry.
                $mountEntity = $this->mountMapper->findByMountPointFileId($parameters['destinationId']);
                $richSubstitutions['destination']['mount'] = json_encode($mountEntity->jsonSerialize());
              } else {
                $richSubstitutions['destination']['status'] = 'extract';
              }
              break;
            }
          }
        } catch (NotFoundException $e) {
          $this->logException($e, 'SUCCESS, but no folder BY ID');
        }

        if ($subjectType & self::TYPE_MOUNT) {
          $subjectTemplate = $l->t('Archive mount of {source} succeeded.');
        } else {
          $subjectTemplate = $l->t('Archive extraction of {source} succeeded.');
        }

        if ($subjectType & self::TYPE_MOUNT) {
          $messageTemplate = $l->t(
            'Your archive file {source} has been mounted as a virtual folder at {destination}.'
            . ' In order to unmount it it is safe to just delete the mount point {destination}.'
            . ' This will do no harm to the archive file and just issue an "unmount" action.'
            . ' Please note that the virtual folder is read-only.'
          );
        } else {
          $messageTemplate = $l->t(
            'Your archive file {source} has been extracted to {destination}.'
            . ' The archive contents reside there as ordinary files.'
            . ' Please feel free to use those as it pleases you.'
            . ' There is not automatic cleanup, the extracted files will remain in your file-space until you delete them manually.'
            . ' Just delete the folder {destination} if you have no more use of those files.'
            . ' Please note also that any changes committed to the extracted files will not be written back to the archive file {source}.'
          );
        }

        break;
      case self::TYPE_FAILURE:
        $richSubstitutions['destination'] = [
          'type' => 'highlight',
          'id' => $notification->getObjectId(),
          'name' => $parameters['destinationPath'],
        ];
        $errorMessage = $parameters['errorMessage'] ?? null;
        if ($errorMessage) {
          if ($subjectType & self::TYPE_MOUNT) {
            $subjectTemplate = $l->t('Mounting {source} at {destination} has failed: {message}');
          } else {
            $subjectTemplate = $l->t('Extracting {source} to {destination} has failed: {message}');
          }
          $richSubstitutions['message'] = [
            'type' => 'highlight',
            'id' => $notification->getObjectId(),
            'name' => $l->t($errorMessage),
          ];
        } else {
          if ($subjectType & self::TYPE_MOUNT) {
            $subjectTemplate = $l->t('Mounting {source} at {destination} has failed.');
          } else {
            $subjectTemplate = $l->t('Extacting {source} to {destination} bas failed.');
          }
        }
        break;
      default:
        throw new InvalidArgumentException($l->t('Unsupported subject: "%s".', $notification->getSubject()));
    }

    $notification->setIcon($this->urlGenerator->getAbsoluteURL($this->urlGenerator->imagePath($this->appName, 'app-dark.svg')));

    $notification->setRichSubject($subjectTemplate, $richSubstitutions);
    $this->setParsedSubjectFromRichSubject($notification);

    if ($messageTemplate !== null) {
      $notification->setRichMessage($messageTemplate, $richSubstitutions);
      $this->setParsedMessageFromRichMessage($notification);
    }

    return $notification;
  }

  /**
   * @param INotification $notification
   *
   * @return void
   */
  protected function setParsedSubjectFromRichSubject(INotification $notification):void
  {
    $placeholders = $replacements = [];
    foreach ($notification->getRichSubjectParameters() as $placeholder => $parameter) {
      $placeholders[] = '{' . $placeholder . '}';
      if ($parameter['type'] === 'file') {
        $replacements[] = $parameter['path'];
      } else {
        $replacements[] = $parameter['name'];
      }
    }

    $subject = str_replace($placeholders, $replacements, $notification->getRichSubject());
    $notification->setParsedSubject($subject);
  }

  /**
   * @param INotification $notification
   *
   * @return void
   */
  protected function setParsedMessageFromRichMessage(INotification $notification):void
  {
    $placeholders = $replacements = [];
    foreach ($notification->getRichMessageParameters() as $placeholder => $parameter) {
      $placeholders[] = '{' . $placeholder . '}';
      if ($parameter['type'] === 'file') {
        $replacements[] = $parameter['path'];
      } else {
        $replacements[] = $parameter['name'];
      }
    }

    $notification->setParsedMessage(str_replace($placeholders, $replacements, $notification->getRichMessage()));
  }
}
