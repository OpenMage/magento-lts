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
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product stock qty block for abstract composite product
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 */
abstract class Mage_CatalogInventory_Block_Stockqty_Composite extends Mage_CatalogInventory_Block_Stockqty_Default
{
    /**
     * Child products cache
     *
     * @var array
     */
    private $_childProducts;

    /**
     * Retrieve child products
     *
     * @return array
     */
    abstract protected function _getChildProducts();

    /**
     * Retrieve child products (using cache)
     *
     * @return array
     */
    public function getChildProducts()
    {
        if ($this->_childProducts === null) {
            $this->_childProducts = $this->_getChildProducts();
        }
        return $this->_childProducts;
    }

    /**
     * Retrieve product stock qty
     *
     * @param Mage_Catalog_Model_Product $product
     * @return float
     */
    public function getProductStockQty($product)
    {
        return $product->getStockItem()->getStockQty();
    }

    /**
     * Retrieve id of details table placeholder in template
     *
     * @return string
     */
    public function getDetailsPlaceholderId()
    {
        return $this->getPlaceholderId() . '-details';
    }
}
