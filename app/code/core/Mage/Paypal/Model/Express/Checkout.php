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
 * Wrapper that performs Paypal Express and Checkout communication
 * Use current Paypal Express method instance
 */
class Mage_Paypal_Model_Express_Checkout
{
    /**
     * Cache ID prefix for "pal" lookup
     * @var string
     */
    const PAL_CACHE_ID = 'paypal_express_checkout_pal';

    /**
     * Keys for passthrough variables in sales/quote_payment and sales/order_payment
     * Uses additional_information as storage
     * @var string
     */
    const PAYMENT_INFO_TRANSPORT_TOKEN    = 'paypal_express_checkout_token';
    const PAYMENT_INFO_TRANSPORT_SHIPPING_OVERRIDEN = 'paypal_express_checkout_shipping_overriden';
    const PAYMENT_INFO_TRANSPORT_PAYER_ID = 'paypal_express_checkout_payer_id';
    const PAYMENT_INFO_TRANSPORT_REDIRECT = 'paypal_express_checkout_redirect_required';

    /**
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote = null;

    /**
     * Config instance
     * @var Mage_Paypal_Model_Config
     */
    protected $_config = null;

    /**
     * API instance
     * @var Mage_Paypal_Model_Api_Nvp
     */
    protected $_api = null;

    /**
     * Api Model Type
     *
     * @var string
     */
    protected $_apiType = 'paypal/api_nvp';

    /**
     * Payment method type
     *
     * @var unknown_type
     */
    protected $_methodType = Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS;

    /**
     * State helper variables
     * @var string
     */
    protected $_redirectUrl = '';
    protected $_pendingPaymentMessage = '';
    protected $_checkoutRedirectUrl = '';

    /**
     * Redirect urls supposed to be set to support giropay
     *
     * @var array
     */
    protected $_giropayUrls = array();

    /**
     * Set quote and config instances
     * @param array $params
     */
    public function __construct($params = array())
    {
        if (isset($params['quote']) && $params['quote'] instanceof Mage_Sales_Model_Quote) {
            $this->_quote = $params['quote'];
        } else {
            throw new Exception('Quote instance is required.');
        }
        if (isset($params['config']) && $params['config'] instanceof Mage_Paypal_Model_Config) {
            $this->_config = $params['config'];
        } else {
            throw new Exception('Config instance is required.');
        }
    }

    /**
     * Checkout with PayPal image URL getter
     * Spares API calls of getting "pal" variable, by putting it into cache per store view
     * @return string
     */
    public function getCheckoutShortcutImageUrl()
    {
        // get "pal" thing from cache or lookup it via API
        $pal = null;
        if ($this->_config->areButtonsDynamic()) {
            $cacheId = self::PAL_CACHE_ID . Mage::app()->getStore()->getId();
            $pal = Mage::app()->loadCache($cacheId);
            if (-1 == $pal) {
                $pal = null;
            } elseif (!$pal) {
                $pal = null;
                $this->_getApi();
                try {
                    $this->_api->callGetPalDetails();
                    $pal = $this->_api->getPal();
                    Mage::app()->saveCache($pal, $cacheId, array(Mage_Core_Model_Config::CACHE_TAG));
                } catch (Exception $e) {
                    Mage::app()->saveCache(-1, $cacheId, array(Mage_Core_Model_Config::CACHE_TAG));
                    Mage::logException($e);
                }
            }
        }

        return $this->_config->getExpressCheckoutShortcutImageUrl(
            Mage::app()->getLocale()->getLocaleCode(),
            $this->_quote->getBaseGrandTotal(),
            $pal
        );
    }

    /**
     * Setter that enables giropay redirects flow
     *
     * @param string $successUrl - payment success result
     * @param string $cancelUrl  - payment cancellation result
     * @param string $pendingUrl - pending payment result
     */
    public function prepareGiropayUrls($successUrl, $cancelUrl, $pendingUrl)
    {
        $this->_giropayUrls = array($successUrl, $cancelUrl, $pendingUrl);
        return $this;
    }

    /**
     * Reserve order ID for specified quote and start checkout on PayPal
     * @return string
     */
    public function start($returnUrl, $cancelUrl)
    {
        $this->_quote->reserveOrderId()->save();
        // prepare API
        $this->_getApi();
        $this->_api->setAmount($this->_quote->getBaseGrandTotal())
            ->setCurrencyCode($this->_quote->getBaseCurrencyCode())
            ->setInvNum($this->_quote->getReservedOrderId())
            ->setReturnUrl($returnUrl)
            ->setCancelUrl($cancelUrl)
            ->setSolutionType($this->_config->solutionType)
            ->setPaymentAction($this->_config->paymentAction)
        ;
        if ($this->_giropayUrls) {
            list($successUrl, $cancelUrl, $pendingUrl) = $this->_giropayUrls;
            $this->_api->addData(array(
                'giropay_cancel_url' => $cancelUrl,
                'giropay_success_url' => $successUrl,
                'giropay_bank_txn_pending_url' => $pendingUrl,
            ));
        }

        // supress or export shipping address
        if ($this->_quote->getIsVirtual()) {
            $this->_api->setSuppressShipping(true);
        } else {
            $address = $this->_quote->getShippingAddress();
            $isOverriden = 0;
            if (true === $address->validate()) {
                $isOverriden = 1;
                $this->_api->setAddress($address);
            }
            $this->_quote->getPayment()->setAdditionalInformation(
                self::PAYMENT_INFO_TRANSPORT_SHIPPING_OVERRIDEN, $isOverriden
            );
            $this->_quote->getPayment()->save();
        }
        // add line items
        if ($this->_config->lineItemsEnabled && Mage::helper('paypal')->doLineItemsMatchAmount($this->_quote, $this->_quote->getBaseGrandTotal())) {//For transfering line items order amount must be equal to cart total amount
            list($items, $totals) = Mage::helper('paypal')->prepareLineItems($this->_quote);
            $this->_api->setLineItems($items)->setLineItemTotals($totals);
        }

        $this->_config->exportExpressCheckoutStyleSettings($this->_api);

        // call API and redirect with token
        $this->_api->callSetExpressCheckout();
        $token = $this->_api->getToken();
        $this->_redirectUrl = $this->_config->getExpressCheckoutStartUrl($token);
        return $token;
    }

