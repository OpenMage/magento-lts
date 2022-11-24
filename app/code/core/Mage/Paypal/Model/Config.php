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
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Config model that is aware of all Mage_Paypal payment methods
 * Works with PayPal-specific system configuration
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @property mixed $allow_ba_signup;
 * @property mixed $api_cert;
 * @property mixed $api_password;
 * @property mixed $api_signature;
 * @property mixed $api_username;
 * @property mixed $apiAuthentication;
 * @property mixed $apiPassword;
 * @property mixed $apiSignature;
 * @property mixed $apiUsername;
 * @property mixed $business_account;
 * @property mixed $businessAccount;
 * @property mixed $buttonFlavor;
 * @property mixed $buttonType;
 * @property mixed $cctypes;
 * @property mixed $debug;
 * @property mixed $lineItemsEnabled;
 * @property mixed $lineItemsSummary;
 * @property mixed $paymentAction;
 * @property mixed $paymentMarkSize;
 * @property mixed $requireBillingAddress;
 * @property mixed $sandboxFlag;
 * @property mixed $solutionType;
 * @property mixed $transferShippingOptions;
 * @property mixed $verifyPeer;
 * @property mixed $visible_on_cart;
 * @property mixed $visible_on_product;
 */
class Mage_Paypal_Model_Config
{
    /**
     * PayPal Standard
     * @var string
     */
    public const METHOD_WPS         = 'paypal_standard';

    /**
     * US locale
     */
    public const LOCALE_US = 'en_US';

    /**
     * PayPal Website Payments Pro - Express Checkout
     * @var string
     */
    public const METHOD_WPP_EXPRESS = 'paypal_express';

    /**
     * PayPal Bill Me Later - Express Checkout
     * @var string
     */
    public const METHOD_BML = 'paypal_express_bml';

    /**
     * PayPal Website Payments Pro - Direct Payments
     * @var string
     */
    public const METHOD_WPP_DIRECT  = 'paypal_direct';

    /**
     * Direct Payments (Payflow Edition)
     * @var string
     */
    public const METHOD_WPP_PE_DIRECT  = 'paypaluk_direct';

    /**
     * PayPal Bill Me Later - Express Checkout (Payflow Edition)
     * @var string
     */
    public const METHOD_WPP_PE_BML = 'paypaluk_express_bml';

    /**
     * Express Checkout (Payflow Edition)
     * @var string
     */
    public const METHOD_WPP_PE_EXPRESS  = 'paypaluk_express';

    /**
     * Payflow Pro Gateway
     * @var string
     */
    public const METHOD_PAYFLOWPRO         = 'verisign';

    public const METHOD_PAYFLOWLINK        = 'payflow_link';
    public const METHOD_PAYFLOWADVANCED    = 'payflow_advanced';

    public const METHOD_HOSTEDPRO          = 'hosted_pro';

    public const METHOD_BILLING_AGREEMENT  = 'paypal_billing_agreement';

    /**
     * Buttons and images
     * @var string
     */
    public const EC_FLAVOR_DYNAMIC = 'dynamic';
    public const EC_FLAVOR_STATIC  = 'static';
    public const EC_BUTTON_TYPE_SHORTCUT = 'ecshortcut';
    public const EC_BUTTON_TYPE_MARK     = 'ecmark';
    public const PAYMENT_MARK_37X23   = '37x23';
    public const PAYMENT_MARK_50X34   = '50x34';
    public const PAYMENT_MARK_60X38   = '60x38';
    public const PAYMENT_MARK_180X113 = '180x113';

    public const DEFAULT_LOGO_TYPE = 'wePrefer_150x60';

    /**
     * Payment actions
     * @var string
     */
    public const PAYMENT_ACTION_SALE  = 'Sale';
    public const PAYMENT_ACTION_ORDER = 'Order';
    public const PAYMENT_ACTION_AUTH  = 'Authorization';

    /**
     * Authorization amounts for Account Verification
     *
     * @deprecated since 1.6.2.0
     * @var int
     */
    public const AUTHORIZATION_AMOUNT_ZERO = 0;
    public const AUTHORIZATION_AMOUNT_ONE = 1;
    public const AUTHORIZATION_AMOUNT_FULL = 2;

    /**
     * Require Billing Address
     * @var int
     */
    public const REQUIRE_BILLING_ADDRESS_NO = 0;
    public const REQUIRE_BILLING_ADDRESS_ALL = 1;
    public const REQUIRE_BILLING_ADDRESS_VIRTUAL = 2;

    /**
     * Fraud management actions
     * @var string
     */
    public const FRAUD_ACTION_ACCEPT = 'Acept';
    public const FRAUD_ACTION_DENY   = 'Deny';

    /**
     * Refund types
     * @var string
     */
    public const REFUND_TYPE_FULL = 'Full';
    public const REFUND_TYPE_PARTIAL = 'Partial';

    /**
     * Express Checkout flows
     * @var string
     */
    public const EC_SOLUTION_TYPE_SOLE = 'Sole';
    public const EC_SOLUTION_TYPE_MARK = 'Mark';

    /**
     * Payment data transfer methods (Standard)
     *
     * @var string
     */
    public const WPS_TRANSPORT_IPN      = 'ipn';
    public const WPS_TRANSPORT_PDT      = 'pdt';
    public const WPS_TRANSPORT_IPN_PDT  = 'ipn_n_pdt';

    /**
     * Billing Agreement Signup
     *
     * @var string
     */
    public const EC_BA_SIGNUP_AUTO     = 'auto';
    public const EC_BA_SIGNUP_ASK      = 'ask';
    public const EC_BA_SIGNUP_NEVER    = 'never';

    /**
     * Config path for enabling/disabling order review step in express checkout
     */
    public const XML_PATH_PAYPAL_EXPRESS_SKIP_ORDER_REVIEW_STEP_FLAG = 'payment/paypal_express/skip_order_review_step';

    /**
     * Default URL for centinel API (PayPal Direct)
     *
     * @var string
     */
    public $centinelDefaultApiUrl = 'https://paypal.cardinalcommerce.com/maps/txns.asp';

    /**
     * Current payment method code
     * @var string
     */
    protected $_methodCode = null;

