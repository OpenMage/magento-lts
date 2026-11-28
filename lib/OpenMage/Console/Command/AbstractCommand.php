<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Console
 */

declare(strict_types=1);

namespace OpenMage\Console\Command;

use Override;
use Mage;
use Mage_Core_Model_App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Zend_Db_Adapter_Exception;

/**
 * Base class for commands that need the OpenMage application: starts Mage::app() before execute()
 *
 * Commands that run without database or installation extend Symfony's Command directly.
 */
abstract class AbstractCommand extends Command
{
    /**
     * Event area loaded after start-up, e.g. Mage_Core_Model_App_Area::AREA_ADMINHTML or 'crontab'
     */
    protected ?string $eventArea = null;

    /**
     * Whether the command refuses to run before OpenMage is installed
     */
    protected bool $requiresInstall = true;

    public function __construct(?string $name = null)
    {
        parent::__construct($name);

        $this->addOption(
            'store',
            null,
            InputOption::VALUE_REQUIRED,
            'Code of the store the application is started in; it does not filter what a command reads or writes'
                . ' (config:* commands select a scope with --scope and --scope-id)',
            'admin',
        );
    }

    #[Override]
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        if ($this->requiresInstall && !Mage::isInstalled()) {
            throw new RuntimeException('OpenMage is not installed: app/etc/local.xml is missing or has no install date.');
        }

        $store = $input->getOption('store');
        $connectionError = null;
        try {
            $this->initApp(is_string($store) ? $store : 'admin');
        } catch (Zend_Db_Adapter_Exception $zendDbAdapterException) {
            $connectionError = $zendDbAdapterException->getMessage();
        }

        // Thrown without the driver exception: Symfony prints each previous exception with its file and line.
        if ($connectionError !== null) {
            throw new RuntimeException(sprintf(
                'Cannot connect to the database on host "%s" set in app/etc/local.xml: %s',
                $this->databaseHost(),
                $connectionError,
            ));
        }
    }

    /**
     * Mage::app() ignores the store code once the application is started.
     */
    protected function initApp(string $store): Mage_Core_Model_App
    {
        $app = Mage::app($store);
        if ($this->eventArea !== null) {
            $app->addEventArea($this->eventArea);
        }

        return $app;
    }

    /**
     * Writes a message to the error output; an output without one, as in CommandTester by default, receives it itself
     */
    protected function writeError(OutputInterface $output, string $message): void
    {
        $errorOutput = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
        $errorOutput->writeln($message);
    }

    /**
     * Mage::app() merges app/etc/local.xml into the configuration before it opens the first connection.
     */
    private function databaseHost(): string
    {
        $host = Mage::getConfig()?->getNode('global/resources/default_setup/connection/host');

        return $host === false || $host === null ? 'unknown' : (string) $host;
    }
}
