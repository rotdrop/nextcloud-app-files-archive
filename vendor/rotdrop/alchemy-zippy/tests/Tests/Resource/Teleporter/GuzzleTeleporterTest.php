<?php

declare(strict_types=1);

namespace Alchemy\Zippy\Tests\Resource\Teleporter;

use Alchemy\Zippy\Resource\Teleporter\GuzzleTeleporter;
use Alchemy\Zippy\Resource\Resource;

final class GuzzleTeleporterTest extends TeleporterTestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('provideContexts')]
    public function testTeleport($context)
    {
        $teleporter = GuzzleTeleporter::create();

        $target = 'plop-badge.png';
        $resource = new Resource('http://127.0.0.1:8080/plus-badge.png', $target);

        if (is_file($context . '/' . $target)) {
            unlink($context . '/' . $target);
        }

        $teleporter->teleport($resource, $context);

        $this->assertFileExists($context . '/' . $target);
        unlink($context . '/' . $target);
    }

    public function testCreate()
    {
        $this->assertInstanceOf('Alchemy\Zippy\Resource\Teleporter\GuzzleTeleporter', GuzzleTeleporter::create());
    }
}
