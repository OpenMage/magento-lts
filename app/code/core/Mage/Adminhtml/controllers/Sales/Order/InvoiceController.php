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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order edit controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Sales_Order_InvoiceController extends Mage_Adminhtml_Controller_Sales_Invoice
{
    protected function _getItemQtys()
    {
        $data = $this->getRequest()->getParam('invoice');
        if (isset($data['items'])) {
            $qtys = $data['items'];
            //$this->_getSession()->setInvoiceItemQtys($qtys);
        }
        /*elseif ($this->_getSession()->getInvoiceItemQtys()) {
            $qtys = $this->_getSession()->getInvoiceItemQtys();
        }*/
        else {
            $qtys = array();
        }
        return $qtys;
    }

    /**
     * Initialize invoice model instance
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    protected function _initInvoice($update = false)
    {
        $invoice = false;
        if ($invoiceId = $this->getRequest()->getParam('invoice_id')) {
            $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
        }
        elseif ($orderId = $this->getRequest()->getParam('order_id')) {
            $order      = Mage::getModel('sales/order')->load($orderId);
            /**
             * Check order existing
             */
            if (!$order->getId()) {
                $this->_getSession()->addError($this->__('Order not longer exist'));
                return false;
            }
            /**
             * Check invoice create availability
             */
            if (!$order->canInvoice()) {
                $this->_getSession()->addError($this->__('Can not do invoice for order'));
                return false;
            }

            $convertor  = Mage::getModel('sales/convert_order');
            $invoice    = $convertor->toInvoice($order);

            $savedQtys = $this->_getItemQtys();
            foreach ($order->getAllItems() as $orderItem) {

                if (!$orderItem->isDummy() && !$orderItem->getQtyToInvoice()) {
                    continue;
                }

                if (!$update && $orderItem->isDummy() && !empty($savedQtys) && !$this->_needToAddDummy($orderItem, $savedQtys)) {
                    continue;
                }
                $item = $convertor->itemToInvoiceItem($orderItem);

                if (isset($savedQtys[$orderItem->getId()])) {
                    $qty = $savedQtys[$orderItem->getId()];
                }
                else {
                    if ($orderItem->isDummy()) {
                        $qty = 1;
                    } else {
                        $qty = $orderItem->getQtyToInvoice();
                    }
                }
                $item->setQty($qty);
                $invoice->addItem($item);
            }
            $invoice->collectTotals();
        }

        Mage::register('current_invoice', $invoice);
        return $invoice;
    }

    /**
     * Save data for invoice and related order
     *
     * @param   Mage_Sales_Model_Order_Invoice $invoice
     * @return  Mage_Adminhtml_Sales_Order_InvoiceController
     */
    protected function _saveInvoice($invoice)
    {
        $invoice->getOrder()->setIsInProcess(true);
        $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($invoice)
            ->addObject($invoice->getOrder())
            ->save();

        return $this;
    }

    protected function _prepareShipment($invoice)
    {
        $convertor  = Mage::getModel('sales/convert_order');
        $shipment    = $convertor->toShipment($invoice->getOrder());

        $savedQtys = $this->_getItemQtys();
        $skipedParent = array();
        //echo "<pre>";
        foreach ($invoice->getOrder()->getAllItems() as $item) {
            //echo "\n".$item->getSku();
            /*
             * if this is child and its parent was skipped
             * bc of something we need to skip child also
             */
            if ($item->getParentItem() && isset($skipedParent[$item->getParentItem()->getId()])){
                continue;
            }
            //echo "1";
            if (isset($savedQtys[$item->getId()])) {
                $qty = min($savedQtys[$item->getId()], $item->getQtyToShip());
            } else {
                $qty = $item->getQtyToShip();
            }
            //echo "2";
            if (!$item->isDummy(true) && !$item->getQtyToShip()) {
                continue;
            }
            //echo "3";
            /**
             * if this is a dummy item and we don't need it. we skip it.
             * also if this item is parent we need to mark that we skipped
             * it so children will be also skipped
             */
            if ($item->isDummy(true) && !$this->_needToAddDummyForShipment($item, $savedQtys)) {
                if ($item->getChildrenItems()) {
                    $skipedParent[$item->getId()] = 1;
                }
                continue;
            }
            //echo "4";
            if ($item->getIsVirtual()) {
                continue;
            }
            //echo "5";
            $shipItem = $convertor->itemToShipmentItem($item);

            if ($item->isDummy(true)) {
                $qty = 1;
            }
            //echo "Qty:".$qty;
            $shipItem->setQty($qty);
            $shipment->addItem($shipItem);
        }
        //die;
        if (!count($shipment->getAllItems())) {
            // no need to create empty shipment
            return false;
        }

        $shipment->register();

        if ($tracks = $this->getRequest()->getPost('tracking')) {
            foreach ($tracks as $data) {
                $track = Mage::getModel('sales/order_shipment_track')
                ->addData($data);
                $shipment->addTrack($track);
            }
        }
        return $shipment;
    }

    /**
     * Invoice information page
     */
    public function viewAction()
    {
        if ($invoice = $this->_initInvoice()) {
            $this->loadLayout()
                ->_setActiveMenu('sales/order');
            $this->getLayout()->getBlock('sales_invoice_view')
                ->updateBackButtonUrl($this->getRequest()->getParam('come_from'));
            $this->renderLayout();
        }
        else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Start create invoice action
     */
    public function startAction()
    {
        /**
         * Clear old values for invoice qty's
         */
        $this->_getSession()->getInvoiceItemQtys(true);
        $this->_redirect('*/*/new', array('order_id'=>$this->getRequest()->getParam('order_id')));
    }

    /**
     * Invoice create page
     */
    public function newAction()
    {
        if ($invoice = $this->_initInvoice()) {
            $this->loadLayout()
                ->_setActiveMenu('sales/order')
                ->renderLayout();
        }
        else {
            // $this->_forward('noRoute');
            $this->_redirect('*/sales_order/view', array('order_id'=>$this->getRequest()->getParam('order_id')));
        }
    }

    /**
     * Update items qty action
     */
    public function updateQtyAction()
    {
        try {
            $invoice = $this->_initInvoice(true);
            $this->loadLayout();
            $response = $this->getLayout()->getBlock('order_items')->toHtml();
        }
        catch (Mage_Core_Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage()
            );
            $response = Zend_Json::encode($response);
        }
        catch (Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $this->__('Can not update item qty')
            );
            $response = Zend_Json::encode($response);
        }
        $this->getResponse()->setBody($response);
    }

    /**
     * Save invoice
     * We can save only new invoice. Existing invoices are not editable
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost('invoice');
        try {
            if ($invoice = $this->_initInvoice()) {

                if (!empty($data['capture_case'])) {
                    $invoice->setRequestedCaptureCase($data['capture_case']);
                }

                if (!empty($data['comment_text'])) {
                    $invoice->addComment($data['comment_text'], isset($data['comment_customer_notify']));
                }

                $invoice->register();

                if (!empty($data['send_email'])) {
                    $invoice->setEmailSent(true);
                }

                $invoice->getOrder()->setIsInProcess(true);

                $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder());
                $shipment = false;
                if (!empty($data['do_shipment'])) {
                    $shipment = $this->_prepareShipment($invoice);
                    if ($shipment) {
                        $transactionSave->addObject($shipment);
                    }
                }
                $transactionSave->save();

                /**
                 * Sending emails
                 */
                $comment = '';
                if (isset($data['comment_customer_notify'])) {
                    $comment = $data['comment_text'];
                }
                $invoice->sendEmail(!empty($data['send_email']), $comment);
                if ($shipment) {
                    $shipment->sendEmail(!empty($data['send_email']));
                }

                if (!empty($data['do_shipment'])) {
                    $this->_getSession()->addSuccess($this->__('Invoice and shipment was successfully created.'));
                }
                else {
                    $this->_getSession()->addSuccess($this->__('Invoice was successfully created.'));
                }

                $this->_redirect('*/sales_order/view', array('order_id' => $invoice->getOrderId()));
                return;
            }
            else {
                $this->_forward('noRoute');
                return;
            }
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addError($this->__('Can not save invoice'));
        }

        $this->_redirect('*/*/new', array('order_id' => $this->getRequest()->getParam('order_id')));
    }


    /**
     * Capture invoice action
     */
    public function captureAction()
    {
        if ($invoice = $this->_initInvoice()) {
            try {
                $invoice->capture();
                $this->_saveInvoice($invoice);
                $this->_getSession()->addSuccess($this->__('Invoice was successfully captured'));
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError($this->__('Invoice capture error'));
            }
            $this->_redirect('*/*/view', array('invoice_id'=>$invoice->getId()));
        }
        else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Cancel invoice action
     */
    public function cancelAction()
    {
        if ($invoice = $this->_initInvoice()) {
            try {
                $invoice->cancel();
                $this->_saveInvoice($invoice);
                $this->_getSession()->addSuccess($this->__('Invoice was successfully canceled.'));
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError($this->__('Invoice cancel error.'));
            }
            $this->_redirect('*/*/view', array('invoice_id'=>$invoice->getId()));
        }
        else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Void invoice action
     */
    public function voidAction()
    {
        if ($invoice = $this->_initInvoice()) {
            try {
                $invoice->void();
                $this->_saveInvoice($invoice);
                $this->_getSession()->addSuccess($this->__('Invoice was successfully voided'));
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError($this->__('Invoice void error'));
            }
            $this->_redirect('*/*/view', array('invoice_id'=>$invoice->getId()));
        }
        else {
            $this->_forward('noRoute');
        }
    }

    public function addCommentAction()
    {
        try {
            $this->getRequest()->setParam('invoice_id', $this->getRequest()->getParam('id'));
            $data = $this->getRequest()->getPost('comment');
            if (empty($data['comment'])) {
                Mage::throwException($this->__('Comment text field can not be empty.'));
            }
            $invoice = $this->_initInvoice();
            $invoice->addComment($data['comment'], isset($data['is_customer_notified']));
            $invoice->sendUpdateEmail(!empty($data['is_customer_notified']), $data['comment']);
            $invoice->save();

            $this->loadLayout();
            $response = $this->getLayout()->getBlock('invoice_comments')->toHtml();
        }
        catch (Mage_Core_Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage()
            );
            $response = Zend_Json::encode($response);
        }
        catch (Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $this->__('Can not add new comment.')
            );
            $response = Zend_Json::encode($response);
        }
        $this->getResponse()->setBody($response);
    }

    /**
     * Decides if we need to create dummy invoice item or not
     * for eaxample we don't need create dummy parent if all
     * children are not in process
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @param array $qtys
     * @return bool
     */
    protected function _needToAddDummy($item, $qtys) {
        if ($item->getHasChildren()) {
            foreach ($item->getChildrenItems() as $child) {
                if (isset($qtys[$child->getId()]) && $qtys[$child->getId()] > 0) {
                    return true;
                }
            }
            return false;
        } else if($item->getParentItem()) {
            if (isset($qtys[$item->getParentItem()->getId()]) && $qtys[$item->getParentItem()->getId()] > 0) {
                return true;
            }
            return false;
        }
    }

    /**
     * Decides if we need to create dummy shipment item or not
     * for eaxample we don't need create dummy parent if all
     * children are not in process
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @param array $qtys
     * @return bool
     */
    protected function _needToAddDummyForShipment($item, $qtys) {
        if ($item->getHasChildren()) {
            foreach ($item->getChildrenItems() as $child) {
                if ($child->getIsVirtual()) {
                    continue;
                }
                if (isset($qtys[$child->getId()]) && $qtys[$child->getId()] > 0) {
                    return true;
                }
            }
            if ($item->isShipSeparately()) {
                return true;
            }
            return false;
        } else if($item->getParentItem()) {
            if ($item->getIsVirtual()) {
                return false;
            }
            if (isset($qtys[$item->getParentItem()->getId()]) && $qtys[$item->getParentItem()->getId()] > 0) {
                return true;
            }
            return false;
        }
    }

}