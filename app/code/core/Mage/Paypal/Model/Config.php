<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Config model that is aware of all Mage_Paypal payment methods
 * Works with PayPal-specific system configuration
 */
class Mage_Paypal_Model_Config
{
    /**
     * PayPal Standard
     * @var string
     */
    const METHOD_WPS         = 'paypal_standard';

    /**
     * PayPal Website Payments Pro - Express Checkout
     * @var string
     */
    const METHOD_WPP_EXPRESS = 'paypal_express';

    /**
     * PayPal Website Payments Pro - Direct Payments
     * @var string
     */
    const METHOD_WPP_DIRECT  = 'paypal_direct';

    /**
     * Buttons and images
     * @var string
     */
    const EC_FLAVOR_DYNAMIC = 'dynamic';
    const EC_FLAVOR_STATIC  = 'static';
    const EC_BUTTON_TYPE_SHORTCUT = 'ecshortcut';
    const EC_BUTTON_TYPE_MARK     = 'ecmark';
    const PAYMENT_MARK_37x23   = '37x23';
    const PAYMENT_MARK_50x34   = '50x34';
    const PAYMENT_MARK_60x38   = '60x38';
    const PAYMENT_MARK_180x113 = '180x113';

    /**
     * Payment actions
     * @var string
     */
    const PAYMENT_ACTION_SALE  = 'Sale';
    const PAYMENT_ACTION_ORDER = 'Order';
    const PAYMENT_ACTION_AUTH  = 'Authorization';

    /**
     * Fraud management actions
     * @var string
     */
    const FRAUD_ACTION_ACCEPT = 'Acept';
    const FRAUD_ACTION_DENY   = 'Deny';

    /**
     * Capture types (make authorization close or remain open)
     * @var string
     */
    const CAPTURE_TYPE_COMPLETE = 'Complete';
    const CAPTURE_TYPE_NOTCOMPLETE = 'NotComplete';

    /**
     * Refund types
     * @var string
     */
    const REFUND_TYPE_FULL = 'Full';
    const REFUND_TYPE_PARTIAL = 'Partial';

    /**
     * Express Checkout flows
     * @var string
     */
    const EC_SOLUTION_TYPE_SOLE = 'Sole';
    const EC_SOLUTION_TYPE_MARK = 'Mark';

    /**
     * Payment data transfer methods (Standard)
     *
     * @var string
     */
    const WPS_TRANSPORT_IPN      = 'ipn';
    const WPS_TRANSPORT_PDT      = 'pdt';
    const WPS_TRANSPORT_IPN_PDT  = 'ipn_n_pdt';

    /**
     * Current payment method code
     * @var string
     */
    protected $_methodCode = null;

    /**
     * Current store id
     * @var int
     */
    protected $_storeId = null;

    /**
     * Instructions for generating proper BN code
     *
     * @var array
     */
    protected $_buildNotationPPMap = array(
        'paypal_standard' => 'WPS',
        'paypal_express'  => 'EC',
        'paypal_direct'   => 'DP',
    );

    /**
     * Legacy BN codes:
     * 'Varien_Cart_EC_US', 'Varien_Cart_DP_US', 'Varien_Cart_WPS_US', 'Varien_Cart_EC_UK', 'Varien_Cart_DP_UK'
     * @deprecated
     * @var string
     */
    protected $_bnLegacyCountryCode = 'US';

    /**
     * Style system config map (Express Checkout)
     * @var array
     */
    protected $_ecStyleConfigMap = array(
        'page_style'    => 'page_style',
        'paypal_hdrimg' => 'hdrimg',
        'paypal_hdrbordercolor' => 'hdrbordercolor',
        'paypal_hdrbackcolor'   => 'hdrbackcolor',
        'paypal_payflowcolor'   => 'payflowcolor',
    );

    /**
     * Currency codes supported by PayPal methods
     * @var array
     */
    protected $_supportedCurrencyCodes = array('AUD', 'CAD', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'ILS', 'JPY', 'MXN',
        'NOK', 'NZD', 'PLN', 'GBP', 'SGD', 'SEK', 'CHF', 'USD');

