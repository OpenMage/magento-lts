<?php

declare(strict_types=1);

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Mage_Sales Model Observer
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Observer_AddVatRequestParamsOrderComment implements Mage_Core_Observer_Interface
{
    /**
     * Add VAT validation request date and identifier to order comments
     */
    public function execute(Varien_Event_Observer $observer): self
    {
        /** @var Mage_Sales_Model_Order $orderInstance */
        $orderInstance = $observer->getOrder();
        /** @var Mage_Sales_Model_Order_Address $orderAddress */
        $orderAddress = $this->_getVatRequiredSalesAddress($orderInstance);
        if (!($orderAddress instanceof Mage_Sales_Model_Order_Address)) {
            return $this;
        }

        $vatRequestId = $orderAddress->getVatRequestId();
        $vatRequestDate = $orderAddress->getVatRequestDate();
        if (is_string($vatRequestId) && !empty($vatRequestId) && is_string($vatRequestDate)
            && !empty($vatRequestDate)
        ) {
            $orderHistoryComment = Mage::helper('customer')->__('VAT Request Identifier')
                . ': ' . $vatRequestId . '<br />' . Mage::helper('customer')->__('VAT Request Date')
                . ': ' . $vatRequestDate;
            $orderInstance->addStatusHistoryComment($orderHistoryComment, false);
        }

        return $this;
    }

    /**
     * Retrieve sales address (order or quote) on which tax calculation must be based
     *
     * @param Mage_Core_Model_Abstract $salesModel
     * @param Mage_Core_Model_Store|string|int|null $store
     * @return Mage_Customer_Model_Address_Abstract|null
     */
    protected function _getVatRequiredSalesAddress($salesModel, $store = null)
    {
        $configAddressType = Mage::helper('customer/address')->getTaxCalculationAddressType($store);
        switch ($configAddressType) {
            case Mage_Customer_Model_Address_Abstract::TYPE_SHIPPING:
                $requiredAddress = $salesModel->getShippingAddress();
                break;
            default:
                $requiredAddress = $salesModel->getBillingAddress();
        }
        return $requiredAddress;
    }

    /**
     * Retrieve customer address (default billing or default shipping) ID on which tax calculation must be based
     *
     * @param Mage_Core_Model_Store|string|int|null $store
     * @return int
     */
    protected function _getVatRequiredCustomerAddress(Mage_Customer_Model_Customer $customer, $store = null)
    {
        $configAddressType = Mage::helper('customer/address')->getTaxCalculationAddressType($store);
        switch ($configAddressType) {
            case Mage_Customer_Model_Address_Abstract::TYPE_SHIPPING:
                $requiredAddress = $customer->getDefaultShipping();
                break;
            default:
                $requiredAddress = $customer->getDefaultBilling();
        }
        return $requiredAddress;
    }
}
