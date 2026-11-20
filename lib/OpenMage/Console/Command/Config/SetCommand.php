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
use Mage_Adminhtml_Helper_Config;
use Mage_Adminhtml_Model_Config;
use Mage_Adminhtml_Model_System_Config_Backend_Encrypted;
use Mage_Adminhtml_Model_System_Config_Backend_File;
use Mage_Core_Exception;
use Mage_Core_Model_Config_Data;
use Mage_Core_Model_Resource_Config_Data_Collection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Varien_Simplexml_Element;

/**
 * Ported from the config:set command of n98-magerun (https://github.com/netz98/n98-magerun)
 *
 * n98-magerun: Copyright (c) netz98 GmbH, MIT license, see lib/OpenMage/Console/LICENSE-n98-magerun.txt
 */
#[AsCommand(name: 'config:set', description: 'Save a configuration value in the database')]
final class SetCommand extends AbstractConfigCommand
{
    /**
     * Admin field types posted as arrays and stored comma-separated
     */
    private const ARRAY_FIELD_TYPES = ['multiselect', 'time'];

    #[Override]
    protected function configure(): void
    {
        $this
            ->addArgument('path', InputArgument::REQUIRED, 'Configuration path, e.g. web/unsecure/base_url')
            ->addArgument('value', InputArgument::REQUIRED, 'Value; NULL stores a null value, \n and \r are line breaks')
            ->addOption('scope', null, InputOption::VALUE_REQUIRED, 'Scope: ' . implode(', ', self::SCOPES), 'default')
            ->addOption('scope-id', null, InputOption::VALUE_REQUIRED, 'Scope ID, or website or store code', '0')
            ->addOption('encrypt', null, InputOption::VALUE_NONE, 'Encrypt the value with the encryption key of local.xml')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Allow the admin website and store (ID 0 or code admin) and IDs of websites or stores that do not exist')
            ->addOption('no-null', null, InputOption::VALUE_NONE, 'Store the value NULL as the string "NULL"')
            ->addOption('validate', null, InputOption::VALUE_NONE, 'Save through the backend model of the field, as the admin configuration does')
            ->addOption('no-reinit', null, InputOption::VALUE_NONE, 'Do not rebuild the configuration afterwards')
            ->setHelp(<<<'HELP'
                Writes the value in the core_config_data table, then rebuilds the configuration as the admin does after saving.
                To set the value of a website or a store view, give <info>--scope</info> and <info>--scope-id</info>.

                With <info>--validate</info>, the backend model declared by the field in system.xml checks and transforms the value
                (validation, encryption of protected fields, cron expressions) and the admin events
                admin_system_config_section_save_after and admin_system_config_changed_section_<section> are dispatched.
                The other fields of the group are given to the backend model with their current values.
                Fields uploading a file cannot be saved with --validate, and a path that is not a field of system.xml is refused,
                as a field that system.xml saves under another path (config_path).

                The path needs at least three segments (section/group/field): a shorter one would replace a whole branch of the configuration.
                The two-character sequences <info>\n</info> and <info>\r</info> of the value are converted to line breaks; they cannot be escaped.
                HELP);
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $this->checkPath((string) $input->getArgument('path'));
        $scope = $this->checkScope($input->getOption('scope'));
        $scopeId = $this->resolveScopeId($scope, (string) $input->getOption('scope-id'), $input->getOption('force') === true);
        $encrypt = $input->getOption('encrypt') === true;

        $value = (string) $input->getArgument('value');
        if ($value === 'NULL' && $input->getOption('no-null') !== true) {
            if ($encrypt) {
                throw new InvalidArgumentException('A NULL value cannot be encrypted.');
            }

            $value = null;
            $display = self::DISPLAY_NULL_VALUE;
        } else {
            $value = str_replace(['\n', '\r'], ["\n", "\r"], $value);
            $display = $encrypt ? '(encrypted)' : $value;
            $value = $encrypt ? $this->coreHelper()->encrypt($value) : $value;
        }

        try {
            if ($input->getOption('validate') === true) {
                $backendEncrypts = $this->saveThroughBackend($path, $value, $scope, $scopeId, $encrypt);
                $display = $backendEncrypts && $value !== null && $value !== '' ? '(encrypted)' : $display;
            } else {
                // saveConfig() writes null as SQL NULL, although its phpdoc only allows a string.
                /** @phpstan-ignore argument.type */
                Mage::app()->getConfig()->saveConfig($path, $value, $scope, $scopeId);
            }

            if ($input->getOption('no-reinit') !== true) {
                Mage::app()->getConfig()->reinit();
                Mage::app()->reinitStores();
            }

            if ($input->getOption('validate') === true) {
                $this->dispatchAdminEvents($path, $scope, $scopeId);
            }
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->writeError($output, sprintf('<error>%s</error>', $mageCoreException->getMessage()));

            return Command::FAILURE;
        }

        $output->writeln(sprintf('<comment>%s</comment> => <comment>%s</comment>', $path, $display));

        return Command::SUCCESS;
    }

