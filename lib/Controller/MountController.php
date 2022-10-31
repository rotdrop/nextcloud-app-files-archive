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

use Psr\Log\LoggerInterface;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\Response;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\IAppContainer;
use OCP\IRequest;
use OCP\IConfig;
use OCP\IL10N;

/**
 * Manage user mount requests for archive files.
 */
class MountController extends Controller
{
  use \OCA\FilesArchive\Traits\ResponseTrait;
  use \OCA\FilesArchive\Traits\LoggerTrait;
  use \OCA\FilesArchive\Traits\UtilTrait;

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
   * @param string $archiveFile
   *
   * @param null|string $mountPoint
   *
   * @return DataResponse
   *
   * @NoAdminRequired
   */
  public function mount(string $archiveFile, ?string $mountPoint = null)
  {
    return self::grumble($this->l->t('UNIMPLEMENTED'));
  }

  /**
   * @param string $archiveFile
   *
   * @return DataResponse
   *
   * @NoAdminRequired
   */
  public function unmount(string $archiveFile)
  {
    return self::grumble($this->l->t('UNIMPLEMENTED'));
  }

  /**
   * @param string $archiveFile
   *
   * @return DataResponse
   *
   * @NoAdminRequired
   */
  public function mountStatus(string $archiveFile)
  {
    return self::grumble($this->l->t('UNIMPLEMENTED'));
  }
}
