<?php

declare(strict_types=1);

namespace Alchemy\Zippy\Tests;

use Alchemy\Zippy\Zippy;
use Alchemy\Zippy\Exception\NoAdapterOnPlatformException;
use Alchemy\Zippy\Exception\FormatNotSupportedException;
use Alchemy\Zippy\Exception\RuntimeException;

final class ZippyTest extends TestCase
{
    public function testItShouldCreateAnArchive()
    {
        $filename = 'file.zippo';
        $fileToAdd = 'file1';
        $recursive = true;

        $adapter = $this->getSupportedAdapter();

        $adapter->expects($this->once())
            ->method('create')
            ->with($filename, $fileToAdd, $recursive);

        $adapters = array($adapter);
        $strategy = $this->getStrategy('zippo', $adapters);

        $zippy = new Zippy($this->getContainer());
        $zippy->addStrategy($strategy);

        $zippy->create($filename, $fileToAdd, $recursive);
    }

    public function testItShouldCreateAnArchiveByForcingType()
    {
        $filename = 'file';
        $fileToAdd = 'file1';
        $recursive = true;

        $adapter = $this->getSupportedAdapter();

        $adapter->expects($this->once())
            ->method('create')
            ->with($filename, $fileToAdd, $recursive);

        $adapters = array($adapter);
        $strategy = $this->getStrategy('zippo', $adapters);

        $zippy = new Zippy($this->getContainer());
        $zippy->addStrategy($strategy);

        $zippy->create($filename, $fileToAdd, $recursive, 'zippo');
    }

    public function testItShouldNotCreateAndThrowAnException()
    {
        $filename = 'file';
        $fileToAdd = 'file1';
        $recursive = true;

        $adapter = $this->getSupportedAdapter();

        $adapter->expects($this->never())->method('create');

        $adapters = array($adapter);
        $strategy = $this->getStrategy('zippo', $adapters);

        $zippy = new Zippy($this->getContainer());
        $zippy->addStrategy($strategy);

        try {
            $zippy->create($filename, $fileToAdd, $recursive, 'zippotte');
            $this->fail('Should have raised an exception');
        } catch (RuntimeException $e) {

        }
    }

    public function testItShouldOpenAnArchive()
    {
        $filename = 'file.zippo';

        $adapter = $this->getSupportedAdapter();

        $adapter->expects($this->once())
            ->method('open')
            ->with($filename);

        $adapters = array($adapter);
        $strategy = $this->getStrategy('zippo', $adapters);

        $zippy = new Zippy($this->getContainer());
        $zippy->addStrategy($strategy);

        $zippy->open($filename);
    }

    public function testItShouldExposeContainerPassedOnConstructor()
    {
        $container = $this->getContainer();

        $zippy = new Zippy($container);

        $this->assertEquals($container, $zippy->adapters);
    }

    public function testItShouldRegisterStrategies()
    {
        $adapters = array($this->getSupportedAdapter(true));
        $strategy = $this->getStrategy('zippo', $adapters);

        $zippy = new Zippy($this->getContainer());
        $zippy->addStrategy($strategy);

        $this->assertEquals(array('zippo' => array($strategy)), $zippy->getStrategies());
    }

    public function testRegisterTwoStrategiesWithSameExtensionShouldBeginRightOrder()
    {
        $adapters1 = array($this->getSupportedAdapter(true));
        $strategy1 = $this->getStrategy('zippo', $adapters1);

        $adapters2 = array($this->getSupportedAdapter(true));
        $strategy2 = $this->getStrategy('zippo', $adapters2);

        $zippy = new Zippy($this->getContainer());
        $zippy->addStrategy($strategy1);
        $zippy->addStrategy($strategy2);

        $this->assertEquals(array('zippo' => array($strategy2, $strategy1)), $zippy->getStrategies());
    }

