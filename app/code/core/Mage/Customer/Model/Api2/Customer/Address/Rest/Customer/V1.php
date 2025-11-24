<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * API2 class for customer address (customer)
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Api2_Customer_Address_Rest_Customer_V1 extends Mage_Customer_Model_Api2_Customer_Address_Rest
{
    /**
     * Load customer address by id
     *
     * @param int $id
     * @return Mage_Customer_Model_Address
     * @throws Mage_Api2_Exception
     */
    protected function _loadCustomerAddressById($id)
    {
        $customerAddress = parent::_loadCustomerAddressById($id);
        // check owner
        if ($this->getApiUser()->getUserId() != $customerAddress->getCustomerId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        return $customerAddress;
    }

    /**
     * Load customer by id
     *
     * @param int $id
     * @return Mage_Customer_Model_Customer
     * @throws Mage_Api2_Exception
     */
    protected function _loadCustomerById($id)
    {
        $customer = parent::_loadCustomerById($id);
        // check customer accaunt owner
        if ($this->getApiUser()->getUserId() != $customer->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        return $customer;
    }
}
