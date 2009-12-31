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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

    protected static $_states;

    protected $_items;
    protected $_comments;
    protected $_order;

    protected $_saveBeforeDestruct = false;

    protected $_eventPrefix = 'sales_order_invoice';
    protected $_eventObject = 'invoice';

    public function __destruct()
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
     * Load invoice by increment id
     *
     * @param string $incrementId
     * @return Mage_Sales_Model_Order_Invoice
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
     * @return  Mage_Sales_Model_Order_Invoice
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
        return $this->_order;
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
        if ($this->getState() != self::STATE_CANCELED &&
            $this->getState() != self::STATE_PAID &&
            $this->getOrder()->getPayment()->canCapture()) {
            return true;
        }
        return false;
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
                }
            }
            else {
                $canVoid = (bool) $canVoid;
            }
        }
        return $canVoid;
    }

    /**
     * Check invice cancel action availability
     *
     * @return bool
     */
    public function canCancel()
    {
        return $this->getState() == self::STATE_OPEN;
    }

    /**
     * Capture invoice
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function capture()
    {
        $this->getOrder()->getPayment()->capture($this);
        $this->pay();
        return $this;
    }

    /**
     * Pay invoice
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function pay()
    {
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
        Mage::dispatchEvent('sales_order_invoice_pay', array($this->_eventObject=>$this));
        return $this;
    }

    /**
     * Void invoice
     *
     * @return Mage_Sales_Model_Order_Invoice
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
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function cancel()
    {
        $this->getOrder()->getPayment()->cancelInvoice($this);
        foreach ($this->getAllItems() as $item) {
            $item->cancel();
        }

        /**
         * Unregister order totals only for invoices in state PAID
         */
        if ($this->getState() == self::STATE_PAID) {
            $this->getOrder()->setTotalPaid(
                $this->getOrder()->getTotalPaid()-$this->getGrandTotal()
            );
            $this->getOrder()->setBaseTotalPaid(
                $this->getOrder()->getBaseTotalPaid()-$this->getBaseGrandTotal()
            );

            $this->getOrder()->setTotalInvoiced(
                $this->getOrder()->getTotalInvoiced()-$this->getGrandTotal()
            );
            $this->getOrder()->setBaseTotalInvoiced(
                $this->getOrder()->getBaseTotalInvoiced()-$this->getBaseGrandTotal()
            );
        }
        $this->setState(self::STATE_CANCELED);
        $this->getOrder()->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
        Mage::dispatchEvent('sales_order_invoice_cancel', array($this->_eventObject=>$this));
        return $this;
    }

    /**
     * Invoice totals collecting
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function collectTotals()
    {
        foreach ($this->getConfig()->getTotalModels() as $model) {
            $model->collect($this);
        }
        return $this;
    }

    public function getItemsCollection()
    {
        if (empty($this->_items)) {
            $this->_items = Mage::getResourceModel('sales/order_invoice_item_collection')
                ->addAttributeToSelect('*')
                ->setInvoiceFilter($this->getId());

            if ($this->getId()) {
                foreach ($this->_items as $item) {
                    $item->setInvoice($this);
                }
            }
        }
        return $this->_items;
    }

    public function getAllItems()
    {
        $items = array();
        foreach ($this->getItemsCollection() as $item) {
            if (!$item->isDeleted()) {
                $items[] =  $item;
            }
        }
        return $items;
    }

    public function getItemById($itemId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getId()==$itemId) {
                return $item;
            }
        }
        return false;
    }

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
            self::$_states = array(
                self::STATE_OPEN       => Mage::helper('sales')->__('Pending'),
                self::STATE_PAID       => Mage::helper('sales')->__('Paid'),
                self::STATE_CANCELED   => Mage::helper('sales')->__('Canceled'),
            );
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
     * @return unknown
     */
    public function register()
    {
        if ($this->getId()) {
            Mage::throwException(
                Mage::helper('sales')->__('Can not register existing invoice')
            );
        }

        foreach ($this->getAllItems() as $item) {
            if ($item->getQty()>0) {
                $item->register();
            }
            else {
                $item->isDeleted(true);
            }
        }

        if ($this->canCapture()) {
            if ($captureCase = $this->getRequestedCaptureCase()) {
                if ($captureCase == self::CAPTURE_ONLINE) {
                    $this->capture();
                }
                elseif ($captureCase == self::CAPTURE_OFFLINE) {
                    $this->setCanVoidFlag(false);
                    $this->pay();
                }
            }
        }
        elseif(!$this->getOrder()->getPayment()->getMethodInstance()->isGateway()) {
            $this->pay();
        }

        $this->getOrder()->setTotalInvoiced(
            $this->getOrder()->getTotalInvoiced()+$this->getGrandTotal()
        );
        $this->getOrder()->setBaseTotalInvoiced(
            $this->getOrder()->getBaseTotalInvoiced()+$this->getBaseGrandTotal()
        );

        $this->getOrder()->setSubtotalInvoiced(
            $this->getOrder()->getSubtotalInvoiced()+$this->getSubtotal()
        );
        $this->getOrder()->setBaseSubtotalInvoiced(
            $this->getOrder()->getBaseSubtotalInvoiced()+$this->getBaseSubtotal()
        );

        $this->getOrder()->setTaxInvoiced(
            $this->getOrder()->getTaxInvoiced()+$this->getTaxAmount()
        );
        $this->getOrder()->setBaseTaxInvoiced(
            $this->getOrder()->getBaseTaxInvoiced()+$this->getBaseTaxAmount()
        );

        $this->getOrder()->setShippingInvoiced(
            $this->getOrder()->getShippingInvoiced()+$this->getShippingAmount()
        );
        $this->getOrder()->setBaseShippingInvoiced(
            $this->getOrder()->getBaseShippingInvoiced()+$this->getBaseShippingAmount()
        );

        $this->getOrder()->setDiscountInvoiced(
            $this->getOrder()->getDiscountInvoiced()+$this->getDiscountAmount()
        );
        $this->getOrder()->setBaseDiscountInvoiced(
            $this->getOrder()->getBaseDiscountInvoiced()+$this->getBaseDiscountAmount()
        );
        $this->getOrder()->setBaseTotalInvoicedCost(
            $this->getOrder()->getBaseTotalInvoicedCost()+$this->getBaseCost()
        );

        $state = $this->getState();
        if (is_null($state)) {
            $this->setState(self::STATE_OPEN);
        }
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
            if (!$item->isLast()) {
                return false;
            }
        }
        return true;
    }

    public function addComment($comment, $notify=false)
    {
        if (!($comment instanceof Mage_Sales_Model_Order_Invoice_Comment)) {
            $comment = Mage::getModel('sales/order_invoice_comment')
                ->setComment($comment)
                ->setIsCustomerNotified($notify);
        }
        $comment->setInvoice($this)
            ->setStoreId($this->getStoreId())
            ->setParentId($this->getId());
        if (!$comment->getId()) {
            $this->getCommentsCollection()->addItem($comment);
        }
        return $this;
    }

    public function getCommentsCollection($reload=false)
    {
        if (is_null($this->_comments) || $reload) {
            $this->_comments = Mage::getResourceModel('sales/order_invoice_comment_collection')
                ->addAttributeToSelect('*')
                ->setInvoiceFilter($this->getId())
                ->setCreatedAtOrder();
            if ($this->getId()) {
                foreach ($this->_comments as $comment) {
                    $comment->setInvoice($this);
                }
            }
        }
        return $this->_comments;
    }

    /**
     * Sending email with Invoice data
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function sendEmail($notifyCustomer=true, $comment='')
    {
        if (!Mage::helper('sales')->canSendNewInvoiceEmail($this->getOrder()->getStore()->getId())) {
            return $this;
        }

        $currentDesign = Mage::getDesign()->setAllGetOld(array(
            'package' => Mage::getStoreConfig('design/package/name', $this->getStoreId()),
            'store'   => $this->getStoreId()
        ));

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $order  = $this->getOrder();
        $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $this->getStoreId());

        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }
        $paymentBlock   = Mage::helper('payment')->getInfoBlock($order->getPayment())
            ->setIsSecureMode(true);

        $mailTemplate = Mage::getModel('core/email_template');

        if ($order->getCustomerIsGuest()) {
            $template = Mage::getStoreConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $order->getStoreId());
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $template = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $order->getStoreId());
            $customerName = $order->getCustomerName();
        }

        if ($notifyCustomer) {
            $sendTo[] = array(
                'name'  => $customerName,
                'email' => $order->getCustomerEmail()
            );
            if ($copyTo && $copyMethod == 'bcc') {
                foreach ($copyTo as $email) {
                    $mailTemplate->addBcc($email);
                }
            }

        }

        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {
            foreach ($copyTo as $email) {
                $sendTo[] = array(
                    'name'  => null,
                    'email' => $email
                );
            }
        }

        foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$order->getStoreId()))
                ->sendTransactional(
                    $template,
                    Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $order->getStoreId()),
                    $recipient['email'],
                    $recipient['name'],
                    array(
                        'order'       => $order,
                        'invoice'     => $this,
                        'comment'     => $comment,
                        'billing'     => $order->getBillingAddress(),
                        'payment_html'=> $paymentBlock->toHtml(),
                    )
                );
        }

        $translate->setTranslateInline(true);

        Mage::getDesign()->setAllGetOld($currentDesign);

        return $this;
    }

    /**
     * Sending email with invoice update information
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function sendUpdateEmail($notifyCustomer=true, $comment='')
    {
        if (!Mage::helper('sales')->canSendInvoiceCommentEmail($this->getOrder()->getStore()->getId())) {
            return $this;
        }

        $currentDesign = Mage::getDesign()->setAllGetOld(array(
            'package' => Mage::getStoreConfig('design/package/name', $this->getStoreId()),
        ));

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $order  = $this->getOrder();

        $copyTo = $this->_getEmails(self::XML_PATH_UPDATE_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_COPY_METHOD, $this->getStoreId());

        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }

        $sendTo = array();

        $mailTemplate = Mage::getModel('core/email_template');

        if ($order->getCustomerIsGuest()) {
            $template = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE, $order->getStoreId());
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $template = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_TEMPLATE, $order->getStoreId());
            $customerName = $order->getCustomerName();
        }

        if ($notifyCustomer) {
            $sendTo[] = array(
                'name'  => $customerName,
                'email' => $order->getCustomerEmail()
            );
            if ($copyTo && $copyMethod == 'bcc') {
                foreach ($copyTo as $email) {
                    $mailTemplate->addBcc($email);
                }
            }

        }

        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {
            foreach ($copyTo as $email) {
                $sendTo[] = array(
                    'name'  => null,
                    'email' => $email
                );
            }
        }

        foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$order->getStoreId()))
                ->sendTransactional(
                    $template,
                    Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_IDENTITY, $order->getStoreId()),
                    $recipient['email'],
                    $recipient['name'],
                    array(
                        'order'  => $order,
                        'billing'=> $order->getBillingAddress(),
                        'invoice'=> $this,
                        'comment'=> $comment
                    )
                );
        }

        $translate->setTranslateInline(true);

        Mage::getDesign()->setAllGetOld($currentDesign);

        return $this;
    }

    protected function _getEmails($configPath)
    {
        $data = Mage::getStoreConfig($configPath, $this->getStoreId());
        if (!empty($data)) {
            return explode(',', $data);
        }
        return false;
    }

    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        return parent::_beforeDelete();
    }
}
