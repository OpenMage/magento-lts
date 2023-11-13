<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 class for customer address (customer)
 *
 * @category   Mage
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Api2_Customer_Address_Rest_Customer_V1 extends Mage_Customer_Model_Api2_Customer_Address_Rest
{
    /**
     * Load customer address by id
     *
     * @param int $id
     * @throws Mage_Api2_Exception
     * @return Mage_Customer_Model_Address
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
     * @throws Mage_Api2_Exception
     * @return Mage_Customer_Model_Customer
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
