<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Paypal Billing Agreement method
 *
 * @category   Mage
 * @package    Mage_Paypal
 */
class Mage_Paypal_Model_Method_Agreement extends Mage_Sales_Model_Payment_Method_Billing_AgreementAbstract implements Mage_Payment_Model_Billing_Agreement_MethodInterface
{
    /**
     * Method code
     *
     * @var string
     */
    protected $_code = Mage_Paypal_Model_Config::METHOD_BILLING_AGREEMENT;

    /**
     * Method instance settings
     *
     */
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid                 = true;
    protected $_canUseCheckout          = false;
    protected $_canUseInternal          = false;
    protected $_canFetchTransactionInfo = true;
    protected $_canReviewPayment        = true;

    /**
     * Website Payments Pro instance
     *
     * @var Mage_Paypal_Model_Pro
     */
    protected $_pro = null;

    /**
     * Initialize Mage_Paypal_Model_Pro model
     *
     * @param array $params
     */
    public function __construct($params = [])
    {
        $proInstance = array_shift($params);
        if ($proInstance && ($proInstance instanceof Mage_Paypal_Model_Pro)) {
            $this->_pro = $proInstance;
        } else {
            $this->_pro = Mage::getModel('paypal/pro');
        }
        $this->_pro->setMethod($this->_code);
    }

    /**
     * Store setter
     * Also updates store ID in config object
     *
     * @param Mage_Core_Model_Store|int $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->setData('store', $store);
        if ($store === null) {
            $store = Mage::app()->getStore()->getId();
        }
        $this->_pro->getConfig()->setStoreId(is_object($store) ? $store->getId() : $store);
        return $this;
    }

    /**
     * Init billing agreement
     *
     * @return $this
     */
    public function initBillingAgreementToken(Mage_Payment_Model_Billing_AgreementAbstract $agreement)
    {
        $api = $this->_pro->getApi()
            ->setReturnUrl($agreement->getReturnUrl())
            ->setCancelUrl($agreement->getCancelUrl())
            ->setBillingType($this->_pro->getApi()->getBillingAgreementType());

        $api->callSetCustomerBillingAgreement();
        $agreement->setRedirectUrl(
            $this->_pro->getConfig()->getStartBillingAgreementUrl($api->getToken())
        );
        return $this;
    }

    /**
     * Retrieve billing agreement customer details by token
     *
     * @return array
     */
    public function getBillingAgreementTokenInfo(Mage_Payment_Model_Billing_AgreementAbstract $agreement)
    {
        $api = $this->_pro->getApi()
            ->setToken($agreement->getToken());
        $api->callGetBillingAgreementCustomerDetails();
        $responseData = [
            'token'         => $api->getData('token'),
            'email'         => $api->getData('email'),
            'payer_id'      => $api->getData('payer_id'),
            'payer_status'  => $api->getData('payer_status')
        ];
        $agreement->addData($responseData);
        return $responseData;
    }

    /**
     * Create billing agreement by token specified in request
     *
     * @return $this
     */
    public function placeBillingAgreement(Mage_Payment_Model_Billing_AgreementAbstract $agreement)
    {
        $api = $this->_pro->getApi()
            ->setToken($agreement->getToken());
        $api->callCreateBillingAgreement();
        $agreement->setBillingAgreementId($api->getData('billing_agreement_id'));
        return $this;
    }

    /**
     * Update billing agreement status
     *
     * @return $this
     */
    public function updateBillingAgreementStatus(Mage_Payment_Model_Billing_AgreementAbstract $agreement)
    {
        $targetStatus = $agreement->getStatus();
        $api = $this->_pro->getApi()
            ->setReferenceId($agreement->getReferenceId())
            ->setBillingAgreementStatus($targetStatus);
        try {
            $api->callUpdateBillingAgreement();
        } catch (Mage_Core_Exception $e) {
            // when BA was already canceled, just pretend that the operation succeeded
            if (!(Mage_Sales_Model_Billing_Agreement::STATUS_CANCELED == $targetStatus
                && $api->getIsBillingAgreementAlreadyCancelled())
            ) {
                throw $e;
            }
        }
        return $this;
    }

