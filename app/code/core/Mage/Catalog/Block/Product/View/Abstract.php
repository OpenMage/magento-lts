<?php
/**
 * Product view abstract block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Catalog
 */
abstract class Mage_Catalog_Block_Product_View_Abstract extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * Retrieve product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        $product = parent::getProduct();
        if (is_null($product->getTypeInstance(true)->getStoreFilter($product))) {
            $product->getTypeInstance(true)->setStoreFilter(Mage::app()->getStore(), $product);
        }

        return $product;
    }
}