    /**
     * Current store id
     *
     * @var int
     */
    protected $_storeId = null;

    /**
     * Instructions for generating proper BN code
     *
     * @var array
     */
    protected $_buildNotationPPMap = [
        'paypal_standard'  => 'WPS',
        'paypal_express'   => 'EC',
        'paypal_direct'    => 'DP',
        'paypaluk_express' => 'EC',
        'paypaluk_direct'  => 'DP',
    ];

    /**
     * Style system config map (Express Checkout)
     *
     * @var array
     */
    protected $_ecStyleConfigMap = [
        'page_style'    => 'page_style',
        'paypal_hdrimg' => 'hdrimg',
        'paypal_hdrbordercolor' => 'hdrbordercolor',
        'paypal_hdrbackcolor'   => 'hdrbackcolor',
        'paypal_payflowcolor'   => 'payflowcolor',
    ];

    /**
     * Currency codes supported by PayPal methods
     *
     * @var array
     */
    protected $_supportedCurrencyCodes = [
        'AUD',
        'CAD',
        'CZK',
        'DKK',
        'EUR',
        'HKD',
        'HUF',
        'ILS',
        'JPY',
        'MXN',
        'NOK',
        'NZD',
        'PLN',
        'GBP',
        'RUB',
        'SGD',
        'SEK',
        'CHF',
        'TWD',
        'THB',
        'USD',
        'INR',
    ];

    /**
     * Merchant country supported by PayPal
     *
     * @var array
     */
    protected $_supportedCountryCodes = [
        'AE',
        'AR',
        'AT',
        'AU',
        'BE',
        'BG',
        'BR',
        'CA',
        'CN',
        'CH',
        'CL',
        'CR',
        'CY',
        'CZ',
        'DE',
        'DK',
        'DO',
        'EC',
        'EE',
        'ES',
        'FI',
        'FR',
        'GB',
        'GF',
        'GI',
        'GP',
        'GR',
        'HK',
        'HU',
        'ID',
        'IE',
        'IL',
        'IN',
        'IS',
        'IT',
        'JM',
        'JP',
        'KR',
        'LI',
        'LT',
        'LU',
        'LV',
        'MQ',
        'MT',
        'MX',
        'MY',
        'NL',
        'NO',
        'NZ',
        'PH',
        'PL',
        'PT',
        'RU',
        'RE',
        'RO',
        'SE',
        'SG',
        'SI',
        'SK',
        'SM',
        'TH',
        'TR',
        'TW',
        'US',
        'UY',
        'VE',
        'VN',
        'ZA',
    ];

    /**
     * Buyer country supported by PayPal
     *
     * @var array
     */
    protected $_supportedBuyerCountryCodes = [
        'AF ',
        'AX ',
        'AL ',
        'DZ ',
        'AS ',
        'AD ',
        'AO ',
        'AI ',
        'AQ ',
        'AG ',
        'AR ',
        'AM ',
        'AW ',
        'AU ',
        'AT ',
        'AZ ',
        'BS ',
        'BH ',
        'BD ',
        'BB ',
        'BY ',
        'BE ',
        'BZ ',
        'BJ ',
        'BM ',
        'BT ',
        'BO ',
        'BA ',
        'BW ',
        'BV ',
        'BR ',
        'IO ',
        'BN ',
        'BG ',
        'BF ',
        'BI ',
        'KH ',
        'CM ',
        'CA ',
        'CV ',
        'KY ',
        'CF ',
        'TD ',
        'CL ',
        'CN ',
        'CX ',
        'CC ',
        'CO ',
        'KM ',
        'CG ',
        'CD ',
        'CK ',
        'CR ',
        'CI ',
        'HR ',
        'CU ',
        'CY ',
        'CZ ',
        'DK ',
        'DJ ',
        'DM ',
        'DO ',
        'EC ',
        'EG ',
        'SV ',
        'GQ ',
        'ER ',
        'EE ',
        'ET ',
        'FK ',
        'FO ',
        'FJ ',
        'FI ',
        'FR ',
        'GF ',
        'PF ',
        'TF ',
        'GA ',
        'GM ',
        'GE ',
        'DE ',
        'GH ',
        'GI ',
        'GR ',
        'GL ',
        'GD ',
        'GP ',
        'GU ',
        'GT ',
        'GG ',
        'GN ',
        'GW ',
        'GY ',
        'HT ',
        'HM ',
        'VA ',
        'HN ',
        'HK ',
        'HU ',
        'IS ',
        'IN ',
        'ID ',
        'IR ',
        'IQ ',
        'IE ',
        'IM ',
        'IL ',
        'IT ',
        'JM ',
        'JP ',
        'JE ',
        'JO ',
        'KZ ',
        'KE ',
        'KI ',
        'KP ',
        'KR ',
        'KW ',
        'KG ',
        'LA ',
        'LV ',
        'LB ',
        'LS ',
        'LR ',
        'LY ',
        'LI ',
        'LT ',
        'LU ',
        'MO ',
        'MK ',
        'MG ',
        'MW ',
        'MY ',
        'MV ',
        'ML ',
        'MT ',
        'MH ',
        'MQ ',
        'MR ',
        'MU ',
        'YT ',
        'MX ',
        'FM ',
        'MD ',
        'MC ',
        'MN ',
        'MS ',
        'MA ',
        'MZ ',
        'MM ',
        'NA ',
        'NR ',
        'NP ',
        'NL ',
        'AN ',
        'NC ',
        'NZ ',
        'NI ',
        'NE ',
        'NG ',
        'NU ',
        'NF ',
        'MP ',
        'NO ',
        'OM ',
        'PK ',
        'PW ',
        'PS ',
        'PA ',
        'PG ',
        'PY ',
        'PE ',
        'PH ',
        'PN ',
        'PL ',
        'PT ',
        'PR ',
        'QA ',
        'RE ',
        'RO ',
        'RU ',
        'RW ',
        'SH ',
        'KN ',
        'LC ',
        'PM ',
        'VC ',
        'WS ',
        'SM ',
        'ST ',
        'SA ',
        'SN ',
        'CS ',
        'SC ',
        'SL ',
        'SG ',
        'SK ',
        'SI ',
        'SB ',
        'SO ',
        'ZA ',
        'GS ',
        'ES ',
        'LK ',
        'SD ',
        'SR ',
        'SJ ',
        'SZ ',
        'SE ',
        'CH ',
        'SY ',
        'TW ',
        'TJ ',
        'TZ ',
        'TH ',
        'TL ',
        'TG ',
        'TK ',
        'TO ',
        'TT ',
        'TN ',
        'TR ',
        'TM ',
        'TC ',
        'TV ',
        'UG ',
        'UA ',
        'AE ',
        'GB ',
        'US ',
        'UM ',
        'UY ',
        'UZ ',
        'VU ',
        'VE ',
        'VN ',
        'VG ',
        'VI ',
        'WF ',
        'EH ',
        'YE ',
        'ZM ',
        'ZW',
    ];

