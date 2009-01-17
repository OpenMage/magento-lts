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
 * @category   Mage
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract class for Paypal API wrappers
 *
 * @author      Magento Core Team <core@magentocommerce.com>
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


/******************************************************************************************************************/
    public function getConfigData($key, $default=false)
    {
        if (!$this->hasData($key)) {
             $value = Mage::getStoreConfig('paypal/wpuk/'.$key);
             if (is_null($value) || false===$value) {
                 $value = $default;
             }
            $this->setData($key, $value);
        }
        return $this->getData($key);
    }

    public function getSession()
    {
        return Mage::getSingleton('paypaluk/session');
    }

    public function getUseSession()
    {
        if (!$this->hasData('use_session')) {
            $this->setUseSession(true);
        }
        return $this->getData('use_session');
    }

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

    public function setSessionData($key, $value)
    {
        if ($this->getUseSession()) {
            $this->getSession()->setData($key, $value);
        }
        $this->setData($key, $value);
        return $this;
    }

    public function getApiUrl()
    {
        return $this->getConfigData('url');
    }

    public function getApiUser()
    {
        return $this->getConfigData('user');
    }

    public function getApiVendor()
    {
        return $this->getConfigData('vendor');
    }

    public function getApiPassword()
    {
       return $this->getConfigData('pwd');
    }

    public function getDebug()
    {
        return $this->getConfigData('debug_flag', true);
    }

    public function getPartner()
    {
        return $this->getConfigData('partner', 'PayPalUK');
    }

    public function getError()
    {
        return $this->getSessionData('error');
    }

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

    public function setToken($data)
    {
        return $this->setSessionData('token', $data);
    }

    public function getTransactionId()
    {
        return $this->getSessionData('transaction_id');
    }

    public function setTransactionId($data)
    {
        return $this->setSessionData('transaction_id', $data);
    }

    public function getAuthorizationId()
    {
        return $this->getSessionData('authorization_id');
    }

    public function setAuthorizationId($data)
    {
        return $this->setSessionData('authorization_id', $data);
    }

    public function getPayerId()
    {
        return $this->getSessionData('payer_id');
    }

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

    public function setAmount($data)
    {
        $data = sprintf('%.2f', $data);
        return $this->setSessionData('amount', $data);
    }

    public function getCurrencyCode()
    {
        //return $this->getSessionData('currency_code', 'USD');
        return $this->getSessionData('currency_code', Mage::app()->getStore()->getBaseCurrencyCode());
    }

    public function setCurrencyCode($data)
    {
        return $this->setSessionData('currency_code', $data);
    }


    public function getButtonSourceEc()
    {
        return $this->getConfigData('button_source', 'Varien_Cart_EC_UK');
    }

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

}
