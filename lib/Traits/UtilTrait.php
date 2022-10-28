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

use OCP\IL10N;

use \NumberFormatter;

/**
 * Trait for some general nice-to-have functions.
 */
trait UtilTrait
{
  /** @var IL10N */
  private $l;

  /**
   * Take any dashed or "underscored" lower-case string and convert to
   * camel-case.
   *
   * @param string $string the string to convert.
   *
   * @param bool $capitalizeFirstCharacter self explaining.
   *
   * @param string $dashes Characters to replace.
   *
   * @return string
   */
  protected static function dashesToCamelCase(string $string, bool $capitalizeFirstCharacter = false, string $dashes = '_-'):string
  {
    $str = str_replace(str_split($dashes), '', ucwords($string, $dashes));

    if (!$capitalizeFirstCharacter) {
      $str[0] = strtolower($str[0]);
    }

    return $str;
  }

  /**
   * Take an camel-case string and convert to lower-case with dashes
   * or underscores between the words. First letter may or may not
   * be upper case.
   *
   * @param string $string String to work on.
   *
   * @param string $separator Separator to use, defaults to '-'.
   *
   * @return string
   */
  protected static function camelCaseToDashes(string $string, string $separator = '-'):string
  {
    return strtolower(preg_replace('/([A-Z])/', $separator.'$1', lcfirst($string)));
  }

  /**
   * Return the locale as string, e.g. de_DE.UTF-8.
   *
   * @return string
   */
  protected function getLocale():string
  {
    $locale = $this->l->getLocaleCode();
    $primary = locale_get_primary_language($locale);
    if ($primary == $locale) {
      $locale = $locale.'_'.strtoupper($locale);
    }
    if (strpos($locale, '.') === false) {
      $locale .= '.UTF-8';
    }
    return $locale;
  }

  /**
   * Transliterate the given string to the given or default locale.
   *
   * @param string $string
   *
   * @param string $locale
   *
   * @return string
   */
  protected function transliterate(string $string, string $locale = null):string
  {
    $oldlocale = setlocale(LC_CTYPE, '0');
    empty($locale) && $locale = $this->getLocale();
    setlocale(LC_CTYPE, $locale);
    $result = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
    setlocale(LC_CTYPE, $oldlocale);
    return $result;
  }

  /**
   * Try to parse a floating point value.
   *
   * @param string $value Input value. Maybe a percentage.
   *
   * @param null|string $locale
   *
   * @return bool|float
   */
  public function floatValue(string $value, ?string $locale = null)
  {
    $amount = preg_replace('/\s+/u', '', $value);
    empty($locale) && $locale = $this->getLocale();
    $locales = [ $locale, 'en_US_POSIX' ];
    $parsed = false;
    foreach ($locales as $locale) {
      $fmt = new NumberFormatter($locale, \NumberFormatter::DECIMAL);

      $decimalSeparator = $fmt->getSymbol(\NumberFormatter::DECIMAL_SEPARATOR_SYMBOL);
      $groupingSeparator = $fmt->getSymbol(\NumberFormatter::GROUPING_SEPARATOR_SYMBOL);

      $decPos = strpos($amount, $decimalSeparator);
      $grpPos = strpos($amount, $groupingSeparator);

      if ($grpPos !== false && $decPos === false) {
        // unlikely: 1,000, we assume 1,000.00 would be used
        continue;
      } elseif ($decPos < $grpPos) {
        // unlikely: 1.000,00 in en_US
        continue;
      }

      $parsed = $fmt->parse($amount);
      if ($parsed !== false) {
        $percent = $fmt->getSymbol(\NumberFormatter::PERCENT_SYMBOL);
        if (preg_match('/'.$percent.'/u', $amount)) {
            $parsed /= 100.0;
        }
        break;
      }
    }
    return $parsed !== false ? (float)$parsed : $parsed;
  }

  /**
   * Parse a storage user input value and return its value in bytes.
   *
   * @param null|string $value Input value.
   *
   * @param null|string $locale Locale or null for the user's default locale.
   *
   * @return int
   *
   * - if the passed value is null or the empty string then the function
   * returns null.
   * - if otherwise an error occurs during parsing, null is returned
   * - otherwise the storage value in bytes is returned
   */
  public function storageValue(?string $value, ?string $locale = null)
  {
    if ($value === null || $value === '') {
      return null;
    }
    $factor = [
      'b' => 1,
      'kb' => 1000, 'kib' => (1 << 10),
      'mb' => 1000000, 'mib' => (1 << 20),
      'gb' => 1000000000, 'gib' => (1 << 30),
      'tb' => 1000000000000, 'tib' => (1 << 40),
      'pb' => 1000000000000000, 'pib' => (1 << 50),
    ];
    $value = preg_replace('/\s+/u', '', $value);
    $value = strtolower(
      str_ireplace(
        [ 'bytes', 'kilo', 'kibi', 'mega', 'mebi', 'giga', 'gibi', 'tera', 'tibi', 'peta', 'pebi' ],
        [ 'b', 'k', 'ki', 'm', 'mi', 'g', 'gi', 't', 'ti', 'p', 'pi' ],
        $value));

    if (preg_match('/([-0-9,.]+)([kmgtp]?i?b?)?$/', $value, $matches)) {
      $this->logInfo('MATCHES ' . print_r($matches, true));
      $value = $this->floatValue($matches[1], $locale);
      if (empty($value)) {
        return null;
      }
      if (!empty($matches[2])) {
        if (empty($factor[$matches[2]])) {
          return null;
        }
        $value *= $factor[$matches[2]];
      }
      return $value;
    } else {
      return null;
    }
  }

  /**
   * Unset all array elements with value $value.
   *
   * @param array $hayStack The array to modify.
   *
   * @param mixed $value The value to remove.
   *
   * @return int The number of array slots that have been unset. As
   * the values need not be unique this can be any non-negative
   * integer.
   */
  protected static function unsetValue(array &$hayStack, mixed $value):int
  {
    $numUnset = 0;
    while (($key = array_search($value, $hayStack)) !== false) {
      unset($hayStack[$key]);
      ++$numUnset;
    }
    return $numUnset;
  }
}
