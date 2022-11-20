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
 * @package    Mage_Rss
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Poll index controller
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rss_IndexController extends Mage_Rss_Controller_Abstract
{
    /**
     * Current wishlist
     *
     * @var Mage_Wishlist_Model_Wishlist
     */
    protected $_wishlist;

    /**
     * Current customer
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * Index action
     */
    public function indexAction()
    {
        /** @var Mage_Rss_Helper_Data $helper */
        $helper = $this->_getHelper('rss');
        if ($helper->isRssEnabled()) {
            $this->loadLayout();
            $this->renderLayout();
        } else {
            $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
            $this->getResponse()->setHeader('Status', '404 File not found');
            $this->_forward('defaultNoRoute');
        }
    }

    /**
     * Display feed not found message
     */
    public function nofeedAction()
    {
        $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
        $this->getResponse()->setHeader('Status', '404 File not found');
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Wishlist rss feed action
     * Show all public wishlists and private wishlists that belong to current user
     *
     * @return void
     */
    public function wishlistAction()
    {
        if (!$this->isFeedEnable('wishlist/active')) {
            $this->_forward('nofeed', 'index', 'rss');
            return;
        }

        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            $this->_forward('nofeed', 'index', 'rss');
            return;
        }

        if ($wishlist->getVisibility()) {
            $this->_showWishlistRss();
            return ;
        }

        if (Mage::getSingleton('customer/session')->authenticate($this)
            && $wishlist->getCustomerId() == $this->_getCustomer()->getId()) {
            $this->_showWishlistRss();
        } else {
            $this->_forward('nofeed', 'index', 'rss');
        }
    }

    /**
     * Show wishlist rss
     */
    protected function _showWishlistRss()
    {
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Retrieve Wishlist model
     *
     * @return Mage_Wishlist_Model_Wishlist
     */
    protected function _getWishlist()
    {
        if (is_null($this->_wishlist)) {
            $this->_wishlist = Mage::getModel('wishlist/wishlist');
            $wishlistId = $this->getRequest()->getParam('wishlist_id');
            if ($wishlistId) {
                $this->_wishlist->load($wishlistId);
            } else {
                if ($this->_getCustomer()->getId()) {
                    $this->_wishlist->loadByCustomer($this->_getCustomer());
                }
            }
        }
        return $this->_wishlist;
    }

    /**
     * Retrieve Customer instance
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
        if (is_null($this->_customer)) {
            $this->_customer = Mage::getModel('customer/customer');

            $params = $this->_getHelper('core')->urlDecode($this->getRequest()->getParam('data'));
            $data   = explode(',', $params);
            $customerId    = abs(intval($data[0]));
            if ($customerId && ($customerId == Mage::getSingleton('customer/session')->getCustomerId())) {
                $this->_customer->load($customerId);
            }
        }

        return $this->_customer;
    }
}
