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

use InvalidArgumentException;

use Psr\Log\LoggerInterface;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\Response;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\IAppContainer;
use OCP\IRequest;
use OCP\IConfig;
use OCP\IL10N;

use OCA\FilesArchive\Constants;

/**
 * Settings-controller for both, personal and admin, settings.
 */
class SettingsController extends Controller
{
  use \OCA\FilesArchive\Traits\ResponseTrait;
  use \OCA\FilesArchive\Traits\LoggerTrait;
  use \OCA\FilesArchive\Traits\UtilTrait;

  public const DEFAULT_ADMIN_ARCHIVE_SIZE_LIMIT = Constants::DEFAULT_ADMIN_ARCHIVE_SIZE_LIMIT;

  private const ADMIN_SETTING = 'Admin';

  public const ARCHIVE_SIZE_LIMIT = 'archiveSizeLimit';
  public const ARCHIVE_SIZE_LIMIT_ADMIN = self::ARCHIVE_SIZE_LIMIT . self::ADMIN_SETTING;

  /**
   * @var array<string, array>
   *
   * Admin settings with r/w flag and default value (booleans)
   */
  const ADMIN_SETTINGS = [
    self::ARCHIVE_SIZE_LIMIT => [ 'rw' => true, 'default' => self::DEFAULT_ADMIN_ARCHIVE_SIZE_LIMIT ],
  ];

  /**
   * @var array<string, array>
   *
   * Personal settings with r/w flag and default value (booleans)
   */
  const PERSONAL_SETTINGS = [
    self::ARCHIVE_SIZE_LIMIT => [ 'rw' => true, ],
    self::ARCHIVE_SIZE_LIMIT_ADMIN => [ 'rw' => false, 'default' => self::DEFAULT_ADMIN_ARCHIVE_SIZE_LIMIT ],
  ];

  /** @var IAppContainer */
  private $appContainer;

  /** @var IConfig */
  private $config;

  /** @var IL10N */
  private $l;

  /** @var string */
  private $userId;

  // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
  public function __construct(
    string $appName,
    IRequest $request,
    $userId,
    LoggerInterface $logger,
    IL10N $l10n,
    IConfig $config,
    IAppContainer $appContainer,
  ) {
    parent::__construct($appName, $request);
    $this->logger = $logger;
    $this->l = $l10n;
    $this->config = $config;
    $this->userId = $userId;
    $this->appContainer = $appContainer;
  }
  // phpcs:enable

  /**
   * @param string $setting
   *
   * @param mixed $value
   *
   * @param bool $force
   *
   * @return DataResponse
   *
   * @AuthorizedAdminSetting(settings=OCA\GroupFolders\Settings\Admin)
   * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
   */
  public function setAdmin(string $setting, mixed $value, bool $force = false):DataResponse
  {
    if (!isset(self::ADMIN_SETTINGS[$setting])) {
      return self::grumble($this->l->t('Unknown personal setting: "%1$s"', $setting));
    }
    if (!(self::ADMIN_SETTINGS[$setting]['rw'] ?? false)) {
      return self::grumble($this->l->t('The personal setting "%1$s" is read-only', $setting));
    }
    $oldValue = $this->config->getAppValue(
      $this->appName,
      $setting,
      self::ADMIN_SETTINGS[$setting]['default'] ?? null
    );
    switch ($setting) {
      case self::ARCHIVE_SIZE_LIMIT:
        try {
          $newValue = $this->parseMemorySize($value);
        } catch (InvalidArgumentException $t) {
          return self::grumble($t->getMessage());
        }
        break;
      default:
        return self::grumble($this->l->t('Unknown admin setting: "%1$s"', $setting));
    }

    if ($newValue === null) {
      $this->config->deleteAppValue($this->appName, $setting);
      $newValue = self::ADMIN_SETTINGS[$setting]['default'] ?? null;
    } else {
      $this->config->setAppValue($this->appName, $setting, $newValue);
    }

    switch ($setting) {
      case self::ARCHIVE_SIZE_LIMIT:
        $humanValue = $newValue === null ? '' : $this->formatStorageValue($newValue);
        break;
      default:
        $humanValue = $value;
        break;
    }

    return new DataResponse([
      'newValue' => $newValue,
      'oldValue' => $oldValue,
      'humanValue' => $humanValue,
    ]);
  }

  /**
   * @param string $setting
   *
   * @return DataResponse
   *
   * @AuthorizedAdminSetting(settings=OCA\GroupFolders\Settings\Admin)
   */
  public function getAdmin(?string $setting = null):DataResponse
  {
    if ($setting === null) {
      $allSettings = self::ADMIN_SETTINGS;
    } else {
      if (!isset(self::ADMIN_SETTINGS[$setting])) {
        return self::grumble($this->l->t('Unknown admin setting: "%1$s"', $setting));
      }
      $allSettings = [ $setting => self::ADMIN_SETTINGS[$setting] ];
    }
    $results = [];
    foreach (array_keys($allSettings) as $oneSetting) {
      $value = $this->config->getAppValue(
        $this->appName,
        $oneSetting,
        self::ADMIN_SETTINGS[$oneSetting]['default'] ?? null);
      $humanValue = $value;
      switch ($oneSetting) {
        case self::ARCHIVE_SIZE_LIMIT:
          if ($value !== null) {
            $value = (int)$value;
            $humanValue = $this->formatStorageValue($value);
          } else {
            $humanValue = '';
          }
          break;
        default:
          return self::grumble($this->l->t('Unknown admin setting: "%1$s"', $oneSetting));
      }
      $results[$oneSetting] = $value;
      $results['human' . ucfirst($oneSetting)] = $humanValue;
    }

    if ($setting === null) {
      return new DataResponse($results);
    } else {
      return new DataResponse([
        'value' => $results[$setting],
        'humanValue' => $results['human' . ucfirst($setting)],
      ]);
    }
  }

