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
 * Customer edit block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'customer';

        if ($this->getCustomerId() &&
            $this->getAdminSession()->isAllowed('sales/order/actions/create')
        ) {
            $this->_addButton('order', [
                'label'     => Mage::helper('customer')->__('Create Order'),
                'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getCreateOrderUrl()),
                'class'     => 'add',
            ], 0);
        }

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('customer')->__('Save Customer'));
        $this->_updateButton('delete', 'label', Mage::helper('customer')->__('Delete Customer'));

        if (Mage::registry('current_customer')->isReadonly()) {
            $this->_removeButton('save');
            $this->_removeButton('reset');
        }

        if (!Mage::registry('current_customer')->isDeleteable()) {
            $this->_removeButton('delete');
        }
    }

    /**
     * @return string
     */
    public function getCreateOrderUrl()
    {
        return $this->getUrl('*/sales_order_create/start', ['customer_id' => $this->getCustomerId()]);
    }

    public function getCustomerId()
    {
        return Mage::registry('current_customer')->getId();
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_customer')->getId()) {
            return $this->escapeHtml(Mage::registry('current_customer')->getName());
        }
        return Mage::helper('customer')->__('New Customer');
    }

    /**
     * Prepare form html. Add block for configurable product modification interface
     *
     * @return string
     */
    public function getFormHtml()
    {
        $html = parent::getFormHtml();
        $html .= $this->getLayout()->createBlock('adminhtml/catalog_product_composite_configure')->toHtml();
        return $html;
    }

    /**
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', ['_current' => true]);
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        if (!Mage::registry('current_customer')->isReadonly()) {
            $this->_addButton('save_and_continue', [
                'label'     => Mage::helper('customer')->__('Save and Continue Edit'),
                'onclick'   => Mage::helper('core/js')->getSaveAndContinueEditJs($this->_getSaveAndContinueUrl()),
                'class'     => 'save'
            ], 10);
        }

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', [
            '_current'  => true,
            'back'      => 'edit',
            'tab'       => '{{tab_id}}'
        ]);
    }
}
