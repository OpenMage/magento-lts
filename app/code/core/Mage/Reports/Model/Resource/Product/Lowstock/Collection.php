<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * Product Low Stock Report Collection
 *
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Product_Lowstock_Collection extends Mage_Reports_Model_Resource_Product_Collection
{
    /**
     * CatalogInventory Stock Item Resource instance
     *
     * @var Mage_CatalogInventory_Model_Resource_Stock_Item
     */
    protected $_inventoryItemResource      = null;

    /**
     * Flag about is joined CatalogInventory Stock Item
     *
     * @var bool
     */
    protected $_inventoryItemJoined        = false;

    /**
     * Alias for CatalogInventory Stock Item Table
     *
     * @var string
     */
    protected $_inventoryItemTableAlias    = 'lowstock_inventory_item';

    /**
     * Retrieve CatalogInventory Stock Item Resource instance
     *
     * @return Mage_CatalogInventory_Model_Resource_Stock_Item
     */
    protected function _getInventoryItemResource()
    {
        if ($this->_inventoryItemResource === null) {
            $this->_inventoryItemResource = Mage::getResourceSingleton('cataloginventory/stock_item');
        }

        return $this->_inventoryItemResource;
    }

    /**
     * Retrieve CatalogInventory Stock Item Table
     *
     * @return string
     */
    protected function _getInventoryItemTable()
    {
        return $this->_getInventoryItemResource()->getMainTable();
    }

    /**
     * Retrieve CatalogInventory Stock Item Table Id field name
     *
     * @return string
     */
    protected function _getInventoryItemIdField()
    {
        return $this->_getInventoryItemResource()->getIdFieldName();
    }

    /**
     * Retrieve alias for CatalogInventory Stock Item Table
     *
     * @return string
     */
    protected function _getInventoryItemTableAlias()
    {
        return $this->_inventoryItemTableAlias;
    }

    /**
     * Add catalog inventory stock item field to select
     *
     * @param string $field
     * @param string $alias
     * @return $this
     */
    protected function _addInventoryItemFieldToSelect($field, $alias = null)
    {
        if (empty($alias)) {
            $alias = $field;
        }

        if (isset($this->_joinFields[$alias])) {
            return $this;
        }

        $this->_joinFields[$alias] = [
            'table' => $this->_getInventoryItemTableAlias(),
            'field' => $field,
        ];

        $this->getSelect()->columns([$alias => $field], $this->_getInventoryItemTableAlias());
        return $this;
    }

    /**
     * Retrieve catalog inventory stock item field correlation name
     *
     * @param string $field
     * @return string
     */
    protected function _getInventoryItemField($field)
    {
        return sprintf('%s.%s', $this->_getInventoryItemTableAlias(), $field);
    }

    /**
     * Join catalog inventory stock item table for further stock_item values filters
     *
     * @param array|string $fields
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function joinInventoryItem($fields = [])
    {
        if (!$this->_inventoryItemJoined) {
            $this->getSelect()->join(
                [$this->_getInventoryItemTableAlias() => $this->_getInventoryItemTable()],
                sprintf(
                    'e.%s = %s.product_id',
                    $this->getEntity()->getEntityIdField(),
                    $this->_getInventoryItemTableAlias(),
                ),
                [],
            );
            $this->_inventoryItemJoined = true;
        }

        if (!is_array($fields)) {
            if (empty($fields)) {
                $fields = [];
            } else {
                $fields = [$fields];
            }
        }

        foreach ($fields as $alias => $field) {
            if (!is_string($alias)) {
                $alias = null;
            }

            $this->_addInventoryItemFieldToSelect($field, $alias);
        }

        return $this;
    }

    /**
     * Add filter by product type(s)
     *
     * @param array|string $typeFilter
     * @return $this
     */
    public function filterByProductType($typeFilter)
    {
        if (!is_string($typeFilter) && !is_array($typeFilter)) {
            Mage::throwException(
                Mage::helper('catalog')->__('Wrong product type filter specified'),
            );
        }

        $this->addAttributeToFilter('type_id', $typeFilter);
        return $this;
    }

    /**
     * Add filter by product types from config
     * Only types witch has QTY parameter
     *
     * @return $this
     */
    public function filterByIsQtyProductTypes()
    {
        $this->filterByProductType(
            array_keys(array_filter(Mage::helper('cataloginventory')->getIsQtyTypeIds())),
        );
        return $this;
    }

    /**
     * Add Use Manage Stock Condition to collection
     *
     * @param null|int $storeId
     * @return $this
     */
    public function useManageStockFilter($storeId = null)
    {
        $this->joinInventoryItem();
        $manageStockExpr = $this->getConnection()->getCheckSql(
            $this->_getInventoryItemField('use_config_manage_stock') . ' = 1',
            (string) Mage::getStoreConfigAsInt(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK, $storeId),
            $this->_getInventoryItemField('manage_stock'),
        );
        $this->getSelect()->where($manageStockExpr . ' = ?', 1);
        return $this;
    }

    /**
     * Add Notify Stock Qty Condition to collection
     *
     * @param int $storeId
     * @return $this
     */
    public function useNotifyStockQtyFilter($storeId = null)
    {
        $this->joinInventoryItem(['qty']);
        $notifyStockExpr = $this->getConnection()->getCheckSql(
            $this->_getInventoryItemField('use_config_notify_stock_qty') . ' = 1',
            (string) Mage::getStoreConfigAsInt(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_NOTIFY_STOCK_QTY, $storeId),
            $this->_getInventoryItemField('notify_stock_qty'),
        );
        $this->getSelect()->where('qty < ?', $notifyStockExpr);
        return $this;
    }
}
