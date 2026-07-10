<?php

declare(strict_types=1);

namespace Alchemy\Zippy\Tests\Adapter\Resource;

use Alchemy\Zippy\Tests\TestCase;
use Alchemy\Zippy\Adapter\Resource\FileResource;

final class FileResourceTest extends TestCase
{
    public function testGetResource()
    {
        $path = '/path/to/resource';
        $resource = new FileResource($path);

        $this->assertEquals($path, $resource->getResource());
    }
}
