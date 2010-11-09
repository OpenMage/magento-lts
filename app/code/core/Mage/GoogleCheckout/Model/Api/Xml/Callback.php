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
 * @package     Mage_GoogleCheckout
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_GoogleCheckout_Model_Api_Xml_Callback extends Mage_GoogleCheckout_Model_Api_Xml_Abstract
{
    protected $_cachedShippingInfo = array(); // Cache of possible shipping carrier-methods combinations per storeId

    /**
     * Process notification from google
     * @return Mage_GoogleCheckout_Model_Api_Xml_Callback
     */
    public function process()
    {
        // Retrieve the XML sent in the HTTP POST request to the ResponseHandler
        $xmlResponse = isset($GLOBALS['HTTP_RAW_POST_DATA']) ?
            $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        if (get_magic_quotes_gpc()) {
            $xmlResponse = stripslashes($xmlResponse);
        }

        $debugData = array('request' => $xmlResponse, 'dir' => 'in');

        if (empty($xmlResponse)) {
            $this->getApi()->debugData($debugData);
            return false;
        }

        list($root, $data) = $this->getGResponse()->GetParsedXML($xmlResponse);

        $this->getGResponse()->SetMerchantAuthentication($this->getMerchantId(), $this->getMerchantKey());
        $status = $this->getGResponse()->HttpAuthentication();

        if (!$status || empty($data[$root])) {
            exit;
        }

        $this->setRootName($root)->setRoot($data[$root]);
        $serialNumber = $this->getData('root/serial-number');
        $this->getGResponse()->setSerialNumber($serialNumber);

        /*
         * Prevent multiple notification processing
         */
        $notification = Mage::getModel('googlecheckout/notification')
            ->setSerialNumber($serialNumber)
            ->loadNotificationData();

        if ($notification->getStartedAt()) {
            if ($notification->isProcessed()) {
                $this->getGResponse()->SendAck();
                return;
            }
            if ($notification->isTimeout()) {
                $notification->updateProcess();
            } else {
                $this->getGResponse()->SendServerErrorStatus();
                return;
            }
        } else {
            $notification->startProcess();
        }

        $method = '_response'.uc_words($root, '', '-');
        if (method_exists($this, $method)) {
            ob_start();

            try {
                $this->$method();
                $notification->stopProcess();
            } catch (Exception $e) {
                $this->getGResponse()->log->logError($e->__toString());
            }

            $debugData['result'] = ob_get_flush();
            $this->getApi()->debugData($debugData);
        } else {
            $this->getGResponse()->SendBadRequestStatus("Invalid or not supported Message");
        }

        return $this;
    }

    /**
     * Load quote from request and make sure the proper payment method is set
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _loadQuote()
    {
        $quoteId = $this->getData('root/shopping-cart/merchant-private-data/quote-id/VALUE');
        $storeId = $this->getData('root/shopping-cart/merchant-private-data/store-id/VALUE');
        $quote = Mage::getModel('sales/quote')
            ->setStoreId($storeId)
            ->load($quoteId);
        if ($quote->isVirtual()) {
            $quote->getBillingAddress()->setPaymentMethod('googlecheckout');
        } else {
            $quote->getShippingAddress()->setPaymentMethod('googlecheckout');
        }
        return $quote;
    }

    protected function _getApiUrl()
    {
        return null;
    }

    protected function getGoogleOrderNumber()
    {
        return $this->getData('root/google-order-number/VALUE');
    }

    protected function _responseRequestReceived()
    {

    }

    protected function _responseError()
    {

    }

    protected function _responseDiagnosis()
    {

    }

    protected function _responseCheckoutRedirect()
    {

    }

    /**
     * Calculate available shipping amounts and taxes
     */
    protected function _responseMerchantCalculationCallback()
    {
        $merchantCalculations = new GoogleMerchantCalculations($this->getCurrency());

        $quote = $this->_loadQuote();
        $storeId = $quote->getStoreId();

        $billingAddress = $quote->getBillingAddress();
        $address = $quote->getShippingAddress();

        $googleAddress = $this->getData('root/calculate/addresses/anonymous-address');

        $googleAddresses = array();
        if ( isset( $googleAddress['id'] ) ) {
            $googleAddresses[] = $googleAddress;
        } else {
            $googleAddresses = $googleAddress;
        }

        $methods = Mage::getStoreConfig('google/checkout_shipping_merchant/allowed_methods', $this->getStoreId());
        $methods = unserialize($methods);
        $limitCarrier = array();
        foreach ($methods['method'] as $method) {
            if ($method) {
                list($carrierCode, $methodCode) = explode('/', $method);
                $limitCarrier[$carrierCode] = $carrierCode;
            }
        }
        $limitCarrier = array_values($limitCarrier);

        foreach($googleAddresses as $googleAddress) {
            $addressId = $googleAddress['id'];
            $regionCode = $googleAddress['region']['VALUE'];
            $countryCode = $googleAddress['country-code']['VALUE'];
            $regionModel = Mage::getModel('directory/region')->loadByCode($regionCode, $countryCode);
            $regionId = $regionModel->getId();

            $address->setCountryId($countryCode)
                ->setRegion($regionCode)
                ->setRegionId($regionId)
                ->setCity($googleAddress['city']['VALUE'])
                ->setPostcode($googleAddress['postal-code']['VALUE'])
                ->setLimitCarrier($limitCarrier);
            $billingAddress->setCountryId($countryCode)
                ->setRegion($regionCode)
                ->setRegionId($regionId)
                ->setCity($googleAddress['city']['VALUE'])
                ->setPostcode($googleAddress['postal-code']['VALUE'])
                ->setLimitCarrier($limitCarrier);

            $billingAddress->collectTotals();

            $gRequestMethods = $this->getData('root/calculate/shipping/method');
            if ($gRequestMethods) {
                // Make stable format of $gRequestMethods for convenient usage
                if (array_key_exists('VALUE', $gRequestMethods)) {
                    $gRequestMethods = array($gRequestMethods);
                }

                // Form list of mapping Google method names to applicable address rates
                $rates = array();
                $address->setCollectShippingRates(true)
                    ->collectShippingRates();
                foreach ($address->getAllShippingRates() as $rate) {
                    if ($rate instanceof Mage_Shipping_Model_Rate_Result_Error) {
                        continue;
                    }
                    $methodName = sprintf('%s - %s', $rate->getCarrierTitle(), $rate->getMethodTitle());
                    $rates[$methodName] = $rate;
                }

                foreach ($gRequestMethods as $method) {
                    $result = new GoogleResult($addressId);
                    $methodName = $method['name'];

                    if (isset($rates[$methodName])) {
                        $rate = $rates[$methodName];

                        $address->setShippingMethod($rate->getCode())
                            ->setLimitCarrier($rate->getCarrier())
                            ->setCollectShippingRates(true)
                            ->collectTotals();
                        $shippingRate = $address->getBaseShippingAmount() - $address->getBaseShippingDiscountAmount();
                        $result->SetShippingDetails($methodName, $shippingRate, "true");

                        if ($this->getData('root/calculate/tax/VALUE') == 'true') {
                            $taxAmount = $address->getBaseTaxAmount();
                            $taxAmount += $billingAddress->getBaseTaxAmount();
                            $result->setTaxDetails($taxAmount);
                        }
                    } else {
                        $result->SetShippingDetails($methodName, 0, "false");
                    }
                    $merchantCalculations->AddResult($result);
                }

            } else if ($this->getData('root/calculate/tax/VALUE')=='true') {
                $address->setShippingMethod(null);
                $address->setCollectShippingRates(true)->collectTotals();
                $billingAddress->setCollectShippingRates(true)->collectTotals();

                $taxAmount = $address->getBaseTaxAmount();
                $taxAmount += $billingAddress->getBaseTaxAmount();

                $result = new GoogleResult($addressId);
                $result->setTaxDetails($taxAmount);
                $merchantCalculations->addResult($result);
            }
        }

        $this->getGResponse()->ProcessMerchantCalculations($merchantCalculations);
    }

    /**
     * Process new order creation notification from google.
     * Convert customer quote to order
     */
    protected function _responseNewOrderNotification()
    {
        $this->getGResponse()->SendAck();

        // LOOK FOR EXISTING ORDER TO AVOID DUPLICATES
        $orders = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('ext_order_id', $this->getGoogleOrderNumber());
        if (count($orders)) {
            return;
        }

        // IMPORT GOOGLE ORDER DATA INTO QUOTE
        /* @var $quote Mage_Sales_Model_Quote */
        $quote = $this->_loadQuote();
        $quote->setIsActive(true)->reserveOrderId();
        $storeId = $quote->getStoreId();

        Mage::app()->setCurrentStore(Mage::app()->getStore($storeId));
        if ($quote->getQuoteCurrencyCode() != $quote->getBaseCurrencyCode()) {
            Mage::app()->getStore()->setCurrentCurrencyCode($quote->getQuoteCurrencyCode());
        }

        $billing = $this->_importGoogleAddress($this->getData('root/buyer-billing-address'));
        $quote->setBillingAddress($billing);

        $shipping = $this->_importGoogleAddress($this->getData('root/buyer-shipping-address'));

        $quote->setShippingAddress($shipping);

        $this->_importGoogleTotals($quote->getShippingAddress());

        $quote->getPayment()->importData(array('method'=>'googlecheckout'));

        // CONVERT QUOTE TO ORDER
        $convertQuote = Mage::getSingleton('sales/convert_quote');

        /* @var $order Mage_Sales_Model_Order */
        $order = $convertQuote->toOrder($quote);

        if ($quote->isVirtual()) {
            $convertQuote->addressToOrder($quote->getBillingAddress(), $order);
        } else {
            $convertQuote->addressToOrder($quote->getShippingAddress(), $order);
        }

        $order->setExtOrderId($this->getGoogleOrderNumber());
        $order->setExtCustomerId($this->getData('root/buyer-id/VALUE'));

        if (!$order->getCustomerEmail()) {
            $order->setCustomerEmail($billing->getEmail())
                ->setCustomerPrefix($billing->getPrefix())
                ->setCustomerFirstname($billing->getFirstname())
                ->setCustomerMiddlename($billing->getMiddlename())
                ->setCustomerLastname($billing->getLastname())
                ->setCustomerSuffix($billing->getSuffix());
        }

        $order->setBillingAddress($convertQuote->addressToOrderAddress($quote->getBillingAddress()));

        if (!$quote->isVirtual()) {
            $order->setShippingAddress($convertQuote->addressToOrderAddress($quote->getShippingAddress()));
        }
        #$order->setPayment($convertQuote->paymentToOrderPayment($quote->getPayment()));

        foreach ($quote->getAllItems() as $item) {
            $orderItem = $convertQuote->itemToOrderItem($item);
            if ($item->getParentItem()) {
                $orderItem->setParentItem($order->getItemByQuoteItemId($item->getParentItem()->getId()));
            }
            $order->addItem($orderItem);
        }

        $payment = Mage::getModel('sales/order_payment')->setMethod('googlecheckout');
        $order->setPayment($payment);
        $order->setCanShipPartiallyItem(false);

        $emailAllowed = ($this->getData('root/buyer-marketing-preferences/email-allowed/VALUE')==='true');

        $emailStr = $emailAllowed ? $this->__('Yes') : $this->__('No');
        $message = $this->__('Google Order Number: %s', '<strong>'.$this->getGoogleOrderNumber()).'</strong><br />'.
            $this->__('Google Buyer ID: %s', '<strong>'.$this->getData('root/buyer-id/VALUE').'</strong><br />').
            $this->__('Is Buyer Willing to Receive Marketing Emails: %s', '<strong>' . $emailStr . '</strong>');

        $order->addStatusToHistory($order->getStatus(), $message);
        $order->place();
        $order->save();
        $order->sendNewOrderEmail();

        $quote->setIsActive(false)->save();

        if ($emailAllowed) {
            Mage::getModel('newsletter/subscriber')->subscribe($order->getCustomerEmail());
        }

        Mage::dispatchEvent('checkout_submit_all_after', array('order' => $order, 'quote' => $quote));

        $this->getGRequest()->SendMerchantOrderNumber($order->getExtOrderId(), $order->getIncrementId());
    }

    /**
     * Import address data from goole request to address object
     *
     * @param array | Varien_Object $gAddress
     * @param Varien_Object $qAddress
     * @return Varien_Object
     */
    protected function _importGoogleAddress($gAddress, Varien_Object $qAddress=null)
    {
        if (is_array($gAddress)) {
            $gAddress = new Varien_Object($gAddress);
        }

        if (!$qAddress) {
            $qAddress = Mage::getModel('sales/quote_address');
        }
        $nameArr = $gAddress->getData('structured-name');
        if ($nameArr) {
            $qAddress->setFirstname($nameArr['first-name']['VALUE'])
                ->setLastname($nameArr['last-name']['VALUE']);
        } else {
            $nameArr = explode(' ', $gAddress->getData('contact-name/VALUE'), 2);
            $qAddress->setFirstname($nameArr[0]);
            if (!empty($nameArr[1])) {
                $qAddress->setLastname($nameArr[1]);
            }
        }
        $region = Mage::getModel('directory/region')->loadByCode(
            $gAddress->getData('region/VALUE'),
            $gAddress->getData('country-code/VALUE')
        );

        $qAddress->setCompany($gAddress->getData('company-name/VALUE'))
            ->setEmail($gAddress->getData('email/VALUE'))
            ->setStreet(trim($gAddress->getData('address1/VALUE')."\n".$gAddress->getData('address2/VALUE')))
            ->setCity($gAddress->getData('city/VALUE'))
            ->setRegion($gAddress->getData('region/VALUE'))
            ->setRegionId($region->getId())
            ->setPostcode($gAddress->getData('postal-code/VALUE'))
            ->setCountryId($gAddress->getData('country-code/VALUE'))
            ->setTelephone($gAddress->getData('phone/VALUE'))
            ->setFax($gAddress->getData('fax/VALUE'));

        return $qAddress;
    }

    /**
     * Returns array of possible shipping methods combinations
     * Includes internal GoogleCheckout shipping methods, that can be created
     * after successful Google Checkout
     *
     * @return array
     */
    protected function _getShippingInfos($storeId = null)
    {
        $cacheKey = ($storeId === null) ? 'nofilter' : $storeId;
        if (!isset($this->_cachedShippingInfo[$cacheKey])) {
            /* @var $shipping Mage_Shipping_Model_Shipping */
            $shipping = Mage::getModel('shipping/shipping');
            $carriers = Mage::getStoreConfig('carriers', $storeId);
            $infos = array();

            foreach (array_keys($carriers) as $carrierCode) {
                $carrier = $shipping->getCarrierByCode($carrierCode);
                if (!$carrier) {
                    continue;
                }

                if ($carrierCode == 'googlecheckout') {
                    // Add info about internal google checkout methods
                    $methods = array_merge($carrier->getAllowedMethods(), $carrier->getInternallyAllowedMethods());
                    $carrierName = 'Google Checkout';
                } else {
                    $methods = $carrier->getAllowedMethods();
                    $carrierName = Mage::getStoreConfig('carriers/' . $carrierCode . '/title', $storeId);
                }

                foreach ($methods as $methodCode => $methodName) {
                    $code = $carrierCode . '_' . $methodCode;
                    $name = sprintf('%s - %s', $carrierName, $methodName);
                    $infos[$code] = array(
                        'code' => $code,
                        'name' => $name, // Internal name for google checkout api - to distinguish it in google requests
                        'carrier' => $carrierCode,
                        'carrier_title' => $carrierName,
                        'method' => $methodCode,
                        'method_title' => $methodName
                    );
                }
            }
            $this->_cachedShippingInfo[$cacheKey] = $infos;
        }

        return $this->_cachedShippingInfo[$cacheKey];
    }

    /**
     * Return shipping method code by shipping method name received from Google
     *
     * @param string $name
     * @param int|string|Mage_Core_Model_Store $storeId
     * @return string|false
     */
    protected function _getShippingMethodByName($name, $storeId = null)
    {
        $code = false;
        $infos = $this->_getShippingInfos($storeId);
        foreach ($infos as $info) {
            if ($info['name'] == $name) {
                $code = $info['code'];
                break;
            }
        }
        return $code;
    }

    /**
     * Creates rate by method code
     * Sets shipping rate's accurate description, titles and so on,
     * so it will get in order description properly
     *
     * @param string $code
     * @return Mage_Sales_Model_Quote_Address_Rate
     */
    protected function _createShippingRate($code, $storeId = null)
    {
        $rate = Mage::getModel('sales/quote_address_rate')
            ->setCode($code);

        $infos = $this->_getShippingInfos($storeId);
        if (isset($infos[$code])) {
            $info = $infos[$code];
            $rate->setCarrier($info['carrier'])
                ->setCarrierTitle($info['carrier_title'])
                ->setMethod($info['method'])
                ->setMethodTitle($info['method_title']);
        }

        return $rate;
    }

    /**
     * Import totals information from google request to quote address
     *
     * @param Varien_Object $qAddress
     */
    protected function _importGoogleTotals($qAddress)
    {
        $quote = $qAddress->getQuote();
        $qAddress->setTaxAmount(
            $this->_reCalculateToStoreCurrency($this->getData('root/order-adjustment/total-tax/VALUE'), $quote)
        );
        $qAddress->setBaseTaxAmount($this->getData('root/order-adjustment/total-tax/VALUE'));

        $method = null;
        $prefix = 'root/order-adjustment/shipping/';
        if (null !== ($shipping = $this->getData($prefix.'carrier-calculated-shipping-adjustment'))) {
            $method = 'googlecheckout_carrier';
        } else if (null !== ($shipping = $this->getData($prefix.'merchant-calculated-shipping-adjustment'))) {
            $method = $this->_getShippingMethodByName($shipping['shipping-name']['VALUE']);
            if ($method === false) {
                $method = 'googlecheckout_merchant';
            }
        } else if (null !== ($shipping = $this->getData($prefix.'flat-rate-shipping-adjustment'))) {
            $method = 'googlecheckout_flatrate';
        } else if (null !== ($shipping = $this->getData($prefix.'pickup-shipping-adjustment'))) {
            $method = 'googlecheckout_pickup';
        }

        if ($method) {
            Mage::getSingleton('tax/config')->setShippingPriceIncludeTax(false);
            $rate = $this->_createShippingRate($method)
                ->setPrice($shipping['shipping-cost']['VALUE']);
            $qAddress->addShippingRate($rate)
                ->setShippingMethod($method)
                ->setShippingDescription($shipping['shipping-name']['VALUE'])
                ->setShippingAmountForDiscount(0); // We get from Google price with discounts applied via merchant calculations

            /*if (!Mage::helper('tax')->shippingPriceIncludesTax($quote->getStore())) {
                $includingTax = Mage::helper('tax')->getShippingPrice($excludingTax, true, $qAddress, $quote->getCustomerTaxClassId());
                $shippingTax = $includingTax - $excludingTax;
                $qAddress->setShippingTaxAmount($this->_reCalculateToStoreCurrency($shippingTax, $quote))
                    ->setBaseShippingTaxAmount($shippingTax)
                    ->setShippingInclTax($includingTax)
                    ->setBaseShippingInclTax($this->_reCalculateToStoreCurrency($includingTax, $quote));
            } else {
                if ($method == 'googlecheckout_carrier') {
                    $qAddress->setShippingTaxAmount(0)
                        ->setBaseShippingTaxAmount(0);
                }
            }*/
        } else {
            $qAddress->setShippingMethod(null);
        }


        $qAddress->setGrandTotal(
            $this->_reCalculateToStoreCurrency($this->getData('root/order-total/VALUE'), $quote)
        );
        $qAddress->setBaseGrandTotal($this->getData('root/order-total/VALUE'));
    }

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->hasData('order')) {
            $order = Mage::getModel('sales/order')
                ->loadByAttribute('ext_order_id', $this->getGoogleOrderNumber());
            if (!$order->getId()) {
                Mage::throwException('Invalid Order: '.$this->getGoogleOrderNumber());
            }
            $this->setData('order', $order);
        }
        return $this->getData('order');
    }

    protected function _responseRiskInformationNotification()
    {
        $this->getGResponse()->SendAck();

        $order = $this->getOrder();
        $payment = $order->getPayment();

        $order
            ->setRemoteIp($this->getData('root/risk-information/ip-address/VALUE'));

        $payment
            ->setCcLast4($this->getData('root/risk-information/partial-cc-number/VALUE'))
            ->setCcAvsStatus($this->getData('root/risk-information/avs-response/VALUE'))
            ->setCcCidStatus($this->getData('root/risk-information/cvn-response/VALUE'));

        $msg = $this->__('Google Risk Information:');
        $msg .= '<br />'.$this->__('IP Address: %s', '<strong>'.$order->getRemoteIp().'</strong>');
        $msg .= '<br />'.$this->__('CC Partial: xxxx-%s', '<strong>'.$payment->getCcLast4().'</strong>');
        $msg .= '<br />'.$this->__('AVS Status: %s', '<strong>'.$payment->getCcAvsStatus().'</strong>');
        $msg .= '<br />'.$this->__('CID Status: %s', '<strong>'.$payment->getCcCidStatus().'</strong>');
        $msg .= '<br />'.$this->__('Eligible for Protection: %s', '<strong>'.($this->getData('root/risk-information/eligible-for-protection/VALUE')=='true' ? 'Yes' : 'No').'</strong>');
        $msg .= '<br />'.$this->__('Buyer Account Age: %s days', '<strong>'.$this->getData('root/risk-information/buyer-account-age/VALUE').'</strong>');

        $order->addStatusToHistory($order->getStatus(), $msg);
        $order->save();
    }

    /**
     * Process authorization notification
     */
    protected function _responseAuthorizationAmountNotification()
    {
        $this->getGResponse()->SendAck();

        $order = $this->getOrder();
        $payment = $order->getPayment();

        $payment->setAmountAuthorized($this->getData('root/authorization-amount/VALUE'));

        $expDate = $this->getData('root/authorization-expiration-date/VALUE');
        $expDate = new Zend_Date($expDate);
        $msg = $this->__('Google Authorization:');
        $msg .= '<br />'.$this->__('Amount: %s', '<strong>' . $this->_formatAmount($payment->getAmountAuthorized()) . '</strong>');
        $msg .= '<br />'.$this->__('Expiration: %s', '<strong>' . $expDate->toString() . '</strong>');

        $order->addStatusToHistory($order->getStatus(), $msg);

        $order->setPaymentAuthorizationAmount($payment->getAmountAuthorized());
        $order->setPaymentAuthorizationExpiration(Mage::getModel('core/date')->gmtTimestamp($this->getData('root/authorization-expiration-date/VALUE')));

        $order->save();
    }

    /**
     * Process charge notification
     *
     */
    protected function _responseChargeAmountNotification()
    {
        $this->getGResponse()->SendAck();

        $order = $this->getOrder();
        $payment = $order->getPayment();

        $latestCharged = $this->getData('root/latest-charge-amount/VALUE');
        $totalCharged = $this->getData('root/total-charge-amount/VALUE');
        $payment->setAmountCharged($totalCharged);
        $order->setIsInProcess(true);

        $msg = $this->__('Google Charge:');
        $msg .= '<br />'.$this->__('Latest Charge: %s', '<strong>' . $this->_formatAmount($latestCharged) . '</strong>');
        $msg .= '<br />'.$this->__('Total Charged: %s', '<strong>' . $this->_formatAmount($totalCharged) . '</strong>');

        if (!$order->hasInvoices() && abs($order->getBaseGrandTotal() - $latestCharged)<.0001) {
            $invoice = $this->_createInvoice();
            $msg .= '<br />'.$this->__('Invoice Auto-Created: %s', '<strong>'.$invoice->getIncrementId().'</strong>');
        }

        foreach ($order->getInvoiceCollection() as $orderInvoice) {
            $open = Mage_Sales_Model_Order_Invoice::STATE_OPEN;
            $paid = Mage_Sales_Model_Order_Invoice::STATE_PAID;
            if ($orderInvoice->getState() == $open && $orderInvoice->getBaseGrandTotal() == $latestCharged) {
                $orderInvoice->setState($paid)->save();
                break;
            }
        }

        $order->addStatusToHistory($order->getStatus(), $msg);
        $order->save();

    }

    protected function _createInvoice()
    {
        $order = $this->getOrder();

        $invoice = $order->prepareInvoice();
        $invoice->addComment(Mage::helper('googlecheckout')->__('Auto-generated from GoogleCheckout Charge'));
        $invoice->register();
        $invoice->pay();

        $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($invoice)
            ->addObject($invoice->getOrder());

        $transactionSave->save();

        return $invoice;
    }

    protected function _createShipment()
    {
        $order = $this->getOrder();
        $shipment = $order->prepareShipment();
        if ($shipment) {
            $shipment->register();

            $order->setIsInProcess(true);

            $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($shipment)
                ->addObject($shipment->getOrder())
                ->save();
        }

        return $shipment;
    }

    /**
     * Process chargeback notification
     */
    protected function _responseChargebackAmountNotification()
    {
        $this->getGResponse()->SendAck();

        $latestChargeback = $this->getData('root/latest-chargeback-amount/VALUE');
        $totalChargeback = $this->getData('root/total-chargeback-amount/VALUE');

        $order = $this->getOrder();
        if ($order->getBaseGrandTotal() == $totalChargeback) {
            $creditmemo = Mage::getModel('sales/service_order', $order)
                ->prepareCreditmemo()
                ->setPaymentRefundDisallowed(true)
                ->setAutomaticallyCreated(true)
                ->register();

            $creditmemo->addComment($this->__('Credit memo has been created automatically'));
            $creditmemo->save();
        }
        $msg = $this->__('Google Chargeback:');
        $msg .= '<br />'.$this->__('Latest Chargeback: %s', '<strong>' . $this->_formatAmount($latestChargeback) . '</strong>');
        $msg .= '<br />'.$this->__('Total Chargeback: %s', '<strong>' . $this->_formatAmount($totalChargeback) . '</strong>');

        $order->addStatusToHistory($order->getStatus(), $msg);
        $order->save();
    }

    /**
     * Process refund notification
     */
    protected function _responseRefundAmountNotification()
    {
        $this->getGResponse()->SendAck();

        $latestRefunded = $this->getData('root/latest-refund-amount/VALUE');
        $totalRefunded = $this->getData('root/total-refund-amount/VALUE');

        $order = $this->getOrder();
        $amountRefundLeft = $order->getBaseGrandTotal() - $order->getBaseTotalRefunded();
        if ($amountRefundLeft < $latestRefunded) {
            $latestRefunded = $amountRefundLeft;
            $totalRefunded  = $order->getBaseGrandTotal();
        }

        if ($order->getBaseTotalRefunded() > 0) {
            $adjustment = array('adjustment_positive' => $latestRefunded);
        } else {
            $adjustment = array('adjustment_negative' => $order->getBaseGrandTotal() - $latestRefunded);
        }

        $creditmemo = Mage::getModel('sales/service_order', $order)
            ->prepareCreditmemo($adjustment)
            ->setPaymentRefundDisallowed(true)
            ->setAutomaticallyCreated(true)
            ->register()
            ->addComment($this->__('Credit memo has been created automatically'))
            ->save();

        $msg = $this->__('Google Refund:');
        $msg .= '<br />'.$this->__('Latest Refund: %s', '<strong>' . $this->_formatAmount($latestRefunded) . '</strong>');
        $msg .= '<br />'.$this->__('Total Refunded: %s', '<strong>' . $this->_formatAmount($totalRefunded) . '</strong>');

        $order->addStatusToHistory($order->getStatus(), $msg);
        $order->save();
    }

    protected function _responseOrderStateChangeNotification()
    {
        $this->getGResponse()->SendAck();

        $prevFinancial = $this->getData('root/previous-financial-order-state/VALUE');
        $newFinancial = $this->getData('root/new-financial-order-state/VALUE');
        $prevFulfillment = $this->getData('root/previous-fulfillment-order-state/VALUE');
        $newFulfillment = $this->getData('root/new-fulfillment-order-state/VALUE');

        $msg = $this->__('Google Order Status Change:');
        if ($prevFinancial!=$newFinancial) {
            $msg .= "<br />".$this->__('Financial: %s -> %s', '<strong>'.$prevFinancial.'</strong>', '<strong>'.$newFinancial.'</strong>');
        }
        if ($prevFulfillment!=$newFulfillment) {
            $msg .= "<br />".$this->__('Fulfillment: %s -> %s', '<strong>'.$prevFulfillment.'</strong>', '<strong>'.$newFulfillment.'</strong>');
        }
        $this->getOrder()
            ->addStatusToHistory($this->getOrder()->getStatus(), $msg)
            ->save();

        $method = '_orderStateChangeFinancial'.uc_words(strtolower($newFinancial), '', '_');
        if (method_exists($this, $method)) {
            $this->$method();
        }

        $method = '_orderStateChangeFulfillment'.uc_words(strtolower($newFulfillment), '', '_');
        if (method_exists($this, $method)) {
            $this->$method();
        }
    }

    protected function _orderStateChangeFinancialReviewing()
    {

    }

    protected function _orderStateChangeFinancialChargeable()
    {
        #$this->getGRequest()->SendProcessOrder($this->getGoogleOrderNumber());
        #$this->getGRequest()->SendChargeOrder($this->getGoogleOrderNumber(), '');
    }

    protected function _orderStateChangeFinancialCharging()
    {

    }

    protected function _orderStateChangeFinancialCharged()
    {

    }

    protected function _orderStateChangeFinancialPaymentDeclined()
    {

    }

    protected function _orderStateChangeFinancialCancelled()
    {
        $this->getOrder()->setBeingCanceledFromGoogleApi(true)->cancel()->save();
    }

    protected function _orderStateChangeFinancialCancelledByGoogle()
    {
        $this->getOrder()->setBeingCanceledFromGoogleApi(true)->cancel()->save();
        $this->getGRequest()->SendBuyerMessage($this->getGoogleOrderNumber(), "Sorry, your order is cancelled by Google", true);
    }

    protected function _orderStateChangeFulfillmentNew()
    {

    }

    protected function _orderStateChangeFulfillmentProcessing()
    {

    }

    protected function _orderStateChangeFulfillmentDelivered()
    {
        $shipment = $this->_createShipment();
        if (!is_null($shipment))
            $shipment->save();
    }

    protected function _orderStateChangeFulfillmentWillNotDeliver()
    {

    }

    /**
     * Format amount to be displayed
     *
     * @param mixed $amount
     * @return string
     */
    protected function _formatAmount($amount)
    {
        // format currency in currency format, but don't enclose it into <span>
        return Mage::helper('core')->currency($amount, true, false);
    }

}
