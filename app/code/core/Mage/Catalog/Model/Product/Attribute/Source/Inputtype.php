<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product attribute source input types
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
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
                'label' => Mage::helper('catalog')->__('Price')
            ],
            [
                'value' => 'media_image',
                'label' => Mage::helper('catalog')->__('Media Image')
            ]
        ];

        $response = new Varien_Object();
        $response->setTypes([]);
        Mage::dispatchEvent('adminhtml_product_attribute_types', ['response'=>$response]);
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
