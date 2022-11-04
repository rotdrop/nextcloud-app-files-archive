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

use Psr\Log\LoggerInterface;
use OCP\IRequest;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\Response;
use OCP\AppFramework\Http\DataResponse;
use OCP\Files\IRootFolder;
use OCP\IL10N;

use OCA\FilesArchive\Exceptions;
use OCA\FilesArchive\Service\ArchiveService;

/**
 * AJAX end-point for archive operations and info.
 */
class ArchiveController extends Controller
{
  use \OCA\FilesArchive\Traits\ResponseTrait;
  use \OCA\FilesArchive\Traits\LoggerTrait;
  use \OCA\FilesArchive\Traits\UtilTrait;

  /** @var string */
  private $userId;

  /** @var IRootFolder */
  private $rootFolder;

  /** @var ArchiveService */
  private $archiveService;

  // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
  public function __construct(
    ?string $appName,
    IRequest $request,
    ?string $userId,
    LoggerInterface $logger,
    IL10N $l10n,
    IRootFolder $rootFolder,
    ArchiveService $archiveService,
  ) {
    parent::__construct($appName, $request);
    $this->logger = $logger;
    $this->l = $l10n;
    $this->userId = $userId;
    $this->rootFolder = $rootFolder;
    $this->archiveService = $archiveService;
  }
  // phpcs:enable

  /**
   * @param string $archivePath
   *
   * @return DataResponse
   *
   * @NoAdminRequired
   */
  public function info(string $archivePath):DataResponse
  {
    $archivePath = urldecode($archivePath);

    $userFolder = $this->rootFolder->getUserFolder($this->userId);
    if (empty($userFolder)) {
      return self::grumble($this->l->t('The user folder for user "%s" could not be opened.', $this->userId));
    }
    try {
      /** @var File $archiveFile */
      $archiveFile = $userFolder->get($archivePath);
    } catch (FileNotFoundException $e) {
      return self::grumble($this->l->t('The archive file "%s" could not be found on the server.', $archivePath));
    }

    try {
      $this->archiveService->open($archiveFile);
    } catch (Exceptions\ArchiveException $e) {
      return self::grumble($this->l->t('Unable to open the archive file "%s".', $archivePath));
    }

    return self::dataResponse($this->archiveService->getArchiveInfo());
  }
}
