<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Website Payments Pro Hosted Solution payment gateway model
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Model_Hostedpro extends Mage_Paypal_Model_Direct
{
    /**
     * Default layout template
     */
    public const LAYOUT_TEMPLATE = 'templateD';

    /**
     * Mobile layout template
     */
    public const MOBILE_LAYOUT_TEMPLATE = 'mobile-iframe';

    /**
     * Button code
     *
     * @var string
     */
    public const BM_BUTTON_CODE    = 'TOKEN';

    /**
     * Button type
     *
     * @var string
     */
    public const BM_BUTTON_TYPE    = 'PAYMENT';

    /**
     * Paypal API method name for button creation
     *
     * @var string
     */
    public const BM_BUTTON_METHOD  = 'BMCreateButton';

    /**
     * Payment method code
     */
    protected $_code = Mage_Paypal_Model_Config::METHOD_HOSTEDPRO;

    protected $_formBlockType = 'paypal/hosted_pro_form';

    protected $_infoBlockType = 'paypal/hosted_pro_info';

    /**
     * Availability options
     */
    protected $_canUseInternal          = false;

    protected $_canUseForMultishipping  = false;

    protected $_canSaveCc               = false;

    protected $_isInitializeNeeded      = true;

    /**
     * Return available CC types for gateway based on merchant country.
     * We do not have to check the availability of card types.
     *
     * @return bool
     */
    public function getAllowedCcTypes()
    {
        return true;
    }

    /**
     * Return merchant country code from config,
     * use default country if it not specified in General settings
     *
     * @return string
     */
    public function getMerchantCountry()
    {
        return $this->_pro->getConfig()->getMerchantCountry();
    }

    /**
     * Return iframe template value depending on config
     *
     * @return string
     */
    public function getTemplate()
    {
        if ($this->getConfigData('mobile_optimized')) {
            return self::MOBILE_LAYOUT_TEMPLATE;
        } else {
            return self::LAYOUT_TEMPLATE;
        }
    }

    /**
     * Do not validate payment form using server methods
     *
     * @return  bool
     */
    public function validate()
    {
        return true;
    }

    /**
     * Instantiate state and set it to state object
     *
     * @param string $paymentAction
     * @param Varien_Object $stateObject
     */
    public function initialize($paymentAction, $stateObject)
    {
        switch ($paymentAction) {
            case Mage_Paypal_Model_Config::PAYMENT_ACTION_AUTH:
            case Mage_Paypal_Model_Config::PAYMENT_ACTION_SALE:
                $payment = $this->getInfoInstance();
                $order = $payment->getOrder();
                $order->setCanSendNewEmailFlag(false);
                $payment->setAmountAuthorized($order->getTotalDue());
                $payment->setBaseAmountAuthorized($order->getBaseTotalDue());

                $this->_setPaymentFormUrl($payment);

                $stateObject->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
                $stateObject->setStatus('pending_payment');
                $stateObject->setIsNotified(false);
                break;
            default:
                break;
        }

        return $this;
    }

    /**
     * Sends API request to PayPal to get form URL, then sets this URL to $payment object.
     */
    protected function _setPaymentFormUrl(Mage_Payment_Model_Info $payment)
    {
        $request = $this->_buildFormUrlRequest($payment);
        $response = $this->_sendFormUrlRequest($request);
        if ($response) {
            $payment->setAdditionalInformation('secure_form_url', $response);
        } else {
            Mage::throwException('Cannot get secure form URL from PayPal');
        }
    }

    /**
     * Returns request object with needed data for API request to PayPal to get form URL.
     *
     * @return Mage_Paypal_Model_Hostedpro_Request
     */
    protected function _buildFormUrlRequest(Mage_Payment_Model_Info $payment)
    {
        return $this->_buildBasicRequest()
            ->setOrder($payment->getOrder())
            ->setPaymentMethod($this);
    }

    /**
     * Returns form URL from request to PayPal.
     *
     * @return false|string
     */
    protected function _sendFormUrlRequest(Mage_Paypal_Model_Hostedpro_Request $request)
    {
        $api = $this->_pro->getApi();
        $response = $api->call(self::BM_BUTTON_METHOD, $request->getRequestData());

        return $response['EMAILLINK'] ?? false;
    }

    /**
     * Return request object with basic information
     *
     * @return Mage_Paypal_Model_Hostedpro_Request
     */
    protected function _buildBasicRequest()
    {
        $request = Mage::getModel('paypal/hostedpro_request');
        $request->setData([
            'METHOD'     => self::BM_BUTTON_METHOD,
            'BUTTONCODE' => self::BM_BUTTON_CODE,
            'BUTTONTYPE' => self::BM_BUTTON_TYPE,
        ]);
        return $request;
    }

    /**
     * Get return URL
     *
     * @param int $storeId
     * @return string
     */
    public function getReturnUrl($storeId = null)
    {
        return $this->_getUrl('paypal/hostedpro/return', $storeId);
    }

    /**
     * Get notify (IPN) URL
     *
     * @param int $storeId
     * @return string
     */
    public function getNotifyUrl($storeId = null)
    {
        return $this->_getUrl('paypal/ipn', $storeId);
    }

    /**
     * Get cancel URL
     *
     * @param int $storeId
     * @return string
     */
    public function getCancelUrl($storeId = null)
    {
        return $this->_getUrl('paypal/hostedpro/cancel', $storeId);
    }

    /**
     * Build URL for store
     *
     * @param string $path
     * @param int $storeId
     * @param bool $secure
     * @return string
     */
    protected function _getUrl($path, $storeId, $secure = null)
    {
        $store = Mage::app()->getStore($storeId);
        return Mage::getUrl($path, [
            '_store'   => $store,
            '_secure'  => is_null($secure) ? $store->isCurrentlySecure() : $secure,
        ]);
    }
}
