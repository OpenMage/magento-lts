<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @method Mage_Sales_Model_Resource_Order_Invoice _getResource()
 * @method Mage_Sales_Model_Resource_Order_Invoice getResource()
 * @method string getBackUrl()
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method float getBaseGrandTotal()
 * @method $this setBaseGrandTotal(float $value)
 * @method float getShippingTaxAmount()
 * @method $this setShippingTaxAmount(float $value)
 * @method float getTaxAmount()
 * @method $this setTaxAmount(float $value)
 * @method float getBaseTaxAmount()
 * @method $this setBaseTaxAmount(float $value)
 * @method float getStoreToOrderRate()
 * @method $this setStoreToOrderRate(float $value)
 * @method float getBaseShippingTaxAmount()
 * @method $this setBaseShippingTaxAmount(float $value)
 * @method float getBaseDiscountAmount()
 * @method $this setBaseDiscountAmount(float $value)
 * @method float getBaseToOrderRate()
 * @method $this setBaseToOrderRate(float $value)
 * @method float getGrandTotal()
 * @method $this setGrandTotal(float $value)
 * @method float getShippingAmount()
 * @method $this setShippingAmount(float $value)
 * @method float getSubtotalInclTax()
 * @method $this setSubtotalInclTax(float $value)
 * @method float getBaseSubtotalInclTax()
 * @method $this setBaseSubtotalInclTax(float $value)
 * @method float getStoreToBaseRate()
 * @method $this setStoreToBaseRate(float $value)
 * @method float getBaseShippingAmount()
 * @method $this setBaseShippingAmount(float $value)
 * @method float getTotalQty()
 * @method $this setTotalQty(float $value)
 * @method float getBaseToGlobalRate()
 * @method $this setBaseToGlobalRate(float $value)
 * @method float getSubtotal()
 * @method $this setSubtotal(float $value)
 * @method float getBaseSubtotal()
 * @method $this setBaseSubtotal(float $value)
 * @method float getDiscountAmount()
 * @method $this setDiscountAmount(float $value)
 * @method int getBillingAddressId()
 * @method $this setBillingAddressId(int $value)
 * @method int getIsUsedForRefund()
 * @method $this setIsUsedForRefund(int $value)
 * @method int getOrderId()
 * @method $this setOrderId(int $value)
 * @method int getEmailSent()
 * @method $this setEmailSent(int $value)
 * @method int getCanVoidFlag()
 * @method $this setCanVoidFlag(int $value)
 * @method int getState()
 * @method $this setState(int $value)
 * @method int getShippingAddressId()
 * @method $this setShippingAddressId(int $value)
 * @method string getCybersourceToken()
 * @method $this setCybersourceToken(string $value)
 * @method string getStoreCurrencyCode()
 * @method $this setStoreCurrencyCode(string $value)
 * @method string getTransactionId()
 * @method $this setTransactionId(string $value)
 * @method string getOrderCurrencyCode()
 * @method $this setOrderCurrencyCode(string $value)
 * @method string getBaseCurrencyCode()
 * @method $this setBaseCurrencyCode(string $value)
 * @method string getGlobalCurrencyCode()
 * @method $this setGlobalCurrencyCode(string $value)
 * @method string getIncrementId()
 * @method $this setIncrementId(string $value)
 * @method string getCreatedAt()
 * @method $this setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method $this setUpdatedAt(string $value)
 * @method float getHiddenTaxAmount()
 * @method $this setHiddenTaxAmount(float $value)
 * @method float getBaseHiddenTaxAmount()
 * @method $this setBaseHiddenTaxAmount(float $value)
 * @method float getShippingHiddenTaxAmount()
 * @method $this setShippingHiddenTaxAmount(float $value)
 * @method float getBaseShippingHiddenTaxAmount()
 * @method $this setBaseShippingHiddenTaxAmount(float $value)
 * @method float getShippingInclTax()
 * @method $this setShippingInclTax(float $value)
 * @method float getBaseShippingInclTax()
 * @method $this setBaseShippingInclTax(float $value)
 * @method float getBaseTotalRefunded()
 * @method bool getIsPaid()
 * @method $this setBaseTotalRefunded(float $value)
 * @method $this setCustomerId(int $value)
 * @method $this setIsPaid(bool $value)
 * @method float getBaseCost()
 * @method string getRequestedCaptureCase()
 * @method $this setBaseCost(float $value)
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Invoice extends Mage_Sales_Model_Abstract
{
    /**
     * Invoice states
     */
    const STATE_OPEN       = 1;
    const STATE_PAID       = 2;
    const STATE_CANCELED   = 3;

    const CAPTURE_ONLINE   = 'online';
    const CAPTURE_OFFLINE  = 'offline';
    const NOT_CAPTURE      = 'not_capture';

    const XML_PATH_EMAIL_TEMPLATE               = 'sales_email/invoice/template';
    const XML_PATH_EMAIL_GUEST_TEMPLATE         = 'sales_email/invoice/guest_template';
    const XML_PATH_EMAIL_IDENTITY               = 'sales_email/invoice/identity';
    const XML_PATH_EMAIL_COPY_TO                = 'sales_email/invoice/copy_to';
    const XML_PATH_EMAIL_COPY_METHOD            = 'sales_email/invoice/copy_method';
    const XML_PATH_EMAIL_ENABLED                = 'sales_email/invoice/enabled';

    const XML_PATH_UPDATE_EMAIL_TEMPLATE        = 'sales_email/invoice_comment/template';
    const XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE  = 'sales_email/invoice_comment/guest_template';
    const XML_PATH_UPDATE_EMAIL_IDENTITY        = 'sales_email/invoice_comment/identity';
    const XML_PATH_UPDATE_EMAIL_COPY_TO         = 'sales_email/invoice_comment/copy_to';
    const XML_PATH_UPDATE_EMAIL_COPY_METHOD     = 'sales_email/invoice_comment/copy_method';
    const XML_PATH_UPDATE_EMAIL_ENABLED         = 'sales_email/invoice_comment/enabled';

    const REPORT_DATE_TYPE_ORDER_CREATED        = 'order_created';
    const REPORT_DATE_TYPE_INVOICE_CREATED      = 'invoice_created';

    /**
     * Identifier for order history item
     */
    const HISTORY_ENTITY_NAME = 'invoice';

    protected static $_states;

    /**
     * @var Mage_Sales_Model_Resource_Order_Invoice_Item_Collection|Mage_Sales_Model_Order_Invoice_Item[]
     */
    protected $_items;

    /**
     * @var Mage_Sales_Model_Resource_Order_Invoice_Comment_Collection|Mage_Sales_Model_Order_Invoice_Comment[]
     */
    protected $_comments;

    /**
     * @var Mage_Sales_Model_Order
     */
    protected $_order;

    /**
     * Calculator instances for delta rounding of prices
     *
     * @var array
     */
    protected $_rounders = [];

    protected $_saveBeforeDestruct = false;

    protected $_eventPrefix = 'sales_order_invoice';
    protected $_eventObject = 'invoice';

    /**
     * Whether the pay() was called
     * @var bool
     */
    protected $_wasPayCalled = false;

    /**
     * Uploader clean on shutdown
     */
    public function destruct()
    {
        if ($this->_saveBeforeDestruct) {
            $this->save();
        }
    }

    /**
     * Initialize invoice resource model
     */
    protected function _construct()
    {
        $this->_init('sales/order_invoice');
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
     * Load invoice by increment id
     *
     * @param string $incrementId
     * @return $this
     */
    public function loadByIncrementId($incrementId)
    {
        $ids = $this->getCollection()
            ->addAttributeToFilter('increment_id', $incrementId)
            ->getAllIds();

        if (!empty($ids)) {
            reset($ids);
            $this->load(current($ids));
        }
        return $this;
    }

    /**
     * Retrieve invoice configuration model
     *
     * @return Mage_Sales_Model_Order_Invoice_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('sales/order_invoice_config');
    }

    /**
     * Retrieve store model instance
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return $this->getOrder()->getStore();
    }

    /**
     * Declare order for invoice
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  $this
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        $this->setOrderId($order->getId())
            ->setStoreId($order->getStoreId());
        return $this;
    }

    /**
     * Retrieve the order the invoice for created for
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->_order instanceof Mage_Sales_Model_Order) {
            $this->_order = Mage::getModel('sales/order')->load($this->getOrderId());
        }
        return $this->_order->setHistoryEntityName(self::HISTORY_ENTITY_NAME);
    }

    /**
     * Retrieve the increment_id of the order
     *
     * @return string
     */
    public function getOrderIncrementId()
    {
        return Mage::getModel('sales/order')->getResource()->getIncrementId($this->getOrderId());
    }

    /**
     * Retrieve billing address
     *
     * @return Mage_Sales_Model_Order_Address
     */
    public function getBillingAddress()
    {
        return $this->getOrder()->getBillingAddress();
    }

    /**
     * Retrieve shipping address
     *
     * @return Mage_Sales_Model_Order_Address
     */
    public function getShippingAddress()
    {
        return $this->getOrder()->getShippingAddress();
    }

    /**
     * Check invoice cancel state
     *
     * @return bool
     */
    public function isCanceled()
    {
        return $this->getState() == self::STATE_CANCELED;
    }

    /**
     * Check invice capture action availability
     *
     * @return bool
     */
    public function canCapture()
    {
        return $this->getState() != self::STATE_CANCELED
            && $this->getState() != self::STATE_PAID
            && $this->getOrder()->getPayment()->canCapture();
    }

    /**
     * Check invice void action availability
     *
     * @return bool
     */
    public function canVoid()
    {
        $canVoid = false;
        if ($this->getState() == self::STATE_PAID) {
            $canVoid = $this->getCanVoidFlag();
            /**
             * If we not retrieve negative answer from payment yet
             */
            if (is_null($canVoid)) {
                $canVoid = $this->getOrder()->getPayment()->canVoid($this);
                if ($canVoid === false) {
                    $this->setCanVoidFlag(false);
                    $this->_saveBeforeDestruct = true;
                    register_shutdown_function([$this, 'destruct']);
                }
            } else {
                $canVoid = (bool) $canVoid;
            }
        }
        return $canVoid;
    }

    /**
     * Check invoice cancel action availability
     *
     * @return bool
     */
    public function canCancel()
    {
        return $this->getState() == self::STATE_OPEN;
    }

    /**
     * Check invoice refund action availability
     *
     * @return bool
     */
    public function canRefund()
    {
        if ($this->getState() != self::STATE_PAID) {
            return false;
        }
        if (abs($this->getBaseGrandTotal() - $this->getBaseTotalRefunded()) < .0001) {
            return false;
        }
        return true;
    }

    /**
     * Capture invoice
     *
     * @return $this
     */
    public function capture()
    {
        $this->getOrder()->getPayment()->capture($this);
        if ($this->getIsPaid()) {
            $this->pay();
        }
        return $this;
    }

    /**
     * Pay invoice
     *
     * @return $this
     */
    public function pay()
    {
        if ($this->_wasPayCalled) {
            return $this;
        }
        $this->_wasPayCalled = true;

        $invoiceState = self::STATE_PAID;
        if ($this->getOrder()->getPayment()->hasForcedState()) {
            $invoiceState = $this->getOrder()->getPayment()->getForcedState();
        }

        $this->setState($invoiceState);

        $this->getOrder()->getPayment()->pay($this);
        $this->getOrder()->setTotalPaid(
            $this->getOrder()->getTotalPaid()+$this->getGrandTotal()
        );
        $this->getOrder()->setBaseTotalPaid(
            $this->getOrder()->getBaseTotalPaid()+$this->getBaseGrandTotal()
        );
        Mage::dispatchEvent('sales_order_invoice_pay', [$this->_eventObject=>$this]);
        return $this;
    }

    /**
     * Whether pay() method was called (whether order and payment totals were updated)
     * @return bool
     */
    public function wasPayCalled()
    {
        return $this->_wasPayCalled;
    }

    /**
     * Void invoice
     *
     * @return $this
     */
    public function void()
    {
        $this->getOrder()->getPayment()->void($this);
        $this->cancel();
        return $this;
    }

    /**
     * Cancel invoice action
     *
     * @return $this
     */
    public function cancel()
    {
        $order = $this->getOrder();
        $order->getPayment()->cancelInvoice($this);
        foreach ($this->getAllItems() as $item) {
            $item->cancel();
        }

        /**
         * Unregister order totals only for invoices in state PAID
         */
        $order->setTotalInvoiced($order->getTotalInvoiced() - $this->getGrandTotal());
        $order->setBaseTotalInvoiced($order->getBaseTotalInvoiced() - $this->getBaseGrandTotal());

        $order->setSubtotalInvoiced($order->getSubtotalInvoiced() - $this->getSubtotal());
        $order->setBaseSubtotalInvoiced($order->getBaseSubtotalInvoiced() - $this->getBaseSubtotal());

        $order->setTaxInvoiced($order->getTaxInvoiced() - $this->getTaxAmount());
        $order->setBaseTaxInvoiced($order->getBaseTaxInvoiced() - $this->getBaseTaxAmount());

        $order->setHiddenTaxInvoiced($order->getHiddenTaxInvoiced() - $this->getHiddenTaxAmount());
        $order->setBaseHiddenTaxInvoiced($order->getBaseHiddenTaxInvoiced() - $this->getBaseHiddenTaxAmount());

        $order->setShippingTaxInvoiced($order->getShippingTaxInvoiced() - $this->getShippingTaxAmount());
        $order->setBaseShippingTaxInvoiced($order->getBaseShippingTaxInvoiced() - $this->getBaseShippingTaxAmount());

        $order->setShippingInvoiced($order->getShippingInvoiced() - $this->getShippingAmount());
        $order->setBaseShippingInvoiced($order->getBaseShippingInvoiced() - $this->getBaseShippingAmount());

        $order->setDiscountInvoiced($order->getDiscountInvoiced() - $this->getDiscountAmount());
        $order->setBaseDiscountInvoiced($order->getBaseDiscountInvoiced() - $this->getBaseDiscountAmount());
        $order->setBaseTotalInvoicedCost($order->getBaseTotalInvoicedCost() - $this->getBaseCost());

        if ($this->getState() == self::STATE_PAID) {
            $this->getOrder()->setTotalPaid($this->getOrder()->getTotalPaid()-$this->getGrandTotal());
            $this->getOrder()->setBaseTotalPaid($this->getOrder()->getBaseTotalPaid()-$this->getBaseGrandTotal());
        }
        $this->setState(self::STATE_CANCELED);
        $this->getOrder()->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
        Mage::dispatchEvent('sales_order_invoice_cancel', [$this->_eventObject=>$this]);
        return $this;
    }

    /**
     * Invoice totals collecting
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
     * @param float $price
     * @param string $type
     * @param bool $negative Indicates if we perform addition (true) or subtraction (false) of rounded value
     * @return float
     */
    public function roundPrice($price, $type = 'regular', $negative = false)
    {
        if ($price) {
            if (!isset($this->_rounders[$type])) {
                $this->_rounders[$type] = Mage::getModel('core/calculator', $this->getStore());
            }
            $price = $this->_rounders[$type]->deltaRound($price, $negative);
        }
        return $price;
    }

    /**
     * Get invoice items collection
     *
     * @return Mage_Sales_Model_Resource_Order_Invoice_Item_Collection
     */
    public function getItemsCollection()
    {
        if (empty($this->_items)) {
            $this->_items = Mage::getResourceModel('sales/order_invoice_item_collection')
                ->setInvoiceFilter($this->getId());

            if ($this->getId()) {
                foreach ($this->_items as $item) {
                    $item->setInvoice($this);
                }
            }
        }
        return $this->_items;
    }

    /**
     * @return Mage_Sales_Model_Order_Invoice_Item[]
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
     * @param int|string $itemId
     * @return false|Mage_Sales_Model_Order_Invoice_Item
     */
    public function getItemById($itemId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getId()==$itemId) {
                return $item;
            }
        }
        return false;
    }

    /**
     * @param Mage_Sales_Model_Order_Invoice_Item $item
     * @return $this
     * @throws Exception
     */
    public function addItem(Mage_Sales_Model_Order_Invoice_Item $item)
    {
        $item->setInvoice($this)
            ->setParentId($this->getId())
            ->setStoreId($this->getStoreId());

        if (!$item->getId()) {
            $this->getItemsCollection()->addItem($item);
        }
        return $this;
    }

    /**
     * Retrieve invoice states array
     *
     * @return array
     */
    public static function getStates()
    {
        if (is_null(self::$_states)) {
            self::$_states = [
                self::STATE_OPEN       => Mage::helper('sales')->__('Pending'),
                self::STATE_PAID       => Mage::helper('sales')->__('Paid'),
                self::STATE_CANCELED   => Mage::helper('sales')->__('Canceled'),
            ];
        }
        return self::$_states;
    }

    /**
     * Retrieve invoice state name by state identifier
     *
     * @param   int $stateId
     * @return  string
     */
    public function getStateName($stateId = null)
    {
        if (is_null($stateId)) {
            $stateId = $this->getState();
        }

        if (is_null(self::$_states)) {
            self::getStates();
        }
        if (isset(self::$_states[$stateId])) {
            return self::$_states[$stateId];
        }
        return Mage::helper('sales')->__('Unknown State');
    }

    /**
     * Register invoice
     *
     * Apply to order, order items etc.
     *
     * @return $this
     */
    public function register()
    {
        if ($this->getId()) {
            Mage::throwException(Mage::helper('sales')->__('Cannot register existing invoice'));
        }

        foreach ($this->getAllItems() as $item) {
            if ($item->getQty()>0) {
                $item->register();
            } else {
                $item->isDeleted(true);
            }
        }

        $order = $this->getOrder();
        $captureCase = $this->getRequestedCaptureCase();
        if ($this->canCapture()) {
            if ($captureCase) {
                if ($captureCase == self::CAPTURE_ONLINE) {
                    $this->capture();
                } elseif ($captureCase == self::CAPTURE_OFFLINE) {
                    $this->setCanVoidFlag(false);
                    $this->pay();
                }
            }
        } elseif (!$order->getPayment()->getMethodInstance()->isGateway() || $captureCase == self::CAPTURE_OFFLINE) {
            if (!$order->getPayment()->getIsTransactionPending()) {
                $this->setCanVoidFlag(false);
                $this->pay();
            }
        }

        $order->setTotalInvoiced($order->getTotalInvoiced() + $this->getGrandTotal());
        $order->setBaseTotalInvoiced($order->getBaseTotalInvoiced() + $this->getBaseGrandTotal());

        $order->setSubtotalInvoiced($order->getSubtotalInvoiced() + $this->getSubtotal());
        $order->setBaseSubtotalInvoiced($order->getBaseSubtotalInvoiced() + $this->getBaseSubtotal());

        $order->setTaxInvoiced($order->getTaxInvoiced() + $this->getTaxAmount());
        $order->setBaseTaxInvoiced($order->getBaseTaxInvoiced() + $this->getBaseTaxAmount());

        $order->setHiddenTaxInvoiced($order->getHiddenTaxInvoiced() + $this->getHiddenTaxAmount());
        $order->setBaseHiddenTaxInvoiced($order->getBaseHiddenTaxInvoiced() + $this->getBaseHiddenTaxAmount());

        $order->setShippingTaxInvoiced($order->getShippingTaxInvoiced() + $this->getShippingTaxAmount());
        $order->setBaseShippingTaxInvoiced($order->getBaseShippingTaxInvoiced() + $this->getBaseShippingTaxAmount());

        $order->setShippingInvoiced($order->getShippingInvoiced() + $this->getShippingAmount());
        $order->setBaseShippingInvoiced($order->getBaseShippingInvoiced() + $this->getBaseShippingAmount());

        $order->setDiscountInvoiced($order->getDiscountInvoiced() + $this->getDiscountAmount());
        $order->setBaseDiscountInvoiced($order->getBaseDiscountInvoiced() + $this->getBaseDiscountAmount());
        $order->setBaseTotalInvoicedCost($order->getBaseTotalInvoicedCost() + $this->getBaseCost());

        $state = $this->getState();
        if (is_null($state)) {
            $this->setState(self::STATE_OPEN);
        }

        Mage::dispatchEvent('sales_order_invoice_register', [$this->_eventObject=>$this, 'order' => $order]);
        return $this;
    }

    /**
     * Checking if the invoice is last
     *
     * @return bool
     */
    public function isLast()
    {
        foreach ($this->getAllItems() as $item) {
            $orderItem = $item->getOrderItem();
            if ($orderItem->isDummy()) {
                continue;
            }

            if (!$item->isLast()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Adds comment to invoice with additional possibility to send it to customer via email
     * and show it in customer account
     *
     * @param Mage_Sales_Model_Order_Invoice_Comment|string $comment
     * @param bool $notify
     * @param bool $visibleOnFront
     *
     * @return $this
     */
    public function addComment($comment, $notify = false, $visibleOnFront = false)
    {
        if (!($comment instanceof Mage_Sales_Model_Order_Invoice_Comment)) {
            $comment = Mage::getModel('sales/order_invoice_comment')
                ->setComment($comment)
                ->setIsCustomerNotified($notify)
                ->setIsVisibleOnFront($visibleOnFront);
        }
        $comment->setInvoice($this)
            ->setStoreId($this->getStoreId())
            ->setParentId($this->getId());
        if (!$comment->getId()) {
            $this->getCommentsCollection()->addItem($comment);
        }
        $this->_hasDataChanges = true;
        return $this;
    }

    /**
     * @param bool $reload
     * @return Mage_Sales_Model_Resource_Order_Comment_Collection_Abstract
     */
    public function getCommentsCollection($reload = false)
    {
        if (is_null($this->_comments) || $reload) {
            $this->_comments = Mage::getResourceModel('sales/order_invoice_comment_collection')
                ->setInvoiceFilter($this->getId())
                ->setCreatedAtOrder();
            /**
             * When invoice created with adding comment, comments collection
             * must be loaded before we added this comment.
             */
            $this->_comments->load();

            if ($this->getId()) {
                foreach ($this->_comments as $comment) {
                    $comment->setInvoice($this);
                }
            }
        }
        return $this->_comments;
    }

    /**
     * Send email with invoice data
     *
     * @param boolean $notifyCustomer
     * @param string $comment
     * @return $this
     */
    public function sendEmail($notifyCustomer = true, $comment = '')
    {
        $order = $this->getOrder();
        $storeId = $order->getStore()->getId();

        if (!Mage::helper('sales')->canSendNewInvoiceEmail($storeId)) {
            return $this;
        }
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $storeId);
        // Check if at least one recepient is found
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
        } catch (Exception $e) {
            // Stop store emulation process
            if (isset($appEmulation, $initialEnvironmentInfo)) {
                $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            }
            throw $e;
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
            $emailInfo->addTo($order->getCustomerEmail(), $customerName);
            if ($copyTo && $copyMethod == 'bcc') {
                // Add bcc to customer email
                foreach ($copyTo as $email) {
                    $emailInfo->addBcc($email);
                }
            }
            $mailer->addEmailInfo($emailInfo);
        }

        // Email copies are sent as separated emails if their copy method is 'copy' or a customer should not be notified
        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {
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
                'invoice'      => $this,
                'comment'      => $comment,
                'billing'      => $order->getBillingAddress(),
                'payment_html' => $paymentBlockHtml
        ]);
        $mailer->send();

        if ($notifyCustomer) {
            $this->setEmailSent(true);
            $this->_getResource()->saveAttribute($this, 'email_sent');
        }

        return $this;
    }

    /**
     * Send email with invoice update information
     *
     * @param boolean $notifyCustomer
     * @param string $comment
     * @return $this
     */
    public function sendUpdateEmail($notifyCustomer = true, $comment = '')
    {
        $order = $this->getOrder();
        $storeId = $order->getStore()->getId();

        if (!Mage::helper('sales')->canSendInvoiceCommentEmail($storeId)) {
            return $this;
        }
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_UPDATE_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_COPY_METHOD, $storeId);
        // Check if at least one recepient is found
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
            $emailInfo->addTo($order->getCustomerEmail(), $customerName);
            if ($copyTo && $copyMethod == 'bcc') {
                // Add bcc to customer email
                foreach ($copyTo as $email) {
                    $emailInfo->addBcc($email);
                }
            }
            $mailer->addEmailInfo($emailInfo);
        }

        // Email copies are sent as separated emails if their copy method is 'copy' or a customer should not be notified
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
                'order'        => $order,
                'invoice'      => $this,
                'comment'      => $comment,
                'billing'      => $order->getBillingAddress()
        ]);
        $mailer->send();

        return $this;
    }

    /**
     * @param string $configPath
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
     * Reset invoice object
     *
     * @return $this
     */
    public function reset()
    {
        $this->unsetData();
        $this->_origData = null;
        $this->_items = null;
        $this->_comments = null;
        $this->_order = null;
        $this->_saveBeforeDestruct = false;
        $this->_wasPayCalled = false;
        return $this;
    }

    /**
     * Before object save manipulations
     *
     * @return $this
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
     * After object save manipulation
     *
     * @inheritDoc
     */
    protected function _afterSave()
    {

        if ($this->_items !== null) {
            /**
             * Save invoice items
             */
            foreach ($this->_items as $item) {
                $item->setOrderItem($item->getOrderItem());
                $item->save();
            }
        }

        if ($this->_comments !== null) {
            foreach ($this->_comments as $comment) {
                $comment->save();
            }
        }

        return parent::_afterSave();
    }
}
