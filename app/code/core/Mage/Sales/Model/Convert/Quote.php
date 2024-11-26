<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Quote data convert model
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Convert_Quote extends Varien_Object
{
    /**
     * Convert quote model to order model
     *
     * @param null|Mage_Sales_Model_Order  $order
     * @return  Mage_Sales_Model_Order
     */
    public function toOrder(Mage_Sales_Model_Quote $quote, $order = null)
    {
        if (!($order instanceof Mage_Sales_Model_Order)) {
            $order = Mage::getModel('sales/order');
        }

        $order->setIncrementId($quote->getReservedOrderId())
            ->setStoreId($quote->getStoreId())
            ->setQuoteId($quote->getId())
            ->setQuote($quote)
            ->setCustomer($quote->getCustomer());

        Mage::helper('core')->copyFieldset('sales_convert_quote', 'to_order', $quote, $order);
        Mage::dispatchEvent('sales_convert_quote_to_order', ['order' => $order, 'quote' => $quote]);
        return $order;
    }

    /**
     * Convert quote address model to order
     *
     * @param   null|Mage_Sales_Model_Order $order
     * @return  Mage_Sales_Model_Order
     */
    public function addressToOrder(Mage_Sales_Model_Quote_Address $address, $order = null)
    {
        if (!($order instanceof Mage_Sales_Model_Order)) {
            $order = $this->toOrder($address->getQuote());
        }

        Mage::helper('core')->copyFieldset('sales_convert_quote_address', 'to_order', $address, $order);

        Mage::dispatchEvent('sales_convert_quote_address_to_order', ['address' => $address, 'order' => $order]);
        return $order;
    }

    /**
     * Convert quote address to order address
     *
     * @return  Mage_Sales_Model_Order_Address
     */
    public function addressToOrderAddress(Mage_Sales_Model_Quote_Address $address)
    {
        $orderAddress = Mage::getModel('sales/order_address')
            ->setStoreId($address->getStoreId())
            ->setAddressType($address->getAddressType())
            ->setCustomerId($address->getCustomerId())
            ->setCustomerAddressId($address->getCustomerAddressId());

        Mage::helper('core')->copyFieldset('sales_convert_quote_address', 'to_order_address', $address, $orderAddress);

        Mage::dispatchEvent(
            'sales_convert_quote_address_to_order_address',
            ['address' => $address, 'order_address' => $orderAddress]
        );

        return $orderAddress;
    }

    /**
     * Convert quote payment to order payment
     *
     * @return  Mage_Sales_Model_Order_Payment
     */
    public function paymentToOrderPayment(Mage_Sales_Model_Quote_Payment $payment)
    {
        $orderPayment = Mage::getModel('sales/order_payment')
            ->setStoreId($payment->getStoreId())
            ->setCustomerPaymentId($payment->getCustomerPaymentId());
        Mage::helper('core')->copyFieldset('sales_convert_quote_payment', 'to_order_payment', $payment, $orderPayment);

        Mage::dispatchEvent(
            'sales_convert_quote_payment_to_order_payment',
            ['order_payment' => $orderPayment, 'quote_payment' => $payment]
        );

        return $orderPayment;
    }

    /**
     * Convert quote item to order item
     *
     * @return  Mage_Sales_Model_Order_Item
     */
    public function itemToOrderItem(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        $orderItem = Mage::getModel('sales/order_item')
            ->setStoreId($item->getStoreId())
            ->setQuoteItemId($item->getId())
            ->setQuoteParentItemId($item->getParentItemId())
            ->setProductId($item->getProductId())
            ->setProductType($item->getProductType())
            ->setQtyBackordered($item->getBackorders())
            ->setProduct($item->getProduct())
            ->setBaseOriginalPrice($item->getBaseOriginalPrice())
        ;

        $options = $item->getProductOrderOptions();
        if (!$options) {
            $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
        }
        $orderItem->setProductOptions($options);
        Mage::helper('core')->copyFieldset('sales_convert_quote_item', 'to_order_item', $item, $orderItem);

        if ($item->getParentItem()) {
            $orderItem->setQtyOrdered($orderItem->getQtyOrdered() * $item->getParentItem()->getQty());
        }

        if (!$item->getNoDiscount()) {
            Mage::helper('core')->copyFieldset('sales_convert_quote_item', 'to_order_item_discount', $item, $orderItem);
        }

        Mage::dispatchEvent(
            'sales_convert_quote_item_to_order_item',
            ['order_item' => $orderItem, 'item' => $item]
        );
        return $orderItem;
    }
}
