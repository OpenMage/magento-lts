<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Console
 */

declare(strict_types=1);

namespace OpenMage\Console\Command\Config;

use InvalidArgumentException;
use Override;
use Mage;
use Mage_Core_Model_Config_Data;
use Mage_Core_Model_Resource_Config_Data_Collection;
use OpenMage\Console\Output\TableRenderer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Ported from the config:get command of n98-magerun (https://github.com/netz98/n98-magerun)
 *
 * n98-magerun: Copyright (c) netz98 GmbH, MIT license, see lib/OpenMage/Console/LICENSE-n98-magerun.txt
 */
#[AsCommand(name: 'config:get', description: 'Print configuration values stored in the database')]
final class GetCommand extends AbstractConfigCommand
{
    /**
     * Block size in bytes of Blowfish, the cipher of Mage_Core_Model_Encryption
     */
    private const CIPHER_BLOCK_SIZE = 8;

    #[Override]
    protected function configure(): void
    {
        $this
            ->addArgument('path', InputArgument::OPTIONAL, 'Configuration path, may contain * wildcards')
            ->addOption('scope', null, InputOption::VALUE_REQUIRED, 'Only values of this scope: ' . implode(', ', self::SCOPES))
            ->addOption('scope-id', null, InputOption::VALUE_REQUIRED, 'Only values of this scope ID, or website or store code with --scope')
            ->addOption('decrypt', null, InputOption::VALUE_NONE, 'Decrypt the encrypted values with the encryption key of local.xml')
            ->addOption('update-script', null, InputOption::VALUE_NONE, 'Print the values as setConfigData() calls for a setup script')
            ->addOption('magerun-script', null, InputOption::VALUE_NONE, 'Print the values as config:set commands')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, TableRenderer::OPTION_DESCRIPTION, 'table')
            ->setHelp(<<<'HELP'
                Lists the values of the core_config_data table, every scope included; default values of the config.xml files are not listed.
                Without <info>path</info>, every value is listed. The <info>path</info> may contain wildcards (*),
                and a trailing slash lists the children: <info>config:get web/</info> is the same as <info>config:get 'web/*'</info>.

                With <info>--decrypt</info>, a value is decrypted when it has the form of an encrypted value and decrypts to readable text;
                the other values are printed as they are stored. With <info>--magerun-script</info>, the decrypted values get
                the <info>--encrypt</info> option of config:set.

                When no value matches, the command succeeds: the table format writes a notice to the error output,
                JSON prints <info>[]</info>, and CSV and the script options print nothing.
                HELP);
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $format = TableRenderer::checkFormat($input->getOption('format'));
        $path = $input->getArgument('path');
        $path = is_string($path) ? $path : '';

        /** @var Mage_Core_Model_Resource_Config_Data_Collection $collection */
        $collection = Mage::getResourceModel('core/config_data_collection');
        if ($path !== '') {
            $collection->addFieldToFilter('path', ['like' => $this->toLikePattern(str_ends_with($path, '/') ? $path . '*' : $path)]);
        }

        $scope = $input->getOption('scope');
        if ($scope !== null) {
            $scope = $this->checkScope($scope);
            $collection->addFieldToFilter('scope', $scope);
        }

        $scopeId = $input->getOption('scope-id');
        if (is_string($scopeId)) {
            if ($scope === null && preg_match('/^\d+$/', $scopeId) !== 1) {
                throw new InvalidArgumentException('A website or store code in --scope-id needs --scope.');
            }

            $collection->addFieldToFilter('scope_id', $scope === null ? (int) $scopeId : $this->resolveScopeId($scope, $scopeId, true));
        }

        $rows = $this->sortedRows($collection, $input->getOption('decrypt') === true);
        $asScript = $input->getOption('update-script') === true || $input->getOption('magerun-script') === true;
        if ($rows === [] && ($asScript || $format === 'table')) {
            if (!$asScript) {
                $this->writeError($output, $path === ''
                    ? '<comment>Could not find any configuration value.</comment>'
                    : sprintf('<comment>Could not find a configuration value for "%s".</comment>', $path));
            }

            return Command::SUCCESS;
        }

        if ($input->getOption('update-script') === true) {
            $this->renderUpdateScript($output, $rows);
        } elseif ($input->getOption('magerun-script') === true) {
            $this->renderMagerunScript($output, $rows);
        } else {
            $this->renderRows($output, $rows, $format);
        }

        return Command::SUCCESS;
    }

    /**
     * Escapes the LIKE wildcards of the path, then turns its * into %
     */
    private function toLikePattern(string $path): string
    {
        return str_replace('*', '%', addcslashes($path, '\\%_'));
    }

    /**
     * Sorts by path, then in the override order default, websites, stores
     *
     * @return list<array{path: string, scope: string, scope_id: int, value: null|string, decrypted: bool}>
     */
    private function sortedRows(Mage_Core_Model_Resource_Config_Data_Collection $collection, bool $decrypt): array
    {
        $rows = [];
        /** @var Mage_Core_Model_Config_Data $item */
        foreach ($collection as $item) {
            $value = $item->getValue();
            $value = is_scalar($value) ? (string) $value : null;
            $decrypted = $decrypt && $value !== null ? $this->decryptValue($value) : null;
            $rows[] = [
                'path' => (string) $item->getPath(),
                'scope' => (string) $item->getScope(),
                'scope_id' => (int) $item->getScopeId(),
                'value' => $decrypted ?? $value,
                'decrypted' => $decrypted !== null,
            ];
        }

        $scopeRank = array_flip(self::SCOPES);
        usort($rows, static fn(array $a, array $b): int => [$a['path'], $scopeRank[$a['scope']] ?? 99, $a['scope_id']]
            <=> [$b['path'], $scopeRank[$b['scope']] ?? 99, $b['scope_id']]);

        return $rows;
    }

    /**
     * Decrypts a value stored as padded base64 of whole cipher blocks, when the result is printable UTF-8
     *
     * @return null|string null when the value is not an encrypted one
     */
    private function decryptValue(string $value): ?string
    {
        $binary = strlen($value) % 4 === 0 ? base64_decode($value, true) : false;
        if ($binary === false || $binary === '' || strlen($binary) % self::CIPHER_BLOCK_SIZE !== 0) {
            return null;
        }

        $decrypted = $this->coreHelper()->decrypt($value);
        if ($decrypted === '' || !mb_check_encoding($decrypted, 'UTF-8') || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $decrypted) === 1) {
            return null;
        }

        return $decrypted;
    }

    /**
     * @param list<array{path: string, scope: string, scope_id: int, value: null|string, decrypted: bool}> $rows
     */
    private function renderRows(OutputInterface $output, array $rows, string $format): void
    {
        $nullDisplay = match ($format) {
            'json' => null,
            'csv' => 'NULL',
            default => self::DISPLAY_NULL_VALUE,
        };

        $tableRows = array_map(
            static fn(array $row): array => [$row['path'], $row['scope'], $row['scope_id'], $row['value'] ?? $nullDisplay],
            $rows,
        );

        (new TableRenderer())->render($output, ['path', 'scope', 'scope_id', 'value'], $tableRows, $format);
    }

    /**
     * @param list<array{path: string, scope: string, scope_id: int, value: null|string, decrypted: bool}> $rows
     */
    private function renderUpdateScript(OutputInterface $output, array $rows): void
    {
        $output->writeln('<?php', OutputInterface::OUTPUT_RAW);
        $output->writeln('$installer = $this;', OutputInterface::OUTPUT_RAW);
        $output->writeln('# generated by bin/openmage', OutputInterface::OUTPUT_RAW);

        foreach ($rows as $row) {
            $arguments = [var_export($row['path'], true), var_export($row['value'], true)];
            if ($row['scope'] !== 'default') {
                $arguments[] = var_export($row['scope'], true);
                $arguments[] = var_export($row['scope_id'], true);
            }

            $output->writeln(sprintf('$installer->setConfigData(%s);', implode(', ', $arguments)), OutputInterface::OUTPUT_RAW);
        }
    }

    /**
     * Line breaks are written as \n and \r, which config:set converts back; the admin scope ID 0 gets --force
     * and a decrypted value gets --encrypt
     *
     * @param list<array{path: string, scope: string, scope_id: int, value: null|string, decrypted: bool}> $rows
     */
    private function renderMagerunScript(OutputInterface $output, array $rows): void
    {
        foreach ($rows as $row) {
            $value = $row['value'] === null ? null : str_replace(["\n", "\r"], ['\n', '\r'], $row['value']);

            $output->writeln(sprintf(
                'config:set %s%s%s--scope-id=%d --scope=%s -- %s %s',
                $value === 'NULL' ? '--no-null ' : '',
                $row['decrypted'] ? '--encrypt ' : '',
                $row['scope'] !== 'default' && $row['scope_id'] === 0 ? '--force ' : '',
                $row['scope_id'],
                $row['scope'],
                escapeshellarg($row['path']),
                $value === null ? 'NULL' : escapeshellarg($value),
            ), OutputInterface::OUTPUT_RAW);
        }
    }
}
