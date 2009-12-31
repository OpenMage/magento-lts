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
 * @package     Mage_PaypalUk
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract class for PayflowUk Pro API wrappers
 */
abstract class Mage_PaypalUk_Model_Api_Abstract extends Varien_Object
{
    const PAYMENT_TYPE_SALE = 'authorize_capture';
    const PAYMENT_TYPE_AUTH = 'authorize';
    const PAYMENT_TYPE_ORDER = 'Order';

    const REFUND_TYPE_FULL = 'Full';
    const REFUND_TYPE_PARTIAL = 'Partial';

    const COMPLETE = 'Complete';
    const NOTCOMPLETE = 'NotComplete';

    const USER_ACTION_COMMIT = 'commit';
    const USER_ACTION_CONTINUE = 'continue';

    const ACTION_ACCEPT = 'Acept';
    const ACTION_DENY   = 'Deny';

    const SOLUTION_TYPE_SOLE = 'Sole';
    const SOLUTION_TYPE_MARK = 'Mark';

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

/******************************************************************************************************************/
    /**
     * Return config data based on payment and store id
     *
     * @return string
     */
    public function getGeneralConfigData($key, $default=false, $storeId = null, $path = 'paypal/wpuk/')
    {
        if (!$this->hasData($key)) {
            if ($storeId === null && $this->getPayment() instanceof Varien_Object) {
                $storeId = $this->getPayment()->getOrder()->getStoreId();
            }
            $value = Mage::getStoreConfig($path . $key, $storeId);
            if (is_null($value) || false===$value) {
                $value = $default;
            }
            $this->setData($key, $value);
        }
        return $this->getData($key);
    }

    /**
     * Return config data based on paymethod, store id
     *
     * @return string
     */
    public function getConfigData($key, $default=false, $storeId = null)
    {
        return $this->getGeneralConfigData($key, $default, $storeId, 'paypal/wpuk/');
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
     * Return paypaluk session model
     *
     * @return Mage_PayplaUk_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('paypaluk/session');
    }

    /**
     * Get use session flag status
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
     * Return session data
     *
     * @param $key string
     * @param $default string, default value
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
     * Set data in paypaluk session
     *
     * @param $key string
     * @param $value string
     *
     * @return Mage_PayplaUk_Model_Api_Abstract
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
     * Return api config url
     *
     * @return string
     */
    public function getApiUrl()
    {
        return $this->getConfigData('url');
    }

    /**
     * Return Api user from config
     *
     * @return string
     */
    public function getApiUser()
    {
        return $this->getConfigData('user');
    }

    /**
     * Return Api vendor id from config
     *
     * @return string
     */
    public function getApiVendor()
    {
        return $this->getConfigData('vendor');
    }

    /**
     * Return Api password from config
     *
     * @return string
     */
    public function getApiPassword()
    {
       return $this->getConfigData('pwd');
    }

    /**
     * Return debug status based on config data
     *
     * @return bool
     */
    public function getDebug()
    {
        return $this->getConfigData('debug_flag', true);
    }

    /**
     * Get partner value from config
     *
     * @return string
     */
    public function getPartner()
    {
        return $this->getConfigData('partner', 'PayPalUK');
    }

    /**
     * Get Error Message from session
     *
     * @return string
     */
    public function getError()
    {
        return $this->getSessionData('error');
    }

    /**
     * Set error mesage in session
     *
     * @param string
     *
     * @return Mage_PayplaUk_Model_Api_Abstract
     */
    public function setError($data)
    {
        return $this->setSessionData('error', $data);
    }

    /**
     * the page where buyers return to after they are done with the payment review on PayPal
     *
     * @return string
     */
    public function getReturnUrl()
    {
        return Mage::getUrl($this->getConfigData('api_return_url', 'paypaluk/express/return'));
    }

    /**
     * The page where buyers return to when they cancel the payment review on PayPal
     *
     * @return string
     */
    public function getCancelUrl()
    {
        return Mage::getUrl($this->getConfigData('api_cancel_url', 'paypaluk/express/cancel'));
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
     * Set user action to session data
     *
     * @param $data string
     *
     * @return Mage_PayplaUk_Model_Api_Abstract
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
     * Set token data to session
     *
     * @param $data string
     *
     * @return Mage_PayplaUk_Model_Api_Abstract
     *
     */
    public function setToken($data)
    {
        return $this->setSessionData('token', $data);
    }

    /**
     * Get transaction id from session
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->getSessionData('transaction_id');
    }

    /**
     * Set transaction id to session
     *
     * @param $data string
     *
     * @return Mage_PayplaUk_Model_Api_Abstract
     *
     */
    public function setTransactionId($data)
    {
        return $this->setSessionData('transaction_id', $data);
    }

    /**
     * Get authorization id from session
     *
     * @return string
     *
     */
    public function getAuthorizationId()
    {
        return $this->getSessionData('authorization_id');
    }

    /**
     * Set authorization id to session
     *
     * @param $data string
     *
     * @return Mage_PayplaUk_Model_Api_Abstract
     *
     */
    public function setAuthorizationId($data)
    {
        return $this->setSessionData('authorization_id', $data);
    }

    /**
     * Get payer id from session
     *
     * @return string
     *
     */
    public function getPayerId()
    {
        return $this->getSessionData('payer_id');
    }

    /**
     * Set payer id to session
     *
     * @param $data string
     *
     * @return Mage_PayplaUk_Model_Api_Abstract
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
     * Set payment type to session
     *
     * @param $data string
     *
     * @return Mage_PayplaUk_Model_Api_Abstract
     *
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
     * Set formated amount value to session
     *
     * @param $data string
     *
     * @return Mage_PayplaUk_Model_Api_Abstract
     *
     */
    public function setAmount($data)
    {
        $data = sprintf('%.2f', $data);
        return $this->setSessionData('amount', $data);
    }

    /**
     * get formated currency code from session
     *
     * @return string
     *
     */
    public function getCurrencyCode()
    {
        //return $this->getSessionData('currency_code', 'USD');
        // !!! return $this->getSessionData('currency_code', $this->getPayment()->getOrder()->getStore()->getBaseCurrencyCode());
        return $this->getSessionData('currency_code', Mage::app()->getStore()->getBaseCurrencyCode());
    }

    /**
     * Set currency code to session
     *
     * @param $data string
     *
     * @return Mage_PayplaUk_Model_Api_Abstract
     *
     */
    public function setCurrencyCode($data)
    {
        return $this->setSessionData('currency_code', $data);
    }

    /**
     * return source for express checkout button from config
     *
     * @return string
     *
     */
    public function getButtonSourceEc()
    {
        return $this->getConfigData('button_source', 'Varien_Cart_EC_UK');
    }

    /**
     * return source for direct payment button from config
     *
     * @return string
     *
     */
    public function getButtonSourceDp()
    {
        return $this->getConfigData('button_source', 'Varien_Cart_DP_UK');
    }

    /**
     * the page where buyers will go if there are API error
     *
     * @return string
     */
    public function getApiErrorUrl()
    {
        return Mage::getUrl($this->getConfigData('api_error_url', 'paypaluk/express/error'));
    }

    /**
     *return all avaialble uk cardtypes
     */
    public function getCcTypes()
    {
        foreach (Mage::getSingleton('payment/config')->getCcTypes() as $code => $name) {
            $ccTypes[$code] = $name;
        }
        return $ccTypes;
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
     * Return Cvv Detailed description by cvv responce code
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
