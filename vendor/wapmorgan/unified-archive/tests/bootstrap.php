<?php
use PHPUnit\Framework\TestCase;
use wapmorgan\UnifiedArchive\Formats;
use wapmorgan\UnifiedArchive\UnifiedArchive;

require_once __DIR__ . '/../vendor/autoload.php';

define('ARCHIVES_DIR', __DIR__ . '/archives');
define('FIXTURES_DIR', __DIR__ . '/fixtures');
define('WORK_DIR', __DIR__ . '/workdir');

class PhpUnitTestCase extends TestCase
{
    /**
     * @var array<string format, array<string md5_hash, string filename, string remote_file>>
     */
    static public $archives;

    /**
     * @var array<string format, array<string md5_hash, string filename, string remote_file>>
     */
    static public $oneFileArchives;

    /**
     * @var array List of directories/files and content stored in archive
     */
    static public $fixtureContents;

    /**
     * @return array
     */
    public function getFixtures()
    {
        return self::$archives;
    }

    /**
     * @return array
     */
    public function getOneFileFixtures()
    {
        return self::$oneFileArchives;
    }

    /**
     * @param $fixture
     *
     * @return string
     */
    static public function getArchivePath($fixture)
    {
        return ARCHIVES_DIR.'/'.$fixture;
    }

    /**
     * @return array
     */
    public function archiveTypes()
    {
        return [
            ['archive.tar', Formats::TAR],
            ['archive.tgz', Formats::TAR_GZIP],
            ['archive.tar.gz', Formats::TAR_GZIP],
            ['archive.tbz2', Formats::TAR_BZIP],
            ['archive.tar.bz2', Formats::TAR_BZIP],
            ['archive.txz', Formats::TAR_LZMA],
            ['archive.tar.xz', Formats::TAR_LZMA],
            ['archive.zip', Formats::ZIP],
            ['archive.rar', Formats::RAR],
            ['archive.iso', Formats::ISO],
            ['archive.7z', Formats::SEVEN_ZIP],
        ];
    }

    /**
     * @return array
     */
    public function oneFileArchiveTypes()
    {
        return [
            ['onefile.gz', Formats::GZIP],
            ['onefile.bz2', Formats::BZIP],
        ];
    }

    /**
     *
     */
    public function cleanWorkDir()
    {
        foreach (glob(WORK_DIR.'/*') as $file) {
            if (basename($file) !== '.gitignore')
                unlink($file);
        }
    }

    /**
     * @param       $prefix
     * @param array $list
     * @param array $output
     */
    protected function flattenFilesList($prefix, array $list, array &$output)
    {
        foreach ($list as $name => $value) {
            if (is_array($value))
                $this->flattenFilesList($prefix.$name.'/', $value, $output);
            else
                $output[$prefix.$name] = $value;
        }
    }

    protected function assertValueIsInteger($actual)
    {
        if (method_exists($this, 'assertIsInt'))
            return $this->assertIsInt($actual);
        return $this->assertInternalType('integer', $actual);
    }

    protected function prepareTempFolder($prefix)
    {
        $temp_file = tempnam(sys_get_temp_dir(), $prefix);
        unlink($temp_file);
        mkdir($temp_file, 0777);
        return $temp_file . '/';
    }

    protected function removeTempFolder($path) {

        $files = glob($path . '/*');
        foreach ($files as $file) {
            is_dir($file) ? $this->removeTempFolder($file) : unlink($file);
        }
        return rmdir($path);
    }
}

PhpUnitTestCase::$archives = [
    Formats::SEVEN_ZIP => ['a91fb294d6eb88df24ab26ae5f713775', 'fixtures.7z', 'https://github.com/wapmorgan/UnifiedArchive/releases/download/0.0.1/fixtures.7z'],
    Formats::ISO => ['f3bb89062d2c62fb2339c913933db112', 'fixtures.iso', 'https://github.com/wapmorgan/UnifiedArchive/releases/download/0.0.1/fixtures.iso'],
    Formats::TAR => ['d64474b28bfd036abb885b4e80c847b3', 'fixtures.tar', 'https://github.com/wapmorgan/UnifiedArchive/releases/download/0.0.1/fixtures.tar'],
    Formats::TAR_BZIP => ['e2ca07d2f1007f312493a12b239544df', 'fixtures.tar.bz2', 'https://github.com/wapmorgan/UnifiedArchive/releases/download/0.0.1/fixtures.tar.bz2'],
    Formats::TAR_GZIP => ['510479bdead0ecafcaeac2d755a30112', 'fixtures.tar.gz', 'https://github.com/wapmorgan/UnifiedArchive/releases/download/0.0.1/fixtures.tar.gz'],
    Formats::TAR_LZMA => ['f4be3134e45818b8c7d4e8f8ac76d2dc', 'fixtures.tar.xz', 'https://github.com/wapmorgan/UnifiedArchive/releases/download/0.0.1/fixtures.tar.xz'],
    Formats::ZIP => ['69dcdf13d2a8b7630e2f54fa5ab97d5a', 'fixtures.zip', 'https://github.com/wapmorgan/UnifiedArchive/releases/download/0.0.1/fixtures.zip'],
];
PhpUnitTestCase::$oneFileArchives = [
    Formats::GZIP => ['4ab7e4e61bc74dfd151487e94e58ccf8', 'onefile.gz', 'https://github.com/wapmorgan/UnifiedArchive/releases/download/0.0.1/onefile.gz'],
    Formats::BZIP => ['f3295b2a5afded3e4b42c583aa0bde6a', 'onefile.bz2', 'https://github.com/wapmorgan/UnifiedArchive/releases/download/0.0.1/onefile.bz2'],
    Formats::LZMA => ['9637c331bc694bf977ad34828c83c911', 'onefile.xz', 'https://github.com/wapmorgan/UnifiedArchive/releases/download/0.0.1/onefile.xz'],
];

PhpUnitTestCase::$fixtureContents = [
    'folder' => [
        'subfolder' => [
            'subfile' => 'Content',
        ],
        'subdoc' => 'Subdoc',
    ],
    'doc' => 'Doc',
];

/**
 * Downloading function with retrying
 * @param $url
 * @param $target
 * @param $md5
 * @param int $retry
 * @return bool
 * @throws Exception
 */
function downloadFixture($url, $target, $md5, $retry = 3)
{
    if (copy($url, $target) === false) {
        if ($retry > 0)
            return downloadFixture($url, $target, $md5, $retry - 1);
    } else if (md5_file($target) === $md5) {
        echo 'Downloaded '.$target.PHP_EOL;
        return true;
    }

    if (unlink($target) && $retry > 0)
        return downloadFixture($url, $target, $md5, $retry - 1);

    throw new Exception('Unable to download '.$url.' to '.$target);
}

/**
 * Checking fixtures
 */
foreach ([ARCHIVES_DIR, WORK_DIR] as $dir) {
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0777))
            throw new Exception('Could not create '.$dir.' directory');
    }
}

foreach (array_merge(PhpUnitTestCase::$archives, PhpUnitTestCase::$oneFileArchives) as $fixture) {
    $fixture_file = PhpUnitTestCase::getArchivePath($fixture[1]);
    if (!file_exists($fixture_file)) {
        downloadFixture($fixture[2], $fixture_file, $fixture[0]);
    } else if (md5_file($fixture_file) !== $fixture[0]) {
        if (!unlink($fixture_file)) {
            throw new Exception('Unable to delete fixture: ' . $fixture_file);
        }
        downloadFixture($fixture[2], $fixture_file, $fixture[0]);
    }
}