    /**
     * Update quote when returned from PayPal
     * @param string $token
     */
    public function returnFromPaypal($token)
    {
        $this->_getApi();
        $this->_api->setToken($token)->callGetExpressCheckoutDetails();

        // import addresses
        $billingAddress = $this->_quote->getBillingAddress();
        $exportedBillingAddress = $this->_api->getExportedBillingAddress();
        foreach ($exportedBillingAddress->getExportedKeys() as $key) {
            $billingAddress->setDataUsingMethod($key, $exportedBillingAddress->getData($key));
        }
        $exportedShippingAddress = $this->_api->getExportedShippingAddress();
        if ((!$this->_quote->getIsVirtual()) && $exportedShippingAddress
            && $shippingAddress = $this->_quote->getShippingAddress()) {
            foreach ($exportedShippingAddress->getExportedKeys() as $key) {
                $shippingAddress->setDataUsingMethod($key, $exportedShippingAddress->getData($key));
            }
            $shippingAddress->setCollectShippingRates(true);
        }
        $this->_ignoreAddressValidation();

        // import payment info
        $payment = $this->_quote->getPayment();
        $payment->setMethod($this->_methodType);
        Mage::getSingleton('paypal/info')->importToPayment($this->_api, $payment);
        $payment->setAdditionalInformation(self::PAYMENT_INFO_TRANSPORT_PAYER_ID, $this->_api->getPayerId())
            ->setAdditionalInformation(self::PAYMENT_INFO_TRANSPORT_TOKEN, $token)
        ;
        $this->_quote->collectTotals()->save();
    }

    /**
     * Check whether order review has enough data to initialize
     *
     * @param $token
     * @throws Mage_Core_Exception
     */
    public function prepareOrderReview($token = null)
    {
        $payment = $this->_quote->getPayment();
        if (!$payment || !$payment->getAdditionalInformation(self::PAYMENT_INFO_TRANSPORT_PAYER_ID)) {
            Mage::throwException(Mage::helper('paypal')->__('Payer is not identified.'));
        }
        $this->_ignoreAddressValidation();
        $this->_quote->collectTotals()->save();
    }

    /**
     * Set shipping method to quote, if needed
     * @param string $methodCode
     */
    public function updateShippingMethod($methodCode)
    {
        if (!$this->_quote->getIsVirtual() && $shippingAddress = $this->_quote->getShippingAddress()) {
            if ($methodCode != $shippingAddress->getShippingMethod()) {
                $this->_ignoreAddressValidation();
                $shippingAddress->setShippingMethod($methodCode)->setCollectShippingRates(true);
                $this->_quote->collectTotals()->save();
            }
        }
    }

    /**
     * Place the order when customer returned from paypal
     * Until this moment all quote data must be valid
     *
     * @param string $token
     * @param string $shippingMethodCode
     * @return Mage_Sales_Model_Order
     */
    public function placeOrder($token, $shippingMethodCode = null)
    {
        if ($shippingMethodCode) {
            $this->updateShippingMethod($shippingMethodCode);
        }

        if (!$this->_quote->getCustomerId()) {
            $this->_quote->setCustomerIsGuest(true)
                ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID)
                ->setCustomerEmail($this->_quote->getBillingAddress()->getEmail());
        }

        $this->_ignoreAddressValidation();
        $order = Mage::getModel('sales/service_quote', $this->_quote)->submit();
        $this->_quote->save();

        // commence redirecting to finish payment, if paypal requires it
        if ($order->getPayment()->getAdditionalInformation(Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_REDIRECT)) {
            $this->_redirectUrl = $this->_config->getExpressCheckoutCompleteUrl($token);
        }

        switch ($order->getState()) {
            // even after placement paypal can disallow to authorize/capture, but will wait until bank transfers money
            case Mage_Sales_Model_Order::STATE_PENDING_PAYMENT:
                // TODO
                break;
            // regular placement, when everything is ok
            case Mage_Sales_Model_Order::STATE_PROCESSING:
            case Mage_Sales_Model_Order::STATE_COMPLETE:
                $order->sendNewOrderEmail();
                break;
        }
        return $order;
    }

    /**
     * Whether customer is allowed to edit shipping address on order review
     *
     * @return bool
     */
    public function mayEditShippingAddress()
    {
        return 1 != $this->_quote->getPayment()
            ->getAdditionalInformation(self::PAYMENT_INFO_TRANSPORT_SHIPPING_OVERRIDEN);
    }

    /**
     * Make sure addresses will be saved without validation errors
     */
    private function _ignoreAddressValidation()
    {
        $this->_quote->getBillingAddress()->setShouldIgnoreValidation(true);
        if (!$this->_quote->getIsVirtual()) {
            $this->_quote->getShippingAddress()->setShouldIgnoreValidation(true);
        }
    }

    /**
     * Determine whether redirect somewhere specifically is required
     *
     * @param string $action
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->_redirectUrl;
    }

    /**
     * @return Mage_Paypal_Model_Api_Nvp
     */
    protected function _getApi()
    {
        if (null === $this->_api) {
            $this->_api = Mage::getModel($this->_apiType)->setConfigObject($this->_config);
        }
        return $this->_api;
    }
}