    /**
     * Locale codes supported by misc images (marks, shortcuts etc)
     *
     * @var array
     * @link https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_ECButtonIntegration#id089QD0O0TX4__id08AH904I0YK
     */
    protected $_supportedImageLocales = [
        'de_DE',
        'en_AU',
        'en_GB',
        'en_US',
        'es_ES',
        'es_XC',
        'fr_FR',
        'fr_XC',
        'it_IT',
        'ja_JP',
        'nl_NL',
        'pl_PL',
        'zh_CN',
        'zh_XC',
    ];

    /**
     * Set method and store id, if specified
     *
     * @param array $params
     */
    public function __construct($params = [])
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
     * @return $this
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
     *
     * @return string
     */
    public function getMethodCode()
    {
        return $this->_methodCode;
    }

    /**
     * Store ID setter
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = (int)$storeId;
        return $this;
    }

    /**
     * Check whether method active in configuration and supported for merchant country or not
     *
     * @param string $method Method code
     * @return bool
     */
    public function isMethodActive($method)
    {
        if ($this->isMethodSupportedForCountry($method)
            && Mage::getStoreConfigFlag("payment/{$method}/active", $this->_storeId)
        ) {
            return true;
        }
        return false;
    }

    /**
     * Check whether method available for checkout or not
     * Logic based on merchant country, methods dependence
     *
     * @param string|null $methodCode Method code
     * @return bool
     */
    public function isMethodAvailable($methodCode = null)
    {
        if ($methodCode === null) {
            $methodCode = $this->getMethodCode();
        }

        $result = true;

        if (!$this->isMethodActive($methodCode)) {
            $result = false;
        }

        switch ($methodCode) {
            case self::METHOD_WPS:
                if (!$this->businessAccount) {
                    $result = false;
                    break;
                }
                // check for direct payments dependence
                if ($this->isMethodActive(self::METHOD_WPP_DIRECT)
                    || $this->isMethodActive(self::METHOD_WPP_PE_DIRECT)) {
                    $result = false;
                }
                break;
            case self::METHOD_WPP_EXPRESS:
                // check for direct payments dependence
                if ($this->isMethodActive(self::METHOD_WPP_PE_DIRECT)) {
                    $result = false;
                } elseif ($this->isMethodActive(self::METHOD_WPP_DIRECT)) {
                    $result = true;
                }
                break;
            case self::METHOD_BML:
                // check for express payments dependence
                if (!$this->isMethodActive(self::METHOD_WPP_EXPRESS)) {
                    $result = false;
                }
                break;
            case self::METHOD_WPP_PE_EXPRESS:
                // check for direct payments dependence
                if ($this->isMethodActive(self::METHOD_WPP_PE_DIRECT)) {
                    $result = true;
                } elseif (!$this->isMethodActive(self::METHOD_WPP_PE_DIRECT)
                          && !$this->isMethodActive(self::METHOD_PAYFLOWPRO)) {
                    $result = false;
                }
                break;
            case self::METHOD_WPP_PE_BML:
                // check for express payments dependence
                if (!$this->isMethodActive(self::METHOD_WPP_PE_EXPRESS)) {
                    $result = false;
                }
                break;
            case self::METHOD_BILLING_AGREEMENT:
                $result = $this->isWppApiAvailabe();
                break;
            case self::METHOD_WPP_DIRECT:
            case self::METHOD_WPP_PE_DIRECT:
                break;
        }
        return $result;
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
        $value = $this->_prepareValue($underscored, $value);
        $this->$key = $value;
        $this->$underscored = $value;
        return $value;
    }

    /**
     * Perform additional config value preparation and return new value if needed
     *
     * @param string $key Underscored key
     * @param string $value Old value
     * @return string Modified value or old value
     */
    protected function _prepareValue($key, $value)
    {
        // Always set payment action as "Sale" for Unilateral payments in EC
        if ($key == 'payment_action'
            && $value != self::PAYMENT_ACTION_SALE
            && $this->_methodCode == self::METHOD_WPP_EXPRESS
            && $this->shouldUseUnilateralPayments()) {
            return self::PAYMENT_ACTION_SALE;
        }
        return $value;
    }

    /**
     * Return merchant country codes supported by PayPal
     *
     * @return array
     */
    public function getSupportedMerchantCountryCodes()
    {
        return $this->_supportedCountryCodes;
    }

    /**
     * Return buyer country codes supported by PayPal
     *
     * @return array
     */
    public function getSupportedBuyerCountryCodes()
    {
        return $this->_supportedBuyerCountryCodes;
    }

    /**
     * Return merchant country code, use default country if it not specified in General settings
     *
     * @return string
     */
    public function getMerchantCountry()
    {
        $countryCode = Mage::getStoreConfig($this->_mapGeneralFieldset('merchant_country'), $this->_storeId);
        if (!$countryCode) {
            $countryCode = Mage::helper('core')->getDefaultCountry($this->_storeId);
        }
        return $countryCode;
    }

    /**
     * Check whether method supported for specified country or not
     * Use $_methodCode and merchant country by default
     *
     * @return bool
     */
    public function isMethodSupportedForCountry($method = null, $countryCode = null)
    {
        if ($method === null) {
            $method = $this->getMethodCode();
        }
        if ($countryCode === null) {
            $countryCode = $this->getMerchantCountry();
        }
        $countryMethods = $this->getCountryMethods($countryCode);
        if (in_array($method, $countryMethods)) {
            return true;
        }
        return false;
    }

