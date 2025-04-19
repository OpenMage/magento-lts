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
class Mage_Sales_Model_Observer_ChangeQuoteCustomerGroupId implements Mage_Core_Observer_Interface
{
    /**
     * Handle customer VAT number if needed on collect_totals_before event of quote address
     *
     * @throws Mage_Core_Model_Store_Exception
     */
    public function execute(Varien_Event_Observer $observer): self
    {
        $addressHelper = Mage::helper('customer/address');

        /** @var Mage_Sales_Model_Quote_Address $quoteAddress */
        $quoteAddress = $observer->getDataByKey('quote_address');
        $quoteInstance = $quoteAddress->getQuote();
        $customerInstance = $quoteInstance->getCustomer();
        $isDisableAutoGroupChange = $customerInstance->getDisableAutoGroupChange();

        $storeId = $customerInstance->getStore();

        $configAddressType = Mage::helper('customer/address')->getTaxCalculationAddressType($storeId);

        // When VAT is based on billing address then Magento have to handle only billing addresses
        $additionalBillingAddressCondition = ($configAddressType == Mage_Customer_Model_Address_Abstract::TYPE_BILLING)
            ? $configAddressType != $quoteAddress->getAddressType() : false;
        // Handle only addresses that corresponds to VAT configuration
        if (!$addressHelper->isVatValidationEnabled($storeId) || $additionalBillingAddressCondition) {
            return $this;
        }

        $customerHelper = Mage::helper('customer');

        $customerCountryCode = $quoteAddress->getCountryId();
        $customerVatNumber = $quoteAddress->getVatId();

        if ((empty($customerVatNumber) || !Mage::helper('core')->isCountryInEU($customerCountryCode))
            && !$isDisableAutoGroupChange
        ) {
            $groupId = ($customerInstance->getId()) ? $customerHelper->getDefaultCustomerGroupId($storeId)
                : Mage_Customer_Model_Group::NOT_LOGGED_IN_ID;

            $quoteAddress->setPrevQuoteCustomerGroupId($quoteInstance->getCustomerGroupId());
            $customerInstance->setGroupId($groupId);
            $quoteInstance->setCustomerGroupId($groupId);

            return $this;
        }

        /** @var Mage_Core_Helper_Data $coreHelper */
        $coreHelper = Mage::helper('core');
        $merchantCountryCode = $coreHelper->getMerchantCountryCode();
        $merchantVatNumber = $coreHelper->getMerchantVatNumber();

        $gatewayResponse = null;
        if ($addressHelper->getValidateOnEachTransaction($storeId)
            || $customerCountryCode != $quoteAddress->getValidatedCountryCode()
            || $customerVatNumber != $quoteAddress->getValidatedVatNumber()
        ) {
            // Send request to gateway
            $gatewayResponse = $customerHelper->checkVatNumber(
                $customerCountryCode,
                $customerVatNumber,
                ($merchantVatNumber !== '') ? $merchantCountryCode : '',
                $merchantVatNumber,
            );

            // Store validation results in corresponding quote address
            $quoteAddress->setVatIsValid((int) $gatewayResponse->getIsValid())
                ->setVatRequestId($gatewayResponse->getRequestIdentifier())
                ->setVatRequestDate($gatewayResponse->getRequestDate())
                ->setVatRequestSuccess($gatewayResponse->getRequestSuccess())
                ->setValidatedVatNumber($customerVatNumber)
                ->setValidatedCountryCode($customerCountryCode)
                ->save();
        } else {
            // Restore validation results from corresponding quote address
            $gatewayResponse = new Varien_Object([
                'is_valid' => (int) $quoteAddress->getVatIsValid(),
                'request_identifier' => (string) $quoteAddress->getVatRequestId(),
                'request_date' => (string) $quoteAddress->getVatRequestDate(),
                'request_success' => (bool) $quoteAddress->getVatRequestSuccess(),
            ]);
        }

        // Magento always has to emulate group even if customer uses default billing/shipping address
        if (!$isDisableAutoGroupChange) {
            $groupId = $customerHelper->getCustomerGroupIdBasedOnVatNumber(
                $customerCountryCode,
                $gatewayResponse,
                $customerInstance->getStore(),
            );
        } else {
            $groupId = $quoteInstance->getCustomerGroupId();
        }

        if ($groupId) {
            $quoteAddress->setPrevQuoteCustomerGroupId($quoteInstance->getCustomerGroupId());
            $customerInstance->setGroupId($groupId);
            $quoteInstance->setCustomerGroupId($groupId);
        }

        return $this;
    }
}
