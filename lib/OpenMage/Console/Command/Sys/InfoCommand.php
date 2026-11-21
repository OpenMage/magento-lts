<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Console
 */

declare(strict_types=1);

namespace OpenMage\Console\Command\Sys;

use Exception;
use FilesystemIterator;
use Override;
use Mage;
use OpenMage\Console\Command\AbstractCommand;
use OpenMage\Console\Output\TableRenderer;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Ported from the sys:info command of n98-magerun (https://github.com/netz98/n98-magerun), with the same name, argument and options
 *
 * n98-magerun: Copyright (c) netz98 GmbH, MIT license, see lib/OpenMage/Console/LICENSE-n98-magerun.txt
 */
#[AsCommand(name: 'sys:info', description: 'Print information about the OpenMage installation')]
final class InfoCommand extends AbstractCommand
{
    protected bool $requiresInstall = false;

    #[Override]
    protected function configure(): void
    {
        $this
            ->addArgument('key', InputArgument::OPTIONAL, 'Only print the value of this key, e.g. version or magento_version (case-insensitive)')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, TableRenderer::OPTION_DESCRIPTION, 'table')
            ->setHelp('Prints the version, the paths and the code pools, and the entity counts once OpenMage is installed.'
                . PHP_EOL . 'With a key, prints the raw value only and ignores --format.'
                . PHP_EOL . 'The encryption key is never printed.');
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $format = TableRenderer::checkFormat($input->getOption('format'));
        $infos = $this->collectInfos($output);

        $key = $input->getArgument('key');
        if (is_string($key)) {
            $values = array_change_key_case($infos, CASE_LOWER);
            if (!array_key_exists(strtolower($key), $values)) {
                $this->writeError($output, sprintf('<error>Unknown key: %s. Known keys: %s.</error>', $key, implode(', ', array_keys($infos))));

                return Command::FAILURE;
            }

            $output->writeln((string) $values[strtolower($key)], OutputInterface::OUTPUT_RAW);

            return Command::SUCCESS;
        }

        $rows = [];
        foreach ($infos as $name => $value) {
            $rows[] = [$name, $value];
        }

        (new TableRenderer())->render($output, ['name', 'value'], $rows, $format);

        return Command::SUCCESS;
    }

    /**
     * @return array<string, int|string>
     */
    private function collectInfos(OutputInterface $output): array
    {
        $config = Mage::app()->getConfig();
        $options = $config->getOptions();
        $installed = Mage::isInstalled();
        $cacheBackend = Mage::app()->getCache()->getBackend();

        $infos = [
            'version' => Mage::getOpenMageVersion(),
            'magento_version' => (string) Mage::getVersion(),
            'edition' => (string) Mage::getEdition(),
            'root' => (string) Mage::getBaseDir(),
            'installed' => $installed ? 'yes' : 'no',
            'var_directory' => (string) $options->getVarDir(),
            'cache_directory' => (string) $options->getCacheDir(),
            'media_directory' => (string) $options->getMediaDir(),
            'cache_backend' => $cacheBackend === null ? '' : $cacheBackend::class,
        ];

        if ($installed) {
            $infos['install_date'] = (string) $config->getNode('global/install/date');
            $infos['session'] = (string) $config->getNode('global/session_save');
        }

        $infos += $this->codePoolInfos();

        if ($installed) {
            try {
                $infos['attribute_count'] = (int) Mage::getResourceModel('eav/entity_attribute_collection')->getSize();
                $infos['customer_count'] = (int) Mage::getResourceModel('customer/customer_collection')->getSize();
                $infos['category_count'] = (int) Mage::getResourceModel('catalog/category_collection')->getSize();
                $infos['product_count'] = (int) Mage::getResourceModel('catalog/product_collection')->getSize();
            } catch (Exception $exception) {
                $this->writeError($output, sprintf('<error>%s</error>', $exception->getMessage()));
            }
        }

        return $infos;
    }

    /**
     * Counts the files overriding core classes through the include path, and lists the vendor folders of each code pool
     *
     * @return array<string, int|string>
     */
    private function codePoolInfos(): array
    {
        $codeDir = Mage::getBaseDir('code');
        $infos = [];

        $overrideDirs = array_filter(
            [$codeDir . '/local/Mage', $codeDir . '/community/Mage'],
            is_dir(...),
        );
        if ($overrideDirs !== []) {
            $count = 0;
            foreach ($overrideDirs as $dir) {
                $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
                $count += iterator_count($files);
            }

            $infos['core_autoloader_overwrites'] = $count;
        }

        foreach (['core', 'community', 'local'] as $pool) {
            $poolDir = $codeDir . '/' . $pool;
            if (!is_dir($poolDir)) {
                continue;
            }

            $dirs = glob($poolDir . '/*', GLOB_ONLYDIR);
            $vendors = array_map(basename(...), is_array($dirs) ? $dirs : []);
            sort($vendors);
            $infos['vendors_' . $pool] = implode(', ', $vendors);
        }

        return $infos;
    }
}
