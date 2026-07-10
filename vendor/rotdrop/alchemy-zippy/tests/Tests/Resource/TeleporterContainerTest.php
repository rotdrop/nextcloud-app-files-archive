<?php

declare(strict_types=1);

namespace Alchemy\Zippy\Tests\Resource;

use Alchemy\Zippy\Tests\TestCase;
use Alchemy\Zippy\Resource\TeleporterContainer;

#[\PHPUnit\Framework\Attributes\CoversMethod(\Alchemy\Zippy\Resource\TeleporterContainer::class, 'fromResource')]
final class TeleporterContainerTest extends TestCase
{
    // _AT_[\PHPUnit\Framework\Attributes\DataProvider('provideResourceData')]
    public function testFromResource()
    {
        $container = TeleporterContainer::load();

        foreach ($this->provideResourceData() as $data) {
            [$resource, $classname] = $data;
            $this->assertInstanceOf($classname, $container->fromResource($resource));
        }
    }

    public function testFromResourceThatFails()
    {
        $this->expectException(\Alchemy\Zippy\Exception\InvalidArgumentException::class);
        $container = TeleporterContainer::load();
        $container->fromResource($this->createResource(array()));
    }

    public function provideResourceData(): \Iterator
    {
        yield array($this->createResource(__FILE__), 'Alchemy\Zippy\Resource\Teleporter\LocalTeleporter');
        yield array($this->createResource(fopen(__FILE__, 'rb')), 'Alchemy\Zippy\Resource\Teleporter\StreamTeleporter');
        yield array($this->createResource('ftp://192.168.1.1/images/elephant.png'), 'Alchemy\Zippy\Resource\Teleporter\StreamTeleporter');
        yield array($this->createResource('http://127.0.0.1:8080/plus-badge.png'), 'Alchemy\Zippy\Resource\Teleporter\GenericTeleporter');
    }

    private function createResource($data)
    {
        $resource = $this->createStub('\Alchemy\Zippy\Resource\Resource');

        $resource
            ->method('getOriginal')
            ->willReturn($data);

        return $resource;
    }

    public function testLoad()
    {
        $container = TeleporterContainer::load();

        $this->assertInstanceOf('Alchemy\Zippy\Resource\TeleporterContainer', $container);

        $this->assertInstanceOf('Alchemy\Zippy\Resource\Teleporter\GenericTeleporter', $container['guzzle-teleporter']);
        $this->assertInstanceOf('Alchemy\Zippy\Resource\Teleporter\StreamTeleporter', $container['stream-teleporter']);
        $this->assertInstanceOf('Alchemy\Zippy\Resource\Teleporter\LocalTeleporter', $container['local-teleporter']);
    }
}
