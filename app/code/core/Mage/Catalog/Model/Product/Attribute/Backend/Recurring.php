<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Backend for recurring profile parameter
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
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
