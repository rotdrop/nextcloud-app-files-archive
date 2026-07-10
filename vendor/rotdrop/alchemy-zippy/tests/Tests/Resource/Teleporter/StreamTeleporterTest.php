<?php

declare(strict_types=1);

namespace Alchemy\Zippy\Tests\Resource\Teleporter;

use Alchemy\Zippy\Resource\Resource;
use Alchemy\Zippy\Resource\Teleporter\StreamTeleporter;

final class StreamTeleporterTest extends TeleporterTestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('provideContexts')]
    public function testTeleport($context)
    {
        $teleporter = StreamTeleporter::create();

        $target = 'plop-badge.php';
        $resource = new Resource(fopen(__FILE__, 'rb'), $target);

        if (is_file($context . '/' . $target)) {
            unlink($context . '/' . $target);
        }

        $teleporter->teleport($resource, $context);

        $this->assertFileExists($context . '/' . $target);
        unlink($context . '/' . $target);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('provideContexts')]
    public function testTeleportInNonStreamMode($context)
    {
        $teleporter = StreamTeleporter::create();

        $target = 'plop-badge.php';
        $resource = new Resource(__FILE__, $target);

        if (is_file($context . '/' . $target)) {
            unlink($context . '/' . $target);
        }

        $teleporter->teleport($resource, $context);

        $this->assertFileExists($context . '/' . $target);
        unlink($context . '/' . $target);
    }

    public function testCreate()
    {
        $this->assertInstanceOf('Alchemy\Zippy\Resource\Teleporter\StreamTeleporter', StreamTeleporter::create());
    }
}
