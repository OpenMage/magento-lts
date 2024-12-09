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
 * Adminhtml sales order create
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'sales_order';
        $this->_mode = 'create';

        parent::__construct();

        $this->setId('sales_order_create');

        $customerId = $this->_getSession()->getCustomerId();
        $storeId    = $this->_getSession()->getStoreId();

        $this->_updateButton('save', 'label', Mage::helper('sales')->__('Submit Order'));
        $this->_updateButton('save', 'onclick', 'order.submit()');
        $this->_updateButton('save', 'id', 'submit_order_top_button');
        if (is_null($customerId) || !$storeId) {
            $this->_updateButton('save', 'style', 'display:none');
        }

        $this->_updateButton('back', 'id', 'back_order_top_button');
        $this->_updateButton('back', 'onclick', Mage::helper('core/js')->getSetLocationJs($this->getBackUrl()));

        $this->_updateButton('reset', 'id', 'reset_order_top_button');

        if (!$this->_isCanCancel() || is_null($customerId)) {
            $this->_updateButton('reset', 'style', 'display:none');
        } else {
            $this->_updateButton('back', 'style', 'display:none');
        }

        $this->_updateButton('reset', 'label', Mage::helper('sales')->__('Cancel'));
        $this->_updateButton('reset', 'class', 'cancel');
        $this->_updateButton(
            'reset',
            'onclick',
            Mage::helper('core/js')->getDeleteConfirmJs(
                $this->getCancelUrl(),
                Mage::helper('sales')->__('Are you sure you want to cancel this order?'),
            ),
        );
    }

    /**
     * Check access for cancel action
     *
     * @return bool
     */
    protected function _isCanCancel()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/cancel');
    }

    /**
     * Prepare header html
     *
     * @return string
     */
    public function getHeaderHtml()
    {
        return '<div id="order-header">'
            . $this->getLayout()->createBlock('adminhtml/sales_order_create_header')->toHtml()
            . '</div>';
    }

    /**
     * Prepare form html. Add block for configurable product modification interface
     *
     * @return string
     */
    public function getFormHtml()
    {
        $html = parent::getFormHtml();
        return $html . $this->getLayout()->createBlock('adminhtml/catalog_product_composite_configure')->toHtml();
    }

    /**
     * @return string
     */
    public function getHeaderWidth()
    {
        return 'width: 70%;';
    }

    /**
     * Retrieve quote session object
     *
     * @return Mage_Adminhtml_Model_Session_Quote
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session_quote');
    }

    /**
     * @return string
     */
    public function getCancelUrl()
    {
        if ($this->_getSession()->getOrder()->getId()) {
            $url = $this->getUrl('*/sales_order/view', [
                'order_id' => Mage::getSingleton('adminhtml/session_quote')->getOrder()->getId(),
            ]);
        } else {
            $url = $this->getUrl('*/*/cancel');
        }

        return $url;
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/' . $this->_controller . '/');
    }
}
