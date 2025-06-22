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

use Throwable;

use Psr\Log\LoggerInterface;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\AppFramework\IAppContainer;
use OCP\AppFramework\Services\IInitialState;
use OCP\IUserSession;
use OCP\IGroupManager;
use OCP\IL10N;
use OCP\IConfig as CloudConfig;

use OCA\Files\Event\LoadAdditionalScriptsEvent;
use OCA\Files\Event\LoadSidebar;

use OCA\FilesArchive\Service\ArchiveService;
use OCA\FilesArchive\Service\MimeTypeService;
use OCA\FilesArchive\Controller\SettingsController;
use OCA\FilesArchive\Constants;

/**
 * In particular listen to the asset-loading events.
 */
class FilesActionListener implements IEventListener
{
  use \OCA\FilesArchive\Toolkit\Traits\LoggerTrait;
  use \OCA\FilesArchive\Toolkit\Traits\CloudAdminTrait;
  use \OCA\FilesArchive\Toolkit\Traits\AssetTrait;

  const EVENT = [
    LoadAdditionalScriptsEvent::class,
    LoadSidebar::class,
  ];

  const ASSET_BASENAME = [
    LoadAdditionalScriptsEvent::class => [
      Constants::JS => 'files-hooks',
      Constants::CSS => null,
    ],
    LoadSidebar::class => [
      Constants::JS => 'files-sidebar-hooks',
      Constants::CSS => null,
    ],
  ];

  /** @var array */
  private $handled = [
    LoadAdditionalScriptsEvent::class => false,
    LoadSidebar::class => false,
  ];

  /** @var bool */
  private $initialStateEmitted = false;

  // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
  public function __construct(protected IAppContainer $appContainer)
  {
  }
  // phpcs:enable

  /**
   * @param Event $event
   *
   * @SuppressWarnings(PHPMD.Superglobals)
   *
   * @return void
   */
  public function handle(Event $event): void
  {
    $eventClass = get_class($event);
    if (!in_array($eventClass, self::EVENT)) {
      return;
    }

    // this really only needs to be executed once per request.
    if ($this->handled[$eventClass]) {
      return;
    }
    $this->handled[$eventClass] = true;

    /** @var IUserSession $userSession */
    $userSession = $this->appContainer->get(IUserSession::class);
    $user = $userSession->getUser();

    if (empty($user)) {
      return;
    }

    $userId = $user->getUID();

    $appName = $this->appContainer->get('appName');

    $this->l = $this->appContainer->get(IL10N::class);

    $this->logger = $this->appContainer->get(LoggerInterface::class);

    /** @var IInitialState $initialState */
    $initialState = $this->appContainer->get(IInitialState::class);

    /** @var CloudConfig $cloudConfig */
    $cloudConfig = $this->appContainer->get(CloudConfig::class);

    if (!$this->initialStateEmitted) {
      /** @var MimeTypeService $mimeTypeService */
      $mimeTypeService = $this->appContainer->get(MimeTypeService::class);
      $archiveMimeTypes = $mimeTypeService->getSupportedArchiveMimeTypes();
      $archiveMimeTypes = array_values($archiveMimeTypes);
      sort($archiveMimeTypes);
      $archiveMimeTypes = array_values(array_unique($archiveMimeTypes));

      // just admin contact and stuff to make the ajax error handlers work.
      $this->groupManager = $this->appContainer->get(IGroupManager::class);
      $initialState->provideInitialState('config', [
        'adminContact' => $this->getCloudAdminContacts(implode: true),
        'phpUserAgent' => $_SERVER['HTTP_USER_AGENT'], // @@todo get in javascript from request
        'archiveMimeTypes' => $archiveMimeTypes,
        SettingsController::MOUNT_STRIP_COMMON_PATH_PREFIX_DEFAULT => $cloudConfig->getUserValue(
          $userId, $appName, SettingsController::MOUNT_STRIP_COMMON_PATH_PREFIX_DEFAULT, false),
        SettingsController::EXTRACT_STRIP_COMMON_PATH_PREFIX_DEFAULT => $cloudConfig->getUserValue(
          $userId, $appName, SettingsController::EXTRACT_STRIP_COMMON_PATH_PREFIX_DEFAULT, false),
        SettingsController::MOUNT_BACKGROUND_JOB => $cloudConfig->getUserValue(
          $userId, $appName, SettingsController::MOUNT_BACKGROUND_JOB, SettingsController::MOUNT_BACKGROUND_JOB_DEFAULT),
        SettingsController::EXTRACT_BACKGROUND_JOB => $cloudConfig->getUserValue(
          $userId, $appName, SettingsController::EXTRACT_BACKGROUND_JOB, SettingsController::EXTRACT_BACKGROUND_JOB_DEFAULT),
        SettingsController::MOUNT_BY_LEFT_CLICK => $cloudConfig->getUserValue(
          $userId, $appName, SettingsController::MOUNT_BY_LEFT_CLICK, SettingsController::MOUNT_BY_LEFT_CLICK_DEFAULT),
        SettingsController::MOUNT_POINT_TEMPLATE => SettingsController::FOLDER_TEMPLATE_DEFAULT,
        SettingsController::EXTRACT_TARGET_TEMPLATE => SettingsController::FOLDER_TEMPLATE_DEFAULT,
      ]);

      // $this->logInfo('MIME ' . print_r($archiveMimeTypes, true));
    }

    if (empty($this->assets)) {
      $this->initializeAssets(__DIR__);
    }

    $assetBasename = self::ASSET_BASENAME[$eventClass][Constants::JS];
    if ($assetBasename) {
      try {
        list('asset' => $scriptAsset,) = $this->getJSAsset($assetBasename);
        \OCP\Util::addScript($appName, $scriptAsset);
      } catch (Throwable $t) {
        $this->logException($t, 'Unable to add script asset ' . $assetBasename);
      }
    }
    $assetBasename = self::ASSET_BASENAME[$eventClass][Constants::CSS];
    if ($assetBasename) {
      try {
        list('asset' => $styleAsset,) = $this->getCSSAsset($assetBasename);
        \OCP\Util::addStyle($appName, $styleAsset);
      } catch (Throwable $t) {
        $this->logException($t, 'Unable to add style asset ' . $assetBasename);
      }
    }
  }
}
