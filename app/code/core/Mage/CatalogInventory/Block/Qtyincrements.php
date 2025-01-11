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
 * Product qty increments block
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 */
class Mage_CatalogInventory_Block_Qtyincrements extends Mage_Core_Block_Template
{
    /**
     * Qty Increments cache
     *
     * @var float|false
     */
    protected $_qtyIncrements;

    /**
     * Retrieve current product object
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Retrieve current product name
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->_getProduct()->getName();
    }

    /**
     * Retrieve product qty increments
     *
     * @return float|false
     */
    public function getProductQtyIncrements()
    {
        if ($this->_qtyIncrements === null) {
            $this->_qtyIncrements = $this->_getProduct()->getStockItem()->getQtyIncrements();
            if (!$this->_getProduct()->isSaleable()) {
                $this->_qtyIncrements = false;
            }
        }
        return $this->_qtyIncrements;
    }

    /**
     * Retrieve block cache tags
     *
     * @return array
     */
    public function getCacheTags()
    {
        return array_merge(parent::getCacheTags(), $this->_getProduct()->getCacheIdTags());
    }
}
