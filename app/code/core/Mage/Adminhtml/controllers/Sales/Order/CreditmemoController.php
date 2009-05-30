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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order creditmemo controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Sales_Order_CreditmemoController extends Mage_Adminhtml_Controller_Sales_Creditmemo
{
    protected function _getItemData()
    {
        $data = $this->getRequest()->getParam('creditmemo');
        if (isset($data['items'])) {
            $qtys = $data['items'];
        }
        else {
            $qtys = array();
        }
        return $qtys;
    }

    protected function _canCreditmemo($order)
    {
        /**
         * Check order existing
         */
        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('Order not longer exist'));
            return false;
        }

        /**
         * Check creditmemo create availability
         */
        if (!$order->canCreditmemo()) {
            $this->_getSession()->addError($this->__('Can not do credit memo for order'));
            return false;
        }
        return true;
    }

    /**
     * Initialize creditmemo model instance
     *
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    protected function _initCreditmemo($update = false)
    {
        $creditmemo = false;
        if ($creditmemoId = $this->getRequest()->getParam('creditmemo_id')) {
            $creditmemo = Mage::getModel('sales/order_creditmemo')->load($creditmemoId);
        }
        elseif ($orderId = $this->getRequest()->getParam('order_id')) {
            $data   = $this->getRequest()->getParam('creditmemo');
            $order  = Mage::getModel('sales/order')->load($orderId);
            $invoiceId = $this->getRequest()->getParam('invoice_id');
            $invoice= null;

            if (!$this->_canCreditmemo($order)) {
                return false;
            }

            if ($invoiceId) {
                $invoice = Mage::getModel('sales/order_invoice')
                    ->load($invoiceId)
                    ->setOrder($order);
            }

            $convertor  = Mage::getModel('sales/convert_order');
            $creditmemo = $convertor->toCreditmemo($order)
                ->setInvoice($invoice);

            $savedData = $this->_getItemData();

            if ($invoice && $invoice->getId()) {
                foreach ($invoice->getAllItems() as $invoiceItem) {
                    $orderItem = $invoiceItem->getOrderItem();

                    if (!$orderItem->isDummy() && !$orderItem->getQtyToRefund()) {
                        continue;
                    }

                    if (!$update && $orderItem->isDummy() && !empty($savedData) && !$this->_needToAddDummy($orderItem, $savedData)) {
                        continue;
                    }

                    $item = $convertor->itemToCreditmemoItem($orderItem);
                    if (isset($savedData[$orderItem->getId()]['qty'])) {
                        $qty = $savedData[$orderItem->getId()]['qty'];
                    }
                    else {
                        if ($orderItem->isDummy()) {
                            if ($orderItem->getParentItem() && isset($savedData[$orderItem->getParentItem()->getId()]['qty'])) {
                                $parentItemNewQty = $savedData[$orderItem->getParentItem()->getId()]['qty'];
                                $parentItemOrigQty = $orderItem->getParentItem()->getQtyOrdered();
                                $itemOrigQty = $orderItem->getQtyOrdered()/$parentItemOrigQty;
                                $qty = $itemOrigQty*$parentItemNewQty;
                                if (isset($savedData[$orderItem->getParentItem()->getId()]['back_to_stock'])) {
                                    $savedData[$orderItem->getId()]['back_to_stock'] = 1;
                                }
                            } else {
                                $qty = 1;
                            }
                        } else {
                            $qty = min($orderItem->getQtyToRefund(), $invoiceItem->getQty());
                        }
                    }

                    $item->setQty($qty);

                    $children = $orderItem->getChildrenItems();
                    if (!empty($children)) {
                        $item->setBackToStock(false);
                    } else {
                        $item->setBackToStock(isset($savedData[$orderItem->getId()]['back_to_stock']));
                    }

                    $creditmemo->addItem($item);
                }
            } else {
                foreach ($order->getAllItems() as $orderItem) {

                    if (!$orderItem->isDummy() && !$orderItem->getQtyToRefund()) {
                        continue;
                    }

                    if (!$update && $orderItem->isDummy() && !empty($savedData) && !$this->_needToAddDummy($orderItem, $savedData)) {
                        continue;
                    }

                    $item = $convertor->itemToCreditmemoItem($orderItem);
                    if (isset($savedData[$orderItem->getId()]['qty'])) {
                        $qty = $savedData[$orderItem->getId()]['qty'];
                    }
                    else {
                        if ($orderItem->isDummy()) {
                            if ($orderItem->getParentItem() && isset($savedData[$orderItem->getParentItem()->getId()]['qty'])) {
                                $parentItemNewQty = $savedData[$orderItem->getParentItem()->getId()]['qty'];
                                $parentItemOrigQty = $orderItem->getParentItem()->getQtyOrdered();
                                $itemOrigQty = $orderItem->getQtyOrdered()/$parentItemOrigQty;
                                $qty = $itemOrigQty*$parentItemNewQty;
                                if (isset($savedData[$orderItem->getParentItem()->getId()]['back_to_stock'])) {
                                    $savedData[$orderItem->getId()]['back_to_stock'] = 1;
                                }
                            } else {
                                $qty = 1;
                            }
                        } else {
                            $qty = $orderItem->getQtyToRefund();
                        }
                    }

                    $item->setQty($qty);

                    $children = $orderItem->getChildrenItems();
                    if (!empty($children)) {
                        $item->setBackToStock(false);
                    } else {
                        $item->setBackToStock(isset($savedData[$orderItem->getId()]['back_to_stock']));
                    }

                    $creditmemo->addItem($item);
                }
            }

            if (isset($data['shipping_amount'])) {
                $creditmemo->setShippingAmount($data['shipping_amount']);
            } elseif ($invoice) {
                $creditmemo->setShippingAmount($invoice->getShippingAmount());
            }
            else {
                $creditmemo->setShippingAmount(
                    $order->getBaseShippingAmount()-$order->getBaseShippingRefunded()
                );
            }

            if (isset($data['adjustment_positive'])) {
                $creditmemo->setAdjustmentPositive($data['adjustment_positive']);
            }
            if (isset($data['adjustment_negative'])) {
                $creditmemo->setAdjustmentNegative($data['adjustment_negative']);
            }

            $creditmemo->collectTotals();
        }

        $args = array(
            'creditmemo' => $creditmemo,
            'request'    => $this->getRequest(),
        );
        Mage::dispatchEvent('adminhtml_sales_order_creditmemo_register_before', $args);

        Mage::register('current_creditmemo', $creditmemo);
        return $creditmemo;
    }

    protected function _saveCreditmemo($creditmemo)
    {
        $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($creditmemo)
            ->addObject($creditmemo->getOrder());
        if ($creditmemo->getInvoice()) {
            $transactionSave->addObject($creditmemo->getInvoice());
        }
        $transactionSave->save();

        return $this;
    }

    /**
     * creditmemo information page
     */
    public function viewAction()
    {
        if ($creditmemo = $this->_initCreditmemo()) {
            $this->loadLayout();
            $this->getLayout()->getBlock('sales_creditmemo_view')
                ->updateBackButtonUrl($this->getRequest()->getParam('come_from'));
            $this->_setActiveMenu('sales/order')
                ->renderLayout();
        }
        else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Start create creditmemo action
     */
    public function startAction()
    {
        /**
         * Clear old values for creditmemo qty's
         */
        $this->_redirect('*/*/new', array('_current'=>true));
    }

    /**
     * creditmemo create page
     */
    public function newAction()
    {
        if ($creditmemo = $this->_initCreditmemo()) {
            $commentText = Mage::getSingleton('adminhtml/session')->getCommentText(true);

            $creditmemo->addData(array('commentText'=>$commentText));

            $this->loadLayout()
                ->_setActiveMenu('sales/order')
                ->renderLayout();
        }
        else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Update items qty action
     */
    public function updateQtyAction()
    {
        try {
            $creditmemo = $this->_initCreditmemo(true);
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
     * Save creditmemo
     * We can save only new creditmemo. Existing creditmemos are not editable
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost('creditmemo');
        try {
            if ($creditmemo = $this->_initCreditmemo()) {
                if (($creditmemo->getGrandTotal() <=0) && (!$creditmemo->getAllowZeroGrandTotal())) {
                    Mage::throwException(
                        $this->__('Credit Memo total must be positive.')
                    );
                }

                Mage::getSingleton('adminhtml/session')->setCommentText($data['comment_text']);

                $comment = '';
                if (!empty($data['comment_text'])) {
                    $comment = $data['comment_text'];
                    $creditmemo->addComment($data['comment_text'], isset($data['comment_customer_notify']));
                }

                if (isset($data['do_refund'])) {
                    $creditmemo->setRefundRequested(true);
                }
                if (isset($data['do_offline'])) {
                    $creditmemo->setOfflineRequested($data['do_offline']);
                }

                $creditmemo->register();
                if (!empty($data['send_email'])) {
                    $creditmemo->setEmailSent(true);
                }

                $this->_saveCreditmemo($creditmemo);
                $creditmemo->sendEmail(!empty($data['send_email']), $comment);
                $this->_getSession()->addSuccess($this->__('Credit Memo was successfully created'));
                Mage::getSingleton('adminhtml/session')->getCommentText(true);
                $this->_redirect('*/sales_order/view', array('order_id' => $creditmemo->getOrderId()));
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
            $this->_getSession()->addError($this->__('Can not save credit memo'));
        }
        $this->_redirect('*/*/new', array('_current' => true));
    }

    /**
     * Cancel creditmemo action
     */
    public function cancelAction()
    {
        if ($creditmemo = $this->_initCreditmemo()) {
            try {
                $creditmemo->cancel();
                $this->_saveCreditmemo($creditmemo);
                $this->_getSession()->addSuccess($this->__('Credit Memo was successfully canceled.'));
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError($this->__('Credit Memo cancel error.'));
            }
            $this->_redirect('*/*/view', array('creditmemo_id'=>$creditmemo->getId()));
        }
        else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Void creditmemo action
     */
    public function voidAction()
    {
        if ($invoice = $this->_initCreditmemo()) {
            try {
                $creditmemo->void();
                $this->_saveCreditmemo($creditmemo);
                $this->_getSession()->addSuccess($this->__('Credit Memo was successfully voided'));
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError($this->__('Credit Memo void error'));
            }
            $this->_redirect('*/*/view', array('creditmemo_id'=>$creditmemo->getId()));
        }
        else {
            $this->_forward('noRoute');
        }
    }

    public function addCommentAction()
    {
        try {
            $this->getRequest()->setParam(
                'creditmemo_id',
                $this->getRequest()->getParam('id')
            );
            $data = $this->getRequest()->getPost('comment');
            if (empty($data['comment'])) {
                Mage::throwException($this->__('Comment text field can not be empty.'));
            }
            $creditmemo = $this->_initCreditmemo();
            $creditmemo->addComment($data['comment'], isset($data['is_customer_notified']));
            $creditmemo->save();
            $creditmemo->sendUpdateEmail(!empty($data['is_customer_notified']), $data['comment']);

            $this->loadLayout();
            $response = $this->getLayout()->getBlock('creditmemo_comments')->toHtml();
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
                if (isset($qtys[$child->getId()])
                    && isset($qtys[$child->getId()]['qty'])
                    && $qtys[$child->getId()]['qty'] > 0)
                {
                    return true;
                }
            }
            return false;
        } else if($item->getParentItem()) {
            if (isset($qtys[$item->getParentItem()->getId()])
                && isset($qtys[$item->getParentItem()->getId()]['qty'])
                && $qtys[$item->getParentItem()->getId()]['qty'] > 0)
            {
                return true;
            }
            return false;
        }
    }
}
