<?php
/**
 * @author    Fabio Fantoni <fabio.fantoni@m2r.biz>
 * @copyright 2026 Fabio Fantoni
 * @license   AGPL-3.0-or-later
 *
 * Replays what the server does when it updates an app from the app store
 * (occ app:update, the web UI, or `occ upgrade` for the apps it disabled as
 * incompatible): the installed version is registered while bootstrapping,
 * then the files are replaced and the app is upgraded in the same PHP
 * process. The classes loaded by the old version stay in memory, so
 * incompatible changes to them only show up this way, not with a plain
 * `occ upgrade` after replacing the files.
 *
 * Usage, from the server root as the web server user:
 *   php in-process-update.php <app tarball>
 */

require getcwd() . '/lib/base.php';

$appId = 'files_archive';
$tarball = $argv[1] ?? '';
if (!is_file($tarball)) {
  fwrite(STDERR, "usage: php in-process-update.php <app tarball>\n");
  exit(2);
}

$appManager = \OCP\Server::get(\OCP\App\IAppManager::class);
$appPath = $appManager->getAppPath($appId);
$loaded = array_filter(
  [...get_declared_classes(), ...get_declared_traits(), ...get_declared_interfaces()],
  fn(string $name) => str_starts_with($name, 'OCA\\FilesArchive\\'),
);
echo 'Classes of the installed version loaded at bootstrap: ' . count($loaded) . PHP_EOL;

$command = 'rm -rf ' . escapeshellarg($appPath)
  . ' && tar -xzf ' . escapeshellarg($tarball) . ' -C ' . escapeshellarg(dirname($appPath));
passthru($command, $status);
if ($status !== 0) {
  fwrite(STDERR, "replacing the app files failed\n");
  exit(1);
}

$result = $appManager->upgradeApp($appId);
echo 'upgradeApp: ' . var_export($result, true) . PHP_EOL;
exit($result ? 0 : 1);
