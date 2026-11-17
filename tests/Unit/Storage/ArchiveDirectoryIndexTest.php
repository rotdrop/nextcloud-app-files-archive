<?php

namespace OCA\FilesArchive\Tests\Unit\Storage;

use OCA\FilesArchive\Storage\ArchiveDirectoryIndex;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ArchiveDirectoryIndex::class)]
class ArchiveDirectoryIndexTest extends TestCase
{
  /**
   * @return list<string>
   */
  private static function memberPaths(): array
  {
    return [
      'a/b/c.txt',
      'a/b/d.txt',
      'a/e.txt',
      'top.txt',
    ];
  }

  public function testRootIsADirectory(): void
  {
    $index = new ArchiveDirectoryIndex([]);
    self::assertTrue($index->isDirectory(''));
    self::assertTrue($index->isDirectory('/'));
    self::assertFalse($index->isDirectory('missing'));
    self::assertSame([], $index->children(''));
  }

  public function testOnlyDirectoriesOfMembersAreDirectories(): void
  {
    $index = new ArchiveDirectoryIndex(self::memberPaths());
    self::assertTrue($index->isDirectory('a'));
    self::assertTrue($index->isDirectory('a/b'));
    self::assertFalse($index->isDirectory('a/b/c.txt'));
    self::assertFalse($index->isDirectory('top.txt'));
  }

  public function testChildrenAreTheImmediateChildrenOnly(): void
  {
    $index = new ArchiveDirectoryIndex(self::memberPaths());
    self::assertSame(['a', 'top.txt'], $index->children(''));
    self::assertSame(['b', 'e.txt'], $index->children('a'));
    self::assertSame(['c.txt', 'd.txt'], $index->children('a/b'));
    self::assertSame([], $index->children('a/b/c.txt'));
  }

  public function testIntermediateDirectoriesAreDerivedFromMemberPaths(): void
  {
    $index = new ArchiveDirectoryIndex(['x/y/z/deep.txt']);
    self::assertTrue($index->isDirectory('x'));
    self::assertTrue($index->isDirectory('x/y'));
    self::assertTrue($index->isDirectory('x/y/z'));
    self::assertSame(['y'], $index->children('x'));
    self::assertSame(['z'], $index->children('x/y'));
    self::assertSame(['deep.txt'], $index->children('x/y/z'));
  }

  public function testPathPrefixIsNotAChild(): void
  {
    $index = new ArchiveDirectoryIndex(['foo/one.txt', 'foobar/two.txt']);
    self::assertSame(['one.txt'], $index->children('foo'));
    self::assertSame(['two.txt'], $index->children('foobar'));
    self::assertTrue($index->isDirectory('foobar'));
    self::assertFalse($index->isDirectory('foo/bar'));
  }

  public function testSlashesAndDuplicateMembersAreNormalised(): void
  {
    $index = new ArchiveDirectoryIndex(['/a/x.txt', 'a/x.txt', 'a/']);
    self::assertSame(['a'], $index->children(''));
    self::assertSame(['x.txt'], $index->children('a'));
    self::assertTrue($index->isDirectory('a/'));
  }

  public function testDirectoriesAreSorted(): void
  {
    $index = new ArchiveDirectoryIndex(['b/1', 'a/2', 'c/3']);
    self::assertSame(['a', 'b', 'c'], $index->directories());
  }
}
