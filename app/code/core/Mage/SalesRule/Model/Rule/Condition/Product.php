<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_SalesRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product rule condition data model
 *
 * @category   Mage
 * @package    Mage_SalesRule
 */
class Mage_SalesRule_Model_Rule_Condition_Product extends Mage_Rule_Model_Condition_Product_Abstract
{
    /**
     * Add special attributes
     */
    protected function _addSpecialAttributes(array &$attributes)
    {
        parent::_addSpecialAttributes($attributes);
        $attributes['quote_item_qty'] = Mage::helper('salesrule')->__('Quantity in cart');
        $attributes['quote_item_price'] = Mage::helper('salesrule')->__('Price in cart');
        $attributes['quote_item_row_total'] = Mage::helper('salesrule')->__('Row total in cart');
    }

    /**
     * Validate Product Rule Condition
     *
     *
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = ($object instanceof Mage_Catalog_Model_Product) ? $object : $object->getProduct();
        if (!($product instanceof Mage_Catalog_Model_Product)) {
            $product = Mage::getModel('catalog/product')->load($object->getProductId());
        }

        $product
            ->setQuoteItemQty($object->getQty())
            ->setQuoteItemPrice($object->getPrice()) // possible bug: need to use $object->getBasePrice()
            ->setQuoteItemRowTotal($object->getBaseRowTotal());

        return parent::validate($product);
    }
}
