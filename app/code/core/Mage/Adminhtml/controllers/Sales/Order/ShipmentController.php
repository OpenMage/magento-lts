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
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order shipment controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Sales_Order_ShipmentController extends Mage_Adminhtml_Controller_Sales_Shipment
{
    /**
     * Initialize shipment items QTY
     */
    protected function _getItemQtys()
    {
        $data = $this->getRequest()->getParam('shipment');
        if (isset($data['items'])) {
            $qtys = $data['items'];
        } else {
            $qtys = array();
        }
        return $qtys;
    }

    /**
     * Initialize shipment model instance
     *
     * @return Mage_Sales_Model_Order_Shipment|bool
     */
    protected function _initShipment()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Shipments'));

        $shipment = false;
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        $orderId = $this->getRequest()->getParam('order_id');
        if ($shipmentId) {
            $shipment = Mage::getModel('sales/order_shipment')->load($shipmentId);
        } elseif ($orderId) {
            $order      = Mage::getModel('sales/order')->load($orderId);

            /**
             * Check order existing
             */
            if (!$order->getId()) {
                $this->_getSession()->addError($this->__('The order no longer exists.'));
                return false;
            }
            /**
             * Check shipment is available to create separate from invoice
             */
            if ($order->getForcedDoShipmentWithInvoice()) {
                $this->_getSession()->addError($this->__('Cannot do shipment for the order separately from invoice.'));
                return false;
            }
            /**
             * Check shipment create availability
             */
            if (!$order->canShip()) {
                $this->_getSession()->addError($this->__('Cannot do shipment for the order.'));
                return false;
            }
            $savedQtys = $this->_getItemQtys();
            $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($savedQtys);

            $tracks = $this->getRequest()->getPost('tracking');
            if ($tracks) {
                foreach ($tracks as $data) {
                    if (empty($data['number'])) {
                        Mage::throwException($this->__('Tracking number cannot be empty.'));
                    }
                    $track = Mage::getModel('sales/order_shipment_track')
                        ->addData($data);
                    $shipment->addTrack($track);
                }
            }
        }

        Mage::register('current_shipment', $shipment);
        return $shipment;
    }

    /**
     * Save shipment and order in one transaction
     *
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @return $this
     */
    protected function _saveShipment($shipment)
    {
        $shipment->getOrder()->setIsInProcess(true);
        $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($shipment)
            ->addObject($shipment->getOrder())
            ->save();

        return $this;
    }

    /**
     * Shipment information page
     */
    public function viewAction()
    {
        if ($this->_initShipment()) {
            $this->_title($this->__('View Shipment'));

            $this->loadLayout();
            $this->getLayout()->getBlock('sales_shipment_view')
                ->updateBackButtonUrl($this->getRequest()->getParam('come_from'));
            $this->_setActiveMenu('sales/order')
                ->renderLayout();
        } else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Start create shipment action
     */
    public function startAction()
    {
        /**
         * Clear old values for shipment qty's
         */
        $this->_redirect('*/*/new', array('order_id'=>$this->getRequest()->getParam('order_id')));
    }

    /**
     * Shipment create page
     */
    public function newAction()
    {
        if ($shipment = $this->_initShipment()) {
            $this->_title($this->__('New Shipment'));

            $comment = Mage::getSingleton('adminhtml/session')->getCommentText(true);
            if ($comment) {
                $shipment->setCommentText($comment);
            }

            $this->loadLayout()
                ->_setActiveMenu('sales/order')
                ->renderLayout();
        } else {
            $this->_redirect('*/sales_order/view', array('order_id'=>$this->getRequest()->getParam('order_id')));
        }
    }

    /**
     * Save shipment
     * We can save only new shipment. Existing shipments are not editable
     *
     * @return null
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost('shipment');
        if (!empty($data['comment_text'])) {
            Mage::getSingleton('adminhtml/session')->setCommentText($data['comment_text']);
        }

        $shipment = $this->_initShipment();
        if (!$shipment) {
            $this->_forward('noRoute');
            return;
        }

        $responseAjax = new Varien_Object();
        $isNeedCreateLabel = isset($data['create_shipping_label']) && $data['create_shipping_label'];

        try {
            $shipment->register();
            $comment = '';
            if (!empty($data['comment_text'])) {
                $shipment->addComment(
                    $data['comment_text'],
                    isset($data['comment_customer_notify']),
                    isset($data['is_visible_on_front'])
                );
                if (isset($data['comment_customer_notify'])) {
                    $comment = $data['comment_text'];
                }
            }

            if (!empty($data['send_email'])) {
                $shipment->setEmailSent(true);
            }

            $shipment->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));

            if ($isNeedCreateLabel && $this->_createShippingLabel($shipment)) {
                $responseAjax->setOk(true);
            }

            $this->_saveShipment($shipment);

            $shipment->sendEmail(!empty($data['send_email']), $comment);

            $shipmentCreatedMessage = $this->__('The shipment has been created.');
            $labelCreatedMessage    = $this->__('The shipping label has been created.');

            $this->_getSession()->addSuccess($isNeedCreateLabel ? $shipmentCreatedMessage . ' ' . $labelCreatedMessage
                : $shipmentCreatedMessage);
            Mage::getSingleton('adminhtml/session')->getCommentText(true);
        } catch (Mage_Core_Exception $e) {
            if ($isNeedCreateLabel) {
                $responseAjax->setError(true);
                $responseAjax->setMessage($e->getMessage());
            } else {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/new', array('order_id' => $this->getRequest()->getParam('order_id')));
            }
        } catch (Exception $e) {
            Mage::logException($e);
            if ($isNeedCreateLabel) {
                $responseAjax->setError(true);
                $responseAjax->setMessage(
                    Mage::helper('sales')->__('An error occurred while creating shipping label.'));
            } else {
                $this->_getSession()->addError($this->__('Cannot save shipment.'));
                $this->_redirect('*/*/new', array('order_id' => $this->getRequest()->getParam('order_id')));
            }
        }
        if ($isNeedCreateLabel) {
            $this->getResponse()->setBody($responseAjax->toJson());
        } else {
            $this->_redirect('*/sales_order/view', array('order_id' => $shipment->getOrderId()));
        }
    }

    /**
     * Send email with shipment data to customer
     */
    public function emailAction()
    {
        try {
            $shipment = $this->_initShipment();
            if ($shipment) {
                $shipment->sendEmail(true)
                    ->setEmailSent(true)
                    ->save();
                $historyItem = Mage::getResourceModel('sales/order_status_history_collection')
                    ->getUnnotifiedForInstance($shipment, Mage_Sales_Model_Order_Shipment::HISTORY_ENTITY_NAME);
                if ($historyItem) {
                    $historyItem->setIsCustomerNotified(1);
                    $historyItem->save();
                }
                $this->_getSession()->addSuccess($this->__('The shipment has been sent.'));
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Cannot send shipment information.'));
        }
        $this->_redirect('*/*/view', array(
            'shipment_id' => $this->getRequest()->getParam('shipment_id')
        ));
    }

    /**
     * Add new tracking number action
     */
    public function addTrackAction()
    {
        try {
            $carrier = $this->getRequest()->getPost('carrier');
            $number  = $this->getRequest()->getPost('number');
            $title  = $this->getRequest()->getPost('title');
            if (empty($carrier)) {
                Mage::throwException($this->__('The carrier needs to be specified.'));
            }
            if (empty($number)) {
                Mage::throwException($this->__('Tracking number cannot be empty.'));
            }
            $shipment = $this->_initShipment();
            if ($shipment) {
                $track = Mage::getModel('sales/order_shipment_track')
                    ->setNumber($number)
                    ->setCarrierCode($carrier)
                    ->setTitle($title);
                $shipment->addTrack($track)
                    ->save();

                $this->loadLayout();
                $response = $this->getLayout()->getBlock('shipment_tracking')->toHtml();
            } else {
                $response = array(
                    'error'     => true,
                    'message'   => $this->__('Cannot initialize shipment for adding tracking number.'),
                );
            }
        } catch (Mage_Core_Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage(),
            );
        } catch (Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $this->__('Cannot add tracking number.'),
            );
        }
        if (is_array($response)) {
            $response = Mage::helper('core')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }

    /**
     * Remove tracking number from shipment
     */
    public function removeTrackAction()
    {
        $trackId    = $this->getRequest()->getParam('track_id');
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        $track = Mage::getModel('sales/order_shipment_track')->load($trackId);
        if ($track->getId()) {
            try {
                if ($this->_initShipment()) {
                    $track->delete();

                    $this->loadLayout();
                    $response = $this->getLayout()->getBlock('shipment_tracking')->toHtml();
                } else {
                    $response = array(
                        'error'     => true,
                        'message'   => $this->__('Cannot initialize shipment for delete tracking number.'),
                    );
                }
            } catch (Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $this->__('Cannot delete tracking number.'),
                );
            }
        } else {
            $response = array(
                'error'     => true,
                'message'   => $this->__('Cannot load track with retrieving identifier.'),
            );
        }
        if (is_array($response)) {
            $response = Mage::helper('core')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }

    /**
     * View shipment tracking information
     */
    public function viewTrackAction()
    {
        $trackId    = $this->getRequest()->getParam('track_id');
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        $track = Mage::getModel('sales/order_shipment_track')->load($trackId);
        if ($track->getId()) {
            try {
                $response = $track->getNumberDetail();
            } catch (Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $this->__('Cannot retrieve tracking number detail.'),
                );
            }
        } else {
            $response = array(
                'error'     => true,
                'message'   => $this->__('Cannot load track with retrieving identifier.'),
            );
        }

        if ( is_object($response)){
            $className = Mage::getConfig()->getBlockClassName('adminhtml/template');
            $block = new $className();
            $block->setType('adminhtml/template')
                ->setIsAnonymous(true)
                ->setTemplate('sales/order/shipment/tracking/info.phtml');

            $block->setTrackingInfo($response);

            $this->getResponse()->setBody($block->toHtml());
        } else {
            if (is_array($response)) {
                $response = Mage::helper('core')->jsonEncode($response);
            }

            $this->getResponse()->setBody($response);
        }
    }

    /**
     * Add comment to shipment history
     */
    public function addCommentAction()
    {
        try {
            $this->getRequest()->setParam(
                'shipment_id',
                $this->getRequest()->getParam('id')
            );
            $data = $this->getRequest()->getPost('comment');
            if (empty($data['comment'])) {
                Mage::throwException($this->__('Comment text field cannot be empty.'));
            }
            $shipment = $this->_initShipment();
            $shipment->addComment(
                $data['comment'],
                isset($data['is_customer_notified']),
                isset($data['is_visible_on_front'])
            );
            $shipment->sendUpdateEmail(!empty($data['is_customer_notified']), $data['comment']);
            $shipment->save();

            $this->loadLayout(false);
            $response = $this->getLayout()->getBlock('shipment_comments')->toHtml();
        } catch (Mage_Core_Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage()
            );
            $response = Mage::helper('core')->jsonEncode($response);
        } catch (Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $this->__('Cannot add new comment.')
            );
            $response = Mage::helper('core')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }



    /**
     * Decides if we need to create dummy shipment item or not
     * for eaxample we don't need create dummy parent if all
     * children are not in process
     *
     * @deprecated after 1.4, Mage_Sales_Model_Service_Order used
     * @param Mage_Sales_Model_Order_Item $item
     * @param array $qtys
     * @return bool
     */
    protected function _needToAddDummy($item, $qtys) {
        if ($item->getHasChildren()) {
            foreach ($item->getChildrenItems() as $child) {
                if ($child->getIsVirtual()) {
                    continue;
                }
                if ((isset($qtys[$child->getId()]) && $qtys[$child->getId()] > 0)
                        || (!isset($qtys[$child->getId()]) && $child->getQtyToShip())) {
                    return true;
                }
            }
            return false;
        } else if($item->getParentItem()) {
            if ($item->getIsVirtual()) {
                return false;
            }
            if ((isset($qtys[$item->getParentItem()->getId()]) && $qtys[$item->getParentItem()->getId()] > 0)
                || (!isset($qtys[$item->getParentItem()->getId()]) && $item->getParentItem()->getQtyToShip())) {
                return true;
            }
            return false;
        }
    }

    /**
     * Create shipping label for specific shipment with validation.
     *
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @return bool
     */
    protected function _createShippingLabel(Mage_Sales_Model_Order_Shipment $shipment)
    {
        if (!$shipment) {
            return false;
        }
        $carrier = $shipment->getOrder()->getShippingCarrier();
        if (!$carrier->isShippingLabelsAvailable()) {
            return false;
        }
        $shipment->setPackages($this->getRequest()->getParam('packages'));
        $response = Mage::getModel('shipping/shipping')->requestToShipment($shipment);
        if ($response->hasErrors()) {
            Mage::throwException($response->getErrors());
        }
        if (!$response->hasInfo()) {
            return false;
        }
        $labelsContent = array();
        $trackingNumbers = array();
        $info = $response->getInfo();
        foreach ($info as $inf) {
            if (!empty($inf['tracking_number']) && !empty($inf['label_content'])) {
                $labelsContent[] = $inf['label_content'];
                $trackingNumbers[] = $inf['tracking_number'];
            }
        }
        $outputPdf = $this->_combineLabelsPdf($labelsContent);
        $shipment->setShippingLabel($outputPdf->render());
        $carrierCode = $carrier->getCarrierCode();
        $carrierTitle = Mage::getStoreConfig('carriers/'.$carrierCode.'/title', $shipment->getStoreId());
        if ($trackingNumbers) {
            foreach ($trackingNumbers as $trackingNumber) {
                $track = Mage::getModel('sales/order_shipment_track')
                        ->setNumber($trackingNumber)
                        ->setCarrierCode($carrierCode)
                        ->setTitle($carrierTitle);
                $shipment->addTrack($track);
            }
        }
        return true;
    }

    /**
     * Create shipping label action for specific shipment
     *
     */
    public function createLabelAction()
    {
        $response = new Varien_Object();
        try {
            $shipment = $this->_initShipment();
            if ($this->_createShippingLabel($shipment)) {
                $shipment->save();
                $this->_getSession()->addSuccess(Mage::helper('sales')->__('The shipping label has been created.'));
                $response->setOk(true);
            }
        } catch (Mage_Core_Exception $e) {
            $response->setError(true);
            $response->setMessage($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
            $response->setError(true);
            $response->setMessage(Mage::helper('sales')->__('An error occurred while creating shipping label.'));
        }

        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Print label for one specific shipment
     *
     */
    public function printLabelAction()
    {
        try {
            $shipment = $this->_initShipment();
            $labelContent = $shipment->getShippingLabel();
            if ($labelContent) {
                $pdfContent = null;
                if (stripos($labelContent, '%PDF-') !== false) {
                    $pdfContent = $labelContent;
                } else {
                    $pdf = new Zend_Pdf();
                    $page = $this->_createPdfPageFromImageString($labelContent);
                    if (!$page) {
                        $this->_getSession()->addError(Mage::helper('sales')->__('File extension not known or unsupported type in the following shipment: %s', $shipment->getIncrementId()));
                    }
                    $pdf->pages[] = $page;
                    $pdfContent = $pdf->render();
                }

                return $this->_prepareDownloadResponse(
                    'ShippingLabel(' . $shipment->getIncrementId() . ').pdf',
                    $pdfContent,
                    'application/pdf'
                );
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()
                ->addError(Mage::helper('sales')->__('An error occurred while creating shipping label.'));
       }
       $this->_redirect('*/sales_order_shipment/view', array(
           'shipment_id' => $this->getRequest()->getParam('shipment_id')
       ));
    }

    /**
     * Create pdf document with information about packages
     *
     * @return void
     */
    public function printPackageAction()
    {
        $shipment = $this->_initShipment();

        if ($shipment) {
            $pdf = Mage::getModel('sales/order_pdf_shipment_packaging')->getPdf($shipment);
            $this->_prepareDownloadResponse('packingslip'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf',
                $pdf->render(), 'application/pdf'
            );
        }
        else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Batch print shipping labels for whole shipments.
     * Push pdf document with shipping labels to user browser
     *
     * @return null
     */
    public function massPrintShippingLabelAction()
    {
        $request = $this->getRequest();
        $ids = $request->getParam('order_ids');
        $createdFromOrders = !empty($ids);
        $shipments = null;
        $labelsContent = array();
        switch ($request->getParam('massaction_prepare_key')) {
            case 'shipment_ids':
                $ids = $request->getParam('shipment_ids');
                array_filter($ids, 'intval');
                if (!empty($ids)) {
                    $shipments = Mage::getResourceModel('sales/order_shipment_collection')
                        ->addFieldToFilter('entity_id', array('in' => $ids));
                }
                break;
            case 'order_ids':
                $ids = $request->getParam('order_ids');
                array_filter($ids, 'intval');
                if (!empty($ids)) {
                    $shipments = Mage::getResourceModel('sales/order_shipment_collection')
                        ->setOrderFilter(array('in' => $ids));
                }
                break;
        }

        if ($shipments && $shipments->getSize()) {
            foreach ($shipments as $shipment) {
                $labelContent = $shipment->getShippingLabel();
                if ($labelContent) {
                    $labelsContent[] = $labelContent;
                }
            }
        }

        if (!empty($labelsContent)) {
            $outputPdf = $this->_combineLabelsPdf($labelsContent);
            $this->_prepareDownloadResponse('ShippingLabels.pdf', $outputPdf->render(), 'application/pdf');
            return;
        }

        if ($createdFromOrders) {
            $this->_getSession()
                ->addError(Mage::helper('sales')->__('There are no shipping labels related to selected orders.'));
            $this->_redirect('*/sales_order/index');
        } else {
            $this->_getSession()
                ->addError(Mage::helper('sales')->__('There are no shipping labels related to selected shipments.'));
            $this->_redirect('*/sales_order_shipment/index');
        }
    }

    /**
     * Combine array of labels as instance PDF
     *
     * @param array $labelsContent
     * @return Zend_Pdf
     */
    protected function _combineLabelsPdf(array $labelsContent)
    {
        $outputPdf = new Zend_Pdf();
        foreach ($labelsContent as $content) {
            if (stripos($content, '%PDF-') !== false) {
                $pdfLabel = Zend_Pdf::parse($content);
                foreach ($pdfLabel->pages as $page) {
                    $outputPdf->pages[] = clone $page;
                }
            } else {
                $page = $this->_createPdfPageFromImageString($content);
                if ($page) {
                    $outputPdf->pages[] = $page;
                }
            }
        }
        return $outputPdf;
    }

    /**
     * Create Zend_Pdf_Page instance with image from $imageString. Supports JPEG, PNG, GIF, WBMP, and GD2 formats.
     *
     * @param string $imageString
     * @return Zend_Pdf_Page|bool
     */
    protected function _createPdfPageFromImageString($imageString)
    {
        $image = imagecreatefromstring($imageString);
        if (!$image) {
            return false;
        }

        $xSize = imagesx($image);
        $ySize = imagesy($image);
        $page = new Zend_Pdf_Page($xSize, $ySize);

        imageinterlace($image, 0);
        $tmpFileName = sys_get_temp_dir() . DS . 'shipping_labels_'
                     . uniqid(mt_rand()) . time() . '.png';
        imagepng($image, $tmpFileName);
        $pdfImage = Zend_Pdf_Image::imageWithPath($tmpFileName);
        $page->drawImage($pdfImage, 0, 0, $xSize, $ySize);
        unlink($tmpFileName);
        return $page;
    }

    /**
     * Return grid with shipping items for Ajax request
     *
     * @return Mage_Core_Controller_Response_Http
     */
    public function getShippingItemsGridAction()
    {
        $this->_initShipment();
        return $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('adminhtml/sales_order_shipment_packaging_grid')
                ->setIndex($this->getRequest()->getParam('index'))
                ->toHtml()
           );
    }
}
