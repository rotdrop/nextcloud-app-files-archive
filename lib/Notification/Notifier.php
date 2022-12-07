<?php
/**
 * Recursive PDF Downloader App for Nextcloud
 *
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022 Claus-Justus Heine <himself@claus-justus-heine.de>
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

use InvalidArgumentException;

use Psr\Log\LoggerInterface as ILogger;
use OCP\IURLGenerator;
use OCP\L10N\IFactory as IL10NFactory;
use OCP\Notification\INotification;
use OCP\Notification\INotifier;

use OCA\FilesArchive\BackgroundJob\ArchiveJob;

/**
 * Notifier for the status of the background jobs.
 */
class Notifier implements INotifier
{
  use \OCA\RotDrop\Toolkit\Traits\LoggerTrait;

  public const TYPE_MOUNT = (1 << 0);
  public const TYPE_EXTRACT = (1 << 1);
  public const TYPE_SCHEDULED = (1 << 2);
  public const TYPE_SUCCESS = (1 << 3);
  public const TYPE_FAILURE = (1 << 4);
  public const TYPE_STAGES = self::TYPE_SCHEDULED|self::TYPE_FAILURE|self::TYPE_SUCCESS;
  public const TYPE_TARGETS = self::TYPE_MOUNT|self::TYPE_EXTRACT;

  /** @var string */
  protected $appName;

  /** @var IL10NFactory */
  protected $l10nFactory;

  /** @var IURLGenerator */
  protected $urlGenerator;

  // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
  public function __construct(
    string $appName,
    ILogger $logger,
    IL10NFactory $l10nFactory,
    IURLGenerator $urlGenerator,
  ) {
    $this->appName = $appName;
    $this->logger = $logger;
    $this->l10nFactory = $l10nFactory;
    $this->urlGenerator = $urlGenerator;
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
      throw new InvalidArgumentException('Application should be files_zip instead of ' . $notification->getApp());
    }

    $l = $this->l10nFactory->get($this->appName, $languageCode);

    $subjectType = (int)$notification->getSubject();
    $parameters = $notification->getSubjectParameters();
    $subjectSubstitutions = [
      'source' => [
        'type' => 'file',
        'id' => $parameters['sourceId'],
        'name' => $parameters['sourceBaseName'],
        'path' => $parameters['sourceDirectory'],
        'link' => $this->urlGenerator->linkToRouteAbsolute('files.viewcontroller.showFile', [
          'fileid' => $parameters['sourceId'],
        ]),
      ],
    ];


    switch ($subjectType & self::TYPE_STAGES) {
      case self::TYPE_SCHEDULED:
        $subjectSubstitutions['destination'] = [
          'type' => 'highlight',
          'id' => $notification->getObjectId(),
          'name' => $parameters['destinationBaseName'],
        ];
        if ($subjectType & self::TYPE_MOUNT) {
          $subjectTemplate = $l->t('The archive file at {source} will be mounted as a virtual folder at {destination}.');
        } else {
          $subjectTemplate = $l->t('The archive file at {source} will be extracted to {destination}.');
        }
        break;
      case self::TYPE_SUCCESS:
        $subjectSubstitutions['destination'] = [
          'type' => 'file',
          'id' => $parameters['destinationId'],
          'name' => $parameters['destinationBaseName'],
          'path' => $parameters['destinationDirectory'],
          'link' => $this->urlGenerator->linkToRouteAbsolute('files.viewcontroller.showFile', [
            'fileid' => $parameters['destinationId'],
          ]),
        ];
        if ($subjectType & self::TYPE_MOUNT) {
          $subjectTemplate = $l->t('Your archive file {source} has been mounted as a virtual folder at {destination}.');
        } else {
          $subjectTemplate = $l->t('Your archive file {source} has been extracted to {destination}.');
        }
        break;
      case self::TYPE_FAILURE:
        $subjectSubstitutions['destination'] = [
          'type' => 'file',
          'id' => $parameters['destinationId'],
          'name' => $parameters['destinationBaseName'],
          'path' => $parameters['destinationDirectory'],
          'link' => $this->urlGenerator->linkToRouteAbsolute('files.viewcontroller.showFile', [
            'fileid' => $parameters['destinationId'],
          ]),
        ];
        $errorMessage = $parameters['errorMessage'] ?? null;
        if ($errorMessage) {
          if ($subjectType & self::TYPE_MOUNT) {
            $subjectTemplate = $l->t('Mounting {source} at {destination} has failed: {message}');
          } else {
            $subjectTemplate = $l->t('Extacting {source} to {destination} bas failed: {message}');
          }
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

    $notification->setRichSubject($subjectTemplate, $subjectSubstitutions);

    $notification->setIcon($this->urlGenerator->getAbsoluteURL($this->urlGenerator->imagePath($this->appName, 'app-dark.svg')));
    $this->setParsedSubjectFromRichSubject($notification);
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

    $notification->setParsedSubject(str_replace($placeholders, $replacements, $notification->getRichSubject()));
  }
}
