<?php
/**
 * @author    Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022, 2023 Claus-Justus Heine <himself@claus-justus-heine.de>
 * @license   AGPL-3.0-or-later
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

namespace OCA\FilesArchive\Controller;

use OCA\FilesArchive\AppInfo\Application;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\Response;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;
use OCP\IL10N;

/**
 * Catch-all controller in order to generate usable error messages.
 */
class CatchAllController extends Controller
{
  use \OCA\FilesArchive\Toolkit\Traits\UtilTrait;
  use \OCA\FilesArchive\Toolkit\Traits\ResponseTrait;

  /**
   * @param string $appName
   *
   * @param IRequest $request
   *
   * @param IL10N $l10n
   */
  public function __construct(
    string $appName,
    IRequest $request,
    IL10N $l10n,
  ) {
    parent::__construct($this->appName, $request);
    $this->l = $l10n;
  }

  // phpcs:disable Squiz.Commenting.FunctionComment.MissingParamTag
  /**
   * @return Response
   *
   * @NoAdminRequired
   * @NoCSRFRequired
   *
   * @SuppressWarnings(PHPMD.ShortVariable)
   */
  public function post($a, $b, $c, $d, $e, $f, $g):Response
  {
    $parts = [ $a, $b, $c, $d, $e, $f, $g ];
    $request = implode('/', array_filter($parts));
    if (!empty($request)) {
      return self::grumble(
        $this->l->t('Post to endpoint "%s" not implemented.', $request));
    } else {
      return self::grumble(
        $this->l->t('Post to base URL of app "%s" not allowed.', $this->appName()));
    }
  }
  // phpcs:enable Squiz.Commenting.FunctionComment.MissingParamTag

  // phpcs:disable Squiz.Commenting.FunctionComment.MissingParamTag
  /**
   * @return Response
   *
   * @NoAdminRequired
   * @NoCSRFRequired
   *
   * @SuppressWarnings(PHPMD.ShortVariable)
   */
  public function get($a, $b, $c, $d, $e, $f, $g):Response
  {
    $parts = [ $a, $b, $c, $d, $e, $f, $g ];
    $request = implode('/', array_filter($parts));
    return self::grumble(
      $this->l->t('Get from endpoint "%s" not implemented.', $request));
  }
  // phpcs:enable Squiz.Commenting.FunctionComment.MissingParamTag
}
