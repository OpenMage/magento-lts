<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2015-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * JavaScript helper
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Helper_Config extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Core';

    /**
     * @param string $codePoolFilter
     * @param bool $activeFilter
     * @return array
     */
    public function listModules(
        string $codePoolFilter = null,
        bool $activeFilter = null,
        bool $dependFilter = null,
    ): array {
        $list = [];

        $modules = Mage::app()->getConfig()->getNode('modules')->asArray();
        foreach ($modules as $moduleName => $moduleInfo) {
            $codePool   = isset($moduleInfo['codePool']) ? $moduleInfo['codePool'] : '';
            $version    = isset($moduleInfo['version']) ? $moduleInfo['version'] : '';
            $active     = isset($moduleInfo['active']) ? $this->activeStringToBool($moduleInfo['active']) : false;
            $depends    = isset($moduleInfo['depends']) ? $moduleInfo['depends'] : false;

            if (!is_null($codePoolFilter) && $codePoolFilter !== $codePool) {
                continue;
            }

            if (!is_null($activeFilter) && $activeFilter !== $active) {
                continue;
            }

            $list[$moduleName]['codePool']  = trim($codePool);
            $list[$moduleName]['Name']      = trim($moduleName);
            $list[$moduleName]['Version']   = trim($version);
            $list[$moduleName]['Status']    = $this->activeBoolToString($active);
            $list[$moduleName]['Used by']   = [];

            if ($depends) {
                foreach (array_keys($depends) as $dependModuleName) {
                    $list[$dependModuleName]['Used by'][] = trim($moduleName);
                }
            }
        }

        foreach ($list as $moduleName => $moduleInfo) {
            if ($dependFilter && $moduleInfo['Used by']) {
                unset($list[$moduleName]);
            } else {
                $list[$moduleName]['Used by'] = implode("\n", $moduleInfo['Used by']);
            }
        }

        asort($list);

        return $list;
    }

    /**
     * @param string $moduleInfo
     * @return bool
     */
    protected function activeStringToBool(string $moduleInfo): bool
    {
        return trim($moduleInfo) === 'true';
    }

    /**
     * @param bool $moduleInfo
     * @return string
     */
    protected function activeBoolToString(bool $moduleInfo): string
    {
        return $moduleInfo ? 'enabled' : 'disabled';
    }
}
