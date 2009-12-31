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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_GoogleCheckout_Model_Api_Xml_Callback extends Mage_GoogleCheckout_Model_Api_Xml_Abstract
{
    public function process()
    {
        // Retrieve the XML sent in the HTTP POST request to the ResponseHandler
        $xmlResponse = isset($GLOBALS['HTTP_RAW_POST_DATA']) ?
            $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        if (get_magic_quotes_gpc()) {
            $xmlResponse = stripslashes($xmlResponse);
        }

        #$this->log($xmlResponse);
        $debug = Mage::getModel('googlecheckout/api_debug')->setDir('in')
            ->setUrl('process')
            ->setRequestBody($xmlResponse)
            ->save();

        if (empty($xmlResponse)) {
            return false;
        }

        list($root, $data) = $this->getGResponse()->GetParsedXML($xmlResponse);

        $debug->setUrl($root)->save();

        $this->getGResponse()->SetMerchantAuthentication($this->getMerchantId(), $this->getMerchantKey());
        $status = $this->getGResponse()->HttpAuthentication();

        if (!$status || empty($data[$root])) {
            exit;
        }

        $this->setRootName($root)->setRoot($data[$root]);

        $this->getGResponse()->setSerialNumber($this->getData('root/serial-number'));

        $method = '_response'.uc_words($root, '', '-');
        if (method_exists($this, $method)) {
            ob_start();

            try {
                $this->$method();
            } catch (Exception $e) {
                $this->getGResponse()->log->logError($e->getMessage());
            }

            $response = ob_get_flush();
            #$this->log($response);
            $debug->setResponseBody($response)->save();
        } else {
            $this->getGResponse()->SendBadRequestStatus("Invalid or not supported Message");
        }

        return $this;
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

    protected function _responseMerchantCalculationCallback()
    {
        $merchantCalculations = new GoogleMerchantCalculations($this->getCurrency());

        $quoteId = $this->getData('root/shopping-cart/merchant-private-data/quote-id/VALUE');
        $quote = Mage::getModel('sales/quote')->load($quoteId);

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
                $limitCarrier[] = $carrierCode;
            }
        }

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

            $address->setCollectShippingRates(true)->collectShippingRates();

            if ($gRequestMethods = $this->getData('root/calculate/shipping/method')) {
                $carriers = array();
                $errors = array();
                foreach (Mage::getStoreConfig('carriers', $this->getStoreId()) as $carrierCode=>$carrierConfig) {
                    if (!isset($carrierConfig['title'])) {
                        continue;
                    }
                    $title = $carrierConfig['title'];
                    foreach ($gRequestMethods as $method) {
                        $methodName = is_array($method) ? $method['name'] : $method;
                        if ($title && $method && strpos($methodName, $title)===0) {
                            $carriers[$carrierCode] = $title;
                            $errors[$title] = true;
                        }
                    }
                }

                $result = Mage::getModel('shipping/shipping')
                    ->collectRatesByAddress($address, array_keys($carriers))
                    ->getResult();

                $rates = array();
                $rateCodes = array();
                foreach ($result->getAllRates() as $rate) {
                    if ($rate instanceof Mage_Shipping_Model_Rate_Result_Error) {
                        $errors[$rate->getCarrierTitle()] = 1;
                    } else {
                        $k = $rate->getCarrierTitle().' - '.$rate->getMethodTitle();

                        if ($address->getFreeShipping()) {
                            $price = 0;
                        } else {
                            $price = $rate->getPrice();
                        }

                        if ($price) {
                            $price = Mage::helper('tax')->getShippingPrice($price, false, $address);
                        }

                        $rates[$k] = $price;
                        $rateCodes[$k] = $rate->getCarrier() . '_' . $rate->getMethod();
                        unset($errors[$rate->getCarrierTitle()]);
                    }
                }

                foreach ($gRequestMethods as $method) {
                    $methodName = is_array($method) ? $method['name'] : $method;
                    $result = new GoogleResult($addressId);

                    if (!empty($errors)) {
                        $continue = false;
                        foreach ($errors as $carrier=>$dummy) {
                            if (strpos($methodName, $carrier)===0) {
                                $result->SetShippingDetails($methodName, 0, "false");
                                $merchantCalculations->AddResult($result);
                                $continue = true;
                                break;
                            }
                        }
                        if ($continue) {
                            continue;
                        }
                    }

                    if (isset($rates[$methodName])) {
                        if ($this->getData('root/calculate/tax/VALUE')=='true') {
                            $address->setShippingMethod($rateCodes[$methodName]);

                            $address->setCollectShippingRates(true)->collectTotals();
                            $billingAddress->setCollectShippingRates(true)->collectTotals();

                            $taxAmount = $address->getBaseTaxAmount();
                            $taxAmount += $billingAddress->getBaseTaxAmount();

                            $result->setTaxDetails($taxAmount);
                        }

                        $result->SetShippingDetails($methodName, $rates[$methodName], "true");
                        $merchantCalculations->AddResult($result);
                    }
                }
            } elseif ($this->getData('root/calculate/tax/VALUE')=='true') {
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

        $quoteId = $this->getData('root/shopping-cart/merchant-private-data/quote-id/VALUE');
        $quote = Mage::getModel('sales/quote')->load($quoteId)->setIsActive(true);
//
//        $quoteItems = $quote->getItemsCollection();
//        foreach ($this->getData('root/shopping-cart/items') as $item) {
//            if (!isset($item['merchant-private-item-data']['quote-item-id']['VALUE'])
//                || !isset($item['quantity']['VALUE'])) {
//                continue;
//            }
//            $quoteItem = $quoteItems->getItemById($item['merchant-private-item-data']['quote-item-id']['VALUE']);
//            $quoteItem->setQty($item['quantity']['VALUE']);
//        }

        $billing = $this->_importGoogleAddress($this->getData('root/buyer-billing-address'));
        $quote->setBillingAddress($billing);

        $shipping = $this->_importGoogleAddress($this->getData('root/buyer-shipping-address'));
        $quote->setShippingAddress($shipping);

        $quote->collectTotals();

        $this->_importGoogleTotals($quote->getShippingAddress());

        // CONVERT QUOTE TO ORDER

        $convertQuote = Mage::getSingleton('sales/convert_quote');

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
                ->setCustomerSuffix($billing->getSuffix())
            ;
        }

        $order->setBillingAddress($convertQuote->addressToOrderAddress($quote->getBillingAddress()));
        if (!$quote->isVirtual()) {
            $order->setShippingAddress($convertQuote->addressToOrderAddress($quote->getShippingAddress()));
        }
        #$order->setPayment($convertQuote->paymentToOrderPayment($quote->getPayment()));

        foreach ($quote->getAllItems() as $item) {
            $order->addItem($convertQuote->itemToOrderItem($item));
        }

        $payment = Mage::getModel('sales/order_payment')->setMethod('googlecheckout');
        $order->setPayment($payment);
        $order->setCanShipPartiallyItem(false);

        $emailAllowed = ($this->getData('root/buyer-marketing-preferences/email-allowed/VALUE')==='true');

        $order->addStatusToHistory(
            $order->getStatus(),
            $this->__('Google Order Number: %s', '<strong>'.$this->getGoogleOrderNumber()).'</strong>'.
            '<br />'.
            $this->__('Google Buyer Id: %s', '<strong>'.$this->getData('root/buyer-id/VALUE').'</strong>').
            '<br />'.
            $this->__('Is Buyer Willing To Receive Marketing E-Mails: %s', '<strong>' . ($emailAllowed ? $this->__('Yes') : $this->__('No')) . '</strong>')
        );

//        $order->setCustomerNote(
//            $this->__('Google Order Number: %s', '<strong>'.$this->getGoogleOrderNumber()).'</strong>'.
//            '<br />'.
//            $this->__('Google Buyer Id: %s', '<strong>'.$this->getData('root/buyer-id/VALUE').'</strong>').
//            '<br />'.
//            $this->__('Is Buyer Willing To Receive Marketing E-Mails: %s', '<strong>' . ($emailAllowed ? $this->__('Yes') : $this->__('No')) . '</strong>')
//        );

#ob_start(array($this, 'log'));

        $order->place();
        $order->save();
#$this->log(ob_get_clean());

        $order->sendNewOrderEmail();

        Mage::getSingleton('checkout/session')
            ->setLastQuoteId($quote->getId())
            ->setLastOrderId($order->getId())
            ->setLastSuccessQuoteId($quote->getId())
            ->setLastRealOrderId($order->getIncrementId());

        $quote->setIsActive(false)->save();

        if ($emailAllowed) {
            Mage::getModel('newsletter/subscriber')->subscribe($order->getCustomerEmail());
        }

        $shoppingCartQuoteId = Mage::getSingleton('checkout/session')->getQuoteId();
        $tmpQuote = Mage::getModel('sales/quote')->load($shoppingCartQuoteId);
/*
        if (!$tmpQuote->getIsChanged()) {
            $tmpQuote->delete();
        }
*/
        $this->getGRequest()->SendMerchantOrderNumber($order->getExtOrderId(), $order->getIncrementId());
    }

    protected function _importGoogleAddress($gAddress, Varien_Object $qAddress=null)
    {
        if (is_array($gAddress)) {
            $gAddress = new Varien_Object($gAddress);
        }

        if (!$qAddress) {
            $qAddress = Mage::getModel('sales/quote_address');
        }

        if ($nameArr = $gAddress->getData('structured-name')) {
            $qAddress
                ->setFirstname($nameArr['first-name']['VALUE'])
                ->setLastname($nameArr['last-name']['VALUE']);
        } else {
            $nameArr = explode(' ', $gAddress->getData('contact-name/VALUE'), 2);
            $qAddress->setFirstname($nameArr[0]);
            if (!empty($nameArr[1])) {
                $qAddress->setLastname($nameArr[1]);
            }
        }
        $region = Mage::getModel('directory/region')->loadByCode($gAddress->getData('region/VALUE'), $gAddress->getData('country-code/VALUE'));

        $qAddress
            ->setCompany($gAddress->getData('company-name/VALUE'))
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

    protected function _importGoogleTotals($qAddress)
    {
        $qAddress->setTaxAmount(
            $this->_reCalculateToStoreCurrency(
                $this->getData('root/order-adjustment/total-tax/VALUE'), $qAddress->getQuote()
            )
        );
        $qAddress->setBaseTaxAmount($this->getData('root/order-adjustment/total-tax/VALUE'));

        $prefix = 'root/order-adjustment/shipping/';
        if ($shipping = $this->getData($prefix.'carrier-calculated-shipping-adjustment')) {
            $method = 'googlecheckout_carrier';
        } elseif ($shipping = $this->getData($prefix.'merchant-calculated-shipping-adjustment')) {
            $method = 'googlecheckout_merchant';
        } elseif ($shipping = $this->getData($prefix.'flat-rate-shipping-adjustment')) {
            $method = 'googlecheckout_flatrate';
        } elseif ($shipping = $this->getData($prefix.'pickup-shipping-adjustment')) {
            $method = 'googlecheckout_pickup';
        }
        if (!empty($method)) {
            $excludingTax = $shipping['shipping-cost']['VALUE'];
            $qAddress->setShippingMethod($method)
                ->setShippingDescription($shipping['shipping-name']['VALUE'])
                ->setShippingAmount(
                    $this->_reCalculateToStoreCurrency($excludingTax, $qAddress->getQuote()),
                    true
                )
                ->setBaseShippingAmount($excludingTax, true);

            if (!Mage::helper('tax')->shippingPriceIncludesTax()) {
                $includingTax = Mage::helper('tax')->getShippingPrice($excludingTax, true, $qAddress, $qAddress->getQuote()->getCustomerTaxClassId());
                $shippingTax = $includingTax - $excludingTax;
                $qAddress->setShippingTaxAmount(
                    $this->_reCalculateToStoreCurrency($shippingTax, $qAddress->getQuote())
                    )
                    ->setBaseShippingTaxAmount($shippingTax);
            } else {
                if ($method == 'googlecheckout_carrier') {
                    $qAddress->setShippingTaxAmount(0)
                        ->setBaseShippingTaxAmount(0);
                }
            }
        } else {
            $qAddress->setShippingMethod(null);
        }


        $qAddress->setGrandTotal(
            $this->_reCalculateToStoreCurrency(
                $this->getData('root/order-total/VALUE'), $qAddress->getQuote()
            )
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
        $msg .= '<br />'.$this->__('Eligible for protection: %s', '<strong>'.($this->getData('root/risk-information/eligible-for-protection/VALUE')=='true' ? 'Yes' : 'No').'</strong>');
        $msg .= '<br />'.$this->__('Buyer account age: %s days', '<strong>'.$this->getData('root/risk-information/buyer-account-age/VALUE').'</strong>');

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

        if (!$order->hasInvoices() && abs($order->getGrandTotal()-$latestCharged)<.0001) {
            $invoice = $this->_createInvoice();
            $msg .= '<br />'.$this->__('Invoice auto-created: %s', '<strong>'.$invoice->getIncrementId().'</strong>');
        }

        foreach ($order->getInvoiceCollection() as $orderInvoice) {
            $open = Mage_Sales_Model_Order_Invoice::STATE_OPEN;
            $paid = Mage_Sales_Model_Order_Invoice::STATE_PAID;
            if ($orderInvoice->getState() == $open && $orderInvoice->getGrandTotal() == $latestCharged) {
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

        if (!empty($data['comment_text'])) {
            $invoice->addComment(Mage::helper('googlecheckout')->__('Auto-generated from GoogleCheckout Charge'));
        }

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


    protected function _responseChargebackAmountNotification()
    {
        $this->getGResponse()->SendAck();

    }

    /**
     * Process refund notification
     *
     */
    protected function _responseRefundAmountNotification()
    {
        $this->getGResponse()->SendAck();

        $order = $this->getOrder();
        $payment = $order->getPayment();

        $totalRefunded = $this->getData('root/total-refund-amount/VALUE');
        $payment->setAmountCharged($totalRefunded);

        $msg = $this->__('Google Refund:');
        $msg .= '<br />'.$this->__('Latest Refund: %s', '<strong>' . $this->_formatAmount($this->getData('root/latest-refund-amount/VALUE')) . '</strong>');
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

        $msg = $this->__('Google order status change:');
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
