<?php

declare(strict_types=1);

namespace Alchemy\Zippy\Tests\Adapter;

use Alchemy\Zippy\Adapter\ZipExtensionAdapter;
use Alchemy\Zippy\Adapter\Resource\ZipArchiveResource;
use Alchemy\Zippy\Exception\RuntimeException;

final class ZipExtensionAdapterTest extends AdapterTestCase
{
    /**
     * @var ZipExtensionAdapter
     */
    private $adapter;

    public function setUp(): void
    {
        $this->adapter = $this->provideSupportedAdapter();
    }

    public function testNewInstance()
    {
        $adapter = ZipExtensionAdapter::newInstance();

        $this->assertInstanceOf('Alchemy\Zippy\Adapter\ZipExtensionAdapter', $adapter);
    }

    protected function provideSupportedAdapter()
    {
        $adapter = new ZipExtensionAdapter($this->getResourceManagerMock());
        $this->setProbeIsOk($adapter);

        return $adapter;
    }

    protected function provideNotSupportedAdapter()
    {
        $adapter = new ZipExtensionAdapter($this->getResourceManagerMock());
        $this->setProbeIsNotOk($adapter);

        return $adapter;
    }

    public function testCreateNoFiles()
    {
        $this->expectException(\Alchemy\Zippy\Exception\NotSupportedException::class);
        $this->adapter->create(__DIR__ . '/zip-file.zip', array());
    }

    public function testCreate()
    {
        $file = __DIR__ . '/zip-file.zip';
        $manager = $this->getResourceManagerMock(__DIR__, array(__FILE__));
        $this->adapter = new ZipExtensionAdapter($manager);
        $this->setProbeIsOk($this->adapter);
        $archive = $this->adapter->create($file, array(__FILE__));
        $this->assertInstanceOf('Alchemy\Zippy\Archive\Archive', $archive);
        $this->assertFileExists($file);
        unlink($file);
    }

    public function testOpenWithWrongFileName()
    {
        $file = __DIR__ . '/zip-file-non-existing.zip';

        $this->expectException(RuntimeException::class);

        $this->adapter->open($file);
    }

    public function testOpen()
    {
        $file = __DIR__ . '/zip-file.zip';
        touch($file);
        $archive = $this->adapter->open($file);
        $this->assertInstanceOf('Alchemy\Zippy\Archive\Archive', $archive);
        unlink($file);
    }

    public function testGetName()
    {
        $this->assertIsString($this->adapter->getName());
    }

    public function testListMembers()
    {
        $resource = $this->createStub('\ZipArchive');

        $members = $this->adapter->listMembers(new ZipArchiveResource($resource));

        $this->assertIsArray($members);
    }

    public function testExtract()
    {
        $resource = $this->createMock('\ZipArchive');

        $resource->expects($this->once())
            ->method('extractTo')
            ->with(__DIR__, $this->anything())
            ->willReturn(true);

        $this->adapter->extract(new ZipArchiveResource($resource), __DIR__);
    }

    public function testExtractOnError()
    {
        $this->expectException(\Alchemy\Zippy\Exception\InvalidArgumentException::class);
        $resource = $this->createMock('\ZipArchive');

        $resource->expects($this->once())
            ->method('extractTo')
            ->with(__DIR__, $this->anything())
            ->willReturn(false);

        $this->adapter->extract(new ZipArchiveResource($resource), __DIR__);
    }

    public function testExtractWithInvalidTarget()
    {
        $this->expectException(\Alchemy\Zippy\Exception\InvalidArgumentException::class);

        $resource = $this->createStub('\ZipArchive');

        $this->adapter->extract(new ZipArchiveResource($resource), __DIR__ . '/boursin');
    }

    public function testExtractWithInvalidTarget2()
    {
        $this->expectException(\Alchemy\Zippy\Exception\InvalidArgumentException::class);

        $resource = $this->createStub('\ZipArchive');

        $this->adapter->extract(new ZipArchiveResource($resource));
    }

    public function testRemove()
    {
        $resource = $this->createMock('\ZipArchive');

        $files = array(
            'one-file.jpg',
            'second-file.jpg',
        );

        $resource->expects($this->exactly(2))
            ->method('locateName')
            ->willReturn(0);

        $resource->expects($this->exactly(2))
            ->method('deleteName')
            ->willReturn(true);

        $this->adapter->remove(new ZipArchiveResource($resource), $files);
    }

    public function testRemoveWithLocateFailing()
    {
        $this->expectException(\Alchemy\Zippy\Exception\InvalidArgumentException::class);

        $resource = $this->createMock('\ZipArchive');

        $files = array(
            'one-file.jpg'
        );

        $resource->expects($this->once())
            ->method('locateName')
            ->with('one-file.jpg')
            ->willReturn(false);

        $this->adapter->remove(new ZipArchiveResource($resource), $files);
    }

    public function testRemoveWithDeleteFailing()
    {
        $this->expectException(RuntimeException::class);

        $resource = $this->createMock('\ZipArchive');

        $files = array(
            'one-file.jpg'
        );

        $resource->expects($this->once())
            ->method('locateName')
            ->with('one-file.jpg')
            ->willReturn(0);

        $resource->expects($this->once())
            ->method('deleteName')
            ->with('one-file.jpg')
            ->willReturn(false);

        $this->adapter->remove(new ZipArchiveResource($resource), $files);
    }

    public function testAdd()
    {
        $resource = $this->createMock('\ZipArchive');

        $resource->expects($this->once())
            ->method('addFile')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('addEmptyDir')
            ->willReturn(true);

        $dir = __DIR__ . '/temp-dir';
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $files = array(
            __FILE__,
            $dir,
        );

        $manager = $this->getResourceManagerMock(__DIR__, $files);
        $this->adapter = new ZipExtensionAdapter($manager);
        $this->setProbeIsOk($this->adapter);
        $this->adapter->add(new ZipArchiveResource($resource), $files);

        rmdir($dir);
    }

    public function testAddFailOnFile()
    {
        $this->expectException(RuntimeException::class);

        $resource = $this->createMock('\ZipArchive');

        $resource->expects($this->once())
            ->method('addFile')
            ->willReturn(false);

        $dir = __DIR__ . '/temp-dir';
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $files = array(
            __FILE__,
            $dir,
        );

        $manager = $this->getResourceManagerMock(__DIR__, $files);
        $this->adapter = new ZipExtensionAdapter($manager);
        $this->setProbeIsOk($this->adapter);
        $this->adapter->add(new ZipArchiveResource($resource), $files);
    }

    public function testAddFailOnDir()
    {
        $this->expectException(RuntimeException::class);

        $resource = $this->createMock('\ZipArchive');

        $resource->expects($this->once())
            ->method('addFile')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('addEmptyDir')
            ->willReturn(false);

        $dir = __DIR__ . '/temp-dir';
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $files = array(
            __FILE__,
            $dir,
        );

        $manager = $this->getResourceManagerMock(__DIR__, $files);
        $this->adapter = new ZipExtensionAdapter($manager);
        $this->setProbeIsOk($this->adapter);
        $this->adapter->add(new ZipArchiveResource($resource), $files);
    }
}
