<?php

declare(strict_types=1);

namespace Alchemy\Zippy\Tests\Adapter\BSDTar;

use Alchemy\Zippy\Adapter\BSDTar\TarBSDTarAdapter;
use Alchemy\Zippy\Tests\Adapter\AdapterTestCase;
use Alchemy\Zippy\Parser\ParserFactory;

final class TarBSDTarAdapterTest extends AdapterTestCase
{
    protected static $tarFile;

    /**
     * @var TarBSDTarAdapter
     */
    protected $adapter;

    public static function setUpBeforeClass(): void
    {
        self::$tarFile = sprintf('%s/%s.tar', self::getResourcesPath(), TarBSDTarAdapter::getName());

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
        $inflator = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ProcessBuilderFactory')
                ->disableOriginalConstructor()
                ->onlyMethods(array('useBinary'))
            ->getMock();
        $inflator->expects($this->never())->method('useBinary');

        $outputParser = ParserFactory::create(TarBSDTarAdapter::getName());

        $manager = $this->getResourceManagerMock(__DIR__);

        return new TarBSDTarAdapter($outputParser, $manager, $inflator, $inflator);
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

    public function testCreateNoFiles()
    {
        $mockedProcessBuilder = $this->createMock('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder');

        $nullFile = defined('PHP_WINDOWS_VERSION_BUILD') ? 'NUL' : '/dev/null';

        $mockedProcessBuilder
            ->expects($this->exactly(5))
            ->method('add')
            ->willReturnMap([
              ['-c', $mockedProcessBuilder],
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
        $outputParser = ParserFactory::create(TarBSDTarAdapter::getName());
        $manager = $this->getResourceManagerMock(__DIR__, array('lalalalala'));
        $mockedProcessBuilder = $this->createMock('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder');

        $mockedProcessBuilder
            ->expects($this->exactly(3))
            ->method('add')
            ->willReturnMap([
                ['-c', $mockedProcessBuilder],
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

        $this->adapter = new TarBSDTarAdapter($outputParser, $manager, $this->getMockedProcessBuilderFactory($mockedProcessBuilder), $this->getMockedProcessBuilderFactory($mockedProcessBuilder, 0));
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
            ->expects($this->exactly(3))
            ->method('add')
            ->willReturnMap([
                ['--list', $mockedProcessBuilder],
                ['-v', $mockedProcessBuilder],
                [sprintf('--file=%s', $resource->getResource()), $mockedProcessBuilder],
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

        $mockedProcessBuilder = $this->createMock('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder');

        $mockedProcessBuilder
            ->expects($this->exactly(2))
            ->method('add')
            ->willReturnMap([
                ['--append', $mockedProcessBuilder],
                [sprintf('--file=%s', $resource->getResource()), $mockedProcessBuilder],
            ]);

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($this->getSuccessFullMockProcess());

        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

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
            ->expects($this->exactly(2))
            ->method('add')
            ->willReturnMap([
                ['--extract', $mockedProcessBuilder],
                [sprintf('--file=%s', $resource->getResource()), $mockedProcessBuilder],
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
            ->expects($this->exactly(6))
            ->method('add')
            ->willReturnMap([
                ['-k', $mockedProcessBuilder],
                ['--extract', $mockedProcessBuilder],
                ['--file=' . $resource->getResource(), $mockedProcessBuilder],
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
            ->expects($this->exactly(4))
            ->method('add')
            ->willReturnMap([
                ['--delete', $mockedProcessBuilder],
                ['--file=' . $resource->getResource(), $mockedProcessBuilder],
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
        $this->assertEquals('bsd-tar', TarBSDTarAdapter::getName());
    }

    public function testGetDefaultInflatorBinaryName()
    {
        $this->assertEquals(array('bsdtar', 'tar'), TarBSDTarAdapter::getDefaultInflatorBinaryName());
    }

    public function testGetDefaultDeflatorBinaryName()
    {
        $this->assertEquals(array('bsdtar', 'tar'), TarBSDTarAdapter::getDefaultDeflatorBinaryName());
    }
}
