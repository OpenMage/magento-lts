<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * API2 for catalog_product (Customer)
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Api2_Product_Rest_Customer_V1 extends Mage_Catalog_Model_Api2_Product_Rest
{
    /**
     * Current logged in customer
     *
     * @var null|Mage_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * Get customer group
     *
     * @return int
     */
    protected function _getCustomerGroupId()
    {
        return $this->_getCustomer()->getGroupId();
    }

    /**
     * Define product price with or without taxes
     *
     * @param float $price
     * @param bool $withTax
     * @return float
     */
    protected function _applyTaxToPrice($price, $withTax = true)
    {
        $customer = $this->_getCustomer();
        /** @var Mage_Customer_Model_Session $session */
        $session = Mage::getSingleton('customer/session');
        $session->setCustomerId($customer->getId());

        $price = $this->_getPrice(
            $price,
            $withTax,
            $customer->getPrimaryShippingAddress(),
            $customer->getPrimaryBillingAddress(),
            $customer->getTaxClassId(),
        );
        $session->setCustomerId(null);

        return $price;
    }

    /**
     * Retrieve current customer
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
        if (is_null($this->_customer)) {
            /** @var Mage_Customer_Model_Customer $customer */
            $customer = Mage::getModel('customer/customer')->load($this->getApiUser()->getUserId());
            if (!$customer->getId()) {
                $this->_critical('Customer not found.', Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
            }

            $this->_customer = $customer;
        }

        return $this->_customer;
    }
}