    /**
     * Mage_Core_Model_Config loads a value with setNode(), which replaces the node of the path and its children
     */
    private function checkPath(string $path): string
    {
        $segments = explode('/', $path);
        if (count($segments) < 3 || in_array('', $segments, true)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid path "%s", expected at least three non-empty segments, e.g. web/unsecure/base_url.',
                $path,
            ));
        }

        return $path;
    }

    /**
     * Saves one field as Mage_Adminhtml_Model_Config_Data::save() does, with the other fields of its group at their current value
     *
     * @return bool                whether the backend model encrypts the value
     * @throws Mage_Core_Exception
     */
    private function saveThroughBackend(string $path, ?string $value, string $scope, int $scopeId, bool $encrypted): bool
    {
        $parts = explode('/', $path);
        $fieldConfig = null;
        $groupFields = [];
        [$section, $group, $field] = $parts + [null, null, null];

        /** @var Mage_Adminhtml_Model_Config $adminConfig */
        $adminConfig = Mage::getSingleton('adminhtml/config');
        $fieldsNode = count($parts) === 3 ? $adminConfig->getSections()->descend($section . '/groups/' . $group . '/fields') : false;
        if ($fieldsNode instanceof Varien_Simplexml_Element) {
            foreach ($fieldsNode->children() as $name => $node) {
                $currentValue = $this->currentValue($section . '/' . $group . '/' . $name, $scope, $scopeId);
                $groupFields[$name] = ['value' => $this->toPostedValue($currentValue, $node)];
            }

            $node = $fieldsNode->descend((string) $field);
            $fieldConfig = $node instanceof Varien_Simplexml_Element ? $node : null;
        }

        if ($group === null || $field === null || !$fieldConfig instanceof Varien_Simplexml_Element) {
            Mage::throwException(sprintf('No field of system.xml has the path "%s": --validate cannot check the value, nothing was saved.', $path));
        }

        $configPath = (string) $fieldConfig->config_path;
        if ($configPath !== '' && $configPath !== $path) {
            Mage::throwException(sprintf('The field %s is saved under %s: set that path without --validate.', $path, $configPath));
        }

        /** @var Mage_Adminhtml_Helper_Config $configHelper */
        $configHelper = Mage::helper('adminhtml/config');
        $backendClass = $configHelper->getBackendModelByFieldConfig($fieldConfig);
        $model = Mage::getModel($backendClass ?? 'core/config_data');
        if (!$model instanceof Mage_Core_Model_Config_Data) {
            Mage::throwException(sprintf('Invalid config field backend model: %s', (string) $backendClass));
        }

        // _beforeSave() of the file backends takes the value from an uploaded file and drops the posted one.
        if ($model instanceof Mage_Adminhtml_Model_System_Config_Backend_File) {
            Mage::throwException(sprintf('The field %s uploads a file: --validate cannot save it, nothing was saved.', $path));
        }

        if ($encrypted && $model instanceof Mage_Adminhtml_Model_System_Config_Backend_Encrypted) {
            Mage::throwException(sprintf('The backend model of %s encrypts the value, remove --encrypt.', $path));
        }

        $postedValue = $this->toPostedValue($value, $fieldConfig);
        $groupFields[$field] = ['value' => $postedValue];

        [$websiteCode, $storeCode] = $this->scopeCodes($scope, $scopeId);

        /** @var Mage_Core_Model_Resource_Config_Data_Collection $existing */
        $existing = Mage::getResourceModel('core/config_data_collection');
        $existing->addFieldToFilter('scope', $scope)
            ->addFieldToFilter('scope_id', $scopeId)
            ->addFieldToFilter('path', $path);
        $configId = $existing->getFirstItem()->getId();

        $model->setPath($path)
            ->setValue($postedValue)
            ->setScope($scope)
            ->setScopeId($scopeId)
            ->setWebsiteCode($websiteCode)
            ->setStoreCode($storeCode)
            ->setField($field)
            ->setGroupId($group)
            ->setFieldConfig($fieldConfig)
            ->setGroups([$group => ['fields' => $groupFields]])
            ->setFieldsetData(array_map(static fn(array $fieldData): mixed => $fieldData['value'], $groupFields));
        if ($configId !== null) {
            $model->setConfigId($configId);
        }

        $model->save();

        return $model instanceof Mage_Adminhtml_Model_System_Config_Backend_Encrypted;
    }

    /**
     * Value of a path in a scope, as the admin form shows it
     */
    private function currentValue(string $path, string $scope, int $scopeId): ?string
    {
        $value = match ($scope) {
            'websites' => Mage::app()->getWebsite($scopeId)->getConfig($path),
            'stores' => Mage::app()->getStore($scopeId)->getConfig($path),
            default => Mage::app()->getConfig()->getNode('default/' . $path),
        };

        if ($value instanceof Varien_Simplexml_Element) {
            $value = $value->hasChildren() ? null : (string) $value;
        }

        return is_scalar($value) ? (string) $value : null;
    }

    /**
     * @return null|list<string>|string
     */
    private function toPostedValue(?string $value, Varien_Simplexml_Element $fieldConfig): null|array|string
    {
        if ($value !== null && in_array((string) $fieldConfig->frontend_type, self::ARRAY_FIELD_TYPES, true)) {
            return explode(',', $value);
        }

        return $value;
    }

    /**
     * @return array{0: null|string, 1: null|string} website code and store code, as the admin request parameters
     */
    private function scopeCodes(string $scope, int $scopeId): array
    {
        if ($scope === 'websites') {
            return [(string) Mage::app()->getWebsite($scopeId)->getCode(), null];
        }

        if ($scope === 'stores') {
            $store = Mage::app()->getStore($scopeId);
            $website = $store->getWebsite();

            return [$website === false ? null : (string) $website->getCode(), (string) $store->getCode()];
        }

        return [null, null];
    }

    private function dispatchAdminEvents(string $path, string $scope, int $scopeId): void
    {
        $section = explode('/', $path)[0];
        [$website, $store] = $this->scopeCodes($scope, $scopeId);

        Mage::dispatchEvent('admin_system_config_section_save_after', ['website' => $website, 'store' => $store, 'section' => $section]);
        Mage::dispatchEvent('admin_system_config_changed_section_' . $section, ['website' => $website, 'store' => $store]);
    }
}
