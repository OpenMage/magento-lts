<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
