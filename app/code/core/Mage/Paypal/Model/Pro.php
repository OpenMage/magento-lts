<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * PayPal Website Payments Pro implementation for payment method instances
 * This model was created because right now PayPal Direct and PayPal Express payment methods cannot have same abstract
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Model_Pro
{
    /**
     * Possible payment review actions (for FMF only)
     *
     * @var string
     */
    public const PAYMENT_REVIEW_ACCEPT = 'accept';

    public const PAYMENT_REVIEW_DENY = 'deny';

    /**
     * Config instance
     *
     * @var Mage_Paypal_Model_Config
     */
    protected $_config = null;

    /**
     * API instance
     *
     * @var null|false|Mage_Paypal_Model_Api_Nvp
     */
    protected $_api = null;

    /**
     * PayPal info object
     *
     * @var Mage_Paypal_Model_Info
     */
    protected $_infoInstance = null;

    /**
     * API model type
     *
     * @var string
     */
    protected $_apiType = 'paypal/api_nvp';

    /**
     * Config model type
     *
     * @var string
     */
    protected $_configType = 'paypal/config';

    /**
     * Payment method code setter. Also instantiates/updates config
     *
     * @param  string   $code
     * @param  null|int $storeId
     * @return $this
     */
    public function setMethod($code, $storeId = null)
    {
        if ($this->_config === null) {
            $params = [$code];
            if ($storeId !== null) {
                $params[] = $storeId;
            }

            /** @var Mage_Paypal_Model_Config $model */
            $model = Mage::getModel($this->_configType, $params);
            $this->_config = $model;
        } else {
            $this->_config->setMethod($code);
            if ($storeId !== null) {
                $this->_config->setStoreId($storeId);
            }
        }

        return $this;
    }

    /**
     * Config instance setter
     *
     * @param  int   $storeId
     * @return $this
     */
    public function setConfig(Mage_Paypal_Model_Config $instace, $storeId = null)
    {
        $this->_config = $instace;
        if ($storeId !== null) {
            $this->_config->setStoreId($storeId);
        }

        return $this;
    }

    /**
     * Config instance getter
     *
     * @return Mage_Paypal_Model_Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * API instance getter
     * Sets current store id to current config instance and passes it to API
     *
     * @return Mage_Paypal_Model_Api_Nvp
     */
    public function getApi()
    {
        if ($this->_api === null) {
            $this->_api = Mage::getModel($this->_apiType);
        }

        $this->_api->setConfigObject($this->_config);
        return $this->_api;
    }

    /**
     * Destroy existing NVP Api object
     *
     * @return $this
     */
    public function resetApi()
    {
        $this->_api = null;

        return $this;
    }

    /**
     * Instantiate and return info model
     *
     * @return Mage_Paypal_Model_Info
     */
    public function getInfo()
    {
        if ($this->_infoInstance === null) {
            $this->_infoInstance = Mage::getModel('paypal/info');
        }

        return $this->_infoInstance;
    }

    /**
     * Transfer transaction/payment information from API instance to order payment
     *
     * @param  Mage_Paypal_Model_Api_Abstract $from
     * @return $this
     */
    public function importPaymentInfo(Varien_Object $from, Mage_Payment_Model_Info $to)
    {
        // update PayPal-specific payment information in the payment object
        $this->getInfo()->importToPayment($from, $to);

        /**
         * Detect payment review and/or frauds
         * PayPal pro API returns fraud results only in the payment call response
         */
        if ($from->getDataUsingMethod(Mage_Paypal_Model_Info::IS_FRAUD)) {
            $to->setIsTransactionPending(true);
            $to->setIsFraudDetected(true);
        } elseif ($this->getInfo()->isPaymentReviewRequired($to)) {
            $to->setIsTransactionPending(true);
        }

        // give generic info about transaction state
        if ($this->getInfo()->isPaymentSuccessful($to)) {
            $to->setIsTransactionApproved(true);
        } elseif ($this->getInfo()->isPaymentFailed($to)) {
            $to->setIsTransactionDenied(true);
        }

        return $this;
    }

    /**
     * Void transaction
     *
     * @param  Mage_Payment_Model_Info $payment
     * @throws Mage_Core_Exception
     */
    public function void(Varien_Object $payment)
    {
        if ($authTransactionId = $this->_getParentTransactionId($payment)) {
            $api = $this->getApi();
            $api->setPayment($payment)->setAuthorizationId($authTransactionId)->callDoVoid();
            $this->importPaymentInfo($api, $payment);
        } else {
            Mage::throwException(Mage::helper('paypal')->__('Authorization transaction is required to void.'));
        }
    }

    /**
     * Attempt to capture payment
     * Will return false if the payment is not supposed to be captured
     *
     * @param  Mage_Sales_Model_Order_Payment $payment
     * @param  float                          $amount
     * @return false|void
     */
    public function capture(Varien_Object $payment, $amount)
    {
        $authTransactionId = $this->_getParentTransactionId($payment);
        if (!$authTransactionId) {
            return false;
        }

        $api = $this->getApi()
            ->setAuthorizationId($authTransactionId)
            ->setIsCaptureComplete($payment->getShouldCloseParentTransaction())
            ->setAmount($amount)
            ->setCurrencyCode($payment->getOrder()->getBaseCurrencyCode())
            ->setInvNum($payment->getOrder()->getIncrementId())
            // TODO: pass 'NOTE' to API
        ;

        $api->callDoCapture();
        $this->_importCaptureResultToPayment($api, $payment);
    }

    /**
     * Refund a capture transaction
     *
     * @param  Mage_Sales_Model_Order_Payment $payment
     * @param  float                          $amount
     * @throws Mage_Core_Exception
     */
    public function refund(Varien_Object $payment, $amount)
    {
        $captureTxnId = $this->_getParentTransactionId($payment);
        if ($captureTxnId) {
            $api = $this->getApi();
            $order = $payment->getOrder();
            $api->setPayment($payment)
                ->setTransactionId($captureTxnId)
                ->setAmount($amount)
                ->setCurrencyCode($order->getBaseCurrencyCode())
            ;
            $canRefundMore = $payment->getCreditmemo()->getInvoice()->canRefund();
            $isFullRefund = !$canRefundMore
                && (((float) $order->getBaseTotalOnlineRefunded() + (float) $order->getBaseTotalOfflineRefunded()) == 0);
            $api->setRefundType($isFullRefund ? Mage_Paypal_Model_Config::REFUND_TYPE_FULL
                : Mage_Paypal_Model_Config::REFUND_TYPE_PARTIAL);
            $api->callRefundTransaction();
            $this->_importRefundResultToPayment($api, $payment, $canRefundMore);
        } else {
            Mage::throwException(Mage::helper('paypal')->__('Impossible to issue a refund transaction because the capture transaction does not exist.'));
        }
    }

    /**
     * Cancel payment
     *
     * @param  Mage_Payment_Model_Info $payment
     * @throws Mage_Core_Exception
     */
    public function cancel(Varien_Object $payment)
    {
        if (!$payment->getOrder()->getInvoiceCollection()->count()) {
            $this->void($payment);
        }
    }

    /**
     * @param  Mage_Sales_Model_Order_Payment $payment
     * @return bool
     */
    public function canReviewPayment(Mage_Payment_Model_Info $payment)
    {
        return Mage_Paypal_Model_Info::isPaymentReviewRequired($payment);
    }

    /**
     * Perform the payment review
     *
     * @param  string $action
     * @return bool
     */
    public function reviewPayment(Mage_Payment_Model_Info $payment, $action)
    {
        $api = $this->getApi()->setTransactionId($payment->getLastTransId());

        // check whether the review is still needed
        $api->callGetTransactionDetails();
        $this->importPaymentInfo($api, $payment);
        if (!$this->getInfo()->isPaymentReviewRequired($payment)) {
            return false;
        }

        // perform the review action
        $api->setAction($action)->callManagePendingTransactionStatus();
        $api->callGetTransactionDetails();
        $this->importPaymentInfo($api, $payment);
        return true;
    }

    /**
     * Fetch transaction details info
     *
     * @param  string $transactionId
     * @return array
     */
    public function fetchTransactionInfo(Mage_Payment_Model_Info $payment, $transactionId)
    {
        $api = $this->getApi()
            ->setTransactionId($transactionId)
            ->setRawResponseNeeded(true);
        $api->callGetTransactionDetails();
        $this->importPaymentInfo($api, $payment);
        $data = $api->getRawSuccessResponseData();
        return ($data) ? $data : [];
    }

    /**
     * Validate RP data
     *
     * @throws Mage_Core_Exception
     */
    public function validateRecurringProfile(Mage_Payment_Model_Recurring_Profile $profile)
    {
        $errors = [];
        if (strlen($profile->getSubscriberName()) > 32) { // up to 32 single-byte chars
            $errors[] = Mage::helper('paypal')->__('Subscriber name is too long.');
        }

        $refId = $profile->getInternalReferenceId(); // up to 127 single-byte alphanumeric
        if (strlen($refId) > 127) { //  || !preg_match('/^[a-z\d\s]+$/i', $refId)
            $errors[] = Mage::helper('paypal')->__('Merchant reference ID format is not supported.');
        }

        $scheduleDescr = $profile->getScheduleDescription(); // up to 127 single-byte alphanumeric
        if (strlen($refId) > 127) { //  || !preg_match('/^[a-z\d\s]+$/i', $scheduleDescr)
            $errors[] = Mage::helper('paypal')->__('Schedule description is too long.');
        }

        if ($errors) {
            Mage::throwException(implode(' ', $errors));
        }
    }

    /**
     * Submit RP to the gateway
     *
     * @throws Mage_Core_Exception
     */
    public function submitRecurringProfile(
        Mage_Payment_Model_Recurring_Profile $profile,
        Mage_Payment_Model_Info $paymentInfo
    ) {
        $api = $this->getApi();
        Varien_Object_Mapper::accumulateByMap($profile, $api, [
            'token', // EC fields
            // TODO: DP fields
            // profile fields
            'subscriber_name', 'start_datetime', 'internal_reference_id', 'schedule_description',
            'suspension_threshold', 'bill_failed_later', 'period_unit', 'period_frequency', 'period_max_cycles',
            'billing_amount' => 'amount', 'trial_period_unit', 'trial_period_frequency', 'trial_period_max_cycles',
            'trial_billing_amount', 'currency_code', 'shipping_amount', 'tax_amount', 'init_amount', 'init_may_fail',
        ]);
        $api->callCreateRecurringPaymentsProfile();
        $profile->setReferenceId($api->getRecurringProfileId());
        if ($api->getIsProfileActive()) {
            $profile->setState(Mage_Sales_Model_Recurring_Profile::STATE_ACTIVE);
        } elseif ($api->getIsProfilePending()) {
            $profile->setState(Mage_Sales_Model_Recurring_Profile::STATE_PENDING);
        }
    }

    /**
     * Fetch RP details
     *
     * @param string $referenceId
     */
    public function getRecurringProfileDetails($referenceId, Varien_Object $result)
    {
        $api = $this->getApi();
        $api->setRecurringProfileId($referenceId)
            ->callGetRecurringPaymentsProfileDetails($result)
        ;
    }

    /**
     * Update RP data
     */
    public function updateRecurringProfile(Mage_Payment_Model_Recurring_Profile $profile) {}

    /**
     * Manage status
     */
    public function updateRecurringProfileStatus(Mage_Payment_Model_Recurring_Profile $profile)
    {
        $api = $this->getApi();
        $action = null;
        switch ($profile->getNewState()) {
            case Mage_Sales_Model_Recurring_Profile::STATE_CANCELED:
                $action = 'cancel';
                break;
            case Mage_Sales_Model_Recurring_Profile::STATE_SUSPENDED:
                $action = 'suspend';
                break;
            case Mage_Sales_Model_Recurring_Profile::STATE_ACTIVE:
                $action = 'activate';
                break;
        }

        $state = $profile->getState();
        $api->setRecurringProfileId($profile->getReferenceId())
            ->setIsAlreadyCanceled($state == Mage_Sales_Model_Recurring_Profile::STATE_CANCELED)
            ->setIsAlreadySuspended($state == Mage_Sales_Model_Recurring_Profile::STATE_SUSPENDED)
            ->setIsAlreadyActive($state == Mage_Sales_Model_Recurring_Profile::STATE_ACTIVE)
            ->setAction($action)
            ->callManageRecurringPaymentsProfileStatus()
        ;
    }

    /**
     * Import capture results to payment
     *
     * @param Mage_Paypal_Model_Api_Nvp      $api
     * @param Mage_Sales_Model_Order_Payment $payment
     */
    protected function _importCaptureResultToPayment($api, $payment)
    {
        $payment->setTransactionId($api->getTransactionId())->setIsTransactionClosed(false);
        $this->importPaymentInfo($api, $payment);
    }

    /**
     * Import refund results to payment
     *
     * @param Mage_Paypal_Model_Api_Nvp      $api
     * @param Mage_Sales_Model_Order_Payment $payment
     * @param bool                           $canRefundMore
     */
    protected function _importRefundResultToPayment($api, $payment, $canRefundMore)
    {
        $payment->setTransactionId($api->getRefundTransactionId())
                ->setIsTransactionClosed(1) // refund initiated by merchant
                ->setShouldCloseParentTransaction(!$canRefundMore)
        ;
        $this->importPaymentInfo($api, $payment);
    }

    /**
     * Parent transaction id getter
     *
     * @return string
     */
    protected function _getParentTransactionId(Varien_Object $payment)
    {
        return $payment->getParentTransactionId() ? $payment->getParentTransactionId() : $payment->getLastTransId();
    }
}
