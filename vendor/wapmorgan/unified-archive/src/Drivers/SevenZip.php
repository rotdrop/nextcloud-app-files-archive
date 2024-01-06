<?php
namespace wapmorgan\UnifiedArchive\Drivers;

use Exception;
use wapmorgan\UnifiedArchive\Abilities;
use wapmorgan\UnifiedArchive\Archive7z;
use wapmorgan\UnifiedArchive\ArchiveEntry;
use wapmorgan\UnifiedArchive\ArchiveInformation;
use wapmorgan\UnifiedArchive\Drivers\Basic\BasicDriver;
use wapmorgan\UnifiedArchive\Drivers\Basic\BasicUtilityDriver;
use wapmorgan\UnifiedArchive\Exceptions\ArchiveCreationException;
use wapmorgan\UnifiedArchive\Exceptions\ArchiveExtractionException;
use wapmorgan\UnifiedArchive\Exceptions\ArchiveModificationException;
use wapmorgan\UnifiedArchive\Exceptions\NonExistentArchiveFileException;
use wapmorgan\UnifiedArchive\Exceptions\UnsupportedOperationException;
use wapmorgan\UnifiedArchive\Formats;

class SevenZip extends BasicUtilityDriver
{
    /** @var Archive7z */
    protected $sevenZip;

    /**
     * @var string
     */
    protected $format;

    const COMMENT_FILE = 'descript.ion';

    public static function isInstalled()
    {
        return class_exists('\Archive7z\Archive7z') && Archive7z::getBinaryVersion() !== false;
    }

    /**
     * @inheritDoc
     */
    public static function getInstallationInstruction()
    {
        if (!class_exists('\Archive7z\Archive7z'))
            return 'install library [gemorroj/archive7z]: `composer require gemorroj/archive7z`' . "\n"
                . ' and console program p7zip [7za]: `apt install p7zip-full` - depends on OS';

        if (Archive7z::getBinaryVersion() === false)
            return 'install console program p7zip [7za]: `apt install p7zip-full` - depends on OS';

        return null;
    }

    /**
     * @inheritDoc
     */
    public static function getDescription()
    {
        return 'php-library and console program'
            .(class_exists('\Archive7z\Archive7z') && ($version = Archive7z::getBinaryVersion()) !== false
                ? ' ('.$version.')'
                : null);
    }

    /**
     * @return array
     */
    public static function getFormats()
    {
        return [
            Formats::SEVEN_ZIP,
            Formats::ZIP,
//            Formats::RAR,
            Formats::TAR,
            // disabled
//            Formats::TAR_GZIP,
//            Formats::TAR_BZIP,
            Formats::CAB,
            Formats::ISO,
            Formats::ARJ,
//            Formats::LZMA,
            Formats::UEFI,
            Formats::GPT,
            Formats::MBR,
            Formats::MSI,
            Formats::DMG,
            Formats::RPM,
            Formats::DEB,
            Formats::UDF,
        ];
    }

