<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order data convert model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Convert_Order extends Varien_Object
{
    /**
     * Converting order object to quote object
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  Mage_Sales_Model_Quote
     */
    public function toQuote(Mage_Sales_Model_Order $order, $quote=null)
    {
        if (!($quote instanceof Mage_Sales_Model_Quote)) {
            $quote = Mage::getModel('sales/quote');
        }

        $quote->setStoreId($order->getStoreId())
            ->setOrderId($order->getId());

        Mage::helper('core')->copyFieldset('sales_convert_order', 'to_quote', $order, $quote);

        Mage::dispatchEvent('sales_convert_order_to_quote', array('order'=>$order, 'quote'=>$quote));
        return $quote;
    }

    /**
     * Convert order to shipping address
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  Mage_Sales_Model_Quote_Address
     */
    public function toQuoteShippingAddress(Mage_Sales_Model_Order $order)
    {
        $address = $this->addressToQuoteAddress($order->getShippingAddress());

        Mage::helper('core')->copyFieldset('sales_convert_order', 'to_quote_address', $order, $address);
        return $address;
    }

    /**
     * Convert order address to quote address
     *
     * @param   Mage_Sales_Model_Order_Address $address
     * @return  Mage_Sales_Model_Quote_Address
     */
    public function addressToQuoteAddress(Mage_Sales_Model_Order_Address $address)
    {
        $quoteAddress = Mage::getModel('sales/quote_address')
            ->setStoreId($address->getStoreId())
            ->setAddressType($address->getAddressType())
            ->setCustomerId($address->getCustomerId())
            ->setCustomerAddressId($address->getCustomerAddressId());

        Mage::helper('core')->copyFieldset('sales_convert_order_address', 'to_quote_address', $address, $quoteAddress);
        return $quoteAddress;
    }

    /**
     * Convert order payment to quote payment
     *
     * @param   Mage_Sales_Model_Order_Payment $payment
     * @return  Mage_Sales_Model_Quote_Payment
     */
    public function paymentToQuotePayment(Mage_Sales_Model_Order_Payment $payment, $quotePayment=null)
    {
        if (!($quotePayment instanceof Mage_Sales_Model_Quote_Payment)) {
            $quotePayment = Mage::getModel('sales/quote_payment');
        }

        $quotePayment->setStoreId($payment->getStoreId())
            ->setCustomerPaymentId($payment->getCustomerPaymentId());

        Mage::helper('core')->copyFieldset('sales_convert_order_payment', 'to_quote_payment', $payment, $quotePayment);
        return $quotePayment;
    }

    /**
     * Retrieve
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @return unknown
     */
    public function itemToQuoteItem(Mage_Sales_Model_Order_Item $item)
    {
        $quoteItem = Mage::getModel('sales/quote_item')
            ->setStoreId($item->getOrder()->getStoreId())
            ->setQuoteItemId($item->getId())
            ->setProductId($item->getProductId())
            ->setParentProductId($item->getParentProductId());

        Mage::helper('core')->copyFieldset('sales_convert_order_item', 'to_quote_item', $item, $quoteItem);
        return $quoteItem;
    }

    /**
     * Convert order object to invoice
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  Mage_Sales_Model_Order_Invoice
     */
    public function toInvoice(Mage_Sales_Model_Order $order)
    {
        $invoice = Mage::getModel('sales/order_invoice');
        $invoice->setOrder($order)
            ->setStoreId($order->getStoreId())
            ->setCustomerId($order->getCustomerId())
            ->setBillingAddressId($order->getBillingAddressId())
            ->setShippingAddressId($order->getShippingAddressId());

        Mage::helper('core')->copyFieldset('sales_convert_order', 'to_invoice', $order, $invoice);
        return $invoice;
    }

    /**
     * Convert order item object to invoice item
     *
     * @param   Mage_Sales_Model_Order_Item $item
     * @return  Mage_Sales_Model_Order_Invoice_Item
     */
    public function itemToInvoiceItem(Mage_Sales_Model_Order_Item $item)
    {
        $invoiceItem = Mage::getModel('sales/order_invoice_item');
        $invoiceItem->setOrderItem($item)
            ->setProductId($item->getProductId());

        Mage::helper('core')->copyFieldset('sales_convert_order_item', 'to_invoice_item', $item, $invoiceItem);
        return $invoiceItem;
    }

    /**
     * Convert order object to Shipment
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  Mage_Sales_Model_Order_Shipment
     */
    public function toShipment(Mage_Sales_Model_Order $order)
    {
        $shipment = Mage::getModel('sales/order_shipment');
        $shipment->setOrder($order)
            ->setStoreId($order->getStoreId())
            ->setCustomerId($order->getCustomerId())
            ->setBillingAddressId($order->getBillingAddressId())
            ->setShippingAddressId($order->getShippingAddressId());

        Mage::helper('core')->copyFieldset('sales_convert_order', 'to_shipment', $order, $shipment);
        return $shipment;
    }

    /**
     * Convert order item object to Shipment item
     *
     * @param   Mage_Sales_Model_Order_Item $item
     * @return  Mage_Sales_Model_Order_Shipment_Item
     */
    public function itemToShipmentItem(Mage_Sales_Model_Order_Item $item)
    {
        $shipmentItem = Mage::getModel('sales/order_shipment_item');
        $shipmentItem->setOrderItem($item)
            ->setProductId($item->getProductId());

        Mage::helper('core')->copyFieldset('sales_convert_order_item', 'to_shipment_item', $item, $shipmentItem);
        return $shipmentItem;
    }

    /**
     * Convert order object to creditmemo
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  Mage_Sales_Model_Order_Creditmemo
     */
    public function toCreditmemo(Mage_Sales_Model_Order $order)
    {
        $creditmemo = Mage::getModel('sales/order_creditmemo');
        $creditmemo->setOrder($order)
            ->setStoreId($order->getStoreId())
            ->setCustomerId($order->getCustomerId())
            ->setBillingAddressId($order->getBillingAddressId())
            ->setShippingAddressId($order->getShippingAddressId());

        Mage::helper('core')->copyFieldset('sales_convert_order', 'to_cm', $order, $creditmemo);
        return $creditmemo;
    }

    /**
     * Convert order item object to Creditmemo item
     *
     * @param   Mage_Sales_Model_Order_Item $item
     * @return  Mage_Sales_Model_Order_Creditmemo_Item
     */
    public function itemToCreditmemoItem(Mage_Sales_Model_Order_Item $item)
    {
        $creditmemoItem = Mage::getModel('sales/order_creditmemo_item');
        $creditmemoItem->setOrderItem($item)
            ->setProductId($item->getProductId());

        Mage::helper('core')->copyFieldset('sales_convert_order_item', 'to_cm_item', $item, $creditmemoItem);
        return $creditmemoItem;
    }
}
