<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
 */

/**
 * Catalog product price block
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Block_Product_View_Price extends Mage_Core_Block_Template
{
    /**
      * @return mixed
      */
    public function getPrice()
    {
        $product = Mage::registry('product');
        /*if($product->isConfigurable()) {
           $price = $product->getCalculatedPrice((array)$this->getRequest()->getParam('super_attribute', array()));
           return Mage::app()->getStore()->formatPrice($price);
        }*/

        return $product->getFormatedPrice();
    }
}
