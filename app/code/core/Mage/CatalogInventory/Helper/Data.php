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
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_CatalogInventory
 */
class Mage_CatalogInventory_Helper_Data extends Mage_Core_Helper_Abstract
{
    public const XML_PATH_SHOW_OUT_OF_STOCK    = 'cataloginventory/options/show_out_of_stock';
    public const XML_PATH_ITEM_AUTO_RETURN     = 'cataloginventory/item_options/auto_return';
    /**
     * Path to configuration option 'Display product stock status'
     */
    public const XML_PATH_DISPLAY_PRODUCT_STOCK_STATUS = 'cataloginventory/options/display_product_stock_status';

    /**
     * Error codes, that Catalog Inventory module can set to quote or quote items
     */
    public const ERROR_QTY =               1;
    public const ERROR_QTY_INCREMENTS =    2;

    protected $_moduleName = 'Mage_CatalogInventory';

    /**
     * All product types registry in scope of quantity availability
     *
     * @var array
     */
    protected static $_isQtyTypeIds;

    /**
     * Check if quantity defined for specified product type
     *
     * @param string $productTypeId
     * @return bool
     */
    public function isQty($productTypeId)
    {
        $this->getIsQtyTypeIds();
        return self::$_isQtyTypeIds[$productTypeId] ?? false;
    }

    /**
     * Get all registered product type ids and if quantity is defined for them
     *
     * @param bool $filter
     * @return array
     */
    public function getIsQtyTypeIds($filter = null)
    {
        if (self::$_isQtyTypeIds === null) {
            self::$_isQtyTypeIds = [];
            $productTypesXml = Mage::getConfig()->getNode('global/catalog/product/type');
            foreach ($productTypesXml->children() as $typeId => $configXml) {
                self::$_isQtyTypeIds[$typeId] = (bool) $configXml->is_qty;
            }
        }
        if ($filter === null) {
            return self::$_isQtyTypeIds;
        }
        $result = self::$_isQtyTypeIds;
        foreach ($result as $key => $value) {
            if ($value !== $filter) {
                unset($result[$key]);
            }
        }
        return $result;
    }

    /**
     * Retrieve inventory item options (used in config)
     *
     * @return array
     */
    public function getConfigItemOptions()
    {
        return [
            'min_qty',
            'backorders',
            'min_sale_qty',
            'max_sale_qty',
            'notify_stock_qty',
            'manage_stock',
            'enable_qty_increments',
            'qty_increments',
            'is_decimal_divided',
        ];
    }

    /**
     * Display out of stock products option
     *
     * @return bool
     */
    public function isShowOutOfStock()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_SHOW_OUT_OF_STOCK);
    }

    /**
     * Check if creditmemo items auto return option is enabled
     * @return bool
     */
    public function isAutoReturnEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ITEM_AUTO_RETURN);
    }

    /**
     * Get 'Display product stock status' option value
     * Shows if it is necessary to show product stock status ('in stock'/'out of stock')
     *
     * @return bool
     */
    public function isDisplayProductStockStatus()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_DISPLAY_PRODUCT_STOCK_STATUS);
    }
}
