<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * API2 class for customer address rest
 *
 * @package    Mage_Customer
 */
abstract class Mage_Customer_Model_Api2_Customer_Address_Rest extends Mage_Customer_Model_Api2_Customer_Address
{
    /**
     * Create customer address
     *
     * @throws Mage_Api2_Exception
     * @return string
     */
    protected function _create(array $data)
    {
        $customer = $this->_loadCustomerById($this->getRequest()->getParam('customer_id'));
        $validator = $this->_getValidator();

        $data = $validator->filter($data);
        if (!$validator->isValidData($data) || !$validator->isValidDataForCreateAssociationWithCountry($data)) {
            foreach ($validator->getErrors() as $error) {
                $this->_error($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }

            $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }

        if (isset($data['region']) && isset($data['country_id'])) {
            $data['region'] = $this->_getRegionIdByNameOrCode($data['region'], $data['country_id']);
        }

        /** @var Mage_Customer_Model_Address $address */
        $address = Mage::getModel('customer/address');
        $address->setData($data);
        $address->setCustomer($customer);

        try {
            $address->save();
        } catch (Mage_Core_Exception $e) {
            $this->_error($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }

        return $this->_getLocation($address);
    }

    /**
     * Retrieve information about specified customer address
     *
     * @throws Mage_Api2_Exception
     * @return array
     */
    protected function _retrieve()
    {
        $address = $this->_loadCustomerAddressById($this->getRequest()->getParam('id'));
        $addressData = $address->getData();
        $addressData['street'] = $address->getStreet();
        return $addressData;
    }

    /**
     * Get customer addresses list
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $data = [];
        /** @var Mage_Customer_Model_Address $address */
        foreach ($this->_getCollectionForRetrieve() as $address) {
            $addressData           = $address->getData();
            $addressData['street'] = $address->getStreet();
            $data[]                = array_merge($addressData, $this->_getDefaultAddressesInfo($address));
        }

        return $data;
    }

    /**
     * Retrieve collection instances
     *
     * @return Mage_Customer_Model_Resource_Address_Collection
     */
    protected function _getCollectionForRetrieve()
    {
        $customer = $this->_loadCustomerById($this->getRequest()->getParam('customer_id'));

        /** @var Mage_Customer_Model_Resource_Address_Collection $collection */
        $collection = $customer->getAddressesCollection();

        $this->_applyCollectionModifiers($collection);
        return $collection;
    }

    /**
     * Get array with default addresses information if possible
     *
     * @return array
     */
    protected function _getDefaultAddressesInfo(Mage_Customer_Model_Address $address)
    {
        return [
            'is_default_billing'  => (int) $this->_isDefaultBillingAddress($address),
            'is_default_shipping' => (int) $this->_isDefaultShippingAddress($address),
        ];
    }

    /**
     * Update specified stock item
     *
     * @throws Mage_Api2_Exception
     */
    protected function _update(array $data)
    {
        $address = $this->_loadCustomerAddressById($this->getRequest()->getParam('id'));
        $validator = $this->_getValidator();

        $data = $validator->filter($data);
        if (!$validator->isValidData($data, true)
            || !$validator->isValidDataForChangeAssociationWithCountry($address, $data)
        ) {
            foreach ($validator->getErrors() as $error) {
                $this->_error($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }

            $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }

        if (isset($data['region'])) {
            $data['region'] = $this->_getRegionIdByNameOrCode(
                $data['region'],
                $data['country_id'] ?? $address->getCountryId(),
            );
            $data['region_id'] = null; // to avoid overwrite region during update in address model _beforeSave()
        }

        $address->addData($data);

        try {
            $address->save();
        } catch (Mage_Core_Exception $e) {
            $this->_error($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }
    }

    /**
     * Delete customer
     */
    protected function _delete()
    {
        $address = $this->_loadCustomerAddressById($this->getRequest()->getParam('id'));

        if ($this->_isDefaultBillingAddress($address) || $this->_isDefaultShippingAddress($address)) {
            $this->_critical(
                'Address is default for customer so is not allowed to be deleted',
                Mage_Api2_Model_Server::HTTP_BAD_REQUEST,
            );
        }

        try {
            $address->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }
    }
}
