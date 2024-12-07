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
 * PayPal online logo with additional options
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @deprecated
 */
class Mage_Paypal_Block_Logo extends Mage_Core_Block_Template
{
    /**
     * Return URL for Paypal Landing page
     *
     * @return string
     */
    public function getAboutPaypalPageUrl()
    {
        return $this->_getConfig()->getPaymentMarkWhatIsPaypalUrl(Mage::app()->getLocale());
    }

    /**
     * Getter for paypal config
     *
     * @return Mage_Paypal_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('paypal/config');
    }

    /**
     * Disable block output if logo turned off
     *
     * @return string
     */
    protected function _toHtml()
    {
        $type = $this->getLogoType(); // assigned in layout etc.
        $logoUrl = $this->_getConfig()->getAdditionalOptionsLogoUrl(Mage::app()->getLocale()->getLocaleCode(), $type);
        if (!$logoUrl) {
            return '';
        }
        $country = Mage::getStoreConfig(Mage_Paypal_Helper_Data::MERCHANT_COUNTRY_CONFIG_PATH);
        if ($country == Mage_Paypal_Helper_Data::US_COUNTRY) {
            $this->setTemplate('paypal/partner/us_logo.phtml');
            return parent::_toHtml();
        }
        $this->setLogoImageUrl($logoUrl);
        return parent::_toHtml();
    }
}
