<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml catalog product edit action attributes update tab block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Action_Attribute_Tab_Attributes extends Mage_Adminhtml_Block_Catalog_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _construct()
    {
        parent::_construct();
        $this->setShowGlobalIcon(true);
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        $this->setFormExcludedFieldList([
            'tier_price','gallery', 'media_gallery', 'recurring_profile', 'group_price',
        ]);
        Mage::dispatchEvent('adminhtml_catalog_product_form_prepare_excluded_field_list', ['object' => $this]);

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('fields', ['legend' => Mage::helper('catalog')->__('Attributes')]);
        $attributes = $this->getAttributes();
        /**
         * Initialize product object as form property
         * for using it in elements generation
         */
        $form->setDataObject(Mage::getModel('catalog/product'));
        $this->_setFieldset($attributes, $fieldset, $this->getFormExcludedFieldList());
        $form->setFieldNameSuffix('attributes');
        $this->setForm($form);
        return $this;
    }

    /**
     * Retrieve attributes for product massupdate
     *
     * @return array
     */
    public function getAttributes()
    {
        /** @var Mage_Adminhtml_Helper_Catalog_Product_Edit_Action_Attribute $helper */
        $helper = $this->helper('adminhtml/catalog_product_edit_action_attribute');
        return $helper->getAttributes()->getItems();
    }

    /**
     * Additional element types for product attributes
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return [
            'price' => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_price'),
            'weight' => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_weight'),
            'image' => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_image'),
            'boolean' => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_boolean'),
        ];
    }

    /**
     * Custom additional element html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getAdditionalElementHtml($element)
    {
        // Add name attribute to checkboxes that correspond to multiselect elements
        $nameAttributeHtml = ($element->getExtType() === 'multiple') ? 'name="' . $element->getId() . '_checkbox"'
            : '';
        return '<span class="attribute-change-checkbox"><input type="checkbox" id="' . $element->getId()
             . '-checkbox" ' . $nameAttributeHtml . ' onclick="toogleFieldEditMode(this, \'' . $element->getId()
             . '\')" /><label for="' . $element->getId() . '-checkbox">' . Mage::helper('catalog')->__('Change')
             . '</label></span>
                <script type="text/javascript">initDisableFields(\'' . $element->getId() . '\')</script>';
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('catalog')->__('Attributes');
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('catalog')->__('Attributes');
    }

    /**
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return false
     */
    public function isHidden()
    {
        return false;
    }
}
