<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Generate options for media database selection
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Storage_Media_Database
{
    /**
     * Store all detected connections
     *
     * @var array
     */
    protected $_connections = [];

    /**
     * Recursively collect connection configuration
     *
     * @param  string $connectionName
     * @return array
     */
    protected function _collectConnectionConfig($connectionName)
    {
        $config = [];

        if (isset($this->_connections[$connectionName])) {
            $connection = $this->_connections[$connectionName];
            $connection = (array) $connection->descend('connection');

            if (isset($connection['use'])) {
                $config = $this->_collectConnectionConfig((string) $connection['use']);
            }

            $config = array_merge($config, $connection);
        }

        return $config;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $mediaStorages = [];

        $this->_connections = (array) Mage::app()->getConfig()->getNode('global/resources')->children();
        foreach (array_keys($this->_connections) as $connectionName) {
            $connection = $this->_collectConnectionConfig($connectionName);
            if (!isset($connection['active']) || $connection['active'] != 1) {
                continue;
            }

            $mediaStorages[] = ['value' => $connectionName, 'label' => $connectionName];
        }
        sort($mediaStorages);
        reset($mediaStorages);

        return $mediaStorages;
    }
}
