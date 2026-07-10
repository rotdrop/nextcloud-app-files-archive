<?php

declare(strict_types=1);

namespace Alchemy\Zippy\Tests\Adapter;

use Alchemy\Zippy\Adapter\ZipAdapter;
use Alchemy\Zippy\Parser\ParserFactory;

final class ZipAdapterTest extends AdapterTestCase
{
    protected static $zipFile;

    /**
     * @var ZipAdapter
     */
    protected $adapter;

    public static function setUpBeforeClass(): void
    {
        self::$zipFile = sprintf('%s/%s.zip', self::getResourcesPath(), ZipAdapter::getName());

        if (file_exists(self::$zipFile)) {
            unlink(self::$zipFile);
        }
    }

    public static function tearDownAfterClass(): void
    {
        if (file_exists(self::$zipFile)) {
            unlink(self::$zipFile);
        }
    }

    public function setUp(): void
    {
        $this->adapter = $this->provideSupportedAdapter();
    }

    protected function provideNotSupportedAdapter()
    {
        $inflator = $deflator = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ProcessBuilderFactory')
                                    ->disableOriginalConstructor()
                                    ->onlyMethods(array('useBinary'))
                                    ->getMock();
        $inflator->expects($this->never())->method('useBinary');

        $outputParser = ParserFactory::create(ZipAdapter::getName());

        $adapter = new ZipAdapter($outputParser, $this->getResourceManagerMock(), $inflator, $deflator);
        $this->setProbeIsNotOk($adapter);

        return $adapter;
    }

    protected function provideSupportedAdapter()
    {
        $inflator = $deflator = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ProcessBuilderFactory')
                                    ->disableOriginalConstructor()
                                    ->onlyMethods(array('useBinary'))
                                    ->getMock();
        $inflator->expects($this->never())->method('useBinary');

        $outputParser = ParserFactory::create(ZipAdapter::getName());

        $adapter = new ZipAdapter($outputParser, $this->getResourceManagerMock(), $inflator, $deflator);
        $this->setProbeIsOk($adapter);

        return $adapter;
    }

    public function testCreateNoFiles()
    {
        $this->expectException(\Alchemy\Zippy\Exception\NotSupportedException::class);
        $mockedProcessBuilder = $this->createStub('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder');

        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $this->adapter->create(self::$zipFile, array());
    }

    public function testCreate()
    {
        $mockedProcessBuilder = $this->createMock('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder');

        $mockedProcessBuilder
            ->expects($this->exactly(3))
            ->method('add')
            ->willReturnMap([
                ['-r', $mockedProcessBuilder],
                [$this->getExpectedAbsolutePathForTarget(self::$zipFile), $mockedProcessBuilder],
                ['lalala', $mockedProcessBuilder],
            ]);

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('setWorkingDirectory')->willReturnSelf();

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($this->getSuccessFullMockProcess());

        $manager = $this->getResourceManagerMock(__DIR__, array('lalala'));
        $outputParser = ParserFactory::create(ZipAdapter::getName());
        $deflator = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ProcessBuilderFactory')
                                    ->disableOriginalConstructor()
                                    ->onlyMethods(array('useBinary'))
                                    ->getMock();
        $deflator->expects($this->never())->method('useBinary');

        $this->adapter = new ZipAdapter($outputParser, $manager, $this->getMockedProcessBuilderFactory($mockedProcessBuilder), $deflator);
        $this->setProbeIsOk($this->adapter);

        $this->adapter->create(self::$zipFile, array(__FILE__));

        return self::$zipFile;
    }

    #[\PHPUnit\Framework\Attributes\Depends('testCreate')]
    public function testOpen($zipFile)
    {
        $archive = $this->adapter->open($this->getResource($zipFile));
        $this->assertInstanceOf('Alchemy\Zippy\Archive\ArchiveInterface', $archive);
    }

