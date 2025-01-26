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
 * Adminhtml sales order view
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_View extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Mage_Adminhtml_Block_Sales_Order_View constructor.
     * @throws Mage_Core_Exception
     */
    public function __construct()
    {
        $this->_objectId    = 'order_id';
        $this->_controller  = 'sales_order';
        $this->_mode        = 'view';

        parent::__construct();

        $this->_removeButton('delete');
        $this->_removeButton('reset');
        $this->_removeButton('save');
        $this->setId('sales_order_view');
        $order = $this->getOrder();
        $coreHelper = Mage::helper('core');

        if ($this->_isAllowedAction('edit') && $order->canEdit()) {
            $onclickJs = Mage::helper('core/js')->getDeleteConfirmJs(
                $this->getEditUrl(),
                Mage::helper('sales')->__('Are you sure? This order will be canceled and a new one will be created instead')
            );
            $this->_addButton('order_edit', [
                'label'    => Mage::helper('sales')->__('Edit'),
                'onclick'  => $onclickJs,
            ]);
            // see if order has non-editable products as items
            $nonEditableTypes = array_keys($this->getOrder()->getResource()->aggregateProductsByTypes(
                $order->getId(),
                array_keys(Mage::getConfig()
                    ->getNode('adminhtml/sales/order/create/available_product_types')
                    ->asArray()),
                false
            ));
            if ($nonEditableTypes) {
                $confirmationMessage = $coreHelper->jsQuoteEscape(
                    Mage::helper('sales')->__(
                        'This order contains (%s) items and therefore cannot be edited through the admin interface at this time, if you wish to continue editing the (%s) items will be removed, the order will be canceled and a new order will be placed.',
                        implode(', ', $nonEditableTypes),
                        implode(', ', $nonEditableTypes)
                    )
                );
                $this->_updateButton(
                    'order_edit',
                    'onclick',
                    'if (!confirm(\'' . $confirmationMessage . '\')) return false;' . $onclickJs
                );
            }
        }

        if ($this->_isAllowedAction('cancel') && $order->canCancel()) {
            $this->_addButton('order_cancel', [
                'label'     => Mage::helper('sales')->__('Cancel'),
                'onclick'   => Mage::helper('core/js')->getDeleteConfirmJs(
                    $this->getCancelUrl(),
                    Mage::helper('sales')->__('Are you sure you want to cancel this order?')
                )
            ]);
        }

        if ($this->_isAllowedAction('emails') && !$order->isCanceled()) {
            $this->addButton('send_notification', [
                'label'     => Mage::helper('sales')->__('Send Email'),
                'onclick'   => Mage::helper('core/js')->getDeleteConfirmJs(
                    $this->getEmailUrl(),
                    Mage::helper('sales')->__('Are you sure you want to send order email to customer?')
                )
            ]);
        }

        // invoice action intentionally
        if ($this->_isAllowedAction('invoice') && $order->canVoidPayment()) {
            $this->addButton('void_payment', [
                'label'     => Mage::helper('sales')->__('Void'),
                'onclick'   => Mage::helper('core/js')->getDeleteConfirmJs(
                    $this->getVoidPaymentUrl(),
                    Mage::helper('sales')->__('Are you sure you want to void the payment?')
                )
            ]);
        }

        if ($this->_isAllowedAction('hold') && $order->canHold()) {
            $this->_addButton('order_hold', [
                'label'     => Mage::helper('sales')->__('Hold'),
                'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getHoldUrl())
            ]);
        }

        if ($this->_isAllowedAction('unhold') && $order->canUnhold()) {
            $this->_addButton('order_unhold', [
                'label'     => Mage::helper('sales')->__('Unhold'),
                'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getUnholdUrl())
            ]);
        }

        if ($this->_isAllowedAction('review_payment')) {
            if ($order->canReviewPayment()) {
                $this->_addButton('accept_payment', [
                    'label'     => Mage::helper('sales')->__('Accept Payment'),
                    'onclick'   => Mage::helper('core/js')->getConfirmSetLocationJs(
                        $this->getReviewPaymentUrl('accept'),
                        Mage::helper('sales')->__('Are you sure you want to accept this payment?')
                    )
                ]);
                $this->_addButton('deny_payment', [
                    'label'     => Mage::helper('sales')->__('Deny Payment'),
                    'onclick'   => Mage::helper('core/js')->getConfirmSetLocationJs(
                        $this->getReviewPaymentUrl('deny'),
                        Mage::helper('sales')->__('Are you sure you want to deny this payment?')
                    )
                ]);
            }
            if ($order->canFetchPaymentReviewUpdate()) {
                $this->_addButton('get_review_payment_update', [
                    'label'     => Mage::helper('sales')->__('Get Payment Update'),
                    'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getReviewPaymentUrl('update'))
                ]);
            }
        }

        if ($this->_isAllowedAction('invoice') && $order->canInvoice()) {
            $label = $order->getForcedDoShipmentWithInvoice() ?
                Mage::helper('sales')->__('Invoice and Ship') :
                Mage::helper('sales')->__('Invoice');
            $this->_addButton('order_invoice', [
                'label'     => $label,
                'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getInvoiceUrl()),
                'class'     => 'go'
            ]);
        }

        if ($this->_isAllowedAction('ship') && $order->canShip()
            && !$order->getForcedDoShipmentWithInvoice()
        ) {
            $this->_addButton('order_ship', [
                'label'     => Mage::helper('sales')->__('Ship'),
                'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getShipUrl()),
                'class'     => 'go'
            ]);
        }

        if ($this->_isAllowedAction('creditmemo') && $order->canCreditmemo()) {
            $onClick = Mage::helper('core/js')->getSetLocationJs($this->getCreditmemoUrl());
            if ($order->getPayment()->getMethodInstance()->isGateway()) {
                $onClick = Mage::helper('core/js')->getConfirmSetLocationJs(
                    $this->getCreditmemoUrl(),
                    Mage::helper('sales')->__('This will create an offline refund. To create an online refund, open an invoice and create credit memo for it. Do you wish to proceed?')
                );
            }
            $this->_addButton('order_creditmemo', [
                'label'     => Mage::helper('sales')->__('Credit Memo'),
                'onclick'   => $onClick,
                'class'     => 'go'
            ]);
        }

        /** @var Mage_Sales_Helper_Reorder $helper */
        $helper = $this->helper('sales/reorder');
        if ($this->_isAllowedAction('reorder')
            && $helper->isAllowed($order->getStore())
            && $order->canReorderIgnoreSalable()
        ) {
            $this->_addButton('order_reorder', [
                'label'     => Mage::helper('sales')->__('Reorder'),
                'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getReorderUrl()),
                'class'     => 'go'
            ]);
        }
    }

    /**
     * Retrieve order model object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('sales_order');
    }

    /**
     * Retrieve Order Identifier
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->getOrder()->getId();
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        if ($extOrderId = $this->getOrder()->getExtOrderId()) {
            $extOrderId = '[' . $extOrderId . '] ';
        } else {
            $extOrderId = '';
        }
        return Mage::helper('sales')->__(
            'Order # %s %s | %s',
            $this->getOrder()->getRealOrderId(),
            $extOrderId,
            $this->formatDate(
                $this->getOrder()->getCreatedAtDate(),
                'medium',
                true
            )
        );
    }

    /**
     * @param string $params
     * @param array $params2
     * @return string
     */
    public function getUrl($params = '', $params2 = [])
    {
        $params2['order_id'] = $this->getOrderId();
        return parent::getUrl($params, $params2);
    }

    /**
     * @return string
     */
    public function getEditUrl()
    {
        return $this->getUrl('*/sales_order_edit/start');
    }

    /**
     * @return string
     */
    public function getEmailUrl()
    {
        return $this->getUrl('*/*/email');
    }

    /**
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->getUrlSecure('*/*/cancel');
    }

    /**
     * @return string
     */
    public function getInvoiceUrl()
    {
        return $this->getUrl('*/sales_order_invoice/start');
    }

    /**
     * @return string
     */
    public function getCreditmemoUrl()
    {
        return $this->getUrl('*/sales_order_creditmemo/start');
    }

    /**
     * @return string
     */
    public function getHoldUrl()
    {
        return $this->getUrl('*/*/hold');
    }

    /**
     * @return string
     */
    public function getUnholdUrl()
    {
        return $this->getUrl('*/*/unhold');
    }

    /**
     * @return string
     */
    public function getShipUrl()
    {
        return $this->getUrl('*/sales_order_shipment/start');
    }

    /**
     * @return string
     */
    public function getCommentUrl()
    {
        return $this->getUrl('*/*/comment');
    }

    /**
     * @return string
     */
    public function getReorderUrl()
    {
        return $this->getUrl('*/sales_order_create/reorder');
    }

    /**
     * Payment void URL getter
     */
    public function getVoidPaymentUrl()
    {
        return $this->getUrl('*/*/voidPayment');
    }

    /**
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/' . $action);
    }

    /**
     * Return back url for view grid
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->getOrder()->getBackUrl()) {
            return $this->getOrder()->getBackUrl();
        }

        return $this->getUrl('*/*/');
    }

    /**
     * @param string $action
     * @return string
     */
    public function getReviewPaymentUrl($action)
    {
        return $this->getUrl('*/*/reviewPayment', ['action' => $action]);
    }

    /**
     * Return header for view grid
     *
     * @return string
     */
    public function getHeaderHtml()
    {
        return '<h3 class="' . $this->getHeaderCssClass() . '">' . $this->escapeHtml($this->getHeaderText()) . '</h3>';
    }
}
