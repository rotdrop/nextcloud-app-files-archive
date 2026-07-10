<?php

declare(strict_types=1);

namespace Alchemy\Zippy\Tests\Archive;

use Alchemy\Zippy\Tests\TestCase;
use Alchemy\Zippy\Archive\ArchiveInterface;
use Alchemy\Zippy\Archive\Archive;

final class ArchiveTest extends TestCase
{
    public function testNewInstance(): \Alchemy\Zippy\Archive\Archive
    {
        $archive = new Archive($this->getResource('location'), $this->getAdapterMock(true), $this->getResourceManagerMock());

        $this->assertInstanceOf(\Alchemy\Zippy\Archive\ArchiveInterface::class, $archive);

        return $archive;
    }

    public function testCount()
    {
        $mockAdapter = $this->getAdapterMock();

        $mockAdapter
            ->expects($this->once())
            ->method('listMembers')
            ->willReturn(array('1', '2'));

        $archive = new Archive($this->getResource('location'), $mockAdapter, $this->getResourceManagerMock());

        $this->assertCount(2, $archive);
    }

    public function testGetMembers()
    {
        $mockAdapter = $this->getAdapterMock();

        $resource = $this->getResource('location');

        $mockAdapter
            ->expects($this->once())
            ->method('listMembers')
            ->with($resource)
            ->willReturn(array('1', '2'));

        $archive = new Archive($this->getResource('location'), $mockAdapter, $this->getResourceManagerMock());

        $members = $archive->getMembers();

        $this->assertTrue(is_array($members));
        $this->assertCount(2, $members);
    }

    public function testAddMembers()
    {
        $resource = $this->getResource('location');

        $mockAdapter = $this->getAdapterMock();

        $mockAdapter
            ->expects($this->once())
            ->method('add')
            ->with($resource, array('hello'), true);

        $resourceManager = $this->getResourceManagerMock();

        $archive = new Archive($resource, $mockAdapter, $resourceManager);

        $this->assertEquals($archive, $archive->addMembers(array('hello')));
    }

    public function testRemoveMember()
    {
        $mockAdapter = $this->getAdapterMock();

        $mockAdapter
            ->expects($this->once())
            ->method('remove');

        $archive = new Archive($this->getResource('location'), $mockAdapter, $this->getResourceManagerMock());

        $this->assertEquals($archive, $archive->removeMembers('hello'));
    }

    private function getAdapterMock(bool $stub = false)
    {
        return $stub
            ? $this->createStub('\Alchemy\Zippy\Adapter\AdapterInterface')
            : $this->createMock('\Alchemy\Zippy\Adapter\AdapterInterface');
    }
}
