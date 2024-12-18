<?php
/**
 * @author    Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2024 Claus-Justus Heine
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

namespace OCA\FilesArchive\Migration;

use Throwable;

use OCP\Migration\IOutput;
use OCP\Migration\IRepairStep;
use Psr\Log\LoggerInterface as ILogger;

use OCA\FilesArchive\Service\MimeTypeService;

/**
 * Register some extra mime-types, in particuluar in order to have custom
 * folder icons for the database storage. Nextcloud uses the `dir-MOUNTTYPE`
 * pseudo mime-type in order to select icons for directories.
 *
 * @see OCA\CAFEVDB\Storage\Database\MountProvider
 */
class RegisterMimeTypes implements IRepairStep
{
  use \OCA\FilesArchive\Toolkit\Traits\LoggerTrait;

  const MIMETYPE_MAPPING_FILE = 'mimetypemapping.json';
  const MIMETYPE_ALIASES_FILE = 'mimetypealiases.json';

  // phpcs:disable Squiz.Commenting.FunctionComment.Missing
  public function __construct(
    protected string $appName,
    protected ILogger $logger,
    protected MimeTypeService $mimeTypeService,
  ) {
  }
  // phpcs:enable

  /** {@inheritdoc} */
  public function getName()
  {
    return 'Register MIME types for ' . $this->appName;
  }

  /** {@inheritdoc} */
  public function run(IOutput $output)
  {
    $mimeData = [
      'mapping' => [
        'core' => \OC::$configDir . self::MIMETYPE_MAPPING_FILE,
        'app' => __DIR__ . '/../../config/nextcloud/' . self::MIMETYPE_MAPPING_FILE,
      ],
      'aliases' => [
        'core' => \OC::$configDir . self::MIMETYPE_ALIASES_FILE,
        'app' => __DIR__ . '/../../config/nextcloud/' . self::MIMETYPE_ALIASES_FILE,
      ],
    ];

    foreach ($mimeData as $key => $dataSet) {
      $coreFile = $dataSet['core'];

      if (!is_writable($coreFile)) {
        $this->logError('Unable to update "' . $coreFile . '", file is not writable.');
        continue;
      } else {
        $this->logInfo('Modifying "' . $coreFile . '" ...');
      }

      switch ($key) {
        case 'aliases':
          try {
            $appFile = $dataSet['app'];
            if (file_exists($appFile)) {
              $appData = json_decode(file_get_contents($appFile), true);
            } else {
              $appData = [];
            }
          } catch (Throwable $t) {
            $this->logException($t);
            continue 2;
          }
          break;
        case 'mapping':
          $appData = $this->mimeTypeService->getMissingMimeTypeMappings();
          break;
      }

      if (file_exists($coreFile)) {
        $coreData = json_decode(file_get_contents($coreFile), true);
        $data = array_merge($coreData, $appData);
      }
      file_put_contents($coreFile, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
      $this->logInfo('... added mime-data ' . print_r($dataSet['app'], true));
    }

    // @todo Check whether `occ maintenance:mimetype:update-js` and/or `occ
    // maintenance:mimetype:update-db` must be run. This should be automated.
    //
    // @todo Implement a mime-type cleanup on uninstall (not sooo important
    // but should be done one day).
  }
}
