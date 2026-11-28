<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Console
 */

declare(strict_types=1);

namespace OpenMage\Console;

use Override;
use Mage;
use Mage_Core_Model_Config;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * OpenMage console application: discovers commands from module configuration without starting Mage::app()
 */
final class Application extends BaseApplication
{
    /**
     * @var list<string>
     */
    private readonly array $loaderErrors;

    public function __construct(CommandLoader $commandLoader)
    {
        parent::__construct('OpenMage', Mage::getOpenMageVersion());

        $this->setCommandLoader($commandLoader);
        $this->loaderErrors = $commandLoader->getErrors();
    }

    /**
     * Builds the application from the config.xml files of the active modules
     */
    public static function create(): self
    {
        return new self(CommandLoader::fromConfig(self::loadModulesConfig()));
    }

    /**
     * Reads app/etc and the config.xml files of the active modules, without database or local.xml
     *
     * Config::init() is avoided: its config cache check starts Mage::app().
     */
    public static function loadModulesConfig(): Mage_Core_Model_Config
    {
        Mage::setRoot();
        $config = new Mage_Core_Model_Config();
        $config->loadBase();
        $config->loadModules();

        return $config;
    }

    #[Override]
    public function doRun(InputInterface $input, OutputInterface $output): int
    {
        $errorOutput = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
        foreach ($this->loaderErrors as $error) {
            $errorOutput->writeln(sprintf('<comment>%s</comment>', $error));
        }

        return parent::doRun($input, $output);
    }

    #[Override]
    protected function getDefaultInputDefinition(): InputDefinition
    {
        $definition = parent::getDefaultInputDefinition();
        $definition->addOption(new InputOption(
            'root-dir',
            null,
            InputOption::VALUE_REQUIRED,
            'OpenMage root directory (read by bin/openmage before the application starts)',
        ));

        return $definition;
    }
}
