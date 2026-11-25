<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Abstract API2 class for customer
 *
 * @package    Mage_Customer
 */
abstract class Mage_Customer_Model_Api2_Customer_Rest extends Mage_Customer_Model_Api2_Customer
{
    /**
     * Create customer
     *
     * @return string
     */
    protected function _create(array $data)
    {
        /** @var Mage_Api2_Model_Resource_Validator_Eav $validator */
        $validator = Mage::getResourceModel('api2/validator_eav', ['resource' => $this]);

        $data = $validator->filter($data);
        if (!$validator->isValidData($data)) {
            foreach ($validator->getErrors() as $error) {
                $this->_error($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }

            $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }

        /** @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getModel('customer/customer');
        $customer->setData($data);

        try {
            $customer->save();
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_error($mageCoreException->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $exception) {
            Mage::logException($exception);
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }

        return $this->_getLocation($customer);
    }

    /**
     * Retrieve information about customer
     *
     * @return array
     * @throws Mage_Api2_Exception
     */
    protected function _retrieve()
    {
        $customer = $this->_loadCustomerById($this->getRequest()->getParam('id'));
        return $this->_prepareCustomerDataForResponse($customer->getData());
    }

    /**
     * Get customers list
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $data = $this->_getCollectionForRetrieve()->load()->toArray();
        $items = $data['items'] ?? $data;

        foreach ($items as $key => $item) {
            if (is_array($item)) {
                $items[$key] = $this->_prepareCustomerDataForResponse($item);
            }
        }

        return $items;
    }

    /**
     * Reject collection filters that could recover a credential or the confirmation key
     *
     * See Mage_Customer_Helper_Data::isApiFilterAllowed(): credentials cannot be filtered on, whatever
     * attributes the API role allows, and the confirmation key only accepts null/notnull.
     *
     * @throws Exception
     * @throws Mage_Api2_Exception
     */
    protected function _validateSecretFilters(): void
    {
        $filter = $this->getRequest()->getFilter();
        if (!is_array($filter)) {
            return;
        }

        /** @var Mage_Customer_Helper_Data $helper */
        $helper = Mage::helper('customer');
        foreach ($filter as $filterEntry) {
            if (!is_array($filterEntry) || !isset($filterEntry['attribute']) || !is_scalar($filterEntry['attribute'])) {
                continue;
            }

            $attributeCode = strtolower(trim((string) $filterEntry['attribute']));
            unset($filterEntry['attribute']);
            if (!$helper->isApiFilterAllowed([$attributeCode], $filterEntry)) {
                $this->_critical(self::RESOURCE_COLLECTION_FILTERING_ERROR);
            }
        }
    }

    /**
     * Remove credentials from customer data before it is returned
     *
     * Credentials are removed whatever attributes the API role allows, and only whether confirmation is
     * pending is returned for the confirmation key.
     *
     * @param  array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function _prepareCustomerDataForResponse(array $data)
    {
        /** @var Mage_Customer_Helper_Data $helper */
        $helper = Mage::helper('customer');
        foreach ($helper->getApiSecretAttributeCodes() as $attributeCode) {
            unset($data[$attributeCode]);
        }

        if (array_key_exists('confirmation', $data)) {
            $data['confirmation'] = $helper->prepareApiConfirmationForRead($data['confirmation']);
        }

        return $data;
    }

    /**
     * Update customer
     *
     * @throws Mage_Api2_Exception
     */
    protected function _update(array $data)
    {
        $customer = $this->_loadCustomerById($this->getRequest()->getParam('id'));
        /** @var Mage_Api2_Model_Resource_Validator_Eav $validator */
        $validator = Mage::getResourceModel('api2/validator_eav', ['resource' => $this]);

        $data = $validator->filter($data);

        unset($data['website_id']); // website is not allowed to change

        if (!$validator->isValidData($data, true)) {
            foreach ($validator->getErrors() as $error) {
                $this->_error($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }

            $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }

        $customer->addData($data);

        try {
            $customer->save();
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_error($mageCoreException->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $exception) {
            Mage::logException($exception);
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }
    }

    /**
     * Load customer by id
     *
     * @param  int                          $id
     * @return Mage_Customer_Model_Customer
     * @throws Mage_Api2_Exception
     */
    protected function _loadCustomerById($id)
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getModel('customer/customer')->load($id);
        if (!$customer->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        return $customer;
    }

    /**
     * Retrieve collection instances
     *
     * @return Mage_Customer_Model_Resource_Customer_Collection
     */
    protected function _getCollectionForRetrieve()
    {
        /** @var Mage_Customer_Model_Resource_Customer_Collection $collection */
        $collection = Mage::getResourceModel('customer/customer_collection');
        $collection->addAttributeToSelect(array_keys(
            $this->getAvailableAttributes($this->getUserType(), Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ),
        ));

        $this->_validateSecretFilters();
        $this->_applyCollectionModifiers($collection);
        return $collection;
    }
}
