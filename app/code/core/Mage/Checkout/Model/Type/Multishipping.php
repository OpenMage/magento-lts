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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Multishipping checkout model
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Model_Type_Multishipping extends Mage_Checkout_Model_Type_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->_init();
    }

    /**
     * Initialize multishipping checkout
     *
     * @return Mage_Checkout_Model_Type_Multishipping
     */
    protected function _init()
    {
        /**
         * reset quote shipping addresses and items
         */
        $this->getQuote()->setIsMultiShipping(true);

        if ($this->getCheckoutSession()->getCheckoutState() === Mage_Checkout_Model_Session::CHECKOUT_STATE_BEGIN) {
            $this->getCheckoutSession()->setCheckoutState(true);

            /**
             * Remove all addresses
             */
            $addresses  = $this->getQuote()->getAllAddresses();
            foreach ($addresses as $address) {
                $this->getQuote()->removeAddress($address->getId());
            }

            if ($defaultShipping = $this->getCustomerDefaultShippingAddress()) {
                $this->getQuote()->getShippingAddress()
                    ->importCustomerAddress($defaultShipping);

                foreach ($this->getQuoteItems() as $item) {
                    /**
                     * Items with parent id we add in importQuoteItem method
                     */
                    if ($item->getParentItemId()) {
                        continue;
                    }
                    if ($item->getProduct()->getIsVirtual()) {
                        continue;
                    }
                    $this->getQuote()->getShippingAddress()
                        ->addItem($item);
                }
            }

            if ($this->getCustomerDefaultBillingAddress()) {
                $this->getQuote()->getBillingAddress()
                    ->importCustomerAddress($this->getCustomerDefaultBillingAddress());
                foreach ($this->getQuoteItems() as $item) {
                    if ($item->getParentItemId()) {
                        continue;
                    }
                    if ($item->getProduct()->getIsVirtual()) {
                        $this->getQuote()->getBillingAddress()->addItem($item);
                    }
                }
            }

            $this->save();
        }
        $this->getQuote()->collectTotals();
        return $this;
    }

    public function getQuoteShippingAddressesItems()
    {
        $items = array();
        $addresses  = $this->getQuote()->getAllAddresses();
        foreach ($addresses as $address) {
            foreach ($address->getAllItems() as $item) {
                if ($item->getParentItemId()) {
                    continue;
                }

                if ($item->getProduct()->getIsVirtual()) {
                    $items[] = $item;
                    continue;
                }
                else {
                    if ($item->getQty() > 1) {
                        for ($i = 0, $n = $item->getQty(); $i < $n; $i++) {
                            if ($i == 0) {
                                $addressItem = $item;
                            }
                            else {
                                $addressItem = clone $item;
                            }
                            $addressItem->setQty(1)
                                ->setCustomerAddressId($address->getCustomerAddressId())
                                ->save();
                            $items[] = $addressItem;
                        }
                    }
                    else {
                        $item->setCustomerAddressId($address->getCustomerAddressId());
                        $items[] = $item;
                    }
                }
            }
        }
        return $items;
    }

    public function removeAddressItem($addressId, $itemId)
    {
        $address = $this->getQuote()->getAddressById($addressId);
        /* @var $address Mage_Sales_Model_Quote_Address */
        if ($address) {
            if ($item = $address->getItemById($itemId)) {
                if ($item->getQty()>1 && !$item->getProduct()->getIsVirtual()) {
                    $item->setQty($item->getQty()-1);
                }
                else {
                    $address->removeItem($item->getId());
                }

                if (count($address->getAllItems()) == 0) {
                    $address->isDeleted(true);
                }

                if ($quoteItem = $this->getQuote()->getItemById($item->getQuoteItemId())) {
                    $newItemQty = $quoteItem->getQty()-1;
                    if ($newItemQty > 0 && !$item->getProduct()->getIsVirtual()) {
                        $quoteItem->setQty($quoteItem->getQty()-1);
                    }
                    else {
                        $this->getQuote()->removeItem($quoteItem->getId());
                    }
                }

                $this->save();
            }
        }
        return $this;
    }

    public function setShippingItemsInformation($info)
    {
        if (is_array($info)) {
            $allQty = 0;
            foreach ($info as $itemData) {
                foreach ($itemData as $quoteItemId => $data) {
                    $allQty += $data['qty'];
                }
            }

            $maxQty = (int)Mage::getStoreConfig('shipping/option/checkout_multiple_maximum_qty');
            if ($allQty > $maxQty) {
                Mage::throwException(Mage::helper('checkout')->__('Maximum qty allowed for Shipping to multiple addresses is %s', $maxQty));
            }

            $addresses  = $this->getQuote()->getAllShippingAddresses();
            foreach ($addresses as $address) {
                $this->getQuote()->removeAddress($address->getId());
            }

            foreach ($info as $itemData) {
                foreach ($itemData as $quoteItemId => $data) {
                    $this->_addShippingItem($quoteItemId, $data);
                }
            }

            if ($billingAddress = $this->getQuote()->getBillingAddress()) {
                $this->getQuote()->removeAddress($billingAddress->getId());
            }

            $this->getQuote()->getBillingAddress()
                ->importCustomerAddress($this->getCustomerDefaultBillingAddress());

            foreach ($this->getQuote()->getAllItems() as $_item) {
                if (!$_item->getProduct()->getIsVirtual()) {
                    continue;
                }
                if (isset($itemData[$_item->getId()]['qty']) && ($qty = (int)$itemData[$_item->getId()]['qty'])) {
                    $_item->setQty($qty);
                }
                $this->getQuote()->getBillingAddress()->addItem($_item);
            }

            $this->save();
            Mage::dispatchEvent('checkout_type_multishipping_set_shipping_items', array('quote'=>$this->getQuote()));
        }
        return $this;
    }

    protected function _addShippingItem($quoteItemId, $data)
    {
        $qty       = isset($data['qty']) ? (int) $data['qty'] : 0;
        $qty       = $qty > 0 ? $qty : 1;
        $addressId = isset($data['address']) ? (int) $data['address'] : false;
        $quoteItem = $this->getQuote()->getItemById($quoteItemId);

        if ($addressId && $quoteItem) {
            $quoteItem->setMultisippingQty((int)$quoteItem->getMultisippingQty()+$qty);
            $quoteItem->setQty($quoteItem->getMultisippingQty());

            $address = $this->getCustomer()->getAddressById($addressId);
            if ($address) {
                if (!$quoteAddress = $this->getQuote()->getShippingAddressByCustomerAddressId($addressId)) {
                    $quoteAddress = Mage::getModel('sales/quote_address')
                       ->importCustomerAddress($address);
                    $this->getQuote()->addShippingAddress($quoteAddress);
                }

                $quoteAddress = $this->getQuote()->getShippingAddressByCustomerAddressId($address->getId());

                if ($quoteAddressItem = $quoteAddress->getItemByQuoteItemId($quoteItemId)) {
                    $quoteAddressItem->setQty((int)($quoteAddressItem->getQty()+$qty));
                }
                else {
                    $quoteAddress->addItem($quoteItem, $qty);
                }
                /**
                 * Collect rates for shipping method page only
                 */
                //$quoteAddress->setCollectShippingRates(true);
                $quoteAddress->setCollectShippingRates((boolean) $this->getCollectRatesFlag());
            }
        }
        return $this;
    }

    public function updateQuoteCustomerShippingAddress($addressId)
    {
        if ($address = $this->getCustomer()->getAddressById($addressId)) {
            $address->setCollectShippingRates(true);
            $this->getQuote()->getShippingAddressByCustomerAddressId($addressId)
                ->importCustomerAddress($address)
                ->collectTotals();
            $this->getQuote()->save();
        }
        return $this;
    }

    public function setQuoteCustomerBillingAddress($addressId)
    {
        if ($address = $this->getCustomer()->getAddressById($addressId)) {
            $this->getQuote()->getBillingAddress($addressId)
                ->importCustomerAddress($address)
                ->collectTotals();
            $this->getQuote()->collectTotals()->save();
        }
        return $this;
    }

    public function setShippingMethods($methods)
    {
        $addresses = $this->getQuote()->getAllShippingAddresses();
        foreach ($addresses as $address) {
            if (isset($methods[$address->getId()])) {
                $address->setShippingMethod($methods[$address->getId()]);
            }
            elseif (!$address->getShippingMethod()) {
                Mage::throwException(Mage::helper('checkout')->__('Please select shipping methods for all addresses'));
            }
        }
        $addresses = $this->getQuote()
            ->collectTotals()
            ->save();
        return $this;
    }

    public function setPaymentMethod($payment)
    {
        if (!isset($payment['method'])) {
            Mage::throwException(Mage::helper('checkout')->__('Payment method is not defined'));
        }
        $this->getQuote()->getPayment()
            ->importData($payment)
            ->save();
        return $this;
    }

    /**
     * Prepare order
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Mage_Sales_Model_Order
     */
    protected function _prepareOrder(Mage_Sales_Model_Quote_Address $address)
    {
        $this->getQuote()->reserveOrderId();
        $convertQuote = Mage::getSingleton('sales/convert_quote');
        $order = $convertQuote->addressToOrder($address);
        $order->setBillingAddress(
            $convertQuote->addressToOrderAddress($this->getQuote()->getBillingAddress())
        );

        if ($address->getAddressType() == 'billing') {
            $order->setIsVirtual(1);
        }
        else {
            $order->setShippingAddress($convertQuote->addressToOrderAddress($address));
        }
        $order->setPayment($convertQuote->paymentToOrderPayment($this->getQuote()->getPayment()));

        foreach ($address->getAllItems() as $item) {
            $orderItem = $convertQuote->itemToOrderItem($item->getQuoteItem());
            if ($item->getQuoteItem()->getParentItem()) {
                $orderItem->setParentItem($order->getItemByQuoteItemId($item->getQuoteItem()->getParentItem()->getId()));
            }
            $order->addItem($orderItem);
        }

        return $order;
    }

    protected function _validate()
    {
        $helper = Mage::helper('checkout');
        if (!$this->getQuote()->getIsMultiShipping()) {
            Mage::throwException($helper->__('Invalid checkout type.'));
        }

        $addresses = $this->getQuote()->getAllShippingAddresses();
        foreach ($addresses as $address) {
            $addressValidation = $address->validate();
            if ($addressValidation !== true) {
                Mage::throwException($helper->__('Please check shipping addresses information.'));
            }
            $method= $address->getShippingMethod();
            $rate  = $address->getShippingRateByCode($method);
            if (!$method || !$rate) {
                Mage::throwException($helper->__('Please specify shipping methods for all addresses.'));
            }
        }
        $addressValidation = $this->getQuote()->getBillingAddress()->validate();
        if ($addressValidation !== true) {
            Mage::throwException($helper->__('Please check billing address information.'));
        }
        return $this;
    }

    public function createOrders()
    {
        $orderIds = array();
        $this->_validate();
        $shippingAddresses = $this->getQuote()->getAllShippingAddresses();
        $orders = array();

        if ($this->getQuote()->hasVirtualItems()) {
            $shippingAddresses[] = $this->getQuote()->getBillingAddress();
        }

        foreach ($shippingAddresses as $address) {
            $order = $this->_prepareOrder($address);

            $orders[] = $order;
            Mage::dispatchEvent(
                'checkout_type_multishipping_create_orders_single',
                array('order'=>$order, 'address'=>$address)
            );
        }

        foreach ($orders as $order) {
            #$order->save();
            $order->place();
            $order->save();

            $order->sendNewOrderEmail();
            $orderIds[$order->getId()] = $order->getIncrementId();
        }

        Mage::getSingleton('core/session')->setOrderIds($orderIds);
        $this->getQuote()
            ->setIsActive(false)
            ->save();

        return $this;
    }

    public function save()
    {
        $this->getQuote()->collectTotals()
            ->save();
        return $this;
    }

    public function reset()
    {
        $this->getCheckoutSession()->setCheckoutState(Mage_Checkout_Model_Session::CHECKOUT_STATE_BEGIN);
        return $this;
    }

    public function validateMinimumAmount()
    {
        return !(Mage::getStoreConfigFlag('sales/minimum_order/active')
            && Mage::getStoreConfigFlag('sales/minimum_order/multi_address')
            && !$this->getQuote()->validateMinimumAmount());
    }

    public function getMinimumAmountDescription()
    {
        $descr = Mage::getStoreConfig('sales/minimum_order/multi_address_description');
        if (empty($descr)) {
            $descr = Mage::getStoreConfig('sales/minimum_order/description');
        }
        return $descr;
    }

    public function getMinimumAmountError()
    {
        $error = Mage::getStoreConfig('sales/minimum_order/multi_address_error_message');
        if (empty($error)) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
        }
        return $error;
    }
}
