<?php
/**
 * @author    Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2026 Claus-Justus Heine
 * @license   AGPL-3.0-or-later
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace OCA\FilesArchive\Storage;

/**
 * Directory index of the members of an archive.
 *
 * The archive storage answers is_dir() and opendir() for every member while
 * Nextcloud builds the file cache. Looking those answers up in the flat member
 * list on each call makes a scan of an archive with N members and D directories
 * O(N x D); this class computes the two maps once, in a single pass.
 */
class ArchiveDirectoryIndex
{
  /**
   * @var array<string,true>
   *
   * Paths of all directories, keyed by path. Does not include the root.
   */
  private array $directories = [];

  /**
   * @var array<string,list<string>>
   *
   * Directory path => names of its immediate children. The root has the empty
   * string as key.
   */
  private array $children = ['' => []];

  /**
   * @param iterable<string> $memberPaths Archive member paths, '/'-separated.
   *                                       Leading and trailing slashes are
   *                                       ignored; empty paths are skipped.
   */
  public function __construct(iterable $memberPaths)
  {
    foreach ($memberPaths as $memberPath) {
      $memberPath = $this->normalize($memberPath);
      if ($memberPath === '') {
        continue;
      }
      $parent = $this->parent($memberPath);
      $this->children[$parent][] = $this->name($memberPath);
      for (; $parent !== ''; $parent = $this->parent($parent)) {
        $this->directories[$parent] = true;
      }
    }
    // A directory may only occur as an intermediate component of a member
    // path ("a/b/c.txt" implies "a" and "a/b"). Register it as a child of its
    // own parent, and give it an (empty) child list even when it is empty.
    foreach (array_keys($this->directories) as $directory) {
      $this->children[$this->parent($directory)][] = $this->name($directory);
      $this->children[$directory] ??= [];
    }
  }

  /**
   * Is the given path a directory of the archive? The root always is.
   */
  public function isDirectory(string $path): bool
  {
    $path = $this->normalize($path);
    return $path === '' || isset($this->directories[$path]);
  }

  /**
   * @return list<string> The immediate children of $path, sorted, without
   *                      duplicates. Empty when $path is not a directory.
   */
  public function children(string $path): array
  {
    $path = $this->normalize($path);
    $children = array_values(array_unique($this->children[$path] ?? []));
    sort($children);
    return $children;
  }

  /**
   * @return list<string> All directory paths, sorted. The root is not included.
   */
  public function directories(): array
  {
    $directories = array_keys($this->directories);
    sort($directories);
    return $directories;
  }

  private function normalize(string $path): string
  {
    return trim($path, '/');
  }

  private function parent(string $path): string
  {
    $slash = strrpos($path, '/');
    return $slash === false ? '' : substr($path, 0, $slash);
  }

  private function name(string $path): string
  {
    $slash = strrpos($path, '/');
    return $slash === false ? $path : substr($path, $slash + 1);
  }
}
