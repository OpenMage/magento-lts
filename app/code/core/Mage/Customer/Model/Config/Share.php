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
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer sharing config model
 *
 * @category   Mage
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Config_Share extends Mage_Core_Model_Config_Data
{
    /**
     * Xml config path to customers sharing scope value
     *
     */
    public const XML_PATH_CUSTOMER_ACCOUNT_SHARE = 'customer/account_share/scope';

    /**
     * Possible customer sharing scopes
     *
     */
    public const SHARE_GLOBAL  = 0;
    public const SHARE_WEBSITE = 1;

    /**
     * Check whether current customers sharing scope is global
     *
     * @return bool
     */
    public function isGlobalScope()
    {
        return !$this->isWebsiteScope();
    }

    /**
     * Check whether current customers sharing scope is website
     *
     * @return bool
     */
    public function isWebsiteScope()
    {
        return Mage::getStoreConfig(self::XML_PATH_CUSTOMER_ACCOUNT_SHARE) == self::SHARE_WEBSITE;
    }

    /**
     * Get possible sharing configuration options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            self::SHARE_GLOBAL  => Mage::helper('customer')->__('Global'),
            self::SHARE_WEBSITE => Mage::helper('customer')->__('Per Website'),
        ];
    }

    /**
     * Check for email duplicates before saving customers sharing options
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function _beforeSave()
    {
        $value = $this->getValue();
        if ($value == self::SHARE_GLOBAL) {
            if (Mage::getResourceSingleton('customer/customer')->findEmailDuplicates()) {
                Mage::throwException(
                    Mage::helper('customer')->__('Cannot share customer accounts globally because some customer accounts with the same emails exist on multiple websites and cannot be merged.'),
                );
            }
        }
        return $this;
    }
}
