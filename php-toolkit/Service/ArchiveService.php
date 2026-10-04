<?php
/**
 * Some PHP utility functions for Nextcloud apps.
 *
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022-2026 Claus-Justus Heine <himself@claus-justus-heine.de>
 * @license AGPL-3.0-or-later
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

namespace OCA\RotDrop\Toolkit\Service;

use Spatie\TypeScriptTransformer\Attributes as TSAttributes;

use DateTimeInterface;
use Normalizer;
use SensitiveParameter;
use Throwable;
use RarArchive;
use RarException;
use ZipArchive;

use wapmorgan\UnifiedArchive\Abilities as DriverAbilities;
use wapmorgan\UnifiedArchive\ArchiveEntry;
use wapmorgan\UnifiedArchive\Drivers\Basic\BasicDriver;
use wapmorgan\UnifiedArchive\Exceptions as BackendExceptions;

use OCP\Files\File;
use OCP\IL10N;
use OCP\Util as CloudUtil;
use Psr\Log\LoggerInterface as ILogger;

use OCA\RotDrop\Toolkit\Backend\ArchiveBackend;
use OCA\RotDrop\Toolkit\Backend\ArchiveFormats;
use OCA\RotDrop\Toolkit\Exceptions;
use OCA\RotDrop\Toolkit\Service\ArchiveService\ArchiveInfo;

/**
 * Wrapper around the actual archive backend class in order to interface with
 * the virtual storage and actual archive extraction controllers.
 */
#[TSAttributes\Typescript]
class ArchiveService
{
  use \OCA\RotDrop\Toolkit\Traits\LoggerTrait;
  use \OCA\RotDrop\Toolkit\Traits\UtilTrait;

  /**
   * @var string
   * Internal format of the underlying archive backend.
   */
  public const ARCHIVE_INFO_FORMAT = 'format';

  /**
   * @var string
   * Mime-type of the archive file.
   */
  public const ARCHIVE_INFO_MIME_TYPE = 'mimeType';

  public const ARCHIVE_INFO_IS_ENCRYPTED = 'isEncrypted';

  /**
   * @var string
   *
   * The size of the archive file (not neccessarily the sum of the size of the
   * archive members).
   */
  public const ARCHIVE_INFO_SIZE = 'size';

  /**
   * @var string
   *
   * The sum of the compressed size of the archive members.
   */
  public const ARCHIVE_INFO_COMPRESSED_SIZE = 'compressedSize';

  /**
   * @var string
   *
   * The sum of the uncompressed size of the archive members.
   */
  public const ARCHIVE_INFO_ORIGINAL_SIZE = 'originalSize';

  /**
   * @var string
   *
   * The number of archive members (files) in the archive.
   */
  public const ARCHIVE_INFO_NUMBER_OF_FILES = 'numberOfFiles';

  /**
   * @var string
   *
   * Some archive formats support optional creator supplied comments.
   */
  public const ARCHIVE_INFO_COMMENT = 'comment';

  /**
   * @var string
   *
   * Propose a mount point name based on the archive name.
   */
  public const ARCHIVE_INFO_DEFAULT_MOUNT_POINT = 'defaultMountPoint';

  /**
   * @var string
   *
   * Compute the common path prefix of the archive members.
   */
  public const ARCHIVE_INFO_COMMON_PATH_PREFIX = 'commonPathPrefix';

  /**
   * @var string
   *
   * The basename of the backend driver class.
   */
  public const ARCHIVE_INFO_BACKEND_DRIVER = 'backendDriver';

  /**
   * @var array All array keys contained in the info-array obtained from
   * archiveInfo().
   */
  public const ARCHIVE_INFO_KEYS = [
    self::ARCHIVE_INFO_BACKEND_DRIVER,
    self::ARCHIVE_INFO_COMMENT,
    self::ARCHIVE_INFO_COMMON_PATH_PREFIX,
    self::ARCHIVE_INFO_COMPRESSED_SIZE,
    self::ARCHIVE_INFO_DEFAULT_MOUNT_POINT,
    self::ARCHIVE_INFO_FORMAT,
    self::ARCHIVE_INFO_IS_ENCRYPTED,
    self::ARCHIVE_INFO_MIME_TYPE,
    self::ARCHIVE_INFO_NUMBER_OF_FILES,
    self::ARCHIVE_INFO_ORIGINAL_SIZE,
    self::ARCHIVE_INFO_SIZE,
  ];

