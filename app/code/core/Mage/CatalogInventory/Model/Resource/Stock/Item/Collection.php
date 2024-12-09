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
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Stock item collection resource model
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 */
class Mage_CatalogInventory_Model_Resource_Stock_Item_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('cataloginventory/stock_item');
    }

    /**
     * Add stock filter to collection
     *
     * @param mixed $stock
     * @return $this
     */
    public function addStockFilter($stock)
    {
        if ($stock instanceof Mage_CatalogInventory_Model_Stock) {
            $this->addFieldToFilter('main_table.stock_id', $stock->getId());
        } else {
            $this->addFieldToFilter('main_table.stock_id', $stock);
        }
        return $this;
    }

    /**
     * Add product filter to collection
     *
     * @param array|Mage_Catalog_Model_Product[] $products
     * @return $this
     */
    public function addProductsFilter($products)
    {
        $productIds = [];
        foreach ($products as $product) {
            if ($product instanceof Mage_Catalog_Model_Product) {
                $productIds[] = $product->getId();
            } else {
                $productIds[] = $product;
            }
        }
        if (empty($productIds)) {
            $productIds[] = false;
            $this->_setIsLoaded(true);
        }
        $this->addFieldToFilter('main_table.product_id', ['in' => $productIds]);
        return $this;
    }

    /**
     * Join Stock Status to collection
     *
     * @param int $storeId
     * @return $this
     */
    public function joinStockStatus($storeId = null)
    {
        $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
        $this->getSelect()->joinLeft(
            ['status_table' => $this->getTable('cataloginventory/stock_status')],
            'main_table.product_id=status_table.product_id'
                . ' AND main_table.stock_id=status_table.stock_id'
                . $this->getConnection()->quoteInto(' AND status_table.website_id=?', $websiteId),
            ['stock_status'],
        );

        return $this;
    }

    /**
     * Add Managed Stock products filter to collection
     *
     * @param bool $isStockManagedInConfig
     * @return $this
     */
    public function addManagedFilter($isStockManagedInConfig)
    {
        if ($isStockManagedInConfig) {
            $this->getSelect()->where('(manage_stock = 1 OR use_config_manage_stock = 1)');
        } else {
            $this->addFieldToFilter('manage_stock', 1);
        }

        return $this;
    }

    /**
     * Add filter by quantity to collection
     *
     * @param string $comparsionMethod
     * @param float $qty
     * @return $this
     */
    public function addQtyFilter($comparsionMethod, $qty)
    {
        $methods = [
            '<'  => 'lt',
            '>'  => 'gt',
            '='  => 'eq',
            '<=' => 'lteq',
            '>=' => 'gteq',
            '<>' => 'neq',
        ];
        if (!isset($methods[$comparsionMethod])) {
            Mage::throwException(
                Mage::helper('cataloginventory')->__('%s is not a correct comparsion method.', $comparsionMethod),
            );
        }

        return $this->addFieldToFilter('main_table.qty', [$methods[$comparsionMethod] => $qty]);
    }

    /**
     * @inheritDoc
     */
    protected function _initSelect()
    {
        return parent::_initSelect()->getSelect()
            ->join(
                ['cp_table' => $this->getTable('catalog/product')],
                'main_table.product_id = cp_table.entity_id',
                ['type_id'],
            );
    }
}
