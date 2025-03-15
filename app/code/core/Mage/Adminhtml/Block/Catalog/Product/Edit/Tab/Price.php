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
 * @copyright  Copyright (c) 2022-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml product edit price block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $product = Mage::registry('product');

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('tiered_price', ['legend' => Mage::helper('catalog')->__('Tier Pricing')]);

        $fieldset->addField('default_price', 'label', [
            'label' => Mage::helper('catalog')->__('Default Price'),
            'title' => Mage::helper('catalog')->__('Default Price'),
            'name' => 'default_price',
            'bold' => true,
            'value' => $product->getPrice(),
        ]);

        $fieldset->addField('tier_price', 'text', [
            'name' => 'tier_price',
            'class' => 'requried-entry',
            'value' => $product->getData('tier_price'),
        ]);

        $renderer = $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_price_tier');
        if ($renderer instanceof Varien_Data_Form_Element_Renderer_Interface) {
            $form->getElement('tier_price')->setRenderer($renderer);
        }

        $this->setForm($form);
        return $this;
    }
}
