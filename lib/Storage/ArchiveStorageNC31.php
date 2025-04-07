<?php
/**
 * @author    Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022, 2023, 2024, 2025 Claus-Justus Heine
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

use Throwable;
use wapmorgan\UnifiedArchive\ArchiveEntry;

use Psr\Log\LoggerInterface;

// F I X M E: those are not public, but ...
use OC\Files\Storage\Common as AbstractStorage;
use OC\Files\Storage\PolyFill\CopyDirectory;

use Icewind\Streams\CallbackWrapper;
use Icewind\Streams\CountWrapper;
use Icewind\Streams\IteratorDirectory;

use OCP\AppFramework\IAppContainer;
use OCP\Cache\CappedMemoryCache;
use OCP\Files\Cache\ICacheEntry;
use OCP\Files\Cache\IScanner;
use OCP\Files\File;
use OCP\Files\FileInfo;
use OCP\Files\Storage\IStorage;

use OCA\FilesArchive\Constants;
use OCA\FilesArchive\Service\ArchiveServiceFactory;
use OCA\FilesArchive\Toolkit\Exceptions as ToolkitExceptions;
use OCA\FilesArchive\Toolkit\Service\ArchiveService;

// phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

/** Virtual storage mapping an archive file into the user file-space. */
class ArchiveStorageNC31 extends AbstractStorage
{
  use \OCA\FilesArchive\Toolkit\Traits\LoggerTrait;
  use \OCA\FilesArchive\Toolkit\Traits\UtilTrait;
  use CopyDirectory;

  public const PATH_SEPARATOR = Constants::PATH_SEPARATOR;

  public const PARAMETER_ARCHIVE_FILE = 'archiveFile';
  public const PARAMETER_ARCHIVE_PASS_PHRASE = 'passPhrase';
  public const PARAMETER_APP_CONTAINER = 'appContainer';
  public const PARAMETER_ARCHIVE_SIZE_LIMIT = 'archiveSizeLimit';
  public const PARAMETER_STRIP_COMMON_PATH_PREFIX = 'stripCommonPathPrefix';

  /** @var string */
  protected $appName;

  /** @var IAppContainer */
  protected IAppContainer $appContainer;

  /** @var ArchiveService */
  protected ArchiveService $archiveService;

  /** @var File */
  protected File $archiveFile;

  /** @var string */
  protected ?string $commonPathPrefix = null;

  /** @var null|string */
  protected ?string $archivePassPhrase;

  /** @var bool */
  protected bool $stripCommonPathPrefix;

  /**
   * @var bool
   *
   * Control lazy-scanning of the archive. The scanner must not be called in
   * the constructor in order not to kill performance and is called on demand
   * when trying to access the listing-cache.
   */
  protected $archiveHasBeenScanned = false;

  /**
   * @var null|array<string, ArchiveEntry>
   *
   * Cache for the archive listing. Only called on demand as this is costly
   * for large archives.
   */
  protected $files = null;

  /**
   * @var array
   *
   * Cache for the folder entries of the directory listing. Computed from the
   * $files array.
   */
  protected $dirNames = null;

  /**
   * @var int
   *
   * The file-id of the mount-point. This is non-null after the initial
   * file-scan has completed. Once set, the storage will forward several tasks
   * to the filecache instead of rescanning the archive over and over again.
   */
  protected ?int $rootId = null;

  /**
   * @var CappedMemoryCache
   *
   * Cache for the database filecache
   */
  protected $fileCacheCache;

  /** {@inheritdoc} */
  public function __construct($parameters)
  {
    parent::__construct($parameters);
    $this->archiveFile = $parameters[self::PARAMETER_ARCHIVE_FILE];
    $sizeLimit = $parameters[self::PARAMETER_ARCHIVE_SIZE_LIMIT] ?? Constants::DEFAULT_ADMIN_ARCHIVE_SIZE_LIMIT;
    $this->archivePassPhrase = $parameters[self::PARAMETER_ARCHIVE_PASS_PHRASE] ?? null;
    $this->appContainer = $parameters[self::PARAMETER_APP_CONTAINER];
    $this->stripCommonPathPrefix = $parameters[self::PARAMETER_STRIP_COMMON_PATH_PREFIX] ?? false;

    $this->appName = $this->appContainer->get('appName');
    $factory = $this->appContainer->get(ArchiveServiceFactory::class);
    $this->archiveService = $factory->get($this->archiveFile);
    $this->archiveService->setSizeLimit($sizeLimit);
    $this->logger = $this->appContainer->get(LoggerInterface::class);

    $this->fileCacheCache = new CappedMemoryCache;
  }

  /**
   * @param int $rootId
   *
   * @return void
   */
  public function setRootId(int $rootId):void
  {
    $this->rootId = $rootId;
  }

