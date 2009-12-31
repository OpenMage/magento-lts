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
 * Stock item collection resource model
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogInventory_Model_Mysql4_Stock_Item_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('cataloginventory/stock_item');
    }

    /**
     * Add stock filter to collection
     *
     * @param   mixed $stock
     * @return  Mage_CatalogInventory_Model_Mysql4_Stock_Item_Collection
     */
    public function addStockFilter($stock)
    {
        if ($stock instanceof Mage_CatalogInventory_Model_Stock) {
            $this->addFieldToFilter('main_table.stock_id', $stock->getId());
        }
        else {
            $this->addFieldToFilter('main_table.stock_id', $stock);
        }
        return $this;
    }

    /**
     * Add product filter to collection
     *
     * @param   mixed $products
     * @return  Mage_CatalogInventory_Model_Mysql4_Stock_Item_Collection
     */
    public function addProductsFilter($products)
    {
        $productIds = array();
        foreach ($products as $product) {
            if ($product instanceof Mage_Catalog_Model_Product) {
                $productIds[] = $product->getId();
            }
            else {
                $productIds[] = $product;
            }
        }
        if (empty($productIds)) {
            $productIds[] = false;
            $this->_setIsLoaded(true);
        }
        $this->addFieldToFilter('main_table.product_id', array('in'=>$productIds));
        return $this;
    }

    /**
     * Join Stock Status to collection
     *
     * @param int $storeId
     * @return Mage_CatalogInventory_Model_Mysql4_Stock_Item_Collection
     */
    public function joinStockStatus($storeId = null)
    {
        $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
        $this->getSelect()->joinLeft(
            array('status_table' => $this->getTable('cataloginventory/stock_status')),
            '`main_table`.`product_id`=`status_table`.`product_id`'
                . ' AND `main_table`.`stock_id`=`status_table`.`stock_id`'
                . $this->getConnection()->quoteInto(' AND `status_table`.`website_id`=?', $websiteId),
            array('stock_status')
        );

        return $this;
    }

    public function addManagedFilter($isStockManagedInConfig)
    {
        if ($isStockManagedInConfig) {
            $this->getSelect()->where('(manage_stock = 1 OR use_config_manage_stock = 1)');
        } else {
            $this->addFieldToFilter('manage_stock', 1);
        }

        return $this;
    }

    public function addQtyFilter($comparsionMethod, $qty)
    {
        $allowedMethods = array('<', '>', '=', '<=', '>=', '<>');
        if (!in_array($comparsionMethod, $allowedMethods)) {
            Mage::throwException(Mage::helper('cataloginventory')->__('%s is not correct comparsion method.', $comparsionMethod));
        }
        $this->getSelect()->where("main_table.qty {$comparsionMethod} ?", $qty);
        return $this;
    }

    /**
     * Load data
     *
     * @return  Varien_Data_Collection_Db
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if (!$this->isLoaded()) {
            $this->getSelect()->joinInner(array('_products_table' => $this->getTable('catalog/product')),
                'main_table.product_id=_products_table.entity_id', 'type_id'
            );
        }
        return parent::load($printQuery, $logQuery);
    }
}
