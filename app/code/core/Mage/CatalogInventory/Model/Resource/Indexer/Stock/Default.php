<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogInventory
 */

/**
 * CatalogInventory Default Stock Status Indexer Resource Model
 *
 * @package    Mage_CatalogInventory
 */
class Mage_CatalogInventory_Model_Resource_Indexer_Stock_Default extends Mage_Catalog_Model_Resource_Product_Indexer_Abstract implements Mage_CatalogInventory_Model_Resource_Indexer_Stock_Interface
{
    /**
     * Current Product Type Id
     *
     * @var null|string
     */
    protected $_typeId;

    /**
     * Product Type is composite flag
     *
     * @var bool
     */
    protected $_isComposite    = false;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('cataloginventory/stock_status', 'product_id');
    }

    /**
     * Reindex all stock status data for default logic product type
     *
     * @return $this
     * @throws Exception
     */
    public function reindexAll()
    {
        $this->useIdxTable(true);
        $this->beginTransaction();
        try {
            $this->_prepareIndexTable();
            $this->commit();
        } catch (Exception $exception) {
            $this->rollBack();
            throw $exception;
        }

        return $this;
    }

    /**
     * Reindex stock data for defined product ids
     *
     * @param  array|int $entityIds
     * @return $this
     */
    public function reindexEntity($entityIds)
    {
        $this->_updateIndex($entityIds);
        return $this;
    }

    /**
     * Set active Product Type Id
     *
     * @param  string $typeId
     * @return $this
     */
    public function setTypeId($typeId)
    {
        $this->_typeId = $typeId;
        return $this;
    }

    /**
     * Retrieve active Product Type Id
     *
     * @return string
     * @throws Mage_Core_Exception
     */
    public function getTypeId()
    {
        if (is_null($this->_typeId)) {
            Mage::throwException(Mage::helper('cataloginventory')->__('Undefined product type.'));
        }

        return $this->_typeId;
    }

    /**
     * Set Product Type Composite flag
     *
     * @param  bool  $flag
     * @return $this
     */
    public function setIsComposite($flag)
    {
        $this->_isComposite = (bool) $flag;
        return $this;
    }

    /**
     * Check product type is composite
     *
     * @return bool
     */
    public function getIsComposite()
    {
        return $this->_isComposite;
    }

    /**
     * Retrieve is Global Manage Stock enabled
     *
     * @return bool
     */
    protected function _isManageStock()
    {
        return Mage::getStoreConfigFlag(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK);
    }

    /**
     * Get the select object for get stock status by product ids
     *
     * @param  array|int           $entityIds
     * @param  bool                $usePrimaryTable use primary or temporary index table
     * @return Varien_Db_Select
     * @throws Mage_Core_Exception
     */
    protected function _getStockStatusSelect($entityIds = null, $usePrimaryTable = false)
    {
        $adapter = $this->_getWriteAdapter();
        $qtyExpr = $adapter->getCheckSql('cisi.qty > 0', 'cisi.qty', '0');
        $select  = $adapter->select()
            ->from(['e' => $this->getTable('catalog/product')], ['entity_id']);
        $this->_addWebsiteJoinToSelect($select, true);
        $this->_addProductWebsiteJoinToSelect($select, 'cw.website_id', 'e.entity_id');
        $select->columns('cw.website_id')
            ->join(
                ['cis' => $this->getTable('cataloginventory/stock')],
                '',
                ['stock_id'],
            )
            ->joinLeft(
                ['cisi' => $this->getTable('cataloginventory/stock_item')],
                'cisi.stock_id = cis.stock_id AND cisi.product_id = e.entity_id',
                [],
            )
            ->columns(['qty' => $qtyExpr])
            ->where('cw.website_id != 0')
            ->where('e.type_id = ?', $this->getTypeId());

        // add limitation of status
        $psExpr = $this->_addAttributeToSelect($select, 'status', 'e.entity_id', 'cs.store_id');
        $psCondition = $adapter->quoteInto($psExpr . '=?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);

        if ($this->_isManageStock()) {
            $statusExpr = $adapter->getCheckSql(
                'cisi.use_config_manage_stock = 0 AND cisi.manage_stock = 0',
                '1',
                'cisi.is_in_stock',
            );
        } else {
            $statusExpr = $adapter->getCheckSql(
                'cisi.use_config_manage_stock = 0 AND cisi.manage_stock = 1',
                'cisi.is_in_stock',
                '1',
            );
        }

        $optExpr = $adapter->getCheckSql($psCondition, '1', '0');
        $stockStatusExpr = $adapter->getLeastSql([$optExpr, $statusExpr]);

        $select->columns(['status' => $stockStatusExpr]);

        if (!is_null($entityIds)) {
            $select->where('e.entity_id IN(?)', $entityIds);
        }

        return $select;
    }

    /**
     * Prepare stock status data in temporary index table
     *
     * @param  array|int                 $entityIds the product limitation
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Zend_Db_Adapter_Exception
     */
    protected function _prepareIndexTable($entityIds = null)
    {
        $adapter = $this->_getWriteAdapter();
        $select  = $this->_getStockStatusSelect($entityIds);
        $query   = $select->insertFromSelect($this->getIdxTable());
        $adapter->query($query);

        return $this;
    }

    /**
     * Update Stock status index by product ids
     *
     * @param  array|int                   $entityIds
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Zend_Db_Adapter_Exception
     * @throws Zend_Db_Exception
     * @throws Zend_Db_Statement_Exception
     */
    protected function _updateIndex($entityIds)
    {
        $adapter = $this->_getWriteAdapter();
        $select  = $this->_getStockStatusSelect($entityIds, true);
        $query   = $adapter->query($select);

        $index  = 0;
        $data   = [];
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $index++;
            $data[] = [
                'product_id'    => (int) $row['entity_id'],
                'website_id'    => (int) $row['website_id'],
                'stock_id'      => (int) $row['stock_id'],
                'qty'           => (float) $row['qty'],
                'stock_status'  => (int) $row['status'],
            ];
            if (($index % 1000) == 0) {
                $this->_updateIndexTable($data);
                $data = [];
            }
        }

        $this->_updateIndexTable($data);

        return $this;
    }

    /**
     * Update stock status index table (INSERT ... ON DUPLICATE KEY UPDATE ...)
     *
     * @param  array               $data
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Zend_Db_Exception
     */
    protected function _updateIndexTable($data)
    {
        if (empty($data)) {
            return $this;
        }

        $adapter = $this->_getWriteAdapter();
        $adapter->insertOnDuplicate($this->getMainTable(), $data, ['qty', 'stock_status']);

        return $this;
    }

    /**
     * Retrieve temporary index table name
     *
     * @param  string $table
     * @return string
     */
    public function getIdxTable($table = null)
    {
        if ($this->useIdxTable()) {
            return $this->getTable('cataloginventory/stock_status_indexer_idx');
        }

        return $this->getTable('cataloginventory/stock_status_indexer_tmp');
    }
}
