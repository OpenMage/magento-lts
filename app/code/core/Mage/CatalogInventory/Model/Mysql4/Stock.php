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
 * @package     Mage_CatalogInventory
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Stock resource model
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogInventory_Model_Mysql4_Stock extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_isConfig;
    protected $_isConfigManageStock;
    protected $_isConfigBackorders;
    protected $_configMinQty;
    protected $_configTypeIds;
    protected $_configNotifyStockQty;
    protected $_stock;

    protected function  _construct()
    {
        $this->_init('cataloginventory/stock', 'stock_id');
    }

    public function lockProductItems($stock, $productIds)
    {
        $itemTable = $this->getTable('cataloginventory/stock_item');
        $select = $this->_getReadAdapter()->select()
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
     * add join to select only in stock products
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Link_Product_Collection $collection
     * @return Mage_CatalogInventory_Model_Mysql4_Stock
     */
    public function setInStockFilterToCollection( $collection)
    {
        $manageStock = Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK);
        $cond = array(
            '{{table}}.use_config_manage_stock = 0 AND {{table}}.manage_stock=1 AND {{table}}.is_in_stock=1',
            '{{table}}.use_config_manage_stock = 0 AND {{table}}.manage_stock=0',
        );

        if ($manageStock) {
            $cond[] = '{{table}}.use_config_manage_stock = 1 AND {{table}}.is_in_stock=1';
        }
        else {
            $cond[] = '{{table}}.use_config_manage_stock = 1';
        }

        $collection->joinField(
            'inventory_in_stock',
            'cataloginventory/stock_item',
            'is_in_stock',
            'product_id=entity_id',
            '('.join(') OR (', $cond) . ')'
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
            $this->_isConfig = true;
            $this->_isConfigManageStock  = (int)Mage::getStoreConfigFlag(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK);
            $this->_isConfigBackorders   = (int)Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_BACKORDERS);
            $this->_configMinQty         = (int)Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MIN_QTY);
            $this->_configNotifyStockQty = (int)Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_NOTIFY_STOCK_QTY);
            $this->_configTypeIds        = array_keys(Mage::helper('catalogInventory')->getIsQtyTypeIds(true));
            $this->_stock                = Mage::getModel('cataloginventory/stock');
        }
    }

    /**
     * Set items out of stock basing on their quantities and config settings
     *
     */
    public function updateSetOutOfStock()
    {
        $this->_initConfig();
        $this->_getWriteAdapter()->update($this->getTable('cataloginventory/stock_item'),
            array('is_in_stock' => 0, 'stock_status_changed_automatically' => 1),
            sprintf('stock_id = %d
                AND is_in_stock = 1
                AND (use_config_manage_stock = 1 AND 1 = %d OR use_config_manage_stock = 0 AND manage_stock = 1)
                AND (use_config_backorders = 1 AND %d = %d OR use_config_backorders = 0 AND backorders = %d)
                AND (use_config_min_qty = 1 AND qty <= %d OR use_config_min_qty = 0 AND qty <= min_qty)
                AND product_id IN (SELECT entity_id FROM %s WHERE type_id IN (%s))',
                $this->_stock->getId(),
                $this->_isConfigManageStock,
                Mage_CatalogInventory_Model_Stock::BACKORDERS_NO, $this->_isConfigBackorders, Mage_CatalogInventory_Model_Stock::BACKORDERS_NO,
                $this->_configMinQty,
                $this->getTable('catalog/product'), $this->_getWriteAdapter()->quote($this->_configTypeIds)
        ));
    }

    /**
     * Set items in stock basing on their quantities and config settings
     *
     */
    public function updateSetInStock()
    {
        $this->_initConfig();
        $this->_getWriteAdapter()->update($this->getTable('cataloginventory/stock_item'),
            array('is_in_stock' => 1),
            sprintf('stock_id = %d
                AND is_in_stock = 0
                AND stock_status_changed_automatically = 1
                AND (use_config_manage_stock = 1 AND 1 = %d OR use_config_manage_stock = 0 AND manage_stock = 1)
                AND (use_config_min_qty = 1 AND qty > %d OR use_config_min_qty = 0 AND qty > min_qty)
                AND product_id IN (SELECT entity_id FROM %s WHERE type_id IN (%s))',
                $this->_stock->getId(),
                $this->_isConfigManageStock,
                $this->_configMinQty,
                $this->getTable('catalog/product'), $this->_getWriteAdapter()->quote($this->_configTypeIds)
        ));
    }

    /**
     * Update items low stock date basing on their quantities and config settings
     *
     */
    public function updateLowStockDate()
    {
        $nowUTC = Mage::app()->getLocale()->date(null, null, null, false)->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        $this->_initConfig();
        $this->_getWriteAdapter()->update($this->getTable('cataloginventory/stock_item'),
            array('low_stock_date' => new Zend_Db_Expr(sprintf('CASE
                WHEN (use_config_notify_stock_qty = 1 AND qty < %d) OR (use_config_notify_stock_qty = 0 AND qty < notify_stock_qty)
                THEN %s ELSE NULL
                END
                ', $this->_configNotifyStockQty, $this->_getWriteAdapter()->quote($nowUTC)
            ))),
            sprintf('stock_id = %d
                AND (use_config_manage_stock = 1 AND 1 = %d OR use_config_manage_stock = 0 AND manage_stock = 1)
                AND product_id IN (SELECT entity_id FROM %s WHERE type_id IN (%s))',
                $this->_stock->getId(),
                $this->_isConfigManageStock,
                $this->getTable('catalog/product'), $this->_getWriteAdapter()->quote($this->_configTypeIds)
        ));
    }
}