    /**
     * Locale codes supported by misc images (marks, shortcuts etc)
     * @var array
     * @see https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_ECButtonIntegration#id089QD0O0TX4__id08AH904I0YK
     */
    protected $_supportedImageLocales = array('de_DE', 'en_AU', 'en_GB', 'en_US', 'es_ES', 'es_XC', 'fr_FR',
        'fr_XC', 'it_IT', 'ja_JP', 'nl_NL', 'pl_PL', 'zh_CN', 'zh_XC',
    );

    /**
     * Set method and store id, if specified
     * @param array $params
     */
    public function __construct($params = array())
    {
        if ($params) {
            $method = array_shift($params);
            $this->setMethod($method);
            if ($params) {
                $storeId = array_shift($params);
                $this->setStoreId($storeId);
            }
        }
    }

    /**
     * Method code setter
     *
     * @param string|Mage_Payment_Model_Method_Abstract $method
     * @return Mage_Paypal_Model_Config
     */
    public function setMethod($method)
    {
        if ($method instanceof Mage_Payment_Model_Method_Abstract) {
            $this->_methodCode = $method->getCode();
        } elseif (is_string($method)) {
            $this->_methodCode = $method;
        }
        return $this;
    }

    /**
     * Payment method instance code getter
     * @return string
     */
    public function getMethodCode()
    {
        return $this->_methodCode;
    }

