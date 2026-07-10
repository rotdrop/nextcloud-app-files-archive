<?php

declare(strict_types=1);

namespace Alchemy\Zippy\Tests\Adapter\BSDTar;

use Alchemy\Zippy\Tests\Adapter\AdapterTestCase;
use Alchemy\Zippy\Parser\ParserFactory;

abstract class BSDTarAdapterWithOptionsTestBase extends AdapterTestCase
{
    protected static $tarFile;

    /**
     * @var AbstractBSDTarAdapter
     */
    protected $adapter;

    public static function setUpBeforeClass(): void
    {
        $classname = static::getAdapterClassName();
        self::$tarFile = sprintf('%s/%s.tar', self::getResourcesPath(), $classname::getName());

        if (file_exists(self::$tarFile)) {
            unlink(self::$tarFile);
        }
    }

    public static function tearDownAfterClass(): void
    {
        if (file_exists(self::$tarFile)) {
            unlink(self::$tarFile);
        }
    }

    public function setUp(): void
    {
        $this->adapter = $this->provideSupportedAdapter();
    }

    private function provideAdapter()
    {
        $classname = static::getAdapterClassName();

        $inflator = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ProcessBuilderFactory')
                ->disableOriginalConstructor()
                ->onlyMethods(array('useBinary'))
                ->getMock();
        $inflator->expects($this->never())->method('useBinary');

        $outputParser = ParserFactory::create($classname::getName());

        $manager = $this->getResourceManagerMock(__DIR__);

        return new $classname($outputParser, $manager, $inflator, $inflator);
    }

    protected function provideNotSupportedAdapter()
    {
        $adapter = $this->provideAdapter();
        $this->setProbeIsNotOk($adapter);

        return $adapter;
    }

    protected function provideSupportedAdapter()
    {
        $adapter = $this->provideAdapter();
        $this->setProbeIsOk($adapter);

        return $adapter;
    }

    public function testNewinstance()
    {
        $classname = static::getAdapterClassName();
        $finder = $this->createStub('\Symfony\Component\Process\ExecutableFinder');
        $manager = $this->createStub('\Alchemy\Zippy\Resource\ResourceManager');

        $instance = $classname::newInstance(
            $finder,
            $manager,
            $this->createStub('\Alchemy\Zippy\ProcessBuilder\ProcessBuilderFactoryInterface'),
            $this->createStub('\Alchemy\Zippy\ProcessBuilder\ProcessBuilderFactoryInterface')
        );

        $this->assertInstanceOf($classname, $instance);
    }

    public function testCreateNoFiles()
    {
        $mockedProcessBuilder = $this->createMock('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder');

        $nullFile = defined('PHP_WINDOWS_VERSION_BUILD') ? 'NUL' : '/dev/null';

        $mockedProcessBuilder
            ->expects($this->exactly(6))
            ->method('add')
            ->willReturnMap([
                ['-c', $mockedProcessBuilder],
                [$this->getOptions(), $mockedProcessBuilder],
                ['-f', $mockedProcessBuilder],
                [$this->getExpectedAbsolutePathForTarget(self::$tarFile), $mockedProcessBuilder],
                ['-T', $mockedProcessBuilder],
                [$nullFile, $mockedProcessBuilder],
           ]);


        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($this->getSuccessFullMockProcess());

        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $this->adapter->create(self::$tarFile, array());
    }

    public function testCreate()
    {
        $mockedProcessBuilder = $this->createMock('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder');

        $mockedProcessBuilder
            ->expects($this->exactly(4))
            ->method('add')
            ->willReturnMap([
                ['-c', $mockedProcessBuilder],
                [$this->getOptions(), $mockedProcessBuilder],
                [sprintf('--file=%s', $this->getExpectedAbsolutePathForTarget(self::$tarFile)), $mockedProcessBuilder],
                ['lalalalala', $mockedProcessBuilder],
            ]);

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('setWorkingDirectory')->willReturnSelf();

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($this->getSuccessFullMockProcess());

        $classname = static::getAdapterClassName();
        $outputParser = ParserFactory::create($classname::getName());
        $manager = $this->getResourceManagerMock(__DIR__, array('lalalalala'));

        $this->adapter = new $classname($outputParser, $manager, $this->getMockedProcessBuilderFactory($mockedProcessBuilder), $this->getMockedProcessBuilderFactory($mockedProcessBuilder, 0));
        $this->setProbeIsOk($this->adapter);

        $this->adapter->create(self::$tarFile, array(__FILE__));

        return self::$tarFile;
    }

