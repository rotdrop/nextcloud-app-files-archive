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

use OCP\Files\IMimeTypeDetector;
use OCP\Files\IMimeTypeLoader;
use OCP\Migration\IOutput;
use OCP\Migration\IRepairStep;
use Psr\Log\LoggerInterface as ILogger;

use OCA\FilesArchive\Service\MimeTypeService;

/**
 * Make the archive MIME-types known to this Nextcloud instance.
 *
 * The extension to MIME-type mappings themselves are registered at runtime
 * with the MIME-type detector during the app bootstrap (see
 * \OCA\FilesArchive\AppInfo\Application and
 * \OCA\FilesArchive\Toolkit\Service\MimeTypeService::registerMimeTypeMappings()).
 * This repair step takes care of the two things which cannot be done at
 * runtime per request:
 *
 * - persist the MIME-types in the database and fix the `filecache` rows of
 *   already existing archive files (the same work as
 *   `occ maintenance:mimetype:update-db`), using only public API;
 *
 * - register icon aliases (every archive type should use the `package/x-generic`
 *   icon) for the archive MIME-types which the core does not know about yet.
 *   Aliases have no runtime-registration API, so this is the only situation in
 *   which we touch the instance configuration -- and only when something is
 *   actually missing and the configuration is writable.
 */
class RegisterMimeTypes implements IRepairStep
{
  use \OCA\FilesArchive\Toolkit\Traits\LoggerTrait;

  const MIMETYPE_ALIASES_FILE = 'mimetypealiases.json';

  // phpcs:disable Squiz.Commenting.FunctionComment.Missing
  public function __construct(
    protected string $appName,
    protected ILogger $logger,
    protected MimeTypeService $mimeTypeService,
    protected IMimeTypeDetector $mimeTypeDetector,
    protected IMimeTypeLoader $mimeTypeLoader,
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
    // getSupportedArchiveMimeTypes() also registers our extension to
    // MIME-type mappings with the detector for the current process, so that
    // the database update below sees them when running via occ.
    $archiveMimeTypes = $this->mimeTypeService->getSupportedArchiveMimeTypes();

    $this->updateDatabaseMimeTypes($output, $archiveMimeTypes);
    $this->registerMissingAliases($output);

    // @todo Implement a mime-type cleanup on uninstall (not sooo important
    // but should be done one day).
  }

  /**
   * Make sure the archive MIME-types exist in the database and fix the
   * `filecache` rows of already existing archive files. This mirrors
   * `occ maintenance:mimetype:update-db` but is restricted to our own
   * MIME-types and only touches the filecache for newly added ones.
   *
   * @param IOutput $output
   *
   * @param array<string, string> $archiveMimeTypes Extension to MIME-type map.
   *
   * @return void
   */
  protected function updateDatabaseMimeTypes(IOutput $output, array $archiveMimeTypes):void
  {
    // Snapshot which MIME-types already exist before we add anything, so that
    // multiple extensions sharing a freshly added MIME-type all get their
    // filecache rows updated.
    $existedBefore = [];
    foreach ($archiveMimeTypes as $mimeType) {
      if (!array_key_exists($mimeType, $existedBefore)) {
        $existedBefore[$mimeType] = $this->mimeTypeLoader->exists($mimeType);
      }
    }

    foreach ($archiveMimeTypes as $extension => $mimeType) {
      // getId() inserts the MIME-type if it is not present yet.
      $mimeTypeId = $this->mimeTypeLoader->getId($mimeType);
      if ($existedBefore[$mimeType]) {
        continue;
      }
      $touched = $this->mimeTypeLoader->updateFilecache((string)$extension, $mimeTypeId);
      $this->logInfo('Registered archive MIME-type "' . $mimeType . '" (.' . $extension . '), updated ' . $touched . ' filecache rows.');
    }
  }

  /**
   * Register the icon aliases for the archive MIME-types which the core does
   * not know about. Aliases cannot be registered at runtime, so they have to
   * live in the instance configuration. We only write the configuration when
   * something is actually missing -- the comparison is done against the
   * effective alias set, which already folds in any pre-existing custom
   * `config/mimetypealiases.json`, so a file written by an admin or an earlier
   * run is respected and never clobbered.
   *
   * @param IOutput $output
   *
   * @return void
   */
  protected function registerMissingAliases(IOutput $output):void
  {
    $appFile = __DIR__ . '/../../config/nextcloud/' . self::MIMETYPE_ALIASES_FILE;
    if (!file_exists($appFile)) {
      return;
    }
    $appAliases = json_decode(file_get_contents($appFile), true) ?? [];
    $appAliases = array_filter(
      $appAliases,
      fn(string $key) => !str_starts_with($key, '_'),
      ARRAY_FILTER_USE_KEY,
    );

    // getAllAliases() == dist aliases + any existing config/mimetypealiases.json.
    $effectiveAliases = $this->mimeTypeDetector->getAllAliases();
    $missingAliases = array_diff_key($appAliases, $effectiveAliases);
    if (empty($missingAliases)) {
      // Everything is already aliased (by the core or by an existing override),
      // so there is no reason to touch the configuration.
      return;
    }

    $coreFile = \OC::$configDir . self::MIMETYPE_ALIASES_FILE;

    // Nextcloud only ships *.dist.json in resources/config/; the override in
    // config/ usually does not exist yet, so probe the directory rather than
    // is_writable() of a non-existing path.
    if (file_exists($coreFile)) {
      if (!is_writable($coreFile)) {
        $this->logWarn('Cannot add archive icon aliases: "' . $coreFile . '" is not writable. Unknown archive types will use the generic file icon.');
        return;
      }
      $existing = json_decode(file_get_contents($coreFile), true) ?? [];
    } elseif (is_writable(dirname($coreFile))) {
      $existing = [];
    } else {
      $this->logWarn('Cannot add archive icon aliases: "' . dirname($coreFile) . '" is not writable. Unknown archive types will use the generic file icon.');
      return;
    }

    $merged = array_merge($existing, $missingAliases);
    if ($merged == $existing) {
      return;
    }

    file_put_contents($coreFile, json_encode($merged, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    $message = 'Added archive icon aliases to "' . $coreFile . '". Run "occ maintenance:mimetype:update-js" to refresh the web UI icons.';
    $this->logInfo($message);
    $output->info($message);
  }
}
