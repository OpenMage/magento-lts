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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect checkout controller
 *
 * @category    Mage
 * @package     Mage_Xmlconnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_CheckoutController extends Mage_XmlConnect_Controller_Action
{
    /**
     * Make sure customer is logged in
     *
     * @return null
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::getSingleton('customer/session')->isLoggedIn()
            && !Mage::getSingleton('checkout/session')->getQuote()->isAllowedGuestCheckout()
        ) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            $this->_message($this->__('Customer not logged in.'), self::MESSAGE_STATUS_ERROR, array(
                'logged_in' => '0'
            ));
            return ;
        }
    }

    /**
     * Get one page checkout model
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }

    /**
     * Onepage Checkout page
     *
     * @return null
     */
    public function indexAction()
    {
        if ($this->_checkApiForward('addressmassaction', Mage_XmlConnect_Helper_Data::DEVICE_API_V_23)) {
            return;
        }
        if (!Mage::helper('checkout')->canOnepageCheckout()) {
            $this->_message($this->__('Onepage checkout is disabled.'), self::MESSAGE_STATUS_ERROR);
            return;
        }
        /** @var $quote Mage_Sales_Model_Quote */
        $quote = $this->getOnepage()->getQuote();
        if ($quote->getHasError()) {
            $this->_message($this->__('Cart has some errors.'), self::MESSAGE_STATUS_ERROR);
            return;
        } else if (!$quote->hasItems()) {
            $this->_message($this->__('Cart is empty.'), self::MESSAGE_STATUS_ERROR);
            return;
        } else if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            $this->_message($error, self::MESSAGE_STATUS_ERROR);
            return;
        }
        Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
        $this->getOnepage()->initCheckout();

        try {
            $this->loadLayout(false);
            $this->renderLayout();
        } catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        } catch (Exception $e) {
            $this->_message($this->__('Unable to load checkout.'), self::MESSAGE_STATUS_ERROR);
            Mage::logException($e);
        }
    }

    /**
     * Display customer new billing address form
     *
     * @return null
     */
    public function newBillingAddressFormAction()
    {
        try {
            $this->loadLayout(false);
            $this->renderLayout();
        } catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        } catch (Exception $e) {
            $this->_message($this->__('Unable to load billing address form.'), self::MESSAGE_STATUS_ERROR);
            Mage::logException($e);
        }
    }

    /**
     * Display customer new shipping address form
     *
     * @return null
     */
    public function newShippingAddressFormAction()
    {
        try {
            $this->loadLayout(false);
            $this->renderLayout();
        } catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        } catch (Exception $e) {
            $this->_message($this->__('Unable to load shipping address form.'), self::MESSAGE_STATUS_ERROR);
            Mage::logException($e);
        }
    }

    /**
     * Billing addresses list action
     *
     * @return null
     */
    public function billingAddressAction()
    {
        try {
            $this->loadLayout(false);
            $this->renderLayout();
        } catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        } catch (Exception $e) {
            $this->_message($this->__('Unable to load billing address.'), self::MESSAGE_STATUS_ERROR);
            Mage::logException($e);
        }
    }

    /**
     * Save billing address to current quote using onepage model
     *
     * @return null
     */
    public function saveBillingAddressAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_message($this->__('Specified invalid data.'), self::MESSAGE_STATUS_ERROR);
            return;
        }

        $data = $this->getRequest()->getPost('billing', array());
        $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
        if (isset($data['email'])) {
            $data['email'] = trim($data['email']);
        }
        $result = $this->getOnepage()->saveBilling($data, $customerAddressId);
        if (!isset($result['error'])) {
            $this->_message($this->__('Billing address has been set.'), self::MESSAGE_STATUS_SUCCESS);
        } else {
            if (!is_array($result['message'])) {
                $result['message'] = array($result['message']);
            }
            $this->_message(implode('. ', $result['message']), self::MESSAGE_STATUS_ERROR);
        }
    }

    /**
     * Shipping addresses list action
     *
     * @return null
     */
    public function shippingAddressAction()
    {
        try {
            $this->loadLayout(false);
            $this->renderLayout();
        } catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        } catch (Exception $e) {
            $this->_message($this->__('Unable to load billing address.'), self::MESSAGE_STATUS_ERROR);
            Mage::logException($e);
        }
    }

    /**
     * Save shipping address to current quote using onepage model
     *
     * @return null
     */
    public function saveShippingAddressAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_message($this->__('Specified invalid data.'), self::MESSAGE_STATUS_ERROR);
            return;
        }

        $data = $this->getRequest()->getPost('shipping', array());
        $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
        /**
         * For future use, please do not remove for now
         */
        $useForShipping = $this->getRequest()->getPost('use_for_shipping');

        $billingAddress = $this->getOnepage()->getQuote()->getBillingAddress();
        /**
         * Checking whether shipping address is the same with billing address?
         * This should be removed when mobile app will send just the 'use_for_shipping' flag
         */
        if (is_null($useForShipping)) {
            $useForShipping = $this->_checkUseForShipping($data, $billingAddress, $customerAddressId);
        }

        if ($useForShipping) {
            /**
             * Set address Id with the billing address Id
             */
            $customerAddressId = $billingAddress->getId();
            /**
             * Set flag of shipping address is same as billing address
             */
            $data['same_as_billing'] = true;
        }
        $result = $this->getOnepage()->saveShipping($data, $customerAddressId);
        if (!isset($result['error'])) {
            $this->_message($this->__('Shipping address has been set.'), self::MESSAGE_STATUS_SUCCESS);
        } else {
            if (!is_array($result['message'])) {
                $result['message'] = array($result['message']);
            }
            $this->_message(implode('. ', $result['message']), self::MESSAGE_STATUS_ERROR);
        }
    }

    /**
     * Checks the shipping address is equal with billing address
     *
     * ATTENTION!!!
     * It should be removed when mobile app will send just the 'use_for_shipping' flag
     * instead of send shipping address same as a billing address
     *
     * @todo Remove when mobile app will send just the 'use_for_shipping' flag
     * @param array $data
     * @param Mage_Sales_Model_Quote_Address $billingAddress
     * @param integer $shippingAddressId
     * @return bool
     */
    protected function _checkUseForShipping(array $data, $billingAddress, $shippingAddressId)
    {
        $useForShipping = !$shippingAddressId || $billingAddress->getId() == $shippingAddressId;

        if ($useForShipping) {
            foreach ($data as $key => $value) {
                if ($key == 'save_in_address_book') {
                    continue;
                }
                $billingData = $billingAddress->getDataUsingMethod($key);
                if (is_array($value) && is_array($billingData)) {
                    foreach ($value as $k => $v) {
                        if (!isset($billingData[$k]) || $billingData[$k] != trim($v)) {
                            $useForShipping = false;
                            break;
                        }
                    }
                } else {
                    if (is_string($value) && $billingData != trim($value)) {
                        $useForShipping = false;
                        break;
                    } else {
                        $useForShipping = false;
                        break;
                    }
                }
            }
        }
        return $useForShipping;
    }

    /**
     * Get shipping methods for current quote
     *
     * @return null
     */
    public function shippingMethodsAction()
    {
        try {
            $result = array('error' => $this->__('Error'));
            $this->getOnepage()->getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->getOnepage()->getQuote()->collectTotals()->save();
            $this->loadLayout(false);
            $this->renderLayout();
            return;
        } catch (Mage_Core_Exception $e) {
            $result['error'] = $e->getMessage();
        }
        $this->_message($result['error'], self::MESSAGE_STATUS_ERROR);
    }

    /**
     * Get shipping methods for current quote API v23
     *
     * @return null
     */
    public function shippingMethodsListAction()
    {
        try {
            $result = array('error' => $this->__('Error'));
            $this->getOnepage()->getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->getOnepage()->getQuote()->collectTotals()->save();
            $this->loadLayout(false);
            $this->renderLayout();
            return;
        } catch (Mage_Core_Exception $e) {
            $result['error'] = $e->getMessage();
        }
        $this->_message($result['error'], self::MESSAGE_STATUS_ERROR);
    }

    /**
     * Shipping method save action
     *
     * @return null
     */
    public function saveShippingMethodAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_message($this->__('Specified invalid data.'), self::MESSAGE_STATUS_ERROR);
            return;
        }

        $data = $this->getRequest()->getPost('shipping_method', '');
        $result = $this->getOnepage()->saveShippingMethod($data);
        if (!$result) {

            Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array(
                'request' => $this->getRequest(),
                'quote' => $this->getOnepage()->getQuote()
            ));
            $this->getOnepage()->getQuote()->collectTotals()->save();
            if ($this->_checkApiForward('paymentmethodlist', Mage_XmlConnect_Helper_Data::DEVICE_API_V_23)) {
                return;
            }
            $this->_message($this->__('Shipping method has been set.'), self::MESSAGE_STATUS_SUCCESS);
        } elseif (isset($result['error'])) {
            if (!is_array($result['message'])) {
                $result['message'] = array($result['message']);
            }
            Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array(
                'request' => $this->getRequest(),
                'quote' => $this->getOnepage()->getQuote()
            ));
            $this->getOnepage()->getQuote()->collectTotals()->save();
            $this->_message(implode('. ', $result['message']), self::MESSAGE_STATUS_ERROR);
        }
    }


    /**
     * Save checkout method
     *
     * @return null
     */
    public function saveMethodAction()
    {
        if ($this->getRequest()->isPost()) {
            $method = (string) $this->getRequest()->getPost('method');
            $result = $this->getOnepage()->saveCheckoutMethod($method);
            if (!isset($result['error'])) {
                $this->_message($this->__('Payment Method has been set.'), self::MESSAGE_STATUS_SUCCESS);
            } else {
                if (!is_array($result['message'])) {
                    $result['message'] = array($result['message']);
                }
                $this->_message(implode('. ', $result['message']), self::MESSAGE_STATUS_ERROR);
            }
        }
    }

    /**
     * Get payment methods action API v23
     *
     * @return null
     */
    public function paymentMethodListAction()
    {
        try {
            $this->loadLayout(false);
            /** @var $paymentMethodsBlock Mage_XmlConnect_Block_Checkout_Payment_Method_List */
            $paymentMethodsBlock = $this->getLayout()->getBlock('payment.methods');
            $response = $paymentMethodsBlock->toHtml();
            $this->getResponse()->setBody($response);
            return;
        } catch (Mage_Core_Exception $e) {
            $result['error'] = $e->getMessage();
        }
        $this->_message($result['error'], self::MESSAGE_STATUS_ERROR);
    }

    /**
     * Get payment methods action
     *
     * @return null
     */
    public function paymentMethodsAction()
    {
        if ($this->_checkApiForward('paymentmethodlist', Mage_XmlConnect_Helper_Data::DEVICE_API_V_23)) {
            return;
        }
        $this->paymentMethodListAction();
    }

    /**
     * Save payment action
     *
     * @return null
     */
    public function savePaymentAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_message($this->__('Specified invalid data.'), self::MESSAGE_STATUS_ERROR);
            return;
        }
        // set payment to quote
        $result = array();
        $data = $this->getRequest()->getPost('payment', array());
        try {
            $result = $this->getOnepage()->savePayment($data);
            if (empty($result['error'])) {
                $method = Mage::getSingleton('checkout/session')->getQuote()->getPayment()->getMethodInstance();
                $sentinelData = array();
                if ($method->getIsCentinelValidationEnabled()) {
                    $centinel = $method->getCentinelValidator();
                    if ($centinel && $centinel->shouldAuthenticate()) {
                        $sentinelData = array('sentinel_secure' => Mage::getUrl('*/cms/sentinelsecure'));
                    }
                }

                if ($this->_checkApiForward('ordersummary', Mage_XmlConnect_Helper_Data::DEVICE_API_V_23)) {
                    return;
                }
                $this->_message(
                    $this->__('Payment method was successfully set.'), self::MESSAGE_STATUS_SUCCESS, $sentinelData
                );
                return;
            }

        } catch (Mage_Payment_Exception $e) {
            $result['error'] = $e->getMessage();
            Mage::logException($e);
        } catch (Mage_Core_Exception $e) {
            $result['error'] = $e->getMessage();
            Mage::logException($e);
        } catch (Exception $e) {
            Mage::logException($e);
            $result['error'] = $e->getMessage();
        }
        $this->_message($result['error'], self::MESSAGE_STATUS_ERROR);
    }

    /**
     * Order review action
     *
     * @return null
     */
    public function orderReviewAction()
    {
        $this->getOnepage()->getQuote()->collectTotals()->save();
        try {
            $this->loadLayout(false);
            $this->renderLayout();
        } catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        } catch (Exception $e) {
            $this->_message($this->__('Unable to load order review.'), self::MESSAGE_STATUS_ERROR);
            Mage::logException($e);
        }
    }

    /**
     * Checkout order summary info action
     *
     * @return null
     */
    public function orderSummaryAction()
    {
        $this->getOnepage()->getQuote()->collectTotals()->save();
        try {
            $this->loadLayout(false);
            $this->renderLayout();
        } catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        } catch (Exception $e) {
            $this->_message($this->__('Unable to load order review.'), self::MESSAGE_STATUS_ERROR);
            Mage::logException($e);
        }
    }

    /**
     * Create order action
     *
     * @return null
     */
    public function saveOrderAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_message($this->__('Specified invalid data.'), self::MESSAGE_STATUS_ERROR);
            return;
        }

        try {
            $requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds();
            if (!empty($requiredAgreements)) {
                $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
                if (array_diff($requiredAgreements, $postedAgreements)) {
                    $error = $this->__('Please agree to all the terms and conditions before placing the order.');
                    $this->_message($error, self::MESSAGE_STATUS_ERROR);
                    return;
                }
            }
            $data = $this->getRequest()->getPost('payment', false);
            if ($data) {
                $this->getOnepage()->getQuote()->getPayment()->importData($data);
            }
            $this->getOnepage()->saveOrder();

            /** @var $message Mage_XmlConnect_Model_Simplexml_Element */
            $message = Mage::getModel('xmlconnect/simplexml_element', '<message></message>');
            $message->addChild('status', self::MESSAGE_STATUS_SUCCESS);

            $orderId = $this->getOnepage()->getLastOrderId();

            $text = $this->__('Thank you for your purchase! ');
            $text .= $this->__('Your order # is: %s. ', $orderId);
            $text .= $this->__('You will receive an order confirmation email with details of your order and a link to track its progress.');
            $message->addChild('text', $text);

            $message->addChild('order_id', $orderId);

            $this->getOnepage()->getQuote()->save();
            $this->getOnepage()->getCheckout()->clear();

            $this->getResponse()->setBody($message->asNiceXml());
            return;
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $error = $e->getMessage();
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $error = $this->__('An error occurred while processing your order. Please contact us or try again later.');
        }
        $this->getOnepage()->getQuote()->save();
        $this->_message($error, self::MESSAGE_STATUS_ERROR);
    }

    /**
     * Action return address form and customer saved addresses
     *
     * @return null
     */
    public function addressMassactionAction()
    {
        if (!Mage::helper('checkout')->canOnepageCheckout()) {
            $this->_message($this->__('Onepage checkout is disabled.'), self::MESSAGE_STATUS_ERROR);
            return;
        }
        try {
            /** @var $quote Mage_Sales_Model_Quote */
            $quote = $this->getOnepage()->getQuote();
            if ($quote->getHasError()) {
                $this->_message($this->__('Cart has some errors.'), self::MESSAGE_STATUS_ERROR);
                return;
            } elseif (!$quote->hasItems()) {
                $this->_message($this->__('Cart is empty.'), self::MESSAGE_STATUS_ERROR);
                return;
            } elseif (!$quote->validateMinimumAmount()) {
                $error = Mage::getStoreConfig('sales/minimum_order/error_message');
                $this->_message($error, self::MESSAGE_STATUS_ERROR);
                return;
            }
            Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
            $this->getOnepage()->initCheckout();

            $this->loadLayout(false);
            $this->renderLayout();
        } catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        } catch (Exception $e) {
            $this->_message($this->__('Unable to load addresses.'), self::MESSAGE_STATUS_ERROR);
            Mage::logException($e);
        }
    }

    /**
     * Action save customer addresses info
     *
     * @return null
     */
    public function saveAddressInfoAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_message($this->__('Specified invalid data.'), self::MESSAGE_STATUS_ERROR);
            return;
        }

        /** Save billing address */
        $useForShipping = false;
        $isVirtual = Mage::helper('checkout/cart')->getIsVirtualQuote();
        $billingAddressId = $this->getRequest()->getPost('billing_address_id', false);
        $billingData = $this->getRequest()->getPost('billing', array());
        if (isset($billingData['use_for_shipping']) && $billingData['use_for_shipping'] == 1) {
            $useForShipping = true;
        }
        try {
            if ($billingAddressId) {
                $billingData = Mage::getModel('customer/address')->load($billingAddressId)->getData();
            }
            if (isset($billingData['email'])) {
                $billingData['email'] = trim($billingData['email']);
            }
            $billingSaveResult = $this->getOnepage()->saveBilling($billingData, $billingAddressId);
            if (isset($billingSaveResult['error'])) {
                if (!is_array($billingSaveResult['message'])) {
                    $billingSaveResult['message'] = array($billingSaveResult['message']);
                }
                $this->_message(implode('. ', $billingSaveResult['message']), self::MESSAGE_STATUS_ERROR);
                return;
            }

            if (!$useForShipping && !$isVirtual) {
                $shippingAddressId = $this->getRequest()->getPost('shipping_address_id', false);
                if ($shippingAddressId) {
                    $shippingData = Mage::getModel('customer/address')->load($shippingAddressId)->getData();
                } else {
                    $shippingData = $this->getRequest()->getPost('shipping', array());
                }
                $shippingSaveResult = $this->getOnepage()->saveShipping($shippingData, $shippingAddressId);
                if (isset($shippingSaveResult['error'])) {
                    if (!is_array($shippingSaveResult['message'])) {
                        $shippingSaveResult['message'] = array($shippingSaveResult['message']);
                    }
                    $this->_message(implode('. ', $shippingSaveResult['message']), self::MESSAGE_STATUS_ERROR);
                    return;
                }
            } else {
                $result = $this->getOnepage()->saveShipping($billingData, $billingAddressId);
                if (isset($result['error'])) {
                    if (!is_array($result['message'])) {
                        $result['message'] = array($result['message']);
                    }
                    $this->_message(implode('. ', $result['message']), self::MESSAGE_STATUS_ERROR);
                    return;
                }
            }

            if ($isVirtual) {
                /** If quote is virtual - redirect to payment methods list */
                $this->_forward('paymentmethods', null, null, array(
                    Mage_XmlConnect_Helper_Data::API_VERSION_REQUEST_PARAM
                        => Mage_XmlConnect_Helper_Data::DEVICE_API_V_23
                ));
            } else {
                /** Redirect to shipping methods list */
                $this->_forward('shippingMethodsList');
            }
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
            Mage::logException($e);
        } catch (Exception $e) {
            $this->_message($this->__('Unable to load addresses.'), self::MESSAGE_STATUS_ERROR);
            Mage::logException($e);
        }
    }
}