    /**
     * @param string $format
     * @return array
     * @throws \Archive7z\Exception
     */
    public static function getFormatAbilities($format)
    {
        if (!static::isInstalled()) {
            return [];
        }

        // in 4.0.0 version it was supporting only 7z
        if (!Archive7z::supportsAllFormats() && $format !== Formats::SEVEN_ZIP) {
            return [];
        }

        $abilities = [
            Abilities::OPEN,
            Abilities::EXTRACT_CONTENT,
        ];

        if (static::canRenameFiles()) {
            if (in_array($format, [Formats::SEVEN_ZIP, Formats::RAR, Formats::ZIP], true)) {
                $abilities[] = Abilities::OPEN_ENCRYPTED;
            }

            if (in_array($format, [Formats::ZIP, Formats::SEVEN_ZIP], true)) {
                $abilities[] = Abilities::CREATE_ENCRYPTED;
            }

            if (in_array($format, [Formats::SEVEN_ZIP,
                Formats::BZIP,
                Formats::GZIP,
                Formats::TAR,
                Formats::LZMA,
                Formats::ZIP], true)) {
                $abilities[] = Abilities::CREATE;
                $abilities[] = Abilities::APPEND;
                $abilities[] = Abilities::DELETE;
            }

            if ($format === Formats::SEVEN_ZIP) {
                $abilities[] = Abilities::GET_COMMENT;
                $abilities[] = Abilities::SET_COMMENT;
            }

        }
        return $abilities;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function __construct($archiveFileName, $format, $password = null)
    {
        parent::__construct($archiveFileName, $format);
        try {
            $this->format = $format;
            $this->sevenZip = new Archive7z($archiveFileName, null, null);
            if ($password !== null)
                $this->sevenZip->setPassword($password);
        } catch (\Archive7z\Exception $e) {
            throw new Exception('Could not open 7Zip archive: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @return ArchiveInformation
     */
    public function getArchiveInformation()
    {
        $information = new ArchiveInformation();

        foreach ($this->sevenZip->getEntries() as $entry) {
            if ($entry->isDirectory()) {
                continue;
            }

            if (!isset($can_get_unix_path))
                $can_get_unix_path = method_exists($entry, 'getUnixPath');

            $information->files[] = $can_get_unix_path
                ? $entry->getUnixPath()
                : str_replace('\\', '/', $entry->getPath());
            $information->compressedFilesSize += (int)$entry->getPackedSize();
            $information->uncompressedFilesSize += (int)$entry->getSize();
        }
        return $information;
    }

    /**
     * @return array
     */
    public function getFileNames()
    {
        $files = [];
        foreach ($this->sevenZip->getEntries() as $entry) {
            if ($entry->isDirectory())
                continue;
            $files[] = $entry->getPath();
        }
        return $files;
    }

    /**
     * @param string $fileName
     *
     * @return bool
     */
    public function isFileExists($fileName)
    {
        return $this->sevenZip->getEntry($fileName) !== null;
    }

    /**
     * @param string $fileName
     *
     * @return ArchiveEntry|false
     */
    public function getFileData($fileName)
    {
        $entry = $this->sevenZip->getEntry($fileName);
        return new ArchiveEntry(
            $fileName,
            $entry->getPackedSize(),
            $entry->getSize(),
            strtotime($entry->getModified()),
            $entry->getSize() !== $entry->getPackedSize(),
            $entry->getComment(),
            $this->format === Formats::ZIP ? $entry->getCrc() : null
        );
    }

    /**
     * @param string $fileName
     *
     * @return string|false
     * @throws NonExistentArchiveFileException
     */
    public function getFileContent($fileName)
    {
        $entry = $this->sevenZip->getEntry($fileName);
        if ($entry === null) {
            throw new NonExistentArchiveFileException('File ' . $fileName . ' does not exist');
        }
        return $entry->getContent();
    }

    /**
     * @param string $fileName
     *
     * @return bool|resource|string
     */
    public function getFileStream($fileName)
    {
        $entry = $this->sevenZip->getEntry($fileName);
        return self::wrapStringInStream($entry->getContent());
    }

    /**
     * @param string $outputFolder
     * @param array $files
     * @return int
     * @throws ArchiveExtractionException
     */
    public function extractFiles($outputFolder, array $files)
    {
        $count = 0;
        try {
            $this->sevenZip->setOutputDirectory($outputFolder);

            foreach ($files as $file) {
                $this->sevenZip->extractEntry($file);
                $count++;
            }
            return $count;
        } catch (Exception $e) {
            throw new ArchiveExtractionException('Could not extract archive: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $outputFolder
     *
     * @return bool
     * @throws ArchiveExtractionException
     */
    public function extractArchive($outputFolder)
    {
        try {
            $this->sevenZip->setOutputDirectory($outputFolder);
            $this->sevenZip->extract();
            return true;
        } catch (Exception $e) {
            throw new ArchiveExtractionException('Could not extract archive: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param array $files
     * @return int Number of deleted files
     * @throws ArchiveModificationException
     */
    public function deleteFiles(array $files)
    {
        $count = 0;
        try {
            foreach ($files as $file) {
                $this->sevenZip->delEntry($file);
                $count++;
            }
            return $count;
        } catch (Exception $e) {
            throw new ArchiveModificationException('Could not modify archive: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param array $files
     *
     * @return int
     * @throws ArchiveModificationException
     */
    public function addFiles(array $files)
    {
        $added_files = 0;
        try {
            foreach ($files as $localName => $filename) {
                if (!is_null($filename)) {
                    $this->sevenZip->addEntry($filename);
                    $this->sevenZip->renameEntry($filename, $localName);
                    $added_files++;
                }
            }
            return $added_files;
        } catch (Exception $e) {
            throw new ArchiveModificationException('Could not modify archive: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $inArchiveName
     * @param string $content
     * @return bool|void
     * @throws ArchiveModificationException
     * @throws \Archive7z\Exception
     */
    public function addFileFromString($inArchiveName, $content)
    {
        $tmp_file = tempnam(sys_get_temp_dir(), 'ua');
        if (!$tmp_file)
            throw new ArchiveModificationException('Could not create temporarily file');

        file_put_contents($tmp_file, $content);
        $this->sevenZip->addEntry($tmp_file, true);
        $this->sevenZip->renameEntry($tmp_file, $inArchiveName);
        unlink($tmp_file);
        return true;
    }

    /**
     * @param array $files
     * @param string $archiveFileName
     * @param int $archiveFormat
     * @param int $compressionLevel
     * @param null $password
     * @param $fileProgressCallable
     * @return int
     * @throws ArchiveCreationException
     * @throws UnsupportedOperationException
     */
    public static function createArchive(
        array $files,
        $archiveFileName,
        $archiveFormat,
        $compressionLevel = self::COMPRESSION_AVERAGE,
        $password = null,
        $fileProgressCallable = null
    )
    {
        static $compressionLevelMap = [
            self::COMPRESSION_NONE => 0,
            self::COMPRESSION_WEAK => 2,
            self::COMPRESSION_AVERAGE => 4,
            self::COMPRESSION_STRONG => 7,
            self::COMPRESSION_MAXIMUM => 9,
        ];

        if ($password !== null && !static::canEncrypt($archiveFormat)) {
            throw new UnsupportedOperationException('SevenZip could not encrypt an archive of '.$archiveFormat.' format');
        }

        if ($fileProgressCallable !== null && !is_callable($fileProgressCallable)) {
            throw new ArchiveCreationException('File progress callable is not callable');
        }

        try {
            $current_file = 0;
            $total_files = count($files);

            $seven_zip = new Archive7z($archiveFileName);
            if ($password !== null)
                $seven_zip->setPassword($password);
            $seven_zip->setCompressionLevel($compressionLevelMap[$compressionLevel]);
            foreach ($files as $archiveName => $localName) {
                if ($localName !== null) {
                    $seven_zip->addEntry($localName, true);
                    $seven_zip->renameEntry($localName, $archiveName);
                }
                if ($fileProgressCallable !== null) {
                    call_user_func_array($fileProgressCallable, [$current_file++, $total_files, $localName, $archiveName]);
                }
            }
            unset($seven_zip);
        } catch (Exception $e) {
            throw new ArchiveCreationException('Could not create archive: '.$e->getMessage(), $e->getCode(), $e);
        }
        return count($files);
    }

    /**
     * @return bool
     * @throws \Archive7z\Exception
     */
    protected static function canRenameFiles()
    {
        $version = Archive7z::getBinaryVersion();
        return $version !== false && version_compare('9.30', $version, '<=');
    }

    /**
     * @param $format
     * @return bool
     * @throws \Archive7z\Exception
     */
    public static function canEncrypt($format)
    {
        return in_array($format, [Formats::ZIP, Formats::SEVEN_ZIP]) && self::canRenameFiles();
    }

    /**
     * @return string|null
     */
    public function getComment()
    {
        if ($this->format !== Formats::SEVEN_ZIP) {
            return null;
        }
        try {
            return $this->getFileContent(static::COMMENT_FILE);
        } catch (NonExistentArchiveFileException $e) {
            return null;
        }
    }

    /**
     * @param string|null $comment
     * @return null
     * @throws ArchiveModificationException
     * @throws \Archive7z\Exception
     */
    public function setComment($comment)
    {
        if ($this->format !== Formats::SEVEN_ZIP) {
            return null;
        }
        $this->addFileFromString(static::COMMENT_FILE, $comment);
    }
}
