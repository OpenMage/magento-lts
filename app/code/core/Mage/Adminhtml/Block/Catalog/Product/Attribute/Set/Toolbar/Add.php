<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Toolbar_Add extends Mage_Adminhtml_Block_Template
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->setTemplate('catalog/product/attribute/set/toolbar/add.phtml');
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('catalog')->__('Save Attribute Set'),
                    'onclick'   => "if (addSet.submit()) disableElements('save');",
                    'class' => 'save',
                ]),
        );
        $this->setChild(
            'back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('catalog')->__('Back'),
                    'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getUrl('*/*/')),
                    'class' => 'back',
                ]),
        );

        $this->setChild(
            'setForm',
            $this->getLayout()->createBlock('adminhtml/catalog_product_attribute_set_main_formset'),
        );
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    protected function _getHeader()
    {
        return Mage::helper('catalog')->__('Add New Attribute Set');
    }

    /**
     * @return string
     */
    protected function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    /**
     * @return string
     */
    protected function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    /**
     * @return string
     */
    protected function getFormHtml()
    {
        return $this->getChildHtml('setForm');
    }

    protected function getFormId()
    {
        return $this->getChild('setForm')->getForm()->getId();
    }
}
