<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Product attribute source input types
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Attribute_Source_Inputtype extends Mage_Eav_Model_Adminhtml_System_Config_Source_Inputtype
{
    /**
     * Get product input types as option array
     * @return array
     */
    public function toOptionArray()
    {
        $inputTypes = [
            [
                'value' => 'price',
                'label' => Mage::helper('catalog')->__('Price'),
            ],
            [
                'value' => 'media_image',
                'label' => Mage::helper('catalog')->__('Media Image'),
            ],
        ];

        $response = new Varien_Object();
        $response->setTypes([]);
        Mage::dispatchEvent('adminhtml_product_attribute_types', ['response' => $response]);
        $_disabledTypes = [];
        $_hiddenFields = [];
        foreach ($response->getTypes() as $type) {
            $inputTypes[] = $type;
            if (isset($type['hide_fields'])) {
                $_hiddenFields[$type['value']] = $type['hide_fields'];
            }
            if (isset($type['disabled_types'])) {
                $_disabledTypes[$type['value']] = $type['disabled_types'];
            }
        }

        if (Mage::registry('attribute_type_hidden_fields') === null) {
            Mage::register('attribute_type_hidden_fields', $_hiddenFields);
        }
        if (Mage::registry('attribute_type_disabled_types') === null) {
            Mage::register('attribute_type_disabled_types', $_disabledTypes);
        }

        return array_merge(parent::toOptionArray(), $inputTypes);
    }
}
