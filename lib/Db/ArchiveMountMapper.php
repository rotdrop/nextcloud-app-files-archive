<?php
/**
 * @author    Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022 Claus-Justus Heine
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
use OCP\AppFramework\Db\QBMapper;

use OCP\Files\File;
use OCP\Files\Folder;
use OCP\Files\FileInfo;

/**
 * @extends QBMapper<Note>
 */
class ArchiveMountMapper extends QBMapper
{
  /**
   * @param IDBConnection $dbConnection
   */
  public function __construct(IDBConnection $dbConnection)
  {
    parent::__construct($dbConnection, 'files_archive_mounts', ArchiveMount::class);
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
   * Find a mount by the path of the underlying archive file.
   *
   * @param string $archivePath
   *
   * @return array
   */
  public function findByArchivePath(string $archivePath):array
  {
    $qb = $this->db->getQueryBuilder();

    $qb->select('*')
      ->from($this->getTableName())
      ->where($qb->expr()->eq('archive_file_path_hash', $qb->createNamedParameter(md5($archivePath))));

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
          $qb->createNamedParamter($userId)));

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
          $qb->createNamedParamter($userId)));

    return $this->findEntity($qb);
  }
}
