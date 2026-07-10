<?php

declare(strict_types=1);

namespace Alchemy\Zippy\Functional;

use Symfony\Component\Finder\Finder;

final class CreateArchiveTest extends FunctionalTestCase
{
    private static $file;

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        if (file_exists(self::$file)) {
            unlink(self::$file);
            self::$file = null;
        }
    }

    #[\PHPUnit\Framework\Attributes\DoesNotPerformAssertions]
    public function testCreate()
    {
        $adapter = $this->getAdapter();
        $extension = $this->getArchiveExtensionForAdapter($adapter);

        self::$file = __DIR__ . '/samples/create-archive.' . $extension;

        $archive = $adapter->create(self::$file, array(
            'directory' => __DIR__ . '/samples/directory',
        ), true);

        return $archive;
    }

    #[\PHPUnit\Framework\Attributes\Depends('testCreate')]
    public function testExtract($archive)
    {
        $target = __DIR__ . '/samples/tmp';
        $archive->extract($target);

        $finder = new Finder();
        $finder
            ->files()
            ->in($target);

        $files2find = array(
            '/directory/README.md',
            '/directory/photo.jpg'
        );

        foreach ($finder as $file) {
            $this->assertSame(0, strpos($file->getPathname(), $target));
            $member = substr($file->getPathname(), strlen($target));
            $this->assertContains($member, $files2find, "looking for $member in files2find");
            unset($files2find[array_search($member, $files2find)]);
        }

        $this->assertSame(array(), $files2find);
    }

    #[\PHPUnit\Framework\Attributes\Depends('testCreate')]
    public function testExtractOnExistingFilesCanOverwrite($archive)
    {
      $random = (string) uniqid((string)mt_rand(), true);
        $target = __DIR__ . '/samples/tmp';

        $files2find = array(
            '/directory/README.md',
            '/directory/photo.jpg'
        );
        foreach ($files2find as $file) {
            $file2create = $target . $file;
            if (!is_dir(dirname($file2create))) {
                mkdir(dirname($file2create), 0777, true);
            }
            file_put_contents($file2create, $random);
        }

        $archive->extract($target, true);

        $finder = new Finder();
        $finder
            ->files()
            ->in($target);

        foreach ($finder as $file) {
            $this->assertSame(0, strpos($file->getPathname(), $target));
            $this->assertNotSame($random, file_get_contents($file->getPathname()));
            $member = substr($file->getPathname(), strlen($target));
            $this->assertContains($member, $files2find);
            unset($files2find[array_search($member, $files2find)]);
        }

        $this->assertSame(array(), $files2find);
    }
}
