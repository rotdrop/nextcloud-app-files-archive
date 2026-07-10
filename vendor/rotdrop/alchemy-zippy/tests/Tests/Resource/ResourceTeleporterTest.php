<?php

declare(strict_types=1);

namespace Alchemy\Zippy\Tests\Resource;

use Alchemy\Zippy\Tests\TestCase;
use Alchemy\Zippy\Resource\ResourceTeleporter;

final class ResourceTeleporterTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\DoesNotPerformAssertions]
    public function testConstruct(): \Alchemy\Zippy\Resource\ResourceTeleporter
    {
        $container = $this->createStub('\Alchemy\Zippy\Resource\TeleporterContainer');

        $teleporter = new ResourceTeleporter($container);

        return $teleporter;
    }

    public function testTeleport()
    {
        $context = 'supa-context';
        $resource = $this->createStub('\Alchemy\Zippy\Resource\Resource');

        $container = $this->createMock('\Alchemy\Zippy\Resource\TeleporterContainer');

        $teleporter = $this->createMock('\Alchemy\Zippy\Resource\Teleporter\TeleporterInterface');
        $teleporter->expects($this->once())
            ->method('teleport')
            ->with($resource, $context);

        $container->expects($this->once())
            ->method('fromResource')
            ->with($resource)
            ->willReturn($teleporter);

        $resourceTeleporter = new ResourceTeleporter($container);
        $resourceTeleporter->teleport($context, $resource);
    }

    public function testCreate()
    {
        $this->assertInstanceOf('Alchemy\Zippy\Resource\ResourceTeleporter', ResourceTeleporter::create());
    }
}
