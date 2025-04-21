<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ConfigurableSwatches
 */

/**
 * @package    Mage_ConfigurableSwatches
 */
class Mage_ConfigurableSwatches_Model_System_Config_Source_Catalog_Product_Configattribute
{
    /**
     * Attributes array
     *
     * @var null|array
     */
    protected $_attributes = null;

    /**
     * Retrieve attributes as array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (is_null($this->_attributes)) {
            $attrCollection = Mage::getResourceModel('catalog/product_attribute_collection')
                ->addVisibleFilter()
                ->setFrontendInputTypeFilter('select')
                ->addFieldToFilter('additional_table.is_configurable', 1)
                ->addFieldToFilter('additional_table.is_visible', 1)
                ->addFieldToFilter('main_table.is_user_defined', 1)
                ->setOrder('frontend_label', Varien_Data_Collection::SORT_ORDER_ASC);

            $this->_attributes = [];
            /** @var Mage_Eav_Model_Attribute $attribute */
            foreach ($attrCollection as $attribute) {
                $this->_attributes[] = [
                    'label' => $attribute->getFrontendLabel(),
                    'value' => $attribute->getId(),
                ];
            }
        }
        return $this->_attributes;
    }
}
