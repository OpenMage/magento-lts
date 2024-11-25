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
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Assign order status to order state form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Status_Assign_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('order_status_state');
    }

    /**
     * Prepare form fields
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form   = new Varien_Data_Form([
            'id'        => 'edit_form',
            'method'    => 'post',
        ]);

        $fieldset   = $form->addFieldset('base_fieldset', [
            'legend'    => Mage::helper('sales')->__('Assignment Information'),
        ]);

        $statuses = Mage::getResourceModel('sales/order_status_collection')
            ->toOptionArray();
        array_unshift($statuses, ['value' => '', 'label' => '']);

        $states = Mage::getSingleton('sales/order_config')->getStates();
        $states = array_merge(['' => ''], $states);

        $fieldset->addField(
            'status',
            'select',
            [
                'name'      => 'status',
                'label'     => Mage::helper('sales')->__('Order Status'),
                'class'     => 'required-entry',
                'values'    => $statuses,
                'required'  => true,
            ],
        );

        $fieldset->addField(
            'state',
            'select',
            [
                'name'      => 'state',
                'label'     => Mage::helper('sales')->__('Order State'),
                'class'     => 'required-entry',
                'values'    => $states,
                'required'  => true,
            ],
        );

        $fieldset->addField(
            'is_default',
            'checkbox',
            [
                'name'      => 'is_default',
                'label'     => Mage::helper('sales')->__('Use Order Status As Default'),
                'value'     => 1,
            ],
        );

        $form->setAction($this->getUrl('*/sales_order_status/assignPost'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
