<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Paypal Data helper
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Paypal';

    /**
     * US country code
     */
    public const US_COUNTRY = 'US';

    /**
     * Config path for merchant country
     */
    public const MERCHANT_COUNTRY_CONFIG_PATH = 'paypal/general/merchant_country';

    /**
     * Cache for shouldAskToCreateBillingAgreement()
     *
     * @var bool
     */
    protected static $_shouldAskToCreateBillingAgreement = null;

    /**
     * Check whether customer should be asked confirmation whether to sign a billing agreement
     *
     * @param Mage_Paypal_Model_Config $config
     * @param int $customerId
     * @return bool
     */
    public function shouldAskToCreateBillingAgreement(Mage_Paypal_Model_Config $config, $customerId)
    {
        if (self::$_shouldAskToCreateBillingAgreement === null) {
            self::$_shouldAskToCreateBillingAgreement = false;
            if ($customerId && $config->shouldAskToCreateBillingAgreement()) {
                if (Mage::getModel('sales/billing_agreement')->needToCreateForCustomer($customerId)) {
                    self::$_shouldAskToCreateBillingAgreement = true;
                }
            }
        }
        return self::$_shouldAskToCreateBillingAgreement;
    }

    /**
     * Return backend config for element like JSON
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return false|string
     */
    public function getElementBackendConfig(Varien_Data_Form_Element_Abstract $element)
    {
        $config = $element->getFieldConfig()->backend_congif;
        if (!$config) {
            return false;
        }
        $config = $config->asCanonicalArray();
        if (isset($config['enable_for_countries'])) {
            $config['enable_for_countries'] = explode(',', str_replace(' ', '', $config['enable_for_countries']));
        }
        if (isset($config['disable_for_countries'])) {
            $config['disable_for_countries'] = explode(',', str_replace(' ', '', $config['disable_for_countries']));
        }
        return Mage::helper('core')->jsonEncode($config);
    }

    /**
     * Get selected merchant country code in system configuration
     *
     * @return string
     */
    public function getConfigurationCountryCode()
    {
        $requestParam = Mage_Paypal_Block_Adminhtml_System_Config_Field_Country::REQUEST_PARAM_COUNTRY;
        $countryCode  = Mage::app()->getRequest()->getParam($requestParam);
        if (is_null($countryCode) || preg_match('/^[a-zA-Z]{2}$/', $countryCode) == 0) {
            $countryCode = (string)Mage::getSingleton('adminhtml/config_data')
                ->getConfigDataValue(self::MERCHANT_COUNTRY_CONFIG_PATH);
        }
        if (empty($countryCode)) {
            $countryCode = Mage::helper('core')->getDefaultCountry();
        }
        return $countryCode;
    }

    /**
     * Get HTML representation of transaction id
     *
     * @param string $methodCode
     * @param string $txnId
     * @return string
     */
    public function getHtmlTransactionId($methodCode, $txnId)
    {
        if (in_array($methodCode, [
            Mage_Paypal_Model_Config::METHOD_WPP_DIRECT,
            Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS,
            Mage_Paypal_Model_Config::METHOD_HOSTEDPRO,
            Mage_Paypal_Model_Config::METHOD_WPS,
            ])) {
            /** @var Mage_Paypal_Model_Config $config */
            $config = Mage::getModel('paypal/config')->setMethod($methodCode);
            $url = 'https://www.' . ($config->sandboxFlag ? 'sandbox.' : '')
                . 'paypal.com/cgi-bin/webscr?cmd=_view-a-trans&id=' . $txnId;
            return '<a target="_blank" href="' . $url . '">' . $txnId . '</a>';
        }
        return $txnId;
    }
}
