<?php

declare(strict_types=1);

namespace Alchemy\Zippy\Tests\FileStrategy;

use InvalidArgumentException;

use Alchemy\Zippy\Adapter\AdapterContainer;
use Alchemy\Zippy\Tests\TestCase;
use Alchemy\Zippy\Exception\RuntimeException;

trait AbstractFileStrategyTrait
{
    public function testGetAdaptersWithNoDefinedServices()
    {
        $this->expectException(InvalidArgumentException::class);

        $container = AdapterContainer::load();

        $stub = $this->getMockBuilder(static::STRATEGY_CLASS)
          ->setConstructorArgs(array($container))
          ->onlyMethods(['getServiceNames'])
          ->getMock();
        $stub
            ->expects($this->atLeastOnce())
            ->method('getServiceNames')
            ->willReturn(array(
                'Unknown\Services'
            ));


        $adapters = $stub->getAdapters();
        $this->assertIsArray($adapters);
        $this->assertCount(0, $adapters);
    }

    public function testGetAdapters()
    {
        $container = AdapterContainer::load();

        $stub = $this->getMockBuilder(static::STRATEGY_CLASS)
            ->setConstructorArgs(array($container))
            ->onlyMethods(['getServiceNames'])
            ->getMock();
        $stub
            ->expects($this->atLeastOnce())
            ->method('getServiceNames')
            ->willReturn(array(
                'Alchemy\\Zippy\\Adapter\\ZipAdapter',
                'Alchemy\\Zippy\\Adapter\\ZipExtensionAdapter'
            ));

        $adapters = $stub->getAdapters();
        $this->assertIsArray($adapters);
        $this->assertCount(2, $adapters);
        $this->assertContainsOnlyInstancesOf('Alchemy\\Zippy\\Adapter\\AdapterInterface', $adapters);
    }

    public function testGetAdaptersWithAdapterThatRaiseAnException()
    {
        $adapterMock = $this->createStub('\Alchemy\Zippy\Adapter\AdapterInterface');
        $container = $this->createMock('\Alchemy\Zippy\Adapter\AdapterContainer');
        $container
            ->expects($this->exactly(2))
            ->method('offsetGet')
            ->willReturnCallback(
                function(string $class) use ($adapterMock) {
                    switch ($class) {
                        case 'Alchemy\\Zippy\\Adapter\\ZipAdapter':
                            return $adapterMock;
                        case 'Alchemy\\Zippy\\Adapter\\ZipExtensionAdapter':
                            throw new RuntimeException();
                    }
                }
            );

        $stub = $this->getMockBuilder(static::STRATEGY_CLASS)
          ->setConstructorArgs(array($container))
          ->onlyMethods(['getServiceNames'])
          ->getMock();
        $stub
            ->expects($this->atLeastOnce())
            ->method('getServiceNames')
            ->willReturn(array(
                'Alchemy\\Zippy\\Adapter\\ZipAdapter',
                'Alchemy\\Zippy\\Adapter\\ZipExtensionAdapter'
            ));

        $adapters = $stub->getAdapters();
        $this->assertIsArray($adapters);
        $this->assertCount(1, $adapters);
        foreach ($adapters as $adapter) {
            $this->assertSame($adapterMock, $adapter);
        }
    }
}
