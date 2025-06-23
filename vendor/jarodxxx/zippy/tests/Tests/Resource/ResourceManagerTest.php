<?php

namespace Alchemy\Zippy\Tests\Resource;

use Alchemy\Zippy\Resource\ResourceCollection;
use Alchemy\Zippy\Tests\TestCase;
use Alchemy\Zippy\Resource\ResourceManager;
use Symfony\Component\Filesystem\Filesystem;

class ResourceManagerTest extends TestCase
{
    /**
     * @covers Alchemy\Zippy\Resource\ResourceManager::handle
     */
    public function testHandle()
    {
        $mapper = $this->getRequestMapperMock();

        $manager = new ResourceManager(
            $mapper,
            $this->getResourceTeleporterMock(),
            $this->getFilesystemMock()
        );

        $context = '/path/to/current/directory';
        $request = array($this->createProcessableInPlaceResource(), $this->createProcessableInPlaceResource());

        $expectedCollection = new ResourceCollection($context, $request, false);

        $mapper->expects($this->once())
               ->method('map')
               ->with($this->equalTo($context), $this->equalTo($request))
               ->will($this->returnValue($expectedCollection));

        $collection = $manager->handle($context, $request);
        $this->assertEquals($expectedCollection, $collection);
    }

    public function testHandleNotProcessables()
    {
        $mapper = $this->getRequestMapperMock();

        $manager = new ResourceManager(
            $mapper,
            $this->getResourceTeleporterMock(),
            $this->getFilesystemMock()
        );

        $context = '/path/to/current/directory';
        $request = array($this->createNotProcessableInPlaceResource(), $this->createNotProcessableInPlaceResource());

        $expectedCollection = new ResourceCollection($context, $request, false);

        $mapper->expects($this->once())
               ->method('map')
               ->with($this->equalTo($context), $this->equalTo($request))
               ->will($this->returnValue($expectedCollection));

        $collection = $manager->handle($context, $request);
        $this->assertNotEquals($expectedCollection, $collection);
        $this->assertTrue($collection->isTemporary());
    }

    private function createProcessableInPlaceResource()
    {
        $resource = $this->getMockBuilder('\Alchemy\Zippy\Resource\Resource')
            ->disableOriginalConstructor()
            ->getMock();
        $resource->expects($this->any())
            ->method('canBeProcessedInPlace')
            ->will($this->returnValue(true));

        return $resource;
    }

    private function createNotProcessableInPlaceResource()
    {
        $resource = $this->getMockBuilder('\Alchemy\Zippy\Resource\Resource')
            ->disableOriginalConstructor()
            ->getMock();
        $resource->expects($this->any())
            ->method('canBeProcessedInPlace')
            ->will($this->returnValue(false));

        return $resource;
    }

    /**
     * @covers Alchemy\Zippy\Resource\ResourceManager::cleanup
     */
    public function testCleanup()
    {
        $fs = $this->getFilesystemMock();

        $manager = new ResourceManager(
            $this->getRequestMapperMock(),
            $this->getResourceTeleporterMock(),
            $fs
        );

        $context = 'context' . mt_rand();

        $fs->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($context));

        $collection = $this->getMockBuilder('\Alchemy\Zippy\Resource\ResourceCollection')
            ->disableOriginalConstructor()
            ->getMock();

        $collection->expects($this->once())
            ->method('isTemporary')
            ->will($this->returnValue(true));

        $collection->expects($this->once())
            ->method('getContext')
            ->will($this->returnValue($context));

        $manager->cleanup($collection);
    }

    /**
     * @covers Alchemy\Zippy\Resource\ResourceManager::cleanup
     */
    public function testCleanupWhenCollectionIsNotTemporary()
    {
        $fs = $this->getFilesystemMock();

        $manager = new ResourceManager(
            $this->getRequestMapperMock(),
            $this->getResourceTeleporterMock(),
            $fs
        );

        $fs->expects($this->never())
            ->method('remove');

        $collection = $this->getMockBuilder('\Alchemy\Zippy\Resource\ResourceCollection')
            ->disableOriginalConstructor()
            ->getMock();

        $collection->expects($this->once())
            ->method('isTemporary')
            ->will($this->returnValue(false));

        $collection->expects($this->never())
            ->method('getContext');

        $manager->cleanup($collection);
    }

    /**
     * @covers Alchemy\Zippy\Resource\ResourceManager::handle
     */
    public function testFunctionnal()
    {
        $workDir = __DIR__;
        $tempDir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR);

        $filesystem = new Filesystem();
        $filesystem->mkdir($tempDir . '/path/to/local/');
        $filesystem->mkdir($tempDir . '/to/');
        $filesystem->mkdir($tempDir . '/path/to/a');

        $filesystem->touch($tempDir . '/path/to/local/file.ext');
        $filesystem->touch($tempDir . '/path/to/local/file2.ext');
        $filesystem->touch($tempDir . '/to/file3.ext');

        $request = array(
            $workDir . '/input/path/to/local/file.ext',
            $workDir . '/input/path/to/a/../local/file2.ext',
            $tempDir . '/path/to/local/file.ext',
            $tempDir . '/path/to/a/../local/file2.ext',
            'http://127.0.0.1:8080/plus-badge.png',
            'http://127.0.0.1:8080/plusone-button.png',
            'file://' . $tempDir . '/to/file3.ext',
            'file://' . $workDir . '/input/path/to/a/../local/file3.ext',
            '/I/want/this/file/to/go/there' => 'file://' . $workDir . '/input/path/to/local/file2.ext',
            '/I/want/this/file/to/go/here'  => 'file://' . $workDir . '/input/path/to/local/file3.ext'
        );

        $expected = array(
            'input/path/to/local/file.ext',
            'input/path/to/local/file2.ext',
            'file.ext',
            'file2.ext',
            'plus-badge.png',
            'plusone-button.png',
            'file3.ext',
            'input/path/to/local/file3.ext',
            'I/want/this/file/to/go/there',
            'I/want/this/file/to/go/here',
        );
        $expectedSource = array(
            $request[0],
            $request[1],
            $request[2],
            $request[3],
            $request[4],
            $request[5],
            $request[6],
            $request[7],
            $request['/I/want/this/file/to/go/there'],
            $request['/I/want/this/file/to/go/here'],
        );

        $resourceManger = ResourceManager::create();

        $collection = $resourceManger->handle($workDir, $request);

        $this->assertCount(10, $collection);

        $n = 0;
        foreach ($collection as $resource) {
            $this->assertEquals($expected[$n], $resource->getTarget());
            $this->assertEquals($expectedSource[$n], $resource->getOriginal());
            $n++;
        }
    }

    protected function getRequestMapperMock()
    {
        return $this->getMockBuilder('\Alchemy\Zippy\Resource\RequestMapper')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function getResourceTeleporterMock()
    {
        return $this->getMockBuilder('\Alchemy\Zippy\Resource\ResourceTeleporter')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function getFilesystemMock()
    {
        return $this->getMockBuilder('\Symfony\Component\Filesystem\Filesystem')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
