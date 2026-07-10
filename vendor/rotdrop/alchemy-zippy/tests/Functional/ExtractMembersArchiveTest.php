<?php

declare(strict_types=1);

namespace Alchemy\Zippy\Functional;

use Alchemy\Zippy\Archive\ArchiveInterface;

final class ExtractMembersArchiveTest extends FunctionalTestCase
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
    public function testExtractMembersString(ArchiveInterface $archive)
    {
        $archive->extractMembers('directory/README.md', __DIR__ . '/samples/tmp');
        $archive->extractMembers('directory/photo.jpg', __DIR__ . '/samples/tmp');

        $this->assertFileExists(__DIR__ . '/samples/tmp/directory/README.md');
        $this->assertFileExists(__DIR__ . '/samples/tmp/directory/photo.jpg');
    }

    #[\PHPUnit\Framework\Attributes\Depends('testOpen')]
    public function testExtractFromMember(ArchiveInterface $archive)
    {
        foreach ($archive as $file) {
            if (!$file->isDir()) {
                $file->extract(__DIR__ . '/samples/tmp/');
            }
        }

        $this->assertFileExists(__DIR__ . '/samples/tmp/directory/README.md');
        $this->assertFileExists(__DIR__ . '/samples/tmp/directory/photo.jpg');
    }
}