    /**
     * Authorize payment
     *
     * @param float $amount
     * @return $this
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        return $this->_placeOrder($payment, $amount);
    }

    /**
     * Void payment
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return $this
     */
    public function void(Varien_Object $payment)
    {
        $this->_pro->void($payment);
        return $this;
    }

    /**
     * Capture payment
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @param float $amount
     * @return $this
     */
    public function capture(Varien_Object $payment, $amount)
    {
        if ($this->_pro->capture($payment, $amount) === false) {
            $this->_placeOrder($payment, $amount);
        }
        return $this;
    }

    /**
     * Refund capture
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @param float $amount
     * @return $this
     */
    public function refund(Varien_Object $payment, $amount)
    {
        $this->_pro->refund($payment, $amount);
        return $this;
    }

    /**
     * Cancel payment
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return $this
     */
    public function cancel(Varien_Object $payment)
    {
        $this->_pro->cancel($payment);
        return $this;
    }

    /**
     * Whether payment can be reviewed
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return bool
     */
    public function canReviewPayment(Mage_Payment_Model_Info $payment)
    {
        return parent::canReviewPayment($payment) && $this->_pro->canReviewPayment($payment);
    }

    /**
     * Attempt to accept a pending payment
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return bool
     */
    public function acceptPayment(Mage_Payment_Model_Info $payment)
    {
        parent::acceptPayment($payment);
        return $this->_pro->reviewPayment($payment, Mage_Paypal_Model_Pro::PAYMENT_REVIEW_ACCEPT);
    }

    /**
     * Attempt to deny a pending payment
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return bool
     */
    public function denyPayment(Mage_Payment_Model_Info $payment)
    {
        parent::denyPayment($payment);
        return $this->_pro->reviewPayment($payment, Mage_Paypal_Model_Pro::PAYMENT_REVIEW_DENY);
    }

    /**
     * Fetch transaction details info
     *
     * @param string $transactionId
     * @return array
     */
    public function fetchTransactionInfo(Mage_Payment_Model_Info $payment, $transactionId)
    {
        return $this->_pro->fetchTransactionInfo($payment, $transactionId);
    }

    /**
     * Place an order with authorization or capture action
     *
     * @param float $amount
     * @return $this
     */
    protected function _placeOrder(Mage_Sales_Model_Order_Payment $payment, $amount)
    {
        $order = $payment->getOrder();
        $billingAgreement = Mage::getModel('sales/billing_agreement')->load(
            $payment->getAdditionalInformation(
                Mage_Sales_Model_Payment_Method_Billing_AgreementAbstract::TRANSPORT_BILLING_AGREEMENT_ID
            )
        );

        $proConfig = $this->_pro->getConfig();
        $api = $this->_pro->getApi()
            ->setReferenceId($billingAgreement->getReferenceId())
            ->setPaymentAction($proConfig->paymentAction)
            ->setAmount($amount)
            ->setCurrencyCode($payment->getOrder()->getBaseCurrencyCode())
            ->setNotifyUrl(Mage::getUrl('paypal/ipn/'))
            ->setPaypalCart(Mage::getModel('paypal/cart', [$order]))
            ->setIsLineItemsEnabled($proConfig->lineItemsEnabled)
            ->setInvNum($order->getIncrementId())
        ;

        // call api and import transaction and other payment information
        $api->callDoReferenceTransaction();
        $this->_pro->importPaymentInfo($api, $payment);
        $api->callGetTransactionDetails();
        $this->_pro->importPaymentInfo($api, $payment);

        $payment->setTransactionId($api->getTransactionId())
            ->setIsTransactionClosed(0);

        if ($api->getBillingAgreementId()) {
            $order->addRelatedObject($billingAgreement);
            $billingAgreement->setIsObjectChanged(true);
            $billingAgreement->addOrderRelation($order);
        }

        return $this;
    }

    protected function _isAvailable($quote)
    {
        return $this->_pro->getConfig()->isMethodAvailable($this->_code);
    }

    /**
     * Payment action getter compatible with payment model
     *
     * @see Mage_Sales_Model_Payment::place()
     * @return string
     */
    public function getConfigPaymentAction()
    {
        return $this->_pro->getConfig()->getPaymentAction();
    }
}
