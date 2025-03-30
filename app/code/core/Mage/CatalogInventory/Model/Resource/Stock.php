<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2016-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Stock resource model
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 */
class Mage_CatalogInventory_Model_Resource_Stock extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Is initialized configuration flag
     *
     * @var bool
     */
    protected $_isConfig;

    /**
     * Manage Stock flag
     *
     * @var bool
     */
    protected $_isConfigManageStock;

    /**
     * Backorders
     *
     * @var bool
     */
    protected $_isConfigBackorders;

    /**
     * Minimum quantity allowed in shopping card
     *
     * @var int
     */
    protected $_configMinQty;

    /**
     * Product types that could have quantities
     *
     * @var array
     */
    protected $_configTypeIds;

    /**
     * Notify for quantity below _configNotifyStockQty value
     *
     * @var string
     */
    protected $_configNotifyStockQty;

    /**
     * Ctalog Inventory Stock instance
     *
     * @var Mage_CatalogInventory_Model_Stock
     */
    protected $_stock;

    protected function _construct()
    {
        $this->_init('cataloginventory/stock', 'stock_id');
    }

    /**
     * Lock product items
     *
     * @param Mage_CatalogInventory_Model_Stock $stock
     * @param int|array $productIds
     * @return $this
     */
    public function lockProductItems($stock, $productIds)
    {
        $itemTable = $this->getTable('cataloginventory/stock_item');
        $select = $this->_getWriteAdapter()->select()
            ->from($itemTable)
            ->where('stock_id=?', $stock->getId())
            ->where('product_id IN(?)', $productIds)
            ->forUpdate(true);
        /**
         * We use write adapter for resolving problems with replication
         */
        $this->_getWriteAdapter()->query($select);
        return $this;
    }

    /**
     * Get stock items data for requested products
     *
     * @param Mage_CatalogInventory_Model_Stock $stock
     * @param array $productIds
     * @param bool $lockRows
     * @return array
     */
    public function getProductsStock($stock, $productIds, $lockRows = false)
    {
        if (empty($productIds)) {
            return [];
        }
        $itemTable = $this->getTable('cataloginventory/stock_item');
        $productTable = $this->getTable('catalog/product');
        $select = $this->_getWriteAdapter()->select()
            ->from(['si' => $itemTable])
            ->where('stock_id=?', $stock->getId())
            ->where('product_id IN(?)', $productIds)
            ->forUpdate($lockRows);
        $rows = $this->_getWriteAdapter()->fetchAll($select);

        // Add type_id to result using separate select without FOR UPDATE instead
        // of a join which causes only an S lock on catalog_product_entity rather
        // than an X lock. An X lock on a table causes an S lock on all foreign keys
        // so using a separate query here significantly reduces the number of
        // unnecessarily locked rows in other tables, thereby avoiding deadlocks.
        $select = $this->_getWriteAdapter()->select()
            ->from($productTable, ['entity_id', 'type_id'])
            ->where('entity_id IN(?)', $productIds);
        $typeIds = $this->_getWriteAdapter()->fetchPairs($select);
        foreach ($rows as &$row) {
            $row['type_id'] = $typeIds[$row['product_id']];
        }
        return $rows;
    }

    /**
     * Correct particular stock products qty based on operator
     *
     * @param Mage_CatalogInventory_Model_Stock $stock
     * @param array $productQtys
     * @param string $operator +/-
     * @return $this
     */
    public function correctItemsQty($stock, $productQtys, $operator = '-')
    {
        if (empty($productQtys)) {
            return $this;
        }

        $adapter = $this->_getWriteAdapter();
        $conditions = [];
        foreach ($productQtys as $productId => $qty) {
            $case = $adapter->quoteInto('?', $productId);
            $result = $adapter->quoteInto("qty{$operator}?", $qty);
            $conditions[$case] = $result;
        }

        $value = $adapter->getCaseSql('product_id', $conditions, 'qty');

        $where = [
            'product_id IN (?)' => array_keys($productQtys),
            'stock_id = ?'      => $stock->getId(),
        ];

        $adapter->beginTransaction();
        try {
            $adapter->update($this->getTable('cataloginventory/stock_item'), ['qty' => $value], $where);
            $adapter->commit();
        } catch (Exception $e) {
            $adapter->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * add join to select only in stock products
     *
     * @param Mage_Catalog_Model_Resource_Product_Link_Product_Collection $collection
     * @return $this
     */
    public function setInStockFilterToCollection($collection)
    {
        $manageStock = Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK);
        $cond = [
            '{{table}}.use_config_manage_stock = 0 AND {{table}}.manage_stock=1 AND {{table}}.is_in_stock=1',
            '{{table}}.use_config_manage_stock = 0 AND {{table}}.manage_stock=0',
        ];

        if ($manageStock) {
            $cond[] = '{{table}}.use_config_manage_stock = 1 AND {{table}}.is_in_stock=1';
        } else {
            $cond[] = '{{table}}.use_config_manage_stock = 1';
        }

        $collection->joinField(
            'inventory_in_stock',
            'cataloginventory/stock_item',
            'is_in_stock',
            'product_id=entity_id',
            '(' . implode(') OR (', $cond) . ')',
        );
        return $this;
    }

    /**
     * Load some inventory configuration settings
     *
     */
    protected function _initConfig()
    {
        if (!$this->_isConfig) {
            $configMap = [
                '_isConfigManageStock'  => Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK,
                '_isConfigBackorders'   => Mage_CatalogInventory_Model_Stock_Item::XML_PATH_BACKORDERS,
                '_configMinQty'         => Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MIN_QTY,
                '_configNotifyStockQty' => Mage_CatalogInventory_Model_Stock_Item::XML_PATH_NOTIFY_STOCK_QTY,
            ];

            foreach ($configMap as $field => $const) {
                $this->$field = Mage::getStoreConfig($const);
            }

            $this->_isConfig = true;
            $this->_stock = Mage::getModel('cataloginventory/stock');
            $this->_configTypeIds = array_keys(Mage::helper('cataloginventory')->getIsQtyTypeIds(true));
        }
    }

    /**
     * Set items out of stock basing on their quantities and config settings
     *
     */
    public function updateSetOutOfStock()
    {
        $this->_initConfig();
        $adapter = $this->_getWriteAdapter();
        $values  = [
            'is_in_stock'                  => 0,
            'stock_status_changed_auto'    => 1,
        ];

        $select = $adapter->select()
            ->from($this->getTable('catalog/product'), 'entity_id')
            ->where('type_id IN(?)', $this->_configTypeIds);

        $where = sprintf(
            'stock_id = %1$d'
            . ' AND is_in_stock = 1'
            . ' AND ((use_config_manage_stock = 1 AND 1 = %2$d) OR (use_config_manage_stock = 0 AND manage_stock = 1))'
            . ' AND ((use_config_backorders = 1 AND %3$d = %4$d) OR (use_config_backorders = 0 AND backorders = %3$d))'
            . ' AND ((use_config_min_qty = 1 AND qty <= %5$d) OR (use_config_min_qty = 0 AND qty <= min_qty))'
            . ' AND product_id IN (%6$s)',
            $this->_stock->getId(),
            $this->_isConfigManageStock,
            Mage_CatalogInventory_Model_Stock::BACKORDERS_NO,
            $this->_isConfigBackorders,
            $this->_configMinQty,
            $select->assemble(),
        );

        $adapter->update($this->getTable('cataloginventory/stock_item'), $values, $where);
    }

    /**
     * Set items in stock basing on their quantities and config settings
     *
     */
    public function updateSetInStock()
    {
        $this->_initConfig();
        $adapter = $this->_getWriteAdapter();
        $values  = [
            'is_in_stock'   => 1,
        ];

        $select = $adapter->select()
            ->from($this->getTable('catalog/product'), 'entity_id')
            ->where('type_id IN(?)', $this->_configTypeIds);

        $where = sprintf(
            'stock_id = %1$d'
            . ' AND is_in_stock = 0'
            . ' AND stock_status_changed_auto = 1'
            . ' AND ((use_config_manage_stock = 1 AND 1 = %2$d) OR (use_config_manage_stock = 0 AND manage_stock = 1))'
            . ' AND ((use_config_min_qty = 1 AND qty > %3$d) OR (use_config_min_qty = 0 AND qty > min_qty))'
            . ' AND product_id IN (%4$s)',
            $this->_stock->getId(),
            $this->_isConfigManageStock,
            $this->_configMinQty,
            $select->assemble(),
        );

        $adapter->update($this->getTable('cataloginventory/stock_item'), $values, $where);
    }

    /**
     * Update items low stock date basing on their quantities and config settings
     *
     */
    public function updateLowStockDate()
    {
        $this->_initConfig();

        $adapter = $this->_getWriteAdapter();
        $condition = $adapter->quoteInto(
            '(use_config_notify_stock_qty = 1 AND qty < ?)',
            $this->_configNotifyStockQty,
        ) . ' OR (use_config_notify_stock_qty = 0 AND qty < notify_stock_qty)';
        $currentDbTime = $adapter->quoteInto('?', $this->formatDate(true));
        $conditionalDate = $adapter->getCheckSql($condition, $currentDbTime, 'NULL');

        $value  = [
            'low_stock_date' => new Zend_Db_Expr($conditionalDate),
        ];

        $select = $adapter->select()
            ->from($this->getTable('catalog/product'), 'entity_id')
            ->where('type_id IN(?)', $this->_configTypeIds);

        $where = sprintf(
            'stock_id = %1$d'
            . ' AND ((use_config_manage_stock = 1 AND 1 = %2$d) OR (use_config_manage_stock = 0 AND manage_stock = 1))'
            . ' AND product_id IN (%3$s)',
            $this->_stock->getId(),
            $this->_isConfigManageStock,
            $select->assemble(),
        );

        $adapter->update($this->getTable('cataloginventory/stock_item'), $value, $where);
    }

    /**
     * Add low stock filter to product collection
     *
     * @param array $fields
     * @return $this
     */
    public function addLowStockFilter(Mage_Catalog_Model_Resource_Product_Collection $collection, $fields)
    {
        $this->_initConfig();
        $adapter = $collection->getSelect()->getAdapter();
        $qtyIf = $adapter->getCheckSql(
            'invtr.use_config_notify_stock_qty',
            $this->_configNotifyStockQty,
            'invtr.notify_stock_qty',
        );
        $conditions = [
            [
                $adapter->prepareSqlCondition('invtr.use_config_manage_stock', 1),
                $adapter->prepareSqlCondition((string) $this->_isConfigManageStock, 1),
                $adapter->prepareSqlCondition('invtr.qty', ['lt' => $qtyIf]),
            ],
            [
                $adapter->prepareSqlCondition('invtr.use_config_manage_stock', 0),
                $adapter->prepareSqlCondition('invtr.manage_stock', 1),
            ],
        ];

        $where = [];
        foreach ($conditions as $k => $part) {
            $where[$k] = implode(' ' . Zend_Db_Select::SQL_AND . ' ', $part);
        }

        $where = $adapter->prepareSqlCondition('invtr.low_stock_date', ['notnull' => true])
            . ' ' . Zend_Db_Select::SQL_AND . ' (('
            . implode(') ' . Zend_Db_Select::SQL_OR . ' (', $where)
            . '))';

        $collection->joinTable(
            ['invtr' => 'cataloginventory/stock_item'],
            'product_id = entity_id',
            $fields,
            $where,
        );
        return $this;
    }
}
