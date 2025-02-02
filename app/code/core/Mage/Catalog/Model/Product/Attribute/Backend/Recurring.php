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
