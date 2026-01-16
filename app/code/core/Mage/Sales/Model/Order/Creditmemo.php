<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Order creditmemo model
 *
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Resource_Order_Creditmemo _getResource()
 * @method float                                      getAdjustment()
 * @method float                                      getAdjustmentNegative()
 * @method float                                      getAdjustmentPositive()
 * @method float                                      getBaseAdjustment()
 * @method float                                      getBaseAdjustmentNegative()
 * @method float                                      getBaseAdjustmentPositive()
 * @method float                                      getBaseCost()
 * @method string                                     getBaseCurrencyCode()
 * @method float                                      getBaseCustomerBalanceReturnMax()
 * @method float                                      getBaseDiscountAmount()
 * @method float                                      getBaseGrandTotal()
 * @method float                                      getBaseHiddenTaxAmount()
 * @method float                                      getBaseShippingAmount()
 * @method float                                      getBaseShippingHiddenTaxAmount()
 * @method float                                      getBaseShippingInclTax()
 * @method float                                      getBaseShippingTaxAmount()
 * @method float                                      getBaseSubtotal()
 * @method float                                      getBaseSubtotalInclTax()
 * @method float                                      getBaseTaxAmount()
 * @method float                                      getBaseToGlobalRate()
 * @method float                                      getBaseToOrderRate()
 * @method int                                        getBillingAddressId()
 * @method getCanVoidFlag()
 * @method Mage_Sales_Model_Resource_Order_Creditmemo_Collection getCollection()
 * @method int                                                   getCreditmemoStatus()
 * @method string                                                getCybersourceToken()
 * @method float                                                 getDiscountAmount()
 * @method bool                                                  getDoTransaction()
 * @method int                                                   getEmailSent()
 * @method string                                                getGlobalCurrencyCode()
 * @method float                                                 getGrandTotal()
 * @method float                                                 getHiddenTaxAmount()
 * @method string                                                getIncrementId()
 * @method Mage_Sales_Model_Order_Invoice                        getInvoice()
 * @method int                                                   getInvoiceId()
 * @method bool                                                  getOfflineRequested()
 * @method string                                                getOrderCurrencyCode()
 * @method int                                                   getOrderId()
 * @method bool                                                  getPaymentRefundDisallowed()
 * @method Mage_Sales_Model_Resource_Order_Creditmemo            getResource()
 * @method Mage_Sales_Model_Resource_Order_Creditmemo_Collection getResourceCollection()
 * @method int                                                   getShippingAddressId()
 * @method float                                                 getShippingAmount()
 * @method float                                                 getShippingHiddenTaxAmount()
 * @method float                                                 getShippingInclTax()
 * @method float                                                 getShippingTaxAmount()
 * @method int                                                   getState()
 * @method string                                                getStoreCurrencyCode()
 * @method float                                                 getStoreToBaseRate()
 * @method float                                                 getStoreToOrderRate()
 * @method float                                                 getSubtotal()
 * @method float                                                 getSubtotalInclTax()
 * @method float                                                 getTaxAmount()
 * @method string                                                getTransactionId()
 * @method bool                                                  hasBaseShippingAmount()
 * @method $this                                                 setAdjustment(float $value)
 * @method $this                                                 setAutomaticallyCreated(bool $value)
 * @method $this                                                 setBaseAdjustment(float $value)
 * @method $this                                                 setBaseAdjustmentNegative(float $value)
 * @method $this                                                 setBaseAdjustmentPositive(float $value)
 * @method $this                                                 setBaseCost(float $value)
 * @method $this                                                 setBaseCurrencyCode(string $value)
 * @method Mage_Sales_Model_Order_Invoice                        setBaseCustomerBalanceTotalRefunded(float $value)
 * @method $this                                                 setBaseDiscountAmount(float $value)
 * @method $this                                                 setBaseGrandTotal(float $value)
 * @method $this                                                 setBaseHiddenTaxAmount(float $value)
 * @method $this                                                 setBaseShippingAmount(float $value)
 * @method $this                                                 setBaseShippingHiddenTaxAmount(float $value)
 * @method Mage_Sales_Model_Order_Invoice                        setBaseShippingInclTax(float $value)
 * @method $this                                                 setBaseShippingTaxAmount(float $value)
 * @method $this                                                 setBaseSubtotal(float $value)
 * @method $this                                                 setBaseSubtotalInclTax(float $value)
 * @method $this                                                 setBaseTaxAmount(float $value)
 * @method $this                                                 setBaseToGlobalRate(float $value)
 * @method $this                                                 setBaseToOrderRate(float $value)
 * @method $this                                                 setBillingAddressId(int $value)
 * @method Mage_Sales_Model_Order_Invoice                        setBsCustomerBalTotalRefunded(float $value)
 * @method $this                                                 setCanVoidFlag(bool $value)
 * @method $this                                                 setCommentText(string $value)
 * @method $this                                                 setCreditmemoStatus(int $value)
 * @method Mage_Sales_Model_Order_Invoice                        setCustomerBalanceRefundFlag(bool $value)
 * @method $this                                                 setCustomerId(int $value)
 * @method $this                                                 setCybersourceToken(string $value)
 * @method $this                                                 setDiscountAmount(float $value)
 * @method Mage_Sales_Model_Order_Invoice                        setDoTransaction(bool $value)
 * @method $this                                                 setEmailSent(int $value)
 * @method $this                                                 setGlobalCurrencyCode(string $value)
 * @method $this                                                 setGrandTotal(float $value)
 * @method $this                                                 setHiddenTaxAmount(float $value)
 * @method $this                                                 setIncrementId(string $value)
 * @method $this                                                 setInvoice(Mage_Sales_Model_Order_Invoice $value)
 * @method $this                                                 setInvoiceId(int $value)
 * @method $this                                                 setOrderCurrencyCode(string $value)
 * @method $this                                                 setOrderId(int $value)
 * @method $this                                                 setPaymentRefundDisallowed(float $value)
 * @method $this                                                 setShippingAddressId(int $value)
 * @method $this                                                 setShippingHiddenTaxAmount(float $value)
 * @method $this                                                 setShippingInclTax(float $value)
 * @method $this                                                 setShippingTaxAmount(float $value)
 * @method $this                                                 setState(int $value)
 * @method $this                                                 setStoreCurrencyCode(string $value)
 * @method $this                                                 setStoreId(int $value)
 * @method $this                                                 setStoreToBaseRate(float $value)
 * @method $this                                                 setStoreToOrderRate(float $value)
 * @method $this                                                 setSubtotal(float $value)
 * @method $this                                                 setSubtotalInclTax(float $value)
 * @method $this                                                 setTaxAmount(float $value)
 * @method $this                                                 setTotalQty(float $value)
 * @method $this                                                 setTransactionId(string $value)
 */
