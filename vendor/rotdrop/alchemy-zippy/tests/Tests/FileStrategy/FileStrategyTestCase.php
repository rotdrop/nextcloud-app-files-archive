<?php

declare(strict_types=1);

namespace Alchemy\Zippy\Tests\FileStrategy;

use Alchemy\Zippy\Adapter\AdapterInterface;
use Alchemy\Zippy\Exception\RuntimeException;
use Alchemy\Zippy\Tests\TestCase;
use Alchemy\Zippy\FileStrategy\FileStrategyInterface;

abstract class FileStrategyTestCase extends TestCase
{
    use AbstractFileStrategyTrait;

    public function testGetFileExtensionShouldReturnAnString()
    {
        $that = $this;
        $container = $this->createStub('\Alchemy\Zippy\Adapter\AdapterContainer');
        $container
                ->method('offsetGet')
                ->willReturnCallback(function ($offset) use ($that) {
                    if (array_key_exists('Alchemy\Zippy\Adapter\AdapterInterface', class_implements($offset))) {
                        return $that->getMock('Alchemy\Zippy\Adapter\AdapterInterface');
                    }

                    return null;
                });

        $extension = $this->getStrategy($container)->getFileExtension();

        $this->assertNotSame('', trim($extension));
        $this->assertIsString($extension);
    }

    public function testGetAdaptersShouldReturnAnArrayOfAdapter()
    {
        $that = $this;
        $container = $this->createStub('\Alchemy\Zippy\Adapter\AdapterContainer');
        $container
                ->method('offsetGet')
                ->willReturnCallback(function ($offset) use ($that) {
                    if (array_key_exists('Alchemy\Zippy\Adapter\AdapterInterface', class_implements($offset))) {
                        // return $that->getMockBuilder('\Alchemy\Zippy\Adapter\AdapterInterface')->getMock();
                        return $that->createStub('\Alchemy\Zippy\Adapter\AdapterInterface');
                    }

                    return null;
                });

        $adapters = $this->getStrategy($container)->getAdapters();

        $this->assertIsArray($adapters);

        $this->assertContainsOnlyInstancesOf('Alchemy\\Zippy\\Adapter\\AdapterInterface', $adapters);
    }

    public function testGetAdaptersShouldReturnAnArrayOfAdapterEvenIfAdapterRaiseAnException()
    {
        $container = $this->createStub('\Alchemy\Zippy\Adapter\AdapterContainer');
        $container
            ->method('offsetGet')
            ->willThrowException(new RuntimeException());

        $adapters = $this->getStrategy($container)->getAdapters();

        $this->assertIsArray($adapters);

        $this->assertContainsOnlyInstancesOf('Alchemy\\Zippy\\Adapter\\AdapterInterface', $adapters);
    }

    /**
     * @return FileStrategyInterface
     */
    abstract protected function getStrategy($container);
}