  /**
   * @var Enforce UTF-8 in the environment.
   */
  protected const OVERRIDE_ENVIRONMENT = [
    'LANG' => 'C.UTF-8',
    'LC_ALL' => 'C.UTF-8',
  ];

  /** @var null|int */
  private $sizeLimit = null;

  /** @var ArchiveBackend */
  private $archiver;

  /** @var File */
  private $fileNode;

  /** @var array */
  private ?array $archiveFiles = null;

  /** @var */
  private ?ArchiveInfo $archiveInfo;

  /** @var array */
  private array $savedProcessEnvironment;

  /**
   * @var array<string, string>
   *
   * Map from the NFC-normalized member name (as seen by Nextcloud, which
   * normalizes all paths to NFC) to the raw member name as actually stored in
   * the backend archive. Built in open(). Resolving each member individually
   * allows archives which mix Unicode normalization forms (e.g. some names in
   * NFC, some in NFD as produced by macOS) to be extracted correctly instead
   * of failing or silently losing members.
   */
  private array $memberNameMap = [];

  /**
   * @var array<string, string[]>
   *
   * Map from the NFC-normalized member name to the list of distinct raw member
   * names which collapse onto it. A non-empty entry means several archive
   * members would map to the same file name after Unicode normalization and
   * therefore cannot all be extracted as distinct files; without reporting
   * this the surplus members would be lost silently.
   */
  private array $collidingMembers = [];

  /**
   * @var
   * Archive passphrase.
   */
  private ?string $archivePassphrase = null;

  // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
  public function __construct(
    protected ILogger $logger,
    protected IL10N $l,
  ) {
    $this->archiver = null;
    $this->fileNode = null;
    $this->archiveInfo = null;
  }
  // phpcs:enable

  /**
   * Guard against undefined $this->l.
   *
   * @param string $formatString
   *
   * @param mixed $parameters
   *
   * @return string
   */
  protected function t(string $formatString, mixed $parameters = null):string
  {
    if (!empty($this->l)) {
      return $this->l->t($formatString, $parameters);
    }
    return vsprintf($formatString, $parameters);
  }

  /**
   * Set the size limit for the uncompressed size of the archives. Archives
   * with larger uncompressed size will not be handled.
   *
   * @param null|int $sizeLimit Size-limit. Pass null to disable.
   *
   * @return ArchiveService Return $this for chaining.
   */
  public function setSizeLimit(?int $sizeLimit):ArchiveService
  {
    $this->sizeLimit = $sizeLimit;
    return $this;
  }

  /**
   * Return the currently configured size-limit.
   *
   * @return null|int
   */
  public function getSizeLimit():?int
  {
    return $this->sizeLimit;
  }

  /**
   * Return the local operating system path of the given file-node.
   *
   * @param File $fileNode
   *
   * @return string
   */
  private static function getLocalPath(File $fileNode):string
  {
    return realpath($fileNode->getStorage()->getLocalFile($fileNode->getInternalPath()));
  }

