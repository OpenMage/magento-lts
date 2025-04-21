<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Clone model for media images related config fields
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Clone_Media_Image extends Mage_Core_Model_Config_Data
{
    /**
     * Get fields prefixes
     * @return array
     */
    public function getPrefixes()
    {
        // use cached eav config
        $entityTypeId = Mage::getSingleton('eav/config')->getEntityType(Mage_Catalog_Model_Product::ENTITY)->getId();

        /** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Collection $collection */
        $collection = Mage::getResourceModel('catalog/product_attribute_collection');
        $collection->setEntityTypeFilter($entityTypeId);
        $collection->setFrontendInputTypeFilter('media_image');

        $prefixes = [];

        foreach ($collection as $attribute) {
            /** @var Mage_Eav_Model_Entity_Attribute $attribute */
            $prefixes[] = [
                'field' => $attribute->getAttributeCode() . '_',
                'label' => $attribute->getFrontend()->getLabel(),
            ];
        }

        return $prefixes;
    }
}
