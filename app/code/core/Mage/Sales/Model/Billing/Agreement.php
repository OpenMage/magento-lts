<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Billing Agreement abstract model
 *
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Resource_Billing_Agreement _getResource()
 * @method Mage_Sales_Model_Resource_Billing_Agreement getResource()
 * @method Mage_Sales_Model_Resource_Billing_Agreement_Collection getCollection()
 *
 * @method int getAgreementId()
 * @method string getAgreementLabel()
 * @method $this setAgreementLabel(string $value)
 * @method string getCreatedAt()
 * @method $this setCreatedAt(string $value)
 * @method Mage_Customer_Model_Customer getCustomer()
 * @method $this setCustomer(Mage_Customer_Model_Customer $value)
 * @method int getCustomerId()
 * @method $this setCustomerId(int $value)
 * @method string getMethodCode()
 * @method $this setMethodCode(string $value)
 * @method string getReferenceId()
 * @method $this setReferenceId(string $value)
 * @method string getStatus()
 * @method $this setStatus(string $value)
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method string getUpdatedAt()
 * @method $this setUpdatedAt(string $value)
 *
 * @method $this setCancelUrl(string $value)
 * @method string getRedirectUrl()
 * @method $this setReturnUrl(string $value)
 * @method $this setToken(string $value)
 * @method int getBillingAgreementId()
 */
class Mage_Sales_Model_Billing_Agreement extends Mage_Payment_Model_Billing_AgreementAbstract
{
    public const STATUS_ACTIVE     = 'active';
    public const STATUS_CANCELED   = 'canceled';

    /**
     * Related agreement orders
     *
     * @var array
     */
    protected $_relatedOrders = [];

    /**
     * Init model
     */
    protected function _construct()
    {
        $this->_init('sales/billing_agreement');
    }

    /**
     * Set created_at parameter
     *
     * @inheritdoc
     */
    protected function _beforeSave()
    {
        $date = Mage::getModel('core/date')->gmtDate();
        if ($this->isObjectNew() && !$this->getCreatedAt()) {
            $this->setCreatedAt($date);
        } else {
            $this->setUpdatedAt($date);
        }
        return parent::_beforeSave();
    }

    /**
     * Save agreement order relations
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterSave()
    {
        if (!empty($this->_relatedOrders)) {
            $this->_saveOrderRelations();
        }
        return parent::_afterSave();
    }

    /**
     * Retrieve billing agreement status label
     *
     * @return string
     */
    public function getStatusLabel()
    {
        return match ($this->getStatus()) {
            self::STATUS_ACTIVE => Mage::helper('sales')->__('Active'),
            self::STATUS_CANCELED => Mage::helper('sales')->__('Canceled'),
            default => '',
        };
    }

    /**
     * Initialize token
     *
     * @return string
     */
    public function initToken()
    {
        $this->getPaymentMethodInstance()
            ->initBillingAgreementToken($this);
        return $this->getRedirectUrl();
    }

    /**
     * Get billing agreement details
     * Data from response is inside this object
     *
     * @return $this
     */
    public function verifyToken()
    {
        $this->getPaymentMethodInstance()
            ->getBillingAgreementTokenInfo($this);
        return $this;
    }

    /**
     * Check for permissions
     *
     * @param int $customerIdSession
     * @return bool
     */
    public function canPerformAction($customerIdSession)
    {
        // Get the customer id from billing agreement and compare to logged in customer id
        return (int) $this->getCustomerId() === (int) $customerIdSession;
    }

    /**
     * Create billing agreement
     *
     * @return $this
     */
    public function place()
    {
        $this->verifyToken();

        $paymentMethodInstance = $this->getPaymentMethodInstance()
            ->placeBillingAgreement($this);

        $this->setCustomerId($this->getCustomer()->getId())
            ->setMethodCode($this->getMethodCode())
            ->setReferenceId($this->getBillingAgreementId())
            ->setStatus(self::STATUS_ACTIVE)
            ->setAgreementLabel($paymentMethodInstance->getTitle())
            ->save();
        return $this;
    }

    /**
     * Cancel billing agreement
     *
     * @return $this
     */
    public function cancel()
    {
        $this->setStatus(self::STATUS_CANCELED);
        $this->getPaymentMethodInstance()->updateBillingAgreementStatus($this);
        return $this->save();
    }

    /**
     * Check whether can cancel billing agreement
     *
     * @return bool
     */
    public function canCancel()
    {
        return ($this->getStatus() != self::STATUS_CANCELED);
    }

    /**
     * Retrieve billing agreement statuses array
     *
     * @return array
     */
    public function getStatusesArray()
    {
        return [
            self::STATUS_ACTIVE     => Mage::helper('sales')->__('Active'),
            self::STATUS_CANCELED   => Mage::helper('sales')->__('Canceled'),
        ];
    }

    /**
     * Validate data
     *
     * @return bool
     */
    public function isValid()
    {
        $result = parent::isValid();
        if (!$this->getCustomerId()) {
            $this->_errors[] = Mage::helper('payment')->__('Customer ID is not set.');
        }
        if (!$this->getStatus()) {
            $this->_errors[] = Mage::helper('payment')->__('Billing Agreement status is not set.');
        }
        return $result && empty($this->_errors);
    }

    /**
     * Import payment data to billing agreement
     *
     * $payment->getBillingAgreementData() contains array with following structure :
     *  [billing_agreement_id]  => string
     *  [method_code]           => string
     *
     * @return $this
     */
    public function importOrderPayment(Mage_Sales_Model_Order_Payment $payment)
    {
        $baData = $payment->getBillingAgreementData();

        $this->_paymentMethodInstance = (isset($baData['method_code']))
            ? Mage::helper('payment')->getMethodInstance($baData['method_code'])
            : $payment->getMethodInstance();
        if ($this->_paymentMethodInstance) {
            $this->_paymentMethodInstance->setStore($payment->getMethodInstance()->getStore());
            $this->setCustomerId($payment->getOrder()->getCustomerId())
                ->setMethodCode($this->_paymentMethodInstance->getCode())
                ->setReferenceId($baData['billing_agreement_id'])
                ->setStatus(self::STATUS_ACTIVE);
        }
        return $this;
    }

    /**
     * Retrieve available customer Billing Agreements
     *
     * @param int $customerId
     * @return Mage_Sales_Model_Resource_Billing_Agreement_Collection
     */
    public function getAvailableCustomerBillingAgreements($customerId)
    {
        $collection = Mage::getResourceModel('sales/billing_agreement_collection');
        $collection->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('status', self::STATUS_ACTIVE)
            ->setOrder('agreement_id');
        return $collection;
    }

    /**
     * Check whether need to create billing agreement for customer
     *
     * @param int $customerId
     * @return bool
     */
    public function needToCreateForCustomer($customerId)
    {
        return $customerId ? count($this->getAvailableCustomerBillingAgreements($customerId)) == 0 : false;
    }

    /**
     * Add order relation to current billing agreement
     *
     * @param int|Mage_Sales_Model_Order $orderId
     * @return $this
     */
    public function addOrderRelation($orderId)
    {
        $this->_relatedOrders[] = $orderId;
        return $this;
    }

    /**
     * Save related orders
     */
    protected function _saveOrderRelations()
    {
        foreach ($this->_relatedOrders as $order) {
            $orderId = $order instanceof Mage_Sales_Model_Order ? $order->getId() : (int) $order;
            $this->getResource()->addOrderRelation($this->getId(), $orderId);
        }
    }
}
