<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Rss
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer reviews controller
 *
 * @category   Mage
 * @package    Mage_Rss
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

    /**
     * @return $this|void
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
