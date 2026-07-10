<?php

declare(strict_types=1);

namespace Alchemy\Zippy\Tests\Resource;

use Alchemy\Zippy\Tests\TestCase;
use Alchemy\Zippy\Resource\ResourceCollection;

final class ResourceCollectionTest extends TestCase
{
    public function testConstructWithoutElements()
    {
        $collection = new ResourceCollection('supa-context', array(), false);
        $this->assertEquals('supa-context', $collection->getContext());
        $this->assertEquals(array(), $collection->toArray());
    }

    public function testConstructWithElements()
    {
        $data = array($this->createResourceMock(), 'two' => $this->createResourceMock());
        $collection = new ResourceCollection('supa-context', $data, false);
        $this->assertEquals('supa-context', $collection->getContext());
        $this->assertEquals($data, $collection->toArray());
    }

    private function createResourceMock()
    {
        return $this->createStub('\Alchemy\Zippy\Resource\Resource');
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('provideVariousInPlaceResources')]
    public function testCanBeProcessedInPlace($expected, $first, $second, $third)
    {
        $collection = new ResourceCollection('supa-context', array(
            $this->getInPlaceResource($first),
            $this->getInPlaceResource($second),
            $this->getInPlaceResource($third),
        ), false);

        $this->assertIsBool($collection->canBeProcessedInPlace());
        $this->assertEquals($expected, $collection->canBeProcessedInPlace());
    }

    public static function provideVariousInPlaceResources(): \Iterator
    {
        yield array(true, true, true, true);
        yield array(false, true, true, false);
        yield array(false, false, false, false);
        yield array(false, false, false, true);
    }

    private function getInPlaceResource($processInPlace)
    {
        $resource = $this->createStub('\Alchemy\Zippy\Resource\Resource');

        $resource
            ->method('canBeProcessedInPlace')
            ->willReturn($processInPlace);

        return $resource;
    }
}
