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
class Mage_Sales_Model_Observer_CustomerSaveAfter implements Mage_Core_Observer_Interface
{
    /**
     * Set new customer group to all his quotes
     *
     * @throws Mage_Core_Model_Store_Exception
     * @throws Mage_Core_Exception
     * @throws Throwable
     */
    public function execute(Varien_Event_Observer $observer): self
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = $observer->getEvent()->getDataByKey('customer');

        if ($customer->getGroupId() !== $customer->getOrigData('group_id')) {
            /**
             * It is needed to process customer's quotes for all websites
             * if customer accounts are shared between all of them
             */
            $websites = (Mage::getSingleton('customer/config_share')->isWebsiteScope())
                ? [Mage::app()->getWebsite($customer->getWebsiteId())]
                : Mage::app()->getWebsites();

            /** @var Mage_Sales_Model_Quote $quote */
            $quote = Mage::getSingleton('sales/quote');

            foreach ($websites as $website) {
                $quote->setWebsite($website);
                $quote->loadByCustomer($customer);

                if ($quote->getId()) {
                    $quote->setCustomerGroupId($customer->getGroupId());
                    $quote->collectTotals();
                    $quote->save();
                }
            }
        }

        return $this;
    }
}
