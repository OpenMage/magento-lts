<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Order model
 *
 * Supported events:
 *  sales_order_load_after
 *  sales_order_save_before
 *  sales_order_save_after
 *  sales_order_delete_before
 *  sales_order_delete_after
 *
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Resource_Order            _getResource()
 * @method array                                      getAppliedTaxes()
 * @method bool                                       getAppliedTaxIsSaved()
 * @method int                                        getBillingFirstname()
 * @method int                                        getBillingLastname()
 * @method bool                                       getCanReturnToStock()
 * @method Mage_Sales_Model_Resource_Order_Collection getCollection()
 * @method bool                                       getConvertingFromQuote()
 * @method Mage_Customer_Model_Customer               getCustomer()
 * @method bool                                       getForcedCanCreditmemo()
 * @method bool                                       getIsInProcess()
 * @method bool                                       getIsMultiPayment()
 * @method float                                      getQuantity()
 * @method Mage_Sales_Model_Quote                     getQuote()
 * @method bool                                       getReordered()
 * @method Mage_Sales_Model_Resource_Order            getResource()
 * @method Mage_Sales_Model_Resource_Order_Collection getResourceCollection()
 * @method float                                      getRevenue()
 * @method int                                        getRowTaxDisplayPrecision()
 * @method float                                      getShipping()
 * @method float                                      getTax()
 * @method bool                                       hasBillingAddressId()
 * @method bool                                       hasCanReturnToStock()
 * @method bool                                       hasCustomerNoteNotify()
 * @method bool                                       hasForcedCanCreditmemo()
 * @method bool                                       hasShippingAddressId()
 * @method $this                                      setAppliedTaxes(array $value)
 * @method $this                                      setAppliedTaxIsSaved(bool $value)
 * @method $this                                      setCanReturnToStock()
 * @method $this                                      setConvertingFromQuote(bool $value)
 * @method $this                                      setCustomer(Mage_Customer_Model_Customer $value)
 * @method $this                                      setForcedCanCreditmemo(bool $value)
 * @method $this                                      setGiftMessage(string $value)
 * @method $this                                      setIsInProcess(bool $value)
 * @method $this                                      setQuote(Mage_Sales_Model_Quote $value)
 * @method $this                                      unsBillingAddressId()
 * @method $this                                      unsShippingAddressId()
 */
class Mage_Sales_Model_Order extends Mage_Sales_Model_Abstract
{
    /**
     * Identifier for history item
     */
    public const ENTITY                                = 'order';

    /**
     * Event type names for order emails
     */
    public const EMAIL_EVENT_NAME_NEW_ORDER    = 'new_order';

    public const EMAIL_EVENT_NAME_UPDATE_ORDER = 'update_order';

    /**
     * XML configuration paths
     */
    public const XML_PATH_EMAIL_TEMPLATE               = 'sales_email/order/template';

    public const XML_PATH_EMAIL_GUEST_TEMPLATE         = 'sales_email/order/guest_template';

    public const XML_PATH_EMAIL_IDENTITY               = 'sales_email/order/identity';

    public const XML_PATH_EMAIL_COPY_TO                = 'sales_email/order/copy_to';

    public const XML_PATH_EMAIL_COPY_METHOD            = 'sales_email/order/copy_method';

    public const XML_PATH_EMAIL_ENABLED                = 'sales_email/order/enabled';

    public const XML_PATH_UPDATE_EMAIL_TEMPLATE        = 'sales_email/order_comment/template';

    public const XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE  = 'sales_email/order_comment/guest_template';

    public const XML_PATH_UPDATE_EMAIL_IDENTITY        = 'sales_email/order_comment/identity';

    public const XML_PATH_UPDATE_EMAIL_COPY_TO         = 'sales_email/order_comment/copy_to';

    public const XML_PATH_UPDATE_EMAIL_COPY_METHOD     = 'sales_email/order_comment/copy_method';

    public const XML_PATH_UPDATE_EMAIL_ENABLED         = 'sales_email/order_comment/enabled';

    /**
     * Order states
     */
    public const STATE_NEW             = 'new';

    public const STATE_PENDING_PAYMENT = 'pending_payment';

    public const STATE_PROCESSING      = 'processing';

    public const STATE_COMPLETE        = 'complete';

    public const STATE_CLOSED          = 'closed';

    public const STATE_CANCELED        = 'canceled';

    public const STATE_HOLDED          = 'holded';

    public const STATE_PAYMENT_REVIEW  = 'payment_review';

    /**
     * Order statuses
     */
    public const STATUS_FRAUD  = 'fraud';

    /**
     * Order flags
     */
    public const ACTION_FLAG_CANCEL                    = 'cancel';

    public const ACTION_FLAG_HOLD                      = 'hold';

    public const ACTION_FLAG_UNHOLD                    = 'unhold';

    public const ACTION_FLAG_EDIT                      = 'edit';

    public const ACTION_FLAG_CREDITMEMO                = 'creditmemo';

    public const ACTION_FLAG_INVOICE                   = 'invoice';

    public const ACTION_FLAG_REORDER                   = 'reorder';

    public const ACTION_FLAG_SHIP                      = 'ship';

    public const ACTION_FLAG_COMMENT                   = 'comment';

    public const ACTION_FLAG_PRODUCTS_PERMISSION_DENIED = 'product_permission_denied';

    /**
     * Report date types
     */
    public const REPORT_DATE_TYPE_CREATED = 'created';

    public const REPORT_DATE_TYPE_UPDATED = 'updated';

    /**
     * Identifier for history item
     */
    public const HISTORY_ENTITY_NAME = 'order';

    protected $_eventPrefix = 'sales_order';

    protected $_eventObject = 'order';

    /**
     * @var null|Mage_Sales_Model_Order_Address[]|Mage_Sales_Model_Resource_Order_Address_Collection
     */
    protected $_addresses       = null;

    /**
     * @var null|Mage_Sales_Model_Order_Item[]|Mage_Sales_Model_Resource_Order_Item_Collection
     */
    protected $_items           = null;

    /**
     * @var null|Mage_Sales_Model_Order_Payment[]|Mage_Sales_Model_Resource_Order_Payment_Collection
     */
    protected $_payments        = null;

    /**
     * @var null|Mage_Sales_Model_Order_Status_History[]|Mage_Sales_Model_Resource_Order_Status_History_Collection
     */
    protected $_statusHistory   = null;

    /**
     * @var null|Mage_Sales_Model_Resource_Order_Invoice_Collection
     */
    protected $_invoices;

    /**
     * @var null|Mage_Sales_Model_Resource_Order_Shipment_Track_Collection
     */
    protected $_tracks;

    /**
     * @var null|false|Mage_Sales_Model_Resource_Order_Shipment_Collection
     */
    protected $_shipments;

    /**
     * @var null|false|Mage_Sales_Model_Order_Creditmemo[]|Mage_Sales_Model_Resource_Order_Creditmemo_Collection
     */
    protected $_creditmemos;

    protected $_relatedObjects  = [];

    protected $_orderCurrency   = null;

    protected $_baseCurrency    = null;

    /**
     * Array of action flags for canUnhold, canEdit, etc.
     *
     * @var array
     */
    protected $_actionFlag = [];

    /**
     * Flag: if after order placing we can send new email to the customer.
     *
     * @var bool
     */
    protected $_canSendNewEmailFlag = true;