class Mage_Sales_Model_Order_Creditmemo extends Mage_Sales_Model_Abstract
{
    public const STATE_OPEN        = 1;

    public const STATE_REFUNDED    = 2;

    public const STATE_CANCELED    = 3;

    public const XML_PATH_EMAIL_TEMPLATE               = 'sales_email/creditmemo/template';

    public const XML_PATH_EMAIL_GUEST_TEMPLATE         = 'sales_email/creditmemo/guest_template';

    public const XML_PATH_EMAIL_IDENTITY               = 'sales_email/creditmemo/identity';

    public const XML_PATH_EMAIL_COPY_TO                = 'sales_email/creditmemo/copy_to';

    public const XML_PATH_EMAIL_COPY_METHOD            = 'sales_email/creditmemo/copy_method';

    public const XML_PATH_EMAIL_ENABLED                = 'sales_email/creditmemo/enabled';

    public const XML_PATH_UPDATE_EMAIL_TEMPLATE        = 'sales_email/creditmemo_comment/template';

    public const XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE  = 'sales_email/creditmemo_comment/guest_template';

    public const XML_PATH_UPDATE_EMAIL_IDENTITY        = 'sales_email/creditmemo_comment/identity';

    public const XML_PATH_UPDATE_EMAIL_COPY_TO         = 'sales_email/creditmemo_comment/copy_to';

    public const XML_PATH_UPDATE_EMAIL_COPY_METHOD     = 'sales_email/creditmemo_comment/copy_method';

    public const XML_PATH_UPDATE_EMAIL_ENABLED         = 'sales_email/creditmemo_comment/enabled';

    public const REPORT_DATE_TYPE_ORDER_CREATED        = 'order_created';

    public const REPORT_DATE_TYPE_REFUND_CREATED       = 'refund_created';