    /**
     * Return list of allowed methods for specified country iso code
     *
     * @param string $countryCode 2-letters iso code
     * @return array
     */
    public function getCountryMethods($countryCode = null)
    {
        $countryMethods = [
            'other' => [
                self::METHOD_WPS,
                self::METHOD_WPP_EXPRESS,
                self::METHOD_BILLING_AGREEMENT,
            ],
            'US' => [
                self::METHOD_PAYFLOWADVANCED,
                self::METHOD_WPP_DIRECT,
                self::METHOD_WPS,
                self::METHOD_PAYFLOWPRO,
                self::METHOD_PAYFLOWLINK,
                self::METHOD_WPP_EXPRESS,
                self::METHOD_BML,
                self::METHOD_BILLING_AGREEMENT,
                self::METHOD_WPP_PE_EXPRESS,
                self::METHOD_WPP_PE_BML,
            ],
            'CA' => [
                self::METHOD_WPP_DIRECT,
                self::METHOD_WPS,
                self::METHOD_PAYFLOWPRO,
                self::METHOD_PAYFLOWLINK,
                self::METHOD_WPP_EXPRESS,
                self::METHOD_BILLING_AGREEMENT,
            ],
            'GB' => [
                self::METHOD_WPP_DIRECT,
                self::METHOD_WPS,
                self::METHOD_WPP_PE_DIRECT,
                self::METHOD_HOSTEDPRO,
                self::METHOD_WPP_EXPRESS,
                self::METHOD_BILLING_AGREEMENT,
                self::METHOD_WPP_PE_EXPRESS,
            ],
            'AU' => [
                self::METHOD_WPS,
                self::METHOD_PAYFLOWPRO,
                self::METHOD_HOSTEDPRO,
                self::METHOD_WPP_EXPRESS,
                self::METHOD_BILLING_AGREEMENT,
            ],
            'NZ' => [
                self::METHOD_WPS,
                self::METHOD_PAYFLOWPRO,
                self::METHOD_WPP_EXPRESS,
                self::METHOD_BILLING_AGREEMENT,
            ],
            'JP' => [
                self::METHOD_WPS,
                self::METHOD_HOSTEDPRO,
                self::METHOD_WPP_EXPRESS,
                self::METHOD_BILLING_AGREEMENT,
            ],
            'FR' => [
                self::METHOD_WPS,
                self::METHOD_HOSTEDPRO,
                self::METHOD_WPP_EXPRESS,
                self::METHOD_BILLING_AGREEMENT,
            ],
            'IT' => [
                self::METHOD_WPS,
                self::METHOD_HOSTEDPRO,
                self::METHOD_WPP_EXPRESS,
                self::METHOD_BILLING_AGREEMENT,
            ],
            'ES' => [
                self::METHOD_WPS,
                self::METHOD_HOSTEDPRO,
                self::METHOD_WPP_EXPRESS,
                self::METHOD_BILLING_AGREEMENT,
            ],
            'HK' => [
                self::METHOD_WPS,
                self::METHOD_HOSTEDPRO,
                self::METHOD_WPP_EXPRESS,
                self::METHOD_BILLING_AGREEMENT,
            ],
        ];
        if ($countryCode === null) {
            return $countryMethods;
        }
        return $countryMethods[$countryCode] ?? $countryMethods['other'];
    }

    /**
     * Return start url for PayPal Basic
     *
     * @param string $token
     * @return string
     */
    public function getPayPalBasicStartUrl($token)
    {
        $params = [
            'cmd'   => '_express-checkout',
            'token' => $token,
        ];

        if ($this->isOrderReviewStepDisabled()) {
            $params['useraction'] = 'commit';
        }

        return $this->getPaypalUrl($params);
    }

