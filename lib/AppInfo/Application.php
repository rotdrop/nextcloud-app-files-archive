<?php
/**
 * Archive Manager for Nextcloud
 *
 * @author    Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022, 2024, 2026 Claus-Justus Heine <himself@claus-justus-heine.de>
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

namespace OCA\FilesArchive\AppInfo;

use Exception;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\Files\Config\IMountProviderCollection;
use OCP\IConfig;
use Psr\Container\ContainerInterface;

use OCA\FilesArchive\Listener\Registration as ListenerRegistration;
use OCA\FilesArchive\Mount\MountProvider as ArchiveMountProvider;
use OCA\FilesArchive\Notification\Notifier;
use OCA\FilesArchive\Service\MimeTypeService;
use OCA\FilesArchive\Toolkit\AppInfo\AbstractApplication;
use OCA\FilesArchive\Toolkit\Middleware\ExceptionMiddleware;

include_once __DIR__ . '/../Toolkit/AppInfo/AbstractApplication.php';

/**
 * App entry point.
 */
class Application extends AbstractApplication
{
  /**
   * Called later than "register".
   *
   * @param IBootContext $context
   *
   * @return void
   */
  public function boot(IBootContext $context): void
  {
    $context->injectFn(function(IMountProviderCollection $mountProviderCollection, ArchiveMountProvider $mountProvider) {
      $mountProviderCollection->registerProvider($mountProvider, PHP_INT_MAX - 1);
    });

    // Make the archive extension to MIME-type mappings known to the MIME-type
    // detector for every request, so that newly uploaded archives are detected
    // correctly without having to persist anything into config/.
    $context->injectFn(function(MimeTypeService $mimeTypeService) {
      $mimeTypeService->registerMimeTypeMappings();
    });
  }

  /**
   * Called earlier than boot, so anything initialized in the
   * "boot()" method must not be used here.
   *
   * @param IRegistrationContext $context
   *
   * @return void
   */
  public function register(IRegistrationContext $context): void
  {
    parent::register($context);
    if ((include_once __DIR__ . '/../../vendor-scoped/autoload.php') === false) {
      throw new Exception('Cannot include scoped autoload. The app has not been installed properly.');
    }

    // Register listeners
    ListenerRegistration::register($context);

    $context->registerMiddleWare(ExceptionMiddleware::class);
    $context->registerNotifierService(Notifier::class);
  }
}
