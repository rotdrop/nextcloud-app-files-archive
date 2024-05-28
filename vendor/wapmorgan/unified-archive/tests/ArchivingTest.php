<?php

use wapmorgan\UnifiedArchive\Exceptions\UnsupportedOperationException;
use wapmorgan\UnifiedArchive\Formats;
use wapmorgan\UnifiedArchive\Formats\OneFile\OneFile\OneFile\OneFile\SevenZip;
use wapmorgan\UnifiedArchive\UnifiedArchive;

class ArchivingTest extends PhpUnitTestCase
{
    /**
     * @dataProvider updatableArchiveTypes
     *
     * @param string $archiveFileName
     * @param string $archiveType
     *
     * @throws \Exception
     */
    public function testCreate($archiveFileName, $archiveType)
    {
        if (!Formats::canOpen($archiveType))
            $this->markTestSkipped($archiveType.' is not supported with current system configuration');

        if (!Formats::canCreate($archiveType))
            $this->markTestSkipped($archiveType.' does not support archiving');

        $this->cleanWorkDir();

        $test_archive_filename = WORK_DIR.'/'.$archiveFileName;

        $result = UnifiedArchive::create(['' => FIXTURES_DIR], $test_archive_filename);
        $this->assertValueIsInteger($result);
        $this->assertEquals(5, $result);

        unlink($test_archive_filename);
    }

    /**
     * @dataProvider updatableOneFileArchiveTypes
     *
     * @param string $archiveFileName
     * @param string $archiveType
     *
     * @throws \Exception
     */
    public function testCreateOneFile($archiveFileName, $archiveType)
    {
        if (!Formats::canOpen($archiveType))
            $this->markTestSkipped($archiveType.' is not supported with current system configuration');

        if (!Formats::canCreate($archiveType))
            $this->markTestSkipped($archiveType.' does not support archiving');

        $this->cleanWorkDir();

        $test_archive_filename = WORK_DIR.'/'.$archiveFileName;

        $result = UnifiedArchive::create(FIXTURES_DIR . '/doc', $test_archive_filename);
        $this->assertValueIsInteger($result);
        $this->assertEquals(1, $result);

        unlink($test_archive_filename);
    }

    /**
     * @dataProvider updatableArchiveTypes
     *
     * @param string $archiveFileName
     * @param string $archiveType
     *
     * @throws \Exception
     */
    public function testModify($archiveFileName, $archiveType)
    {
        if (!Formats::canOpen($archiveType))
            $this->markTestSkipped($archiveType.' is not supported with current system configuration');
        if (!Formats::canAppend($archiveType) || !Formats::canUpdate($archiveType))
            $this->markTestSkipped($archiveType.' is not supported with current system configuration');

        $this->cleanWorkDir();

        $test_archive_filename = WORK_DIR.'/'.$archiveFileName;
        copy(ARCHIVES_DIR.'/'.$archiveFileName, $test_archive_filename);
        $archive = UnifiedArchive::open($test_archive_filename);
        $this->assertInstanceOf('\wapmorgan\UnifiedArchive\UnifiedArchive', $archive);

        // adding file
        if (Formats::canAppend($archiveType)) {
            $this->assertTrue($archive->add([basename(__FILE__) => __FILE__]) === 1);
            $this->assertTrue($archive->hasFile(basename(__FILE__)));
            $this->assertEquals(file_get_contents(__FILE__), $archive->getFileContent(basename(__FILE__)));
        } else {
            $this->markTestSkipped($archiveType.' does not support adding files to archive');
        }

        // removing file
        if (Formats::canUpdate($archiveType)) {
            $this->assertEquals(1, $archive->delete(basename(__FILE__)));
            $this->assertFalse($archive->hasFile(basename(__FILE__)));
        } else {
            $this->markTestSkipped($archiveType.' does not support deleting files from archive');
        }
        $archive = null;

        unlink($test_archive_filename);
    }

    /**
     * @dataProvider updatableOneFileArchiveTypes
     *
     * @param string $archiveFileName
     * @param string $archiveType
     *
     * @throws \Exception
     */
    public function testModifyOneFile($archiveFileName, $archiveType)
    {
        if (!Formats::canOpen($archiveType))
            $this->markTestSkipped($archiveType.' is not supported with current system configuration');

        $full_filename = self::getArchivePath($archiveFileName);
        $archive = UnifiedArchive::open($full_filename);

        $this->expectException(UnsupportedOperationException::class);
        $archive->delete('onefile');
    }

    /**
     * @return array
     * @throws \Archive7z\Exception
     */
    public function updatableArchiveTypes()
    {
        return [
            ['fixtures.zip', Formats::ZIP],
            ['fixtures.tar', Formats::TAR],
            ['fixtures.7z', Formats::SEVEN_ZIP]
        ];
    }

    /**
     * @return array
     * @throws \Archive7z\Exception
     */
    public function updatableOneFileArchiveTypes()
    {
        return [
            ['onefile.gz', Formats::GZIP],
            ['onefile.bz2', Formats::BZIP],
//            ['onefile.xz', Formats::LZMA]
        ];
    }
}
