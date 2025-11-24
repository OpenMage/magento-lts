<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 */

/**
 * Cehckout type abstract class
 *
 * @package    Mage_Checkout
 */
abstract class Mage_Checkout_Model_Type_Abstract extends Varien_Object
{
    /**
     * Retrieve checkout session model
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckoutSession()
    {
        $checkout = $this->getData('checkout_session');
        if (is_null($checkout)) {
            $checkout = Mage::getSingleton('checkout/session');
            $this->setData('checkout_session', $checkout);
        }

        return $checkout;
    }

    /**
     * Retrieve quote model
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckoutSession()->getQuote();
    }

    /**
     * Retrieve quote items
     *
     * @return Mage_Sales_Model_Quote_Item[]
     */
    public function getQuoteItems()
    {
        return $this->getQuote()->getAllItems();
    }

    /**
     * Retrieve customer session vodel
     *
     * @return Mage_Customer_Model_Session
     */
    public function getCustomerSession()
    {
        $customer = $this->getData('customer_session');
        if (is_null($customer)) {
            $customer = Mage::getSingleton('customer/session');
            $this->setData('customer_session', $customer);
        }

        return $customer;
    }

    /**
     * Retrieve customer object
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return $this->getCustomerSession()->getCustomer();
    }

    /**
     * Retrieve customer default shipping address
     *
     * @return false|Mage_Customer_Model_Address
     */
    public function getCustomerDefaultShippingAddress()
    {
        $address = $this->getData('customer_default_shipping_address');
        if (is_null($address)) {
            $address = $this->getCustomer()->getDefaultShippingAddress();
            if (!$address) {
                foreach ($this->getCustomer()->getAddresses() as $address) {
                    if ($address) {
                        break;
                    }
                }
            }

            $this->setData('customer_default_shipping_address', $address);
        }

        return $address;
    }

    /**
     * Retrieve customer default billing address
     *
     * @return false|Mage_Customer_Model_Address
     */
    public function getCustomerDefaultBillingAddress()
    {
        $address = $this->getData('customer_default_billing_address');
        if (is_null($address)) {
            $address = $this->getCustomer()->getDefaultBillingAddress();
            if (!$address) {
                foreach ($this->getCustomer()->getAddresses() as $address) {
                    if ($address) {
                        break;
                    }
                }
            }

            $this->setData('customer_default_billing_address', $address);
        }

        return $address;
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Mage_Sales_Model_Order
     */
    protected function _createOrderFromAddress($address)
    {
        return Mage::getModel('sales/order')->createFromQuoteAddress($address)
            ->setCustomerId($this->getCustomer()->getId())
            ->setGlobalCurrencyCode('USD')
            ->setBaseCurrencyCode('USD')
            ->setStoreCurrencyCode('USD')
            ->setOrderCurrencyCode('USD')
            ->setStoreToBaseRate(1)
            ->setStoreToOrderRate(1);
    }

    /**
     * @param array|string $email
     * @param string $name
     * @param Mage_Sales_Model_Order $order
     * @deprecated after 1.4.0.0-rc1
     */
    protected function _emailOrderConfirmation($email, $name, $order)
    {
        $mailer = Mage::getModel('core/email')
            ->setTemplate('email/order.phtml')
            ->setType('html')
            ->setTemplateVar('order', $order)
            ->setTemplateVar('quote', $this->getQuote())
            ->setTemplateVar('name', $name)
            ->setToName($name)
            ->setToEmail($email)
            ->send();
    }
}
