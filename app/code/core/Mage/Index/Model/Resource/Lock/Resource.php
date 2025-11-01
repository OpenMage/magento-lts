<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Index
 */

/**
 * Lock resource model
 *
 * @package    Mage_Index
 */
class Mage_Index_Model_Resource_Lock_Resource extends Mage_Core_Model_Resource
{
    public function __construct()
    {
        $this->_connections = Mage::getSingleton('core/resource')->getConnections();
    }

    /**
     * Creates a connection to resource whenever needed
     *
     * @param string $name
     * @param string $extendConfigWith
     *
     * @return false|Varien_Db_Adapter_Interface
     */
    public function getConnection($name, $extendConfigWith = '')
    {
        $index = $name . $extendConfigWith;
        if (isset($this->_connections[$index])) {
            $connection = $this->_connections[$index];
            if (isset($this->_skippedConnections[$index]) && !Mage::app()->getIsCacheLocked()) {
                $connection->setCacheAdapter(Mage::app()->getCache());
                unset($this->_skippedConnections[$index]);
            }

            return $connection;
        }

        $connConfig = Mage::getConfig()->getResourceConnectionConfig($name);

        if (!$connConfig) {
            $this->_connections[$index] = $this->_getDefaultConnection($name, $extendConfigWith);
            return $this->_connections[$index];
        }

        if (!$connConfig->is('active', '1')) {
            return false;
        }

        $origName = $connConfig->getParent()->getName() . $extendConfigWith;
        if (isset($this->_connections[$origName])) {
            $this->_connections[$index] = $this->_connections[$origName];
            return $this->_connections[$origName];
        }

        $origConfigParams = $connConfig->asArray();
        if ($extendConfigWith) {
            $connConfig->extend(Mage::getConfig()->getResourceConnectionConfig($extendConfigWith), true);
        }

        $configDiff = array_diff_assoc($connConfig->asArray(), $origConfigParams);
        if (!$configDiff) {
            $index = $name;
            $origName = $connConfig->getParent()->getName();
            if (isset($this->_connections[$origName])) {
                $this->_connections[$index] = $this->_connections[$origName];
                return $this->_connections[$origName];
            }
        }

        $connection = $this->_newConnection((string) $connConfig->type, $connConfig);
        if ($connection) {
            if (Mage::app()->getIsCacheLocked()) {
                $this->_skippedConnections[$index] = true;
            } else {
                $connection->setCacheAdapter(Mage::app()->getCache());
            }
        }

        $this->_connections[$index] = $connection;
        if ($origName !== $index) {
            $this->_connections[$origName] = $connection;
        }

        return $connection;
    }

    /**
     * Retrieve default connection name by required connection name
     *
     * @param string $requiredConnectionName
     * @param string $extendConfigWith
     *
     * @return false|Varien_Db_Adapter_Interface
     */
    protected function _getDefaultConnection($requiredConnectionName, $extendConfigWith = '')
    {
        if (str_contains($requiredConnectionName, 'read')) {
            return $this->getConnection(self::DEFAULT_READ_RESOURCE, $extendConfigWith);
        }

        return $this->getConnection(self::DEFAULT_WRITE_RESOURCE, $extendConfigWith);
    }
}