    /**
     * Store ID setter
     * @param int $storeId
     * @return Mage_Paypal_Model_Config
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = (int)$storeId;
        return $this;
    }

    /**
     * Config field magic getter
     * The specified key can be either in camelCase or under_score format
     * Tries to map specified value according to set payment method code, into the configuration value
     * Sets the values into public class parameters, to avoid redundant calls of this method
     *
     * @param string $key
     * @return string|null
     */
    public function __get($key)
    {
        $underscored = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $key));
        $value = Mage::getStoreConfig($this->_getSpecificConfigPath($underscored), $this->_storeId);
        $this->$key = $value;
        $this->$underscored = $value;
        return $value;
    }

    /**
     * Get url for dispatching customer to express checkout start
     * @param string $token
     * @return string
     */
    public function getExpressCheckoutStartUrl($token)
    {
        return $this->getPaypalUrl(array(
            'cmd'   => '_express-checkout',
            'token' => $token,
        ));
    }

    /**
     * Get url that allows to edit checkout details on paypal side
     * @param $token
     * @return string
     */
    public function getExpressCheckoutEditUrl($token)
    {
        return $this->getPaypalUrl(array(
            'cmd'        => '_express-checkout',
            'useraction' => 'continue',
            'token'      => $token,
        ));
    }

    /**
     * Get url for additional actions that PayPal may require customer to do after placing the order.
     * For instance, redirecting customer to bank for payment confirmation.
     * @param string $token
     * @return string
     */
    public function getExpressCheckoutCompleteUrl($token)
    {
        return $this->getPaypalUrl(array(
            'cmd'   => '_complete-express-checkout',
            'token' => $token,
        ));
    }

     /**
     * PayPal web URL generic getter
     *
     * @param array $params
     * @return string
     */
    public function getPaypalUrl(array $params = array())
    {
        return sprintf('https://www.%spaypal.com/webscr%s',
            $this->sandboxFlag ? 'sandbox.' : '',
            $params ? '?' . http_build_query($params) : ''
        );
    }

    /**
     * Whether Express Checkout button should be rendered dynamically
     * @return bool
     */
    public function areButtonsDynamic()
    {
        return $this->buttonFlavor === self::EC_FLAVOR_DYNAMIC;
    }

    /**
     * Express checkout shortcut pic URL getter
     * PayPal will ignore "pal", if there is no total amount specified
     *
     * @param string $localeCode
     * @param float $orderTotal
     * @param string $pal encrypted summary about merchant
     * @see Paypal_Model_Api_Nvp::callGetPalDetails()
     */
    public function getExpressCheckoutShortcutImageUrl($localeCode, $orderTotal = null, $pal = null)
    {
        if ($this->areButtonsDynamic()) {
            return $this->_getDynamicImageUrl($this->buttonType, $localeCode, $orderTotal, $pal);
            // return $this->_getDynamicImageUrl(self::EC_BUTTON_TYPE_SHORTCUT, $localeCode, $orderTotal, $pal);
        }
        if ($this->buttonType === self::EC_BUTTON_TYPE_MARK) {
            return $this->getPaymentMarkImageUrl($localeCode);
        }
        return sprintf('https://www.paypal.com/%s/i/btn/btn_xpressCheckout.gif',
            $this->_getSupportedLocaleCode($localeCode));
    }

    /**
     * Get PayPal "mark" image URL
     * Supposed to be used on payment methods selection
     * $staticSize is applicable for static images only
     *
     * @param string $localeCode
     * @param float $orderTotal
     * @param string $pal
     * @param string $staticSize
     */
    public function getPaymentMarkImageUrl($localeCode, $orderTotal = null, $pal = null, $staticSize = null)
    {
        if ($this->areButtonsDynamic()) {
            return $this->_getDynamicImageUrl(self::EC_BUTTON_TYPE_MARK, $localeCode, $orderTotal, $pal);
        }

        if (null === $staticSize) {
            $staticSize = $this->paymentMarkSize;
        }
        switch ($staticSize) {
            case self::PAYMENT_MARK_37x23:
            case self::PAYMENT_MARK_50x34:
            case self::PAYMENT_MARK_60x38:
            case self::PAYMENT_MARK_180x113:
                break;
            default:
                $staticSize = self::PAYMENT_MARK_37x23;
        }
        return sprintf('https://www.paypal.com/%s/i/logo/PayPal_mark_%s.gif',
            $this->_getSupportedLocaleCode($localeCode), $staticSize);
    }

    /**
     * Get "What Is PayPal" localized URL
     * Supposed to be used with "mark" as popup window
     *
     * @param Mage_Core_Model_Locale $locale
     */
    public function getPaymentMarkWhatIsPaypalUrl(Mage_Core_Model_Locale $locale = null)
    {
        $countryCode = 'US';
        if (null !== $locale) {
            $shouldEmulate = (null !== $this->_storeId) && (Mage::app()->getStore()->getId() != $this->_storeId);
            if ($shouldEmulate) {
                $locale->emulate($this->_storeId);
            }
            $countryCode = $locale->getLocale()->getRegion();
            if ($shouldEmulate) {
                $locale->revert();
            }
        }
        return sprintf('https://www.paypal.com/%s/cgi-bin/webscr?cmd=xpt/Marketing/popup/OLCWhatIsPayPal-outside',
            strtolower($countryCode)
        );
    }

    /**
     * Getter for Solution banner images
     *
     * @param string $localeCode
     * @param bool $isVertical
     * @param bool $isEcheck
     */
    public function getSolutionImageUrl($localeCode, $isVertical = false, $isEcheck = false)
    {
        return sprintf('https://www.paypal.com/%s/i/bnr/%s_solution_PP%s.gif',
            $this->_getSupportedLocaleCode($localeCode),
            $isVertical ? 'vertical' : 'horizontal', $isEcheck ? 'eCheck' : ''
        );
    }

    /**
     * BN code getter
     *
     * @param string $countryCode ISO 3166-1
     */
    public function getBuildNotationCode($countryCode = null)
    {
        $product = 'WPP';
        if ($this->_methodCode && isset($this->_buildNotationPPMap[$this->_methodCode])) {
            $product = $this->_buildNotationPPMap[$this->_methodCode];
        }
        if (null === $countryCode) {
            $countryCode = $this->_bnLegacyCountryCode;
            // $countryCode = Mage::getStoreConfig('shipping/origin/country_id', $this->_storeId);
        }
        $format = 'Varien_ShoppingCart_%s_%s';
        if ($this->_bnLegacyCountryCode) {
            $format = 'Varien_Cart_%s_%s';
        }
        return sprintf($format, $product, $countryCode);
    }

    /**
     * Express Checkout button "flavors" source getter
     * @return array
     */
    public function getExpressCheckoutButtonFlavors()
    {
        return array(
            self::EC_FLAVOR_DYNAMIC => Mage::helper('paypal')->__('Dynamic'),
            self::EC_FLAVOR_STATIC  => Mage::helper('paypal')->__('Static'),
        );
    }

    /**
     * Express Checkout button types source getter
     * @return array
     */
    public function getExpressCheckoutButtonTypes()
    {
        return array(
            self::EC_BUTTON_TYPE_SHORTCUT => Mage::helper('paypal')->__('Shortcut'),
            self::EC_BUTTON_TYPE_MARK     => Mage::helper('paypal')->__('Acceptance Mark Image'),
        );
    }

    /**
     * Payment actions source getter
     * @return array
     */
    public function getPaymentActions()
    {
        return array(
            self::PAYMENT_ACTION_AUTH  => Mage::helper('paypal')->__('Authorization'),
            // self::PAYMENT_ACTION_ORDER => Mage::helper('paypal')->__('Order'), // not supported yet
            self::PAYMENT_ACTION_SALE  => Mage::helper('paypal')->__('Sale'),
        );
    }

    /**
     * Mapper from PayPal-specific payment actions to Magento payment actions
     * @return string|null
     */
    public function getPaymentAction()
    {
        switch ($this->paymentAction) {
            case self::PAYMENT_ACTION_AUTH:
                return Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE;
            case self::PAYMENT_ACTION_SALE:
                return Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE;
            case self::PAYMENT_ACTION_ORDER:
                return;
        }
    }

    /**
     * Express Checkout "solution types" source getter
     * @return array
     */
    public function getExpressCheckoutSolutionTypes()
    {
        return array(
            self::EC_SOLUTION_TYPE_SOLE => Mage::helper('paypal')->__('Express Checkout for Auctions'),
            self::EC_SOLUTION_TYPE_MARK => Mage::helper('paypal')->__('Normal Express Checkout'),
        );
    }

    /**
     * Payment data delivery methods getter for PayPal Standard
     * @return array
     */
    public function getWpsPaymentDeliveryMethods()
    {
        return array(
            self::WPS_TRANSPORT_IPN      => Mage::helper('adminhtml')->__('IPN (Instant Payment Notification) Only'),
            // not supported yet:
//            self::WPS_TRANSPORT_PDT      => Mage::helper('adminhtml')->__('PDT (Payment Data Transfer) Only'),
//            self::WPS_TRANSPORT_IPN_PDT  => Mage::helper('adminhtml')->__('Both IPN and PDT'),
        );
    }

    /**
     * PayPal Direct cc types source getter
     *
     * @return array
     */
    public function getDirectCcTypesAsOptionArray()
    {
        $model = Mage::getModel('payment/source_cctype')->setAllowedTypes(array('VI', 'MC', 'AE', 'DI', 'SS', 'OT'));
        return $model->toOptionArray();
    }

    /**
     * Check whether specified currency code is supported
     * @param string $code
     * @return bool
     */
    public function isCurrencyCodeSupported($code)
    {
        return in_array($code, $this->_supportedCurrencyCodes);
    }

    /**
     * Export page style current settings to specified object
     * @param Varien_Object $to
     */
    public function exportExpressCheckoutStyleSettings(Varien_Object $to)
    {
        foreach ($this->_ecStyleConfigMap as $key => $exportKey) {
            if ($this->$key) {
                $to->setData($exportKey, $this->$key);
            }
        }
    }

    /**
     * Whether current payment method works with credit cards
     * @return bool
     */
    public function doesWorkWithCc()
    {
        return $this->_methodCode === self::METHOD_WPP_DIRECT;
    }

    /**
     * Dynamic PayPal image URL getter
     * Also can render dynamic Acceptance Mark
     *
     * @param string $type
     * @param string $localeCode
     * @param float $orderTotal
     * @param string $pal
     */
    protected function _getDynamicImageUrl($type, $localeCode, $orderTotal, $pal)
    {
        $params = array(
            'cmd'        => '_dynamic-image',
            'buttontype' => $type,
            'locale'     => $this->_getSupportedLocaleCode($localeCode),
        );
        if ($orderTotal) {
            $params['ordertotal'] = sprintf('%.2F', $orderTotal);
            if ($pal) {
                $params['pal'] = $pal;
            }
        }
        return sprintf('https://fpdbs%s.paypal.com/dynamicimageweb?%s',
            $this->sandboxFlag ? '.sandbox' : '', http_build_query($params)
        );
    }

    /**
     * Check whether specified locale code is supported. Fallback to en_US
     *
     * @param string $localeCode
     * @return string
     */
    protected function _getSupportedLocaleCode($localeCode = null)
    {
        if (!$localeCode || !in_array($localeCode, $this->_supportedImageLocales)) {
            return 'en_US';
        }
        return $localeCode;
    }

    /**
     * Map any supported payment method into a config path by specified field name
     * @param string $fieldName
     * @return string|null
     */
    protected function _getSpecificConfigPath($fieldName)
    {
        if (self::METHOD_WPS === $this->_methodCode) {
            return $this->_mapStandardFieldset($fieldName);
        } elseif (self::METHOD_WPP_EXPRESS === $this->_methodCode ||  self::METHOD_WPP_DIRECT === $this->_methodCode) {
            $path = self::METHOD_WPP_EXPRESS === $this->_methodCode
                ? $this->_mapExpressFieldset($fieldName)
                : $this->_mapDirectFieldset($fieldName)
             ;
            if (!$path) {
                $path = $this->_mapWppFieldset($fieldName);
            }
            if (!$path) {
                $path = $this->_mapWppStyleFieldset($fieldName);
            }
            return $path;
        }
    }

    /**
     * Map PayPal Standard config fields
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapStandardFieldset($fieldName)
    {
        switch ($fieldName)
        {
            case 'business_account':
            case 'debug_flag':
            case 'sandbox_flag':
                return "paypal/wps/{$fieldName}";
            case 'active':
            case 'title':
            case 'payment_action':
            case 'types':
            case 'order_status':
            case 'transaction_type':
            case 'sort_order':
            case 'allowspecific':
            case 'specificcountry':
            case 'line_items_enabled':
            case 'line_items_summary':
                return 'payment/' . self::METHOD_WPS . "/{$fieldName}";
            default:
                return $this->_mapGenericStyleFieldset($fieldName);
        }
    }

    /**
     * Map PayPal Website Payments Pro common style config fields
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapWppStyleFieldset($fieldName)
    {
        switch ($fieldName)
        {
            case 'button_flavor':
                return "paypal/style/{$fieldName}";
            default:
                return $this->_mapGenericStyleFieldset($fieldName);
        }
    }

    /**
     * Map PayPal common style config fields
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapGenericStyleFieldset($fieldName)
    {
        switch ($fieldName) {
            case 'page_style':
            case 'logo_url':
            case 'paypal_hdrimg':
            case 'paypal_hdrbackcolor':
            case 'paypal_hdrbordercolor':
            case 'paypal_payflowcolor':
                return "paypal/style/{$fieldName}";
        }
    }

    /**
     * Map PayPal Website Payments Pro common config fields
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapWppFieldset($fieldName)
    {
        switch ($fieldName)
        {
            case 'api_password':
            case 'api_signature':
            case 'api_username':
            case 'business_account':
            case 'debug_flag':
            case 'paypal_url':
            case 'proxy_host':
            case 'proxy_port':
            case 'sandbox_flag':
            case 'use_proxy':
                return "paypal/wpp/{$fieldName}";
        }
    }

    /**
     * Map PayPal Express config fields
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapExpressFieldset($fieldName)
    {
        switch ($fieldName)
        {
            case 'active':
            case 'allowspecific':
            case 'fraud_filter':
            case 'invoice_email_copy':
            case 'line_items_enabled':
            case 'order_status':
            case 'payment_action':
            case 'solution_type':
            case 'sort_order':
            case 'specificcountry':
            case 'title':
            case 'visible_on_cart':
                return 'payment/' . self::METHOD_WPP_EXPRESS . "/{$fieldName}";
            case 'button_type':
                return "paypal/style/{$fieldName}";
        }
    }

    /**
     * Map PayPal Direct config fields
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapDirectFieldset($fieldName)
    {
        switch ($fieldName)
        {
            case 'active':
            case 'allowspecific':
            case 'cctypes':
            case 'centinel':
            case 'centinel_require_enrollment':
            case 'fraud_filter':
            case 'line_items_enabled':
            case 'order_status':
            case 'payment_action':
            case 'sort_order':
            case 'specificcountry':
            case 'title':
                return 'payment/' . self::METHOD_WPP_DIRECT . "/{$fieldName}";
        }
    }
}