  /**
   * Set a personal setting value.
   *
   * @param string $setting
   *
   * @param mixed $value
   *
   * @return Response
   *
   * @NoAdminRequired
   */
  public function setPersonal(string $setting, mixed $value):Response
  {
    if (!isset(self::PERSONAL_SETTINGS[$setting])) {
      return self::grumble($this->l->t('Unknown personal setting: "%1$s"', $setting));
    }
    if (!(self::PERSONAL_SETTINGS[$setting]['rw'] ?? false)) {
      return self::grumble($this->l->t('Thge personal setting "%1$s" is read-only', $setting));
    }
    $oldValue = $this->config->getUserValue(
      $this->userId,
      $this->appName,
      $setting,
      self::PERSONAL_SETTINGS[$setting]['default'] ?? null);
    switch ($setting) {
      case self::ARCHIVE_SIZE_LIMIT:
        try {
          $newValue = $this->parseMemorySize($value);
        } catch (InvalidArgumentException $t) {
          return self::grumble($t->getMessage());
        }
        break;
      default:
        return self::grumble($this->l->t('Unknown personal setting: "%s".', [ $setting ]));
    }

    if ($newValue === null) {
      $this->config->deleteUserValue($this->userId, $this->appName, $setting);
      $newValue = self::PERSONAL_SETTINGS[$setting]['default'] ?? null;
    } else {
      $this->config->setUserValue($this->userId, $this->appName, $setting, $newValue);
    }

    switch ($setting) {
      case self::ARCHIVE_SIZE_LIMIT:
        $humanValue = $newValue === null ? '' : $this->formatStorageValue($newValue);
        break;
      default:
        $humanValue = $value;
        break;
    }

    return new DataResponse([
      'newValue' => $newValue,
      'oldValue' => $oldValue,
      'humanValue' => $humanValue,
    ]);
  }

  /**
   * Get one or all personal settings.
   *
   * @param null|string $setting If null get all settings, otherwise just the
   * requested one.
   *
   * @return Response
   *
   * @NoAdminRequired
   */
  public function getPersonal(?string $setting = null):Response
  {
    if ($setting === null) {
      $allSettings = self::PERSONAL_SETTINGS;
    } else {
      if (!isset(self::PERSONAL_SETTINGS[$setting])) {
        return self::grumble($this->l->t('Unknown personal setting: "%1$s"', $setting));
      }
      $allSettings = [ $setting => self::PERSONAL_SETTINGS[$setting] ];
    }
    $results = [];
    foreach (array_keys($allSettings) as $oneSetting) {
      if (str_ends_with($oneSetting, self::ADMIN_SETTING)) {
        $adminKey = substr($oneSetting, 0, -strlen(self::ADMIN_SETTING));
        $value = $this->config->getAppValue(
          $this->appName,
          $adminKey,
          self::ADMIN_SETTINGS[$adminKey]['default'] ?? null,
        );
      } else {
        $value = $this->config->getUserValue(
          $this->userId,
          $this->appName,
          $oneSetting,
          self::PERSONAL_SETTINGS[$oneSetting]['default'] ?? null);
      }
      $humanValue = $value;
      switch ($oneSetting) {
        case self::ARCHIVE_SIZE_LIMIT:
        case self::ARCHIVE_SIZE_LIMIT_ADMIN:
          if ($value !== null) {
            $value = (int)$value;
            $humanValue = $this->formatStorageValue($value);
          } else {
            $humanValue = '';
          }
          break;
        default:
          return self::grumble($this->l->t('Unknown personal setting: "%1$s"', $oneSetting));
      }
      $results[$oneSetting] = $value;
      $results['human' . ucfirst($oneSetting)] = $humanValue;
    }

    if ($setting === null) {
      return new DataResponse($results);
    } else {
      return new DataResponse([
        'value' => $results[$setting],
        'humanValue' => $results['human' . ucfirst($setting)],
      ]);
    }
  }

  /**
   * @param string $stringValue
   *
   * @return null|string
   *
   * @throws InvalidArgumentException
   */
  private function parseMemorySize(string $stringValue):?string
  {
    if ($stringValue === '') {
      $stringValue = null;
    }
    if ($stringValue === null) {
      return $stringValue;
    }
    $newValue = $this->storageValue($stringValue);
    if (!is_int($newValue) && !is_float($newValue)) {
      throw new InvalidArgumentException($this->l->t('Unable to parse memory size limit "%s"', $stringValue));
    }
    if (empty($newValue)) {
      $newValue = null;
    }
    return $newValue;
  }
}
