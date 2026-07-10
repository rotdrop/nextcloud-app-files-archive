<?php

declare(strict_types=1);

namespace Alchemy\Zippy\Tests\Resource\Teleporter;

use Alchemy\Zippy\Resource\Resource;
use Alchemy\Zippy\Resource\Teleporter\LocalTeleporter;

final class LocalTeleporterTest extends TeleporterTestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('provideContexts')]
    public function testTeleport($context)
    {
        $teleporter = LocalTeleporter::create();

        $target = 'plop-badge.php';
        $resource = new Resource(__FILE__, $target);

        if (is_file($context . '/' . $target)) {
            unlink($context . '/' . $target);
        }

        $teleporter->teleport($resource, $context);

        $this->assertFileExists($context . '/' . $target);
        unlink($context . '/' . $target);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('provideContexts')]
    public function testTeleportAStream($context)
    {
        $teleporter = LocalTeleporter::create();

        $target = 'plop-badge.php';
        $resource = new Resource('file://' . __FILE__, $target);

        if (is_file($context . '/' . $target)) {
            unlink($context . '/' . $target);
        }

        $teleporter->teleport($resource, $context);

        $this->assertFileExists($context . '/' . $target);
        unlink($context . '/' . $target);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('provideInvalidSources')]
    public function testTeleportOnNonExistentFile($source)
    {
        $this->expectException(\Alchemy\Zippy\Exception\InvalidArgumentException::class);

        $teleporter = LocalTeleporter::create();

        $target = 'plop-badge.php';
        $resource = new Resource($source, $target);

        $teleporter->teleport($resource, __DIR__);
    }

    public static function provideInvalidSources(): \Iterator
    {
        yield array('file://path/to/nonexistent/file');
        yield array('/path/to/nonexistent/file');
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('provideContexts')]
    public function testTeleportADir($context)
    {
        $teleporter = LocalTeleporter::create();

        $target = 'plop-badge-dir';
        $resource = new Resource(__DIR__ . '/plop-badge', $target);

        if (!is_dir(__DIR__ . '/plop-badge')) {
            mkdir(__DIR__ . '/plop-badge');
        }

        if (!is_file(__DIR__ . '/plop-badge/test-file.png')) {
            touch(__DIR__ . '/plop-badge/test-file.png');
        }

        if (file_exists($context . '/' . $target . '/test-file.png')) {
            unlink($context . '/' . $target . '/test-file.png');
        }

        $teleporter->teleport($resource, $context);

        $this->assertFileExists($context . '/' . $target . '/test-file.png');
        unlink($context . '/' . $target . '/test-file.png');
        rmdir($context . '/' . $target);
    }

    public function testCreate()
    {
        $this->assertInstanceOf('Alchemy\Zippy\Resource\Teleporter\LocalTeleporter', LocalTeleporter::create());
    }
}