    public function testListMembers()
    {
        $resource = $this->getResource(self::$zipFile);
        $archive = $this->adapter->open($resource);

        $mockedProcessBuilder = $this->createMock('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder');

        $mockedProcessBuilder
            ->expects($this->exactly(2))
            ->method('add')
            ->willReturnMap([
                ['-l', $mockedProcessBuilder],
                [$resource->getResource(), $mockedProcessBuilder],
            ]);

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($this->getSuccessFullMockProcess());

        $this->adapter->setDeflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $this->adapter->listMembers($resource);
    }

    public function testAddFile()
    {
        $resource = $this->getResource(self::$zipFile);

        $mockedProcessBuilder = $this->createMock('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder');

        $mockedProcessBuilder
            ->expects($this->exactly(3))
            ->method('add')
            ->willReturnMap([
                ['-r', $mockedProcessBuilder],
                ['-u', $mockedProcessBuilder],
                [$resource->getResource(), $mockedProcessBuilder],
            ]);

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($this->getSuccessFullMockProcess());

        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $this->adapter->add($resource, array(__DIR__ . '/../TestCase.php'));
    }

    public function testgetInflatorVersion()
    {
        $mockedProcessBuilder = $this->createMock('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder');

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('add')
            ->with('-h')->willReturnSelf();

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($this->getSuccessFullMockProcess());

        $this->adapter->setParser($this->createStub('\Alchemy\Zippy\Parser\ParserInterface'));
        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $this->adapter->getInflatorVersion();
    }

    public function testgetDeflatorVersion()
    {
        $mockedProcessBuilder = $this->createMock('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder');

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('add')
            ->with('-h')->willReturnSelf();

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($this->getSuccessFullMockProcess());

        $this->adapter->setParser($this->createStub('\Alchemy\Zippy\Parser\ParserInterface'));
        $this->adapter->setDeflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $this->adapter->getDeflatorVersion();
    }

    public function testRemoveMembers()
    {
        $resource = $this->getResource(self::$zipFile);

        $mockedProcessBuilder = $this->createMock('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder');

        $mockedProcessBuilder
            ->expects($this->exactly(4))
            ->method('add')
            ->willReturnMap([
                ['-d', $mockedProcessBuilder],
                [$resource->getResource(), $mockedProcessBuilder],
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

    public function testExtract()
    {
        $resource = $this->getResource(self::$zipFile);

        $mockedProcessBuilder = $this->createMock('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder');

        $mockedProcessBuilder
            ->expects($this->exactly(2))
            ->method('add')
            ->willReturnMap([
                ['-o', $mockedProcessBuilder],
                [$resource->getResource(), $mockedProcessBuilder],
            ]);

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($this->getSuccessFullMockProcess());

        $this->adapter->setDeflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $dir = $this->adapter->extract($resource);
        $pathinfo = pathinfo(self::$zipFile);
        $this->assertEquals($pathinfo['dirname'], $dir->getPath());
    }

    public function testExtractWithExtractDirPrecised()
    {
        $resource = $this->getResource(self::$zipFile);

        $mockedProcessBuilder = $this->createMock('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder');

        $mockedProcessBuilder
            ->expects($this->exactly(4))
            ->method('add')
            ->willReturnMap([
                [$resource->getResource(), $mockedProcessBuilder],
                ['-d', $mockedProcessBuilder],
                [__DIR__, $mockedProcessBuilder],
                [__FILE__, $mockedProcessBuilder],
            ]);

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($this->getSuccessFullMockProcess());

        $this->adapter->setDeflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $this->adapter->extractMembers($resource, array(__FILE__), __DIR__);
    }

    public function testGetName()
    {
        $this->assertEquals('zip', ZipAdapter::getName());
    }

    public function testGetDefaultInflatorBinaryName()
    {
        $this->assertEquals(array('zip'), ZipAdapter::getDefaultInflatorBinaryName());
    }

    public function testGetDefaultDeflatorBinaryName()
    {
        $this->assertEquals(array('unzip'), ZipAdapter::getDefaultDeflatorBinaryName());
    }
}
