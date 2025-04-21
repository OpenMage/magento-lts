<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * API2 class for customer address
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Api2_Customer_Address extends Mage_Api2_Model_Resource
{
    /**
     * Resource specific method to retrieve attributes' codes. May be overridden in child.
     *
     * @return array
     */
    protected function _getResourceAttributes()
    {
        return $this->getEavAttributes(Mage_Api2_Model_Auth_User_Admin::USER_TYPE != $this->getUserType());
    }

    /**
     * Get customer address resource validator instance
     *
     * @return Mage_Customer_Model_Api2_Customer_Address_Validator
     */
    protected function _getValidator()
    {
        return Mage::getModel('customer/api2_customer_address_validator', ['resource' => $this]);
    }

    /**
     * Is specified address a default billing address?
     *
     * @return bool
     */
    protected function _isDefaultBillingAddress(Mage_Customer_Model_Address $address)
    {
        return $address->getCustomer()->getDefaultBilling() == $address->getId();
    }

    /**
     * Is specified address a default shipping address?
     *
     * @return bool
     */
    protected function _isDefaultShippingAddress(Mage_Customer_Model_Address $address)
    {
        return $address->getCustomer()->getDefaultShipping() == $address->getId();
    }

    /**
     * Get region id by name or code
     * If id is not found then return passed $region
     *
     * @param string $region
     * @param string $countryId
     * @return int|string
     */
    protected function _getRegionIdByNameOrCode($region, $countryId)
    {
        /** @var Mage_Directory_Model_Resource_Region_Collection $collection */
        $collection = Mage::getResourceModel('directory/region_collection');

        $collection->getSelect()
            ->reset() // to avoid locale usage
            ->from(['main_table' => $collection->getMainTable()], 'region_id');

        $collection->addCountryFilter($countryId)
            ->addFieldToFilter(['default_name', 'code'], [$region, $region]);

        $id = $collection->getResource()->getReadConnection()->fetchOne($collection->getSelect());

        return $id ? (int) $id : $region;
    }

    /**
     * Load customer address by id
     *
     * @param int $id
     * @return Mage_Customer_Model_Address
     */
    protected function _loadCustomerAddressById($id)
    {
        /** @var Mage_Customer_Model_Address $address */
        $address = Mage::getModel('customer/address')->load($id);

        if (!$address->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        $address->addData($this->_getDefaultAddressesInfo($address));

        return $address;
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
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getModel('customer/customer')->load($id);
        if (!$customer->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        return $customer;
    }
}
