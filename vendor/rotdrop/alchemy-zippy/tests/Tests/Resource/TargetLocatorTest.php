<?php

declare(strict_types=1);

namespace Alchemy\Zippy\Tests\Resource;

use Alchemy\Zippy\Tests\TestCase;
use Alchemy\Zippy\Resource\TargetLocator;

final class TargetLocatorTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('provideLocationData')]
    public function testLocate($expected, $context, $resource)
    {
        $locator = new TargetLocator();
        $this->assertEquals($expected, $locator->locate($context, $resource));
    }

    public function testLocateThatShouldFail()
    {
        $this->expectException(\Alchemy\Zippy\Exception\TargetLocatorException::class);

        $locator = new TargetLocator();
        $locator->locate("some-context", array());
    }

    public function testLocateThatShouldFail2()
    {
        $this->expectException(\Alchemy\Zippy\Exception\TargetLocatorException::class);

        $locator = new TargetLocator();
        $locator->locate("some-context", fopen('file://', 'rb'));
    }

    public function testLocateThatShouldFail3()
    {
        $this->expectException(\Alchemy\Zippy\Exception\TargetLocatorException::class);

        $locator = new TargetLocator();
        $locator->locate(__DIR__, __DIR__ . '/input/path/to/a/../local/file-non-existent.ext');
    }

    public static function provideLocationData(): \Iterator
    {
        $updir = dirname(__DIR__) . '/';
        yield array(basename(__FILE__), __DIR__, __FILE__);
        yield array(basename(__FILE__), __DIR__, new \SplFileInfo(__FILE__));
        yield array('input/path/to/local/file.ext', __DIR__ , __DIR__ . '/input/path/to/a/../local/file.ext');
        yield array('file.ext', __DIR__ , fopen(__DIR__ . '/input/path/to/a/../local/file.ext', 'rb'));
        yield array(basename(__FILE__), __DIR__, 'file://' . __FILE__);
        yield array(basename(__FILE__), __DIR__, fopen(__FILE__, 'rb'));
        yield array('temporary-file.jpg', __DIR__, '/tmp/temporary-file.jpg');
        yield array('temporary-file.jpg', __DIR__, '/tmp/temporary-file.jpg');
        yield array(str_replace($updir, '', __FILE__), $updir, __FILE__);
        yield array(basename(__FILE__), $updir, fopen(__FILE__, 'rb'));
        yield array('plus-badge.png', $updir, 'http://127.0.0.1:8080/plus-badge.png');
        yield array('plus-badge.png', $updir, fopen('http://127.0.0.1:8080/plus-badge.png', 'rb'));
        yield array('hedgehog.png', $updir, 'ftp://192.168.1.1/images/hedgehog.png');
    }
}
