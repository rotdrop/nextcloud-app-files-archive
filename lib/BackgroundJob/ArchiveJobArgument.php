<?php
/**
 * Recursive PDF Downloader App for Nextcloud
 *
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2026 Claus-Justus Heine <himself@claus-justus-heine.de>
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

namespace OCA\FilesArchive\BackgroundJob;

use Spatie\TypeScriptTransformer\Attributes as TSAttributes;

use OCA\FilesArchive\Toolkit\DTO\AbstractResponseDTO;

/**
 * A DTO which models the archive job argument.
 */
class ArchiveJobArgument extends AbstractResponseDTO
{
  public const TARGET_MOUNT = 'mount';
  public const TARGET_EXTRACT = 'extract';

  /** {@inheritdoc} */
  public function __construct(
    #[TSAttributes\LiteralTypeScriptType("'" . self::TARGET_MOUNT . "'|'" . self::TARGET_EXTRACT . "'")]
    public readonly string $target,
    public readonly string $userId,
    public readonly string $sourcePath,
    public readonly string $sourceId, // not int to prevent overflow in the frontend
    public readonly string $destinationPath,
    public readonly ?string $archivePassPhrase,
    public readonly bool $stripCommonPathPrefix,
    public readonly bool $needsAuthentication = false,
    public readonly ?string $authToken = null,
  ) {
  }

  /**
   * Initialize from the given array.
   *
   * @param array $data
   *
   * @return self
   *
   * @SuppressWarnings(PHPMD.UndefinedVariable)
   * @SuppressWarnings(PHPMD.UnusedLocalVariable)
   */
  public static function fromArray(array $data): self
  {
    static::initKeys();
    extract(array_intersect_key($data, array_flip(static::$keys[__CLASS__])));

    return new self(
      target: $target,
      userId: $userId,
      sourcePath: $sourcePath,
      sourceId: $sourceId,
      destinationPath: $destinationPath,
      archivePassPhrase: $archivePassPhrase ?? null,
      stripCommonPathPrefix: $stripCommonPathPrefix,
      needsAuthentication: $needsAuthentication ?? false,
      authToken: $authToken ?? null,
    );
  }
}
