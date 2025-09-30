<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

require_once 'CreateController.php';

/**
 * Adminhtml sales order edit controller
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Sales_Order_EditController extends Mage_Adminhtml_Sales_Order_CreateController
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'sales/order/actions/edit';

    /**
     * Start edit order initialization
     */
    public function startAction()
    {
        $this->_getSession()->clear();
        $orderId = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($orderId);

        if (!$order->getId()) {
            $this->_redirect('*/sales_order/');
            return;
        }

        try {
            if (!$order->canEdit()) {
                Mage::throwException(Mage::helper('sales')->__('This order cannot be edited.'));
            }

            $this->_getSession()->setUseOldShippingMethod(true);
            $this->_getOrderCreateModel()->initFromOrder($order);
            $this->_redirect('*/*');
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/sales_order/view', ['order_id' => $orderId]);
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addException($e, $e->getMessage());
            $this->_redirect('*/sales_order/view', ['order_id' => $orderId]);
        }
    }

    /**
     * Index page
     */
    public function indexAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Orders'))->_title($this->__('Edit Order'));
        $this->loadLayout();

        $this->_initSession()
            ->_setActiveMenu('sales/order')
            ->renderLayout();
    }
}
