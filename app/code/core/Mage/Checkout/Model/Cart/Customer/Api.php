<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 */

/**
 * Shopping cart api for customer data
 *
 * @package    Mage_Checkout
 */
class Mage_Checkout_Model_Cart_Customer_Api extends Mage_Checkout_Model_Api_Resource_Customer
{
    public function __construct()
    {
        $this->_storeIdSessionField = 'cart_store_id';

        $this->_attributesMap['quote'] = ['quote_id' => 'entity_id'];
        $this->_attributesMap['quote_customer'] = ['customer_id' => 'entity_id'];
        $this->_attributesMap['quote_address'] = ['address_id' => 'entity_id'];
    }

    /**
     * Set customer for shopping cart
     *
     * @param int $quoteId
     * @param array $customerData
     * @param int|string $store
     * @return true
     */
    public function set($quoteId, $customerData, $store = null)
    {
        $quote = $this->_getQuote($quoteId, $store);

        $customerData = $this->_prepareCustomerData($customerData);
        if (!isset($customerData['mode'])) {
            $this->_fault('customer_mode_is_unknown');
        }

        switch ($customerData['mode']) {
            case self::MODE_CUSTOMER:
                $customer = $this->_getCustomer($customerData['entity_id']);
                $customer->setMode(self::MODE_CUSTOMER);
                break;

            case self::MODE_REGISTER:
            case self::MODE_GUEST:
                $customer = Mage::getModel('customer/customer')
                ->setData($customerData);

                if ($customer->getMode() == self::MODE_GUEST) {
                    $password = $customer->generatePassword();

                    $customer
                    ->setPassword($password)
                    ->setPasswordConfirmation($password);
                }

                $isCustomerValid = $customer->validate();
                if ($isCustomerValid !== true && is_array($isCustomerValid)) {
                    $this->_fault('customer_data_invalid', implode(PHP_EOL, $isCustomerValid));
                }

                break;
        }

        try {
            $quote
                ->setCustomer($customer)
                ->setCheckoutMethod($customer->getMode())
                ->setPasswordHash($customer->encryptPassword($customer->getPassword()))
                ->save();
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_fault('customer_not_set', $mageCoreException->getMessage());
        }

        return true;
    }

    /**
     * @param  int $quoteId
     * @param  array $customerAddressData of array|object
     * @param  int|string $store
     * @return true
     */
    public function setAddresses($quoteId, $customerAddressData, $store = null)
    {
        $quote = $this->_getQuote($quoteId, $store);

        $customerAddressData = $this->_prepareCustomerAddressData($customerAddressData);
        if (is_null($customerAddressData)) {
            $this->_fault('customer_address_data_empty');
        }

        foreach ($customerAddressData as $addressItem) {
            /** @var Mage_Sales_Model_Quote_Address $address */
            $address = Mage::getModel('sales/quote_address');
            $addressMode = $addressItem['mode'];
            unset($addressItem['mode']);

            if (!empty($addressItem['entity_id'])) {
                $customerAddress = $this->_getCustomerAddress($addressItem['entity_id']);
                if ($customerAddress->getCustomerId() != $quote->getCustomerId()) {
                    $this->_fault('address_not_belong_customer');
                }

                $address->importCustomerAddress($customerAddress);
            } else {
                $address->setData($addressItem);
            }

            $address->implodeStreetAddress();

            if (($validateRes = $address->validate()) !== true) {
                $this->_fault('customer_address_invalid', implode(PHP_EOL, $validateRes));
            }

            if (!$address->getEmail() && $quote->getCustomerEmail()) {
                $address->setEmail($quote->getCustomerEmail());
            }

            switch ($addressMode) {
                case self::ADDRESS_BILLING:
                    if (!$quote->isVirtual()) {
                        $usingCase = isset($addressItem['use_for_shipping']) ? (int) $addressItem['use_for_shipping'] : 0;
                        switch ($usingCase) {
                            case 0:
                                $shippingAddress = $quote->getShippingAddress();
                                $shippingAddress->setSameAsBilling(0);
                                break;
                            case 1:
                                $billingAddress = clone $address;
                                $billingAddress->unsAddressId()->unsAddressType();

                                $shippingAddress = $quote->getShippingAddress();
                                $shippingMethod = $shippingAddress->getShippingMethod();
                                $shippingAddress->addData($billingAddress->getData())
                                    ->setSameAsBilling(1)
                                    ->setShippingMethod($shippingMethod)
                                    ->setCollectShippingRates(true);
                                break;
                        }
                    }

                    $quote->setBillingAddress($address);
                    break;

                case self::ADDRESS_SHIPPING:
                    $address->setCollectShippingRates(true)
                        ->setSameAsBilling(0);
                    $quote->setShippingAddress($address);
                    break;
            }
        }

        try {
            $quote
                ->collectTotals()
                ->save();
        } catch (Exception $exception) {
            $this->_fault('address_is_not_set', $exception->getMessage());
        }

        return true;
    }

    /**
     * Prepare customer entered data for implementing
     *
     * @param array $data
     * @return array
     */
    protected function _prepareCustomerData($data)
    {
        foreach ($this->_attributesMap['quote_customer'] as $attributeAlias => $attributeCode) {
            if (isset($data[$attributeAlias])) {
                $data[$attributeCode] = $data[$attributeAlias];
                unset($data[$attributeAlias]);
            }
        }

        return $data;
    }

    /**
     * Prepare customer entered data for implementing
     *
     * @param  array $data
     * @return null|array
     */
    protected function _prepareCustomerAddressData($data)
    {
        if (!is_array($data) || !is_array($data[0])) {
            return null;
        }

        $dataAddresses = [];
        foreach ($data as $addressItem) {
            foreach ($this->_attributesMap['quote_address'] as $attributeAlias => $attributeCode) {
                if (isset($addressItem[$attributeAlias])) {
                    $addressItem[$attributeCode] = $addressItem[$attributeAlias];
                    unset($addressItem[$attributeAlias]);
                }
            }

            $dataAddresses[] = $addressItem;
        }

        return $dataAddresses;
    }
}
