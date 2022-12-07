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

use Throwable;

use OC\Files\Storage\Wrapper\Wrapper as WrapperStorage;

use Psr\Log\LoggerInterface;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\Response;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;
use OCP\IConfig;
use OCP\IL10N;

use OCA\FilesArchive\Constants;

/**
 * Schedule background jobs for archive mounting or extraction.
 */
class BackgroundJobController extends Controller
{
  use \OCA\RotDrop\Toolkit\Traits\ResponseTrait;
  use \OCA\RotDrop\Toolkit\Traits\LoggerTrait;
  use \OCA\RotDrop\Toolkit\Traits\UtilTrait;

  const OPERATION_MOUNT = 'mount';
  const OPERATION_EXTRACT = 'extract';

  /** @var string */
  private $userId;

  // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
  public function __construct(
    ?string $appName,
    IRequest $request,
    ?string $userId,
    LoggerInterface $logger,
    IL10N $l10n,
    IConfig $cloudConfig,
  ) {
    parent::__construct($appName, $request);
    $this->logger = $logger;
    $this->l = $l10n;
    $this->userId = $userId;
  }
  // phpcs:enable

  /**
   * @param string $operation
   *
   * @param string $archivePath
   *
   * @param null|string $mountPoint
   *
   * @param null|string $passPhrase
   *
   * @param null|bool $stripCommonPathPrefix
   *
   * @return DataResponse
   *
   * @NoAdminRequired
   */
  public function schedule(string $operation, string $archivePath, ?string $destinationPath = null, ?string $passPhrase = null, ?bool $stripCommonPathPrefix = null)
  {
    return self::grumble($this->l->t('UNIMPLEMENTED'));
  }
}
