<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Index
 */

/**
 * Database lock storage
 *
 * @package    Mage_Index
 */
class Mage_Index_Model_Lock_Storage_Db implements Mage_Index_Model_Lock_Storage_Interface
{
    /**
     * @var Mage_Index_Model_Resource_Helper_Mysql4
     */
    protected $_helper;

    /**
     * @var Varien_Db_Adapter_Interface
     */
    protected $_connection;

    public function __construct()
    {
        /** @var Mage_Index_Model_Resource_Lock_Resource $resource */
        $resource = Mage::getSingleton('index/resource_lock_resource');
        $this->_connection = $resource->getConnection('index_write', 'default_lock');
        /** @var Mage_Index_Model_Resource_Helper_Mysql4 $helper */
        $helper = Mage::getResourceHelper('index');
        $this->_helper = $helper;
    }

    /**
     * @param  string $name
     * @return string
     */
    protected function _prepareLockName($name)
    {
        $config = $this->_connection->getConfig();
        return $config['dbname'] . '.' . $name;
    }

    /**
     * Set named lock
     *
     * @param  string $lockName
     * @return bool
     */
    public function setLock($lockName)
    {
        $lockName = $this->_prepareLockName($lockName);
        return $this->_helper->setLock($lockName);
    }

    /**
     * Release named lock
     *
     * @param  string $lockName
     * @return bool
     */
    public function releaseLock($lockName)
    {
        $lockName = $this->_prepareLockName($lockName);
        return $this->_helper->releaseLock($lockName);
    }

    /**
     * Check whether the lock exists
     *
     * @param  string $lockName
     * @return bool
     */
    public function isLockExists($lockName)
    {
        $lockName = $this->_prepareLockName($lockName);
        return $this->_helper->isLocked($lockName);
    }
}
