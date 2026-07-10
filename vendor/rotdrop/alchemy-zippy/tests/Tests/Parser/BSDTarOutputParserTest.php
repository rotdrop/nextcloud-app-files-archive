<?php

declare(strict_types=1);

namespace Alchemy\Zippy\Tests\Parser;

use Alchemy\Zippy\Parser\BSDTarOutputParser;
use Alchemy\Zippy\Tests\TestCase;

final class BSDTarOutputParserTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\DoesNotPerformAssertions]
    public function testNewParser(): \Alchemy\Zippy\Parser\BSDTarOutputParser
    {
        return new BSDTarOutputParser();
    }

    #[\PHPUnit\Framework\Attributes\Depends('testNewParser')]
    public function testParseFileListing($parser)
    {
        $current_timezone = ini_get('date.timezone');
        ini_set('date.timezone', 'UTC');

        $output = "drw-rw-r--  0 toto titi     0 Jan  3  1980 practice/
-rw-rw-r--  0 toto titi     0 Jan  3  1980 practice/newfile
-rw-rw-r--  0 toto titi     0 Aug 23  1999 practice/hello
-rw-rw-r--  0 toto titi     10240 Jan 22 13:31 practice/records";

        $members = $parser->parseFileListing($output);

        $this->assertCount(4, $members);

        foreach ($members as $member) {
            $this->assertTrue(is_array($member));
        }

        $memberDirectory = array_shift($members);

        $this->assertTrue($memberDirectory['is_dir']);
        $this->assertEquals('practice/', $memberDirectory['location']);
        $this->assertEquals(0, $memberDirectory['size']);
        $date = $memberDirectory['mtime'];
        $this->assertInstanceOf(\DateTime::class, $date);
        $this->assertSame('1980-01-03', $date->format("Y-m-d"));

        $memberFile = array_pop($members);

        $this->assertFalse($memberFile['is_dir']);
        $this->assertEquals('practice/records', $memberFile['location']);
        $this->assertEquals(10240, $memberFile['size']);
        $date = $memberFile['mtime'];

        $currentDate = new \DateTime();
        $expected = \DateTime::createFromFormat('d-m-Y H:i:s', '22-01-'.$currentDate->format('Y').' 13:31:00');
        $this->assertInstanceOf(\DateTime::class, $date);
        $this->assertEquals($expected->format('U'), $date->format("U"));

        ini_set('date.timezone', $current_timezone);
    }

    #[\PHPUnit\Framework\Attributes\Depends('testNewParser')]
    public function testParseVersion($parser)
    {
        $this->assertEquals('2.8.3', $parser->parseInflatorVersion("bsdtar 2.8.3 - libarchive 2.8.3"));
    }
}