  /**
   * Check whether the given file can be opened.
   *
   * @param File $fileNode
   *
   * @return bool
   *
   * @throws Exceptions\ArchiveCannotOpenException
   */
  public function canOpen(File $fileNode):bool
  {
    $this->setProcessEnvironment();

    $localPath = self::getLocalPath($fileNode);

    $format = ArchiveFormats::detectArchiveFormat($localPath);
    $canOpen = $format !== null && ArchiveFormats::canOpen($format);

    $this->restoreProcessEnvironment();

    if ($format === null || $format === false) {
      throw new Exceptions\ArchiveCannotOpenException(
        $this->l->t('Unable to detect the archive format of "%1$s".', $fileNode->getName())
      );
    }
    if (!$canOpen) {
      $messages = [];
      if (str_starts_with($format, 't') && !str_starts_with($format, 'tar')) {
        $innerFormat = 'tar';
        $compositeFormat = $format;
        $format = substr($format, 1);
        $this->setProcessEnvironment();
        $canDecompress = $format !== null && ArchiveFormats::canOpen($format);
        $canUntar = ArchiveFormats::canOpen($innerFormat);
        $this->restoreProcessEnvironment();
        if (!$canDecompress) {
          $messages[] = $this->l->t('Archive format of "%1$s" detected as "%2$s", but there is no backend driver installed which can decompress ".%3$s" files.', [
             $fileNode->getName(),
             $compositeFormat,
             $format,
          ]);
        }
        if (!$canUntar) {
          // this should never be the case ...
          $messages[] = $this->l->t(
            'Unable to deal with tar-files. Please check the installation of the app.'
          );
        }
      } else {
        $messages[] = $this->l->t('The archive format of "%1$s" has been detected as "%2$s", but there is no backend driver installed which can deal with this format.', [
          $fileNode->getName(),
          $format,
        ]);
      }
      $formats = ArchiveFormats::getDeclaredDriverFormats();
      foreach ($formats[$format] as $driverClass) {
        $shortDriver = substr($driverClass, strrpos($driverClass, '\\') + 1);
        if (!$driverClass::isInstalled()) {
          $messages[] = $this->l->t('The "%1$s" driver could handle this format, but it is not installed.', $shortDriver);
          $typeLabel = BasicDriver::$typeLabels[$driverClass::TYPE];
          $instructions = $driverClass::getInstallationInstruction();
          $messages[] = $this->l->t('Installation instructions:')
            . ' '
            . ucfirst($typeLabel)
            . '. '
            . ucfirst($instructions)
            . '.';
          continue;
        }
        $abilities = $driverClass::getFormatAbilities($format);
        $requiredAbilities = [DriverAbilities::OPEN, DriverAbilities::EXTRACT_CONTENT];
        if (count(array_intersect($requiredAbilities, $abilities)) != count($requiredAbilities)) {
          $messages[] = $this->l->t('The "%1$s" driver claims to handle this format, but cannot extract the archive content.', $shortDriver);
        }
      }
      throw new Exceptions\ArchiveCannotOpenException(
        implode(PHP_EOL, $messages)
      );
    }

    return true;
  }

  /**
   * Return the "opened" status.
   *
   * @return true
   */
  public function isOpen():bool
  {
    return $this->archiver !== null;
  }

  /**
   * Close, i.e. unconfigure. This method is error agnostic, it simply unsets
   * the initial state variables.
   *
   * @return void
   */
  public function close():void
  {
    $this->archiver = null;
    $this->fileNode = null;
    $this->archiveFiles = null;
    $this->archiveInfo = null;
  }

