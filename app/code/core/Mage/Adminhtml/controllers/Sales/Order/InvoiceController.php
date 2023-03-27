<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order edit controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Sales_Order_InvoiceController extends Mage_Adminhtml_Controller_Sales_Invoice
{
    /**
     * Get requested items qty's from request
     */
    protected function _getItemQtys()
    {
        $data = $this->getRequest()->getParam('invoice');
        $qtys = $data['items'] ?? [];
        return $qtys;
    }

    /**
     * Initialize invoice model instance
     *
     * @return Mage_Sales_Model_Order_Invoice|false
     * @throws Mage_Core_Exception
     */
    protected function _initInvoice($update = false)
    {
        $this->_title($this->__('Sales'))->_title($this->__('Invoices'));

        $invoice = false;
        $itemsToInvoice = 0;
        $invoiceId = $this->getRequest()->getParam('invoice_id');
        $orderId = $this->getRequest()->getParam('order_id');
        if ($invoiceId) {
            $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
            if (!$invoice->getId()) {
                $this->_getSession()->addError($this->__('The invoice no longer exists.'));
                return false;
            }
        } elseif ($orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            /**
             * Check order existing
             */
            if (!$order->getId()) {
                $this->_getSession()->addError($this->__('The order no longer exists.'));
                return false;
            }
            /**
             * Check invoice create availability
             */
            if (!$order->canInvoice()) {
                $this->_getSession()->addError($this->__('The order does not allow creating an invoice.'));
                return false;
            }
            $savedQtys = $this->_getItemQtys();
            $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($savedQtys);
            if (!$invoice->getTotalQty()) {
                Mage::throwException($this->__('Cannot create an invoice without products.'));
            }
        }

        Mage::register('current_invoice', $invoice);
        return $invoice;
    }

    /**
     * Save data for invoice and related order
     *
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return  Mage_Adminhtml_Sales_Order_InvoiceController
     * @throws Exception
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

    /**
     * Prepare shipment
     *
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return Mage_Sales_Model_Order_Shipment|false
     * @throws Mage_Core_Exception
     */
    protected function _prepareShipment($invoice)
    {
        $savedQtys = $this->_getItemQtys();
        $shipment = Mage::getModel('sales/service_order', $invoice->getOrder())->prepareShipment($savedQtys);
        if (!$shipment->getTotalQty()) {
            return false;
        }

        $shipment->register();
        $tracks = $this->getRequest()->getPost('tracking');
        if ($tracks) {
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
        $invoice = $this->_initInvoice();
        if ($invoice) {
            $this->_title(sprintf("#%s", $invoice->getIncrementId()));

            $this->loadLayout()
                ->_setActiveMenu('sales/order');

            /** @var Mage_Adminhtml_Block_Sales_Order_Invoice_View $block */
            $block = $this->getLayout()->getBlock('sales_invoice_view');
            $block->updateBackButtonUrl($this->getRequest()->getParam('come_from'));

            $this->renderLayout();
        } else {
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
        $this->_redirect('*/*/new', ['order_id' => $this->getRequest()->getParam('order_id')]);
    }

    /**
     * Invoice create page
     */
    public function newAction()
    {
        $invoice = $this->_initInvoice();
        if ($invoice) {
            $this->_title($this->__('New Invoice'));

            if ($comment = Mage::getSingleton('adminhtml/session')->getCommentText(true)) {
                $invoice->setCommentText($comment);
            }

            $this->loadLayout()
                ->_setActiveMenu('sales/order')
                ->renderLayout();
        } else {
            $this->_redirect('*/sales_order/view', ['order_id' => $this->getRequest()->getParam('order_id')]);
        }
    }

    /**
     * Update items qty action
     */
    public function updateQtyAction()
    {
        try {
            $invoice = $this->_initInvoice(true);
            // Save invoice comment text in current invoice object in order to display it in corresponding view
            $invoiceRawData = $this->getRequest()->getParam('invoice');
            $invoiceRawCommentText = $invoiceRawData['comment_text'];
            $invoice->setCommentText($invoiceRawCommentText);

            $this->loadLayout();
            $response = $this->getLayout()->getBlock('order_items')->toHtml();
        } catch (Mage_Core_Exception $e) {
            $response = [
                'error'     => true,
                'message'   => $e->getMessage()
            ];
            $response = Mage::helper('core')->jsonEncode($response);
        } catch (Exception $e) {
            $response = [
                'error'     => true,
                'message'   => $this->__('Cannot update item quantity.')
            ];
            $response = Mage::helper('core')->jsonEncode($response);
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
        $orderId = $this->getRequest()->getParam('order_id');

        if (!empty($data['comment_text'])) {
            Mage::getSingleton('adminhtml/session')->setCommentText($data['comment_text']);
        }

        try {
            $invoice = $this->_initInvoice();
            if ($invoice) {
                if (!empty($data['capture_case'])) {
                    $invoice->setRequestedCaptureCase($data['capture_case']);
                }

                if (!empty($data['comment_text'])) {
                    $invoice->addComment(
                        $data['comment_text'],
                        isset($data['comment_customer_notify']),
                        isset($data['is_visible_on_front'])
                    );
                }

                $invoice->register();

                if (!empty($data['send_email'])) {
                    $invoice->setEmailSent(true);
                }

                $invoice->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
                $invoice->getOrder()->setIsInProcess(true);

                $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder());
                $shipment = false;
                if (!empty($data['do_shipment']) || (int) $invoice->getOrder()->getForcedDoShipmentWithInvoice()) {
                    $shipment = $this->_prepareShipment($invoice);
                    if ($shipment) {
                        $shipment->setEmailSent($invoice->getEmailSent());
                        $transactionSave->addObject($shipment);
                    }
                }
                $transactionSave->save();

                if (isset($shippingResponse) && $shippingResponse->hasErrors()) {
                    $this->_getSession()->addError($this->__('The invoice and the shipment  have been created. The shipping label cannot be created at the moment.'));
                } elseif (!empty($data['do_shipment'])) {
                    $this->_getSession()->addSuccess($this->__('The invoice and shipment have been created.'));
                } else {
                    $this->_getSession()->addSuccess($this->__('The invoice has been created.'));
                }

                // send invoice/shipment emails
                $comment = '';
                if (isset($data['comment_customer_notify'])) {
                    $comment = $data['comment_text'];
                }
                try {
                    $invoice->sendEmail(!empty($data['send_email']), $comment);
                } catch (Exception $e) {
                    Mage::logException($e);
                    $this->_getSession()->addError($this->__('Unable to send the invoice email.'));
                }
                if ($shipment) {
                    try {
                        $shipment->sendEmail(!empty($data['send_email']));
                    } catch (Exception $e) {
                        Mage::logException($e);
                        $this->_getSession()->addError($this->__('Unable to send the shipment email.'));
                    }
                }
                Mage::getSingleton('adminhtml/session')->getCommentText(true);
                $this->_redirect('*/sales_order/view', ['order_id' => $orderId]);
            } else {
                $this->_redirect('*/*/new', ['order_id' => $orderId]);
            }
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Unable to save the invoice.'));
            Mage::logException($e);
        }
        $this->_redirect('*/*/new', ['order_id' => $orderId]);
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
                $this->_getSession()->addSuccess($this->__('The invoice has been captured.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__('Invoice capturing error.'));
            }
            $this->_redirect('*/*/view', ['invoice_id' => $invoice->getId()]);
        } else {
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
                $this->_getSession()->addSuccess($this->__('The invoice has been canceled.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__('Invoice canceling error.'));
            }
            $this->_redirect('*/*/view', ['invoice_id' => $invoice->getId()]);
        } else {
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
                $this->_getSession()->addSuccess($this->__('The invoice has been voided.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__('Invoice voiding error.'));
            }
            $this->_redirect('*/*/view', ['invoice_id' => $invoice->getId()]);
        } else {
            $this->_forward('noRoute');
        }
    }

    public function addCommentAction()
    {
        try {
            $this->getRequest()->setParam('invoice_id', $this->getRequest()->getParam('id'));
            $data = $this->getRequest()->getPost('comment');
            if (empty($data['comment'])) {
                Mage::throwException($this->__('The Comment Text field cannot be empty.'));
            }
            $invoice = $this->_initInvoice();
            $invoice->addComment(
                $data['comment'],
                isset($data['is_customer_notified']),
                isset($data['is_visible_on_front'])
            );
            $invoice->sendUpdateEmail(!empty($data['is_customer_notified']), $data['comment']);
            $invoice->save();

            $this->loadLayout();
            $response = $this->getLayout()->getBlock('invoice_comments')->toHtml();
        } catch (Mage_Core_Exception $e) {
            $response = [
                'error'     => true,
                'message'   => $e->getMessage()
            ];
            $response = Mage::helper('core')->jsonEncode($response);
        } catch (Exception $e) {
            $response = [
                'error'     => true,
                'message'   => $this->__('Cannot add new comment.')
            ];
            $response = Mage::helper('core')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }

    /**
     * Decides if we need to create dummy invoice item or not
     * for example we don't need create dummy parent if all
     * children are not in process
     *
     * @deprecated after 1.4, Mage_Sales_Model_Service_Order used
     * @param Mage_Sales_Model_Order_Item $item
     * @param array $qtys
     * @return bool
     */
    protected function _needToAddDummy($item, $qtys)
    {
        if ($item->getHasChildren()) {
            foreach ($item->getChildrenItems() as $child) {
                if (isset($qtys[$child->getId()]) && $qtys[$child->getId()] > 0) {
                    return true;
                }
            }
            return false;
        }

        if ($item->getParentItem()) {
            if (isset($qtys[$item->getParentItem()->getId()]) && $qtys[$item->getParentItem()->getId()] > 0) {
                return true;
            }
            return false;
        }

        return false;
    }

    /**
     * Decides if we need to create dummy shipment item or not
     * for example we don't need create dummy parent if all
     * children are not in process
     *
     * @deprecated after 1.4, Mage_Sales_Model_Service_Order used
     * @param Mage_Sales_Model_Order_Item $item
     * @param array $qtys
     * @return bool
     */
    protected function _needToAddDummyForShipment($item, $qtys)
    {
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
        }

        if ($item->getParentItem()) {
            if ($item->getIsVirtual()) {
                return false;
            }
            if (isset($qtys[$item->getParentItem()->getId()]) && $qtys[$item->getParentItem()->getId()] > 0) {
                return true;
            }
            return false;
        }

        return false;
    }

    /**
     * Create pdf for current invoice
     */
    public function printAction()
    {
        $this->_initInvoice();
        parent::printAction();
    }
}
