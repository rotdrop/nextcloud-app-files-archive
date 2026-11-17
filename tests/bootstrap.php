<?php

// Unit tests must not require a Nextcloud server install. Autoload the app
// classes only; tests that need OC/OCP belong in tests/Integration.
spl_autoload_register(static function (string $class): void {
  $prefix = 'OCA\\FilesArchive\\';
  if (!str_starts_with($class, $prefix)) {
    return;
  }
  $file = dirname(__DIR__) . '/lib/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
  if (is_file($file)) {
    require_once $file;
  }
});
