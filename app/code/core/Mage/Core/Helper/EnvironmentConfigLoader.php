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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2016-present The OpenMage Contributors (https://www.openmage.org)
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
    protected $_moduleName = 'Mage_Core';
    protected const ENV_STARTS_WITH = 'OPENMAGE_CONFIG';
    protected const ENV_KEY_SEPARATOR = '__';
    protected const CONFIG_KEY_DEFAULT = 'DEFAULT';
    protected const CONFIG_KEY_WEBSITES = 'WEBSITES';
    protected const CONFIG_KEY_STORES = 'STORES';

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
     * @param Mage_Core_Model_Config $xmlConfig
     * @return void
     */
    public function overrideEnvironment(Mage_Core_Model_Config $xmlConfig)
    {
        $env = getenv();

        foreach ($env as $configKey => $value) {
            if (!str_starts_with($configKey, static::ENV_STARTS_WITH)) {
                continue;
            }

            $configKeyParts = array_filter(explode(static::ENV_KEY_SEPARATOR, str_replace(static::ENV_STARTS_WITH, '', $configKey)), 'trim');
            list($_, $scope) = $configKeyParts;

            switch ($scope) {
                case static::CONFIG_KEY_DEFAULT:
                    list($_, $_, $section, $group, $field) = $configKeyParts;
                    $path = $this->buildPath($section, $group, $field);
                    $xmlConfig->setNode($this->buildNodePath($scope, $path), $value);
                    break;

                case static::CONFIG_KEY_WEBSITES:
                case static::CONFIG_KEY_STORES:
                    list($_, $_, $code, $section, $group, $field) = $configKeyParts;
                    $path = $this->buildPath($section, $group, $field);
                    $nodePath = sprintf('%s/%s/%s', strtolower($scope), strtolower($code), $path);
                    $xmlConfig->setNode($nodePath, $value);
                    break;
            }
        }
    }

    /**
     * Build configuration path.
     *
     * @param string $section
     * @param string $group
     * @param string $field
     * @return string
     */
    public function buildPath($section, $group, $field): string
    {
        return strtolower(implode('/', [$section, $group, $field]));
    }

    /**
     * Build configuration node path.
     *
     * @param string $scope
     * @param string $path
     * @return string
     */
    public function buildNodePath($scope, $path): string
    {
        return strtolower($scope) . '/' . $path;
    }
}
