<?php
/**
 * Archive Manager for Nextcloud
 *
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022 Claus-Justus Heine <himself@claus-justus-heine.de>
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

namespace OCA\FilesArchive\Traits;

use \ReflectionClass;

use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\ContentSecurityPolicy;

/**
 * Utility class to ease constructing HTTP responses.
 */
trait ResponseTrait
{
  use UtilTrait;

  private function dataDownloadResponse($data, $fileName, $contentType)
  {
    $response = new Http\DataDownloadResponse($data, $fileName, $contentType);
    $response->addHeader(
      'Content-Disposition',
      'attachment; '
      . 'filename="' . $this->transliterate($fileName) . '"; '
      . 'filename*=UTF-8\'\'' . rawurlencode($fileName));

    return $response;
  }

  private function exceptionResponse(\Throwable $throwable, string $renderAs, string $method = null)
  {
    if (empty($method)) {
      $method = __METHOD__;
    }
    $this->logException($throwable, $method);
    if ($renderAs == 'blank') {
      return self::grumble($this->exceptionChainData($throwable));
    }

    $templateParameters = [
      'error' => 'exception',
      'exception' => $throwable->getMessage(),
      'code' => $throwable->getCode(),
      'trace' => $this->exceptionChainData($throwable),
      'debug' => true,
      'admin' => 'bofh@nowhere.com',
    ];

    return new TemplateResponse($this->appName, 'errorpage', $templateParameters, $renderAs);
  }

  private function exceptionChainData(\Throwable $throwable, bool $top = true)
  {
    $previous = $throwable->getPrevious();
    $shortException = (new ReflectionClass($throwable))->getShortName();
    return [
      'message' => ($top
                    ? $this->l->t('Error, caught an exception.')
                    : $this->l->t('Caused by previous exception')),
      'exception' => $throwable->getFile().':'.$throwable->getLine().' '.$shortException.': '.$throwable->getMessage(),
      'code' => $throwable->getCode(),
      'trace' => $throwable->getTraceAsString(),
      'previous' => empty($previous) ? null : $this->exceptionChainData($previous, false),
    ];
  }

  private static function dataResponse($data, $status = Http::STATUS_OK)
  {
    $response = new DataResponse($data, $status);
    $policy = $response->getContentSecurityPolicy();
    $policy->addAllowedFrameAncestorDomain("'self'");
    return $response;
  }

  private static function valueResponse($value, $message = '', $status = Http::STATUS_OK)
  {
    return self::dataResponse(['messages' => [ $message ], 'value' => $value], $status);
  }

  private static function response($message, $status = Http::STATUS_OK)
  {
    return self::dataResponse(['messages' => [ $message ] ], $status);
  }

  private static function grumble($message, $value = null, $status = Http::STATUS_BAD_REQUEST)
  {
    $trace = debug_backtrace();
    $caller = array_shift($trace);
    $data = [
      'class' => __CLASS__,
      'file' => $caller['file'],
      'line' => $caller['line'],
      'value' => $value,
    ];
    if (is_array($message)) {
      $data = array_merge($data, $message);
    } else {
      $data['messages'] = [ $message ];
    }
    return self::dataResponse($data, $status);
  }
}

// Local Variables: ***
// c-basic-offset: 2 ***
// indent-tabs-mode: nil ***
// End: ***
