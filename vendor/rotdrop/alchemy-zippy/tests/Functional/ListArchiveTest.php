<?php

declare(strict_types=1);

namespace Alchemy\Zippy\Functional;

final class ListArchiveTest extends FunctionalTestCase
{
    #[\PHPUnit\Framework\Attributes\DoesNotPerformAssertions]
    public function testOpen()
    {
        $adapter = $this->getAdapter();
        $archiveFile = $this->getArchiveFileForAdapter($adapter);

        $archive = $adapter->open($archiveFile);

        return $archive;
    }

    #[\PHPUnit\Framework\Attributes\Depends('testOpen')]
    public function testList($archive)
    {
        $target = __DIR__ . '/samples/tmp';

        $files2find = array(
            'directory/',
            'directory/README.md',
            'directory/photo.jpg'
        );

        foreach ($archive as $member) {
            $this->assertInstanceOf('Alchemy\Zippy\Archive\MemberInterface', $member);
            $this->assertContains($member->getLocation(), $files2find);
            unset($files2find[array_search($member->getLocation(), $files2find)]);
        }

        $this->assertSame(array(), $files2find);
    }

    #[\PHPUnit\Framework\Attributes\Depends('testOpen')]
    public function testCount($archive)
    {
        $this->assertCount(3, $archive);
    }
}
