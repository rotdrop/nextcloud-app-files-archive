#!/usr/bin/env php
<?php
/**
 * Archive Manager for Nextcloud
 *
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2024, 2026 Claus-Justus Heine <himself@claus-justus-heine.de>
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

// phpcs:disable PSR1.Files.SideEffects

ini_set('display_errors', 'stderr');

$appDir = realpath(__DIR__) . '/..';
define('ROT_DROP_DEV_SCRIPTS_APP_DIR', $appDir);

try {
  require_once(__DIR__ . '/lib/scripts/console-setup.php');
  require_once($appDir . '/vendor/autoload.php');
  // require_once($appDir . '/vendor-wrapped/autoload.php');
  require_once($appDir . '/vendor-bin/typescript-transformer/vendor/autoload.php');
} catch (\Throwable $t) {
  fwrite(STDERR, 'Composer autoloads not set up: ' . $t->getMessage() . PHP_EOL);
  exit(1);
}

\OC::$composerAutoloader->addPsr4(
  \OCA\RotDrop\DevScripts\PhpToTypeScript::class . '\\',
  __DIR__ . '/lib/scripts/php-to-typescript',
  true,
);
\OC::$composerAutoloader->addPsr4(
  \OCA\RotDrop\Toolkit::class . '\\',
  $appDir . '/php-toolkit/',
  true,
);

use OCA\FilesArchive\Toolkit\Console\ConsoleOutput;
use OCA\RotDrop\DevScripts\PhpToTypeScript;

// store output of different transformers in different files

$excludes = [
  'lib/Toolkit/Doctrine',
];

$scopedNamespaces = [
  // \Doctrine::class,
  // \Carbon::class,
  // \Ramsey\Uuid::class,
];

use Spatie\TypeScriptTransformer\Collectors\EnumCollector;
use OCA\RotDrop\DevScripts\PhpToTypeScript\DTOCollector;

$collectors = [
  EnumCollector::class,
  DTOCollector::class,
];

$phpToTypeScript = new PhpToTypeScript\PhpToTypeScript(
  devScriptsFolder: __DIR__,
  excludes: $excludes,
  scopedNamespaces: $scopedNamespaces,
  collectors: $collectors,
);

try {
  $phpToTypeScript->run(
    input: new \Symfony\Component\Console\Input\ArgvInput,
    output: \OCP\Server::get(ConsoleOutput::class),
  );
} catch (Throwable $t) {
  fwrite(STDERR, 'Dependency injection not set up: ' . $t->getMessage() . PHP_EOL . print_r($t->getTrace(), true) . PHP_EOL);
  exit(1);
}
