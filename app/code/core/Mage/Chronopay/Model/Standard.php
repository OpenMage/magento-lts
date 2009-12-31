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
 * @package     Mage_Chronopay
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Chronopay Standard Model
 *
 * @category   Mage
 * @package    Mage_Chronopay
 * @name       Mage_Chronopay_Model_Standard
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Chronopay_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'chronopay_standard';
    protected $_formBlockType = 'chronopay/standard_form';

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
     * @return object Mage_Chronopay_Model_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('chronopay/config');
    }

    /**
     * Payment validation
     *
     * @param   none
     * @return  Mage_Chronopay_Model_Standard
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
        if ($currency_code != $this->getConfig()->getCurrency()) {
            Mage::throwException(Mage::helper('chronopay')->__('Selected currency code ('.$currency_code.') is not compatible with ChronoPay'));
        }
        return $this;
    }

    /**
     * Capture payment
     *
     * @param   Varien_Object $orderPayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function capture (Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
            ->setLastTransId($this->getTransactionId());

        return $this;
    }

    /**
     *  Returns Target URL
     *
     *  @return	  string Target URL
     */
    public function getChronopayUrl ()
    {
        return 'https://secure.chronopay.com/index_shop.cgi';
    }

    /**
     *  Return URL for Chronopay success response
     *
     *  @return	  string URL
     */
    protected function getSuccessURL ()
    {
        return Mage::getUrl('chronopay/standard/success', array('_secure' => true));
    }

    /**
     *  Return URL for Chronopay notification
     *
     *  @return	  string Notification URL
     */
    protected function getNotificationURL ()
    {
        return Mage::getUrl('chronopay/standard/notify', array('_secure' => true));
    }

    /**
     *  Return URL for Chronopay failure response
     *
     *  @return	  string URL
     */
    protected function getFailureURL ()
    {
        return Mage::getUrl('chronopay/standard/failure', array('_secure' => true));
    }

    /**
     *  Form block description
     *
     *  @return	 object
     */
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('chronopay/form_standard', $name);
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
        return Mage::getUrl('chronopay/standard/redirect');
    }

    /**
     *  Return Standard Checkout Form Fields for request to Chronopay
     *
     *  @return	  array Array of hidden form fields
     */
    public function getStandardCheckoutFormFields ()
    {
        $order = $this->getOrder();
        if (!($order instanceof Mage_Sales_Model_Order)) {
            Mage::throwException($this->_getHelper()->__('Cannot retrieve order object'));
        }

        $billingAddress = $order->getBillingAddress();

        $streets = $billingAddress->getStreet();
        $street = isset($streets[0]) && $streets[0] != ''
                  ? $streets[0]
                  : (isset($streets[1]) && $streets[1] != '' ? $streets[1] : '');

        if ($this->getConfig()->getDescription()) {
            $transDescription = $this->getConfig()->getDescription();
        } else {
            $transDescription = Mage::helper('chronopay')->__('Order #%s', $order->getRealOrderId());
        }

        if ($order->getCustomerEmail()) {
            $email = $order->getCustomerEmail();
        } elseif ($billingAddress->getEmail()) {
            $email = $billingAddress->getEmail();
        } else {
            $email = '';
        }

        $fields = array(
                        'product_id'       => $this->getConfig()->getProductId(),
                        'product_name'     => $transDescription,
                        'product_price'    => $order->getBaseGrandTotal(),
                        'language'         => $this->getConfig()->getLanguage(),
                        'f_name'           => $billingAddress->getFirstname(),
                        's_name'           => $billingAddress->getLastname(),
                        'street'           => $street,
                        'city'             => $billingAddress->getCity(),
                        'state'            => $billingAddress->getRegionModel()->getCode(),
                        'zip'              => $billingAddress->getPostcode(),
                        'country'          => $billingAddress->getCountryModel()->getIso3Code(),
                        'phone'            => $billingAddress->getTelephone(),
                        'email'            => $email,
                        'cb_url'           => $this->getNotificationURL(),
                        'cb_type'          => 'P', // POST method used (G - GET method)
                        'decline_url'      => $this->getFailureURL(),
                        'cs1'              => Mage::helper('core')->encrypt($order->getRealOrderId())
                        );

        if ($this->getConfig()->getDebug()) {
            $debug = Mage::getModel('chronopay/api_debug')
                ->setRequestBody($this->getChronopayUrl()."\n".print_r($fields,1))
                ->save();
            $fields['cs2'] = $debug->getId();
        }

        return $fields;
    }

    /**
     *  Validate Response from ChronoPay
     *
     *  @param    array Post data returned from ChronoPay
     *  @return	  mixed
     */
    public function validateResponse ($data)
    {
        $order = $this->getOrder();

        if (!($order instanceof Mage_Sales_Model_Order)) {
            Mage::throwException($this->_getHelper()->__('Cannot retrieve order object'));
        }

        try {
            $ok = is_array($data)
                && isset($data['transaction_type']) && $data['transaction_type'] != ''
                && isset($data['customer_id']) && $data['customer_id'] != ''
                && isset($data['site_id']) && $data['site_id'] != ''
                && isset($data['product_id']) && $data['product_id'] != '';

            if (!$ok) {
                throw new Exception('Cannot restore order or invalid order ID');
            }

            // validate site ID
            if ($this->getConfig()->getSiteId() != $data['site_id']) {
                throw new Exception('Invalid site ID');
            }

            // validate product ID
            if ($this->getConfig()->getProductId() != $data['product_id']) {
                throw new Exception('Invalid product ID');
            }

            // Successful transaction type
            if (!in_array($data['transaction_type'], array('initial', 'onetime'))) {
                throw new Exception('Transaction is not successful');
            }

        } catch (Exception $e) {
            return $e;
        }
    }
}
