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
class Mage_Core_Helper_EnvironmentLoader extends Mage_Core_Helper_Abstract
{
    const ENV_STARTS_WITH = 'OPENMAGE_CONFIG';
    const ENV_KEY_SEPARATOR = '__';
    const CONFIG_KEY_DEFAULT = 'DEFAULT';
    const CONFIG_KEY_WEBSITES = 'WEBSITES';
    const CONFIG_KEY_STORES = 'STORES';

    /**
     * Load configuration values from ENV variables into xml config object
     *
     * @param Mage_Core_Model_Config $xmlConfig
     * @return void
     */
    public function overrideEnvironment(Mage_Core_Model_Config $xmlConfig)
    {
        // override from env
        $env = getenv();
        foreach ($env as $configKey => $value) {
            if (!str_starts_with($configKey, static::ENV_STARTS_WITH)) {
                continue;
            }
            $configKey = str_replace(static::ENV_STARTS_WITH, '', $configKey);
            list($_, $scope) = array_filter(explode(static::ENV_KEY_SEPARATOR, $configKey), 'trim');
            switch ($scope) {
                case static::CONFIG_KEY_DEFAULT:
                    list($_, $scope, $section, $group, $field) = array_filter(explode(static::ENV_KEY_SEPARATOR, $configKey), 'trim');
                    $path = implode('/', [$section, $group, $field]);
                    $path = strtolower($path);
                    $scope = strtolower($scope);

                    $xmlConfig->setNode($scope . '/' . $path, $value);
                    break;
                case static::CONFIG_KEY_WEBSITES:
                    list($_, $_, $websiteCode, $section, $group, $field) = array_filter(explode(static::ENV_KEY_SEPARATOR, $configKey), 'trim');
                    $path = implode('/', [$section, $group, $field]);
                    $path = strtolower($path);
                    $websiteCode = strtolower($websiteCode);

                    $nodePath = sprintf('websites/%s/%s', $websiteCode, $path);
                    $xmlConfig->setNode($nodePath, $value);
                    break;
                case static::CONFIG_KEY_STORES:
                    list($_, $_, $storeCode, $section, $group, $field) = array_filter(explode(static::ENV_KEY_SEPARATOR, $configKey), 'trim');
                    $path = implode('/', [$section, $group, $field]);
                    $path = strtolower($path);
                    $storeCode = strtolower($storeCode);
                    $nodePath = sprintf('stores/%s/%s', $storeCode, $path);
                    $xmlConfig->setNode($nodePath, $value);
                    break;
            }
        }
    }
}