  /**
   * @return int
   */
  public function getRootId():int
  {
    return $this->rootId;
  }

  /** {@inheritdoc} */
  public function getScanner(string $path = '', ?IStorage $storage = null): IScanner
  {
    if ($storage) {
      return parent::getScanner($path, $storage);
    }
    if (!isset($this->scanner)) {
      $this->scanner = new class($storage) extends \OC\Files\Cache\Scanner
      {
        /** {@inheritfile} */
        public function scanFile($file, $reuseExisting = 0, $parentId = -1, $cacheData = null, $lock = true, $data = null)
        {
          // mark uncached
          $oldRootId = $this->storage->getRootId();
          $this->storage->setRootId(null);

          $result = parent::scanFile($file, $reuseExisting, $parentId, $cacheData, $lock, $data);

          // mark again as cached
          $this->storage->setRootId($oldRootId);

          return $result;
        }
      };
    }
    return $this->scanner;
  }

  /**
   * Return the file-cache entry for the given path, or false if the cache
   * entry does not exist.
   *
   * @param string $path
   *
   * @return bool|ICacheEntry
   */
  protected function getCacheEntry(string $path):ICacheEntry|bool
  {
    if (!$this->fileCacheCache->hasKey($path)) {
      $this->fileCacheCache->set($path, $this->getCache()->get($path));
    }
    return $this->fileCacheCache->get($path);
  }

  /**
   * @return void
   */
  protected function openArchive():void
  {
    if (!$this->archiveService->isOpen()) {
      $this->archiveService->open($this->archiveFile, password: $this->archivePassPhrase);
    }
    if ($this->commonPathPrefix === null) {
      $this->commonPathPrefix = $this->stripCommonPathPrefix
        ? $this->archiveService->getCommonDirectoryPrefix()
        : '';
    }
  }

  /**
   * Archive scanner to be run before actually accessing the storage.
   *
   * @return void
   */
  protected function scanArchive():void
  {
    // $this->logException(new \Exception('RESCAN'));

    try {
      $this->openArchive();

      $commonPrefixLen = strlen($this->commonPathPrefix);

      $files = $this->archiveService->getFiles();
      $this->files = [];
      $this->dirNames = [];
      foreach ($files as $path => $fileInfo) {
        $normalizedPath = trim($this->buildPath($path), Constants::PATH_SEPARATOR);
        $normalizedPath = substr($normalizedPath, $commonPrefixLen);
        $this->files[$normalizedPath] = $fileInfo;
        $dirName = dirname($normalizedPath);
        if (!empty($dirName)) {
          $pathChain = explode(Constants::PATH_SEPARATOR, dirname($normalizedPath));
          $dirPath = array_shift($pathChain);
          $this->dirNames[$dirPath] = true;
          foreach ($pathChain as $pathComponent) {
            $dirPath .= Constants::PATH_SEPARATOR . $pathComponent;
            $this->dirNames[$dirPath] = true;
          }
        }
      }
      $this->dirNames = array_keys($this->dirNames);

      // $this->logInfo('FILES ' . print_r($this->files, true));
      // $this->logInfo('DIRS ' . print_r($this->dirNames, true));

    } catch (ToolkitExceptions\ArchiveTooLargeException $e) {
      throw $e;
    } catch (Throwable $t) {
      $this->logException($t, 'Unable to open archive file ' . $this->archiveFile->getPath());
      $this->files = [];
      $this->dirNames = [];
    }
    $this->archiveHasBeenScanned = true;
  }

  /** @return array The files array, scanning the archive file if not already done. */
  protected function getFiles():array
  {
    if (!$this->archiveHasBeenScanned) {
      $this->scanArchive();
    }
    return $this->files;
  }

  /** @return array The directory names computed from the $files array */
  protected function getDirNames():array
  {
    if (!$this->archiveHasBeenScanned) {
      $this->scanArchive();
    }
    return $this->dirNames;
  }

  /** {@inheritdoc} */
  public function getId()
  {
    return $this->appName . ':' . $this->archiveFile->getPath() . self::PATH_SEPARATOR;
  }

  /**
   * @param null|string $path The path to work on.
   *
   * @return string
   */
  protected function buildPath(?string $path):string
  {
    return \OC\Files\Filesystem::normalizePath($path);
  }

  /**
   * Attach self::PATH_SEPARATOR to the dirname if it is not the root directory.
   *
   * @param string $dirName The directory name to work on.
   *
   * @return string
   */
  protected static function normalizeDirectoryName(string $dirName):string
  {
    if ($dirName == '.') {
      $dirName = '';
    }
    $dirName = trim($dirName, self::PATH_SEPARATOR);
    return empty($dirName) ? $dirName : $dirName . self::PATH_SEPARATOR;
  }

