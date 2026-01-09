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
        } catch (Mage_Core_Exception $e) {
            $this->_error($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            Mage::logException($e);
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
        return $customer->getData();
    }

    /**
     * Get customers list
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $data = $this->_getCollectionForRetrieve()->load()->toArray();
        return $data['items'] ?? $data;
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
        } catch (Mage_Core_Exception $e) {
            $this->_error($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            Mage::logException($e);
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

        $this->_applyCollectionModifiers($collection);
        return $collection;
    }
}
