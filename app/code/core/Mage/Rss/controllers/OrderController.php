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
 * @package     Mage_Rss
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer reviews controller
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Rss_OrderController extends Mage_Rss_Controller_Abstract
{
    public function newAction()
    {
        if ($this->checkFeedEnable('order/new')) {
            $this->loadLayout(false);
            $this->renderLayout();
        }
    }

    public function customerAction()
    {
        if ($this->checkFeedEnable('order/customer')) {
            if (Mage::app()->getStore()->isCurrentlySecure()) {
                Mage::helper('rss')->authFrontend();
            } else {
                $this->_redirect('rss/order/customer', array('_secure'=>true));
                return $this;
            }
        }
    }

    /**
     * Order status action
     */
    public function statusAction()
    {
        if ($this->isFeedEnable('order/status_notified')) {
            $order = Mage::helper('rss/order')->getOrderByStatusUrlKey((string)$this->getRequest()->getParam('data'));
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
     * Controller predispatch method to change area for some specific action.
     *
     * @return Mage_Rss_OrderController
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
