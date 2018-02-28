<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Index
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Database lock storage
 *
 * @category Mage
 * @package Mage_Core
 * @author Magento Core Team core@magentocommerce.com
 */
class Mage_Index_Model_Lock_Storage_Db implements Mage_Index_Model_Lock_Storage_Interface
{
    /**
     * @var Mage_Index_Model_Resource_Helper_Abstract
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
        /** @var $resource Mage_Core_Model_Resource */
        $resource   = Mage::getSingleton('index/resource_lock_resource');
        $this->_connection = $resource->getConnection('index_write', 'default_lock');
        $this->_helper = Mage::getResourceHelper('index');
    }

    protected function _prepareLockName($name)
    {
        $config = $this->_connection->getConfig();
        return $config['dbname'] . '.' . $name;
    }

    /**
     * Set named lock
     *
     * @param string $lockName
     * @return int
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
