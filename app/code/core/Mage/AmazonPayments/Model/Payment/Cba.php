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
 * @category   Mage
 * @package    Mage_AmazonPayments
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_AmazonPayments_Model_Payment_Cba extends Mage_Payment_Model_Method_Abstract
{
    /**
     * Payment module of Checkout by Amazon
     * CBA - Checkout By Amazon
     */

    protected $_code  = 'amazonpayments_cba';
    protected $_formBlockType = 'amazonpayments/cba_form';
    protected $_api;

    protected $_isGateway               = false;
    protected $_canAuthorize            = true;
    protected $_canCapture              = false;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = true;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = false;
    protected $_canUseForMultishipping  = false;

//    const ACTION_AUTHORIZE = 0;
//    const ACTION_AUTHORIZE_CAPTURE = 1;

    protected $_skipProccessDocument = false;

    /**
     * Return true if the method can be used at this time
     *
     * @return bool
     */
    public function isAvailable($quote=null)
    {
        return Mage::getStoreConfig('payment/amazonpayments_cba/active');
    }

    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get AmazonPayments API Model
     *
     * @return Mage_AmazonPayments_Model_Api_Cba
     */
    public function getApi()
    {
        if (!$this->_api) {
            $this->_api = Mage::getSingleton('amazonpayments/api_cba');
            $this->_api->setPaymentCode($this->getCode());
        }
        return $this->_api;
    }

    /**
     * Get AmazonPayments session namespace
     *
     * @return Mage_AmazonPayments_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('amazonpayments/session');
    }

    /**
     * Retrieve redirect url
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return Mage::getUrl('amazonpayments/cba/redirect');
    }

    /**
     * Retrieve redirect to Amazon CBA url
     *
     * @return string
     */
    public function getAmazonRedirectUrl()
    {
        return $this->getApi()->getAmazonRedirectUrl();
    }

    /**
     * Authorize
     *
     * @param   Varien_Object $orderPayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        parent::authorize($payment, $amount);
        return $this;
    }

    /**
     * Capture payment
     *
     * @param   Varien_Object $orderPayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function capture(Varien_Object $payment, $amount)
    {
        parent::capture($payment, $amount);
        return $this;
    }

    public function cancel(Varien_Object $payment)
    {
        if ($this->_skipProccessDocument) {
            return $this;
        }
        $this->getApi()->cancel($payment->getOrder());
        return $this;
    }

    /**
     * Refund order
     *
     * @param   Varien_Object $payment
     * @return  Mage_AmazonPayments_Model_Payment_Cba
     */
    public function refund(Varien_Object $payment, $amount)
    {
        if ($this->_skipProccessDocument) {
            return $this;
        }
        $this->getApi()->refund($payment, $amount);
        return $this;
    }

    /**
     * Handle Callback from CBA and calculate Shipping, Taxes in case XML-based shopping cart
     *
     */
    public function handleCallback($_request)
    {
        $response = '';

        if (!empty($_request['order-calculations-request'])) {
            $xmlRequest = urldecode($_request['order-calculations-request']);

            $session = $this->getCheckout();
            $xml = $this->getApi()->handleXmlCallback($xmlRequest, $session);

            if ($this->getDebug()) {
                $debug = Mage::getModel('amazonpayments/api_debug')
                    ->setRequestBody(print_r($_request, 1))
                    ->setResponseBody(time().' - request callback')
                    ->save();
            }

            if ($xml) {
                $xmlText = $xml->asXML();
                $response .= 'order-calculations-response='.urlencode($xmlText);
                #$response .= 'order-calculations-response='.base64_encode($xmlText);

                $secretKeyID = Mage::getStoreConfig('payment/amazonpayments_cba/secretkey_id');

                $_signature = $this->getApi()->calculateSignature($xmlText, $secretKeyID);

                if ($_signature) {
                    $response .= '&Signature='.urlencode($_signature);
                    #$response .= '&Signature='.$_signature;
                }
                $response .= '&aws-access-key-id='.urlencode(Mage::getStoreConfig('payment/amazonpayments_cba/accesskey_id'));

                if ($this->getDebug()) {
                    $debug = Mage::getModel('amazonpayments/api_debug')
                        ->setResponseBody($response)
                        ->setRequestBody(time() .' - response calllback')
                        ->save();
                }
            }
        } else {
            if ($this->getDebug()) {
                $debug = Mage::getModel('amazonpayments/api_debug')
                    ->setRequestBody(print_r($_request, 1))
                    ->setResponseBody(time().' - error request callback')
                    ->save();
            }
        }
        return $response;
    }

    public function handleNotification($_request)
    {
        if (!empty($_request) && !empty($_request['NotificationData']) && !empty($_request['NotificationType'])) {
            /**
             * Debug
             */
            if ($this->getDebug()) {
                $debug = Mage::getModel('amazonpayments/api_debug')
                    ->setRequestBody(print_r($_request, 1))
                    ->setResponseBody(time().' - Notification: '. $_request['NotificationType'])
                    ->save();
            }
            switch ($_request['NotificationType']) {
                case 'NewOrderNotification':
                    $newOrderDetails = $this->getApi()->parseOrder($_request['NotificationData']);
                    $this->_createNewOrder($newOrderDetails);
                    break;
                case 'OrderReadyToShipNotification':
                    $amazonOrderDetails = $this->getApi()->parseOrder($_request['NotificationData']);
                    $this->_proccessOrder($amazonOrderDetails);
                    break;
                case 'OrderCancelledNotification':
                    $cancelDetails = $this->getApi()->parseCancelNotification($_request['NotificationData']);
                    $this->_skipProccessDocument = true;
                    $this->_cancelOrder($cancelDetails);
                    $this->_skipProccessDocument = false;
                    break;
                default:
                    // Unknown notification type
            }
        } else {
            if ($this->getDebug()) {
                $debug = Mage::getModel('amazonpayments/api_debug')
                    ->setRequestBody(print_r($_request, 1))
                    ->setResponseBody(time().' - error request callback')
                    ->save();
            }
        }
        return $this;
    }

    /**
     * Create new order by data from Amazon NewOrderNotification
     *
     * @param array $newOrderDetails
     */
    protected function _createNewOrder(array $newOrderDetails)
    {
        if (array_key_exists('amazonOrderID', $newOrderDetails)) {
            $_order = Mage::getModel('sales/order')
                ->loadByAttribute('ext_order_id', $newOrderDetails['amazonOrderID']);
            if ($_order->getId()) {
                $_order = null;
                return $this;
            }
            $_order = null;
        }
        $session = $this->getCheckout();

        #$quoteId = $session->getAmazonQuoteId();

        $quoteId = $newOrderDetails['ClientRequestId'];
        $quote = Mage::getModel('sales/quote')->load($quoteId);

        $baseCurrency = $session->getQuote()->getBaseCurrencyCode();
        $currency = Mage::app()->getStore($session->getQuote()->getStoreId())->getBaseCurrency();

        $shipping = $quote->getShippingAddress();
        $billing = $quote->getBillingAddress();

        $_address = $newOrderDetails['shippingAddress'];
        $this->_address = $_address;

        $regionModel = Mage::getModel('directory/region')->loadByCode($_address['regionCode'], $_address['countryCode']);
        $_regionId = $regionModel->getId();

        $sName = explode(' ', $newOrderDetails['shippingAddress']['name']);
        $sFirstname = isset($sName[0])?$sName[0]:'';
        $sLastname = isset($sName[1])?$sName[1]:'';

        $bName = explode(' ', $newOrderDetails['buyerName']);
        $bFirstname = isset($bName[0])?$bName[0]:'';
        $bLastname = isset($bName[1])?$bName[1]:'';

        $shipping->setCountryId($_address['countryCode'])
            ->setRegion($_address['regionCode'])
            ->setRegionId($_regionId)
            ->setCity($_address['city'])
            ->setStreet($_address['street'])
            ->setPostcode($_address['postCode'])
            ->setTaxAmount($newOrderDetails['tax'])
            ->setBaseTaxAmount($newOrderDetails['tax'])
            ->setShippingAmount($newOrderDetails['shippingAmount'])
            ->setBaseShippingAmount($newOrderDetails['shippingAmount'])
            ->setShippingTaxAmount($newOrderDetails['shippingTax'])
            ->setBaseShippingTaxAmount($newOrderDetails['shippingTax'])
            ->setDiscountAmount($newOrderDetails['discount'])
            ->setBaseDiscountAmount($newOrderDetails['discount'])
            ->setSubtotal($newOrderDetails['subtotal'])
            ->setBaseSubtotal($newOrderDetails['subtotal'])
            ->setGrandTotal($newOrderDetails['total'])
            ->setBaseGrandTotal($newOrderDetails['total'])
            ->setFirstname($sFirstname)
            ->setLastname($sLastname);

        $_shippingDesc = '';
        $_shippingServices = unserialize($quote->getExtShippingInfo());
        if (is_array($_shippingServices) && array_key_exists('amazon_service_level', $_shippingServices)) {
            foreach ($_shippingServices['amazon_service_level'] as $_level) {
                if ($_level['service_level'] == $newOrderDetails['ShippingLevel']) {
                    $shipping->setShippingMethod($_level['code']);
                    $_shippingDesc = $_level['description'];
                }
            }
        }
        /** @todo save shipping method */
//        $this->getQuote()->getShippingAddress()->setShippingMethod($shippingMethod);

        $billing->setCountryId($_address['countryCode'])
            ->setRegion($_address['regionCode'])
            ->setRegionId($_regionId)
            ->setCity($_address['city'])
            ->setStreet($_address['street'])
            ->setPostcode($_address['postCode'])
            ->setTaxAmount($newOrderDetails['tax'])
            ->setBaseTaxAmount($newOrderDetails['tax'])
            ->setShippingAmount($newOrderDetails['shippingAmount'])
            ->setBaseShippingAmount($newOrderDetails['shippingAmount'])
            ->setShippingTaxAmount($newOrderDetails['shippingTax'])
            ->setBaseShippingTaxAmount($newOrderDetails['shippingTax'])
            ->setDiscountAmount($newOrderDetails['discount'])
            ->setBaseDiscountAmount($newOrderDetails['discount'])
            ->setSubtotal($newOrderDetails['subtotal'])
            ->setBaseSubtotal($newOrderDetails['subtotal'])
            ->setGrandTotal($newOrderDetails['total'])
            ->setBaseGrandTotal($newOrderDetails['total'])
            ->setFirstname($bFirstname)
            ->setLastname($bLastname);

        $quote->setBillingAddress($billing);
        $quote->setShippingAddress($shipping);

        $billing = $quote->getBillingAddress();
        $shipping = $quote->getShippingAddress();

        $convertQuote = Mage::getModel('sales/convert_quote');
        /* @var $convertQuote Mage_Sales_Model_Convert_Quote */
        $order = Mage::getModel('sales/order');
        /* @var $order Mage_Sales_Model_Order */

        $order = $convertQuote->addressToOrder($billing);

        // add payment information to order
        $order->setBillingAddress($convertQuote->addressToOrderAddress($billing))
            ->setShippingAddress($convertQuote->addressToOrderAddress($shipping));

        $order->setShippingMethod($shipping->getShippingMethod());
        $order->setShippingDescription($_shippingDesc);

        $order->setPayment($convertQuote->paymentToOrderPayment($quote->getPayment()));

        /**
         * Amazon Order Id
         */
        $order->setExtOrderId($newOrderDetails['amazonOrderID']);

        // add items to order
        foreach ($quote->getAllItems() as $item) {
            /* @var $item Mage_Sales_Model_Quote_Item */
            $order->addItem($convertQuote->itemToOrderItem($item));
            $orderItem = $order->getItemByQuoteItemId($item->getId());
            /* @var $orderItem Mage_Sales_Model_Order_Item */
            $orderItem->setExtOrderItemId($newOrderDetails['items'][$item->getId()]['AmazonOrderItemCode']);
            $orderItemOptions = $orderItem->getProductOptions();
            $orderItemOptions['amazon_amounts'] = serialize(array(
                'shipping' => $newOrderDetails['items'][$item->getId()]['shipping'],
                'tax' => $newOrderDetails['items'][$item->getId()]['tax'],
                'shipping_tax' => $newOrderDetails['items'][$item->getId()]['shipping_tax'],
                'principal_promo' => $newOrderDetails['items'][$item->getId()]['principal_promo'],
                'shipping_promo' => $newOrderDetails['items'][$item->getId()]['shipping_promo']
            ));
            $orderItem->setProductOptions($orderItemOptions);
        }

        $order->place();

        $customer = $quote->getCustomer();
        if (isset($customer) && $customer) { // && $quote->getCheckoutMethod()==Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER) {
            $order->setCustomerId($customer->getId())
                ->setCustomerEmail($customer->getEmail())
                ->setCustomerPrefix($customer->getPrefix())
                ->setCustomerFirstname($customer->getFirstname())
                ->setCustomerMiddlename($customer->getMiddlename())
                ->setCustomerLastname($customer->getLastname())
                ->setCustomerSuffix($customer->getSuffix())
                ->setCustomerGroupId($customer->getGroupId())
                ->setCustomerTaxClassId($customer->getTaxClassId());
        }

        $order->save();

        $quote->setIsActive(false);
        $quote->save();

        $orderId = $order->getIncrementId();
        $this->getCheckout()->setLastQuoteId($quote->getId());
        $this->getCheckout()->setLastSuccessQuoteId($quote->getId());
        $this->getCheckout()->setLastOrderId($order->getId());
        $this->getCheckout()->setLastRealOrderId($order->getIncrementId());

        $order->sendNewOrderEmail();
        return $this;
    }

    /**
     * Proccess existing order
     *
     * @param array $amazonOrderDetails
     * @return boolean
     */
    protected function _proccessOrder($amazonOrderDetails)
    {
        if ($quoteId = $newOrderDetails['ClientRequestId']) {
            if ($order = Mage::getModel('sales/order')->loadByAttribute('quote_id', $quoteId)) {
                /* @var $order Mage_Sales_Model_Order */

                $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING);
                $order->setStatus('Processing');
                $order->setIsNotified(false);
                $order->save();
            }
        }
        return true;
    }

    /**
     * Cancel the order
     *
     * @param array $amazonOrderDetails
     * @return boolean
     */
    protected function _cancelOrder($cancelDetails)
    {
        if (array_key_exists('amazon_order_id', $cancelDetails)) {
            $order = Mage::getModel('sales/order')
                ->loadByAttribute('ext_order_id', $cancelDetails['amazon_order_id']);
            /* @var $order Mage_Sales_Model_Order */
            if ($order->getId()) {
                try {
                    $order->cancel()->save();
                } catch (Exception $e) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Return xml with error
     *
     * @param Exception $e
     * @return string
     */

    public function callbackXmlError(Exception $e)
    {
        $_xml = $this->getApi()->callbackXmlError($e);
        $secretKeyID = Mage::getStoreConfig('payment/amazonpayments_cba/secretkey_id');
        $_signature = $this->getApi()->calculateSignature($_xml->asXml(), $secretKeyID);

        $response = 'order-calculations-response='.urlencode($_xml->asXML())
                .'&Signature='.urlencode($_signature)
                .'&aws-access-key-id='.urlencode(Mage::getStoreConfig('payment/amazonpayments_cba/accesskey_id'));
        return $response;
    }

    /**
     * Prepare fields for XML-based signed cart form for CBA
     *
     * @return array
     */
    public function getCheckoutXmlFormFields()
    {
        $secretKeyID = Mage::getStoreConfig('payment/amazonpayments_cba/secretkey_id');
        $_quote = $this->getCheckout()->getQuote();

        $xml = $this->getApi()->getXmlCart($_quote);

        $xmlCart = array('order-input' =>
            "type:merchant-signed-order/aws-accesskey/1;"
            ."order:".base64_encode($xml).";"
            ."signature:{$this->getApi()->calculateSignature($xml, $secretKeyID)};"
            ."aws-access-key-id:".Mage::getStoreConfig('payment/amazonpayments_cba/accesskey_id')
            );
        if ($this->getDebug()) {
            $debug = Mage::getModel('amazonpayments/api_debug')
                ->setResponseBody(print_r($xmlCart, 1)."\norder:".$xml)
                ->setRequestBody(time() .' - xml cart')
                ->save();
        }
        return $xmlCart;
    }

    /**
     * Return CBA order details in case Html-based shopping cart commited to Amazon
     *
     */
    public function returnAmazon()
    {
        $_request = Mage::app()->getRequest()->getParams();
        #$_amazonOrderId = Mage::app()->getRequest()->getParam('amznPmtsOrderIds');
        #$_quoteId = Mage::app()->getRequest()->getParam('amznPmtsReqId');

        if ($this->getDebug()) {
            $debug = Mage::getModel('amazonpayments/api_debug')
                ->setRequestBody(print_r($_request, 1))
                ->setResponseBody(time().' - success')
                ->save();
        }
    }

    /**
     * Rewrite standard logic
     *
     * @return bool
     */
    public function isInitializeNeeded()
    {
        return false;
    }

    /**
     * Get debug flag
     *
     * @return string
     */
    public function getDebug()
    {
        return Mage::getStoreConfig('payment/' . $this->getCode() . '/debug_flag');
    }
}