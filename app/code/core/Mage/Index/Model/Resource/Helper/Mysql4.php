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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Index resource helper class for MySQL adapter
 *
 * @category    Mage
 * @package     Mage_Index
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Index_Model_Resource_Helper_Mysql4 extends Mage_Core_Model_Resource_Helper_Mysql4
    implements Mage_Index_Model_Resource_Helper_Lock_Interface
{
    /**
     * Insert data from select statement
     *
     * @param Mage_Index_Model_Resource_Abstract $object
     * @param Varien_Db_Select $select
     * @param string $destTable
     * @param array $columns
     * @param bool $readToIndex
     * @return Mage_Index_Model_Resource_Helper_Mysql4
     */
    public function insertData($object, $select, $destTable, $columns, $readToIndex)
    {
        return $object->insertFromSelect($select, $destTable, $columns, $readToIndex);
    }

    /**
     * Set lock
     *
     * @param string $name
     * @return bool
     */
    public function setLock($name)
    {
        return (bool) $this->_getWriteAdapter()->query("SELECT GET_LOCK(?, ?);", array($name, self::LOCK_GET_TIMEOUT))
            ->fetchColumn();
    }

    /**
     * Release lock
     *
     * @param string $name
     * @return bool
     */
    public function releaseLock($name)
    {
        return (bool) $this->_getWriteAdapter()->query("SELECT RELEASE_LOCK(?);", array($name))->fetchColumn();
    }

    /**
     * Is lock exists
     *
     * @param string $name
     * @return bool
     */
    public function isLocked($name)
    {
        return (bool) $this->_getWriteAdapter()->query("SELECT IS_USED_LOCK(?);", array($name))->fetchColumn();
    }

    /**
     * @param Varien_Db_Adapter_Interface $adapter
     * @return $this
     */
    public function setWriteAdapter(Varien_Db_Adapter_Interface $adapter)
    {
        $this->_writeAdapter = $adapter;

        return $this;
    }
}
