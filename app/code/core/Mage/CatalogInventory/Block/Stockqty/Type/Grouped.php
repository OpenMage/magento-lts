<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogInventory
 */

/**
 * Product stock qty block for grouped product type
 *
 * @package    Mage_CatalogInventory
 */
class Mage_CatalogInventory_Block_Stockqty_Type_Grouped extends Mage_CatalogInventory_Block_Stockqty_Composite
{
    /**
     * Retrieve child products
     *
     * @return array
     */
    protected function _getChildProducts()
    {
        /** @var Mage_Catalog_Model_Product_Type_Grouped $productType */
        $productType = $this->_getProduct()->getTypeInstance(true);
        return $productType->getAssociatedProducts($this->_getProduct());
    }
}
