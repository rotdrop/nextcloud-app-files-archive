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
  use \OCA\FilesArchive\Toolkit\Traits\UtilTrait;
  use \OCA\FilesArchive\Toolkit\Traits\ResponseTrait;
  use \OCA\FilesArchive\Toolkit\Traits\LoggerTrait;

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

  public const MOUNT_STRIP_COMMON_PATH_PREFIX_DEFAULT = 'mountStripCommonPathPrefixDefault';

  public const EXTRACT_STRIP_COMMON_PATH_PREFIX_DEFAULT = 'extractStripCommonPathPrefixDefault';

  public const MOUNT_POINT_AUTO_RENAME = 'mountPointAutoRename';

  public const EXTRACT_TARGET_AUTO_RENAME = 'extractTargetAutoRename';

  public const ARCHIVE_FILE_NAME_PLACEHOLDER = '{archiveFileName}';

  public const FOLDER_TEMPLATE_DEFAULT = self::ARCHIVE_FILE_NAME_PLACEHOLDER;

  public const MOUNT_POINT_TEMPLATE = 'mountPointTemplate';

  public const EXTRACT_TARGET_TEMPLATE = 'extractTargetTemplate';

  public const MOUNT_BACKGROUND_JOB = 'mountBackgroundJob';
  public const MOUNT_BACKGROUND_JOB_DEFAULT = false;

  public const EXTRACT_BACKGROUND_JOB = 'extractBackgroundJob';
  public const EXTRACT_BACKGROUND_JOB_DEFAULT = false;

  public const MOUNT_BY_LEFT_CLICK = 'mountByLeftClick';
  public const MOUNT_BY_LEFT_CLICK_DEFAULT = false;

  /**
   * @var array<string, array>
   *
   * Personal settings with r/w flag and default value (booleans)
   */
  const PERSONAL_SETTINGS = [
    self::ARCHIVE_SIZE_LIMIT => [ 'rw' => true, ],
    self::ARCHIVE_SIZE_LIMIT_ADMIN => [ 'rw' => false, 'default' => self::DEFAULT_ADMIN_ARCHIVE_SIZE_LIMIT ],
    self::MOUNT_STRIP_COMMON_PATH_PREFIX_DEFAULT => [ 'rw' => true, 'default' => false, ],
    self::EXTRACT_STRIP_COMMON_PATH_PREFIX_DEFAULT => [ 'rw' => true, 'default' => false, ],
    self::MOUNT_POINT_AUTO_RENAME => [ 'rw' => true, 'default' => false, ],
    self::EXTRACT_TARGET_AUTO_RENAME => [ 'rw' => true, 'default' => false, ],
    self::MOUNT_POINT_TEMPLATE => [ 'rw' => true, 'default' => self::FOLDER_TEMPLATE_DEFAULT ],
    self::EXTRACT_TARGET_TEMPLATE => [ 'rw' => true, 'default' => self::FOLDER_TEMPLATE_DEFAULT ],
    self::MOUNT_BACKGROUND_JOB => [ 'rw' => true, 'default' => self::MOUNT_BACKGROUND_JOB_DEFAULT ],
    self::EXTRACT_BACKGROUND_JOB => [ 'rw' => true, 'default' => self::EXTRACT_BACKGROUND_JOB_DEFAULT ],
    self::MOUNT_BY_LEFT_CLICK => [ 'rw' => true, 'default' => self::MOUNT_BY_LEFT_CLICK_DEFAULT ],
  ];

  // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
  public function __construct(
    string $appName,
    IRequest $request,
    private $userId,
    private IConfig $config,
    protected IAppContainer $appContainer,
    protected IL10N $l,
    protected LoggerInterface $logger,
  ) {
    parent::__construct($appName, $request);
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
   * @AuthorizedAdminSetting(settings=OCA\FilesArchive\Settings\Admin)
   * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
   */
  public function setAdmin(string $setting, mixed $value, bool $force = false):DataResponse
  {
    if (!isset(self::ADMIN_SETTINGS[$setting])) {
      return self::grumble($this->l->t('Unknown admin setting: "%1$s"', $setting));
    }
    if (!(self::ADMIN_SETTINGS[$setting]['rw'] ?? false)) {
      return self::grumble($this->l->t('The admin setting "%1$s" is read-only', $setting));
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
   * @AuthorizedAdminSetting(settings=OCA\FilesArchive\Settings\Admin)
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
      return self::grumble($this->l->t('The personal setting "%1$s" is read-only', $setting));
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
      case self::EXTRACT_BACKGROUND_JOB:
      case self::EXTRACT_STRIP_COMMON_PATH_PREFIX_DEFAULT:
      case self::EXTRACT_TARGET_AUTO_RENAME:
      case self::MOUNT_BACKGROUND_JOB:
      case self::MOUNT_BY_LEFT_CLICK:
      case self::MOUNT_POINT_AUTO_RENAME:
      case self::MOUNT_STRIP_COMMON_PATH_PREFIX_DEFAULT:
        $newValue = filter_var($value, FILTER_VALIDATE_BOOLEAN, ['flags' => FILTER_NULL_ON_FAILURE]);
        if ($newValue === null) {
          return self::grumble(
            $this->l->t('Value "%1$s" for setting "%2$s" is not convertible to boolean.', [
              $value, $setting,
            ]));
        }
        if ($newValue === (self::PERSONAL_SETTINGS[$setting]['default'] ?? false)) {
          $newValue = null;
        } else {
          $newValue = (int)$newValue;
        }
        break;
      case self::MOUNT_POINT_TEMPLATE:
      case self::EXTRACT_TARGET_TEMPLATE:
        if (empty($value)) {
          $newValue = null;
          break;
        }
        $newValue = strip_tags($value);
        if ($newValue == self::PERSONAL_SETTINGS[$setting]['default']) {
          $newValue = null;
          break;
        }
        if (strpos($newValue, self::ARCHIVE_FILE_NAME_PLACEHOLDER) === false) {
          return self::grumble($this->l->t(
            'The target folder template "%1$s" must contain the archive file placeholder "%2$s".', [
              $newValue, self::ARCHIVE_FILE_NAME_PLACEHOLDER,
            ]));
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
        case self::EXTRACT_BACKGROUND_JOB:
        case self::EXTRACT_STRIP_COMMON_PATH_PREFIX_DEFAULT:
        case self::EXTRACT_TARGET_AUTO_RENAME:
        case self::EXTRACT_TARGET_TEMPLATE:
        case self::MOUNT_BACKGROUND_JOB:
        case self::MOUNT_BY_LEFT_CLICK:
        case self::MOUNT_POINT_AUTO_RENAME:
        case self::MOUNT_POINT_TEMPLATE:
        case self::MOUNT_STRIP_COMMON_PATH_PREFIX_DEFAULT:
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
