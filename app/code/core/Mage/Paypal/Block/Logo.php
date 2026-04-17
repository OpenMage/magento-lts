<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * PayPal online logo with additional options
 *
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
