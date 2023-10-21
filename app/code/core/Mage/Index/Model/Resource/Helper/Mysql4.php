<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Index
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Index resource helper class for MySQL adapter
 *
 * @category   Mage
 * @package    Mage_Index
 */
class Mage_Index_Model_Resource_Helper_Mysql4 extends Mage_Core_Model_Resource_Helper_Mysql4 implements Mage_Index_Model_Resource_Helper_Lock_Interface
{
    /**
     * Insert data from select statement
     *
     * @param Mage_Index_Model_Resource_Abstract $object
     * @param Varien_Db_Select $select
     * @param string $destTable
     * @param array $columns
     * @param bool $readToIndex
     * @return Mage_Index_Model_Resource_Abstract
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
        return (bool) $this->_getWriteAdapter()->query("SELECT GET_LOCK(?, ?);", [$name, self::LOCK_GET_TIMEOUT])
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
        return (bool) $this->_getWriteAdapter()->query("SELECT RELEASE_LOCK(?);", [$name])->fetchColumn();
    }

    /**
     * Is lock exists
     *
     * @param string $name
     * @return bool
     */
    public function isLocked($name)
    {
        return (bool) $this->_getWriteAdapter()->query("SELECT IS_USED_LOCK(?);", [$name])->fetchColumn();
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
