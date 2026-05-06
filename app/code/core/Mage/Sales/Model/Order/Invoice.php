<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Resource_Order_Invoice            _getResource()
 * @method Mage_Sales_Model_Resource_Order_Invoice_Collection getCollection()
 * @method bool                                               getIsPaid()
 * @method string                                             getRequestedCaptureCase()
 * @method Mage_Sales_Model_Resource_Order_Invoice            getResource()
 * @method Mage_Sales_Model_Resource_Order_Invoice_Collection getResourceCollection()
 * @method $this                                              setIsPaid(bool $value)
 */
class Mage_Sales_Model_Order_Invoice extends Mage_Sales_Model_Abstract
{
    /**
     * Invoice states
     */
    public const STATE_OPEN       = 1;

    public const STATE_PAID       = 2;

    public const STATE_CANCELED   = 3;

    public const CAPTURE_ONLINE   = 'online';

    public const CAPTURE_OFFLINE  = 'offline';

    public const NOT_CAPTURE      = 'not_capture';

    public const XML_PATH_EMAIL_TEMPLATE               = 'sales_email/invoice/template';

    public const XML_PATH_EMAIL_GUEST_TEMPLATE         = 'sales_email/invoice/guest_template';

    public const XML_PATH_EMAIL_IDENTITY               = 'sales_email/invoice/identity';

    public const XML_PATH_EMAIL_COPY_TO                = 'sales_email/invoice/copy_to';

    public const XML_PATH_EMAIL_COPY_METHOD            = 'sales_email/invoice/copy_method';

    public const XML_PATH_EMAIL_ENABLED                = 'sales_email/invoice/enabled';

    public const XML_PATH_UPDATE_EMAIL_TEMPLATE        = 'sales_email/invoice_comment/template';

    public const XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE  = 'sales_email/invoice_comment/guest_template';

    public const XML_PATH_UPDATE_EMAIL_IDENTITY        = 'sales_email/invoice_comment/identity';

    public const XML_PATH_UPDATE_EMAIL_COPY_TO         = 'sales_email/invoice_comment/copy_to';

    public const XML_PATH_UPDATE_EMAIL_COPY_METHOD     = 'sales_email/invoice_comment/copy_method';

    public const XML_PATH_UPDATE_EMAIL_ENABLED         = 'sales_email/invoice_comment/enabled';

    public const REPORT_DATE_TYPE_ORDER_CREATED        = 'order_created';

    public const REPORT_DATE_TYPE_INVOICE_CREATED      = 'invoice_created';

    /**
     * Identifier for order history item
     */
    public const HISTORY_ENTITY_NAME = 'invoice';

    protected static $_states;

    /**
     * @var null|Mage_Sales_Model_Order_Invoice_Item[]|Mage_Sales_Model_Resource_Order_Invoice_Item_Collection
     */
    protected $_items;

    /**
     * @var null|Mage_Sales_Model_Order_Invoice_Comment[]|Mage_Sales_Model_Resource_Order_Invoice_Comment_Collection
     */
    protected $_comments;

    /**
     * @var null|Mage_Sales_Model_Order
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
     * @inheritDoc
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
     * @param  string $incrementId
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
     * @return $this
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

        return abs($this->getBaseGrandTotal() - $this->getBaseTotalRefunded()) >= .0001;
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
            $this->getOrder()->getTotalPaid() + $this->getGrandTotal(),
        );
        $this->getOrder()->setBaseTotalPaid(
            $this->getOrder()->getBaseTotalPaid() + $this->getBaseGrandTotal(),
        );
        Mage::dispatchEvent('sales_order_invoice_pay', [$this->_eventObject => $this]);
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
            $this->getOrder()->setTotalPaid($this->getOrder()->getTotalPaid() - $this->getGrandTotal());
            $this->getOrder()->setBaseTotalPaid($this->getOrder()->getBaseTotalPaid() - $this->getBaseGrandTotal());
        }

        $this->setState(self::STATE_CANCELED);
        $this->getOrder()->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
        Mage::dispatchEvent('sales_order_invoice_cancel', [$this->_eventObject => $this]);
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
     * @param  float  $price
     * @param  string $type
     * @param  bool   $negative Indicates if we perform addition (true) or subtraction (false) of rounded value
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
     * @param  int|string                                $itemId
     * @return false|Mage_Sales_Model_Order_Invoice_Item
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
            if ($item->getQty() > 0) {
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

        Mage::dispatchEvent('sales_order_invoice_register', [$this->_eventObject => $this, 'order' => $order]);
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
     * @param bool                                          $notify
     * @param bool                                          $visibleOnFront
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
     * @param  bool                                                        $reload
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
     * @param  bool   $notifyCustomer
     * @param  string $comment
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
     * Send email with invoice update information
     *
     * @param  bool   $notifyCustomer
     * @param  string $comment
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
            'billing'      => $order->getBillingAddress(),
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
     * @return $this
     * @throws Mage_Core_Exception
     */
    #[Override]
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
    #[Override]
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
    #[Override]
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

