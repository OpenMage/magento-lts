<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2021-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml Tax Rule Edit Form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Checkout_Agreement_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Init class
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('checkoutAgreementForm');
        $this->setTitle(Mage::helper('checkout')->__('Terms and Conditions Information'));
    }

    /**
     *
     * return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model  = Mage::registry('checkout_agreement');
        $form   = new Varien_Data_Form([
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post'
        ]);

        $fieldset   = $form->addFieldset('base_fieldset', [
            'legend'    => Mage::helper('checkout')->__('Terms and Conditions Information'),
            'class'     => 'fieldset-wide',
        ]);

        if ($model->getId()) {
            $fieldset->addField('agreement_id', 'hidden', [
                'name' => 'agreement_id',
            ]);
        }
        $fieldset->addField('name', 'text', [
            'name'      => 'name',
            'label'     => Mage::helper('checkout')->__('Condition Name'),
            'title'     => Mage::helper('checkout')->__('Condition Name'),
            'required'  => true,
        ]);

        $fieldset->addField('is_active', 'select', [
            'label'     => Mage::helper('checkout')->__('Status'),
            'title'     => Mage::helper('checkout')->__('Status'),
            'name'      => 'is_active',
            'required'  => true,
            'options'   => [
                '1' => Mage::helper('checkout')->__('Enabled'),
                '0' => Mage::helper('checkout')->__('Disabled'),
            ],
        ]);

        $fieldset->addField('is_html', 'select', [
            'label'     => Mage::helper('checkout')->__('Show Content as'),
            'title'     => Mage::helper('checkout')->__('Show Content as'),
            'name'      => 'is_html',
            'required'  => true,
            'options'   => [
                0 => Mage::helper('checkout')->__('Text'),
                1 => Mage::helper('checkout')->__('HTML'),
            ],
        ]);

        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'multiselect', [
                'name'      => 'stores[]',
                'label'     => Mage::helper('checkout')->__('Store View'),
                'title'     => Mage::helper('checkout')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ]);
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField('store_id', 'hidden', [
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ]);
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $fieldset->addField('checkbox_text', 'editor', [
            'name'      => 'checkbox_text',
            'label'     => Mage::helper('checkout')->__('Checkbox Text'),
            'title'     => Mage::helper('checkout')->__('Checkbox Text'),
            'rows'      => '5',
            'cols'      => '30',
            'wysiwyg'   => false,
            'required'  => true,
        ]);

        $fieldset->addField('content', 'editor', [
            'name'      => 'content',
            'label'     => Mage::helper('checkout')->__('Content'),
            'title'     => Mage::helper('checkout')->__('Content'),
            'style'     => 'height:24em;',
            'wysiwyg'   => false,
            'required'  => false,
        ]);

        $fieldset->addField('content_height', 'text', [
            'name'      => 'content_height',
            'label'     => Mage::helper('checkout')->__('Content Height (css)'),
            'title'     => Mage::helper('checkout')->__('Content Height'),
            'maxlength' => 25,
            'class'     => 'validate-css-length',
        ]);

        $fieldset->addField('position', 'text', [
            'label'    => Mage::helper('checkout')->__('Position'),
            'title'    => Mage::helper('checkout')->__('Position'),
            'name'     => 'position',
            'value'    => '0',
            'required' => true,
            'class'    => 'validate-digits',
        ]);

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
