<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Index
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Database lock storage
 *
 * @category   Mage
 * @package    Mage_Index
 * @author     Magento Core Team <core@magentocommerce.com>
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

    /**
     * Constructor
     */
    public function __construct()
    {
        /** @var Mage_Core_Model_Resource $resource */
        $resource   = Mage::getSingleton('index/resource_lock_resource');
        $this->_connection = $resource->getConnection('index_write', 'default_lock');
        $this->_helper = Mage::getResourceHelper('index');
    }

    /**
     * @param string $name
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
     * @param string $lockName
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
     * @param string $lockName
     * @return int|null
     */
    public function releaseLock($lockName)
    {
        $lockName = $this->_prepareLockName($lockName);
        return $this->_helper->releaseLock($lockName);
    }

    /**
     * Check whether the lock exists
     *
     * @param string $lockName
     * @return bool
     */
    public function isLockExists($lockName)
    {
        $lockName = $this->_prepareLockName($lockName);
        return $this->_helper->isLocked($lockName);
    }
}
