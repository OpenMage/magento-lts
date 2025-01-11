<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 for catalog_product (Customer)
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Api2_Product_Rest_Customer_V1 extends Mage_Catalog_Model_Api2_Product_Rest
{
    use Mage_Core_Trait_Session;

    /**
     * Current logged in customer
     *
     * @var Mage_Customer_Model_Customer|null
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
        $session = $this->getCustomerSession();
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
