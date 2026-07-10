<?php

declare(strict_types=1);

namespace Alchemy\Zippy\Tests\Resource;

use Alchemy\Zippy\Tests\TestCase;
use Alchemy\Zippy\Resource\PathUtil;

final class PathUtilTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providePathData')]
    public function testBasename($expected, $context)
    {
        $this->assertEquals($expected, PathUtil::basename($context));
    }

    public static function providePathData(): \Iterator
    {
        yield array('file.ext', 'input/path/to/local/file.ext');
        yield array('file.ext', 'input\path\to\local\file.ext');
        yield array('file.ext', '\file.ext');
        yield array('file.ext', 'file.ext');
        yield array('Ängelholm.jpg', '/tmp/Ängelholm.jpg');
        yield array('Ängelholm.jpg', '\tmp\Ängelholm.jpg');
        yield array('Ängelholm.jpg', '\Ängelholm.jpg');
        yield array('Ängelholm.jpg', 'Ängelholm.jpg');
        yield array('я-utf8-name.jpg', '/tmp/я-utf8-name.jpg');
        yield array('я-utf8-name.jpg', '\tmp\я-utf8-name.jpg');
        yield array('я-utf8-name.jpg', 'я-utf8-name.jpg');
        yield array('я-utf8-name.jpg', '/я-utf8-name.jpg');
        yield array('logo.png', 'http://google.com/tmp/logo.png');
        yield array('Ängelholm.png', 'http://google.com/city/Ängelholm.png');
        yield array('Ängelholm.png', 'http://google.com/я/Ängelholm.png');
    }
}