  /**
   * Slightly modified pathinfo() function which also normalized directories
   * before computing the components.
   *
   * @param string $path The path to work on.
   *
   * @param int $flags As for the upstream pathinfo() function.
   *
   * @return string|array
   */
  protected static function pathInfo(string $path, int $flags = PATHINFO_ALL)
  {
    $pathInfo = pathinfo($path, $flags);
    if ($flags == PATHINFO_DIRNAME) {
      $pathInfo = self::normalizeDirectoryName($pathInfo);
    } elseif (is_array($pathInfo)) {
      $pathInfo['dirname'] = self::normalizeDirectoryName($pathInfo['dirname']);
    }
    return $pathInfo;
  }

  /** {@inheritdoc} */
  public static function checkDependencies()
  {
    return true;
  }

  /** {@inheritdoc} */
  public function isReadable($path): bool
  {
    // at least check whether it exists
    // subclasses might want to implement this more thoroughly
    return $this->file_exists($path);
  }

  /** {@inheritdoc} */
  public function isUpdatable($path): bool
  {
    // return $this->file_exists($path);
    return false; // readonly for now
  }

  /** {@inheritdoc} */
  public function isSharable($path): bool
  {
    // sharing cannot work in general as the database access need additional
    // credentials
    return false;
  }

  /** {@inheritdoc} */
  public function filemtime($path):int|false
  {
    $path = trim($path, self::PATH_SEPARATOR);
    $result = false;
    // $this->logInfo('PATH ' . $path);
    if ($this->is_dir($path)) {
      $result = $this->archiveFile->getMTime();
    } elseif ($this->is_file($path)) {
      if ($this->rootId > 0) {
        /** @var ICacheEntry $cacheEntry */
        $cacheEntry = $this->getCacheEntry($path);
        $result = $cacheEntry->getMTime();
      } else {
        $result = $this->getFiles()[$path]->modificationTime;
      }
    }
    // $this->logInfo('MTIME RESULT ' . $path . ' -> ' . (int)$result);
    return $result;
  }

  /**
   * {@inheritdoc}
   *
   * The AbstractStorage class relies on mtime($path) > $time for triggering a
   * cache invalidation.
   *
   * In principle we never update EXCEPT when the associated archive file has
   * been changed. So we return true if the mtime of the associated archive
   * file is later than the $time argument.
   *
   * @param string $path Dir-entry path.
   *
   * @param int $time This is the storage_mtime column of the filecache table
   * for the given $path.
   */
  public function hasUpdated($path, $time): bool
  {
    /** @var ICacheEntry $rootEntry */
    $rootEntry = $this->getCache()->get('');
    $result = min($rootEntry->getStorageMTime(), $rootEntry->getMTime()) < $this->archiveFile->getMTime();
    // $result = $time < $this->archiveFile->getMTime();
    // $this->logInfo('REF TIME ' . $time . ' ARCH TIME ' .  $this->archiveFile->getMTime() . ' UPDATED ' . (int)$result);
    return $result;
  }

  /** {@inheritdoc} */
  public function filesize($path): false|int|float
  {
    if ($this->is_dir($path)) {
      return 0;
    }
    if (!$this->is_file($path)) {
      return false;
    }
    if ($this->rootId > 0) {
      /** @var ICacheEntry $cacheEntry */
      $cacheEntry = $this->getCacheEntry($path);
      return $cacheEntry->getSize();
    }

    $path = trim($path, self::PATH_SEPARATOR);
    // $this->logInfo('PATH ' . $path);
    return $this->getFiles()[$path]->uncompressedSize;
  }

  /** {@inheritdoc} */
  public function rmdir($path)
  {
    return false;
  }

  /** {@inheritdoc} */
  public function test(): bool
  {
    return $this->archiveService->canOpen($this->archiveFile);
  }

  /** {@inheritdoc} */
  public function stat($path)
  {
    if (!$this->is_file($path) && !$this->is_dir($path)) {
      return false;
    }
    return [
      'mtime' => $this->filemtime($path),
      'size' => $this->filesize($path),
    ];
  }

  /** {@inheritdoc} */
  public function file_exists(string $path): bool
  {
    if ($this->rootId > 0) {
      if (!$this->fileCacheCache->hasKey($path)) {
        $this->fileCacheCache->set($path, $this->getCache()->get($path));
      }
      return !!$this->fileCacheCache->get($path);
    }

    return $this->is_dir($path) || $this->is_file($path);
  }

  /** {@inheritdoc} */
  public function unlink(string $path): bool
  {
    return false;
  }

