<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Core Environment helper
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Helper_EnvironmentConfigLoader extends Mage_Core_Helper_Abstract
{
    protected const ENV_STARTS_WITH = 'OPENMAGE_CONFIG';
    protected const ENV_KEY_SEPARATOR = '__';
    protected const CONFIG_KEY_DEFAULT = 'DEFAULT';
    protected const CONFIG_KEY_WEBSITES = 'WEBSITES';
    protected const CONFIG_KEY_STORES = 'STORES';
    /**
     * To be used as regex condition
     */
    protected const ALLOWED_CHARS = ['A-Z', '-', '_'];
    protected $_moduleName = 'Mage_Core';
    protected array $envStore = [];

    /**
     * Load configuration values from ENV variables into xml config object
     *
     * Environment variables work on this schema:
     *
     * self::ENV_STARTS_WITH . self::ENV_KEY_SEPARATOR (OPENMAGE_CONFIG__)
     *        ^ Prefix (required)
     *                  <SCOPE>__
     *                     ^ Where scope is DEFAULT, WEBSITES__<WEBSITE_CODE> or STORES__<STORE_CODE>
     *                           <SYSTEM_VARIABLE_NAME>
     *                                   ^ Where GROUP, SECTION and FIELD are separated by self::ENV_KEY_SEPARATOR
     *
     * Each example will override the 'general/store_information/name' value.
     * Override from the default configuration:
     * @example OPENMAGE_CONFIG__DEFAULT__GENERAL__STORE_INFORMATION__NAME=default
     * Override the website 'base' configuration:
     * @example OPENMAGE_CONFIG__WEBSITES__BASE__GENERAL__STORE_INFORMATION__NAME=website
     * Override the store 'german' configuration:
     * @example OPENMAGE_CONFIG__STORES__GERMAN__GENERAL__STORE_INFORMATION__NAME=store_german
     *
     * @return void
     */
    public function overrideEnvironment(Varien_Simplexml_Config $xmlConfig)
    {
        $env = $this->getEnv();

        foreach ($env as $configKey => $value) {
            if (!$this->isConfigKeyValid($configKey)) {
                continue;
            }

            list($configKeyParts, $scope) = $this->getConfigKey($configKey);

            switch ($scope) {
                case static::CONFIG_KEY_DEFAULT:
                    list($unused1, $unused2, $section, $group, $field) = $configKeyParts;
                    $path = $this->buildPath($section, $group, $field);
                    $xmlConfig->setNode($this->buildNodePath($scope, $path), $value);
                    break;

                case static::CONFIG_KEY_WEBSITES:
                case static::CONFIG_KEY_STORES:
                    list($unused1, $unused2, $code, $section, $group, $field) = $configKeyParts;
                    $path = $this->buildPath($section, $group, $field);
                    $nodePath = sprintf('%s/%s/%s', strtolower($scope), strtolower($code), $path);
                    $xmlConfig->setNode($nodePath, $value);
                    break;
            }
        }
    }

    /**
     * @internal method mostly for mocking
     */
    public function setEnvStore(array $envStorage): void
    {
        $this->envStore = $envStorage;
    }

    public function getEnv(): array
    {
        if (empty($this->envStore)) {
            $this->envStore = getenv();
        }
        return $this->envStore;
    }

    protected function getConfigKey(string $configKey): array
    {
        $configKeyParts = array_filter(
            explode(
                static::ENV_KEY_SEPARATOR,
                $configKey
            ),
            'trim'
        );
        list($unused, $scope) = $configKeyParts;
        return [$configKeyParts, $scope];
    }

    protected function isConfigKeyValid(string $configKey): bool
    {
        if (!str_starts_with($configKey, static::ENV_STARTS_WITH)) {
            return false;
        }

        $sectionGroupFieldRegexp = sprintf('([%s]*)', implode('', static::ALLOWED_CHARS));
        $allowedChars = sprintf('[%s]', implode('', static::ALLOWED_CHARS));
        $regexp = '/' . static::ENV_STARTS_WITH . static::ENV_KEY_SEPARATOR . '(WEBSITES' . static::ENV_KEY_SEPARATOR
            . $allowedChars . '+|DEFAULT|STORES' . static::ENV_KEY_SEPARATOR . $allowedChars . '+)'
            . static::ENV_KEY_SEPARATOR . $sectionGroupFieldRegexp
            . static::ENV_KEY_SEPARATOR . $sectionGroupFieldRegexp
            . static::ENV_KEY_SEPARATOR . $sectionGroupFieldRegexp . '/';
        // /OPENMAGE_CONFIG__(WEBSITES__[A-Z-_]+|DEFAULT|STORES__[A-Z-_]+)__([A-Z-_]*)__([A-Z-_]*)__([A-Z-_]*)/

        return preg_match($regexp, $configKey);
    }

    /**
     * Build configuration path.
     */
    protected function buildPath(string $section, string $group, string $field): string
    {
        return strtolower(implode('/', [$section, $group, $field]));
    }

    /**
     * Build configuration node path.
     */
    protected function buildNodePath(string $scope, string $path): string
    {
        return strtolower($scope) . '/' . $path;
    }
}
