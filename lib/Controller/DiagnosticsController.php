<?php
/**
 * Archive Manager for Nextcloud
 *
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2024 Claus-Justus Heine <himself@claus-justus-heine.de>
 * @license AGPL-3.0-or-later
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *"
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace OCA\FilesArchive\Controller;

use Throwable;

use wapmorgan\UnifiedArchive\Commands as ArchiveCommands;

use SensioLabs\AnsiConverter;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper as ConsoleHelper;

use Psr\Log\LoggerInterface;
use OCP\IRequest;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IL10N;

/**
 * AJAX endpoint for diagnostics, currently the installation status of the
 * UnifiedArchive backend is provided.
 */
class DiagnosticsController extends Controller
{
  use \OCA\FilesArchive\Toolkit\Traits\ResponseTrait;
  use \OCA\FilesArchive\Toolkit\Traits\LoggerTrait;

  // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
  public function __construct(
    ?string $appName,
    IRequest $request,
    protected LoggerInterface $logger,
    protected IL10N $l,
  ) {
    parent::__construct($appName, $request);
  }
  // phpcs:enable

  /**
   * Return the information of the ArchiveCommands\FormatsCommand
   *
   * @return DataResponse
   *
   * @AuthorizedAdminSetting(settings=OCA\FilesArchive\Settings\Admin)
   */
  public function archiveFormats():DataResponse
  {
    return self::dataResponse(
      $this->runArchiveCommand(ArchiveCommands\FormatsCommand::class, [ 'driver' => null, ])
    );
  }

  /**
   * Return the information of the ArchiveCommands\DriversCommand
   *
   * @return DataResponse
   *
   * @AuthorizedAdminSetting(settings=OCA\FilesArchive\Settings\Admin)
   */
  public function archiveDrivers():DataResponse
  {
    return self::dataResponse(
      $this->runArchiveCommand(ArchiveCommands\DriversCommand::class),
    );
  }

  /**
   * Return the information of the ArchiveCommands\FormatCommand
   *
   * @param string $format
   *
   * @return DataResponse
   *
   * @AuthorizedAdminSetting(settings=OCA\FilesArchive\Settings\Admin)
   */
  public function archiveFormat(string $format):DataResponse
  {
    return self::dataResponse(
      $this->runArchiveCommand(ArchiveCommands\FormatCommand::class, [ 'format' => $format ])
    );
  }

  /**
   * @param string $commandClass Class name of the command to run.
   *
   * @param array $arguments The arguments to pass to the command.
   *
   * @return array HTML and CSS output.
   */
  private function runArchiveCommand(string $commandClass, array $arguments = []):array
  {
    $helperSet = new ConsoleHelper\HelperSet([
      new ConsoleHelper\FormatterHelper(),
    ]);
    $inputArguments = [];
    foreach ($arguments as $key => $value) {
      $inputArguments[$key] = new InputArgument(name: $key, default: $value);
    }
    $inputDefinition = new InputDefinition($inputArguments);
    $input = new ArrayInput($arguments, $inputDefinition);
    $output = new BufferedOutput(
      verbosity: OutputInterface::VERBOSITY_NORMAL,
      decorated: true,
    );
    $command = new $commandClass();
    $command->setHelperSet($helperSet);
    $command->execute(input: $input, output: $output);

    $content = $output->fetch();
    $lightTheme = new class extends AnsiConverter\Theme\SolarizedTheme {
      /** {@inheritdoc} */
      public function asArray()
      {
        $darkSolarized = parent::asArray();
        // swap the base colors, see https://ethanschoonover.com/solarized/
        return array_merge(
          $darkSolarized,
          [
            // base03 <-> base3
            'brblack' => $darkSolarized['brwhite'],
            'brwhite' => $darkSolarized['brblack'],
            // base02 <-> base2
            'black' => $darkSolarized['white'],
            'white' => $darkSolarized['black'],
            // base01 <-> base1
            'brgreen' => $darkSolarized['brcyan'],
            'brcyan' => $darkSolarized['brgreen'],
            // base00 <-> base0
            'bryellow' => $darkSolarized['brblue'],
            'brblue' => $darkSolarized['bryellow'],
          ]
        );
      }
    };
    $darkTheme = new AnsiConverter\Theme\SolarizedTheme;
    $converter = new AnsiConverter\AnsiToHtmlConverter(inlineStyles: false);
    $html = $converter->convert($content);


    return [
      'html' => $html,
      'css' => [
        'light' => $lightTheme->asCss(),
        'dark' => $darkTheme->asCss(),
      ]
    ];
  }
}
