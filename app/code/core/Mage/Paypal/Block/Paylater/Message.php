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
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Paypal Pay Later
 *
 * Available to specific list of merchant countries only
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Block_Paylater_Message extends Mage_Core_Block_Template
{
    /**
     * AU country code
     */
    const AU_COUNTRY = 'AU';

    /**
     * Valid countries that the feature will work on.
     * Any other country set will disable feature
     *
     * It is likely only ever going to be USA
     *
     * @var array
     */
    protected $_validCountryCodes = [Mage_Paypal_Helper_Data::US_COUNTRY, self::AU_COUNTRY];

    /**
     * Render the block, if enabled
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->isPayLaterEnabled()) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Check if the Pay Later Messages are enabled
     *
     * @return bool
     *
     */
    public function isPayLaterEnabled()
    {
        if (in_array(Mage::getStoreConfig(Mage_Paypal_Helper_Data::MERCHANT_COUNTRY_CONFIG_PATH),
            $this->_validCountryCodes)) {
            return Mage::getStoreConfigFlag(Mage_Paypal_Helper_Data::MERCHANT_PAYLATER_ENABLED_CONFIG_PATH);
        }
        return false;
    }

    /**
     * Get the current product so pay later can populate the price calculations
     *
     * @return mixed Mage_Catalog_Product|bool
     */
    public function getProduct()
    {
        if ($product = Mage::registry('current_product')) {
            return $product;
        }
        return false;
    }

    /**
     * Return the SDK Client ID from config
     *
     * @return mixed
     */
    public function getPaypalSdkClientId()
    {
        return Mage::getStoreConfig(Mage_Paypal_Helper_Data::MERCHANT_PAYLATER_CLIENTID_CONFIG_PATH);
    }

    /**
     * Get the current quote object
     *
     * @return Mage_Sales_Model_Quote|null
     */
    public function getCurrentQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    /**
     * Prevent the SDK script tag from being inserted to DOM multiple times, and not load the same resource multiple times.
     * Example, if it was rendered in minicart, don't do so again if message is displayed on page, example cart/products
     */
    public function canRenderScript()
    {
        if (!Mage::registry('has_render_paylater_script')) {
            Mage::register('has_render_paylater_script', true);
            return true;
        }
        return false;
    }
}
