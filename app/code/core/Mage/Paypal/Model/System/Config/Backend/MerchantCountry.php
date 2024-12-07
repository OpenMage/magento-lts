<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Backend model for merchant country. Default country used instead of empty value.
 *
 * @category   Mage
 * @package    Mage_Paypal
 */
class Mage_Paypal_Model_System_Config_Backend_MerchantCountry extends Mage_Core_Model_Config_Data
{
    /**
     * Config path to default country
     * @deprecated since 1.4.1.0
     * @var string
     */
    public const XML_PATH_COUNTRY_DEFAULT = 'general/country/default';

    /**
     * Substitute empty value with Default country.
     * @return $this
     */
    protected function _afterLoad()
    {
        $value = (string)$this->getValue();
        if (empty($value)) {
            if ($this->getWebsite()) {
                $defaultCountry = Mage::app()->getWebsite($this->getWebsite())
                    ->getConfig(Mage_Core_Helper_Data::XML_PATH_DEFAULT_COUNTRY);
            } else {
                $defaultCountry = Mage::helper('core')->getDefaultCountry($this->getStore());
            }
            $this->setValue($defaultCountry);
        }
        return $this;
    }
}
