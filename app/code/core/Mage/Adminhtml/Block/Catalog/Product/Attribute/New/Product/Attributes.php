<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Product attributes tab
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Attribute_New_Product_Attributes extends Mage_Adminhtml_Block_Catalog_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        /**
         * Initialize product object as form property
         * for using it in elements generation
         */
        $form->setDataObject(Mage::registry('product'));

        $fieldset = $form->addFieldset('group_fields', []);

        $attributes = $this->getGroupAttributes();

        $this->_setFieldset($attributes, $fieldset, ['gallery']);

        $values = Mage::registry('product')->getData();
        /**
         * Set attribute default values for new product
         */
        if (!Mage::registry('product')->getId()) {
            foreach ($attributes as $attribute) {
                if (!isset($values[$attribute->getAttributeCode()])) {
                    $values[$attribute->getAttributeCode()] = $attribute->getDefaultValue();
                }
            }
        }

        Mage::dispatchEvent('adminhtml_catalog_product_edit_prepare_form', ['form' => $form]);
        $form->addValues($values);
        $form->setFieldNameSuffix('product');
        $this->setForm($form);
        return $this;
    }

    protected function _getAdditionalElementTypes()
    {
        $result = [
            'price'   => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_price'),
            'image'   => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_image'),
            'boolean' => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_boolean'),
        ];

        $response = new Varien_Object();
        $response->setTypes([]);
        Mage::dispatchEvent('adminhtml_catalog_product_edit_element_types', ['response' => $response]);

        foreach ($response->getTypes() as $typeName => $typeClass) {
            $result[$typeName] = $typeClass;
        }

        return $result;
    }

    protected function _toHtml()
    {
        parent::_toHtml();
        return $this->getForm()->getElement('group_fields')->getChildrenHtml();
    }
}