  /**
   * @param File $fileNode
   *
   * @param null|int $sizeLimit
   *
   * @param null|string $password
   *
   * @return null|ArchiveService
   */
  public function open(File $fileNode, ?int $sizeLimit = null, #[SensitiveParameter] ?string $password = null):?ArchiveService
  {
    if (!$this->canOpen($fileNode)) {
      throw new Exceptions\ArchiveCannotOpenException($this->t('Unable to open archive file %s (%s)', [
        $fileNode->getPath(), self::getLocalPath($fileNode),
      ]));
    }

    $this->archivePassPhrase = $password;

    $this->setProcessEnvironment();

    $this->archiver = ArchiveBackend::open(self::getLocalPath($fileNode), password: $this->archivePassPhrase);

    $this->restoreProcessEnvironment();

    if (empty($this->archiver)) {
      throw new Exceptions\ArchiveCannotOpenException($this->t('Unable to open archive file %s (%s)', [
        $fileNode->getPath(), self::getLocalPath($fileNode),
      ]));
    }
    if ($sizeLimit === null) {
      $sizeLimit = $this->sizeLimit;
    }
    $this->fileNode = $fileNode;
    $archiveInfo = $this->getArchiveInfo();
    $archiveSize = $archiveInfo->originalSize;
    if ($sizeLimit !== null && $archiveSize > $sizeLimit) {
      $this->archiver = null;
      $this->fileNode = null;
      throw new Exceptions\ArchiveTooLargeException(
        $this->t('Uncompressed size of archive "%1$s" is too large: %2$s > %3$s', [
          $fileNode->getInternalPath(), CloudUtil::humanFileSize($archiveSize), CloudUtil::humanFileSize($sizeLimit),
        ]),
        $sizeLimit,
        $archiveSize,
      );
    }

    // Build a per-member map from the NFC-normalized name to the raw name as
    // stored in the archive. Nextcloud normalizes every path to NFC, so this
    // is the key by which members will be requested again on read. Resolving
    // each member on its own avoids choosing a single normalization for the
    // whole archive, which breaks archives that mix NFC and NFD names.
    $this->memberNameMap = [];
    $this->collidingMembers = [];
    foreach ($this->archiver->getFileNames() as $rawName) {
      $key = Normalizer::normalize($rawName, Normalizer::NFC);
      if ($key === false) {
        // The name is not valid UTF-8 (e.g. a legacy DOS/codepage name stored
        // without the UTF-8 flag). Keep the raw bytes as key so the member
        // stays addressable instead of being dropped.
        $key = $rawName;
      }
      if (array_key_exists($key, $this->memberNameMap) && $this->memberNameMap[$key] !== $rawName) {
        $this->collidingMembers[$key] ??= [ $this->memberNameMap[$key] ];
        $this->collidingMembers[$key][] = $rawName;
      }
      $this->memberNameMap[$key] = $rawName;
    }

    return $this;
  }

  /** @return ArchiveInfo Archive information, meta-data. */
  public function getArchiveInfo(): ArchiveInfo
  {
    if (empty($this->archiver)) {
      throw new Exceptions\ArchiveNotOpenException(
        $this->t('There is no archive file associated with this archiver instance.'));
    }

    if ($this->archiveInfo !== null) {
      return $this->archiveInfo;
    }

    $this->setProcessEnvironment();

    // getComment() throws if not supported (API documents differently)
    try {
      $archiveComment = $this->archiver->getComment();
    } catch (BackendExceptions\UnsupportedOperationException $e) {
      // just ignored
      $archiveComment = null;
    }

    // $this->logInfo('MIME ' .  $this->fileNode->getMimeType());

    $this->archiveInfo = ArchiveInfo::fromArray([
      self::ARCHIVE_INFO_FORMAT => $this->archiver->getFormat(),
      self::ARCHIVE_INFO_MIME_TYPE => $this->fileNode->getMimeType(),
      self::ARCHIVE_INFO_IS_ENCRYPTED => $this->isEncrypted(),
      self::ARCHIVE_INFO_SIZE => $this->archiver->getSize(),
      self::ARCHIVE_INFO_COMPRESSED_SIZE => $this->archiver->getCompressedSize(),
      self::ARCHIVE_INFO_ORIGINAL_SIZE => $this->archiver->getOriginalSize(),
      self::ARCHIVE_INFO_NUMBER_OF_FILES => $this->archiver->countFiles(),
      self::ARCHIVE_INFO_COMMENT => $archiveComment,
      self::ARCHIVE_INFO_DEFAULT_MOUNT_POINT => self::getArchiveFolderName($this->fileNode->getName()),
      self::ARCHIVE_INFO_COMMON_PATH_PREFIX => $this->getCommonDirectoryPrefix(),
      self::ARCHIVE_INFO_BACKEND_DRIVER => $this->getClassBaseName($this->archiver->getDriverType()),
    ]);

    $this->restoreProcessEnvironment();

    return $this->archiveInfo;
  }

  /**
   * Return a proposal for the extraction destination. Currently, this simply
   * strips double extensions like FOO.tag.N -> FOO.
   *
   * @param string $archiveFileName
   *
   * @return string
   */
  public static function getArchiveFolderName(string $archiveFileName):?string
  {
    // double to account for "nested" archive types
    $archiveFolderName = pathinfo($archiveFileName, PATHINFO_FILENAME);
    $secondExtension = pathinfo($archiveFolderName, PATHINFO_EXTENSION);

    // as a rule of thumb we only strip the second extension if it contains no
    // spaces and is no longer that 4 characters.
    if (strlen($secondExtension) <= 4 && str_replace(' ', '', $secondExtension) === $secondExtension) {
      $archiveFolderName = pathinfo($archiveFolderName, PATHINFO_FILENAME);
    }

    return $archiveFolderName;
  }

  /**
   * Return the name of the top-level folder for the case that there is only a
   * single folder at folder nesting level 0.
   *
   * @return null|string
   */
  public function getCommonDirectoryPrefix(): ?string
  {
    return $this->getCommonPath(array_keys($this->getFiles()), leadingSlash: false);
  }

  /**
   * @return array<string, ArchiveEntry>
   */
  public function getFiles(): array
  {
    if (empty($this->archiver)) {
      throw new Exceptions\ArchiveNotOpenException(
        $this->t('There is no archive file associated with this archiver instance.'));
    }

    if ($this->archiveFiles !== null) {
      return $this->archiveFiles;
    }

    $this->setProcessEnvironment();

    $this->archiveFiles = [];
    foreach ($this->archiver->getFileNames() as $fileName) {
      $fileData = $this->archiver->getFileData($fileName);
      // work around a bug in UnifiedArchive
      if ($fileData->modificationTime instanceof DateTimeInterface) {
        $fileData->modificationTime = $fileData->modificationTime->getTimestamp();
      }
      $this->archiveFiles[$fileName] = $fileData;
    }

    $this->restoreProcessEnvironment();

    return $this->archiveFiles;
  }

  /**
   * Normalize the give file name using the current unicode normalization.
   *
   * @param string $entryName
   *
   * @return string
   *
   * @todo Make it more clever for special so-called operating systems.
   */
  private function normalizeEntryName(string $entryName): string
  {
    return Normalizer::normalize($entryName, $this->unicodeNormalization);
  }

  /**
   * @param string $fileName
   *
   * @param ?Throwable $previous
   *
   * @return void
   */
  private function throwCannotAccessContent(string $fileName, ?Throwable $previous = null): void
  {
    if ($this->isEncrypted($fileName)) {
      if (!empty($this->archivePassphrase)) {
        $driver = $this->archiver->getDriver();
        $driverAbilities = $driver->getFormatAbilities();
        if (in_array(DriverAbilities::OPEN_ENCRYPTED, $driverAbilities)) {
          $reason = $this->l->t('The archive entry "%1$s" of the archive "%2$s" is encrypted but given passphrase may be wrong.', [
            $fileName,
            $this->fileNode->getPath(),
          ]);
        } else {
          $driverClass = get_class($driver);
          $driverClass = substr($driverClass, strrpos($driverClass, '\\') + 1);
          $reason = $this->l->t('The archive entry "%1$s" of the archive "%2$s" is encrypted but the backend-driver "%3$s" does not support decryption.', [
            $fileName,
            $this->fileNode->getPath(),
            $driverClass,
          ]);
        }
      } else {
        $reason = $this->l->t('The archive entry "%1$s" of the archive "%2$s" is encrypted but the decryption passphrase is not known.', [
          $fileName,
          $this->fileNode->getPath(),
        ]);
      }
      throw new Exceptions\ArchivePasswordRequiredException(
        $this->l->t(
          'Could not access file "%1$s" of archive "%2$s". Reason: %3$s',
          [
            $fileName,
            $this->fileNode->getPath(),
            $reason,
          ],
        ),
        0,
        $previous,
        fileName: $fileName,
        archivePath: $this->fileNode->getPath(),
      );
    } else {
      throw new Exceptions\ArchiveCannotAccessContentException(
        $this->l->t('Could not access file "%1$s" of archive "%2$s".'),
        0,
        $previous,
        fileName: $fileName,
        archivePath: $this->fileNode->getPath(),
      );
    }
  }

  /**
   * @param string $fileName
   *
   * @return null|string
   *
   * @throws ArchiveCannotAccessContentException
   */
  public function getFileContent(string $fileName):?string
  {
    if (empty($this->archiver)) {
      throw new Exceptions\ArchiveNotOpenException(
        $this->t('There is no archive file associated with this archiver instance.'));
    }

    $this->setProcessEnvironment();

    try {
      $result = $this->archiver->getFileContent($this->resolveMemberName($fileName));
    } catch (Throwable $t) {

      $this->restoreProcessEnvironment();

      // if ($this->isEncrypted($fileName) && $this->pas

      $this->throwCannotAccessContent($fileName, $t);
    }

    $this->restoreProcessEnvironment();

    return $result;
  }

  /**
   * @param string $fileName
   *
   * @return null|resource
   *
   * @throws ArchiveCannotAccessContentException
   */
  public function getFileStream(string $fileName)
  {
    if (empty($this->archiver)) {
      throw new Exceptions\ArchiveNotOpenException(
        $this->t('There is no archive file associated with this archiver instance.'));
    }

    $this->setProcessEnvironment();

    $t = null;
    try {
      $result = $this->archiver->getFileStream($this->resolveMemberName($fileName));
    } catch (Throwable $t) {
      $result = false;
    }

    $this->restoreProcessEnvironment();

    if ($result === false) {
      $this->throwCannotAccessContent($fileName, $t);
    }

    return $result;
  }

  /**
   * Resolve a member name as requested by the virtual storage (normalized to
   * NFC by Nextcloud) back to the raw member name as actually stored in the
   * backend archive.
   *
   * @param string $fileName
   *
   * @return string
   */
  private function resolveMemberName(string $fileName):string
  {
    $key = Normalizer::normalize($fileName, Normalizer::NFC);
    if ($key === false) {
      $key = $fileName;
    }
    return $this->memberNameMap[$key] ?? $fileName;
  }

  /**
   * Return the groups of archive members which collapse onto the same name
   * after Unicode (NFC) normalization. The array is keyed by the colliding
   * normalized name, the values are the lists of raw member names. Only one
   * member of each group can be extracted as a distinct file; the callers
   * should report the others instead of dropping them silently.
   *
   * @return array<string, string[]>
   */
  public function getCollidingMembers():array
  {
    if (empty($this->archiver)) {
      throw new Exceptions\ArchiveNotOpenException(
        $this->t('There is no archive file associated with this archiver instance.'));
    }
    return $this->collidingMembers;
  }

  /**
   * Enforece UTF-8 locale in the enviroment as otherwise some external
   * helpers do not function correctly.
   *
   * @return void
   *
   * @SuppressWarnings(PHPMD.Superglobals)
   */
  protected function setProcessEnvironment():void
  {
    foreach (self::OVERRIDE_ENVIRONMENT as $key => $value) {
      $this->savedProcessEnvironment[$key] = $_ENV[$key] ?? null;
      $_ENV[$key] = $value;
    }
  }

  /**
   * Restore the environment variables previously overridden by
   * setProcessEnvironment().
   *
   * @return void
   *
   * @SuppressWarnings(PHPMD.Superglobals)
   */
  protected function restoreProcessEnvironment():void
  {
    foreach (array_keys(self::OVERRIDE_ENVIRONMENT) as $key) {
      if (!isset($this->savedProcessEnvironment[$key])) {
        continue;
      }
      if ($this->savedProcessEnvironment[$key] === null) {
        unset($_ENV[$key]);
      } else {
        $_ENV[$key] = $this->savedProcessEnvironment[$key];
      }
    }
  }

  /**
   * Determine whether the current archive is encrypted, or if a particular
   * archive entry is encrypted. Return \null if this information is not available.
   *
   * @param ?string $entryName
   *
   * @return bool
   */
  protected function isEncrypted(?string $entryName = null): ?bool
  {
    if (empty($this->archiver)) {
      throw new Exceptions\ArchiveNotOpenException(
        $this->t('There is no archive file associated with this archiver instance.'));
    }

    $format = $this->archiver->getFormat();
    switch ($format) {
      case ArchiveFormats::ZIP:
        $localPath = self::getLocalPath($this->fileNode);
        $zip = new ZipArchive;
        if ($zip->open($localPath) === false) {
          return null;
        }
        if ($zip->numFiles == 0) {
          return false; // no files, no encryption
        }
        if ($entryName === null) {
          $stat = $zip->statIndex(0);
        } else {
          $stat = $zip->statName($this->normalizeEntryName($entryName));
        }
        if (($stat['encryption_method'] ?? 0) != 0) {
          return true;
        }
        return false; // bogus answer for ZIP-archives containing both encrypted and unencrypted data.
      case ArchiveFormats::
        RAR:\RarException::setUsingExceptions(\true);
        $localPath = self::getLocalPath($this->fileNode);
        $rar = RarArchive::open($localPath);
        // detect encryption of even the archive structure
        try {
          $entries = $rar->getEntries();
          foreach ($entries as $entry) {
            if ($entry->isDirectory()) {
              continue;
            }
            // if we reach here then the archive structure was not encrypted,
            // but the contents may ... as with zip we only cope with fully
            // entryped archives, so we break after the first successful
            // getStream().
            $entry->getStream();
            break;
          }
        } catch (RarException $e) {
          if (str_contains($e->getMessage(), 'ERAR_MISSING_PASSWORD')) {
            return true;
          }
        }
        return null;
      default:
        return false;
    }
  }
}
