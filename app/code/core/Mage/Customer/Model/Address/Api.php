<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer address api
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Address_Api extends Mage_Customer_Model_Api_Resource
{
    protected $_mapAttributes = [
        'customer_address_id' => 'entity_id',
    ];

    public function __construct()
    {
        $this->_ignoredAttributeCodes[] = 'parent_id';
    }

    /**
     * Retrieve customer addresses list
     *
     * @param  int                 $customerId
     * @return array
     * @throws Mage_Api_Exception
     * @throws Mage_Core_Exception
     */
    public function items($customerId)
    {
        $customer = Mage::getModel('customer/customer')
            ->load($customerId);
        /** @var Mage_Customer_Model_Customer $customer */

        if (!$customer->getId()) {
            $this->_fault('customer_not_exists');
        }

        $result = [];
        foreach ($customer->getAddresses() as $address) {
            $data = $address->toArray();
            $row  = [];

            foreach ($this->_mapAttributes as $attributeAlias => $attributeCode) {
                $row[$attributeAlias] = $data[$attributeCode] ?? null;
            }

            foreach (array_keys($this->getAllowedAttributes($address)) as $attributeCode) {
                if (isset($data[$attributeCode])) {
                    $row[$attributeCode] = $data[$attributeCode];
                }
            }

            $row['is_default_billing'] = $customer->getDefaultBilling() == $address->getId();
            $row['is_default_shipping'] = $customer->getDefaultShipping() == $address->getId();

            $result[] = $row;
        }

        return $result;
    }

    /**
     * Create new address for customer
     *
     * @param  int                 $customerId
     * @param  array|Varien_Object $addressData
     * @return int
     * @throws Mage_Api_Exception
     * @throws Mage_Core_Exception
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
            if (isset($addressData[$attributeCode])) {
                $address->setData($attributeCode, $addressData[$attributeCode]);
            }
        }

        if (isset($addressData['is_default_billing'])) {
            $address->setIsDefaultBilling($addressData['is_default_billing']);
        }

        if (isset($addressData['is_default_shipping'])) {
            $address->setIsDefaultShipping($addressData['is_default_shipping']);
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
     * @param  int                 $addressId
     * @return array
     * @throws Mage_Api_Exception
     * @throws Mage_Core_Exception
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
     * @param  int                 $addressId
     * @param  array|Varien_Object $addressData
     * @return bool
     * @throws Mage_Api_Exception
     * @throws Mage_Core_Exception
     */
    public function update($addressId, $addressData)
    {
        $address = Mage::getModel('customer/address')
            ->load($addressId);

        if (!$address->getId()) {
            $this->_fault('not_exists');
        }

        foreach (array_keys($this->getAllowedAttributes($address)) as $attributeCode) {
            if (isset($addressData[$attributeCode])) {
                $address->setData($attributeCode, $addressData[$attributeCode]);
            }
        }

        if (isset($addressData['is_default_billing'])) {
            $address->setIsDefaultBilling($addressData['is_default_billing']);
        }

        if (isset($addressData['is_default_shipping'])) {
            $address->setIsDefaultShipping($addressData['is_default_shipping']);
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
     * @param  int                 $addressId
     * @return bool
     * @throws Mage_Api_Exception
     * @throws Mage_Core_Exception
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
