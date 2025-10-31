<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Sales Controller
 *
 * @package    Mage_Sales
 */
abstract class Mage_Sales_Controller_Abstract extends Mage_Core_Controller_Front_Action
{
    /**
     * Check order view availability
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  bool
     * @throws Mage_Core_Exception
     */
    protected function _canViewOrder($order)
    {
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        $availableStates = Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates();
        if ($order->getId() && $order->getCustomerId() && ($order->getCustomerId() == $customerId)
            && in_array($order->getState(), $availableStates, true)
        ) {
            return true;
        }

        return false;
    }

    /**
     * Init layout, messages and set active block for customer
     *
     * @throws Mage_Core_Exception
     */
    protected function _viewAction()
    {
        if (!$this->_loadValidOrder()) {
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');

        $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('sales/order/history');
        }

        $this->renderLayout();
    }

    /**
     * Try to load valid order by order_id and register it
     *
     * @param int $orderId
     * @return bool
     * @throws Mage_Core_Exception
     */
    protected function _loadValidOrder($orderId = null)
    {
        if ($orderId === null) {
            $orderId = (int) $this->getRequest()->getParam('order_id');
        }

        if (!$orderId) {
            $this->_forward('noRoute');
            return false;
        }

        $order = Mage::getModel('sales/order')->load($orderId);

        if ($this->_canViewOrder($order)) {
            Mage::register('current_order', $order);
            return true;
        } else {
            $this->_redirect('*/*/history');
        }

        return false;
    }

    /**
     * Order view page
     *
     * @throws Mage_Core_Exception
     */
    public function viewAction()
    {
        $this->_viewAction();
    }

    /**
     * Invoice page
     *
     * @throws Mage_Core_Exception
     */
    public function invoiceAction()
    {
        $this->_viewAction();
    }

    /**
     * Shipment page
     *
     * @throws Mage_Core_Exception
     */
    public function shipmentAction()
    {
        $this->_viewAction();
    }

    /**
     * Creditmemo page
     *
     * @throws Mage_Core_Exception
     */
    public function creditmemoAction()
    {
        $this->_viewAction();
    }

    /**
     * Action for reorder
     *
     * @throws Mage_Core_Exception
     */
    public function reorderAction()
    {
        if (!$this->_loadValidOrder()) {
            return;
        }

        $order = Mage::registry('current_order');
        /** @var Mage_Checkout_Model_Cart $cart */
        $cart = Mage::getSingleton('checkout/cart');

        $items = $order->getItemsCollection();
        foreach ($items as $item) {
            try {
                $cart->addOrderItem($item);
            } catch (Mage_Core_Exception $mageCoreException) {
                if (Mage::getSingleton('checkout/session')->getUseNotice(true)) {
                    Mage::getSingleton('checkout/session')->addNotice($mageCoreException->getMessage());
                } else {
                    Mage::getSingleton('checkout/session')->addError($mageCoreException->getMessage());
                }

                $this->_redirect('*/*/history');
            } catch (Exception $exception) { // @phpstan-ignore catch.neverThrown
                Mage::getSingleton('checkout/session')->addException(
                    $exception,
                    Mage::helper('checkout')->__('Cannot add the item to shopping cart.'),
                );
                $this->_redirect('checkout/cart');
            }
        }

        $cart->save();
        $this->_redirect('checkout/cart');
    }

    /**
     * Print Order Action
     *
     * @throws Mage_Core_Exception
     */
    public function printAction()
    {
        if (!$this->_loadValidOrder()) {
            return;
        }

        $this->loadLayout('print');
        $this->renderLayout();
    }

    /**
     * Print Invoice Action
     *
     * @throws Mage_Core_Exception
     */
    public function printInvoiceAction()
    {
        $invoiceId = (int) $this->getRequest()->getParam('invoice_id');
        if ($invoiceId) {
            $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
            $order = $invoice->getOrder();
        } else {
            $orderId = (int) $this->getRequest()->getParam('order_id');
            $order = Mage::getModel('sales/order')->load($orderId);
        }

        if ($this->_canViewOrder($order)) {
            Mage::register('current_order', $order);
            if (isset($invoice)) {
                Mage::register('current_invoice', $invoice);
            }

            $this->loadLayout('print');
            $this->renderLayout();
        } elseif (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect('*/*/history');
        } else {
            $this->_redirect('sales/guest/form');
        }
    }

    /**
     * Print Shipment Action
     *
     * @throws Mage_Core_Exception
     */
    public function printShipmentAction()
    {
        $shipmentId = (int) $this->getRequest()->getParam('shipment_id');
        if ($shipmentId) {
            $shipment = Mage::getModel('sales/order_shipment')->load($shipmentId);
            $order = $shipment->getOrder();
        } else {
            $orderId = (int) $this->getRequest()->getParam('order_id');
            $order = Mage::getModel('sales/order')->load($orderId);
        }

        if ($this->_canViewOrder($order)) {
            Mage::register('current_order', $order);
            if (isset($shipment)) {
                Mage::register('current_shipment', $shipment);
            }

            $this->loadLayout('print');
            $this->renderLayout();
        } elseif (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect('*/*/history');
        } else {
            $this->_redirect('sales/guest/form');
        }
    }

    /**
     * Print Creditmemo Action
     *
     * @throws Mage_Core_Exception
     */
    public function printCreditmemoAction()
    {
        $creditmemoId = (int) $this->getRequest()->getParam('creditmemo_id');
        if ($creditmemoId) {
            $creditmemo = Mage::getModel('sales/order_creditmemo')->load($creditmemoId);
            $order = $creditmemo->getOrder();
        } else {
            $orderId = (int) $this->getRequest()->getParam('order_id');
            $order = Mage::getModel('sales/order')->load($orderId);
        }

        if ($this->_canViewOrder($order)) {
            Mage::register('current_order', $order);
            if (isset($creditmemo)) {
                Mage::register('current_creditmemo', $creditmemo);
            }

            $this->loadLayout('print');
            $this->renderLayout();
        } elseif (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect('*/*/history');
        } else {
            $this->_redirect('sales/guest/form');
        }
    }
}
