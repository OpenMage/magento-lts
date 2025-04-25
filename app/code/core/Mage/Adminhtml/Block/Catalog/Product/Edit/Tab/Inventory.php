<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product inventory data
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Inventory extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/tab/inventory.phtml');
    }

    public function getBackordersOption()
    {
        if ($this->isModuleEnabled('Mage_CatalogInventory', 'catalog')) {
            return Mage::getSingleton('cataloginventory/source_backorders')->toOptionArray();
        }

        return [];
    }

    /**
     * Retrieve stock option array
     *
     * @return array
     */
    public function getStockOption()
    {
        if ($this->isModuleEnabled('Mage_CatalogInventory', 'catalog')) {
            return Mage::getSingleton('cataloginventory/source_stock')->toOptionArray();
        }

        return [];
    }

    /**
     * Return current product instance
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('product');
    }

    /**
     * Retrieve Catalog Inventory  Stock Item Model
     *
     * @return Mage_CatalogInventory_Model_Stock_Item
     */
    public function getStockItem()
    {
        return $this->getProduct()->getStockItem();
    }

    public function isConfigurable()
    {
        return $this->getProduct()->isConfigurable();
    }

    public function getFieldValue($field)
    {
        if ($this->getStockItem()) {
            return $this->getStockItem()->getDataUsingMethod($field);
        }

        return Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_ITEM . $field);
    }

    public function getFieldValueAsFloat(string $field): float
    {
        return (float) $this->getFieldValue($field);
    }

    public function getConfigFieldValue($field)
    {
        if ($this->getStockItem()) {
            if ($this->getStockItem()->getData('use_config_' . $field) == 0) {
                return $this->getStockItem()->getData($field);
            }
        }

        return Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_ITEM . $field);
    }

    public function getDefaultConfigValue($field)
    {
        return Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_ITEM . $field);
    }

    /**
     * Is readonly stock
     *
     * @return bool
     */
    public function isReadonly()
    {
        return $this->getProduct()->getInventoryReadonly();
    }

    public function isNew()
    {
        if ($this->getProduct()->getId()) {
            return false;
        }
        return true;
    }

    public function getFieldSuffix()
    {
        return 'product';
    }

    /**
     * Check Whether product type can have fractional quantity or not
     *
     * @return bool
     */
    public function canUseQtyDecimals()
    {
        return $this->getProduct()->getTypeInstance()->canUseQtyDecimals();
    }

    /**
     * Check if product type is virtual
     *
     * @return bool
     */
    public function isVirtual()
    {
        return $this->getProduct()->getTypeInstance()->isVirtual();
    }
}
