<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 */

/**
 * Checkout api resource for Customer
 *
 * @package    Mage_Checkout
 */
class Mage_Checkout_Model_Api_Resource_Customer extends Mage_Checkout_Model_Api_Resource
{
    /**
     * Customer address types
     */
    public const ADDRESS_BILLING    = Mage_Sales_Model_Quote_Address::TYPE_BILLING;
    public const ADDRESS_SHIPPING   = Mage_Sales_Model_Quote_Address::TYPE_SHIPPING;

    /**
     * Customer checkout types
     */
    public const MODE_CUSTOMER = Mage_Checkout_Model_Type_Onepage::METHOD_CUSTOMER;
    public const MODE_REGISTER = Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER;
    public const MODE_GUEST    = Mage_Checkout_Model_Type_Onepage::METHOD_GUEST;

    /**
     * @param int $customerId
     * @return Mage_Customer_Model_Customer
     * @throws Mage_Api_Exception
     */
    protected function _getCustomer($customerId)
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getModel('customer/customer')
            ->load($customerId);
        if (!$customer->getId()) {
            $this->_fault('customer_not_exists');
        }

        return $customer;
    }

    /**
     * Get customer address by identifier
     *
     * @param   int $addressId
     * @return  Mage_Customer_Model_Address
     */
    protected function _getCustomerAddress($addressId)
    {
        $address = Mage::getModel('customer/address')->load((int) $addressId);
        if (is_null($address->getId())) {
            $this->_fault('invalid_address_id');
        }

        $address->explodeStreetAddress();
        if ($address->getRegionId()) {
            $address->setRegion($address->getRegionId());
        }
        return $address;
    }

    /**
     * @return bool
     */
    public function prepareCustomerForQuote(Mage_Sales_Model_Quote $quote)
    {
        $isNewCustomer = false;
        switch ($quote->getCheckoutMethod()) {
            case self::MODE_GUEST:
                $this->_prepareGuestQuote($quote);
                break;
            case self::MODE_REGISTER:
                $this->_prepareNewCustomerQuote($quote);
                $isNewCustomer = true;
                break;
            default:
                $this->_prepareCustomerQuote($quote);
                break;
        }

        return $isNewCustomer;
    }

    /**
     * Prepare quote for guest checkout order submit
     *
     * @return $this
     */
    protected function _prepareGuestQuote(Mage_Sales_Model_Quote $quote)
    {
        $quote->setCustomerId(null)
            ->setCustomerEmail($quote->getBillingAddress()->getEmail())
            ->setCustomerIsGuest(true)
            ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
        return $this;
    }

    /**
     * Prepare quote for customer registration and customer order submit
     *
     * @return $this
     */
    protected function _prepareNewCustomerQuote(Mage_Sales_Model_Quote $quote)
    {
        $billing    = $quote->getBillingAddress();
        $shipping   = $quote->isVirtual() ? null : $quote->getShippingAddress();

        //$customer = Mage::getModel('customer/customer');
        $customer = $quote->getCustomer();
        /** @var Mage_Customer_Model_Customer $customer */
        $customerBilling = $billing->exportCustomerAddress();
        $customer->addAddress($customerBilling);
        $billing->setCustomerAddress($customerBilling);
        $customerBilling->setIsDefaultBilling(true);
        if ($shipping && !$shipping->getSameAsBilling()) {
            $customerShipping = $shipping->exportCustomerAddress();
            $customer->addAddress($customerShipping);
            $shipping->setCustomerAddress($customerShipping);
            $customerShipping->setIsDefaultShipping(true);
        } else {
            $customerBilling->setIsDefaultShipping(true);
        }

        Mage::helper('core')->copyFieldset('checkout_onepage_quote', 'to_customer', $quote, $customer);
        $customer->setPassword($customer->decryptPassword($quote->getPasswordHash()));
        $customer->setPasswordCreatedAt(time());
        $quote->setCustomer($customer)
            ->setCustomerId(true);
        $quote->setPasswordHash('');
        return $this;
    }

    /**
     * Prepare quote for customer order submit
     *
     * @return $this
     */
    protected function _prepareCustomerQuote(Mage_Sales_Model_Quote $quote)
    {
        $billing    = $quote->getBillingAddress();
        $shipping   = $quote->isVirtual() ? null : $quote->getShippingAddress();

        $customer = $quote->getCustomer();
        if (!$billing->getCustomerId() || $billing->getSaveInAddressBook()) {
            $customerBilling = $billing->exportCustomerAddress();
            $customer->addAddress($customerBilling);
            $billing->setCustomerAddress($customerBilling);
        }
        if ($shipping && ((!$shipping->getCustomerId() && !$shipping->getSameAsBilling())
            || (!$shipping->getSameAsBilling() && $shipping->getSaveInAddressBook()))
        ) {
            $customerShipping = $shipping->exportCustomerAddress();
            $customer->addAddress($customerShipping);
            $shipping->setCustomerAddress($customerShipping);
        }

        if (isset($customerBilling) && !$customer->getDefaultBilling()) {
            $customerBilling->setIsDefaultBilling(true);
        }
        if ($shipping && isset($customerShipping) && !$customer->getDefaultShipping()) {
            $customerShipping->setIsDefaultShipping(true);
        } elseif (isset($customerBilling) && !$customer->getDefaultShipping()) {
            $customerBilling->setIsDefaultShipping(true);
        }
        $quote->setCustomer($customer);

        return $this;
    }

    /**
     * Involve new customer to system
     *
     * @return $this
     */
    public function involveNewCustomer(Mage_Sales_Model_Quote $quote)
    {
        $customer = $quote->getCustomer();
        if ($customer->isConfirmationRequired()) {
            $customer->sendNewAccountEmail('confirmation');
        } else {
            $customer->sendNewAccountEmail();
        }

        return $this;
    }
}
