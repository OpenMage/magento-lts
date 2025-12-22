<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Rss
 */

/**
 * Customer reviews controller
 *
 * @package    Mage_Rss
 */
class Mage_Rss_OrderController extends Mage_Rss_Controller_Abstract
{
    /**
     * @return void
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function newAction()
    {
        if ($this->checkFeedEnable('order/new')) {
            $this->loadLayout(false);
            $this->renderLayout();
        }
    }

    /**
     * @return $this|void
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function customerAction()
    {
        if ($this->checkFeedEnable('order/customer')) {
            if (Mage::app()->getStore()->isCurrentlySecure()) {
                Mage::helper('rss')->authFrontend();
            } else {
                $this->_redirect('rss/order/customer', ['_secure' => true]);
                return $this;
            }
        }
    }

    /**
     * Order status action
     *
     * @return void
     * @throws Mage_Core_Exception
     */
    public function statusAction()
    {
        if ($this->isFeedEnable('order/status_notified')) {
            $order = Mage::helper('rss/order')->getOrderByStatusUrlKey((string) $this->getRequest()->getParam('data'));
            if (!is_null($order)) {
                Mage::register('current_order', $order);
                $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
                $this->loadLayout(false);
                $this->renderLayout();
                return;
            }
        }

        $this->_forward('nofeed', 'index', 'rss');
    }

    /**
     * Controller pre-dispatch method to change area for some specific action.
     *
     * @return $this
     */
    public function preDispatch()
    {
        $action = strtolower($this->getRequest()->getActionName());
        if ($action == 'new' && $this->isFeedEnable('order/new')) {
            $this->_currentArea = 'adminhtml';
            Mage::helper('rss')->authAdmin('sales/order');
        }

        return parent::preDispatch();
    }
}
