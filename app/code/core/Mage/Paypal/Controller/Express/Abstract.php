<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Abstract Express Checkout Controller
 *
 * @package    Mage_Paypal
 */
abstract class Mage_Paypal_Controller_Express_Abstract extends Mage_Core_Controller_Front_Action
{
    /**
     * Registry key that returnAction() sets after checking the return token, and placeOrderAction() consumes
     */
    private const VERIFIED_RETURN_REGISTRY_KEY = 'paypal_express_verified_return';

    /**
     * @var Mage_Paypal_Model_Express_Checkout
     */
    protected $_checkout = null;

    /**
     * @var Mage_Paypal_Model_Config
     */
    protected $_config = null;

    /**
     * @var false|Mage_Sales_Model_Quote
     */
    protected $_quote = false;

    /**
     * @var string
     */
    protected $_checkoutType;

    /**
     * @var string
     */
    protected $_configMethod;

    /**
     * @var string
     */
    protected $_configType;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        /** @var Mage_Paypal_Model_Config $classInstance */
        $classInstance = Mage::getModel($this->_configType, [$this->_configMethod]);
        $this->_config = $classInstance;
    }

    /**
     * Start Express Checkout by requesting initial token and dispatching customer to PayPal
     */
    public function startAction()
    {
        try {
            $this->_initCheckout();

            if ($this->_getQuote()->getIsMultiShipping()) {
                $this->_getQuote()->setIsMultiShipping(false);
                $this->_getQuote()->removeAllAddresses();
            }

            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $quoteCheckoutMethod = $this->_getQuote()->getCheckoutMethod();
            if ($customer && $customer->getId()) {
                $this->_checkout->setCustomerWithAddressChange(
                    $customer,
                    $this->_getQuote()->getBillingAddress(),
                    $this->_getQuote()->getShippingAddress(),
                );
            } elseif ((!$quoteCheckoutMethod
                || $quoteCheckoutMethod != Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER)
                && !Mage::helper('checkout')->isAllowedGuestCheckout(
                    $this->_getQuote(),
                    $this->_getQuote()->getStoreId(),
                )
            ) {
                Mage::getSingleton('core/session')->addNotice(
                    Mage::helper('paypal')->__('To proceed to Checkout, please log in using your email address.'),
                );
                $this->redirectLogin();
                Mage::getSingleton('customer/session')
                    ->setBeforeAuthUrl(Mage::getUrl('*/*/*', ['_current' => true]));
                return;
            }

            // billing agreement
            $isBARequested = (bool) $this->getRequest()
                ->getParam(Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_BILLING_AGREEMENT);
            if ($customer && $customer->getId()) {
                $this->_checkout->setIsBillingAgreementRequested($isBARequested);
            }

            // Bill Me Later
            $this->_checkout->setIsBml((bool) $this->getRequest()->getParam('bml'));

            // giropay
            $this->_checkout->prepareGiropayUrls(
                Mage::getUrl('checkout/onepage/success'),
                Mage::getUrl('paypal/express/cancel'),
                Mage::getUrl('checkout/onepage/success'),
            );

            $button = (bool) $this->getRequest()->getParam(Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_BUTTON);
            $token = $this->_checkout->start(Mage::getUrl('*/*/return'), Mage::getUrl('*/*/cancel'), $button);
            if ($token && $url = $this->_checkout->getRedirectUrl()) {
                $this->_initToken($token);
                $this->getResponse()->setRedirect($url);
                return;
            }
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_getCheckoutSession()->addError($mageCoreException->getMessage());
        } catch (Exception $exception) {
            $this->_getCheckoutSession()->addError($this->__('Unable to start Express Checkout.'));
            Mage::logException($exception);
        }

        $this->_redirect('checkout/cart');
    }

    /**
     * Return shipping options items for shipping address from request
     */
    public function shippingOptionsCallbackAction()
    {
        try {
            $quoteId = $this->getRequest()->getParam('quote_id');
            $this->_quote = Mage::getModel('sales/quote')->load($quoteId);
            $this->_initCheckout();
            $response = $this->_checkout->getShippingOptionsCallbackResponse($this->getRequest()->getParams());
            $this->getResponse()->setBody($response);
        } catch (Exception $exception) {
            Mage::logException($exception);
        }
    }

    /**
     * Cancel Express Checkout
     */
    public function cancelAction()
    {
        try {
            $this->_initToken(false);
            // TODO verify if this logic of order cancellation is deprecated
            // if there is an order - cancel it
            $orderId = $this->_getCheckoutSession()->getLastOrderId();
            $order = ($orderId) ? Mage::getModel('sales/order')->load($orderId) : false;
            if ($order && $order->getId() && $order->getQuoteId() == $this->_getCheckoutSession()->getQuoteId()) {
                $order->cancel()->save();
                $this->_getCheckoutSession()
                    ->unsLastQuoteId()
                    ->unsLastSuccessQuoteId()
                    ->unsLastOrderId()
                    ->unsLastRealOrderId()
                    ->addSuccess($this->__('Express Checkout and Order have been canceled.'))
                ;
            } else {
                $this->_getCheckoutSession()->addSuccess($this->__('Express Checkout has been canceled.'));
            }
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_getCheckoutSession()->addError($mageCoreException->getMessage());
        } catch (Exception $exception) {
            $this->_getCheckoutSession()->addError($this->__('Unable to cancel Express Checkout.'));
            Mage::logException($exception);
        }

        $this->_redirect('checkout/cart');
    }

    /**
     * Return from PayPal and dispatch customer to order review page
     */
    public function returnAction()
    {
        if ($this->getRequest()->getParam('retry_authorization') == 'true'
            && is_array($this->_getCheckoutSession()->getPaypalTransactionData())
        ) {
            // Without the token the retry is not placed here; the review page places it with the form key
            if ($this->_isValidReturnToken()) {
                $this->_forwardVerifiedReturn();
            } else {
                $this->_redirect('*/*/review');
            }

            return;
        }

        try {
            // A return without the matching token must not import the payer's details or place the order
            if (!$this->_isValidReturnToken()) {
                Mage::throwException($this->__('Wrong PayPal Express Checkout Token specified.'));
            }

            $this->_getCheckoutSession()->unsPaypalTransactionData();
            $this->_checkout = $this->_initCheckout();
            $this->_checkout->returnFromPaypal($this->_initToken());

            if ($this->_checkout->canSkipOrderReviewStep()) {
                $this->_forwardVerifiedReturn();
            } else {
                $this->_redirect('*/*/review');
            }

            return;
        } catch (Mage_Core_Exception $mageCoreException) {
            Mage::getSingleton('checkout/session')->addError($mageCoreException->getMessage());
        } catch (Exception $exception) {
            Mage::getSingleton('checkout/session')->addError($this->__('Unable to process Express Checkout approval.'));
            Mage::logException($exception);
        }

        $this->_redirect('checkout/cart');
    }

    /**
     * Review order after returning from PayPal
     */
    public function reviewAction()
    {
        try {
            $this->_initCheckout();
            $this->_checkout->prepareOrderReview($this->_initToken());
            $this->loadLayout();
            $this->_initLayoutMessages('paypal/session');
            $reviewBlock = $this->getLayout()->getBlock('paypal.express.review');
            $reviewBlock->setQuote($this->_getQuote());
            $reviewBlock->getChild('details')->setQuote($this->_getQuote());
            if ($reviewBlock->getChild('shipping_method')) {
                $reviewBlock->getChild('shipping_method')->setQuote($this->_getQuote());
            }

            $this->renderLayout();
            return;
        } catch (Mage_Core_Exception $mageCoreException) {
            Mage::getSingleton('checkout/session')->addError($mageCoreException->getMessage());
        } catch (Exception $exception) {
            Mage::getSingleton('checkout/session')->addError(
                $this->__('Unable to initialize Express Checkout review.'),
            );
            Mage::logException($exception);
        }

        $this->_redirect('checkout/cart');
    }

    /**
     * Dispatch customer back to PayPal for editing payment information
     */
    public function editAction()
    {
        try {
            $this->getResponse()->setRedirect($this->_config->getExpressCheckoutEditUrl($this->_initToken()));
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_getSession()->addError($mageCoreException->getMessage());
            $this->_redirect('*/*/review');
        }
    }

    /**
     * Update shipping method (combined action for ajax and regular request)
     */
    public function saveShippingMethodAction()
    {
        try {
            $isAjax = $this->getRequest()->getParam('isAjax');
            if (!$this->_validateFormKey()) {
                Mage::throwException($this->__('Invalid form key. Please refresh the page.'));
            }

            $this->_initCheckout();
            $this->_checkout->updateShippingMethod($this->getRequest()->getParam('shipping_method'));
            if ($isAjax) {
                $this->loadLayout('paypal_express_review_details');
                $this->getResponse()->setBody($this->getLayout()->getBlock('root')
                    ->setQuote($this->_getQuote())
                    ->toHtml());
                return;
            }
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_getSession()->addError($mageCoreException->getMessage());
        } catch (Exception $exception) {
            $this->_getSession()->addError($this->__('Unable to update shipping method.'));
            Mage::logException($exception);
        }

        if (isset($isAjax) && $isAjax) {
            $this->getResponse()->setBody('<script type="text/javascript">window.location.href = '
                . Mage::helper('core')->jsonEncode(Mage::getUrl('*/*/review')) . ';</script>');
        } else {
            $this->_redirect('*/*/review');
        }
    }

    /**
     * Update Order (combined action for ajax and regular request)
     */
    public function updateShippingMethodsAction()
    {
        try {
            $this->_initCheckout();
            $this->_checkout->prepareOrderReview($this->_initToken());
            $this->loadLayout('paypal_express_review');

            $this->getResponse()->setBody($this->getLayout()->getBlock('express.review.shipping.method')
                ->setQuote($this->_getQuote())
                ->toHtml());
            return;
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_getSession()->addError($mageCoreException->getMessage());
        } catch (Exception $exception) {
            $this->_getSession()->addError($this->__('Unable to update shipping method.'));
            Mage::logException($exception);
        }

        $this->getResponse()->setBody('<script type="text/javascript">window.location.href = '
            . Mage::helper('core')->jsonEncode(Mage::getUrl('*/*/review')) . ';</script>');
    }

    /**
     * Submit the order
     *
     * Requires a POST with the form key, except when returnAction() checked the Express Checkout
     * token of the PayPal return and forwarded here in the same request.
     */
    public function placeOrderAction()
    {
        if (!$this->_consumeVerifiedReturn()
            && (!$this->getRequest()->isPost() || !$this->_validateFormKey())
        ) {
            $this->_getSession()->addError($this->__('Invalid form key. Please refresh the page.'));
            $this->_redirect('*/*/review');
            return;
        }

        try {
            $requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds();
            if ($requiredAgreements) {
                $postedAgreements = array_keys($this->getRequest()->getPost('agreement', []));
                if (array_diff($requiredAgreements, $postedAgreements)) {
                    Mage::throwException(Mage::helper('paypal')->__('Please agree to all the terms and conditions before placing the order.'));
                }
            }

            $this->_initCheckout();
            $this->_checkout->place($this->_initToken());

            // prepare session to success or cancellation page
            $session = $this->_getCheckoutSession();
            $session->clearHelperData();

            // "last successful quote"
            $quoteId = $this->_getQuote()->getId();
            $session->setLastQuoteId($quoteId)->setLastSuccessQuoteId($quoteId);

            // an order may be created
            $order = $this->_checkout->getOrder();
            if ($order) {
                $session->setLastOrderId($order->getId())
                    ->setLastRealOrderId($order->getIncrementId());
                // as well a billing agreement can be created
                $agreement = $this->_checkout->getBillingAgreement();
                if ($agreement) {
                    $session->setLastBillingAgreementId($agreement->getId());
                }
            }

            // recurring profiles may be created along with the order or without it
            $profiles = $this->_checkout->getRecurringPaymentProfiles();
            if ($profiles) {
                $ids = [];
                foreach ($profiles as $profile) {
                    $ids[] = $profile->getId();
                }

                $session->setLastRecurringProfileIds($ids);
            }

            // redirect if PayPal specified some URL (for example, to Giropay bank)
            $url = $this->_checkout->getRedirectUrl();
            if ($url) {
                $this->getResponse()->setRedirect($url);
                return;
            }

            $this->_initToken(false); // no need in token anymore
            $this->_redirect('checkout/onepage/success');
            return;
        } catch (Mage_Paypal_Model_Api_ProcessableException $magePaypalModelApiProcessableException) {
            $this->_processPaypalApiError($magePaypalModelApiProcessableException);
        } catch (Mage_Core_Exception $mageCoreException) {
            Mage::helper('checkout')->sendPaymentFailedEmail($this->_getQuote(), $mageCoreException->getMessage());
            $this->_getSession()->addError($mageCoreException->getMessage());
            $this->_redirect('*/*/review');
        } catch (Exception $exception) {
            Mage::helper('checkout')->sendPaymentFailedEmail(
                $this->_getQuote(),
                $this->__('Unable to place the order.'),
            );
            $this->_getSession()->addError($this->__('Unable to place the order.'));
            Mage::logException($exception);
            $this->_redirect('*/*/review');
        }
    }

    /**
     * Whether the request carries the Express Checkout token of this session
     *
     * PayPal appends the token to the return URL, so a request without it did not come from PayPal.
     */
    protected function _isValidReturnToken(): bool
    {
        $sessionToken = (string) $this->_getSession()->getExpressCheckoutToken();
        $requestToken = $this->getRequest()->getParam('token');
        return $sessionToken !== ''
            && is_string($requestToken)
            && hash_equals($sessionToken, $requestToken);
    }

    /**
     * Forward a PayPal return whose token was checked to placeOrderAction()
     */
    protected function _forwardVerifiedReturn(): void
    {
        Mage::unregister(self::VERIFIED_RETURN_REGISTRY_KEY);
        Mage::register(self::VERIFIED_RETURN_REGISTRY_KEY, true);
        $this->_forward('placeOrder');
    }

    /**
     * Whether returnAction() forwarded a checked PayPal return in this request; the flag is cleared on read
     *
     * The flag lives in the registry, which a client cannot set, unlike request params.
     */
    protected function _consumeVerifiedReturn(): bool
    {
        $isVerified = Mage::registry(self::VERIFIED_RETURN_REGISTRY_KEY) === true;
        Mage::unregister(self::VERIFIED_RETURN_REGISTRY_KEY);
        return $isVerified;
    }

    /**
     * Process PayPal API's processable errors
     *
     * @param Mage_Paypal_Model_Api_ProcessableException $exception
     */
    protected function _processPaypalApiError($exception)
    {
        switch ($exception->getCode()) {
            case Mage_Paypal_Model_Api_ProcessableException::API_MAX_PAYMENT_ATTEMPTS_EXCEEDED:
            case Mage_Paypal_Model_Api_ProcessableException::API_TRANSACTION_EXPIRED:
                $this->getResponse()->setRedirect(
                    $this->_getQuote()->getPayment()->getCheckoutRedirectUrl(),
                );
                break;
            case Mage_Paypal_Model_Api_ProcessableException::API_DO_EXPRESS_CHECKOUT_FAIL:
                $this->_redirectSameToken();
                break;
            case Mage_Paypal_Model_Api_ProcessableException::API_UNABLE_TRANSACTION_COMPLETE:
                if ($this->_config->getPaymentAction() == Mage_Payment_Model_Method_Abstract::ACTION_ORDER) {
                    $paypalTransactionData = $this->_getCheckoutSession()->getPaypalTransactionData();
                    $this->getResponse()->setRedirect(
                        $this->_config->getExpressCheckoutOrderUrl($paypalTransactionData['transaction_id']),
                    );
                } else {
                    $this->_redirectSameToken();
                }

                break;
            default:
                $this->_redirectToCartAndShowError($exception->getUserMessage());
                break;
        }
    }

    /**
     * Redirect customer back to PayPal with the same token
     */
    protected function _redirectSameToken()
    {
        $token = $this->_initToken();
        $this->getResponse()->setRedirect(
            $this->_config->getExpressCheckoutStartUrl($token),
        );
    }

    /**
     * Redirect customer to shopping cart and show error message
     *
     * @param string $errorMessage
     */
    protected function _redirectToCartAndShowError($errorMessage)
    {
        $cart = Mage::getSingleton('checkout/cart');
        $cart->getCheckoutSession()->addError($errorMessage);
        $this->_redirect('checkout/cart');
    }

    /**
     * Instantiate quote and checkout
     *
     * @return Mage_Paypal_Model_Express_Checkout
     * @throws Mage_Core_Exception
     */
    protected function _initCheckout()
    {
        $quote = $this->_getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->getResponse()->setHeader('HTTP/1.1', '403 Forbidden');
            Mage::throwException(Mage::helper('paypal')->__('Unable to initialize Express Checkout.'));
        }

        /** @var Mage_Paypal_Model_Express_Checkout $classInstance */
        $classInstance = Mage::getSingleton($this->_checkoutType, [
            'config' => $this->_config,
            'quote'  => $quote,
        ]);
        $this->_checkout = $classInstance;
        return $this->_checkout;
    }

    /**
     * Search for proper checkout token in request or session or (un)set specified one
     * Combined getter/setter
     *
     * @param  string       $setToken
     * @return $this|string
     */
    protected function _initToken($setToken = null)
    {
        if ($setToken !== null) {
            if ($setToken === false) {
                // security measure for avoid unsetting token twice
                if (!$this->_getSession()->getExpressCheckoutToken()) {
                    Mage::throwException($this->__('PayPal Express Checkout Token does not exist.'));
                }

                $this->_getSession()->unsExpressCheckoutToken();
            } else {
                $this->_getSession()->setExpressCheckoutToken($setToken);
            }

            return $this;
        }

        if ($setToken = $this->getRequest()->getParam('token')) {
            if ($setToken !== $this->_getSession()->getExpressCheckoutToken()) {
                Mage::throwException($this->__('Wrong PayPal Express Checkout Token specified.'));
            }
        } else {
            $setToken = $this->_getSession()->getExpressCheckoutToken();
        }

        return $setToken;
    }

    /**
     * PayPal session instance getter
     *
     * @return Mage_Paypal_Model_Session
     */
    private function _getSession()
    {
        return Mage::getSingleton('paypal/session');
    }

    /**
     * Return checkout session object
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Return checkout quote object
     *
     * @return Mage_Sales_Model_Quote
     */
    private function _getQuote()
    {
        if (!$this->_quote) {
            $this->_quote = $this->_getCheckoutSession()->getQuote();
        }

        return $this->_quote;
    }

    /**
     * Redirect to login page
     */
    public function redirectLogin()
    {
        $this->setFlag('', 'no-dispatch', true);
        $this->getResponse()->setRedirect(
            Mage::helper('core/url')->addRequestParam(
                Mage::helper('customer')->getLoginUrl(),
                ['context' => 'checkout'],
            ),
        );
    }
}