    /**
     * Identifier for history item
     *
     * @var string
     */
    protected $_historyEntityName = self::HISTORY_ENTITY_NAME;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sales/order');
    }

    /**
     * Init mapping array of short fields to
     * its full names
     *
     * @return $this
     */
    protected function _initOldFieldsMap()
    {
        // pre 1.6 fields names, old => new
        $this->_oldFieldsMap = [
            'payment_authorization_expiration' => 'payment_auth_expiration',
            'forced_do_shipment_with_invoice' => 'forced_shipment_with_invoice',
            'base_shipping_hidden_tax_amount' => 'base_shipping_hidden_tax_amnt',
        ];
        return $this;
    }

    /**
     * Clear order object data
     *
     * @param  string $key data key
     * @return $this
     */
    #[Override]
    public function unsetData($key = null)
    {
        parent::unsetData($key);
        if (is_null($key)) {
            $this->_items = null;
        }

        return $this;
    }

    /**
     * Retrieve can flag for action (edit, unhold, etc..)
     *
     * @param  string    $action
     * @return null|bool
     */
    public function getActionFlag($action)
    {
        return $this->_actionFlag[$action] ?? null;
    }

    /**
     * Set can flag value for action (edit, unhold, etc...)
     *
     * @param  string $action
     * @param  bool   $flag
     * @return $this
     */
    public function setActionFlag($action, $flag)
    {
        $this->_actionFlag[$action] = (bool) $flag;
        return $this;
    }

    /**
     * Return flag for order if it can sends new email to customer.
     *
     * @return bool
     */
    public function getCanSendNewEmailFlag()
    {
        return $this->_canSendNewEmailFlag;
    }

    /**
     * Set flag for order if it can sends new email to customer.
     *
     * @param  bool  $flag
     * @return $this
     */
    public function setCanSendNewEmailFlag($flag)
    {
        $this->_canSendNewEmailFlag = (bool) $flag;
        return $this;
    }

    /**
     * Load order by system increment identifier
     *
     * @param  string $incrementId
     * @return $this
     */
    public function loadByIncrementId($incrementId)
    {
        return $this->loadByAttribute('increment_id', $incrementId);
    }

    /**
     * Load order by custom attribute value. Attribute value should be unique
     *
     * @param  string $attribute
     * @param  string $value
     * @return $this
     */
    public function loadByAttribute($attribute, $value)
    {
        $this->load($value, $attribute);
        return $this;
    }

    /**
     * Retrieve store model instance
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        $storeId = $this->getStoreId();
        if ($storeId) {
            return Mage::app()->getStore($storeId);
        }

        return Mage::app()->getStore();
    }

    /**
     * Retrieve order cancel availability
     *
     * @return bool
     */
    public function canCancel()
    {
        if (!$this->_canVoidOrder()) {
            return false;
        }

        if ($this->canUnhold()) {  // $this->isPaymentReview()
            return false;
        }

        $allInvoiced = true;
        foreach ($this->getAllItems() as $item) {
            if ($item->getQtyToInvoice()) {
                $allInvoiced = false;
                break;
            }
        }

        if ($allInvoiced) {
            return false;
        }

        $state = $this->getState();
        if ($this->isCanceled() || $state === self::STATE_COMPLETE || $state === self::STATE_CLOSED) {
            return false;
        }

        /**
         * Use only state for availability detect
         */
        /*foreach ($this->getAllItems() as $item) {
              if ($item->getQtyToCancel()>0) {
                  return true;
              }
          }
          return false;*/
        return $this->getActionFlag(self::ACTION_FLAG_CANCEL) !== false;
    }

    /**
     * Getter whether the payment can be voided
     *
     * @return bool
     */
    public function canVoidPayment()
    {
        return $this->_canVoidOrder() && $this->getPayment()->canVoid($this->getPayment());
    }

    /**
     * Check whether order could be canceled by states and flags
     *
     * @return bool
     */
    protected function _canVoidOrder()
    {
        return !$this->canUnhold() && !$this->isPaymentReview();
    }

    /**
     * Retrieve order invoice availability
     *
     * @return bool
     */
    public function canInvoice()
    {
        if ($this->canUnhold() || $this->isPaymentReview()) {
            return false;
        }

        $state = $this->getState();
        if ($this->isCanceled() || $state === self::STATE_COMPLETE || $state === self::STATE_CLOSED) {
            return false;
        }

        if ($this->getActionFlag(self::ACTION_FLAG_INVOICE) === false) {
            return false;
        }

        foreach ($this->getAllItems() as $item) {
            if ($item->getQtyToInvoice() > 0 && !$item->getLockedDoInvoice()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve order credit memo (refund) availability
     *
     * @return bool
     */
    public function canCreditmemo()
    {
        if ($this->hasForcedCanCreditmemo()) {
            return $this->getForcedCanCreditmemo();
        }

        if ($this->canUnhold() || $this->isPaymentReview()) {
            return false;
        }

        if ($this->isCanceled() || $this->getState() === self::STATE_CLOSED) {
            return false;
        }

        /**
         * We can have problem with float in php (on some server $a=762.73;$b=762.73; $a-$b!=0)
         * for this we have additional diapason for 0
         * TotalPaid - contains amount, that were not rounded.
         */
        if (abs($this->getStore()->roundPrice($this->getTotalPaid()) - $this->getTotalRefunded()) < .0001) {
            return false;
        }

        return $this->getActionFlag(self::ACTION_FLAG_EDIT) !== false;
    }

    /**
     * Retrieve order hold availability
     *
     * @return bool
     */
    public function canHold()
    {
        $state = $this->getState();
        if ($this->isCanceled() || $this->isPaymentReview()
            || $state === self::STATE_COMPLETE || $state === self::STATE_CLOSED || $state === self::STATE_HOLDED
        ) {
            return false;
        }

        return $this->getActionFlag(self::ACTION_FLAG_HOLD) !== false;
    }

    /**
     * Retrieve order unhold availability
     *
     * @return bool
     */
    public function canUnhold()
    {
        if ($this->getActionFlag(self::ACTION_FLAG_UNHOLD) === false || $this->isPaymentReview()) {
            return false;
        }

        return $this->getState() === self::STATE_HOLDED;
    }

    /**
     * Check if comment can be added to order history
     *
     * @return bool
     */
    public function canComment()
    {
        return $this->getActionFlag(self::ACTION_FLAG_COMMENT) !== false;
    }

    /**
     * Retrieve order shipment availability
     *
     * @return bool
     */
    public function canShip()
    {
        if ($this->canUnhold() || $this->isPaymentReview()) {
            return false;
        }

        if ($this->getIsVirtual() || $this->isCanceled()) {
            return false;
        }

        if ($this->getActionFlag(self::ACTION_FLAG_SHIP) === false) {
            return false;
        }

        foreach ($this->getAllItems() as $item) {
            if ($item->getQtyToShip() > 0 && !$item->getIsVirtual()
                && !$item->getLockedDoShip()
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve order edit availability
     *
     * @return bool
     */
    public function canEdit()
    {
        if ($this->canUnhold()) {
            return false;
        }

        $state = $this->getState();
        if ($this->isCanceled() || $this->isPaymentReview()
            || $state === self::STATE_COMPLETE || $state === self::STATE_CLOSED
        ) {
            return false;
        }

        if (!$this->getPayment()->getMethodInstance()->canEdit()) {
            return false;
        }

        return $this->getActionFlag(self::ACTION_FLAG_EDIT) !== false;
    }

    /**
     * Retrieve order reorder availability
     *
     * @return bool
     */
    public function canReorder()
    {
        return $this->_canReorder(true);
    }

    /**
     * Check the ability to reorder ignoring the availability in stock or status of the ordered products
     *
     * @return bool
     */
    public function canReorderIgnoreSalable()
    {
        return $this->_canReorder(true);
    }

    /**
     * Retrieve order reorder availability
     *
     * @param  bool $ignoreSalable
     * @return bool
     */
    protected function _canReorder($ignoreSalable = false)
    {
        if ($this->canUnhold() || $this->isPaymentReview()) {
            return false;
        }

        if ($this->getActionFlag(self::ACTION_FLAG_REORDER) === false) {
            return false;
        }

        $products = [];
        foreach ($this->getItemsCollection() as $item) {
            $products[] = $item->getProductId();
        }

        /*
         * @TODO ACPAOC: Use product collection here, but ensure that product
         * is loaded with order store id, otherwise there'll be problems with isSalable()
         * for configurables, bundles and other composites
         *
         */
        /*
        $productsCollection = Mage::getModel('catalog/product')->getCollection()
            ->setStoreId($this->getStoreId())
            ->addIdFilter($products)
            ->addAttributeToSelect('status')
            ->load();

        foreach ($productsCollection as $product) {
            if (!$product->isSalable()) {
                return false;
            }
        }
        */
        foreach ($products as $productId) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId($this->getStoreId())
                ->load($productId);
            if (!$product->getId() || (!$ignoreSalable && !$product->isSalable())) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check whether the payment is in payment review state
     * In this state order cannot be normally processed. Possible actions can be:
     * - accept or deny payment
     * - fetch transaction information
     *
     * @return bool
     */
    public function isPaymentReview()
    {
        return $this->getState() === self::STATE_PAYMENT_REVIEW;
    }

    /**
     * Check whether payment can be accepted or denied
     *
     * @return bool
     */
    public function canReviewPayment()
    {
        return $this->isPaymentReview() && $this->getPayment()->canReviewPayment();
    }

    /**
     * Check whether there can be a transaction update fetched for payment in review state
     *
     * @return bool
     */
    public function canFetchPaymentReviewUpdate()
    {
        return $this->isPaymentReview() && $this->getPayment()->canFetchTransactionInfo();
    }

    /**
     * Retrieve order configuration model
     *
     * @return Mage_Sales_Model_Order_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('sales/order_config');
    }

    /**
     * Place order payments
     *
     * @return $this
     */
    protected function _placePayment()
    {
        $this->getPayment()->place();
        return $this;
    }

    /**
     * Retrieve order payment model object
     *
     * @return false|Mage_Sales_Model_Order_Payment
     */
    public function getPayment()
    {
        foreach ($this->getPaymentsCollection() as $payment) {
            if (!$payment->isDeleted()) {
                return $payment;
            }
        }

        return false;
    }

    /**
     * Declare order billing address
     *
     * @return $this
     */
    public function setBillingAddress(Mage_Sales_Model_Order_Address $address)
    {
        $old = $this->getBillingAddress();
        if (!empty($old)) {
            $address->setId($old->getId());
        }

        $this->addAddress($address->setAddressType('billing'));
        return $this;
    }

    /**
     * Declare order shipping address
     *
     * @return $this
     */
    public function setShippingAddress(Mage_Sales_Model_Order_Address $address)
    {
        $old = $this->getShippingAddress();
        if (!empty($old)) {
            $address->setId($old->getId());
        }

        $this->addAddress($address->setAddressType('shipping'));
        return $this;
    }

    /**
     * Retrieve order billing address
     *
     * @return false|Mage_Sales_Model_Order_Address
     */
    public function getBillingAddress()
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getAddressType() == 'billing' && !$address->isDeleted()) {
                return $address;
            }
        }

        return false;
    }

    /**
     * Retrieve order shipping address
     *
     * @return false|Mage_Sales_Model_Order_Address
     */
    public function getShippingAddress()
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getAddressType() == 'shipping' && !$address->isDeleted()) {
                return $address;
            }
        }

        return false;
    }

    /**
     * Order state setter.
     * If status is specified, will add order status history with specified comment
     * the setData() cannot be overridden because of compatibility issues with resource model
     *
     * @param  string      $state
     * @param  bool|string $status
     * @param  string      $comment
     * @param  bool        $isCustomerNotified
     * @return $this
     */
    public function setState($state, $status = false, $comment = '', $isCustomerNotified = null)
    {
        return $this->_setState($state, $status, $comment, $isCustomerNotified, true);
    }

    /**
     * Order state protected setter.
     * By default allows to set any state. Can also update status to default or specified value
     * Сomplete and closed states are encapsulated intentionally, see the _checkState()
     *
     * @param  string      $state
     * @param  bool|string $status
     * @param  string      $comment
     * @param  bool        $isCustomerNotified
     * @param  bool        $shouldProtectState
     * @return $this
     */
    protected function _setState(
        $state,
        $status = false,
        $comment = '',
        $isCustomerNotified = null,
        $shouldProtectState = false
    ) {
        // attempt to set the specified state
        if ($shouldProtectState && $this->isStateProtected($state)) {
            Mage::throwException(
                Mage::helper('sales')->__('The Order State "%s" must not be set manually.', $state),
            );
        }

        $this->setData('state', $state);

        // add status history
        if ($status) {
            if ($status === true) {
                $status = $this->getConfig()->getStateDefaultStatus($state);
            }

            $this->setStatus($status);
            $history = $this->addStatusHistoryComment($comment, false); // no sense to set $status again
            $history->setIsCustomerNotified($isCustomerNotified); // for backwards compatibility
        }

        return $this;
    }

    /**
     * Whether specified state can be set from outside
     * @param  string $state
     * @return bool
     */
    public function isStateProtected($state)
    {
        if (empty($state)) {
            return false;
        }

        return self::STATE_COMPLETE == $state || self::STATE_CLOSED == $state;
    }

    /**
     * Retrieve label of order status
     *
     * @return string
     */
    public function getStatusLabel()
    {
        return $this->getConfig()->getStatusLabel($this->getStatus());
    }

    /**
     * Add status change information to history
     *
     * @param  string $status
     * @param  string $comment
     * @param  bool   $isCustomerNotified
     * @return $this
     * @deprecated after 1.4.0.0-alpha3
     */
    public function addStatusToHistory($status, $comment = '', $isCustomerNotified = false)
    {
        $this->addStatusHistoryComment($comment, $status)
            ->setIsCustomerNotified($isCustomerNotified);
        return $this;
    }

    /**
     * Add a comment to order
     * Different or default status may be specified
     *
     * @param  string                                $comment
     * @param  bool|string                           $status
     * @return Mage_Sales_Model_Order_Status_History
     */
    public function addStatusHistoryComment($comment, $status = false)
    {
        if ($status === false) {
            $status = $this->getStatus();
        } elseif ($status === true) {
            $status = $this->getConfig()->getStateDefaultStatus($this->getState());
        } else {
            $this->setStatus($status);
        }

        $history = Mage::getModel('sales/order_status_history')
            ->setStatus($status)
            ->setComment($comment)
            ->setEntityName($this->_historyEntityName);
        $this->addStatusHistory($history);
        return $history;
    }

    /**
     * Overrides entity id, which will be saved to comments history status
     *
     * @param  string $entityName
     * @return $this
     */
    public function setHistoryEntityName($entityName)
    {
        $this->_historyEntityName = $entityName;
        return $this;
    }

    /**
     * Place order
     *
     * @return $this
     */
    public function place()
    {
        Mage::dispatchEvent('sales_order_place_before', ['order' => $this]);
        $this->_placePayment();
        Mage::dispatchEvent('sales_order_place_after', ['order' => $this]);
        return $this;
    }

    /**
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function hold()
    {
        if (!$this->canHold()) {
            Mage::throwException(Mage::helper('sales')->__('Hold action is not available.'));
        }

        $this->setHoldBeforeState($this->getState());
        $this->setHoldBeforeStatus($this->getStatus());
        $this->setState(self::STATE_HOLDED, true);
        return $this;
    }

    /**
     * Attempt to unhold the order
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function unhold()
    {
        if (!$this->canUnhold()) {
            Mage::throwException(Mage::helper('sales')->__('Unhold action is not available.'));
        }

        $this->setState($this->getHoldBeforeState(), $this->getHoldBeforeStatus());
        $this->setHoldBeforeState(null);
        $this->setHoldBeforeStatus(null);
        return $this;
    }

    /**
     * Cancel order
     * @param  string $comment
     * @return $this
     */
    public function cancel($comment = '')
    {
        if ($this->canCancel()) {
            $this->getPayment()->cancel();
            $this->registerCancellation($comment);
            Mage::dispatchEvent('order_cancel_after', ['order' => $this]);
        }

        return $this;
    }

    /**
     * Prepare order totals to cancellation
     * @param  string              $comment
     * @param  bool                $graceful
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function registerCancellation($comment = '', $graceful = true)
    {
        if ($this->canCancel() || $this->isPaymentReview()) {
            $cancelState = self::STATE_CANCELED;
            foreach ($this->getAllItems() as $item) {
                if ($cancelState !== self::STATE_PROCESSING && $item->getQtyToRefund()) {
                    $cancelState = $item->getQtyToShip() > $item->getQtyToCancel() ? self::STATE_PROCESSING : self::STATE_COMPLETE;
                }

                $item->cancel();
            }

            $this->setSubtotalCanceled($this->getSubtotal() - $this->getSubtotalInvoiced());
            $this->setBaseSubtotalCanceled($this->getBaseSubtotal() - $this->getBaseSubtotalInvoiced());

            $this->setTaxCanceled($this->getTaxAmount() - $this->getTaxInvoiced());
            $this->setBaseTaxCanceled($this->getBaseTaxAmount() - $this->getBaseTaxInvoiced());

            $this->setShippingCanceled($this->getShippingAmount() - $this->getShippingInvoiced());
            $this->setBaseShippingCanceled($this->getBaseShippingAmount() - $this->getBaseShippingInvoiced());

            $this->setDiscountCanceled(abs($this->getDiscountAmount()) - $this->getDiscountInvoiced());
            $this->setBaseDiscountCanceled(abs($this->getBaseDiscountAmount()) - $this->getBaseDiscountInvoiced());

            $this->setTotalCanceled($this->getGrandTotal() - $this->getTotalPaid());
            $this->setBaseTotalCanceled($this->getBaseGrandTotal() - $this->getBaseTotalPaid());

            $this->_setState($cancelState, true, $comment);
        } elseif (!$graceful) {
            Mage::throwException(Mage::helper('sales')->__('Order does not allow to be canceled.'));
        }

        return $this;
    }

    /**
     * Retrieve tracking numbers
     *
     * @return array
     */
    public function getTrackingNumbers()
    {
        if ($this->getDataByKey('tracking_numbers')) {
            return explode(',', $this->getDataByKey('tracking_numbers'));
        }

        return [];
    }

    /**
     * Return model of shipping carrier
     *
     * @return Mage_Shipping_Model_Carrier_Abstract
     */
    public function getShippingCarrier()
    {
        $carrierModel = $this->getDataByKey('shipping_carrier');
        if (is_null($carrierModel)) {
            $carrierModel = false;
            /**
             * $method - carrier_method
             */
            $method = $this->getShippingMethod(true);
            if ($method instanceof Varien_Object) {
                $className = Mage::getStoreConfig('carriers/' . $method->getCarrierCode() . '/model');
                if ($className) {
                    $carrierModel = Mage::getModel($className);
                }
            }

            $this->setData('shipping_carrier', $carrierModel);
        }

        return $carrierModel;
    }

    /**
     * Retrieve shipping method
     *
     * @param  bool                 $asObject return carrier code and shipping method data as object
     * @return string|Varien_Object
     */
    public function getShippingMethod($asObject = false)
    {
        $shippingMethod = parent::getShippingMethod();
        if (!$asObject) {
            return $shippingMethod;
        }

        $segments = explode('_', $shippingMethod, 2);
        if (!isset($segments[1])) {
            $segments[1] = $segments[0];
        }

        [$carrierCode, $method] = $segments;
        return new Varien_Object([
            'carrier_code' => $carrierCode,
            'method'       => $method,
        ]);
    }

    /**
     * Get the current customer email.
     *
     * @return string
     */
    public function getCurrentCustomerEmail()
    {
        if (!$this->getDataByKey('current_customer_email')) {
            if ($this->getCustomer()) {
                $email = $this->getCustomer()->getEmail();
            } elseif ($this->getCustomerId()) {
                $email = Mage::getResourceSingleton('customer/customer')->getEmail($this->getCustomerId());
            }

            // Guest checkout or customer was deleted.
            if (empty($email)) {
                $email = $this->getCustomerEmail();
            }

            $this->setData('current_customer_email', $email);
        }

        return $this->getDataByKey('current_customer_email');
    }

    /**
     * Queue email with new order data
     *
     * @param bool $forceMode if true then email will be sent regardless of the fact that it was already sent previously
     *
     * @return $this
     * @throws Exception
     */
    public function queueNewOrderEmail($forceMode = false)
    {
        $storeId = $this->getStore()->getId();

        if (!Mage::helper('sales')->canSendNewOrderEmail($storeId)) {
            return $this;
        }

        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $storeId);

        // Start store emulation process
        if ($storeId != Mage::app()->getStore()->getId()) {
            /** @var Mage_Core_Model_App_Emulation $appEmulation */
            $appEmulation = Mage::getSingleton('core/app_emulation');
            $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);
        }

        try {
            // Retrieve specified view block from appropriate design package (depends on emulated store)
            $paymentBlock = Mage::helper('payment')->getInfoBlock($this->getPayment())
                ->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($storeId);
            $paymentBlockHtml = $paymentBlock->toHtml();
        } catch (Exception $exception) {
            // Stop store emulation process
            if (isset($appEmulation, $initialEnvironmentInfo)) {
                $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            }

            throw $exception;
        }

        // Stop store emulation process
        if (isset($appEmulation, $initialEnvironmentInfo)) {
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
        }

        // Retrieve corresponding email template id and customer name
        if ($this->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $this->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $storeId);
            $customerName = $this->getCustomerName();
        }

        /** @var Mage_Core_Model_Email_Template_Mailer $mailer */
        $mailer = Mage::getModel('core/email_template_mailer');
        /** @var Mage_Core_Model_Email_Info $emailInfo */
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($this->getCurrentCustomerEmail(), $customerName);
        if ($copyTo && $copyMethod == 'bcc') {
            // Add bcc to customer email
            foreach ($copyTo as $email) {
                $emailInfo->addBcc($email);
            }
        }

        $mailer->addEmailInfo($emailInfo);

        // Email copies are sent as separated emails if their copy method is 'copy'
        if ($copyTo && $copyMethod == 'copy') {
            foreach ($copyTo as $email) {
                $emailInfo = Mage::getModel('core/email_info');
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams([
            'order'        => $this,
            'billing'      => $this->getBillingAddress(),
            'payment_html' => $paymentBlockHtml,
        ]);

        /** @var Mage_Core_Model_Email_Queue $emailQueue */
        $emailQueue = Mage::getModel('core/email_queue');
        $emailQueue->setEntityId($this->getId())
            ->setEntityType(self::ENTITY)
            ->setEventType(self::EMAIL_EVENT_NAME_NEW_ORDER)
            ->setIsForceCheck(!$forceMode);

        $mailer->setQueue($emailQueue)->send();

        $this->setEmailSent(true);
        $this->_getResource()->saveAttribute($this, 'email_sent');

        return $this;
    }

    /**
     * Send email with order data
     *
     * @return $this
     */
    public function sendNewOrderEmail()
    {
        $this->queueNewOrderEmail(true);
        return $this;
    }

    /**
     * Queue email with order update information
     *
     * @param bool   $notifyCustomer
     * @param string $comment
     * @param bool   $forceMode      if true then email will be sent regardless of the fact that it was already sent previously
     *
     * @return $this
     */
    public function queueOrderUpdateEmail($notifyCustomer = true, $comment = '', $forceMode = false)
    {
        $storeId = $this->getStore()->getId();

        if (!Mage::helper('sales')->canSendOrderCommentEmail($storeId)) {
            return $this;
        }

        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_UPDATE_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_COPY_METHOD, $storeId);
        // Check if at least one recipient is found
        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }

        // Retrieve corresponding email template id and customer name
        if ($this->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $this->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_TEMPLATE, $storeId);
            $customerName = $this->getCustomerName();
        }

        /** @var Mage_Core_Model_Email_Template_Mailer $mailer */
        $mailer = Mage::getModel('core/email_template_mailer');
        if ($notifyCustomer) {
            /** @var Mage_Core_Model_Email_Info $emailInfo */
            $emailInfo = Mage::getModel('core/email_info');
            $emailInfo->addTo($this->getCurrentCustomerEmail(), $customerName);
            if ($copyTo && $copyMethod == 'bcc') {
                // Add bcc to customer email
                foreach ($copyTo as $email) {
                    $emailInfo->addBcc($email);
                }
            }

            $mailer->addEmailInfo($emailInfo);
        }

        // Email copies are sent as separated emails if their copy method is
        // 'copy' or a customer should not be notified
        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {
            foreach ($copyTo as $email) {
                $emailInfo = Mage::getModel('core/email_info');
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams([
            'order'   => $this,
            'comment' => $comment,
            'billing' => $this->getBillingAddress(),
        ]);

        /** @var Mage_Core_Model_Email_Queue $emailQueue */
        $emailQueue = Mage::getModel('core/email_queue');
        $emailQueue->setEntityId($this->getId())
            ->setEntityType(self::ENTITY)
            ->setEventType(self::EMAIL_EVENT_NAME_UPDATE_ORDER)
            ->setIsForceCheck(!$forceMode);
        $mailer->setQueue($emailQueue)->send();

        return $this;
    }

    /**
     * Send email with order update information
     *
     * @param bool   $notifyCustomer
     * @param string $comment
     *
     * @return $this
     */
    public function sendOrderUpdateEmail($notifyCustomer = true, $comment = '')
    {
        $this->queueOrderUpdateEmail($notifyCustomer, $comment, true);
        return $this;
    }

    /**
     * @param  string      $configPath
     * @return array|false
     */
    protected function _getEmails($configPath)
    {
        $data = Mage::getStoreConfig($configPath, $this->getStoreId());
        if (!empty($data)) {
            return explode(',', $data);
        }

        return false;
    }

    /*********************** ADDRESSES ***************************/

    /**
     * @return Mage_Sales_Model_Resource_Order_Address_Collection
     */
    public function getAddressesCollection()
    {
        if (is_null($this->_addresses)) {
            $this->_addresses = Mage::getResourceModel('sales/order_address_collection')
                ->setOrderFilter($this);

            if ($this->getId()) {
                foreach ($this->_addresses as $address) {
                    $address->setOrder($this);
                }
            }
        }

        return $this->_addresses;
    }

    /**
     * @param  int|string                           $addressId
     * @return false|Mage_Sales_Model_Order_Address
     */
    public function getAddressById($addressId)
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getId() == $addressId) {
                return $address;
            }
        }

        return false;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function addAddress(Mage_Sales_Model_Order_Address $address)
    {
        $address->setOrder($this)->setParentId($this->getId());
        if (!$address->getId()) {
            $this->getAddressesCollection()->addItem($address);
        }

        return $this;
    }

    /**
     * @param  array                                           $filterByTypes
     * @param  bool                                            $nonChildrenOnly
     * @return Mage_Sales_Model_Resource_Order_Item_Collection
     */
    public function getItemsCollection($filterByTypes = [], $nonChildrenOnly = false)
    {
        if (is_null($this->_items)) {
            $this->_items = Mage::getResourceModel('sales/order_item_collection')
                ->setOrderFilter($this);

            if ($filterByTypes) {
                $this->_items->filterByTypes($filterByTypes);
            }

            if ($nonChildrenOnly) {
                $this->_items->filterByParent();
            }

            if ($this->getId()) {
                foreach ($this->_items as $item) {
                    $item->setOrder($this);
                }
            }
        }

        return $this->_items;
    }

    /**
     * Get random items collection with related children
     *
     * @param  int                                             $limit
     * @return Mage_Sales_Model_Resource_Order_Item_Collection
     */
    public function getItemsRandomCollection($limit = 1)
    {
        return $this->_getItemsRandomCollection($limit);
    }

    /**
     * Get random items collection without related children
     *
     * @param  int                                             $limit
     * @return Mage_Sales_Model_Resource_Order_Item_Collection
     */
    public function getParentItemsRandomCollection($limit = 1)
    {
        return $this->_getItemsRandomCollection($limit, true);
    }

    /**
     * Get random items collection with or without related children
     *
     * @param  int                                             $limit
     * @param  bool                                            $nonChildrenOnly
     * @return Mage_Sales_Model_Resource_Order_Item_Collection
     */
    protected function _getItemsRandomCollection($limit, $nonChildrenOnly = false)
    {
        $collection = Mage::getModel('sales/order_item')->getCollection()
            ->setOrderFilter($this)
            ->setRandomOrder();

        if ($nonChildrenOnly) {
            $collection->filterByParent();
        }

        $products = [];
        /** @var Mage_Sales_Model_Order_Item $item */
        foreach ($collection as $item) {
            $products[] = $item->getProductId();
        }

        $productsCollection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addIdFilter($products)
            ->setVisibility(Mage::getSingleton('catalog/product_visibility')::getVisibleInSiteIds())
            /* Price data is added to consider item stock status using price index */
            ->addPriceData()
            ->setPageSize($limit)
            ->load();

        foreach ($collection as $item) {
            $product = $productsCollection->getItemById($item->getProductId());
            if ($product) {
                $item->setProduct($product);
            }
        }

        return $collection;
    }

    /**
     * @return Mage_Sales_Model_Order_Item[]
     */
    public function getAllItems()
    {
        $items = [];
        foreach ($this->getItemsCollection() as $item) {
            if (!$item->isDeleted()) {
                $items[] =  $item;
            }
        }

        return $items;
    }

    /**
     * @return Mage_Sales_Model_Order_Item[]
     */
    public function getAllVisibleItems()
    {
        $items = [];
        foreach ($this->getItemsCollection() as $item) {
            if (!$item->isDeleted() && !$item->getParentItemId()) {
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * @param  int                              $itemId
     * @return null|Mage_Sales_Model_Order_Item
     */
    public function getItemById($itemId)
    {
        return $this->getItemsCollection()->getItemById($itemId);
    }

    /**
     * @param  int                              $quoteItemId
     * @return null|Mage_Sales_Model_Order_Item
     */
    public function getItemByQuoteItemId($quoteItemId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getQuoteItemId() == $quoteItemId) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function addItem(Mage_Sales_Model_Order_Item $item)
    {
        $item->setOrder($this);
        if (!$item->getId()) {
            $this->getItemsCollection()->addItem($item);
        }

        return $this;
    }

    /**
     * Whether the order has nominal items only
     *
     * @return bool
     */
    public function isNominal()
    {
        foreach ($this->getAllVisibleItems() as $item) {
            if ($item->getIsNominal() == '0') {
                return false;
            }
        }

        return true;
    }

    /*********************** PAYMENTS ***************************/

    /**
     * @return Mage_Sales_Model_Resource_Order_Payment_Collection
     */
    public function getPaymentsCollection()
    {
        if (is_null($this->_payments)) {
            $this->_payments = Mage::getResourceModel('sales/order_payment_collection')
                ->setOrderFilter($this);

            if ($this->getId()) {
                foreach ($this->_payments as $payment) {
                    $payment->setOrder($this);
                }
            }
        }

        return $this->_payments;
    }

    /**
     * @return Mage_Sales_Model_Order_Payment[]
     */
    public function getAllPayments()
    {
        $payments = [];
        foreach ($this->getPaymentsCollection() as $payment) {
            if (!$payment->isDeleted()) {
                $payments[] =  $payment;
            }
        }

        return $payments;
    }

    /**
     * @param  int                                 $paymentId
     * @return bool|Mage_Sales_Model_Order_Payment
     */
    public function getPaymentById($paymentId)
    {
        foreach ($this->getPaymentsCollection() as $payment) {
            if ($payment->getId() == $paymentId) {
                return $payment;
            }
        }

        return false;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function addPayment(Mage_Sales_Model_Order_Payment $payment)
    {
        $payment->setOrder($this)
            ->setParentId($this->getId());
        if (!$payment->getId()) {
            $this->getPaymentsCollection()->addItem($payment);
        }

        return $this;
    }

    /**
     * @return Mage_Sales_Model_Order_Payment
     */
    public function setPayment(Mage_Sales_Model_Order_Payment $payment)
    {
        if (!$this->getIsMultiPayment() && ($old = $this->getPayment())) {
            $payment->setId($old->getId());
        }

        $this->addPayment($payment);
        return $payment;
    }

    /*********************** STATUSES ***************************/

    /**
     * @param  bool                                                      $reload
     * @return Mage_Sales_Model_Resource_Order_Status_History_Collection
     */
    public function getStatusHistoryCollection($reload = false)
    {
        if (is_null($this->_statusHistory) || $reload) {
            $this->_statusHistory = Mage::getResourceModel('sales/order_status_history_collection')
                ->setOrderFilter($this)
                ->setOrder('created_at', 'desc')
                ->setOrder('entity_id', 'desc');

            if ($this->getId()) {
                foreach ($this->_statusHistory as $status) {
                    $status->setOrder($this);
                }
            }
        }

        return $this->_statusHistory;
    }

    /**
     * Return collection of order status history items.
     *
     * @return Mage_Sales_Model_Order_Status_History[]
     */
    public function getAllStatusHistory()
    {
        $history = [];
        foreach ($this->getStatusHistoryCollection() as $status) {
            if (!$status->isDeleted()) {
                $history[] =  $status;
            }
        }

        return $history;
    }

    /**
     * Return collection of visible on frontend order status history items.
     *
     * @return Mage_Sales_Model_Order_Status_History[]
     */
    public function getVisibleStatusHistory()
    {
        $history = [];
        foreach ($this->getStatusHistoryCollection() as $status) {
            if (!$status->isDeleted() && $status->getComment() && $status->getIsVisibleOnFront()) {
                $history[] =  $status;
            }
        }

        return $history;
    }

    /**
     * @param  int                                         $statusId
     * @return false|Mage_Sales_Model_Order_Status_History
     */
    public function getStatusHistoryById($statusId)
    {
        foreach ($this->getStatusHistoryCollection() as $status) {
            if ($status->getId() == $statusId) {
                return $status;
            }
        }

        return false;
    }

    /**
     * Set the order status history object and the order object to each other
     * Adds the object to the status history collection, which is automatically saved when the order is saved.
     * See the entity_id attribute backend model.
     * Or the history record can be saved standalone after this.
     *
     * @return $this
     */
    public function addStatusHistory(Mage_Sales_Model_Order_Status_History $history)
    {
        $history->setOrder($this);
        $this->setStatus($history->getStatus());
        if (!$history->getId()) {
            $this->getStatusHistoryCollection()->addItem($history);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getRealOrderId()
    {
        $id = $this->getDataByKey('real_order_id');
        if (is_null($id)) {
            return $this->getIncrementId();
        }

        return $id;
    }

    /**
     * Get currency model instance. Will be used currency with which order placed
     *
     * @return Mage_Directory_Model_Currency
     */
    public function getOrderCurrency()
    {
        if (is_null($this->_orderCurrency)) {
            $this->_orderCurrency = Mage::getModel('directory/currency')->load($this->getOrderCurrencyCode());
        }

        return $this->_orderCurrency;
    }

    /**
     * Get formatted price value including order currency rate to order website currency
     *
     * @param  float  $price
     * @param  bool   $addBrackets
     * @return string
     */
    public function formatPrice($price, $addBrackets = false)
    {
        return $this->formatPricePrecision($price, 2, $addBrackets);
    }

    /**
     * @param  float  $price
     * @param  int    $precision
     * @param  bool   $addBrackets
     * @return string
     */
    public function formatPricePrecision($price, $precision, $addBrackets = false)
    {
        return $this->getOrderCurrency()->formatPrecision($price, $precision, [], true, $addBrackets);
    }

    /**
     * Retrieve currency formatted string.
     *
     * @param  float|string $price Numeric value or field name, e.g. "grand_total".
     * @return string
     */
    public function formatPriceTxt($price)
    {
        $price = (float) (is_numeric($price) ? $price : $this->_getData($price));
        return $this->getOrderCurrency()->formatTxt($price);
    }

    /**
     * Retrieve order website currency for working with base prices
     *
     * @return Mage_Directory_Model_Currency
     */
    public function getBaseCurrency()
    {
        if (is_null($this->_baseCurrency)) {
            $this->_baseCurrency = Mage::getModel('directory/currency')->load($this->getBaseCurrencyCode());
        }

        return $this->_baseCurrency;
    }

    /**
     * Retrieve order website currency for working with base prices
     *
     * @return Mage_Directory_Model_Currency
     * @deprecated  please use getBaseCurrency instead
     */
    public function getStoreCurrency()
    {
        return $this->getDataByKey('store_currency');
    }

    /**
     * @param  float  $price
     * @return string
     */
    public function formatBasePrice($price)
    {
        return $this->formatBasePricePrecision($price, 2);
    }

    /**
     * @param  float  $price
     * @param  int    $precision
     * @return string
     */
    public function formatBasePricePrecision($price, $precision)
    {
        return $this->getBaseCurrency()->formatPrecision($price, $precision);
    }

    /**
     * @return bool
     */
    public function isCurrencyDifferent()
    {
        return $this->getOrderCurrencyCode() != $this->getBaseCurrencyCode();
    }

    /**
     * Retrieve order total due value
     *
     * @return float
     */
    public function getTotalDue()
    {
        $total = $this->getGrandTotal() - $this->getTotalPaid();
        $total = Mage::app()->getStore($this->getStoreId())->roundPrice($total);
        return max($total, 0);
    }

    /**
     * Retrieve order total due value
     *
     * @return float
     */
    public function getBaseTotalDue()
    {
        $total = $this->getBaseGrandTotal() - $this->getBaseTotalPaid();
        $total = Mage::app()->getStore($this->getStoreId())->roundPrice($total);
        return max($total, 0);
    }

    /**
     * @param  string          $key
     * @param  null|int|string $index
     * @return float|mixed
     */
    #[Override]
    public function getData($key = '', $index = null)
    {
        if ($key == 'total_due') {
            return $this->getTotalDue();
        }

        if ($key == 'base_total_due') {
            return $this->getBaseTotalDue();
        }

        return parent::getData($key, $index);
    }

    /**
     * Retrieve order invoices collection
     *
     * @return Mage_Sales_Model_Resource_Order_Invoice_Collection
     */
    public function getInvoiceCollection()
    {
        if (is_null($this->_invoices)) {
            $this->_invoices = Mage::getResourceModel('sales/order_invoice_collection')
                ->setOrderFilter($this);

            if ($this->getId()) {
                foreach ($this->_invoices as $invoice) {
                    $invoice->setOrder($this);
                }
            }
        }

        return $this->_invoices;
    }

    /**
     * Retrieve order invoices collection
     *
     * @return Mage_Sales_Model_Resource_Order_Invoice_Collection
     */
    public function getInvoicesCollection()
    {
        return $this->getInvoiceCollection();
    }

    /**
     * Retrieve order shipments collection
     *
     * @return false|Mage_Sales_Model_Resource_Order_Shipment_Collection
     */
    public function getShipmentsCollection()
    {
        if (empty($this->_shipments)) {
            if ($this->getId()) {
                $this->_shipments = Mage::getResourceModel('sales/order_shipment_collection')
                    ->setOrderFilter($this)
                    ->load();
            } else {
                return false;
            }
        }

        return $this->_shipments;
    }

    /**
     * Retrieve order creditmemos collection
     *
     * @return false|Mage_Sales_Model_Order_Creditmemo[]|Mage_Sales_Model_Resource_Order_Creditmemo_Collection
     */
    public function getCreditmemosCollection()
    {
        if (empty($this->_creditmemos)) {
            if ($this->getId()) {
                $this->_creditmemos = Mage::getResourceModel('sales/order_creditmemo_collection')
                    ->setOrderFilter($this)
                    ->load();
            } else {
                return false;
            }
        }

        return $this->_creditmemos;
    }

    /**
     * Retrieve order tracking numbers collection
     *
     * @return Mage_Sales_Model_Resource_Order_Shipment_Track_Collection
     */
    public function getTracksCollection()
    {
        if (empty($this->_tracks)) {
            $this->_tracks = Mage::getResourceModel('sales/order_shipment_track_collection')
                ->setOrderFilter($this);

            if ($this->getId()) {
                $this->_tracks->load();
            }
        }

        return $this->_tracks;
    }

    /**
     * Check order invoices availability
     *
     * @return int
     */
    public function hasInvoices()
    {
        return $this->getInvoiceCollection()->count();
    }

    /**
     * Check order shipments availability
     *
     * @return bool
     */
    public function hasShipments()
    {
        $result = false;
        $shipmentsCollection = $this->getShipmentsCollection();
        if ($shipmentsCollection) {
            return (bool) $shipmentsCollection->count();
        }

        return $result;
    }

    /**
     * Check order creditmemos availability
     *
     * @return bool
     */
    public function hasCreditmemos()
    {
        $result = false;
        $creditmemosCollection = $this->getCreditmemosCollection();
        if ($creditmemosCollection) {
            return (bool) $creditmemosCollection->count();
        }

        return $result;
    }

    /**
     * Retrieve array of related objects
     *
     * Used for order saving
     *
     * @return array
     */
    public function getRelatedObjects()
    {
        return $this->_relatedObjects;
    }

    /**
     * Retrieve customer name
     *
     * @return string
     */
    public function getCustomerName()
    {
        if ($this->getCustomerFirstname()) {
            return Mage::helper('customer')->getFullCustomerName($this);
        }

        return Mage::helper('sales')->__('Guest');
    }

    /**
     * Add New object to related array
     *
     * @return $this
     */
    public function addRelatedObject(Mage_Core_Model_Abstract $object)
    {
        $this->_relatedObjects[] = $object;
        return $this;
    }

    /**
     * Get formatted order created date in store timezone
     *
     * @param  string $format date format type (short|medium|long|full)
     * @return string
     */
    public function getCreatedAtFormated($format)
    {
        return Mage::helper('core')->formatDate($this->getCreatedAtStoreDate(), $format, true);
    }

    /**
     * @return string
     */
    public function getEmailCustomerNote()
    {
        if ($this->getCustomerNoteNotify()) {
            return $this->getCustomerNote();
        }

        return '';
    }

    /**
     * Processing object before save data
     *
     * @return $this
     */
    #[Override]
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $this->_checkState();
        if (!$this->getId()) {
            $store = $this->getStore();
            $name = [$store->getWebsite()->getName(),$store->getGroup()->getName(),$store->getName()];
            $this->setStoreName(implode("\n", $name));
        }

        if (!$this->getIncrementId()) {
            $incrementId = Mage::getSingleton('eav/config')
                ->getEntityType('order')
                ->fetchNewIncrementId($this->getStoreId());
            $this->setIncrementId($incrementId);
        }

        /**
         * Process items dependency for new order
         */
        if (!$this->getId()) {
            $itemsCount = 0;
            foreach ($this->getAllItems() as $item) {
                $parent = $item->getQuoteParentItemId();
                if ($parent && !$item->getParentItem()) {
                    $item->setParentItem($this->getItemByQuoteItemId($parent));
                } elseif (!$parent) {
                    $itemsCount++;
                }
            }

            // Set items count
            $this->setTotalItemCount($itemsCount);
        }

        if ($this->getCustomer()) {
            $this->setCustomerId($this->getCustomer()->getId());
        }

        if ($this->hasBillingAddressId() && $this->getBillingAddressId() === null) {
            $this->unsBillingAddressId();
        }

        if ($this->hasShippingAddressId() && $this->getShippingAddressId() === null) {
            $this->unsShippingAddressId();
        }

        if (!$this->getId()) {
            $this->setData('protect_code', Mage::helper('core')->getRandomString(16));
        }

        if ($this->getStatus() !== $this->getOrigData('status')) {
            Mage::dispatchEvent('order_status_changed_before_save', ['order' => $this]);
        }

        return $this;
    }

    /**
     * Check order state before saving
     */
    protected function _checkState()
    {
        if (!$this->getId()) {
            return $this;
        }

        $userNotification = $this->hasCustomerNoteNotify() ? $this->getCustomerNoteNotify() : null;

        if (!$this->isCanceled()
            && !$this->canUnhold()
            && !$this->canInvoice()
            && !$this->canShip()
        ) {
            if ($this->getBaseGrandTotal() == 0 || $this->canCreditmemo()) {
                if ($this->getState() !== self::STATE_COMPLETE) {
                    $this->_setState(self::STATE_COMPLETE, true, '', $userNotification);
                }
            } elseif ((float) $this->getTotalRefunded() || (!$this->getTotalRefunded()
                && $this->hasForcedCanCreditmemo())
                /**
                 * Order can be closed just in case when we have refunded amount.
                 * In case of "0" grand total order checking ForcedCanCreditmemo flag
                 */
            ) {
                if ($this->getState() !== self::STATE_CLOSED) {
                    $this->_setState(self::STATE_CLOSED, true, '', $userNotification);
                }
            }
        }

        if ($this->getState() == self::STATE_NEW && $this->getIsInProcess()) {
            $this->setState(self::STATE_PROCESSING, true, '', $userNotification);
        }

        return $this;
    }

    /**
     * Save order related objects
     *
     * @inheritDoc
     */
    #[Override]
    protected function _afterSave()
    {
        if ($this->_addresses !== null) {
            $this->_addresses->save();
            $billingAddress = $this->getBillingAddress();
            $attributesForSave = [];
            if ($billingAddress && $this->getBillingAddressId() != $billingAddress->getId()) {
                $this->setBillingAddressId($billingAddress->getId());
                $attributesForSave[] = 'billing_address_id';
            }

            $shippingAddress = $this->getShippingAddress();
            if ($shippingAddress && $this->getShippingAddressId() != $shippingAddress->getId()) {
                $this->setShippingAddressId($shippingAddress->getId());
                $attributesForSave[] = 'shipping_address_id';
            }

            if ($attributesForSave !== []) {
                $this->_getResource()->saveAttribute($this, $attributesForSave);
            }
        }

        if ($this->_items !== null) {
            $this->_items->save();
        }

        if ($this->_payments !== null) {
            $this->_payments->save();
        }

        if ($this->_statusHistory !== null) {
            $this->_statusHistory->save();
        }

        foreach ($this->getRelatedObjects() as $object) {
            $object->save();
        }

        return parent::_afterSave();
    }

    /**
     * @return string
     */
    public function getStoreGroupName()
    {
        $storeId = $this->getStoreId();
        if (is_null($storeId)) {
            return $this->getStoreName(1); // 0 - website name, 1 - store group name, 2 - store name
        }

        return $this->getStore()->getGroup()->getName();
    }

    /**
     * Resets all data in object
     * so after another load it will be complete new object
     *
     * @return $this
     */
    public function reset()
    {
        $this->unsetData();
        $this->_actionFlag = [];
        $this->_addresses = null;
        $this->_items = null;
        $this->_payments = null;
        $this->_statusHistory = null;
        $this->_invoices = null;
        $this->_tracks = null;
        $this->_shipments = null;
        $this->_creditmemos = null;
        $this->_relatedObjects = [];
        $this->_orderCurrency = null;
        $this->_baseCurrency = null;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsNotVirtual()
    {
        return !$this->getIsVirtual();
    }

    /**
     * @return mixed
     */
    public function getFullTaxInfo()
    {
        $rates = Mage::getModel('tax/sales_order_tax')->getCollection()->loadByOrder($this)->toArray();
        return Mage::getSingleton('tax/calculation')->reproduceProcess($rates['items']);
    }

    /**
     * Create new invoice with maximum qty for invoice for each item
     *
     * @param  array                          $qtys
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function prepareInvoice($qtys = [])
    {
        return Mage::getModel('sales/service_order', $this)->prepareInvoice($qtys);
    }

    /**
     * Create new shipment with maximum qty for shipping for each item
     *
     * @param  array                           $qtys
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function prepareShipment($qtys = [])
    {
        return Mage::getModel('sales/service_order', $this)->prepareShipment($qtys);
    }

    /**
     * Check whether order is canceled
     *
     * @return bool
     */
    public function isCanceled()
    {
        return ($this->getState() === self::STATE_CANCELED);
    }

    /**
     * Protect order delete from not admin scope
     * @inheritDoc
     */
    #[Override]
    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        return parent::_beforeDelete();
    }

    public function getAdjustmentNegative(): float
    {
        return (float) $this->_getData('adjustment_negative');
    }

    public function getAdjustmentPositive(): float
    {
        return (float) $this->_getData('adjustment_positive');
    }

    public function getAppliedRuleIds(): ?string
    {
        $value = $this->_getData('applied_rule_ids');
        return $value === null ? null : (string) $value;
    }

    public function getBaseAdjustmentNegative(): float
    {
        return (float) $this->_getData('base_adjustment_negative');
    }

    public function getBaseAdjustmentPositive(): float
    {
        return (float) $this->_getData('base_adjustment_positive');
    }

    public function getBaseCurrencyCode(): string
    {
        return (string) $this->_getData('base_currency_code');
    }

    public function getBaseDiscountAmount(): float
    {
        return (float) $this->_getData('base_discount_amount');
    }

    public function getBaseDiscountCanceled(): float
    {
        return (float) $this->_getData('base_discount_canceled');
    }

    public function getBaseDiscountInvoiced(): float
    {
        return (float) $this->_getData('base_discount_invoiced');
    }

    public function getBaseDiscountRefunded(): float
    {
        return (float) $this->_getData('base_discount_refunded');
    }

    public function getBaseGrandTotal(): float
    {
        return (float) $this->_getData('base_grand_total');
    }

    public function getBaseHiddenTaxAmount(): float
    {
        return (float) $this->_getData('base_hidden_tax_amount');
    }

    public function getBaseHiddenTaxInvoiced(): float
    {
        return (float) $this->_getData('base_hidden_tax_invoiced');
    }

    public function getBaseHiddenTaxRefunded(): float
    {
        return (float) $this->_getData('base_hidden_tax_refunded');
    }

    public function getBaseShippingAmount(): float
    {
        return (float) $this->_getData('base_shipping_amount');
    }

    public function getBaseShippingCanceled(): float
    {
        return (float) $this->_getData('base_shipping_canceled');
    }

    public function getBaseShippingDiscountAmount(): float
    {
        return (float) $this->_getData('base_shipping_discount_amount');
    }

    public function getBaseShippingHiddenTaxAmount(): float
    {
        return (float) $this->_getData('base_shipping_hidden_tax_amount');
    }

    public function getBaseShippingHiddenTaxInvoiced(): float
    {
        return (float) $this->_getData('base_shipping_hidden_tax_invoiced');
    }

    public function getBaseShippingHiddenTaxRefunded(): float
    {
        return (float) $this->_getData('base_shipping_hidden_tax_refunded');
    }

    public function getBaseShippingInclTax(): float
    {
        return (float) $this->_getData('base_shipping_incl_tax');
    }

    public function getBaseShippingInvoiced(): float
    {
        return (float) $this->_getData('base_shipping_invoiced');
    }

    public function getBaseShippingRefunded(): float
    {
        return (float) $this->_getData('base_shipping_refunded');
    }

    public function getBaseShippingTaxAmount(): float
    {
        return (float) $this->_getData('base_shipping_tax_amount');
    }

    public function getBaseShippingTaxInvoiced(): float
    {
        return (float) $this->_getData('base_shipping_tax_invoiced');
    }

    public function getBaseShippingTaxRefunded(): float
    {
        return (float) $this->_getData('base_shipping_tax_refunded');
    }

    public function getBaseSubtotal(): float
    {
        return (float) $this->_getData('base_subtotal');
    }

    public function getBaseSubtotalCanceled(): float
    {
        return (float) $this->_getData('base_subtotal_canceled');
    }

    public function getBaseSubtotalInclTax(): float
    {
        return (float) $this->_getData('base_subtotal_incl_tax');
    }

    public function getBaseSubtotalInvoiced(): float
    {
        return (float) $this->_getData('base_subtotal_invoiced');
    }

    public function getBaseSubtotalRefunded(): float
    {
        return (float) $this->_getData('base_subtotal_refunded');
    }

    public function getBaseTaxAmount(): float
    {
        return (float) $this->_getData('base_tax_amount');
    }

    public function getBaseTaxCanceled(): float
    {
        return (float) $this->_getData('base_tax_canceled');
    }

    public function getBaseTaxInvoiced(): float
    {
        return (float) $this->_getData('base_tax_invoiced');
    }

    public function getBaseTaxRefunded(): float
    {
        return (float) $this->_getData('base_tax_refunded');
    }

    public function getBaseToGlobalRate(): float
    {
        return (float) $this->_getData('base_to_global_rate');
    }

    public function getBaseToOrderRate(): float
    {
        return (float) $this->_getData('base_to_order_rate');
    }

    public function getBaseTotalCanceled(): float
    {
        return (float) $this->_getData('base_total_canceled');
    }

    public function getBaseTotalInvoiced(): float
    {
        return (float) $this->_getData('base_total_invoiced');
    }

    public function getBaseTotalInvoicedCost(): float
    {
        return (float) $this->_getData('base_total_invoiced_cost');
    }

    public function getBaseTotalOfflineRefunded(): float
    {
        return (float) $this->_getData('base_total_offline_refunded');
    }

    public function getBaseTotalOnlineRefunded(): float
    {
        return (float) $this->_getData('base_total_online_refunded');
    }

    public function getBaseTotalPaid(): float
    {
        return (float) $this->_getData('base_total_paid');
    }

    public function getBaseTotalQtyOrdered(): float
    {
        return (float) $this->_getData('base_total_qty_ordered');
    }

    public function getBaseTotalRefunded(): float
    {
        return (float) $this->_getData('base_total_refunded');
    }

    public function getBillingAddressId(): int
    {
        return (int) $this->_getData('billing_address_id');
    }

    public function getCanShipPartially(): int
    {
        return (int) $this->_getData('can_ship_partially');
    }

    public function getCanShipPartiallyItem(): int
    {
        return (int) $this->_getData('can_ship_partially_item');
    }

    public function getCouponCode(): string
    {
        return (string) $this->_getData('coupon_code');
    }

    public function getCustomerDob(): string
    {
        return (string) $this->_getData('customer_dob');
    }

    public function getCustomerEmail(): string
    {
        return (string) $this->_getData('customer_email');
    }

    public function getCustomerFirstname(): string
    {
        return (string) $this->_getData('customer_firstname');
    }

    public function getCustomerGender(): int
    {
        return (int) $this->_getData('customer_gender');
    }

    public function getCustomerGroupId(): int
    {
        return (int) $this->_getData('customer_group_id');
    }

    public function getCustomerId(): int
    {
        return (int) $this->_getData('customer_id');
    }

    public function getCustomerIsGuest(): int
    {
        return (int) $this->_getData('customer_is_guest');
    }

    public function getCustomerLastname(): string
    {
        return (string) $this->_getData('customer_lastname');
    }

    public function getCustomerMiddlename(): string
    {
        return (string) $this->_getData('customer_middlename');
    }

    public function getCustomerNote(): string
    {
        return (string) $this->_getData('customer_note');
    }

    public function getCustomerNoteNotify(): int
    {
        return (int) $this->_getData('customer_note_notify');
    }

    public function getCustomerPrefix(): string
    {
        return (string) $this->_getData('customer_prefix');
    }

    public function getCustomerSuffix(): string
    {
        return (string) $this->_getData('customer_suffix');
    }

    public function getCustomerTaxvat(): string
    {
        return (string) $this->_getData('customer_taxvat');
    }

    public function getDiscountAmount(): float
    {
        return (float) $this->_getData('discount_amount');
    }

    public function getDiscountCanceled(): float
    {
        return (float) $this->_getData('discount_canceled');
    }

    public function getDiscountDescription(): string
    {
        return (string) $this->_getData('discount_description');
    }

    public function getDiscountInvoiced(): float
    {
        return (float) $this->_getData('discount_invoiced');
    }

    public function getDiscountRefunded(): float
    {
        return (float) $this->_getData('discount_refunded');
    }

    public function getEditIncrement(): int
    {
        return (int) $this->_getData('edit_increment');
    }

    public function getEmailSent(): int
    {
        return (int) $this->_getData('email_sent');
    }

    public function getExtCustomerId(): string
    {
        return (string) $this->_getData('ext_customer_id');
    }

    public function getExtOrderId(): string
    {
        return (string) $this->_getData('ext_order_id');
    }

    public function getForcedDoShipmentWithInvoice(): int
    {
        return (int) $this->_getData('forced_do_shipment_with_invoice');
    }

    public function getGiftMessageId(): int
    {
        return (int) $this->_getData('gift_message_id');
    }

    public function getGlobalCurrencyCode(): string
    {
        return (string) $this->_getData('global_currency_code');
    }

    public function getGrandTotal(): float
    {
        return (float) $this->_getData('grand_total');
    }

    public function getHiddenTaxAmount(): float
    {
        return (float) $this->_getData('hidden_tax_amount');
    }

    public function getHiddenTaxInvoiced(): float
    {
        return (float) $this->_getData('hidden_tax_invoiced');
    }

    public function getHiddenTaxRefunded(): float
    {
        return (float) $this->_getData('hidden_tax_refunded');
    }

    public function getHoldBeforeState(): string
    {
        return (string) $this->_getData('hold_before_state');
    }

    public function getHoldBeforeStatus(): string
    {
        return (string) $this->_getData('hold_before_status');
    }

    public function getIncrementId(): string
    {
        return (string) $this->_getData('increment_id');
    }

    public function getIsVirtual(): int
    {
        return (int) $this->_getData('is_virtual');
    }

    public function getOrderCurrencyCode(): string
    {
        return (string) $this->_getData('order_currency_code');
    }

    public function getOriginalIncrementId(): string
    {
        return (string) $this->_getData('original_increment_id');
    }

    public function getPaymentAuthorizationAmount(): float
    {
        return (float) $this->_getData('payment_authorization_amount');
    }

    public function getPaymentAuthorizationExpiration(): int
    {
        return (int) $this->_getData('payment_authorization_expiration');
    }

    public function getPaypalIpnCustomerNotified(): int
    {
        return (int) $this->_getData('paypal_ipn_customer_notified');
    }

    public function getProtectCode(): string
    {
        return (string) $this->_getData('protect_code');
    }

    public function getQuoteAddressId(): int
    {
        return (int) $this->_getData('quote_address_id');
    }

    public function getQuoteBaseGrandTotal(): float
    {
        return (float) $this->_getData('quote_base_grand_total');
    }

    public function getQuoteId(): int
    {
        return (int) $this->_getData('quote_id');
    }

    public function getRelationChildId(): string
    {
        return (string) $this->_getData('relation_child_id');
    }

    public function getRelationChildRealId(): string
    {
        return (string) $this->_getData('relation_child_real_id');
    }

    public function getRelationParentId(): string
    {
        return (string) $this->_getData('relation_parent_id');
    }

    public function getRelationParentRealId(): string
    {
        return (string) $this->_getData('relation_parent_real_id');
    }

    public function getRemoteIp(): string
    {
        return (string) $this->_getData('remote_ip');
    }

    public function getShippingAddressId(): int
    {
        return (int) $this->_getData('shipping_address_id');
    }

    public function getShippingAmount(): float
    {
        return (float) $this->_getData('shipping_amount');
    }

    public function getShippingCanceled(): float
    {
        return (float) $this->_getData('shipping_canceled');
    }

    public function getShippingDescription(): string
    {
        return (string) $this->_getData('shipping_description');
    }

    public function getShippingDiscountAmount(): float
    {
        return (float) $this->_getData('shipping_discount_amount');
    }

    public function getShippingHiddenTaxAmount(): float
    {
        return (float) $this->_getData('shipping_hidden_tax_amount');
    }

    public function getShippingHiddenTaxInvoiced(): float
    {
        return (float) $this->_getData('shipping_hidden_tax_invoiced');
    }

    public function getShippingHiddenTaxRefunded(): float
    {
        return (float) $this->_getData('shipping_hidden_tax_refunded');
    }

    public function getShippingInclTax(): float
    {
        return (float) $this->_getData('shipping_incl_tax');
    }

    public function getShippingInvoiced(): float
    {
        return (float) $this->_getData('shipping_invoiced');
    }

    public function getShippingRefunded(): float
    {
        return (float) $this->_getData('shipping_refunded');
    }

    public function getShippingTaxAmount(): float
    {
        return (float) $this->_getData('shipping_tax_amount');
    }

    public function getShippingTaxInvoiced(): float
    {
        return (float) $this->_getData('shipping_tax_invoiced');
    }

    public function getShippingTaxRefunded(): float
    {
        return (float) $this->_getData('shipping_tax_refunded');
    }

    public function getState(): string
    {
        return (string) $this->_getData('state');
    }

    public function getStatus(): string
    {
        return (string) $this->_getData('status');
    }

    public function getStoreCurrencyCode(): string
    {
        return (string) $this->_getData('store_currency_code');
    }

    public function getStoreId(): int
    {
        return (int) $this->_getData('store_id');
    }

    public function getStoreName(): string
    {
        return (string) $this->_getData('store_name');
    }

    public function getStoreToBaseRate(): float
    {
        return (float) $this->_getData('store_to_base_rate');
    }

    public function getStoreToOrderRate(): float
    {
        return (float) $this->_getData('store_to_order_rate');
    }

    public function getSubtotal(): float
    {
        return (float) $this->_getData('subtotal');
    }

    public function getSubtotalCanceled(): float
    {
        return (float) $this->_getData('subtotal_canceled');
    }

    public function getSubtotalInclTax(): float
    {
        return (float) $this->_getData('subtotal_incl_tax');
    }

    public function getSubtotalInvoiced(): float
    {
        return (float) $this->_getData('subtotal_invoiced');
    }

    public function getSubtotalRefunded(): float
    {
        return (float) $this->_getData('subtotal_refunded');
    }

    public function getTaxAmount(): float
    {
        return (float) $this->_getData('tax_amount');
    }

    public function getTaxCanceled(): float
    {
        return (float) $this->_getData('tax_canceled');
    }

    public function getTaxInvoiced(): float
    {
        return (float) $this->_getData('tax_invoiced');
    }

    public function getTaxRefunded(): float
    {
        return (float) $this->_getData('tax_refunded');
    }

    public function getTotalCanceled(): float
    {
        return (float) $this->_getData('total_canceled');
    }

    public function getTotalInvoiced(): float
    {
        return (float) $this->_getData('total_invoiced');
    }

    public function getTotalItemCount(): int
    {
        return (int) $this->_getData('total_item_count');
    }

    public function getTotalOfflineRefunded(): float
    {
        return (float) $this->_getData('total_offline_refunded');
    }

    public function getTotalOnlineRefunded(): float
    {
        return (float) $this->_getData('total_online_refunded');
    }

    public function getTotalPaid(): float
    {
        return (float) $this->_getData('total_paid');
    }

    public function getTotalQtyOrdered(): float
    {
        return (float) $this->_getData('total_qty_ordered');
    }

    public function getTotalRefunded(): float
    {
        return (float) $this->_getData('total_refunded');
    }

    public function getWeight(): float
    {
        return (float) $this->_getData('weight');
    }

    public function getXForwardedFor(): string
    {
        return (string) $this->_getData('x_forwarded_for');
    }

    public function setAdjustmentNegative(float $value): static
    {
        return $this->setData('adjustment_negative', $value);
    }

    public function setAdjustmentPositive(float $value): static
    {
        return $this->setData('adjustment_positive', $value);
    }

    public function setAppliedRuleIds(string $value): static
    {
        return $this->setData('applied_rule_ids', $value);
    }

    public function setBaseAdjustmentNegative(float $value): static
    {
        return $this->setData('base_adjustment_negative', $value);
    }

    public function setBaseAdjustmentPositive(float $value): static
    {
        return $this->setData('base_adjustment_positive', $value);
    }

    public function setBaseCurrencyCode(string $value): static
    {
        return $this->setData('base_currency_code', $value);
    }

    public function setBaseDiscountAmount(float $value): static
    {
        return $this->setData('base_discount_amount', $value);
    }

    public function setBaseDiscountCanceled(float $value): static
    {
        return $this->setData('base_discount_canceled', $value);
    }

    public function setBaseDiscountInvoiced(float $value): static
    {
        return $this->setData('base_discount_invoiced', $value);
    }

    public function setBaseDiscountRefunded(float $value): static
    {
        return $this->setData('base_discount_refunded', $value);
    }

    public function setBaseGrandTotal(float $value): static
    {
        return $this->setData('base_grand_total', $value);
    }

    public function setBaseHiddenTaxAmount(float $value): static
    {
        return $this->setData('base_hidden_tax_amount', $value);
    }

    public function setBaseHiddenTaxInvoiced(float $value): static
    {
        return $this->setData('base_hidden_tax_invoiced', $value);
    }

    public function setBaseHiddenTaxRefunded(float $value): static
    {
        return $this->setData('base_hidden_tax_refunded', $value);
    }

    public function setBaseShippingAmount(float $value): static
    {
        return $this->setData('base_shipping_amount', $value);
    }

    public function setBaseShippingCanceled(float $value): static
    {
        return $this->setData('base_shipping_canceled', $value);
    }

    public function setBaseShippingDiscountAmount(float $value): static
    {
        return $this->setData('base_shipping_discount_amount', $value);
    }

    public function setBaseShippingHiddenTaxAmount(float $value): static
    {
        return $this->setData('base_shipping_hidden_tax_amount', $value);
    }

    public function setBaseShippingInclTax(float $value): static
    {
        return $this->setData('base_shipping_incl_tax', $value);
    }

    public function setBaseShippingInvoiced(float $value): static
    {
        return $this->setData('base_shipping_invoiced', $value);
    }

    public function setBaseShippingRefunded(float $value): static
    {
        return $this->setData('base_shipping_refunded', $value);
    }

    public function setBaseShippingTaxAmount(float $value): static
    {
        return $this->setData('base_shipping_tax_amount', $value);
    }

    public function setBaseShippingTaxInvoiced(float $value): static
    {
        return $this->setData('base_shipping_tax_invoiced', $value);
    }

    public function setBaseShippingTaxRefunded(float $value): static
    {
        return $this->setData('base_shipping_tax_refunded', $value);
    }

    public function setBaseSubtotal(float $value): static
    {
        return $this->setData('base_subtotal', $value);
    }

    public function setBaseSubtotalCanceled(float $value): static
    {
        return $this->setData('base_subtotal_canceled', $value);
    }

    public function setBaseSubtotalInclTax(float $value): static
    {
        return $this->setData('base_subtotal_incl_tax', $value);
    }

    public function setBaseSubtotalInvoiced(float $value): static
    {
        return $this->setData('base_subtotal_invoiced', $value);
    }

    public function setBaseSubtotalRefunded(float $value): static
    {
        return $this->setData('base_subtotal_refunded', $value);
    }

    public function setBaseTaxAmount(float $value): static
    {
        return $this->setData('base_tax_amount', $value);
    }

    public function setBaseTaxCanceled(float $value): static
    {
        return $this->setData('base_tax_canceled', $value);
    }

    public function setBaseTaxInvoiced(float $value): static
    {
        return $this->setData('base_tax_invoiced', $value);
    }

    public function setBaseTaxRefunded(float $value): static
    {
        return $this->setData('base_tax_refunded', $value);
    }

    public function setBaseToGlobalRate(float $value): static
    {
        return $this->setData('base_to_global_rate', $value);
    }

    public function setBaseToOrderRate(float $value): static
    {
        return $this->setData('base_to_order_rate', $value);
    }

    public function setBaseTotalCanceled(float $value): static
    {
        return $this->setData('base_total_canceled', $value);
    }

    public function setBaseTotalDue(float $value): static
    {
        return $this->setData('base_total_due', $value);
    }

    public function setBaseTotalInvoiced(float $value): static
    {
        return $this->setData('base_total_invoiced', $value);
    }

    public function setBaseTotalInvoicedCost(float $value): static
    {
        return $this->setData('base_total_invoiced_cost', $value);
    }

    public function setBaseTotalOfflineRefunded(float $value): static
    {
        return $this->setData('base_total_offline_refunded', $value);
    }

    public function setBaseTotalOnlineRefunded(float $value): static
    {
        return $this->setData('base_total_online_refunded', $value);
    }

    public function setBaseTotalPaid(float $value): static
    {
        return $this->setData('base_total_paid', $value);
    }

    public function setBaseTotalQtyOrdered(float $value): static
    {
        return $this->setData('base_total_qty_ordered', $value);
    }

    public function setBaseTotalRefunded(float $value): static
    {
        return $this->setData('base_total_refunded', $value);
    }

    public function setBillingAddressId(int $value): static
    {
        return $this->setData('billing_address_id', $value);
    }

    public function setCanShipPartially(int $value): static
    {
        return $this->setData('can_ship_partially', $value);
    }

    public function setCanShipPartiallyItem(int $value): static
    {
        return $this->setData('can_ship_partially_item', $value);
    }

    public function setCouponCode(string $value): static
    {
        return $this->setData('coupon_code', $value);
    }

    public function setCouponRuleName(string $value): static
    {
        return $this->setData('coupon_rule_name', $value);
    }

    public function setCustomerDob(string $value): static
    {
        return $this->setData('customer_dob', $value);
    }

    public function setCustomerEmail(string $value): static
    {
        return $this->setData('customer_email', $value);
    }

    public function setCustomerFirstname(string $value): static
    {
        return $this->setData('customer_firstname', $value);
    }

    public function setCustomerGender(int $value): static
    {
        return $this->setData('customer_gender', $value);
    }

    public function setCustomerGroupId(int $value): static
    {
        return $this->setData('customer_group_id', $value);
    }

    public function setCustomerId(int $value): static
    {
        return $this->setData('customer_id', $value);
    }

    public function setCustomerIsGuest(int $value): static
    {
        return $this->setData('customer_is_guest', $value);
    }

    public function setCustomerLastname(string $value): static
    {
        return $this->setData('customer_lastname', $value);
    }

    public function setCustomerMiddlename(string $value): static
    {
        return $this->setData('customer_middlename', $value);
    }

    public function setCustomerNote(string $value): static
    {
        return $this->setData('customer_note', $value);
    }

    public function setCustomerNoteNotify(int $value): static
    {
        return $this->setData('customer_note_notify', $value);
    }

    public function setCustomerPrefix(string $value): static
    {
        return $this->setData('customer_prefix', $value);
    }

    public function setCustomerSuffix(string $value): static
    {
        return $this->setData('customer_suffix', $value);
    }

    public function setCustomerTaxvat(string $value): static
    {
        return $this->setData('customer_taxvat', $value);
    }

    public function setDiscountAmount(float $value): static
    {
        return $this->setData('discount_amount', $value);
    }

    public function setDiscountCanceled(float $value): static
    {
        return $this->setData('discount_canceled', $value);
    }

    public function setDiscountDescription(string $value): static
    {
        return $this->setData('discount_description', $value);
    }

    public function setDiscountInvoiced(float $value): static
    {
        return $this->setData('discount_invoiced', $value);
    }

    public function setDiscountRefunded(float $value): static
    {
        return $this->setData('discount_refunded', $value);
    }

    public function setEditIncrement(int $value): static
    {
        return $this->setData('edit_increment', $value);
    }

    public function setEmailSent(int $value): static
    {
        return $this->setData('email_sent', $value);
    }

    public function setExtCustomerId(string $value): static
    {
        return $this->setData('ext_customer_id', $value);
    }

    public function setExtOrderId(string $value): static
    {
        return $this->setData('ext_order_id', $value);
    }

    public function setForcedDoShipmentWithInvoice(int $value): static
    {
        return $this->setData('forced_do_shipment_with_invoice', $value);
    }

    public function setGiftMessageId(int $value): static
    {
        return $this->setData('gift_message_id', $value);
    }

    public function setGlobalCurrencyCode(string $value): static
    {
        return $this->setData('global_currency_code', $value);
    }

    public function setGrandTotal(float $value): static
    {
        return $this->setData('grand_total', $value);
    }

    public function setHiddenTaxAmount(float $value): static
    {
        return $this->setData('hidden_tax_amount', $value);
    }

    public function setHiddenTaxInvoiced(float $value): static
    {
        return $this->setData('hidden_tax_invoiced', $value);
    }

    public function setHiddenTaxRefunded(float $value): static
    {
        return $this->setData('hidden_tax_refunded', $value);
    }

    public function setHoldBeforeState(string $value): static
    {
        return $this->setData('hold_before_state', $value);
    }

    public function setHoldBeforeStatus(string $value): static
    {
        return $this->setData('hold_before_status', $value);
    }

    public function setIncrementId(string $value): static
    {
        return $this->setData('increment_id', $value);
    }

    public function setIsVirtual(int $value): static
    {
        return $this->setData('is_virtual', $value);
    }

    public function setOrderCurrencyCode(string $value): static
    {
        return $this->setData('order_currency_code', $value);
    }

    public function setOriginalIncrementId(string $value): static
    {
        return $this->setData('original_increment_id', $value);
    }

    public function setPaymentAuthorizationAmount(float $value): static
    {
        return $this->setData('payment_authorization_amount', $value);
    }

    public function setPaymentAuthorizationExpiration(int $value): static
    {
        return $this->setData('payment_authorization_expiration', $value);
    }

    public function setPaypalIpnCustomerNotified(int $value): static
    {
        return $this->setData('paypal_ipn_customer_notified', $value);
    }

    public function setProtectCode(string $value): static
    {
        return $this->setData('protect_code', $value);
    }

    public function setQuoteAddressId(int $value): static
    {
        return $this->setData('quote_address_id', $value);
    }

    public function setQuoteId(int $value): static
    {
        return $this->setData('quote_id', $value);
    }

    public function setRelationChildId(string $value): static
    {
        return $this->setData('relation_child_id', $value);
    }

    public function setRelationChildRealId(string $value): static
    {
        return $this->setData('relation_child_real_id', $value);
    }

    public function setRelationParentId(string $value): static
    {
        return $this->setData('relation_parent_id', $value);
    }

    public function setRelationParentRealId(string $value): static
    {
        return $this->setData('relation_parent_real_id', $value);
    }

    public function setRemoteIp(string $value): static
    {
        return $this->setData('remote_ip', $value);
    }

    public function setShippingAddressId(int $value): static
    {
        return $this->setData('shipping_address_id', $value);
    }

    public function setShippingAmount(float $value): static
    {
        return $this->setData('shipping_amount', $value);
    }

    public function setShippingCanceled(float $value): static
    {
        return $this->setData('shipping_canceled', $value);
    }

    public function setShippingDescription(string $value): static
    {
        return $this->setData('shipping_description', $value);
    }

    public function setShippingDiscountAmount(float $value): static
    {
        return $this->setData('shipping_discount_amount', $value);
    }

    public function setShippingHiddenTaxAmount(float $value): static
    {
        return $this->setData('shipping_hidden_tax_amount', $value);
    }

    public function setShippingInclTax(float $value): static
    {
        return $this->setData('shipping_incl_tax', $value);
    }

    public function setShippingInvoiced(float $value): static
    {
        return $this->setData('shipping_invoiced', $value);
    }

    public function setShippingMethod(string $value): static
    {
        return $this->setData('shipping_method', $value);
    }

    public function setShippingRefunded(float $value): static
    {
        return $this->setData('shipping_refunded', $value);
    }

    public function setShippingTaxAmount(float $value): static
    {
        return $this->setData('shipping_tax_amount', $value);
    }

    public function setShippingTaxInvoiced(float $value): static
    {
        return $this->setData('shipping_tax_invoiced', $value);
    }

    public function setShippingTaxRefunded(float $value): static
    {
        return $this->setData('shipping_tax_refunded', $value);
    }

    public function setStatus(string $value): static
    {
        return $this->setData('status', $value);
    }

    public function setStoreCurrencyCode(string $value): static
    {
        return $this->setData('store_currency_code', $value);
    }

    public function setStoreId(int $value): static
    {
        return $this->setData('store_id', $value);
    }

    public function setStoreName(string $value): static
    {
        return $this->setData('store_name', $value);
    }

    public function setStoreToBaseRate(float $value): static
    {
        return $this->setData('store_to_base_rate', $value);
    }

    public function setStoreToOrderRate(float $value): static
    {
        return $this->setData('store_to_order_rate', $value);
    }

    public function setSubtotal(float $value): static
    {
        return $this->setData('subtotal', $value);
    }

    public function setSubtotalCanceled(float $value): static
    {
        return $this->setData('subtotal_canceled', $value);
    }

    public function setSubtotalInclTax(float $value): static
    {
        return $this->setData('subtotal_incl_tax', $value);
    }

    public function setSubtotalInvoiced(float $value): static
    {
        return $this->setData('subtotal_invoiced', $value);
    }

    public function setSubtotalRefunded(float $value): static
    {
        return $this->setData('subtotal_refunded', $value);
    }

    public function setTaxAmount(float $value): static
    {
        return $this->setData('tax_amount', $value);
    }

    public function setTaxCanceled(float $value): static
    {
        return $this->setData('tax_canceled', $value);
    }

    public function setTaxInvoiced(float $value): static
    {
        return $this->setData('tax_invoiced', $value);
    }

    public function setTaxRefunded(float $value): static
    {
        return $this->setData('tax_refunded', $value);
    }

    public function setTotalCanceled(float $value): static
    {
        return $this->setData('total_canceled', $value);
    }

    public function setTotalDue(float $value): static
    {
        return $this->setData('total_due', $value);
    }

    public function setTotalInvoiced(float $value): static
    {
        return $this->setData('total_invoiced', $value);
    }

    public function setTotalItemCount(int $value): static
    {
        return $this->setData('total_item_count', $value);
    }

    public function setTotalOfflineRefunded(float $value): static
    {
        return $this->setData('total_offline_refunded', $value);
    }

    public function setTotalOnlineRefunded(float $value): static
    {
        return $this->setData('total_online_refunded', $value);
    }

    public function setTotalPaid(float $value): static
    {
        return $this->setData('total_paid', $value);
    }

    public function setTotalQtyOrdered(float $value): static
    {
        return $this->setData('total_qty_ordered', $value);
    }

    public function setTotalRefunded(float $value): static
    {
        return $this->setData('total_refunded', $value);
    }

    public function setWeight(float $value): static
    {
        return $this->setData('weight', $value);
    }

    public function setXForwardedFor(string $value): static
    {
        return $this->setData('x_forwarded_for', $value);
    }
}
