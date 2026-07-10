<?php

declare(strict_types=1);

namespace Alchemy\Zippy\Tests;

use Alchemy\Zippy\Adapter\AdapterInterface;
use Alchemy\Zippy\Resource\PathUtil;
use Alchemy\Zippy\Resource\ResourceCollection;
use Alchemy\Zippy\Resource\Resource;
use Alchemy\Zippy\Adapter\VersionProbe\VersionProbeInterface;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    public static function getResourcesPath()
    {
        $dir = __DIR__ . '/../../../resources';

        if (!is_dir($dir)) {
            mkdir($dir);
        }

        return $dir;
    }

    protected function getResourceManagerMock($context = '', $elements = array())
    {
        $elements = array_map(function ($item) {
            return new Resource($item, $item);
        }, $elements);

        $collection = new ResourceCollection($context, $elements, false);

        $manager = $this->createStub('\Alchemy\Zippy\Resource\ResourceManager');

        $manager
            ->method('handle')
            ->willReturn($collection);

        return $manager;
    }

    protected function getResource($data = null)
    {
        $resource = $this->createStub('\Alchemy\Zippy\Adapter\Resource\ResourceInterface');

        if (null !== $data) {
            $resource
                ->method('getResource')
                ->willReturn($data);
        }

        return $resource;
    }

    protected function setProbeIsOk(AdapterInterface $adapter)
    {
        if (!method_exists($adapter, 'setVersionProbe')) {
            $this->fail('Trying to set a probe on an adapter that does not support it');
        }

        $probe = $this->createStub('\Alchemy\Zippy\Adapter\VersionProbe\VersionProbeInterface');
        $probe
            ->method('getStatus')
            ->willReturn(VersionProbeInterface::PROBE_OK);

        $adapter->setVersionProbe($probe);
    }

    protected function setProbeIsNotOk(AdapterInterface $adapter)
    {
        if (!method_exists($adapter, 'setVersionProbe')) {
            $this->fail('Trying to set a probe on an adapter that does not support it');
        }

        $probe = $this->createStub('\Alchemy\Zippy\Adapter\VersionProbe\VersionProbeInterface');
        $probe
            ->method('getStatus')
            ->willReturn(VersionProbeInterface::PROBE_NOTSUPPORTED);

        $adapter->setVersionProbe($probe);
    }

    protected function getMockedProcessBuilderFactory($mockedProcessBuilder, $creations = 1)
    {
        $mockedProcessBuilderFactory =
            $this->createMock('\Alchemy\Zippy\ProcessBuilder\ProcessBuilderFactoryInterface');

        $mockedProcessBuilderFactory
            ->expects($this->exactly($creations))
            ->method('create')
            ->willReturn($mockedProcessBuilder);

        return $mockedProcessBuilderFactory;
    }

    protected function getSuccessFullMockProcess($runs = 1)
    {
        $mockProcess = $this->createMock('\Symfony\Component\Process\Process');

        $mockProcess
            ->expects($this->exactly($runs))
            ->method('run');

        $mockProcess
            ->expects($this->exactly($runs))
            ->method('isSuccessful')
            ->willReturn(true);

        return $mockProcess;
    }

    protected function getExpectedAbsolutePathForTarget($target)
    {
        $directory = dirname($target);

        if (!is_dir($directory)) {
            throw new \InvalidArgumentException(sprintf('Unable to get the absolute path for %s', $target));
        }

        return realpath($directory).'/'.PathUtil::basename($target);
    }
}
