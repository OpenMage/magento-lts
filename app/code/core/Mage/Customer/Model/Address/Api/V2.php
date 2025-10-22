<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer address api V2
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Address_Api_V2 extends Mage_Customer_Model_Address_Api
{
    /**
     * Create new address for customer
     *
     * @param int $customerId
     * @param array $addressData
     * @return int
     */
    public function create($customerId, $addressData)
    {
        $customer = Mage::getModel('customer/customer')
            ->load($customerId);
        /** @var Mage_Customer_Model_Customer $customer */

        if (!$customer->getId()) {
            $this->_fault('customer_not_exists');
        }

        $address = Mage::getModel('customer/address');

        foreach (array_keys($this->getAllowedAttributes($address)) as $attributeCode) {
            if (isset($addressData->$attributeCode)) {
                $address->setData($attributeCode, $addressData->$attributeCode);
            }
        }

        if (isset($addressData->is_default_billing)) {
            $address->setIsDefaultBilling($addressData->is_default_billing);
        }

        if (isset($addressData->is_default_shipping)) {
            $address->setIsDefaultShipping($addressData->is_default_shipping);
        }

        $address->setCustomerId($customer->getId());

        $valid = $address->validate();

        if (is_array($valid)) {
            $this->_fault('data_invalid', implode("\n", $valid));
        }

        try {
            $address->save();
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_fault('data_invalid', $mageCoreException->getMessage());
        }

        return $address->getId();
    }

    /**
     * Retrieve address data
     *
     * @param int $addressId
     * @return array
     */
    public function info($addressId)
    {
        $address = Mage::getModel('customer/address')
            ->load($addressId);

        if (!$address->getId()) {
            $this->_fault('not_exists');
        }

        $result = [];

        foreach ($this->_mapAttributes as $attributeAlias => $attributeCode) {
            $result[$attributeAlias] = $address->getData($attributeCode);
        }

        foreach (array_keys($this->getAllowedAttributes($address)) as $attributeCode) {
            $result[$attributeCode] = $address->getData($attributeCode);
        }

        if ($customer = $address->getCustomer()) {
            $result['is_default_billing']  = $customer->getDefaultBilling() == $address->getId();
            $result['is_default_shipping'] = $customer->getDefaultShipping() == $address->getId();
        }

        return $result;
    }

    /**
     * Update address data
     *
     * @param int $addressId
     * @param array $addressData
     * @return bool
     */
    public function update($addressId, $addressData)
    {
        $address = Mage::getModel('customer/address')
            ->load($addressId);

        if (!$address->getId()) {
            $this->_fault('not_exists');
        }

        foreach (array_keys($this->getAllowedAttributes($address)) as $attributeCode) {
            if (isset($addressData->$attributeCode)) {
                $address->setData($attributeCode, $addressData->$attributeCode);
            }
        }

        if (isset($addressData->is_default_billing)) {
            $address->setIsDefaultBilling($addressData->is_default_billing);
        }

        if (isset($addressData->is_default_shipping)) {
            $address->setIsDefaultShipping($addressData->is_default_shipping);
        }

        $valid = $address->validate();
        if (is_array($valid)) {
            $this->_fault('data_invalid', implode("\n", $valid));
        }

        try {
            $address->save();
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_fault('data_invalid', $mageCoreException->getMessage());
        }

        return true;
    }

    /**
     * Delete address
     *
     * @param int $addressId
     * @return bool
     */
    public function delete($addressId)
    {
        $address = Mage::getModel('customer/address')
            ->load($addressId);

        if (!$address->getId()) {
            $this->_fault('not_exists');
        }

        try {
            $address->delete();
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_fault('not_deleted', $mageCoreException->getMessage());
        }

        return true;
    }
}
