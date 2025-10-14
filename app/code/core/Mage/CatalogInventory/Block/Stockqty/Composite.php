<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogInventory
 */

/**
 * Product stock qty block for abstract composite product
 *
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
