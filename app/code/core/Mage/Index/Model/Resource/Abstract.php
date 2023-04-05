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
 * @package    Mage_Index
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2017-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract resource model. Can be used as base for indexer resources
 *
 * @category   Mage
 * @package    Mage_Index
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Index_Model_Resource_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    public const IDX_SUFFIX = '_idx';
    public const TMP_SUFFIX = '_tmp';

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
    protected $_isDisableKeys = false;

    /**
     * Whether table changes are allowed
     *
     * @deprecated after 1.6.1.0
     * @var bool
     */
    protected $_allowTableChanges = true;

    /**
     * Reindex all
     *
     * @return Mage_Index_Model_Resource_Abstract
     */
    public function reindexAll()
    {
        $this->useIdxTable(true);
        return $this;
    }

    /**
     * Get DB adapter for index data processing
     *
     * @return Varien_Db_Adapter_Interface
     */
    protected function _getIndexAdapter()
    {
        return $this->_getWriteAdapter();
    }

    /**
     * Get index table name with additional suffix
     *
     * @param string $table
     * @return string
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
     * @return Mage_Index_Model_Resource_Abstract
     */
    public function syncData()
    {
        $this->beginTransaction();
        try {
            /**
             * Can't use truncate because of transaction
             */
            $this->_getWriteAdapter()->delete($this->getMainTable());
            $this->insertFromTable($this->getIdxTable(), $this->getMainTable(), false);
            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Create temporary table for index data pregeneration
     *
     * @deprecated since 1.5.0.0
     * @param bool $asOriginal
     * @return Mage_Index_Model_Resource_Abstract
     */
    public function cloneIndexTable($asOriginal = false)
    {
        return $this;
    }

    /**
     * Copy data from source table of read adapter to destination table of index adapter
     *
     * @param string $sourceTable
     * @param string $destTable
     * @param bool $readToIndex data migration direction (true - read=>index, false - index=>read)
     * @return Mage_Index_Model_Resource_Abstract
     */
    public function insertFromTable($sourceTable, $destTable, $readToIndex = true)
    {
        if ($readToIndex) {
            $sourceColumns = array_keys($this->_getReadAdapter()->describeTable($sourceTable));
            $targetColumns = array_keys($this->_getReadAdapter()->describeTable($destTable));
        } else {
            $sourceColumns = array_keys($this->_getIndexAdapter()->describeTable($sourceTable));
            $targetColumns = array_keys($this->_getReadAdapter()->describeTable($destTable));
        }
        $select = $this->_getIndexAdapter()->select()->from($sourceTable, $sourceColumns);

        /** @var Mage_Index_Model_Resource_Helper_Mysql4 $helper */
        $helper = Mage::getResourceHelper('index');
        $helper->insertData($this, $select, $destTable, $targetColumns, $readToIndex);
        return $this;
    }

    /**
     * Insert data from select statement of read adapter to
     * destination table related with index adapter
     *
     * @param Varien_Db_Select $select
     * @param string $destTable
     * @param array $columns
     * @param bool $readToIndex data migration direction (true - read=>index, false - index=>read)
     * @return Mage_Index_Model_Resource_Abstract
     */
    public function insertFromSelect($select, $destTable, array $columns, $readToIndex = true)
    {
        if ($readToIndex) {
            $from   = $this->_getWriteAdapter();
            $to     = $this->_getIndexAdapter();
        } else {
            $from   = $this->_getIndexAdapter();
            $to     = $this->_getWriteAdapter();
        }

        if ($from === $to) {
            $query = $select->insertFromSelect($destTable, $columns);
            $to->query($query);
        } else {
            $stmt = $from->query($select);
            $data = [];
            $counter = 0;
            while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                $data[] = $row;
                $counter++;
                if ($counter > 2000) {
                    $to->insertArray($destTable, $columns, $data);
                    $data = [];
                    $counter = 0;
                }
            }
            if (!empty($data)) {
                $to->insertArray($destTable, $columns, $data);
            }
        }

        return $this;
    }

    /**
     * Set or get what either "_idx" or "_tmp" suffixed temporary index table need to use
     *
     * @param bool $value
     * @return bool
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
     * @return bool
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
     *
     */
    public function clearTemporaryIndexTable()
    {
        $this->_getWriteAdapter()->delete($this->getIdxTable());
    }

    /**
     * Set whether table changes are allowed
     *
     * @deprecated after 1.6.1.0
     * @param bool $value
     * @return Mage_Index_Model_Resource_Abstract
     */
    public function setAllowTableChanges($value = true)
    {
        $this->_allowTableChanges = $value;
        return $this;
    }

    /**
     * Disable Main Table keys
     *
     * @return Mage_Index_Model_Resource_Abstract
     */
    public function disableTableKeys()
    {
        if ($this->useDisableKeys()) {
            $this->_getWriteAdapter()->disableTableKeys($this->getMainTable());
        }
        return $this;
    }

    /**
     * Enable Main Table keys
     *
     * @return Mage_Index_Model_Resource_Abstract
     */
    public function enableTableKeys()
    {
        if ($this->useDisableKeys()) {
            $this->_getWriteAdapter()->enableTableKeys($this->getMainTable());
        }
        return $this;
    }
}