    public function getBaseCost(): float
    {
        return (float) $this->_getData('base_cost');
    }

    public function getBaseCurrencyCode(): string
    {
        return (string) $this->_getData('base_currency_code');
    }

    public function getBaseDiscountAmount(): float
    {
        return (float) $this->_getData('base_discount_amount');
    }

    public function getBaseGrandTotal(): float
    {
        return (float) $this->_getData('base_grand_total');
    }

    public function getBaseHiddenTaxAmount(): float
    {
        return (float) $this->_getData('base_hidden_tax_amount');
    }

    public function getBaseShippingAmount(): float
    {
        return (float) $this->_getData('base_shipping_amount');
    }

    public function getBaseShippingHiddenTaxAmount(): float
    {
        return (float) $this->_getData('base_shipping_hidden_tax_amount');
    }

    public function getBaseShippingInclTax(): float
    {
        return (float) $this->_getData('base_shipping_incl_tax');
    }

    public function getBaseShippingTaxAmount(): float
    {
        return (float) $this->_getData('base_shipping_tax_amount');
    }

    public function getBaseSubtotal(): float
    {
        return (float) $this->_getData('base_subtotal');
    }

    public function getBaseSubtotalInclTax(): float
    {
        return (float) $this->_getData('base_subtotal_incl_tax');
    }

    public function getBaseTaxAmount(): float
    {
        return (float) $this->_getData('base_tax_amount');
    }

    public function getBaseToGlobalRate(): float
    {
        return (float) $this->_getData('base_to_global_rate');
    }

    public function getBaseToOrderRate(): float
    {
        return (float) $this->_getData('base_to_order_rate');
    }

    public function getBaseTotalRefunded(): float
    {
        return (float) $this->_getData('base_total_refunded');
    }

    public function getBillingAddressId(): int
    {
        return (int) $this->_getData('billing_address_id');
    }

    public function getCanVoidFlag(): ?int
    {
        $value = $this->_getData('can_void_flag');
        return $v === null ? null : (int) $v;
    }

    public function getCybersourceToken(): string
    {
        return (string) $this->_getData('cybersource_token');
    }

    public function getDiscountAmount(): float
    {
        return (float) $this->_getData('discount_amount');
    }