    /**
     * Check whether order review step enabled in configuration
     *
     * @return bool
     */
    public function isOrderReviewStepDisabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_PAYPAL_EXPRESS_SKIP_ORDER_REVIEW_STEP_FLAG);
    }

    /**
     * Get url for dispatching customer to express checkout start
     *
     * @param string $token
     * @return string
     */
    public function getExpressCheckoutStartUrl($token)
    {
        return $this->getPaypalUrl([
            'cmd'   => '_express-checkout',
            'token' => $token,
        ]);
    }

    /**
     * Get url for dispatching customer to checkout retrial
     *
     * @param string $orderId
     * @return string
     */
    public function getExpressCheckoutOrderUrl($orderId)
    {
        return $this->getPaypalUrl([
            'cmd'   => '_express-checkout',
            'order_id' => $orderId,
        ]);
    }

    /**
     * Get url that allows to edit checkout details on paypal side
     *
     * @param string $token
     * @return string
     */
    public function getExpressCheckoutEditUrl($token)
    {
        return $this->getPaypalUrl([
            'cmd'        => '_express-checkout',
            'useraction' => 'continue',
            'token'      => $token,
        ]);
    }

    /**
     * Get url for additional actions that PayPal may require customer to do after placing the order.
     * For instance, redirecting customer to bank for payment confirmation.
     *
     * @param string $token
     * @return string
     */
    public function getExpressCheckoutCompleteUrl($token)
    {
        return $this->getPaypalUrl([
            'cmd'   => '_complete-express-checkout',
            'token' => $token,
        ]);
    }

    /**
     * Retrieve url for initialization of billing agreement
     *
     * @param string $token
     * @return string
     */
    public function getStartBillingAgreementUrl($token)
    {
        return $this->getPaypalUrl([
            'cmd'   => '_customer-billing-agreement',
            'token' => $token,
        ]);
    }

    /**
    * PayPal web URL generic getter
    *
    * @param array $params
    * @return string
    */
    public function getPaypalUrl(array $params = [])
    {
        return sprintf(
            'https://www.%spaypal.com/cgi-bin/webscr%s',
            $this->sandboxFlag ? 'sandbox.' : '',
            $params ? '?' . http_build_query($params) : ''
        );
    }

    /**
     * Get postback endpoint URL.
     *
     * @return string
     */
    public function getPostbackUrl()
    {
        return sprintf('https://ipnpb.%spaypal.com/cgi-bin/webscr', $this->sandboxFlag ? 'sandbox.' : '');
    }

    /**
     * Whether Express Checkout button should be rendered dynamically
     *
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
     * @return string
     * @see Paypal_Model_Api_Nvp::callGetPalDetails()
     */
    public function getExpressCheckoutShortcutImageUrl($localeCode, $orderTotal = null, $pal = null)
    {
        $country = Mage::getStoreConfig(Mage_Paypal_Helper_Data::MERCHANT_COUNTRY_CONFIG_PATH);
        if ($country == Mage_Paypal_Helper_Data::US_COUNTRY
            && ($this->areButtonsDynamic() || $this->buttonType != self::EC_BUTTON_TYPE_MARK)
        ) {
            return 'https://www.paypalobjects.com/webstatic/en_US/i/buttons/checkout-logo-medium.png';
        }
        if ($this->areButtonsDynamic()) {
            return $this->_getDynamicImageUrl(self::EC_BUTTON_TYPE_SHORTCUT, $localeCode, $orderTotal, $pal);
        }
        if ($this->buttonType === self::EC_BUTTON_TYPE_MARK) {
            return $this->getPaymentMarkImageUrl($localeCode);
        }
        return sprintf(
            'https://www.paypal.com/%s/i/btn/btn_xpressCheckout.gif',
            $this->_getSupportedLocaleCode($localeCode)
        );
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
     * @return string
     */
    public function getPaymentMarkImageUrl($localeCode, $orderTotal = null, $pal = null, $staticSize = null)
    {
        $country = Mage::getStoreConfig(Mage_Paypal_Helper_Data::MERCHANT_COUNTRY_CONFIG_PATH);
        if ($country == Mage_Paypal_Helper_Data::US_COUNTRY) {
            return 'https://www.paypalobjects.com/webstatic/en_US/i/buttons/pp-acceptance-medium.png';
        }
        if ($this->areButtonsDynamic()) {
            return $this->_getDynamicImageUrl(self::EC_BUTTON_TYPE_MARK, $localeCode, $orderTotal, $pal);
        }

        if ($staticSize === null) {
            $staticSize = $this->paymentMarkSize;
        }
        switch ($staticSize) {
            case self::PAYMENT_MARK_37X23:
            case self::PAYMENT_MARK_50X34:
            case self::PAYMENT_MARK_60X38:
            case self::PAYMENT_MARK_180X113:
                break;
            default:
                $staticSize = self::PAYMENT_MARK_37X23;
        }
        return sprintf(
            'https://www.paypal.com/%s/i/logo/PayPal_mark_%s.gif',
            $this->_getSupportedLocaleCode($localeCode),
            $staticSize
        );
    }

    /**
     * Get "What Is PayPal" localized URL
     * Supposed to be used with "mark" as popup window
     *
     * @param Mage_Core_Model_Locale|null $locale
     * @return string
     */
    public function getPaymentMarkWhatIsPaypalUrl(Mage_Core_Model_Locale $locale = null)
    {
        $countryCode = 'US';
        if ($locale !== null) {
            $shouldEmulate = ($this->_storeId !== null) && (Mage::app()->getStore()->getId() != $this->_storeId);
            if ($shouldEmulate) {
                $locale->emulate($this->_storeId);
            }
            $countryCode = $locale->getLocale()->getRegion();
            if ($shouldEmulate) {
                $locale->revert();
            }
        }
        return sprintf(
            'https://www.paypal.com/%s/cgi-bin/webscr?cmd=xpt/Marketing/popup/OLCWhatIsPayPal-outside',
            strtolower($countryCode)
        );
    }

    /**
     * Getter for Solution banner images
     *
     * @param string $localeCode
     * @param bool $isVertical
     * @param bool $isEcheck
     * @return string
     */
    public function getSolutionImageUrl($localeCode, $isVertical = false, $isEcheck = false)
    {
        return sprintf(
            'https://www.paypal.com/%s/i/bnr/%s_solution_PP%s.gif',
            $this->_getSupportedLocaleCode($localeCode),
            $isVertical ? 'vertical' : 'horizontal',
            $isEcheck ? 'eCheck' : ''
        );
    }

    /**
     * Getter for Payment form logo images
     *
     * @param string $localeCode
     * @return string
     */
    public function getPaymentFormLogoUrl($localeCode)
    {
        $locale = $this->_getSupportedLocaleCode($localeCode);

        $imageType = 'logo';
        $domain = 'paypal.com';
        list(, $country) = explode('_', $locale);
        $countryPrefix = $country . '/';

        switch ($locale) {
            case 'en_GB':
                $imageName = 'horizontal_solution_PP';
                $imageType = 'bnr';
                $countryPrefix = '';
                break;
            case 'de_DE':
                $imageName = 'lockbox_150x47';
                break;
            case 'fr_FR':
                $imageName = 'bnr_horizontal_solution_PP_327wx80h';
                $imageType = 'bnr';
                $locale = self::LOCALE_US;
                $domain = 'paypalobjects.com';
                break;
            case 'it_IT':
                $imageName = 'bnr_horizontal_solution_PP_178wx80h';
                $imageType = 'bnr';
                $domain = 'paypalobjects.com';
                break;
            default:
                $imageName = 'PayPal_mark_60x38';
                $countryPrefix = '';
                break;
        }
        return sprintf('https://www.%s/%s/%si/%s/%s.gif', $domain, $locale, $countryPrefix, $imageType, $imageName);
    }

    /**
     * Return supported types for PayPal logo
     *
     * @return array
     */
    public function getAdditionalOptionsLogoTypes()
    {
        return [
            'wePrefer_150x60'       => Mage::helper('paypal')->__('We prefer PayPal (150 X 60)'),
            'wePrefer_150x40'       => Mage::helper('paypal')->__('We prefer PayPal (150 X 40)'),
            'nowAccepting_150x60'   => Mage::helper('paypal')->__('Now accepting PayPal (150 X 60)'),
            'nowAccepting_150x40'   => Mage::helper('paypal')->__('Now accepting PayPal (150 X 40)'),
            'paymentsBy_150x60'     => Mage::helper('paypal')->__('Payments by PayPal (150 X 60)'),
            'paymentsBy_150x40'     => Mage::helper('paypal')->__('Payments by PayPal (150 X 40)'),
            'shopNowUsing_150x60'   => Mage::helper('paypal')->__('Shop now using (150 X 60)'),
            'shopNowUsing_150x40'   => Mage::helper('paypal')->__('Shop now using (150 X 40)'),
        ];
    }

    /**
     * Return PayPal logo URL with additional options
     *
     * @param string $localeCode Supported locale code
     * @param string|false $type One of supported logo types
     * @return string|bool Logo Image URL or false if logo disabled in configuration
     */
    public function getAdditionalOptionsLogoUrl($localeCode, $type = false)
    {
        $configType = Mage::getStoreConfig($this->_mapGenericStyleFieldset('logo'), $this->_storeId);
        if (!$configType) {
            return false;
        }
        $type = $type ? $type : $configType;
        $locale = $this->_getSupportedLocaleCode($localeCode);
        $supportedTypes = array_keys($this->getAdditionalOptionsLogoTypes());
        if (!in_array($type, $supportedTypes)) {
            $type = self::DEFAULT_LOGO_TYPE;
        }
        return sprintf('https://www.paypalobjects.com/%s/i/bnr/bnr_%s.gif', $locale, $type);
    }

    /**
     * BN code getter
     *
     * @return mixed
     */
    public function getBuildNotationCode()
    {
        return Mage::getStoreConfig("paypal/bncode", $this->_storeId);
    }

    /**
     * Express Checkout button "flavors" source getter
     *
     * @return array
     */
    public function getExpressCheckoutButtonFlavors()
    {
        return [
            self::EC_FLAVOR_DYNAMIC => Mage::helper('paypal')->__('Dynamic'),
            self::EC_FLAVOR_STATIC  => Mage::helper('paypal')->__('Static'),
        ];
    }

    /**
     * Express Checkout button types source getter
     *
     * @return array
     */
    public function getExpressCheckoutButtonTypes()
    {
        return [
            self::EC_BUTTON_TYPE_SHORTCUT => Mage::helper('paypal')->__('Shortcut'),
            self::EC_BUTTON_TYPE_MARK     => Mage::helper('paypal')->__('Acceptance Mark Image'),
        ];
    }

    /**
     * Payment actions source getter
     *
     * @return array
     */
    public function getPaymentActions()
    {
        $paymentActions = [
            self::PAYMENT_ACTION_AUTH => Mage::helper('paypal')->__('Authorization'),
            self::PAYMENT_ACTION_SALE => Mage::helper('paypal')->__('Sale')
        ];
        if (!is_null($this->_methodCode) && $this->_methodCode == self::METHOD_WPP_EXPRESS) {
            $paymentActions[self::PAYMENT_ACTION_ORDER] = Mage::helper('paypal')->__('Order');
        }
        return $paymentActions;
    }

    /**
     * Require Billing Address source getter
     *
     * @return array
     */
    public function getRequireBillingAddressOptions()
    {
        return [
            self::REQUIRE_BILLING_ADDRESS_ALL       => Mage::helper('paypal')->__('Yes'),
            self::REQUIRE_BILLING_ADDRESS_NO        => Mage::helper('paypal')->__('No'),
            self::REQUIRE_BILLING_ADDRESS_VIRTUAL   => Mage::helper('paypal')->__('For Virtual Quotes Only'),
        ];
    }

    /**
     * Mapper from PayPal-specific payment actions to Magento payment actions
     *
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
                return Mage_Payment_Model_Method_Abstract::ACTION_ORDER;
        }
        return null;
    }

    /**
     * Returns array of possible Authorization Amounts for Account Verification
     *
     * @deprecated since 1.6.2.0
     * @return array
     */
    public function getAuthorizationAmounts()
    {
        return [];
    }

    /**
     * Express Checkout "solution types" source getter
     * "sole" = "Express Checkout for Auctions" - PayPal allows guest checkout
     * "mark" = "Normal Express Checkout" - PayPal requires to checkout with PayPal buyer account only
     *
     * @return array
     */
    public function getExpressCheckoutSolutionTypes()
    {
        return [
            self::EC_SOLUTION_TYPE_SOLE => Mage::helper('paypal')->__('Yes'),
            self::EC_SOLUTION_TYPE_MARK => Mage::helper('paypal')->__('No'),
        ];
    }

    /**
     * Retrieve express checkout billing agreement signup options
     *
     * @return array
     */
    public function getExpressCheckoutBASignupOptions()
    {
        return [
            self::EC_BA_SIGNUP_AUTO  => Mage::helper('paypal')->__('Auto'),
            self::EC_BA_SIGNUP_ASK   => Mage::helper('paypal')->__('Ask Customer'),
            self::EC_BA_SIGNUP_NEVER => Mage::helper('paypal')->__('Never')
        ];
    }

    /**
     * Whether to ask customer to create billing agreements
     * Unilateral payments are incompatible with the billing agreements
     *
     * @return bool
     */
    public function shouldAskToCreateBillingAgreement()
    {
        return ($this->allow_ba_signup === self::EC_BA_SIGNUP_ASK) && !$this->shouldUseUnilateralPayments();
    }

    /**
     * Check whether only Unilateral payments (Accelerated Boarding) possible for Express method or not
     *
     * @return bool
     */
    public function shouldUseUnilateralPayments()
    {
        return $this->business_account && !$this->isWppApiAvailabe();
    }

    /**
     * Check whether WPP API credentials are available for this method
     *
     * @return bool
     */
    public function isWppApiAvailabe()
    {
        return $this->api_username && $this->api_password && ($this->api_signature || $this->api_cert);
    }

    /**
     * Payment data delivery methods getter for PayPal Standard
     *
     * @return array
     */
    public function getWpsPaymentDeliveryMethods()
    {
        return [
            self::WPS_TRANSPORT_IPN => Mage::helper('adminhtml')->__('IPN (Instant Payment Notification) Only'),
        ];
    }

    /**
     * Return list of supported credit card types by Paypal Direct gateway
     *
     * @return array
     */
    public function getWppCcTypesAsOptionArray()
    {
        $model = Mage::getModel('payment/source_cctype')->setAllowedTypes(['AE', 'VI', 'MC', 'SM', 'SO', 'DI']);
        return $model->toOptionArray();
    }

    /**
     * Return list of supported credit card types by Paypal Direct (Payflow Edition) gateway
     *
     * @return array
     */
    public function getWppPeCcTypesAsOptionArray()
    {
        $model = Mage::getModel('payment/source_cctype')->setAllowedTypes(['VI', 'MC', 'SM', 'SO', 'OT', 'AE']);
        return $model->toOptionArray();
    }

    /**
     * Return list of supported credit card types by Payflow Pro gateway
     *
     * @return array
     */
    public function getPayflowproCcTypesAsOptionArray()
    {
        $model = Mage::getModel('payment/source_cctype')->setAllowedTypes(['AE', 'VI', 'MC', 'JCB', 'DI']);
        return $model->toOptionArray();
    }

    /**
     * Check whether the specified payment method is a CC-based one
     *
     * @param string $code
     * @return bool
     */
    public static function getIsCreditCardMethod($code)
    {
        switch ($code) {
            case self::METHOD_WPP_DIRECT:
            case self::METHOD_WPP_PE_DIRECT:
            case self::METHOD_PAYFLOWPRO:
            case self::METHOD_PAYFLOWLINK:
            case self::METHOD_PAYFLOWADVANCED:
            case self::METHOD_HOSTEDPRO:
                return true;
        }
        return false;
    }

    /**
     * Check whether specified currency code is supported
     *
     * @param string $code
     * @return bool
     */
    public function isCurrencyCodeSupported($code)
    {
        if (in_array($code, $this->_supportedCurrencyCodes)) {
            return true;
        }
        if ($this->getMerchantCountry() == 'BR' && $code == 'BRL') {
            return true;
        }
        if ($this->getMerchantCountry() == 'MY' && $code == 'MYR') {
            return true;
        }
        return false;
    }

    /**
     * Export page style current settings to specified object
     *
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
     * Dynamic PayPal image URL getter
     * Also can render dynamic Acceptance Mark
     *
     * @param string $type
     * @param string $localeCode
     * @param float $orderTotal
     * @param string $pal
     * @return string
     */
    protected function _getDynamicImageUrl($type, $localeCode, $orderTotal, $pal)
    {
        $params = [
            'cmd'        => '_dynamic-image',
            'buttontype' => $type,
            'locale'     => $this->_getSupportedLocaleCode($localeCode),
        ];
        if ($orderTotal) {
            $params['ordertotal'] = sprintf('%.2F', $orderTotal);
            if ($pal) {
                $params['pal'] = $pal;
            }
        }
        return sprintf(
            'https://fpdbs%s.paypal.com/dynamicimageweb?%s',
            $this->sandboxFlag ? '.sandbox' : '',
            http_build_query($params)
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
            return self::LOCALE_US;
        }
        return $localeCode;
    }

    /**
     * Map any supported payment method into a config path by specified field name
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _getSpecificConfigPath($fieldName)
    {
        $path = null;
        switch ($this->_methodCode) {
            case self::METHOD_WPS:
                $path = $this->_mapStandardFieldset($fieldName);
                break;
            case self::METHOD_BML:
                $path = $this->_mapBmlFieldset($fieldName);
                break;
            case self::METHOD_WPP_PE_BML:
                $path = $this->_mapBmlUkFieldset($fieldName);
                break;
            case self::METHOD_WPP_EXPRESS:
            case self::METHOD_WPP_PE_EXPRESS:
                $path = $this->_mapExpressFieldset($fieldName);
                break;
            case self::METHOD_WPP_DIRECT:
            case self::METHOD_WPP_PE_DIRECT:
                $path = $this->_mapDirectFieldset($fieldName);
                break;
            case self::METHOD_BILLING_AGREEMENT:
            case self::METHOD_HOSTEDPRO:
                $path = $this->_mapMethodFieldset($fieldName);
                break;
        }

        if ($path === null) {
            switch ($this->_methodCode) {
                case self::METHOD_WPP_EXPRESS:
                case self::METHOD_BML:
                case self::METHOD_WPP_DIRECT:
                case self::METHOD_BILLING_AGREEMENT:
                case self::METHOD_HOSTEDPRO:
                    $path = $this->_mapWppFieldset($fieldName);
                    break;
                case self::METHOD_WPP_PE_EXPRESS:
                case self::METHOD_WPP_PE_DIRECT:
                case self::METHOD_PAYFLOWADVANCED:
                case self::METHOD_PAYFLOWLINK:
                    $path = $this->_mapWpukFieldset($fieldName);
                    break;
            }
        }

        if ($path === null) {
            $path = $this->_mapGeneralFieldset($fieldName);
        }
        if ($path === null) {
            $path = $this->_mapGenericStyleFieldset($fieldName);
        }
        return $path;
    }

    /**
     * Check wheter specified country code is supported by build notation codes for specific countries
     *
     * @param string $code
     * @return string|null
     */
    private function _matchBnCountryCode($code)
    {
        switch ($code) {
            // GB == UK
            case 'GB':
                return 'UK';
            // Australia, Austria, Belgium, Canada, China, France, Germany, Hong Kong, Italy
            case 'AU':
            case 'AT':
            case 'BE':
            case 'CA':
            case 'CN':
            case 'FR':
            case 'DE':
            case 'HK':
            case 'IT':
            // Japan, Mexico, Netherlands, Poland, Singapore, Spain, Switzerland, United Kingdom, United States
            case 'JP':
            case 'MX':
            case 'NL':
            case 'PL':
            case 'SG':
            case 'ES':
            case 'CH':
            case 'UK':
            case 'US':
                return $code;
        }
        return null;
    }

    /**
     * Map PayPal Standard config fields
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapStandardFieldset($fieldName)
    {
        switch ($fieldName) {
            case 'line_items_summary':
            case 'sandbox_flag':
                return 'payment/' . self::METHOD_WPS . "/{$fieldName}";
            default:
                return $this->_mapMethodFieldset($fieldName);
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
        switch ($fieldName) {
            case 'transfer_shipping_options':
            case 'solution_type':
            case 'visible_on_cart':
            case 'visible_on_product':
            case 'require_billing_address':
            case 'authorization_honor_period':
            case 'order_valid_period':
            case 'child_authorization_number':
            case 'allow_ba_signup':
                return "payment/{$this->_methodCode}/{$fieldName}";
            default:
                return $this->_mapMethodFieldset($fieldName);
        }
    }

    /**
     * Map PayPal Express Bill Me Later config fields
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapBmlFieldset($fieldName)
    {
        switch ($fieldName) {
            case 'allow_ba_signup':
                return "payment/" . self::METHOD_WPP_EXPRESS . "/{$fieldName}";
            default:
                return $this->_mapExpressFieldset($fieldName);
        }
    }

    /**
     * Map PayPal Express Bill Me Later config fields (Payflow Edition)
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapBmlUkFieldset($fieldName)
    {
        switch ($fieldName) {
            case 'allow_ba_signup':
                return "payment/" . self::METHOD_WPP_PE_EXPRESS . "/{$fieldName}";
            default:
                return $this->_mapExpressFieldset($fieldName);
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
        switch ($fieldName) {
            case 'useccv':
            case 'centinel':
            case 'centinel_is_mode_strict':
            case 'centinel_api_url':
                return "payment/{$this->_methodCode}/{$fieldName}";
            default:
                return $this->_mapMethodFieldset($fieldName);
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
        switch ($fieldName) {
            case 'api_authentication':
            case 'api_username':
            case 'api_password':
            case 'api_signature':
            case 'api_cert':
            case 'sandbox_flag':
            case 'use_proxy':
            case 'proxy_host':
            case 'proxy_port':
            case 'button_flavor':
                return "paypal/wpp/{$fieldName}";
            default:
                return null;
        }
    }

    /**
     * Map PayPal Website Payments Pro common config fields
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapWpukFieldset($fieldName)
    {
        $pathPrefix = 'paypal/wpuk';
        // Use PUMP credentials from Verisign for EC when Direct Payments are unavailable
        if ($this->_methodCode == self::METHOD_WPP_PE_EXPRESS
            && !$this->isMethodAvailable(self::METHOD_WPP_PE_DIRECT)) {
            $pathPrefix = 'payment/verisign';
        } elseif ($this->_methodCode == self::METHOD_PAYFLOWADVANCED
            || $this->_methodCode == self::METHOD_PAYFLOWLINK
        ) {
            $pathPrefix = 'payment/' . $this->_methodCode;
        }
        switch ($fieldName) {
            case 'partner':
            case 'user':
            case 'vendor':
            case 'pwd':
            case 'sandbox_flag':
            case 'use_proxy':
            case 'proxy_host':
            case 'proxy_port':
                return $pathPrefix . '/' . $fieldName;
            default:
                return null;
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
            case 'logo':
            case 'page_style':
            case 'paypal_hdrimg':
            case 'paypal_hdrbackcolor':
            case 'paypal_hdrbordercolor':
            case 'paypal_payflowcolor':
                return "paypal/style/{$fieldName}";
            default:
                return null;
        }
    }

    /**
     * Map PayPal General Settings
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapGeneralFieldset($fieldName)
    {
        switch ($fieldName) {
            case 'business_account':
            case 'merchant_country':
                return "paypal/general/{$fieldName}";
            default:
                return null;
        }
    }

    /**
     * Map PayPal General Settings
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapMethodFieldset($fieldName)
    {
        if (!$this->_methodCode) {
            return null;
        }
        switch ($fieldName) {
            case 'active':
            case 'title':
            case 'payment_action':
            case 'allowspecific':
            case 'specificcountry':
            case 'line_items_enabled':
            case 'cctypes':
            case 'sort_order':
            case 'debug':
            case 'verify_peer':
            case 'mobile_optimized':
                return "payment/{$this->_methodCode}/{$fieldName}";
            default:
                return null;
        }
    }

    /**
     * Payment API authentication methods source getter
     *
     * @return array
     */
    public function getApiAuthenticationMethods()
    {
        return [
            '0' => Mage::helper('paypal')->__('API Signature'),
            '1' => Mage::helper('paypal')->__('API Certificate')
        ];
    }

    /**
     * Api certificate getter
     *
     * @return string
     */
    public function getApiCertificate()
    {
        $websiteId = Mage::app()->getStore($this->_storeId)->getWebsiteId();
        return Mage::getModel('paypal/cert')->loadByWebsite($websiteId, false)->getCertPath();
    }

    /**
     * Get PublisherId from stored config
     *
     * @return mixed
     */
    public function getBmlPublisherId()
    {
        return Mage::getStoreConfig('payment/paypal_express_bml/publisher_id', $this->_storeId);
    }

    /**
     * Get Display option from stored config
     * @param string $section
     *
     * @return mixed
     */
    public function getBmlDisplay($section)
    {
        $display = Mage::getStoreConfig('payment/paypal_express_bml/' . $section . '_display', $this->_storeId);
        $ecActive = Mage::getStoreConfig('payment/paypal_express/active', $this->_storeId);
        $ecUkActive = Mage::getStoreConfig('payment/paypaluk_express/active', $this->_storeId);
        $bmlActive = Mage::getStoreConfig('payment/paypal_express_bml/active', $this->_storeId);
        $bmlUkActive = Mage::getStoreConfig('payment/paypaluk_express_bml/active', $this->_storeId);
        return (($bmlActive && $ecActive) || ($bmlUkActive && $ecUkActive)) ? $display : 0;
    }

    /**
     * Get Position option from stored config
     * @param string $section
     *
     * @return mixed
     */
    public function getBmlPosition($section)
    {
        return Mage::getStoreConfig('payment/paypal_express_bml/' . $section . '_position', $this->_storeId);
    }

    /**
     * Get Size option from stored config
     * @param string $section
     *
     * @return mixed
     */
    public function getBmlSize($section)
    {
        return Mage::getStoreConfig('payment/paypal_express_bml/' . $section . '_size', $this->_storeId);
    }
}