  /** {@inheritdoc} */
  public function opendir(string $path)
  {
    if (!$this->is_dir($path)) {
      return false;
    }

    if ($this->rootId > 0) {
      /** @var ICacheEntry $cacheEntry */
      $cacheEntry = $this->getCacheEntry($path);
      $dirFileId = $cacheEntry->getId();
      $folderContents = $this->getCache()->getFolderContentsById($dirFileId);
      $fileNames = [];
      foreach ($folderContents as $cacheEntry) {
        $path = $cacheEntry->getPath();
        $this->fileCacheCache->set($path, $cacheEntry);
        $fileNames[] = basename($path);
      }
      sort($fileNames);
      return IteratorDirectory::wrap(array_values($fileNames));
    }

    $path = ltrim($path, Constants::PATH_SEPARATOR);

    $fileNames = array_map(
      function(string $memberPath) use ($path) {
        $memberPath = trim(
          substr($memberPath, strlen($path)),
          Constants::PATH_SEPARATOR,
        );
        $slashPos = strpos($memberPath, Constants::PATH_SEPARATOR);
        if ($slashPos === false) {
          return $memberPath;
        }
        return substr($memberPath, 0, $slashPos);
      },
      array_filter(
        array_keys($this->getFiles()),
        fn(string $memberPath) => str_starts_with($memberPath, $path),
      )
    );
    $fileNames = array_unique($fileNames);

    // $this->logInfo('DIRLISTING ' . $path . ': ' . print_r($fileNames, true));

    return IteratorDirectory::wrap(array_values($fileNames));
  }

  /** {@inheritdoc} */
  public function mkdir(string $path): bool
  {
    return false;
  }

  /** {@inheritdoc} */
  public function is_dir($path): bool
  {
    if ($this->rootId > 0) {
      /** @var ICacheEntry $cacheEntry */
      $cacheEntry = $this->getCacheEntry($path);
      if (!$cacheEntry) {
        return false;
      }
      return $cacheEntry->getMimeType() == 'httpd/unix-directory';
    }

    $path = trim($path, self::PATH_SEPARATOR);
    if ($path === '') {
      return true;
    }
    $result = array_search($path, $this->getDirNames()) !== false;

    // $this->logInfo('PATH ' . $path . '  ' . (int)$result);

    return $result;
  }

  /** {@inheritdoc} */
  public function is_file($path): bool
  {
    if ($this->rootId > 0) {
      /** @var ICacheEntry $cacheEntry */
      $cacheEntry = $this->getCacheEntry($path);
      if (!$cacheEntry) {
        return false;
      }
      return $cacheEntry->getMimeType() != 'httpd/unix-directory';
    }

    $path = trim($path, self::PATH_SEPARATOR);
    // $this->logInfo('PATH ' . $path);
    return !empty($this->getFiles()[$path]);
  }

  /** {@inheritdoc} */
  public function filetype($path)
  {
    if ($this->is_dir($path)) {
      return FileInfo::TYPE_FOLDER;
    } elseif ($this->is_file($path)) {
      return FileInfo::TYPE_FILE;
    } else {
      return false;
    }
  }

  /** {@inheritdoc} */
  public function fopen($path, $mode)
  {
    $useExisting = true;
    switch ($mode) {
      case 'r':
      case 'rb':
        return $this->readStream($path);
      case 'w':
      case 'w+':
      case 'wb':
      case 'wb+':
        $useExisting = false;
        // no break
      case 'a':
      case 'ab':
      case 'r+':
      case 'a+':
      case 'x':
      case 'x+':
      case 'c':
      case 'c+':
        //emulate these
        if ($useExisting and $this->file_exists($path)) {
          if (!$this->isUpdatable($path)) {
            return false;
          }
          $tmpFile = $this->getCachedFile($path);
        } else {
          if (!$this->isCreatable(dirname($path))) {
            return false;
          }
          if (!$this->touch($path)) {
            return false;
          }
          $tmpFile = $this->di(ITempManager::class)->getTemporaryFile();
        }
        $source = fopen($tmpFile, $mode);

        return CallbackWrapper::wrap($source, null, null, function () use ($tmpFile, $path) {
          $this->writeStream($path, fopen($tmpFile, 'r'));
          unlink($tmpFile);
        });
    }
    return false;
  }

  /** {@inheritdoc} */
  public function writeStream(string $path, $stream, ?int $size = null): int
  {
    return 0;
  }

  /** {@inheritdoc} */
  public function readStream(string $path)
  {
    if (!$this->is_file($path)) {
      return false;
    }
    $path = trim($path, self::PATH_SEPARATOR);

    $this->openArchive();

    return $this->archiveService->getFileStream($this->commonPathPrefix . $path);
  }

  /** {@inheritdoc} */
  public function touch($path, $mtime = null)
  {
    return false;
  }

  /** {@inheritdoc} */
  public function rename($path1, $path2): bool
  {
    return false;
  }
}
