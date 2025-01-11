<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
