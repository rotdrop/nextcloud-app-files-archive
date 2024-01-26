<?php
/**
 * @author    Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022, 2024 Claus-Justus Heine
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

namespace OCA\FilesArchive\Db;

use OCP\IDBConnection;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\Security\ICrypto;

use OCP\Files\File;
use OCP\Files\Folder;
use OCP\Files\FileInfo;

/**
 * @extends QBMapper<Note>
 */
class ArchiveMountMapper extends QBMapper
{
  /** @var ICrypto */
  private $cryptor;

  /**
   * @param IDBConnection $dbConnection
   *
   * @param ICrypto $cloudCryptor
   */
  public function __construct(
    IDBConnection $dbConnection,
    ICrypto $cloudCryptor,
  ) {
    parent::__construct($dbConnection, 'files_archive_mounts', ArchiveMount::class);
    $this->cryptor = $cloudCryptor;
  }

  /**
   * Find all archive-mounts of the given user.
   *
   * @param string $userId
   *
   * @return array
   */
  public function findAll(string $userId):array
  {
    $qb = $this->db->getQueryBuilder();

    $qb->select('*')
      ->from($this->getTableName())
      ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));

    return $this->findEntities($qb);
  }

  /**
   * Find a mount by its mount-path.
   *
   * @param string $mountPath
   *
   * @return null|ArchiveMount
   */
  public function findByMountPath(string $mountPath):?ArchiveMount
  {
    $qb = $this->db->getQueryBuilder();

    $qb->select('*')
      ->from($this->getTableName())
      ->where($qb->expr()->eq('mount_point_path_hash', $qb->createNamedParameter(md5($mountPath))));

    return $this->findEntity($qb);
  }

  /**
   * Find a mount by its mount-path.
   *
   * @param int $mountPointFileId File id of the mount-point.
   *
   * @return null|ArchiveMount
   */
  public function findByMountPointFileId(int $mountPointFileId):?ArchiveMount
  {
    $qb = $this->db->getQueryBuilder();

    $qb->select('*')
      ->from($this->getTableName())
      ->where($qb->expr()->eq('mount_point_file_id', $qb->createNamedParameter($mountPointFileId)));

    return $this->findEntity($qb);
  }

  /**
   * Find a mount by its mount-path.
   *
   * @param Folder $mountPointFolder
   *
   * @return null|ArchiveMount
   */
  public function findByMountPointFolder(Folder $mountPointFolder):?ArchiveMount
  {
    $qb = $this->db->getQueryBuilder();

    $qb->select('*')
      ->from($this->getTableName())
      ->where($qb->expr()->eq('mount_point_file_id', $qb->createNamedParameter($mountPointFolder->getId())));

    return $this->findEntity($qb);
  }

  /**
   * Find a mount by the path of the underlying archive file.
   *
   * @param string $userId
   *
   * @param string $archivePath
   *
   * @return array
   */
  public function findByArchivePath(string $userId, string $archivePath):array
  {
    $qb = $this->db->getQueryBuilder();

    $qb->select('*')
      ->from($this->getTableName())
      ->where($qb->expr()->eq('archive_file_path_hash', $qb->createNamedParameter(md5($archivePath))))
      ->andWhere(
        $qb->expr()->eq(
          'user_id',
          $qb->createNamedParameter($userId)));

    return $this->findEntities($qb);
  }

  /**
   * Find all mounts for the given archive file and user
   *
   * @param string $userId
   *
   * @param File $archiveFile
   *
   * @return array
   */
  public function findByArchiveFile(string $userId, File $archiveFile):array
  {
    $qb = $this->db->getQueryBuilder();

    $qb->select('*')
      ->from($this->getTableName())
      ->where(
        $qb->expr()->eq(
          'archive_file_id',
          $qb->createNamedParameter($archiveFile->getId())))
      ->andWhere(
        $qb->expr()->eq(
          'user_id',
          $qb->createNamedParameter($userId)));

    return $this->findEntities($qb);
  }

  /**
   * Find all mounts for the given archive file and user
   *
   * @param string $userId
   *
   * @param Folder $mountPoint
   *
   * @return array
   */
  public function findByMountPoint(string $userId, Folder $mountPoint):?ArchiveMount
  {
    $qb = $this->db->getQueryBuilder();

    $qb->select('*')
      ->from($this->getTableName())
      ->where(
        $qb->expr()->eq(
          'archive_file_id',
          $qb->createNamedParameter($mountPoint->getId())))
      ->andWhere(
        $qb->expr()->eq(
          'user_id',
          $qb->createNamedParameter($userId)));

    return $this->findEntity($qb);
  }

  /**
   * @param ArchiveMount $entity
   *
   * @return ArchiveMount
   */
  private function decodeEntity(ArchiveMount $entity):ArchiveMount
  {
    $archivePassPhrase = $entity->getArchivePassPhrase();
    if (empty($archivePassPhrase)) {
      $archivePassPhrase = null;
    } else {
      $archivePassPhrase = $this->cryptor->decrypt($archivePassPhrase);
    }
    $entity->setArchivePassPhrase($archivePassPhrase);

    $entity->setMountFlags(ArchiveMount::MOUNT_FLAGS_MASK & $entity->getMountFlags());
    $entity->setArchiveFilePathHash(md5($entity->getArchiveFilePath()));
    $entity->setMountPointPathHash(md5($entity->getMountPointPath()));

    return $entity;
  }

  /**
   * @param ArchiveMount $entity
   *
   * @return ArchiveMount
   */
  private function encodeEntity(ArchiveMount $entity):ArchiveMount
  {
    $archivePassPhrase = $entity->getArchivePassPhrase();
    if (empty($archivePassPhrase)) {
      $archivePassPhrase = null;
    } else {
      $archivePassPhrase = $this->cryptor->encrypt($archivePassPhrase);
    }
    $entity->setArchivePassPhrase($archivePassPhrase);

    $entity->setMountFlags(ArchiveMount::MOUNT_FLAGS_MASK & $entity->getMountFlags());
    $entity->setArchiveFilePathHash(md5($entity->getArchiveFilePath()));
    $entity->setMountPointPathHash(md5($entity->getMountPointPath()));

    return $entity;
  }

  /** {@inheritdoc} */
  public function insert(Entity $entity):Entity
  {
    $this->encodeEntity($entity);
    $result = parent::insert($entity);
    $this->decodeEntity($result);
    return $result;
  }

  /** {@inheritdoc} */
  public function update(Entity $entity):Entity
  {
    $this->encodeEntity($entity);
    $result = parent::update($entity);
    $this->decodeEntity($result);
    return $result;
  }

  /** {@inheritdoc} */
  protected function mapRowToEntity(array $row):Entity
  {
    $entity = parent::mapRowToEntity($row);
    return $this->decodeEntity($entity);
  }
}
