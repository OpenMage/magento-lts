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
 *
 * PayPal Standard Checkout Module
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
    //changing the payment to different from cc payment type and paypal payment type
    const PAYMENT_TYPE_AUTH = 'AUTHORIZATION';
    const PAYMENT_TYPE_SALE = 'SALE';

    protected $_code  = 'paypal_standard';
    protected $_formBlockType = 'paypal/standard_form';
    protected $_allowCurrencyCode = array('AUD', 'CAD', 'CHF', 'CZK', 'DKK', 'EUR', 'GBP', 'HKD', 'HUF', 'JPY', 'NOK', 'NZD', 'PLN', 'SEK', 'SGD','USD');

     /**
     * Get paypal session namespace
     *
     * @return Mage_Paypal_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('paypal/session');
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
     * Get current quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    /**
     * Using internal pages for input payment data
     *
     * @return bool
     */
    public function canUseInternal()
    {
        return false;
    }

    /**
     * Using for multiple shipping address
     *
     * @return bool
     */
    public function canUseForMultishipping()
    {
        return false;
    }

    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('paypal/standard_form', $name)
            ->setMethod('paypal_standard')
            ->setPayment($this->getPayment())
            ->setTemplate('paypal/standard/form.phtml');

        return $block;
    }

    /*validate the currency code is avaialable to use for paypal or not*/
    public function validate()
    {
        parent::validate();
        $currency_code = $this->getQuote()->getBaseCurrencyCode();
        if (!in_array($currency_code,$this->_allowCurrencyCode)) {
            Mage::throwException(Mage::helper('paypal')->__('Selected currency code ('.$currency_code.') is not compatabile with PayPal'));
        }
        return $this;
    }

    public function onOrderValidate(Mage_Sales_Model_Order_Payment $payment)
    {
       return $this;
    }

    public function onInvoiceCreate(Mage_Sales_Model_Invoice_Payment $payment)
    {

    }

    public function canCapture()
    {
        return true;
    }

    public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('paypal/standard/redirect', array('_secure' => true));
    }

    public function getStandardCheckoutFormFields()
    {
        $a = $this->getQuote()->getShippingAddress();
        //getQuoteCurrencyCode
        $currency_code = $this->getQuote()->getBaseCurrencyCode();
        /*
        //we validate currency before sending paypal so following code is obsolete

        if (!in_array($currency_code,$this->_allowCurrencyCode)) {
            //if currency code is not allowed currency code, use USD as default
            $storeCurrency = Mage::getSingleton('directory/currency')
                ->load($this->getQuote()->getStoreCurrencyCode());
            $amount = $storeCurrency->convert($amount, 'USD');
            $currency_code='USD';
        }
        */

        $sArr = array(
            'business'          => Mage::getStoreConfig('paypal/wps/business_account'),
            'return'            => Mage::getUrl('paypal/standard/success',array('_secure' => true)),
            'cancel_return'     => Mage::getUrl('paypal/standard/cancel',array('_secure' => false)),
            'notify_url'        => Mage::getUrl('paypal/standard/ipn'),
            'invoice'           => $this->getCheckout()->getLastRealOrderId(),
            'currency_code'     => $currency_code,
            'address_override'  => 1,
            'first_name'        => $a->getFirstname(),
            'last_name'         => $a->getLastname(),
            'address1'          => $a->getStreet(1),
            'address2'          => $a->getStreet(2),
            'city'              => $a->getCity(),
            'state'             => $a->getRegionCode(),
            'country'           => $a->getCountry(),
            'zip'               => $a->getPostcode(),
        );

        $logoUrl = Mage::getStoreConfig('paypal/wps/logo_url');
        if($logoUrl){
             $sArr = array_merge($sArr, array(
                  'cpp_header_image' => $logoUrl
             ));
        }

        if($this->getConfigData('payment_action')==self::PAYMENT_TYPE_AUTH){
             $sArr = array_merge($sArr, array(
                  'paymentaction' => 'authorization'
             ));
        }

        $transaciton_type = $this->getConfigData('transaction_type');
        /*
        O=aggregate cart amount to paypal
        I=individual items to paypal
        */
        if ($transaciton_type=='O') {
            $businessName = Mage::getStoreConfig('paypal/wps/business_name');
            $storeName = Mage::getStoreConfig('store/system/name');
            $amount = $a->getBaseSubtotal()-$a->getBaseDiscountAmount();
            $sArr = array_merge($sArr, array(
                    'cmd'           => '_ext-enter',
                    'redirect_cmd'  => '_xclick',
                    'item_name'     => $businessName ? $businessName : $storeName,
                    'amount'        => sprintf('%.2f', $amount),
                ));
            $tax = sprintf('%.2f', $this->getQuote()->getShippingAddress()->getBaseTaxAmount());
            if ($tax>0) {
                  $sArr = array_merge($sArr, array(
                        'tax' => $tax
                  ));
            }

        } else {
            $sArr = array_merge($sArr, array(
                'cmd'       => '_cart',
                'upload'       => '1',
            ));
            $items = $this->getQuote()->getAllItems();
            if ($items) {
                $i = 1;
                foreach($items as $item){
                    if ($item->getParentItem()) {
                        continue;
                    }
                    //echo "<pre>"; print_r($item->getData()); echo"</pre>";
                    $sArr = array_merge($sArr, array(
                        'item_name_'.$i      => $item->getName(),
                        'item_number_'.$i      => $item->getSku(),
                        'quantity_'.$i      => $item->getQty(),
                        'amount_'.$i      => ($item->getBaseCalculationPrice() - $item->getBaseDiscountAmount()),
                    ));
                    if($item->getBaseTaxAmount()>0){
                        $sArr = array_merge($sArr, array(
                        'tax_'.$i      => sprintf('%.2f',$item->getBaseTaxAmount()/$item->getQty()),
                        ));
                    }
                    $i++;
                }
           }
        }

        $totalArr = $a->getTotals();
        $shipping = sprintf('%.2f', $this->getQuote()->getShippingAddress()->getBaseShippingAmount());
        if ($shipping>0) {
          if ($transaciton_type=='O') {
              $sArr = array_merge($sArr, array(
                    'shipping' => $shipping
              ));
          } else {
              $sArr = array_merge($sArr, array(
                    'item_name_'.$i   => $totalArr['shipping']->getTitle(),
                    'quantity_'.$i    => 1,
                    'amount_'.$i      => $shipping,
              ));
              $i++;
          }
        }

        $sReq = '';
        $rArr = array();
        foreach ($sArr as $k=>$v) {
            /*
            replacing & char with and. otherwise it will break the post
            */
            $value =  str_replace("&","and",$v);
            $rArr[$k] =  $value;
            $sReq .= '&'.$k.'='.$value;
        }

        if ($this->getDebug() && $sReq) {
            $sReq = substr($sReq, 1);
            $debug = Mage::getModel('paypal/api_debug')
                    ->setApiEndpoint($this->getPaypalUrl())
                    ->setRequestBody($sReq)
                    ->save();
        }
        return $rArr;
    }

    public function getPaypalUrl()
    {
         if (Mage::getStoreConfig('paypal/wps/sandbox_flag')==1) {
             $url='https://www.sandbox.paypal.com/cgi-bin/webscr';
         } else {
             $url='https://www.paypal.com/cgi-bin/webscr';
         }
         return $url;
    }

    public function getDebug()
    {
        return Mage::getStoreConfig('paypal/wps/debug_flag');
    }


    public function ipnPostSubmit()
    {
        $sReq = '';
        foreach($this->getIpnFormData() as $k=>$v) {
            $sReq .= '&'.$k.'='.urlencode(stripslashes($v));
        }
        //append ipn commdn
        $sReq .= "&cmd=_notify-validate";
        $sReq = substr($sReq, 1);

        if ($this->getDebug()) {
            $debug = Mage::getModel('paypal/api_debug')
                    ->setApiEndpoint($this->getPaypalUrl())
                    ->setRequestBody($sReq)
                    ->save();
        }
        $http = new Varien_Http_Adapter_Curl();
        $http->write(Zend_Http_Client::POST,$this->getPaypalUrl(), '1.1', array(), $sReq);
        $response = $http->read();
        $response = preg_split('/^\r?$/m', $response, 2);
        $response = trim($response[1]);
        if ($this->getDebug()) {
            $debug->setResponseBody($response)->save();
        }

         //when verified need to convert order into invoice
        $id = $this->getIpnFormData('invoice');
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($id);

        if ($response=='VERIFIED') {
            if (!$order->getId()) {
                /*
                * need to have logic when there is no order with the order id from paypal
                */

            } else {

                if ($this->getIpnFormData('mc_gross')!=$order->getGrandTotal()) {
                    //when grand total does not equal, need to have some logic to take care
                    $order->addStatusToHistory(
                        $order->getStatus(),//continue setting current order status
                        Mage::helper('paypal')->__('Order total amount does not match paypal gross total amount')
                    );

                } else {
                    /*
                    //quote id
                    $quote_id = $order->getQuoteId();
                    //the customer close the browser or going back after submitting payment
                    //so the quote is still in session and need to clear the session
                    //and send email
                    if ($this->getQuote() && $this->getQuote()->getId()==$quote_id) {
                        $this->getCheckout()->clear();
                        $order->sendNewOrderEmail();
                    }
                    */
                    /*
                    if payer_status=verified ==> transaction in sale mode
                    if transactin in sale mode, we need to create an invoice
                    otherwise transaction in authorization mode
                    */
                    if ($this->getIpnFormData('payment_status')=='Completed') {
                       if (!$order->canInvoice()) {
                           //when order cannot create invoice, need to have some logic to take care
                           $order->addStatusToHistory(
                                $order->getStatus(),//continue setting current order status
                                Mage::helper('paypal')->__('Error in creating an invoice')
                           );

                       } else {
                           //need to save transaction id
                           $order->getPayment()->setTransactionId($this->getIpnFormData('txn_id'));
                           //need to convert from order into invoice
                           $invoice = $order->prepareInvoice();
                           $invoice->register()->capture();
                           Mage::getModel('core/resource_transaction')
                               ->addObject($invoice)
                               ->addObject($invoice->getOrder())
                               ->save();
                           $order->addStatusToHistory(
                                'processing',//update order status to processing after creating an invoice
                                Mage::helper('paypal')->__('Invoice '.$invoice->getIncrementId().' was created')
                           );
                       }
                    } else {
                        $order->addStatusToHistory(
                                $order->getStatus(),
                                Mage::helper('paypal')->__('Received IPN verification'));
                    }

                }//else amount the same and there is order obj
                //there are status added to order
                $order->save();
            }
        }else{
            /*
            Canceled_Reversal
            Completed
            Denied
            Expired
            Failed
            Pending
            Processed
            Refunded
            Reversed
            Voided
            */
            $payment_status= $this->getIpnFormData('payment_status');
            $comment = $payment_status;
            if ($payment_status == 'Pending') {
                $comment .= ' - ' . $this->getIpnFormData('pending_reason');
            } elseif ( ($payment_status == 'Reversed') || ($payment_status == 'Refunded') ) {
                $comment .= ' - ' . $this->getIpnFormData('reason_code');
            }
            //response error
            if (!$order->getId()) {
                /*
                * need to have logic when there is no order with the order id from paypal
                */
            } else {
                $order->addStatusToHistory(
                    $order->getStatus(),//continue setting current order status
                    Mage::helper('paypal')->__('Paypal IPN Invalid.'.$comment)
                );
                $order->save();
            }
        }
    }

}
