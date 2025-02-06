<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Catalog
 */

/**
 * Backend for recurring profile parameter
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Attribute_Backend_Recurring extends Mage_Eav_Model_Entity_Attribute_Backend_Serialized
{
    /**
     * Serialize or remove before saving
     * @param Mage_Catalog_Model_Product $product
     * @return $this
     */
    public function beforeSave($product)
    {
        if ($product->hasIsRecurring()) {
            if ($product->isRecurring()) {
                parent::beforeSave($product);
            } else {
                $product->unsRecurringProfile();
            }
        }
        return $this;
    }

    /**
     * Unserialize or remove on failure
     * @param Mage_Catalog_Model_Product $product
     * @return $this
     */
    protected function _unserialize(Varien_Object $product)
    {
        if ($product->hasIsRecurring()) {
            if ($product->isRecurring()) {
                parent::_unserialize($product);
            } else {
                $product->unsRecurringProfile();
            }
        }
        return $this;
    }
}
