<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Index
 */

/**
 * Index resource helper class for MySQL adapter
 *
 * @package    Mage_Index
 */
class Mage_Index_Model_Resource_Helper_Mysql4 extends Mage_Core_Model_Resource_Helper_Mysql4 implements Mage_Index_Model_Resource_Helper_Lock_Interface
{
    /**
     * Insert data from select statement
     *
     * @param  Mage_Index_Model_Resource_Abstract $object
     * @param  Varien_Db_Select                   $select
     * @param  string                             $destTable
     * @param  array                              $columns
     * @param  bool                               $readToIndex
     * @return Mage_Index_Model_Resource_Abstract
     */
    public function insertData($object, $select, $destTable, $columns, $readToIndex)
    {
        return $object->insertFromSelect($select, $destTable, $columns, $readToIndex);
    }

    /**
     * Set lock
     *
     * @param  string $name
     * @return bool
     */
    public function setLock($name)
    {
        return (bool) $this->_getWriteAdapter()->query('SELECT GET_LOCK(?, ?);', [$name, self::LOCK_GET_TIMEOUT])
            ->fetchColumn();
    }

    /**
     * Release lock
     *
     * @param  string $name
     * @return bool
     */
    public function releaseLock($name)
    {
        return (bool) $this->_getWriteAdapter()->query('SELECT RELEASE_LOCK(?);', [$name])->fetchColumn();
    }

    /**
     * Is lock exists
     *
     * @param  string $name
     * @return bool
     */
    public function isLocked($name)
    {
        return (bool) $this->_getWriteAdapter()->query('SELECT IS_USED_LOCK(?);', [$name])->fetchColumn();
    }

    /**
     * @return $this
     */
    public function setWriteAdapter(Varien_Db_Adapter_Interface $adapter)
    {
        $this->_writeAdapter = $adapter;

        return $this;
    }
}