    public function testRegisterAStrategyTwiceShouldMoveItToLastAdded()
    {
        $adapters1 = array($this->getSupportedAdapter(true));
        $strategy1 = $this->getStrategy('zippo', $adapters1);

        $adapters2 = array($this->getSupportedAdapter(true));
        $strategy2 = $this->getStrategy('zippo', $adapters2);

        $zippy = new Zippy($this->getContainer());
        $zippy->addStrategy($strategy1);
        $zippy->addStrategy($strategy2);
        $zippy->addStrategy($strategy1);

        $this->assertEquals(array('zippo' => array($strategy1, $strategy2)), $zippy->getStrategies());
    }

    public function testItShouldReturnAnAdapterCorrespondingToTheRightStrategy()
    {
        $adapters = array($this->getSupportedAdapter(true));
        $strategy = $this->getStrategy('zippo', $adapters);

        $zippy = new Zippy($this->getContainer());
        $zippy->addStrategy($strategy);

        $this->assertEquals($adapters[0], $zippy->getAdapterFor('zippo'));
        $this->assertEquals($adapters[0], $zippy->getAdapterFor('.zippo'));
        $this->assertEquals($adapters[0], $zippy->getAdapterFor('ziPPo'));
        $this->assertEquals($adapters[0], $zippy->getAdapterFor('.ZIPPO'));
    }

    public function testItShouldThrowAnExceptionIfNoAdapterSupported()
    {
        $adapters = array($this->getNotSupportedAdapter(true));
        $strategy = $this->getStrategy('zippo', $adapters);

        $zippy = new Zippy($this->getContainer());
        $zippy->addStrategy($strategy);

        $this->expectException(NoAdapterOnPlatformException::class);
        $zippy->getAdapterFor('zippo');

        $this->fail('Should have raised an exception');
    }

    public function testItShouldThrowAnExceptionIfFormatNotSupported()
    {
        $adapters = array($this->getSupportedAdapter(true));
        $strategy = $this->getStrategy('zippotte', $adapters);

        $zippy = new Zippy($this->getContainer());
        $zippy->addStrategy($strategy);

        $this->expectException(FormatNotSupportedException::class);
        $zippy->getAdapterFor('zippo');

        $this->fail('Should have raised an exception');
    }

    public function testLoadShouldRegisterStrategies()
    {
        $zippy = Zippy::load();

        $this->assertCount(7, $zippy->getStrategies());

        $this->assertArrayHasKey('zip', $zippy->getStrategies());
        $this->assertArrayHasKey('tar', $zippy->getStrategies());
        $this->assertArrayHasKey('tar.gz', $zippy->getStrategies());
        $this->assertArrayHasKey('tar.bz2', $zippy->getStrategies());
        $this->assertArrayHasKey('tbz2', $zippy->getStrategies());
        $this->assertArrayHasKey('tb2', $zippy->getStrategies());
        $this->assertArrayHasKey('tgz', $zippy->getStrategies());
    }

    private function getStrategy($extension, $adapters)
    {
        $strategy = $this->createStub('\Alchemy\Zippy\FileStrategy\FileStrategyInterface');

        $strategy
            ->method('getFileExtension')
            ->willReturn($extension);

        $strategy
            ->method('getAdapters')
            ->willReturn($adapters);

        return $strategy;
    }

    private function getSupportedAdapter(bool $stub = false)
    {
        $adapter = $stub
            ? $this->createStub('\Alchemy\Zippy\Adapter\AdapterInterface')
            : $this->createMock('\Alchemy\Zippy\Adapter\AdapterInterface');
        $adapter
            ->method('isSupported')
            ->willReturn(true);

        return $adapter;
    }

    private function getNotSupportedAdapter(bool $stub = false)
    {
        $adapter = $stub
            ? $this->createStub('\Alchemy\Zippy\Adapter\AdapterInterface')
            : $this->createMock('\Alchemy\Zippy\Adapter\AdapterInterface');
        $adapter
            ->method('isSupported')
            ->willReturn(false);

        return $adapter;
    }

    private function getContainer()
    {
        return $this->createStub('\Alchemy\Zippy\Adapter\AdapterContainer');
    }
}
