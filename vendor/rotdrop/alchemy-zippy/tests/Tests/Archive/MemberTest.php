<?php

declare(strict_types=1);

namespace Alchemy\Zippy\Tests\Archive;

use Alchemy\Zippy\Tests\TestCase;
use Alchemy\Zippy\Archive\Member;
use Alchemy\Zippy\Archive\MemberInterface;

final class MemberTest extends TestCase
{
    public function testNewInstance(): \Alchemy\Zippy\Archive\Member
    {
        $member = new Member(
            $this->getResource('archive/located/here'),
            $this->createStub('\Alchemy\Zippy\Adapter\AdapterInterface'),
            'location',
            1233456,
            new \DateTime("2012-07-08 11:14:15"),
            true
        );

        $this->assertInstanceOf(\Alchemy\Zippy\Archive\MemberInterface::class, $member);

        return $member;
    }

    #[\PHPUnit\Framework\Attributes\Depends('testNewInstance')]
    public function testGetLocation($member)
    {
        $this->assertEquals('location', $member->getLocation());
    }

    #[\PHPUnit\Framework\Attributes\Depends('testNewInstance')]
    public function testIsDir($member)
    {
        $this->assertTrue($member->isDir());
    }

    #[\PHPUnit\Framework\Attributes\Depends('testNewInstance')]
    public function testGetLastModifiedDate($member)
    {
        $this->assertEquals(new \DateTime("2012-07-08 11:14:15"), $member->getLastModifiedDate());
    }

    #[\PHPUnit\Framework\Attributes\Depends('testNewInstance')]
    public function testGetSize($member)
    {
        $this->assertEquals(1233456, $member->getSize());
    }

    #[\PHPUnit\Framework\Attributes\Depends('testNewInstance')]
    public function testToString($member)
    {
        $this->assertSame('location', (string) $member);
    }

    public function testExtract()
    {
        $mockAdapter =  $this->createStub('\Alchemy\Zippy\Adapter\AdapterInterface');

        $mockAdapter
            ->method('extractMembers');

        $member = new Member(
           $this->getResource('archive/located/here'),
           $mockAdapter,
           '/member/located/here',
           1233456,
           new \DateTime("2012-07-08 11:14:15"),
           true
        );

        $file = $member->extract();
        $this->assertSame(sprintf('%s%s', getcwd(), '/member/located/here'), $file->getPathname());

        $file = $member->extract('/custom/location');
        $this->assertSame('/custom/location/member/located/here', $file->getPathname());
    }

    public function testRelativeExtract()
    {
        $mockAdapter =  $this->createStub('\Alchemy\Zippy\Adapter\AdapterInterface');

        $mockAdapter
            ->method('extractMembers');

        $member = new Member(
           $this->getResource('archive/located/here'),
           $mockAdapter,
           'relative',
           1233456,
           new \DateTime("2012-07-08 11:14:15"),
           true
        );

        $file = $member->extract();
        $this->assertSame(sprintf('%s%s', getcwd(), '/relative'), $file->getPathname());

        $file = $member->extract('/custom/location');
        $this->assertSame('/custom/location/relative', $file->getPathname());
    }
}