    public function getEmailSent(): int
    {
        return (int) $this->_getData('email_sent');
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

    public function getIncrementId(): string
    {
        return (string) $this->_getData('increment_id');
    }

    public function getIsUsedForRefund(): int
    {
        return (int) $this->_getData('is_used_for_refund');
    }

    public function getOrderCurrencyCode(): string
    {
        return (string) $this->_getData('order_currency_code');
    }

    public function getOrderId(): int
    {
        return (int) $this->_getData('order_id');
    }

    public function getShippingAddressId(): int
    {
        return (int) $this->_getData('shipping_address_id');
    }

    public function getShippingAmount(): float
    {
        return (float) $this->_getData('shipping_amount');
    }

    public function getShippingHiddenTaxAmount(): float
    {
        return (float) $this->_getData('shipping_hidden_tax_amount');
    }

    public function getShippingInclTax(): float
    {
        return (float) $this->_getData('shipping_incl_tax');
    }

    public function getShippingTaxAmount(): float
    {
        return (float) $this->_getData('shipping_tax_amount');
    }

    public function getState(): int
    {
        return (int) $this->_getData('state');
    }

    public function getStoreCurrencyCode(): string
    {
        return (string) $this->_getData('store_currency_code');
    }

    public function getStoreId(): int
    {
        return (int) $this->_getData('store_id');
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

    public function getSubtotalInclTax(): float
    {
        return (float) $this->_getData('subtotal_incl_tax');
    }

    public function getTaxAmount(): float
    {
        return (float) $this->_getData('tax_amount');
    }

    public function getTotalQty(): float
    {
        return (float) $this->_getData('total_qty');
    }

    public function getTransactionId(): string
    {
        return (string) $this->_getData('transaction_id');
    }

    public function setBaseCost(float $value): static
    {
        return $this->setData('base_cost', $value);
    }

    public function setBaseCurrencyCode(string $value): static
    {
        return $this->setData('base_currency_code', $value);
    }

    public function setBaseDiscountAmount(float $value): static
    {
        return $this->setData('base_discount_amount', $value);
    }

    public function setBaseGrandTotal(float $value): static
    {
        return $this->setData('base_grand_total', $value);
    }

    public function setBaseHiddenTaxAmount(float $value): static
    {
        return $this->setData('base_hidden_tax_amount', $value);
    }

    public function setBaseShippingAmount(float $value): static
    {
        return $this->setData('base_shipping_amount', $value);
    }

    public function setBaseShippingHiddenTaxAmount(float $value): static
    {
        return $this->setData('base_shipping_hidden_tax_amount', $value);
    }

    public function setBaseShippingInclTax(float $value): static
    {
        return $this->setData('base_shipping_incl_tax', $value);
    }

    public function setBaseShippingTaxAmount(float $value): static
    {
        return $this->setData('base_shipping_tax_amount', $value);
    }

    public function setBaseSubtotal(float $value): static
    {
        return $this->setData('base_subtotal', $value);
    }

    public function setBaseSubtotalInclTax(float $value): static
    {
        return $this->setData('base_subtotal_incl_tax', $value);
    }

    public function setBaseTaxAmount(float $value): static
    {
        return $this->setData('base_tax_amount', $value);
    }

    public function setBaseToGlobalRate(float $value): static
    {
        return $this->setData('base_to_global_rate', $value);
    }

    public function setBaseToOrderRate(float $value): static
    {
        return $this->setData('base_to_order_rate', $value);
    }

    public function setBaseTotalRefunded(float $value): static
    {
        return $this->setData('base_total_refunded', $value);
    }

    public function setBillingAddressId(int $value): static
    {
        return $this->setData('billing_address_id', $value);
    }

    public function setCanVoidFlag(int $value): static
    {
        return $this->setData('can_void_flag', $value);
    }

    public function setCustomerId(int $value): static
    {
        return $this->setData('customer_id', $value);
    }

    public function setCybersourceToken(string $value): static
    {
        return $this->setData('cybersource_token', $value);
    }

    public function setDiscountAmount(float $value): static
    {
        return $this->setData('discount_amount', $value);
    }

    public function setEmailSent(int $value): static
    {
        return $this->setData('email_sent', $value);
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

    public function setIncrementId(string $value): static
    {
        return $this->setData('increment_id', $value);
    }

    public function setIsUsedForRefund(int $value): static
    {
        return $this->setData('is_used_for_refund', $value);
    }

    public function setOrderCurrencyCode(string $value): static
    {
        return $this->setData('order_currency_code', $value);
    }

    public function setOrderId(int $value): static
    {
        return $this->setData('order_id', $value);
    }

    public function setShippingAddressId(int $value): static
    {
        return $this->setData('shipping_address_id', $value);
    }

    public function setShippingAmount(float $value): static
    {
        return $this->setData('shipping_amount', $value);
    }

    public function setShippingHiddenTaxAmount(float $value): static
    {
        return $this->setData('shipping_hidden_tax_amount', $value);
    }

    public function setShippingInclTax(float $value): static
    {
        return $this->setData('shipping_incl_tax', $value);
    }

    public function setShippingTaxAmount(float $value): static
    {
        return $this->setData('shipping_tax_amount', $value);
    }

    public function setState(int $value): static
    {
        return $this->setData('state', $value);
    }

    public function setStoreCurrencyCode(string $value): static
    {
        return $this->setData('store_currency_code', $value);
    }

    public function setStoreId(int $value): static
    {
        return $this->setData('store_id', $value);
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

    public function setSubtotalInclTax(float $value): static
    {
        return $this->setData('subtotal_incl_tax', $value);
    }

    public function setTaxAmount(float $value): static
    {
        return $this->setData('tax_amount', $value);
    }

    public function setTotalQty(float $value): static
    {
        return $this->setData('total_qty', $value);
    }

    public function setTransactionId(string $value): static
    {
        return $this->setData('transaction_id', $value);
    }
}
