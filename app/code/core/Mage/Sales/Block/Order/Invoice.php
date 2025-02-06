<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Sales order view block
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Block_Order_Invoice extends Mage_Sales_Block_Order_Invoice_Items
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('sales/order/invoice.phtml');
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        /** @var Mage_Page_Block_Html_Head $headBlock */
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle($this->__('Order # %s', $this->getOrder()->getRealOrderId()));
        }

        /** @var Mage_Payment_Helper_Data $helper */
        $helper = $this->helper('payment');
        $this->setChild(
            'payment_info',
            $helper->getInfoBlock($this->getOrder()->getPayment()),
        );

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getPaymentInfoHtml()
    {
        return $this->getChildHtml('payment_info');
    }

    /**
     * Retrieve current order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    /**
     * Return back url for logged in and guest users
     *
     * @return string
     */
    public function getBackUrl()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            return Mage::getUrl('*/*/history');
        }
        return Mage::getUrl('*/*/form');
    }

    /**
     * Return back title for logged in and guest users
     *
     * @return string
     */
    public function getBackTitle()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            return Mage::helper('sales')->__('Back to My Orders');
        }
        return Mage::helper('sales')->__('View Another Order');
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    public function getViewUrl($order)
    {
        return Mage::getUrl('*/*/view', ['order_id' => $order->getId()]);
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    public function getShipmentUrl($order)
    {
        return Mage::getUrl('*/*/shipment', ['order_id' => $order->getId()]);
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    public function getCreditmemoUrl($order)
    {
        return Mage::getUrl('*/*/creditmemo', ['order_id' => $order->getId()]);
    }

    /**
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return string
     */
    public function getPrintInvoiceUrl($invoice)
    {
        return Mage::getUrl('*/*/printInvoice', ['invoice_id' => $invoice->getId()]);
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    public function getPrintAllInvoicesUrl($order)
    {
        return Mage::getUrl('*/*/printInvoice', ['order_id' => $order->getId()]);
    }

    public function canDisplayGiftmessage(): bool
    {
        if (!$this->isModuleOutputEnabled('Mage_GiftMessage')) {
            return false;
        }
        /** @var Mage_GiftMessage_Helper_Message $helper */
        $helper = $this->helper('giftmessage/message');
        return $helper->getIsMessagesAvailable($helper::TYPE_ORDER, $this->getOrder());
    }
}
