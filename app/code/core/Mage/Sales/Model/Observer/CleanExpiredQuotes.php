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
class Mage_Sales_Model_Observer_CleanExpiredQuotes implements Mage_Core_Observer_Interface
{
    /**
     * Expire quotes additional fields to filter
     *
     * @var array
     */
    protected $_expireQuotesFilterFields = [];

    /**
     * Clean expired quotes (cron process)
     */
    public function execute(Varien_Event_Observer $observer): self
    {
        Mage::dispatchEvent('clear_expired_quotes_before', ['sales_observer' => $this]);
        $lifetimes = Mage::getConfig()->getStoresConfigByPath('checkout/cart/delete_quote_after');

        foreach ($lifetimes as $storeId => $day) {
            $day = (int) $day;
            $lifetime = 86400 * $day;

            /** @var Mage_Sales_Model_Resource_Quote_Collection $quotes */
            $quotes = Mage::getResourceModel('sales/quote_collection');
            $quotes->addFieldToFilter('store_id', $storeId);
            $quotes->addFieldToFilter('updated_at', ['to' => date('Y-m-d', time() - $lifetime)]);
            if ($day == 0) {
                $quotes->addFieldToFilter('is_active', 0);
            }

            foreach ($this->getExpireQuotesAdditionalFilterFields() as $field => $condition) {
                $quotes->addFieldToFilter($field, $condition);
            }

            $quotes->walk('delete');
        }

        return $this;
    }

    /**
     * Retrieve expire quotes additional fields to filter
     *
     * @return array
     */
    public function getExpireQuotesAdditionalFilterFields()
    {
        return $this->_expireQuotesFilterFields;
    }

    /**
     * Set expire quotes additional fields to filter
     *
     * @return $this
     */
    public function setExpireQuotesAdditionalFilterFields(array $fields)
    {
        $this->_expireQuotesFilterFields = $fields;
        return $this;
    }
}
