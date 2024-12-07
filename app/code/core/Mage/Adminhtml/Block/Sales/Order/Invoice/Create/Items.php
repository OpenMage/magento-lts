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
 * Adminhtml invoice items grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Invoice_Create_Items extends Mage_Adminhtml_Block_Sales_Items_Abstract
{
    protected $_disableSubmitButton = false;

    /**
     * Prepare child blocks
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $onclick = "submitAndReloadArea($('invoice_item_container'),'" . $this->getUpdateUrl() . "')";
        $this->setChild(
            'update_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                'class'     => 'update-button',
                'label'     => Mage::helper('sales')->__('Update Qty\'s'),
                'onclick'   => $onclick,
            ]),
        );
        $this->_disableSubmitButton = true;
        $submitButtonClass = ' disabled';
        foreach ($this->getInvoice()->getAllItems() as $item) {
            /**
             * @see bug #14839
             */
            if ($item->getQty()/* || $this->getSource()->getData('base_grand_total')*/) {
                $this->_disableSubmitButton = false;
                $submitButtonClass = '';
                break;
            }
        }
        if ($this->getOrder()->getForcedDoShipmentWithInvoice()) {
            $submitLabel = Mage::helper('sales')->__('Submit Invoice and Shipment');
        } else {
            $submitLabel = Mage::helper('sales')->__('Submit Invoice');
        }
        $this->setChild(
            'submit_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                'label'     => $submitLabel,
                'class'     => 'save submit-button' . $submitButtonClass,
                'onclick'   => 'disableElements(\'submit-button\');$(\'edit_form\').submit()',
                'disabled'  => $this->_disableSubmitButton,
            ]),
        );

        return parent::_prepareLayout();
    }

    /**
     * Get is submit button disabled or not
     *
     * @return bool
     */
    public function getDisableSubmitButton()
    {
        return $this->_disableSubmitButton;
    }

    /**
     * Retrieve invoice order
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getInvoice()->getOrder();
    }

    /**
     * Retrieve source
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function getSource()
    {
        return $this->getInvoice();
    }

    /**
     * Retrieve invoice model instance
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function getInvoice()
    {
        return Mage::registry('current_invoice');
    }

    /**
     * Retrieve order totals block settings
     *
     * @return array
     */
    public function getOrderTotalData()
    {
        return [];
    }

    /**
     * Retrieve order totalbar block data
     *
     * @return array
     */
    public function getOrderTotalbarData()
    {
        $totalbarData = [];
        $this->setPriceDataObject($this->getInvoice()->getOrder());
        $totalbarData[] = [Mage::helper('sales')->__('Paid Amount'), $this->displayPriceAttribute('amount_paid'), false];
        $totalbarData[] = [Mage::helper('sales')->__('Refund Amount'), $this->displayPriceAttribute('amount_refunded'), false];
        $totalbarData[] = [Mage::helper('sales')->__('Shipping Amount'), $this->displayPriceAttribute('shipping_captured'), false];
        $totalbarData[] = [Mage::helper('sales')->__('Shipping Refund'), $this->displayPriceAttribute('shipping_refunded'), false];
        $totalbarData[] = [Mage::helper('sales')->__('Order Grand Total'), $this->displayPriceAttribute('grand_total'), true];

        return $totalbarData;
    }

    public function formatPrice($price)
    {
        return $this->getInvoice()->getOrder()->formatPrice($price);
    }

    public function getUpdateButtonHtml()
    {
        return $this->getChildHtml('update_button');
    }

    public function getUpdateUrl()
    {
        return $this->getUrl('*/*/updateQty', ['order_id' => $this->getInvoice()->getOrderId()]);
    }

    /**
     * Check shipment availability for current invoice
     *
     * @return bool
     */
    public function canCreateShipment()
    {
        foreach ($this->getInvoice()->getAllItems() as $item) {
            if ($item->getOrderItem()->getQtyToShip()) {
                return true;
            }
        }
        return false;
    }

    public function canEditQty()
    {
        if ($this->getInvoice()->getOrder()->getPayment()->canCapture()) {
            return $this->getInvoice()->getOrder()->getPayment()->canCapturePartial();
        }
        return true;
    }

    /**
     * Check if capture operation is allowed in ACL
     * @return bool
     */
    public function isCaptureAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/capture');
    }

    /**
     * Check if invoice can be captured
     * @return bool
     */
    public function canCapture()
    {
        return $this->getInvoice()->canCapture();
    }

    /**
     * Check if gateway is associated with invoice order
     * @return bool
     */
    public function isGatewayUsed()
    {
        return $this->getInvoice()->getOrder()->getPayment()->getMethodInstance()->isGateway();
    }

    public function canSendInvoiceEmail()
    {
        return Mage::helper('sales')->canSendNewInvoiceEmail($this->getOrder()->getStore()->getId());
    }
}
