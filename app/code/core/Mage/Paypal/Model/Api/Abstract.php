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
 * Abstract class for Paypal API wrappers
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Paypal_Model_Api_Abstract extends Varien_Object
{
    const PAYMENT_TYPE_SALE = 'Sale';
    const PAYMENT_TYPE_ORDER = 'Order';
    const PAYMENT_TYPE_AUTH = 'Authorization';

    const REFUND_TYPE_FULL = 'Full';
    const REFUND_TYPE_PARTIAL = 'Partial';

    const COMPLETE = 'Complete';
    const NOTCOMPLETE = 'NotComplete';

    const USER_ACTION_COMMIT = 'commit';
    const USER_ACTION_CONTINUE = 'continue';
    const USER_GIROPAY_REDIRECT = 'giropay_redirect';

    const SOLUTION_TYPE_SOLE = 'Sole';
    const SOLUTION_TYPE_MARK = 'Mark';

    const ACTION_ACCEPT = 'Acept';
    const ACTION_DENY   = 'Deny';

    const BUTTON_TYPE_DEFAULT       = 'ec-shortcut';
    const BUTTON_TYPE_ACCEPTANCE    = 'ec-mark';
    const BUTTON_FLAVOR_STATIC      = 'static';
    const BUTTON_FLAVOR_DYNAMIC     = 'dynamic';

    const FRAUD_ERROR_CODE = 11610;

    const AVS_RESPONSE_MATCH          = 'Y';
    const AVS_RESPONSE_NO_MATCH       = 'N';
    const AVS_RESPONSE_NO_CARDHOLDER  = 'X';
    const AVS_RESPONSE_ALL            = 0;
    const AVS_RESPONSE_NONE           = 1;
    const AVS_RESPONSE_PARTIAL        = 2;
    const AVS_RESPONSE_NOT_PROCESSED  = 3;
    const AVS_RESPONSE_NOT_AVAILIABLE = 4;

    const CVV_RESPONSE_MATCH_CC                 = 'M';
    const CVV_RESPONSE_MATCH_SOLO               = 0;
    const CVV_RESPONSE_NOT_MATCH_CC             = 'N';
    const CVV_RESPONSE_NOT_MATCH_SOLO           = 1;
    const CVV_RESPONSE_NOT_PROCESSED_CC         = 'P';
    const CVV_RESPONSE_NOT_IMPLEMENTED_SOLO     = 2;
    const CVV_RESPONSE_NOT_SUPPORTED_CC         = 'S';
    const CVV_RESPONSE_NOT_PRESENT_SOLO         = 3;
    const CVV_RESPONSE_NOT_AVAILIBLE_CC         = 'U';
    const CVV_RESPONSE_NOT_AVAILIBLE_SOLO       = 4;
    const CVV_RESPONSE_NOT_RESPONSE_CC          = 'X';


    /**
     * return server name from as server variable
     *
     * @return string
     */
    public function getServerName()
    {
        if (!$this->hasServerName()) {
            $this->setServerName($_SERVER['SERVER_NAME']);
        }
        return $this->getData('server_name');
    }

    /**
     * Return config data based on paymethod, store id
     *
     * @return string
     */
    public function getConfigData($key, $default=false, $storeId = null)
    {
        return $this->getGeneralConfigData($key, $default, $storeId, 'paypal/wpp/');
    }

    /**
     * Get PayPal Account Style Configuration
     *
     */
    public function getStyleConfigData($key, $default=false, $storeId = null)
    {
        return $this->getGeneralConfigData($key, $default, $storeId, 'paypal/style/');
    }

    /**
     * Return config data by give path, key, default and store Id
     *
     */
    public function getGeneralConfigData($key, $default=false, $storeId = null, $path = 'paypal/wpp/')
    {
        if (!$this->hasData($key)) {
            if ($storeId === null && $this->getPayment() instanceof Varien_Object) {
                $storeId = $this->getPayment()->getOrder()->getStoreId();
            }
            $value = Mage::getStoreConfig($path . $key, $storeId);
            if (empty($value)) {
                $value = $default;
            }
            $this->setData($key, $value);
        }
        return $this->getData($key);
    }

    /**
     * Return paypal session model
     *
     * @return Mage_Paypal_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('paypal/session');
    }

    /**
     * Flag which check if we are use session or not.
     *
     * @return bool
     */
    public function getUseSession()
    {
        if (!$this->hasData('use_session')) {
            $this->setUseSession(true);
        }
        return $this->getData('use_session');
    }

    /**
     * Return data from session based on key and default value
     *
     * @param $key string
     * @param $default string
     *
     * @return string
     */
    public function getSessionData($key, $default=false)
    {
        if (!$this->hasData($key)) {
            $value = $this->getSession()->getData($key);
            if ($this->getSession()->hasData($key)) {
                $value = $this->getSession()->getData($key);
            } else {
                $value = $default;
            }
            $this->setData($key, $value);
        }
        return $this->getData($key);
    }

    /**
     * Set data in session scope
     *
     * @param $key string
     * @param $value string
     *
     * @return Mage_Paypal_Model_Api_Abstract
     */
    public function setSessionData($key, $value)
    {
        if ($this->getUseSession()) {
            $this->getSession()->setData($key, $value);
        }
        $this->setData($key, $value);
        return $this;
    }

    /**
     * Return sandbox flag state, by config
     *
     * @return bool
     */
    public function getSandboxFlag()
    {
        return $this->getConfigData('sandbox_flag', true);
    }

    /**
     * Return Paypal Api user name based on config data
     *
     * @return string
     */
    public function getApiUsername()
    {
        return $this->getConfigData('api_username');
    }

    /**
     * Return Paypal Api password based on config data
     *
     * @return string
     */
    public function getApiPassword()
    {
        return $this->getConfigData('api_password');
    }

    /**
     * Return Paypal Api signature based on config data
     *
     * @return string
     */
    public function getApiSignature()
    {
        return $this->getConfigData('api_signature');
    }

    /**
     * Return Paypal Express check out button source
     *
     * @return string
     */
    public function getButtonSourceEc()
    {
        return $this->getConfigData('button_source_ec', 'Varien_Cart_EC_US');
    }

    /**
     * Return Paypal direct payment button source
     *
     * @return string
     */
    public function getButtonSourceDp()
    {
        return $this->getConfigData('button_source_dp', 'Varien_Cart_DP_US');
    }

    /**
     * Return Paypal Api proxy status based on config data
     *
     * @return bool
     */
    public function getUseProxy()
    {
        return $this->getConfigData('use_proxy', false);
    }

    /**
     * Return Paypal Api proxy host based on config data
     *
     * @return string
     */
    public function getProxyHost()
    {
        return $this->getConfigData('proxy_host', '127.0.0.1');
    }

    /**
     * Return Paypal Api proxy port based on config data
     *
     * @return string
     */
    public function getProxyPort()
    {
        return $this->getConfigData('proxy_port', '808');
    }

    /**
     * Return Paypal Api debug flag based on config data
     *
     * @return bool
     */
    public function getDebug()
    {
        return $this->getConfigData('debug_flag', true);
    }

    /**
     * the page where buyers will go if there are API error
     *
     * @return string
     */
    public function getApiErrorUrl()
    {
        return Mage::getUrl($this->getConfigData('api_error_url', 'paypal/express/error'));
    }

    /**
     * the page where buyers return to after they are done with the payment review on PayPal
     *
     * @return string
     */
    public function getReturnUrl()
    {
        return Mage::getUrl($this->getConfigData('api_return_url', 'paypal/express/return'));
    }

    /**
     * Your URL for receiving Instant Payment Notification
     *
     * @return string
     */
    public function getNotifyUrl($orderId, $method='express')
    {
        return Mage::getUrl($this->getConfigData('api_notify_url', 'paypal/' . $method . '/notify'), array('invoice' => $orderId));
    }

    /**
     * The page where buyers return to when they cancel the payment review on PayPal
     *
     * @return string
     */
    public function getCancelUrl()
    {
        return Mage::getUrl($this->getConfigData('api_cancel_url', 'paypal/express/cancel'));
    }

    /**
     * The page where buyer return to continue giropay transaction
     *
     * @return string
     */
    public function getGiropayRedirectUrl()
    {
        if ($this->getSandboxFlag()) {
            $redirect = 'https://www.sandbox.paypal.com/';
        } else {
            $redirect = 'https://www.paypal.com/';
        }
        $redirect .= 'webscr?cmd=_complete-express-checkout&token=%s';
        return $redirect;
    }

    /**
     * The URL on the merchant site to redirect to after a giropay or bank transfer payment is cancelled or fails.
     * Use this field only if you are using giropay or bank transfer payment methods in Germany
     *
     */
    public function getGiropayCancelUrl()
    {
        return Mage::getUrl($this->getConfigData('giropay_cancel_url', 'paypal/express/cancel'));
    }

    /**
     * The URL on the merchant site to redirect to after a successful giropay payment.
     * Use this field only if you are using giropay or bank transfer payment methods in Germany.
     */
    public function getGiropaySuccessUrl()
    {
        return Mage::getUrl($this->getConfigData('giropay_success_url', 'checkout/onepage/success'));
    }

    /**
     * The URL on the merchant site to transfer to after a bank transfer payment.
     * Use this field only if you are using giropay or bank transfer payment methods in Germany.
     */
    public function getGiropayBankTxnPendingUrl()
    {
        return Mage::getUrl($this->getConfigData('giropay_bank_pending', 'paypal/express/bank'));
    }

    /**
     * Decide whether to return from Paypal EC before payment was made or after
     *
     * @return string
     */
    public function getUserAction()
    {
        return $this->getSessionData('user_action', self::USER_ACTION_CONTINUE);
    }

    /**
     * Return user action based on paypal reqponse process
     *
     * @return string
     */
    public function setUserAction($data)
    {
        return $this->setSessionData('user_action', $data);
    }

    /**
     * PayPal API token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->getSessionData('token');
    }

    /**
     * Set tiken value in session
     *
     * @package $data string
     *
     * @return Mage_Paypal_Model_Api_Abstract
     */
    public function setToken($data)
    {
        return $this->setSessionData('token', $data);
    }

    /**
     * Return transaction id from session
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->getSessionData('transaction_id');
    }

    /**
     * Set transaction id in session
     *
     * @param $data string
     *
     * @return Mage_Paypal_Model_Api_Abstract
     *
     */
    public function setTransactionId($data)
    {
        return $this->setSessionData('transaction_id', $data);
    }

    /**
     * Get authorization id from session data
     *
     * @return string
     */
    public function getAuthorizationId()
    {
        return $this->getSessionData('authorization_id');
    }

    /**
     * Set authorization id in session
     *
     * @param $data string
     *
     * @return Mage_Paypal_Model_Api_Abstract
     *
     */
    public function setAuthorizationId($data)
    {
        return $this->setSessionData('authorization_id', $data);
    }

    /**
     * Return payer id from session
     *
     * @return string
     */
    public function getPayerId()
    {
        return $this->getSessionData('payer_id');
    }

    /**
     * Set payer id in session
     *
     * @param $data string
     *
     * @return Mage_Paypal_Model_Api_Abstract
     *
     */
    public function setPayerId($data)
    {
        return $this->setSessionData('payer_id', $data);
    }

    /**
     * Complete type code (Complete, NotComplete)
     *
     * @return string
     */
    public function getCompleteType()
    {
        return $this->getSessionData('complete_type');
    }

    /**
     * Set Complite type code in session
     *
     * @param $data string
     *
     * @return Mage_Paypal_Model_Api_Abstract
     *
     */
    public function setCompleteType($data)
    {
        return $this->setSessionData('complete_type', $data);
    }

    /**
     * Has to be one of the following values: Sale or Order or Authorization
     *
     * @return string
     */
    public function getPaymentType()
    {
        return $this->getSessionData('payment_type');
    }

    /**
     * Set payment type in session as paypal response comes result
     *
     * @param $data string
     *
     * @return Mage_Paypal_Model_Api_Abstract
     */
    public function setPaymentType($data)
    {
        return $this->setSessionData('payment_type', $data);
    }

    /**
     * Total value of the shopping cart
     *
     * Includes taxes, shipping costs, discount, etc.
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->getSessionData('amount');
    }

    /**
     * Set payment amount in session
     *
     * @param $data string
     *
     * @return Mage_Paypal_Model_Api_Abstract
     */
    public function setAmount($amount)
    {
        $amount = sprintf('%.2F', $amount);
        return $this->setSessionData('amount', $amount);
    }

    /**
     * Return currency code from session data
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        //return $this->getSessionData('currency_code', 'USD');
        return $this->getSessionData('currency_code', Mage::app()->getStore()->getBaseCurrencyCode());
    }

    /**
     * Set currency code in session
     *
     * @param $data string
     *
     * @return Mage_Paypal_Model_Api_Abstract
     */

    public function setCurrencyCode($data)
    {
        return $this->setSessionData('currency_code', $data);
    }

    /**
     * Refund type ('Full', 'Partial')
     *
     * @return string
     */
    public function getRefundType()
    {
        return $this->getSessionData('refund_type');
    }

    /**
     * Set payment return type in session as a result of paypal response come
     *
     * @param $data string
     *
     * @return Mage_Paypal_Model_Api_Abstract
     */
    public function setRefundType($data)
    {
        return $this->setSessionData('refund_type', $data);
    }

    /**
     * Return paypal request errors, get error message from response and set in session
     *
     * @return string
     */
    public function getError()
    {
        return $this->getSessionData('error');
    }


    /**
     * Error message getter intended to be based on error session data
     * @return string
     */
    public function getErrorMessage()
    {
        return '';
    }

    /**
     * Set paypal request error data in session
     *
     * @param $data string
     *
     * @return Mage_Paypal_Model_Api_Abstract
     */
    public function setError($data)
    {
        return $this->setSessionData('error', $data);
    }

    /**
     * Return ccType title by given type code
     *
     * @return string
     */
    public function getCcTypeName($ccType)
    {
        $types = array('AE'=>Mage::helper('paypal')->__('Amex'), 'VI'=>Mage::helper('paypal')->__('Visa'), 'MC'=>Mage::helper('paypal')->__('MasterCard'), 'DI'=>Mage::helper('paypal')->__('Discover'));
        return isset($types[$ccType]) ? $types[$ccType] : false;
    }

    /**
     * Reset session error scope
     *
     * @return Mage_Paypal_Model_Api_Abstract
     */
    public function unsError()
    {
        return $this->setSessionData('error', null);
    }

    /**
     * Get AVS proper text by given AVS response code
     *
     * @return string
     */
    public function getAvsDetail($avsCode)
    {
        switch ($avsCode) {
                    case self::AVS_RESPONSE_MATCH:
                return Mage::helper('paypal')->__('All the address information matched.');
            case self::AVS_RESPONSE_NONE:
            case self::AVS_RESPONSE_NO_MATCH:
                return Mage::helper('paypal')->__('None of the address information matched.');
            case self::AVS_RESPONSE_PARTIAL :
                return Mage::helper('paypal')->__('Part of the address information matched.');
            case self::AVS_RESPONSE_NOT_AVAILIABLE :
                return Mage::helper('paypal')->__('Address not checked, or acquirer had no response. Service not available.');
            case self::AVS_RESPONSE_NO_CARDHOLDER:
                return Mage::helper('paypal')->__('Cardholder\'s bank doesn\'t support address verification');
            case self::AVS_RESPONSE_NOT_PROCESSED :
                return Mage::helper('paypal')->__('The merchant did not provide AVS information. Not processed.');
            default:
                if ($avsCode === self::AVS_RESPONSE_ALL) {
                    return Mage::helper('paypal')->__('All the address information matched.');
                } else {
                    return '';
                }
        }
    }

    /**
     * Return mapped CVV text by given cvv code
     *
     * @return string
     */
    public function getCvvDetail($cvvCode)
    {
        switch ($cvvCode) {
        case self::CVV_RESPONSE_MATCH_CC:
            return Mage::helper('paypal')->__('Matched');
        case self::CVV_RESPONSE_NOT_MATCH_CC:
        case self::CVV_RESPONSE_NOT_MATCH_SOLO:
            return Mage::helper('paypal')->__('No match');
        case self::CVV_RESPONSE_NOT_PROCESSED_CC :
            return Mage::helper('paypal')->__('Not processed');
        case self::CVV_RESPONSE_NOT_IMPLEMENTED_SOLO :
            return Mage::helper('paypal')->__('The merchant has not implemented CVV2 code handling');
        case self::CVV_RESPONSE_NOT_SUPPORTED_CC :
            return Mage::helper('paypal')->__('Service not supported');
        case self::CVV_RESPONSE_NOT_PRESENT_SOLO :
            return Mage::helper('paypal')->__('Merchant has indicated that CVV2 is not present on card');
        case self::CVV_RESPONSE_NOT_AVAILIBLE_CC :
        case self::CVV_RESPONSE_NOT_AVAILIBLE_SOLO :
            return Mage::helper('paypal')->__('Service not available');
        case self::CVV_RESPONSE_NOT_RESPONSE_CC :
            return Mage::helper('paypal')->__('No response');
        default:
            if (self::CVV_RESPONSE_MATCH_SOLO === $cvvCode) {
                return Mage::helper('paypal')->__('Matched');
            } else {
                return '';
            }
        }
    }
}
