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
 * @package     Mage_Ideal
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * iDEAL Basic Checkout Model
 *
 * @category    Mage_Ideal
 * @package     Mage_Ideal
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Ideal_Model_Basic extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'ideal_basic';
    protected $_formBlockType = 'ideal/basic_form';
    protected $_allowCurrencyCode = array('EUR', 'GBP', 'USD', 'CAD', 'SHR', 'NOK', 'SEK', 'DKK');

    protected $_isGateway               = false;
    protected $_canAuthorize            = false;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

    /**
     * Get debug flag
     *
     * @return boolean
     */
    public function getDebug()
    {
        return $this->getConfigData('debug_flag');
    }

    /**
     * validate the currency code is avaialable to use for iDEAL Basic or not
     *
     * @return bool
     */
    public function validate()
    {
        parent::validate();
        $paymentInfo = $this->getInfoInstance();
        if ($paymentInfo instanceof Mage_Sales_Model_Order_Payment) {
            $currency_code = $paymentInfo->getOrder()->getBaseCurrencyCode();
        } else {
            $currency_code = $paymentInfo->getQuote()->getBaseCurrencyCode();
        }
        if (!in_array($currency_code, $this->_allowCurrencyCode)) {
            Mage::throwException(Mage::helper('ideal')->__('Selected currency code (%s) is not compatible with iDEAL', $currency_code));
        }
        return $this;
    }

    public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('ideal/basic/redirect', array('_secure' => true));
    }

    /**
     * Return iDEAL Basic Api Url
     *
     * @return string Payment API URL
     */
    public function getApiUrl()
    {
        if ($this->getConfigData('test_flag') == 1) {
            if (($url = trim($this->getConfigData('api_test_url'))) == '') {
                $url = "https://idealtest.secure-ing.com/ideal/mpiPayInitIng.do";
            }
        } else {
            if (($url = trim($this->getConfigData('api_url'))) == '') {
                $url = "https://ideal.secure-ing.com/ideal/mpiPayInitIng.do";
            }
        }
        return $url;
    }

    /**
     * Generates array of fields for redirect form
     *
     * @return array
     */
    public function getBasicCheckoutFormFields()
    {
        $order = $this->getInfoInstance()->getOrder();

        $shippingAddress = $order->getShippingAddress();
        $currency_code = $order->getBaseCurrencyCode();

        $fields = array(
            'merchantID' => $this->getConfigData('merchant_id'),
            'subID' => '0',
            'amount' => round($order->getBaseGrandTotal()*100),
            'purchaseID' => $order->getIncrementId(),
            'paymentType' => 'ideal',
            'validUntil' => date('Y-m-d\TH:i:s.000\Z', strtotime ('+1 week')) // plus 1 week
        );

        $i = 1;
        foreach ($order->getItemsCollection() as $item) {
            $fields = array_merge($fields, array(
                "itemNumber".$i => $item->getSku(),
                "itemDescription".$i => $item->getName(),
                "itemQuantity".$i => $item->getQtyOrdered()*1,
                "itemPrice".$i => round($item->getBasePrice()*100)
            ));
            $i++;
        }

        if ($order->getBaseShippingAmount() > 0) {
            $fields = array_merge($fields, array(
                "itemNumber".$i => $order->getShippingMethod(),
                "itemDescription".$i => $order->getShippingDescription(),
                "itemQuantity".$i => 1,
                "itemPrice".$i => round($order->getBaseShippingAmount()*100)
            ));
            $i++;
        }

        if ($order->getBaseTaxAmount() > 0) {
            $fields = array_merge($fields, array(
                "itemNumber".$i => 'Tax',
                "itemDescription".$i => '',
                "itemQuantity".$i => 1,
                "itemPrice".$i => round($order->getBaseTaxAmount()*100)
            ));
            $i++;
        }

        if ($order->getBaseDiscountAmount() > 0) {
            $fields = array_merge($fields, array(
                "itemNumber".$i => 'Discount',
                "itemDescription".$i => '',
                "itemQuantity".$i => 1,
                "itemPrice".$i => -round($order->getBaseDiscountAmount()*100)
            ));
            $i++;
        }

        $fields = $this->appendHash($fields);

        $description = $this->getConfigData('description');
        if ($description == '') {
            $description = Mage::app()->getStore()->getName() . ' ' . 'payment';
        }

        $fields = array_merge($fields, array(
            'language' => $this->getConfigData('language'),
            'currency' => $currency_code,
            'description' => $description,
            'urlCancel' => Mage::getUrl('ideal/basic/cancel', array('_secure' => true)),
            'urlSuccess' => Mage::getUrl('ideal/basic/success', array('_secure' => true)),
            'urlError' => Mage::getUrl('ideal/basic/failure', array('_secure' => true))
        ));

        $requestString = '';
        $returnArray = array();

        foreach ($fields as $k=>$v) {
            $returnArray[$k] =  $v;
            $requestString .= '&'.$k.'='.$v;
        }

        if ($this->getDebug()) {
            Mage::getModel('ideal/api_debug')
                ->setRequestBody($this->getApiUrl() . "\n" . $requestString)
                ->save();
        }

        return $returnArray;
    }

    /**
     * Calculates and appends hash to form fields
     *
     * @param array $returnArray
     * @return array
     */
    public function appendHash($returnArray)
    {
        $merchantKey = $this->getConfigData('merchant_key');
        $hashString = $merchantKey.implode('', $returnArray);
        $hashString = str_replace(
            array(" ", "\t", "\n", "&amp;", "&lt;", "&gt;", "&quote;"),
            array("", "", "", "&", "<", ">", "\""),
            $hashString);
        $hash = sha1($hashString);
        return array_merge($returnArray, array('hash' => $hash));
    }
}
