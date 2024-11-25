<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Create Configuranle procuct Settings Tab Block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Settings extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare block children and data
     */
    protected function _prepareLayout()
    {
        $onclick = "setSuperSettings('" . $this->getContinueUrl() . "','attribute-checkbox', 'attributes')";
        $this->setChild(
            'continue_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('catalog')->__('Continue'),
                    'onclick'   => $onclick,
                    'class'     => 'save',
                ]),
        );

        $backButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData([
                'label'     => Mage::helper('catalog')->__('Back'),
                'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getBackUrl()),
                'class'     => 'back',
            ]);

        $this->setChild('back_button', $backButton);
        return parent::_prepareLayout();
    }

    /**
     * Retrieve currently edited product object
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('settings', [
            'legend' => Mage::helper('catalog')->__('Select Configurable Attributes '),
        ]);

        $product    = $this->_getProduct();
        $attributes = $product->getTypeInstance(true)
            ->getSetAttributes($product);

        $fieldset->addField('req_text', 'note', [
            'text' => '<ul class="messages"><li class="notice-msg"><ul><li>'
                . $this->__('Only attributes with scope "Global", input type "Dropdown" and Use To Create Configurable Product "Yes" are available.')
                . '</li></ul></li></ul>',
        ]);

        $hasAttributes = false;

        /** @var Mage_Catalog_Model_Product_Type_Configurable $productType */
        $productType = $product->getTypeInstance(true);

        foreach ($attributes as $attribute) {
            if ($productType->canUseAttribute($attribute, $product)) {
                $hasAttributes = true;
                $fieldset->addField('attribute_' . $attribute->getAttributeId(), 'checkbox', [
                    'label' => $attribute->getFrontend()->getLabel(),
                    'title' => $attribute->getFrontend()->getLabel(),
                    'name'  => 'attribute',
                    'class' => 'attribute-checkbox',
                    'value' => $attribute->getAttributeId(),
                ]);
            }
        }

        if ($hasAttributes) {
            $fieldset->addField('attributes', 'hidden', [
                'name'  => 'attribute_validate',
                'value' => '',
                'class' => 'validate-super-product-attributes',
            ]);

            $fieldset->addField('continue_button', 'note', [
                'text' => $this->getChildHtml('continue_button'),
            ]);
        } else {
            $fieldset->addField('note_text', 'note', [
                'text' => $this->__('This attribute set does not have attributes which we can use for configurable product'),
            ]);
            $fieldset->addField('back_button', 'note', [
                'text' => $this->getChildHtml('back_button'),
            ]);
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve Continue URL
     *
     * @return string
     */
    public function getContinueUrl()
    {
        return $this->getUrl('*/*/new', [
            '_current'   => true,
            'attributes' => '{{attributes}}',
        ]);
    }

    /**
     * Retrieve Back URL
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/new', ['set' => null, 'type' => null]);
    }
}
