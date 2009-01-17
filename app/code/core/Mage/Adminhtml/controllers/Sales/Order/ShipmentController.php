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
 * Adminhtml sales order shipment controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Sales_Order_ShipmentController extends Mage_Adminhtml_Controller_Sales_Shipment
{
    protected function _getItemQtys()
    {
        $data = $this->getRequest()->getParam('shipment');
        if (isset($data['items'])) {
            $qtys = $data['items'];
        }
        else {
            $qtys = array();
        }
        return $qtys;
    }

    /**
     * Initialize shipment model instance
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    protected function _initShipment()
    {
        $shipment = false;
        if ($shipmentId = $this->getRequest()->getParam('shipment_id')) {
            $shipment = Mage::getModel('sales/order_shipment')->load($shipmentId);
        }
        elseif ($orderId = $this->getRequest()->getParam('order_id')) {
            $order      = Mage::getModel('sales/order')->load($orderId);

            /**
             * Check order existing
             */
            if (!$order->getId()) {
                $this->_getSession()->addError($this->__('Order not longer exist.'));
                return false;
            }
            /**
             * Check shipment create availability
             */
            if (!$order->canShip()) {
                $this->_getSession()->addError($this->__('Can not do shipment for order.'));
                return false;
            }

            $convertor  = Mage::getModel('sales/convert_order');
            $shipment    = $convertor->toShipment($order);
            $savedQtys = $this->_getItemQtys();
            foreach ($order->getAllItems() as $orderItem) {
                if (!$orderItem->isDummy(true) && !$orderItem->getQtyToShip()) {
                    continue;
                }
                if ($orderItem->isDummy(true) && !$this->_needToAddDummy($orderItem, $savedQtys)) {
                    continue;
                }
                if ($orderItem->getIsVirtual()) {
                    continue;
                }
                $item = $convertor->itemToShipmentItem($orderItem);
                if (isset($savedQtys[$orderItem->getId()])) {
                    if ($savedQtys[$orderItem->getId()] > 0) {
                        $qty = $savedQtys[$orderItem->getId()];
                    } else {
                        continue;
                    }
                }
                else {
                    if ($orderItem->isDummy(true)) {
                        $qty = 1;
                    } else {
                        $qty = $orderItem->getQtyToShip();
                    }
                }
                $item->setQty($qty);
                $shipment->addItem($item);
            }
            if ($tracks = $this->getRequest()->getPost('tracking')) {
                foreach ($tracks as $data) {
                    $track = Mage::getModel('sales/order_shipment_track')
                    ->addData($data);
                    $shipment->addTrack($track);
                }
            }
        }

        Mage::register('current_shipment', $shipment);
        return $shipment;
    }

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
     * shipment information page
     */
    public function viewAction()
    {
        if ($shipment = $this->_initShipment()) {
            $this->loadLayout();
            $this->getLayout()->getBlock('sales_shipment_view')
                ->updateBackButtonUrl($this->getRequest()->getParam('come_from'));
            $this->_setActiveMenu('sales/order')
                ->renderLayout();
        }
        else {
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
            $this->loadLayout()
                ->_setActiveMenu('sales/order')
                ->renderLayout();
        }
        else {
            $this->_redirect('*/sales_order/view', array('order_id'=>$this->getRequest()->getParam('order_id')));
        }
    }

    /**
     * Save shipment
     * We can save only new shipment. Existing shipments are not editable
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost('shipment');

        try {
            if ($shipment = $this->_initShipment()) {
                $shipment->register();

                $comment = '';
                if (!empty($data['comment_text'])) {
                    $shipment->addComment($data['comment_text'], isset($data['comment_customer_notify']));
                    $comment = $data['comment_text'];
                }

                if (!empty($data['send_email'])) {
                    $shipment->setEmailSent(true);
                }

                $this->_saveShipment($shipment);
                $shipment->sendEmail(!empty($data['send_email']), $comment);
                $this->_getSession()->addSuccess($this->__('Shipment was successfully created.'));
                $this->_redirect('*/sales_order/view', array('order_id' => $shipment->getOrderId()));
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
            $this->_getSession()->addError($this->__('Can not save shipment.'));
        }
        $this->_redirect('*/*/new', array('order_id' => $this->getRequest()->getParam('order_id')));
    }

    public function emailAction()
    {
        try {
            if ($shipment = $this->_initShipment()) {
                $shipment->sendEmail(true);
                $this->_getSession()->addSuccess($this->__('Shipment was successfully sent.'));
            }
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addError($this->__('Can not send shipment information.'));
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
                Mage::throwException($this->__('You need specify carrier.'));
            }
            if (empty($number)) {
                Mage::throwException($this->__('Tracking number can not be empty.'));
            }
            if ($shipment = $this->_initShipment()) {
                $track = Mage::getModel('sales/order_shipment_track')
                    ->setNumber($number)
                    ->setCarrierCode($carrier)
                    ->setTitle($title);
                $shipment->addTrack($track)
                    ->save();

                $this->loadLayout();
                $response = $this->getLayout()->getBlock('shipment_tracking')->toHtml();
            }
            else {
                $response = array(
                    'error'     => true,
                    'message'   => $this->__('Can not initialize shipment for adding tracking number.'),
                );
            }
        }
        catch (Mage_Core_Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage(),
            );
        }
        catch (Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $this->__('Can not add tracking number.'),
            );
        }
        if (is_array($response)) {
            $response = Zend_Json::encode($response);
        }
        $this->getResponse()->setBody($response);
    }

    public function removeTrackAction()
    {
        $trackId    = $this->getRequest()->getParam('track_id');
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        $track = Mage::getModel('sales/order_shipment_track')->load($trackId);
        if ($track->getId()) {
            try {
                if ($shipmentId = $this->_initShipment()) {
                    $track->delete();

                    $this->loadLayout();
                    $response = $this->getLayout()->getBlock('shipment_tracking')->toHtml();
                }
                else {
                    $response = array(
                        'error'     => true,
                        'message'   => $this->__('Can not initialize shipment for delete tracking number.'),
                    );
                }
            }
            catch (Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $this->__('Can not delete tracking number.'),
                );
            }
        }
        else {
            $response = array(
                'error'     => true,
                'message'   => $this->__('Can not load track with retrieving identifier.'),
            );
        }
        if (is_array($response)) {
            $response = Zend_Json::encode($response);
        }
        $this->getResponse()->setBody($response);
    }

    public function viewTrackAction()
    {
        $trackId    = $this->getRequest()->getParam('track_id');
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        $track = Mage::getModel('sales/order_shipment_track')->load($trackId);
        if ($track->getId()) {
            try {
                $response = $track->getNumberDetail();
            }
            catch (Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $this->__('Can not retrieve tracking number detail.'),
                );
            }
        }
        else {
            $response = array(
                'error'     => true,
                'message'   => $this->__('Can not load track with retrieving identifier.'),
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
        }
        else {
            if (is_array($response)) {
                $response = Zend_Json::encode($response);
            }

            $this->getResponse()->setBody($response);
        }
    }

    public function addCommentAction()
    {
        try {
            $this->getRequest()->setParam(
                'shipment_id',
                $this->getRequest()->getParam('id')
            );
            $data = $this->getRequest()->getPost('comment');
            if (empty($data['comment'])) {
                Mage::throwException($this->__('Comment text field can not be empty.'));
            }
            $shipment = $this->_initShipment();
            $shipment->addComment($data['comment'], isset($data['is_customer_notified']));
            $shipment->sendUpdateEmail(!empty($data['is_customer_notified']), $data['comment']);
            $shipment->save();

            $this->loadLayout();
            $response = $this->getLayout()->getBlock('shipment_comments')->toHtml();
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
     * Decides if we need to create dummy shipment item or not
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
                if ($child->getIsVirtual()) {
                    continue;
                }
                if ((isset($qtys[$child->getId()]) && $qtys[$child->getId()] > 0) || (!isset($qtys[$child->getId()]) && $child->getQtyToShip())) {
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
}
