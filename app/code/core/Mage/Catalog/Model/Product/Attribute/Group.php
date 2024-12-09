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
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Attribute_Group extends Mage_Eav_Model_Entity_Attribute_Group
{
    /**
     * Check if group contains system attributes
     *
     * @return bool
     */
    public function hasSystemAttributes()
    {
        $result = false;
        /** @var Mage_Catalog_Model_Resource_Product_Attribute_Collection $attributesCollection */
        $attributesCollection = Mage::getResourceModel('catalog/product_attribute_collection');
        $attributesCollection->setAttributeGroupFilter($this->getId());
        foreach ($attributesCollection as $attribute) {
            if (!$attribute->getIsUserDefined()) {
                $result = true;
                break;
            }
        }
        return $result;
    }

    /**
     * Check if contains attributes used in the configurable products
     *
     * @return bool
     */
    public function hasConfigurableAttributes()
    {
        $result = false;
        /** @var Mage_Catalog_Model_Resource_Product_Attribute_Collection $attributesCollection */
        $attributesCollection = Mage::getResourceModel('catalog/product_attribute_collection');
        $attributesCollection->setAttributeGroupFilter($this->getId());
        foreach ($attributesCollection as $attribute) {
            if ($attribute->getIsConfigurable()) {
                $result = true;
                break;
            }
        }
        return $result;
    }
}