    /**
     * Identifier for order history item
     */
    public const HISTORY_ENTITY_NAME = 'creditmemo';

    protected static $_states;

    /**
     * @var Mage_Sales_Model_Order_Creditmemo_Item[]|Mage_Sales_Model_Resource_Order_Creditmemo_Item_Collection
     */
    protected $_items;

    /**
     * @var Mage_Sales_Model_Order
     */
    protected $_order;

    /**
     * @var null|Mage_Sales_Model_Order_Creditmemo_Comment[]|Mage_Sales_Model_Resource_Order_Creditmemo_Comment_Collection
     */
    protected $_comments;

    /**
     * Calculator instances for delta rounding of prices
     *
     * @var array
     */
    protected $_calculators = [];

    protected $_eventPrefix = 'sales_order_creditmemo';

    protected $_eventObject = 'creditmemo';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sales/order_creditmemo');
    }

    /**
     * Init mapping array of short fields to its full names
     *
     * @return $this
     */
    protected function _initOldFieldsMap()
    {
        return $this;
    }

    /**
     * Retrieve Creditmemo configuration model
     *
     * @return Mage_Sales_Model_Order_Creditmemo_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('sales/order_creditmemo_config');
    }

    /**
     * Retrieve creditmemo store instance
     *
     * @return Mage_Core_Model_Store
     * @throws Mage_Core_Exception
     */
    public function getStore()
    {
        return $this->getOrder()->getStore();
    }

    /**
     * Declare order for creditmemo
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        $this->setOrderId($order->getId())
            ->setStoreId($order->getStoreId());
        return $this;
    }

    /**
     * Retrieve the order the creditmemo for created for
     *
     * @return Mage_Sales_Model_Order
     * @throws Mage_Core_Exception
     */
    public function getOrder()
    {
        if (!$this->_order instanceof Mage_Sales_Model_Order) {
            $this->_order = Mage::getModel('sales/order')->load($this->getOrderId());
        }

        return $this->_order->setHistoryEntityName(self::HISTORY_ENTITY_NAME);
    }

    /**
     * Retrieve billing address
     *
     * @return Mage_Sales_Model_Order_Address
     * @throws Mage_Core_Exception
     */
    public function getBillingAddress()
    {
        return $this->getOrder()->getBillingAddress();
    }

    /**
     * Retrieve shipping address
     *
     * @return Mage_Sales_Model_Order_Address
     * @throws Mage_Core_Exception
     */
    public function getShippingAddress()
    {
        return $this->getOrder()->getShippingAddress();
    }

    /**
     * @return Mage_Sales_Model_Resource_Order_Creditmemo_Item_Collection
     * @throws Mage_Core_Exception
     */
    public function getItemsCollection()
    {
        if (is_null($this->_items)) {
            $this->_items = Mage::getResourceModel('sales/order_creditmemo_item_collection')
                ->setCreditmemoFilter($this->getId());

            if ($this->getId()) {
                foreach ($this->_items as $item) {
                    $item->setCreditmemo($this);
                }
            }
        }

        return $this->_items;
    }

    /**
     * @return Mage_Sales_Model_Order_Creditmemo_Item[]
     * @throws Mage_Core_Exception
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
     * @param  int                 $itemId
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function getItemById($itemId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getId() == $itemId) {
                return $item;
            }
        }

        return false;
    }

    /**
     * Returns credit memo item by its order id
     *
     * @param  int                                         $orderId
     * @return bool|Mage_Sales_Model_Order_Creditmemo_Item
     * @throws Mage_Core_Exception
     */
    public function getItemByOrderId($orderId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getOrderItemId() == $orderId) {
                return $item;
            }
        }

        return false;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function addItem(Mage_Sales_Model_Order_Creditmemo_Item $item)
    {
        $item->setCreditmemo($this)
            ->setParentId($this->getId())
            ->setStoreId($this->getStoreId());
        if (!$item->getId()) {
            $this->getItemsCollection()->addItem($item);
        }

        return $this;
    }

    /**
     * Creditmemo totals collecting
     *
     * @return $this
     */
    public function collectTotals()
    {
        foreach ($this->getConfig()->getTotalModels() as $model) {
            $model->collect($this);
        }

        return $this;
    }

    /**
     * Round price considering delta
     *
     * @param  float               $price
     * @param  string              $type
     * @param  bool                $negative Indicates if we perform addition (true) or subtraction (false) of rounded value
     * @return float
     * @throws Mage_Core_Exception
     */
    public function roundPrice($price, $type = 'regular', $negative = false)
    {
        if ($price) {
            if (!isset($this->_calculators[$type])) {
                $this->_calculators[$type] = Mage::getModel('core/calculator', $this->getStore());
            }

            $price = $this->_calculators[$type]->deltaRound($price, $negative);
        }

        return $price;
    }

    /**
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function canRefund()
    {
        if ($this->getState() != self::STATE_CANCELED
            && $this->getState() != self::STATE_REFUNDED
            && $this->getOrder()->getPayment()->canRefund()
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check creditmemo cancel action availability
     *
     * @return bool
     */
    public function canCancel()
    {
        return $this->getState() == self::STATE_OPEN;
    }

    /**
     * Check invice void action availability
     *
     * @return false
     */
    public function canVoid()
    {
        return false;
    }

    /**
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function refund()
    {
        Mage::dispatchEvent('sales_order_creditmemo_refund_before', [$this->_eventObject => $this]);

        $this->setState(self::STATE_REFUNDED);
        $orderRefund = Mage::app()->getStore()->roundPrice(
            $this->getOrder()->getTotalRefunded() + $this->getGrandTotal(),
        );
        $baseOrderRefund = Mage::app()->getStore()->roundPrice(
            $this->getOrder()->getBaseTotalRefunded() + $this->getBaseGrandTotal(),
        );

        if ($baseOrderRefund > Mage::app()->getStore()->roundPrice($this->getOrder()->getBaseTotalPaid())) {
            $baseAvailableRefund = $this->getOrder()->getBaseTotalPaid() - $this->getOrder()->getBaseTotalRefunded();

            Mage::throwException(
                Mage::helper('sales')->__('Maximum amount available to refund is %s', $this->getOrder()->formatBasePrice($baseAvailableRefund)),
            );
        }

        $order = $this->getOrder();
        $order->setBaseTotalRefunded($baseOrderRefund);
        $order->setTotalRefunded($orderRefund);

        $order->setBaseSubtotalRefunded($order->getBaseSubtotalRefunded() + $this->getBaseSubtotal());
        $order->setSubtotalRefunded($order->getSubtotalRefunded() + $this->getSubtotal());

        $order->setBaseTaxRefunded($order->getBaseTaxRefunded() + $this->getBaseTaxAmount());
        $order->setTaxRefunded($order->getTaxRefunded() + $this->getTaxAmount());
        $order->setBaseHiddenTaxRefunded($order->getBaseHiddenTaxRefunded() + $this->getBaseHiddenTaxAmount());
        $order->setHiddenTaxRefunded($order->getHiddenTaxRefunded() + $this->getHiddenTaxAmount());

        $order->setBaseShippingRefunded($order->getBaseShippingRefunded() + $this->getBaseShippingAmount());
        $order->setShippingRefunded($order->getShippingRefunded() + $this->getShippingAmount());

        $order->setBaseShippingTaxRefunded($order->getBaseShippingTaxRefunded() + $this->getBaseShippingTaxAmount());
        $order->setShippingTaxRefunded($order->getShippingTaxRefunded() + $this->getShippingTaxAmount());

        $order->setAdjustmentPositive($order->getAdjustmentPositive() + $this->getAdjustmentPositive());
        $order->setBaseAdjustmentPositive($order->getBaseAdjustmentPositive() + $this->getBaseAdjustmentPositive());

        $order->setAdjustmentNegative($order->getAdjustmentNegative() + $this->getAdjustmentNegative());
        $order->setBaseAdjustmentNegative($order->getBaseAdjustmentNegative() + $this->getBaseAdjustmentNegative());

        $order->setDiscountRefunded($order->getDiscountRefunded() + $this->getDiscountAmount());
        $order->setBaseDiscountRefunded($order->getBaseDiscountRefunded() + $this->getBaseDiscountAmount());

        if ($this->getInvoice()) {
            $this->getInvoice()->setIsUsedForRefund(true);
            $this->getInvoice()->setBaseTotalRefunded(
                $this->getInvoice()->getBaseTotalRefunded() + $this->getBaseGrandTotal(),
            );
            $this->setInvoiceId($this->getInvoice()->getId());
        }

        if (!$this->getPaymentRefundDisallowed()) {
            $order->getPayment()->refund($this);
        }

        Mage::dispatchEvent('sales_order_creditmemo_refund', [$this->_eventObject => $this]);
        return $this;
    }

    /**
     * Cancel Creditmemo action
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function cancel()
    {
        $this->setState(self::STATE_CANCELED);
        foreach ($this->getAllItems() as $item) {
            $item->cancel();
        }

        $this->getOrder()->getPayment()->cancelCreditmemo($this);

        if ($this->getTransactionId()) {
            $this->getOrder()->setTotalOnlineRefunded(
                $this->getOrder()->getTotalOnlineRefunded() - $this->getGrandTotal(),
            );
            $this->getOrder()->setBaseTotalOnlineRefunded(
                $this->getOrder()->getBaseTotalOnlineRefunded() - $this->getBaseGrandTotal(),
            );
        } else {
            $this->getOrder()->setTotalOfflineRefunded(
                $this->getOrder()->getTotalOfflineRefunded() - $this->getGrandTotal(),
            );
            $this->getOrder()->setBaseTotalOfflineRefunded(
                $this->getOrder()->getBaseTotalOfflineRefunded() - $this->getBaseGrandTotal(),
            );
        }

        $this->getOrder()->setBaseSubtotalRefunded(
            $this->getOrder()->getBaseSubtotalRefunded() - $this->getBaseSubtotal(),
        );
        $this->getOrder()->setSubtotalRefunded($this->getOrder()->getSubtotalRefunded() - $this->getSubtotal());

        $this->getOrder()->setBaseTaxRefunded($this->getOrder()->getBaseTaxRefunded() - $this->getBaseTaxAmount());
        $this->getOrder()->setTaxRefunded($this->getOrder()->getTaxRefunded() - $this->getTaxAmount());

        $this->getOrder()->setBaseShippingRefunded(
            $this->getOrder()->getBaseShippingRefunded() - $this->getBaseShippingAmount(),
        );
        $this->getOrder()->setShippingRefunded($this->getOrder()->getShippingRefunded() - $this->getShippingAmount());

        Mage::dispatchEvent('sales_order_creditmemo_cancel', [$this->_eventObject => $this]);
        return $this;
    }

    /**
     * Register creditmemo
     *
     * Apply to order, order items etc.
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function register()
    {
        if ($this->getId()) {
            Mage::throwException(
                Mage::helper('sales')->__('Cannot register an existing credit memo.'),
            );
        }

        foreach ($this->getAllItems() as $item) {
            if ($item->getQty() > 0) {
                $item->register();
            } else {
                $item->isDeleted(true);
            }
        }

        $this->setDoTransaction(true);
        if ($this->getOfflineRequested()) {
            $this->setDoTransaction(false);
        }

        $this->refund();

        if ($this->getDoTransaction()) {
            $this->getOrder()->setTotalOnlineRefunded(
                $this->getOrder()->getTotalOnlineRefunded() + $this->getGrandTotal(),
            );
            $this->getOrder()->setBaseTotalOnlineRefunded(
                $this->getOrder()->getBaseTotalOnlineRefunded() + $this->getBaseGrandTotal(),
            );
        } else {
            $this->getOrder()->setTotalOfflineRefunded(
                $this->getOrder()->getTotalOfflineRefunded() + $this->getGrandTotal(),
            );
            $this->getOrder()->setBaseTotalOfflineRefunded(
                $this->getOrder()->getBaseTotalOfflineRefunded() + $this->getBaseGrandTotal(),
            );
        }

        $this->getOrder()->setBaseTotalInvoicedCost(
            $this->getOrder()->getBaseTotalInvoicedCost() - $this->getBaseCost(),
        );

        $state = $this->getState();
        if (is_null($state)) {
            $this->setState(self::STATE_OPEN);
        }

        return $this;
    }

    /**
     * Retrieve Creditmemo states array
     *
     * @return array
     */
    public static function getStates()
    {
        if (is_null(self::$_states)) {
            self::$_states = [
                self::STATE_OPEN       => Mage::helper('sales')->__('Pending'),
                self::STATE_REFUNDED   => Mage::helper('sales')->__('Refunded'),
                self::STATE_CANCELED   => Mage::helper('sales')->__('Canceled'),
            ];
        }

        return self::$_states;
    }

    /**
     * Retrieve Creditmemo state name by state identifier
     *
     * @param  int    $stateId
     * @return string
     */
    public function getStateName($stateId = null)
    {
        if (is_null($stateId)) {
            $stateId = $this->getState();
        }

        if (is_null(self::$_states)) {
            self::getStates();
        }

        return self::$_states[$stateId] ?? Mage::helper('sales')->__('Unknown State');
    }

    /**
     * @param  float $amount
     * @return $this
     */
    public function setShippingAmount($amount)
    {
        $this->setData('shipping_amount', $amount);
        return $this;
    }

    /**
     * @param  float               $amount
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function setAdjustmentPositive($amount)
    {
        $amount = trim((string) $amount);
        if (str_ends_with($amount, '%')) {
            $amount = (float) substr($amount, 0, -1);
            $amount = $this->getOrder()->getGrandTotal() * $amount / 100;
        }

        $amount = $this->getStore()->roundPrice($amount);
        $this->setData('base_adjustment_positive', $amount);

        $amount = $this->getStore()->roundPrice(
            $amount * $this->getOrder()->getStoreToOrderRate(),
        );
        $this->setData('adjustment_positive', $amount);
        return $this;
    }

    /**
     * @param  float               $amount
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function setAdjustmentNegative($amount)
    {
        $amount = trim((string) $amount);
        if (str_ends_with($amount, '%')) {
            $amount = (float) substr($amount, 0, -1);
            $amount = $this->getOrder()->getGrandTotal() * $amount / 100;
        }

        $amount = $this->getStore()->roundPrice($amount);
        $this->setData('base_adjustment_negative', $amount);

        $amount = $this->getStore()->roundPrice(
            $amount * $this->getOrder()->getStoreToOrderRate(),
        );
        $this->setData('adjustment_negative', $amount);
        return $this;
    }

    /**
     * Adds comment to credit memo with additional possibility to send it to customer via email
     * and show it in customer account
     *
     * @param  string    $comment
     * @param  bool      $notify
     * @param  bool      $visibleOnFront
     * @return $this
     * @throws Exception
     */
    public function addComment($comment, $notify = false, $visibleOnFront = false)
    {
        if (!($comment instanceof Mage_Sales_Model_Order_Creditmemo_Comment)) {
            $comment = Mage::getModel('sales/order_creditmemo_comment')
                ->setComment($comment)
                ->setIsCustomerNotified($notify)
                ->setIsVisibleOnFront($visibleOnFront);
        }

        $comment->setCreditmemo($this)
            ->setParentId($this->getId())
            ->setStoreId($this->getStoreId());
        if (!$comment->getId()) {
            $this->getCommentsCollection()->addItem($comment);
        }

        $this->_hasDataChanges = true;
        return $this;
    }

    /**
     * @param  bool                                                        $reload
     * @return Mage_Sales_Model_Resource_Order_Comment_Collection_Abstract
     * @throws Mage_Core_Exception
     */
    public function getCommentsCollection($reload = false)
    {
        if (is_null($this->_comments) || $reload) {
            $this->_comments = Mage::getResourceModel('sales/order_creditmemo_comment_collection')
                ->setCreditmemoFilter($this->getId())
                ->setCreatedAtOrder();
            /**
             * When credit memo created with adding comment,
             * comments collection must be loaded before we added this comment.
             */
            $this->_comments->load();

            if ($this->getId()) {
                foreach ($this->_comments as $comment) {
                    $comment->setCreditmemo($this);
                }
            }
        }

        return $this->_comments;
    }

    /**
     * Send email with creditmemo data
     *
     * @param  bool                            $notifyCustomer
     * @param  string                          $comment
     * @return $this
     * @throws Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function sendEmail($notifyCustomer = true, $comment = '')
    {
        $order = $this->getOrder();
        $storeId = $order->getStore()->getId();

        if (!Mage::helper('sales')->canSendNewCreditmemoEmail($storeId)) {
            return $this;
        }

        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $storeId);
        // Check if at least one recipient is found
        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }

        // Start store emulation process
        if ($storeId != Mage::app()->getStore()->getId()) {
            $appEmulation = Mage::getSingleton('core/app_emulation');
            $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);
        }

        try {
            // Retrieve specified view block from appropriate design package (depends on emulated store)
            $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
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
        if ($order->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $storeId);
            $customerName = $order->getCustomerName();
        }

        $mailer = Mage::getModel('core/email_template_mailer');
        if ($notifyCustomer) {
            $emailInfo = Mage::getModel('core/email_info');
            $emailInfo->addTo($order->getCurrentCustomerEmail(), $customerName);
            if ($copyTo && $copyMethod === 'bcc') {
                // Add bcc to customer email
                foreach ($copyTo as $email) {
                    $emailInfo->addBcc($email);
                }
            }

            $mailer->addEmailInfo($emailInfo);
        }

        // Email copies are sent as separated emails if their copy method is 'copy' or a customer should not be notified
        if ($copyTo && ($copyMethod === 'copy' || !$notifyCustomer)) {
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
            'order'        => $order,
            'creditmemo'   => $this,
            'comment'      => $comment,
            'billing'      => $order->getBillingAddress(),
            'payment_html' => $paymentBlockHtml,
        ]);
        $mailer->send();

        if ($notifyCustomer) {
            $this->setEmailSent(true);
            $this->_getResource()->saveAttribute($this, 'email_sent');
        }

        return $this;
    }

    /**
     * Send email with creditmemo update information
     *
     * @param  bool                $notifyCustomer
     * @param  string              $comment
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function sendUpdateEmail($notifyCustomer = true, $comment = '')
    {
        $order = $this->getOrder();
        $storeId = $order->getStore()->getId();

        if (!Mage::helper('sales')->canSendCreditmemoCommentEmail($storeId)) {
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
        if ($order->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_TEMPLATE, $storeId);
            $customerName = $order->getCustomerName();
        }

        $mailer = Mage::getModel('core/email_template_mailer');
        if ($notifyCustomer) {
            $emailInfo = Mage::getModel('core/email_info');
            $emailInfo->addTo($order->getCurrentCustomerEmail(), $customerName);
            if ($copyTo && $copyMethod === 'bcc') {
                // Add bcc to customer email
                foreach ($copyTo as $email) {
                    $emailInfo->addBcc($email);
                }
            }

            $mailer->addEmailInfo($emailInfo);
        }

        // Email copies are sent as separated emails if their copy method is 'copy' or a customer should not be notified
        if ($copyTo && ($copyMethod === 'copy' || !$notifyCustomer)) {
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
            'order'      => $order,
            'creditmemo' => $this,
            'comment'    => $comment,
            'billing'    => $order->getBillingAddress(),
        ]);
        $mailer->send();

        return $this;
    }

    /**
     * @param  string     $configPath
     * @return array|bool
     */
    protected function _getEmails($configPath)
    {
        $data = Mage::getStoreConfig($configPath, $this->getStoreId());
        if (!empty($data)) {
            return explode(',', $data);
        }

        return false;
    }

    /**
     * @return Mage_Sales_Model_Abstract
     * @throws Mage_Core_Exception
     */
    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        return parent::_beforeDelete();
    }

    /**
     * After save object manipulations
     *
     * @inheritDoc
     * @throws Throwable
     */
    protected function _afterSave()
    {
        if ($this->_items != null) {
            foreach ($this->_items as $item) {
                $item->save();
            }
        }

        if ($this->_comments != null) {
            foreach ($this->_comments as $comment) {
                $comment->save();
            }
        }

        return parent::_afterSave();
    }

    /**
     * Before object save manipulations
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getOrderId() && $this->getOrder()) {
            $this->setOrderId($this->getOrder()->getId());
            $this->setBillingAddressId($this->getOrder()->getBillingAddress()->getId());
        }

        return $this;
    }

    /**
     * Get creditmemos collection filtered by $filter
     *
     * @param  null|array                                            $filter
     * @return Mage_Sales_Model_Resource_Order_Creditmemo_Collection
     * @throws Mage_Core_Exception
     */
    public function getFilteredCollectionItems($filter = null)
    {
        return $this->getResourceCollection()->getFiltered($filter);
    }

    /**
     * Checking if the credit memo is last
     *
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function isLast()
    {
        foreach ($this->getAllItems() as $item) {
            if (!$item->isLast()) {
                return false;
            }
        }

        return true;
    }
}
