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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Index
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract resource model. Can be used as base for indexer resources
 */
abstract class Mage_Index_Model_Mysql4_Abstract extends Mage_Core_Model_Mysql4_Abstract
{
    const IDX_SUFFIX    = '_idx';
    const TMP_SUFFIX    = '_tmp';

    /**
     * Flag that defines if need to use "_idx" index table suffix instead of "_tmp"
     *
     * @var bool
     */
    protected $_isNeedUseIdxTable = false;

    /**
     * Flag that defines if need to disable keys during data inserting
     *
     * @var bool
     */
    protected $_isDisableKeys = true;

    public function reindexAll()
    {
        $this->useIdxTable(true);
        return $this;
    }

    /**
     * Get DB adapter for index data processing
     *
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    protected function _getIndexAdapter()
    {
        return $this->_getWriteAdapter();
    }

    /**
     * Get index table name with additional suffix
     *
     * @param   string $table
     * @return  string
     */
    public function getIdxTable($table = null)
    {
        $suffix = self::TMP_SUFFIX;
        if ($this->_isNeedUseIdxTable) {
            $suffix = self::IDX_SUFFIX;
        }
        if ($table) {
            return $table . $suffix;
        }
        return $this->getMainTable() . $suffix;
    }

    /**
     * Synchronize data between index storage and original storage
     *
     * @return Mage_Index_Model_Mysql4_Abstract
     */
    public function syncData()
    {
        $this->beginTransaction();
        /**
         * Can't use truncate because of transaction
         */
        $this->_getWriteAdapter()->delete($this->getMainTable());
        $this->insertFromTable($this->getIdxTable(), $this->getMainTable(), false);
        $this->commit();
        return $this;
    }

    /**
     * Create temporary table for index data pregeneration
     *
     * @return Mage_Index_Model_Mysql4_Abstract
     */
    public function cloneIndexTable($asOriginal = false)
    {
        $mainTable  = $this->getMainTable();
        $idxTable   = $this->getIdxTable();
        $idxAdapter = $this->_getIndexAdapter();

        $sql = 'DROP TABLE IF EXISTS ' . $idxAdapter->quoteIdentifier($idxTable);
        $idxAdapter->query($sql);
        if ($asOriginal) {
            $sql = 'CREATE TABLE ' . $idxAdapter->quoteIdentifier($idxTable)
                . ' LIKE ' . $idxAdapter->quoteIdentifier($this->getMainTable());
        } else {
            $sql = 'CREATE TABLE ' . $idxAdapter->quoteIdentifier($idxTable)
                . ' SELECT * FROM ' . $idxAdapter->quoteIdentifier($mainTable) . ' LIMIT 0';
        }
        $idxAdapter->query($sql);
        return $this;
    }

    /**
     * Copy data from source table of read adapter to destination table of index adapter
     *
     * @param   string $sourceTable
     * @param   string $destTable
     * @param   bool $readToIndex data migration direction (true - read=>index, false - index=>read)
     * @return  Mage_Index_Model_Mysql4_Abstract
     */
    public function insertFromTable($sourceTable, $destTable, $readToIndex=true)
    {
        if ($readToIndex) {
            $columns = $this->_getWriteAdapter()->describeTable($sourceTable);
        } else {
            $columns = $this->_getIndexAdapter()->describeTable($sourceTable);
        }
        $columns = array_keys($columns);
        $select = 'SELECT * FROM ' . $sourceTable;
        return $this->insertFromSelect($select, $destTable, $columns, $readToIndex);
    }

    /**
     * Insert data from select statement of read adapter to
     * destination table related with index adapter
     *
     * @param   string $select
     * @param   string $destTable
     * @param   array $columns
     * @param   bool $readToIndex data migration direction (true - read=>index, false - index=>read)
     * @return  Mage_Index_Model_Mysql4_Abstract
     *      */
    public function insertFromSelect($select, $destTable, array $columns, $readToIndex=true)
    {
        if ($readToIndex) {
            $from   = $this->_getWriteAdapter();
            $to     = $this->_getIndexAdapter();
        } else {
            $from   = $this->_getIndexAdapter();
            $to     = $this->_getWriteAdapter();
        }

        if ($this->useDisableKeys()) {
            $to->query("ALTER TABLE {$destTable} DISABLE KEYS");
        }

        if ($from === $to) {
            $sql = 'INSERT INTO ' . $destTable . ' ' . $select;
            $to->query($sql);
        } else {
            $stmt = $from->query($select);
            $data = array();
            $counter = 0;
            while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                $data[] = $row;
                $counter++;
                if ($counter>2000) {
                    $to->insertArray($destTable, $columns, $data);
                    $data = array();
                    $counter = 0;
                }
            }
            if (!empty($data)) {
                $to->insertArray($destTable, $columns, $data);
            }
        }

        if ($this->useDisableKeys()) {
            $to->query("ALTER TABLE {$destTable} ENABLE KEYS");
        }

        return $this;
    }

    /**
     * Set or get what either "_idx" or "_tmp" suffixed temporary index table need to use
     *
     * @param bool $value
     * @return Mage_Index_Model_Mysql4_Abstract
     */
    public function useIdxTable($value = null)
    {
        if (!is_null($value)) {
            $this->_isNeedUseIdxTable = (bool)$value;
        }
        return $this->_isNeedUseIdxTable;
    }

    /**
     * Set or get flag that defines if need to disable keys during data inserting
     *
     * @param bool $value
     * @return Mage_Index_Model_Mysql4_Abstract
     */
    public function useDisableKeys($value = null)
    {
        if (!is_null($value)) {
            $this->_isDisableKeys = (bool)$value;
        }
        return $this->_isDisableKeys;
    }
    
    
    /**
     * Clean up temporary index table
     */
    public function clearTemporaryIndexTable()
    {
        $this->_getWriteAdapter()->delete($this->getIdxTable());
    }
}
