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
 * @package     Mage_Protx
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Protx Form Model
 *
 * @category   Mage
 * @package    Mage_Protx
 * @name       Mage_Protx_Model_Standard
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Protx_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'protx_standard';
    protected $_formBlockType = 'protx/standard_form';

    protected $_isGateway               = false;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

    protected $_order = null;


    /**
     * Get Config model
     *
     * @return object Mage_Protx_Model_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('protx/config');
    }

    /**
     * Return debug flag
     *
     *  @return  boolean
     */
    public function getDebug ()
    {
        return $this->getConfig()->getDebug();
    }

    /**
     *  Returns Target URL
     *
     *  @return	  string Target URL
     */
    public function getProtxUrl ()
    {
        switch ($this->getConfig()->getMode()) {
            case Mage_Protx_Model_Config::MODE_LIVE:
                $url = 'https://ukvps.protx.com/vspgateway/service/vspform-register.vsp';
                break;
            case Mage_Protx_Model_Config::MODE_TEST:
                $url = 'https://ukvpstest.protx.com/vspgateway/service/vspform-register.vsp';
                break;
            default: // simulator mode
                $url = 'https://ukvpstest.protx.com/VSPSimulator/VSPFormGateway.asp';
                break;
        }
        return $url;
    }

    /**
     *  Return URL for Protx success response
     *
     *  @return	  string URL
     */
    protected function getSuccessURL ()
    {
        return Mage::getUrl('protx/standard/successresponse');
    }

    /**
     *  Return URL for Protx failure response
     *
     *  @return	  string URL
     */
    protected function getFailureURL ()
    {
        return Mage::getUrl('protx/standard/failureresponse');
    }

    /**
     * Transaction unique ID sent to Protx and sent back by Protx for order restore
     * Using created order ID
     *
     *  @return	  string Transaction unique number
     */
    protected function getVendorTxCode ()
    {
        return $this->getOrder()->getRealOrderId();
    }

    /**
     *  Returns cart formatted
     *  String format:
     *  Number of lines:Name1:Quantity1:CostNoTax1:Tax1:CostTax1:Total1:Name2:Quantity2:CostNoTax2...
     *
     *  @return	  string Formatted cart items
     */
    protected function getFormattedCart ()
    {
        $items = $this->getOrder()->getAllItems();
        $resultParts = array();
        $totalLines = 0;
        if ($items) {
            foreach($items as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                $quantity = $item->getQtyOrdered();

                $cost = sprintf('%.2f', $item->getBasePrice() - $item->getBaseDiscountAmount());
                $tax = sprintf('%.2f', $item->getBaseTaxAmount());
                $costPlusTax = sprintf('%.2f', $cost + $tax/$quantity);

                $totalCostPlusTax = sprintf('%.2f', $quantity * $cost + $tax);

                $resultParts[] = str_replace(':', ' ', $item->getName());
                $resultParts[] = $quantity;
                $resultParts[] = $cost;
                $resultParts[] = $tax;
                $resultParts[] = $costPlusTax;
                $resultParts[] = $totalCostPlusTax;
                $totalLines++; //counting actual formatted items
            }
       }

       // add delivery
       $shipping = $this->getOrder()->getBaseShippingAmount();
       if ((int)$shipping > 0) {
           $totalLines++;
           $resultParts = array_merge($resultParts, array('Shipping','','','','',sprintf('%.2f', $shipping)));
       }

       $result = $totalLines . ':' . implode(':', $resultParts);
       return $result;
    }

    /**
     *  Format Crypted string with all order data for request to Protx
     *
     *  @return	  string Crypted string
     */
    protected function getCrypted ()
    {
        $order = $this->getOrder();
        if (!($order instanceof Mage_Sales_Model_Order)) {
            Mage::throwException($this->_getHelper()->__('Cannot retrieve order object'));
        }

        $shipping = $order->getShippingAddress();
        $billing = $order->getBillingAddress();

        $amount = $order->getBaseGrandTotal();

        $currency = $order->getBaseCurrencyCode();

        $queryPairs = array();

        $transactionId = $this->getVendorTxCode();
        $queryPairs['VendorTxCode'] = $transactionId;


        $queryPairs['Amount'] = sprintf('%.2f', $amount);
        $queryPairs['Currency'] = $currency;

        // Up to 100 chars of free format description
        $description = $this->getConfig()->getDescription() != ''
                       ? $this->getConfig()->getDescription()
                       : Mage::app()->getStore()->getName() . ' ' . ' payment';
        $queryPairs['Description'] = $description;

        $queryPairs['SuccessURL'] = $this->getSuccessURL();
        $queryPairs['FailureURL'] = $this->getFailureURL();

        $queryPairs['CustomerName'] = $billing->getFirstname().' '.$billing->getLastname();
        $queryPairs['CustomerEMail'] = $order->getCustomerEmail();
        $queryPairs['ContactNumber'] = $billing->getTelephone();
        $queryPairs['ContactFax'] = $billing->getFax();

        if ($this->getConfig()->getVendorNotification()) {
            $queryPairs['VendorEMail'] = $this->getConfig()->getVendorEmail();
        } else {
            $queryPairs['VendorEMail'] = '';
        }

        $queryPairs['eMailMessage'] = '';

        $queryPairs['BillingAddress'] = $billing->format('oneline');
        $queryPairs['BillingPostCode'] = $billing->getPostcode();

        if ($shipping) {
            $queryPairs['DeliveryAddress'] = $shipping->getFormated();
            $queryPairs['DeliveryPostCode'] = $shipping->getPostcode();
        } else {
            $queryPairs['DeliveryAddress'] = '';
            $queryPairs['DeliveryPostCode'] = '';
        }

        $queryPairs['Basket'] = $this->getFormattedCart();

        // For charities registered for Gift Aid
        $queryPairs['AllowGiftAid'] = '0';

        /**
         * Allow fine control over AVS/CV2 checks and rules by changing this value. 0 is Default
         * It can be changed dynamically, per transaction, if you wish.  See the VSP Server Protocol document
         */
        if ($this->getConfig()->getPaymentType() !== Mage_Protx_Model_Config::PAYMENT_TYPE_AUTHENTICATE) {
            $queryPairs['ApplyAVSCV2'] = '0';
        }

        /**
         * Allow fine control over 3D-Secure checks and rules by changing this value. 0 is Default
         * It can be changed dynamically, per transaction, if you wish.  See the VSP Server Protocol document
         */
        $queryPairs['Apply3DSecure'] = '0';

        if ($this->getDebug()) {
            Mage::getModel('protx/api_debug')
                ->setRequestBody($this->getProtxUrl()."\n".print_r($queryPairs,1))
                ->save();
        }

        // Encrypt the plaintext string for inclusion in the hidden field
        $result = $this->arrayToCrypt($queryPairs);
        return $result;
    }

    /**
     *  Form block description
     *
     *  @return	 object
     */
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('protx/form_standard', $name);
        $block->setMethod($this->_code);
        $block->setPayment($this->getPayment());
        return $block;
    }

    /**
     *  Return Order Place Redirect URL
     *
     *  @return	  string Order Redirect URL
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('protx/standard/redirect');
    }

    /**
     *  Return encrypted string with simple XOR algorithm
     *
     *  @param    string String to be encrypted
     *  @return	  string Encrypted string
     */
    protected function simpleXOR ($string)
    {
        $result = '';
        $cryptKey = $this->getConfig()->getCryptKey();

        if (!$cryptKey) {
            return $string;
        }

        // Initialise key array
        $keyList = array();

        // Convert $cryptKey into array of ASCII values
        for($i = 0; $i < strlen($cryptKey); $i++){
            $keyList[$i] = ord(substr($cryptKey, $i, 1));
        }

        // Step through string a character at a time
        for($i = 0; $i < strlen($string); $i++) {
            /**
             * Get ASCII code from string, get ASCII code from key (loop through with MOD),
             * XOR the two, get the character from the result
             * % is MOD (modulus), ^ is XOR
             */
            $result .= chr(ord(substr($string, $i, 1)) ^ ($keyList[$i % strlen($cryptKey)]));
        }
        return $result;
    }

    /**
     *  Extract possible response values into array from query string
     *
     *  @param    string Query string i.e. var1=value1&var2=value3...
     *  @return	  array
     */
    protected function getToken($queryString) {

        // List the possible tokens
        $Tokens = array(
                        "Status",
                        "StatusDetail",
                        "VendorTxCode",
                        "VPSTxId",
                        "TxAuthNo",
                        "Amount",
                        "AVSCV2",
                        "AddressResult",
                        "PostCodeResult",
                        "CV2Result",
                        "GiftAid",
                        "3DSecureStatus",
                        "CAVV"
                        );

        // Initialise arrays
        $output = array();
        $resultArray = array();

        // Get the next token in the sequence
        $c = count($Tokens);
        for ($i = $c - 1; $i >= 0 ; $i--){
            // Find the position in the string
            $start = strpos($queryString, $Tokens[$i]);
            // If it's present
            if ($start !== false){
                // Record position and token name
                $resultArray[$i]['start'] = $start;
                $resultArray[$i]['token'] = $Tokens[$i];
            }
        }

        // Sort in order of position
        sort($resultArray);

        // Go through the result array, getting the token values
        $c = count($resultArray);
        for ($i = 0; $i < $c; $i++){
            // Get the start point of the value
            $valueStart = $resultArray[$i]['start'] + strlen($resultArray[$i]['token']) + 1;
            // Get the length of the value
            if ($i == $c-1) {
                $output[$resultArray[$i]['token']] = substr($queryString, $valueStart);
            } else {
                $valueLength = $resultArray[$i+1]['start'] - $resultArray[$i]['start'] - strlen($resultArray[$i]['token']) - 2;
                $output[$resultArray[$i]['token']] = substr($queryString, $valueStart, $valueLength);
            }

        }

        return $output;
    }

    /**
     *  Convert array (key => value, key => value, ...) to crypt string
     *
     *  @param    array Array to be converted
     *  @return	  string Crypt string
     */
    public function arrayToCrypt ($array)
    {
        $parts = array();
        if (is_array($array)) {
            foreach ($array as $k => $v) {
                $parts[] = $k . '=' . $v;
            }
        }
        $result = implode('&', $parts);
        $result = $this->simpleXOR($result);
        $result = $this->base64Encode($result);
        return $result;
    }

    /**
     *  Reverse arrayToCrypt
     *
     *  @param    string Crypt string
     *  @return	  array
     */
    public function cryptToArray ($crypted)
    {
        $decoded = $this->base64Decode($crypted);
        $uncrypted = $this->simpleXOR($decoded);
        $tokens = $this->getToken($uncrypted);
        return $tokens;
    }

    /**
     *  Custom base64_encode()
     *
     *  @param    String
     *  @return	  String
     */
    protected function base64Encode($plain)
    {
        return base64_encode($plain);
    }

    /**
     *  Custom base64_decode()
     *
     *  @param    String
     *  @return	  String
     */
    protected function base64Decode($scrambled)
    {
        // Fix plus to space conversion issue
        $scrambled = str_replace(" ","+",$scrambled);
        return base64_decode($scrambled);
    }

    /**
     *  Return Standard Checkout Form Fields for request to Protx
     *
     *  @return	  array Array of hidden form fields
     */
    public function getStandardCheckoutFormFields ()
    {
        $fields = array(
                        'VPSProtocol'       => $this->getConfig()->getVersion(),
                        'TxType'            => $this->getConfig()->getPaymentType(),
                        'Vendor'            => $this->getConfig()->getVendorName(),
                        'Crypt'             => $this->getCrypted()
                        );
        return $fields;
    }
}
