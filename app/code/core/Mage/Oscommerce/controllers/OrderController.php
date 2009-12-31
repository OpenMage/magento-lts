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
 * @package     Mage_Oscommerce
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * osCommerce orders controller
 * 
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Oscommerce_OrderController extends Mage_Core_Controller_Front_Action
{

    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
        $loginUrl = Mage::helper('customer')->getLoginUrl();
        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }


    /**
     * Check osCommerce order view availability
     *
     * @param   array $order
     * @return  bool
     */
    protected function _canViewOrder($order)
    {
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        if (isset($order['osc_magento_id']) && isset($order['magento_customers_id']) && $order['magento_customers_id'] == $customerId) {
            return true;
        }
        return false;
    }
    
    /**
     * 
     */
    public function indexAction()
    {
        $this->_redirect('sales/order/history');
    }
        
    /**
     * osCommerce Order view page
     */
    public function viewAction()
    {
        $orderId = (int) $this->getRequest()->getParam('order_id');
        if (!$orderId) {
            $this->_redirect('sales/order/history');
            return;
        }

        $order = Mage::getModel('oscommerce/oscommerce')->loadOrderById($orderId);
        if ($order && $this->_canViewOrder($order['order'])) {
            Mage::register('current_oscommerce_order', $order);
           $this->loadLayout();
            if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
                $navigationBlock->setActive('oscommerce/order/view');
            }
            
            $this->renderLayout();
        }
        else {
            $this->_redirect('sales/order/history');
        }
    }
}
