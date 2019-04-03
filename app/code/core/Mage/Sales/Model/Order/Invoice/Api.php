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
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Invoice API
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Invoice_Api extends Mage_Sales_Model_Api_Resource
{
    /**
     * Initialize attributes map
     */
    public function __construct()
    {
        $this->_attributesMap = array(
            'invoice' => array('invoice_id' => 'entity_id'),
            'invoice_item' => array('item_id' => 'entity_id'),
            'invoice_comment' => array('comment_id' => 'entity_id'));
    }

    /**
     * Retrive invoices list. Filtration could be applied
     *
     * @param null|object|array $filters
     * @return array
     */
    public function items($filters = null)
    {
        $invoices = array();
        /** @var $invoiceCollection Mage_Sales_Model_Mysql4_Order_Invoice_Collection */
        $invoiceCollection = Mage::getResourceModel('sales/order_invoice_collection');
        $invoiceCollection->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('order_id')
            ->addAttributeToSelect('increment_id')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('state')
            ->addAttributeToSelect('grand_total')
            ->addAttributeToSelect('order_currency_code');

        /** @var $apiHelper Mage_Api_Helper_Data */
        $apiHelper = Mage::helper('api');
        try {
            $filters = $apiHelper->parseFilters($filters, $this->_attributesMap['invoice']);
            foreach ($filters as $field => $value) {
                $invoiceCollection->addFieldToFilter($field, $value);
            }
        } catch (Mage_Core_Exception $e) {
            $this->_fault('filters_invalid', $e->getMessage());
        }
        foreach ($invoiceCollection as $invoice) {
            $invoices[] = $this->_getAttributes($invoice, 'invoice');
        }
        return $invoices;
    }

    /**
     * Retrieve invoice information
     *
     * @param string $invoiceIncrementId
     * @return array
     */
    public function info($invoiceIncrementId)
    {
        $invoice = Mage::getModel('sales/order_invoice')->loadByIncrementId($invoiceIncrementId);

        /* @var Mage_Sales_Model_Order_Invoice $invoice */

        if (!$invoice->getId()) {
            $this->_fault('not_exists');
        }

        $result = $this->_getAttributes($invoice, 'invoice');
        $result['order_increment_id'] = $invoice->getOrderIncrementId();

        $result['items'] = array();
        foreach ($invoice->getAllItems() as $item) {
            $result['items'][] = $this->_getAttributes($item, 'invoice_item');
        }

        $result['comments'] = array();
        foreach ($invoice->getCommentsCollection() as $comment) {
            $result['comments'][] = $this->_getAttributes($comment, 'invoice_comment');
        }

        return $result;
    }

    /**
     * Create new invoice for order
     *
     * @param string $orderIncrementId
     * @param array $itemsQty
     * @param string $comment
     * @param booleam $email
     * @param boolean $includeComment
     * @return string
     */
    public function create($orderIncrementId, $itemsQty, $comment = null, $email = false, $includeComment = false)
    {
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);

        /* @var $order Mage_Sales_Model_Order */
        /**
          * Check order existing
          */
        if (!$order->getId()) {
             $this->_fault('order_not_exists');
        }

        /**
         * Check invoice create availability
         */
        if (!$order->canInvoice()) {
             $this->_fault('data_invalid', Mage::helper('sales')->__('Cannot do invoice for order.'));
        }

        $invoice = $order->prepareInvoice($itemsQty);

        $invoice->register();

        if ($comment !== null) {
            $invoice->addComment($comment, $email);
        }

        if ($email) {
            $invoice->setEmailSent(true);
        }

        $invoice->getOrder()->setIsInProcess(true);

        try {
            $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();

            $invoice->sendEmail($email, ($includeComment ? $comment : ''));
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return $invoice->getIncrementId();
    }

    /**
     * Add comment to invoice
     *
     * @param string $invoiceIncrementId
     * @param string $comment
     * @param boolean $email
     * @param boolean $includeComment
     * @return boolean
     */
    public function addComment($invoiceIncrementId, $comment, $email = false, $includeComment = false)
    {
        $invoice = Mage::getModel('sales/order_invoice')->loadByIncrementId($invoiceIncrementId);

        /* @var $invoice Mage_Sales_Model_Order_Invoice */

        if (!$invoice->getId()) {
            $this->_fault('not_exists');
        }


        try {
            $invoice->addComment($comment, $email);
            $invoice->sendUpdateEmail($email, ($includeComment ? $comment : ''));
            $invoice->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return true;
    }

    /**
     * Capture invoice
     *
     * @param string $invoiceIncrementId
     * @return boolean
     */
    public function capture($invoiceIncrementId)
    {
        $invoice = Mage::getModel('sales/order_invoice')->loadByIncrementId($invoiceIncrementId);

        /* @var $invoice Mage_Sales_Model_Order_Invoice */

        if (!$invoice->getId()) {
            $this->_fault('not_exists');
        }

        if (!$invoice->canCapture()) {
            $this->_fault('status_not_changed', Mage::helper('sales')->__('Invoice cannot be captured.'));
        }

        try {
            $invoice->capture();
            $invoice->getOrder()->setIsInProcess(true);
            $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('status_not_changed', $e->getMessage());
        } catch (Exception $e) {
            $this->_fault('status_not_changed', Mage::helper('sales')->__('Invoice capturing problem.'));
        }

        return true;
    }

    /**
     * Void invoice
     *
     * @param unknown_type $invoiceIncrementId
     * @return unknown
     */
    public function void($invoiceIncrementId)
    {
        $invoice = Mage::getModel('sales/order_invoice')->loadByIncrementId($invoiceIncrementId);

        /* @var $invoice Mage_Sales_Model_Order_Invoice */

        if (!$invoice->getId()) {
            $this->_fault('not_exists');
        }

        if (!$invoice->canVoid()) {
            $this->_fault('status_not_changed', Mage::helper('sales')->__('Invoice cannot be voided.'));
        }

        try {
            $invoice->void();
            $invoice->getOrder()->setIsInProcess(true);
            $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('status_not_changed', $e->getMessage());
        } catch (Exception $e) {
            $this->_fault('status_not_changed', Mage::helper('sales')->__('Invoice void problem'));
        }

        return true;
    }

    /**
     * Cancel invoice
     *
     * @param string $invoiceIncrementId
     * @return boolean
     */
    public function cancel($invoiceIncrementId)
    {
        $invoice = Mage::getModel('sales/order_invoice')->loadByIncrementId($invoiceIncrementId);

        /* @var $invoice Mage_Sales_Model_Order_Invoice */

        if (!$invoice->getId()) {
            $this->_fault('not_exists');
        }

        if (!$invoice->canCancel()) {
            $this->_fault('status_not_changed', Mage::helper('sales')->__('Invoice cannot be canceled.'));
        }

        try {
            $invoice->cancel();
            $invoice->getOrder()->setIsInProcess(true);
            $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('status_not_changed', $e->getMessage());
        } catch (Exception $e) {
            $this->_fault('status_not_changed', Mage::helper('sales')->__('Invoice canceling problem.'));
        }

        return true;
    }
}