    #[\PHPUnit\Framework\Attributes\Depends('testCreate')]
    public function testOpen($tarFile)
    {
        $archive = $this->adapter->open($tarFile);
        $this->assertInstanceOf('Alchemy\Zippy\Archive\ArchiveInterface', $archive);

        return $archive;
    }

    public function testListMembers()
    {
        $resource = $this->getResource(self::$tarFile);

        $mockedProcessBuilder = $this->createMock('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder');

        $mockedProcessBuilder
            ->expects($this->exactly(4))
            ->method('add')
            ->willReturnMap([
                ['--list', $mockedProcessBuilder],
                ['-v', $mockedProcessBuilder],
                [sprintf('--file=%s', $resource->getResource()), $mockedProcessBuilder],
                [$this->getOptions(), $mockedProcessBuilder],
            ]);

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($this->getSuccessFullMockProcess());

        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $this->adapter->listMembers($resource);
    }

    public function testAddFile()
    {
        $resource = $this->getResource(self::$tarFile);
        $this->expectException('Alchemy\Zippy\Exception\NotSupportedException', 'Updating a compressed tar archive is not supported.');
        $this->adapter->add($resource, array(__DIR__ . '/../TestCase.php'));
    }

    public function testgetVersion()
    {
        $mockedProcessBuilder = $this->createMock('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder');

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('add')
            ->with('--version')->willReturnSelf();

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($this->getSuccessFullMockProcess());

        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $this->adapter->getInflatorVersion();
    }

    public function testExtract()
    {
        $resource = $this->getResource(self::$tarFile);

        $mockedProcessBuilder = $this->createMock('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder');

        $mockedProcessBuilder
            ->expects($this->exactly(3))
            ->method('add')
            ->willReturnMap([
                ['--extract', $mockedProcessBuilder],
                [sprintf('--file=%s', $resource->getResource()), $mockedProcessBuilder],
                [$this->getOptions(), $mockedProcessBuilder],
            ]);

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($this->getSuccessFullMockProcess());

        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $dir = $this->adapter->extract($resource);
        $pathinfo = pathinfo(self::$tarFile);
        $this->assertEquals($pathinfo['dirname'], $dir->getPath());
    }

    public function testExtractWithExtractDirPrecised()
    {
        $resource = $this->getResource(self::$tarFile);

        $mockedProcessBuilder = $this->createMock('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder');

        $mockedProcessBuilder
            ->expects($this->exactly(7))
            ->method('add')
            ->willReturnMap([
                ['-k', $mockedProcessBuilder],
                ['--extract', $mockedProcessBuilder],
                ['--file=' . $resource->getResource(), $mockedProcessBuilder],
                [$this->getOptions(), $mockedProcessBuilder],
                ['--directory', $mockedProcessBuilder],
                [__DIR__, $mockedProcessBuilder],
                [__FILE__, $mockedProcessBuilder],
            ]);

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($this->getSuccessFullMockProcess());

        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $this->adapter->extractMembers($resource, array(__FILE__), __DIR__);
    }

    public function testRemoveMembers()
    {
        $resource = $this->getResource(self::$tarFile);

        $mockedProcessBuilder = $this->createMock('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder');

        $mockedProcessBuilder
            ->expects($this->exactly(5))
            ->method('add')
            ->willReturnMap([
                ['--delete', $mockedProcessBuilder],
                ['--file=' . $resource->getResource(), $mockedProcessBuilder],
                [$this->getOptions(), $mockedProcessBuilder],
                [__DIR__ . '/../TestCase.php', $mockedProcessBuilder],
                ['path-to-file', $mockedProcessBuilder],
            ]);

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($this->getSuccessFullMockProcess());

        $archiveFileMock = $this->createStub('\Alchemy\Zippy\Archive\MemberInterface');
        $archiveFileMock
            ->method('getLocation')
            ->willReturn('path-to-file');

        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $this->adapter->remove($resource, array(
            __DIR__ . '/../TestCase.php',
            $archiveFileMock
        ));
    }

    public function testGetName()
    {
        $classname = static::getAdapterClassName();
        $this->assertEquals('bsd-tar', $classname::getName());
    }

    public function testGetDefaultInflatorBinaryName()
    {
        $classname = static::getAdapterClassName();
        $this->assertEquals(array('bsdtar', 'tar'), $classname::getDefaultInflatorBinaryName());
    }

    public function testGetDefaultDeflatorBinaryName()
    {
        $classname = static::getAdapterClassName();
        $this->assertEquals(array('bsdtar', 'tar'), $classname::getDefaultDeflatorBinaryName());
    }

    abstract protected function getOptions();

    protected static function getAdapterClassName()
    {
        self::fail(sprintf('Method %s should be implemented', __METHOD__));
    }
}
