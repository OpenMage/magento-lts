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
 * @package     Mage_Reports
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Reports Event observer model
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Event_Observer
{
    protected $_enabledReports = true;

    /**
     * Object initialization
     */
    public function __construct()
    {
        $this->_enabledReports = Mage::helper('reports')->isReportsEnabled();
    }

    /**
     * Abstract Event obeserver logic
     *
     * Save event
     *
     * @param int $eventTypeId
     * @param int $objectId
     * @param int $subjectId
     * @param int $subtype
     * @return $this
     */
    protected function _event($eventTypeId, $objectId, $subjectId = null, $subtype = 0)
    {
        if (is_null($subjectId)) {
            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                $customer = Mage::getSingleton('customer/session')->getCustomer();
                $subjectId = $customer->getId();
            }
            else {
                $subjectId = Mage::getSingleton('log/visitor')->getId();
                $subtype = 1;
            }
        }

        $eventModel = Mage::getModel('reports/event');
        $storeId    = Mage::app()->getStore()->getId();
        $eventModel
            ->setEventTypeId($eventTypeId)
            ->setObjectId($objectId)
            ->setSubjectId($subjectId)
            ->setSubtype($subtype)
            ->setStoreId($storeId);
        $eventModel->save();

        return $this;
    }

    /**
     * Customer login action
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function customerLogin(Varien_Event_Observer $observer)
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn() || !$this->_enabledReports) {
            return $this;
        }

        $visitorId  = Mage::getSingleton('log/visitor')->getId();
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        $eventModel = Mage::getModel('reports/event');
        $eventModel->updateCustomerType($visitorId, $customerId);

        Mage::getModel('reports/product_index_compared')
            ->updateCustomerFromVisitor()
            ->calculate();
        Mage::getModel('reports/product_index_viewed')
            ->updateCustomerFromVisitor()
            ->calculate();

        return $this;
    }

    /**
     * Customer logout processing
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function customerLogout(Varien_Event_Observer $observer)
    {
        if ($this->_enabledReports) {
            Mage::getModel('reports/product_index_compared')
                ->purgeVisitorByCustomer()
                ->calculate();
            Mage::getModel('reports/product_index_viewed')
                ->purgeVisitorByCustomer()
                ->calculate();
        }

        return $this;
    }

    /**
     * View Catalog Product action
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function catalogProductView(Varien_Event_Observer $observer)
    {
        if (!$this->_enabledReports) {
            return $this;
        }

        $productId = $observer->getEvent()->getProduct()->getId();

        Mage::getModel('reports/product_index_viewed')
            ->setProductId($productId)
            ->save()
            ->calculate();

        return $this->_event(Mage_Reports_Model_Event::EVENT_PRODUCT_VIEW, $productId);
    }

    /**
     * Send Product link to friends action
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function sendfriendProduct(Varien_Event_Observer $observer)
    {
        if (!$this->_enabledReports) {
            return $this;
        }

        return $this->_event(Mage_Reports_Model_Event::EVENT_PRODUCT_SEND,
            $observer->getEvent()->getProduct()->getId()
        );
    }

    /**
     * Remove Product from Compare Products action
     *
     * Reset count of compared products cache
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function catalogProductCompareRemoveProduct(Varien_Event_Observer $observer)
    {
        if ($this->_enabledReports) {
            Mage::getModel('reports/product_index_compared')->calculate();
        }

        return $this;
    }

    /**
     * Remove All Products from Compare Products
     *
     * Reset count of compared products cache
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function catalogProductCompareClear(Varien_Event_Observer $observer)
    {
        if ($this->_enabledReports) {
            Mage::getModel('reports/product_index_compared')->calculate();
        }

        return $this;
    }

    /**
     * Add Product to Compare Products List action
     *
     * Reset count of compared products cache
     *
     * @param Varien_Event_Observer $observer
     * @return unknown
     */
    public function catalogProductCompareAddProduct(Varien_Event_Observer $observer)
    {
        if (!$this->_enabledReports) {
            return $this;
        }

        $productId = $observer->getEvent()->getProduct()->getId();

        Mage::getModel('reports/product_index_compared')
            ->setProductId($productId)
            ->save()
            ->calculate();

        return $this->_event(Mage_Reports_Model_Event::EVENT_PRODUCT_COMPARE, $productId);
    }

    /**
     * Add product to shopping cart action
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function checkoutCartAddProduct(Varien_Event_Observer $observer)
    {
        if ($this->_enabledReports) {
            $quoteItem = $observer->getEvent()->getItem();
            if (!$quoteItem->getId() && !$quoteItem->getParentItem()) {
                $productId = $quoteItem->getProductId();
                $this->_event(Mage_Reports_Model_Event::EVENT_PRODUCT_TO_CART, $productId);
            }
        }

        return $this;
    }

    /**
     * Add product to wishlist action
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function wishlistAddProduct(Varien_Event_Observer $observer)
    {
        if (!$this->_enabledReports) {
            return $this;
        }

        return $this->_event(Mage_Reports_Model_Event::EVENT_PRODUCT_TO_WISHLIST,
            $observer->getEvent()->getProduct()->getId()
        );
    }

    /**
     * Share customer wishlist action
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function wishlistShare(Varien_Event_Observer $observer)
    {
        if (!$this->_enabledReports) {
            return $this;
        }

        return $this->_event(Mage_Reports_Model_Event::EVENT_WISHLIST_SHARE,
            $observer->getEvent()->getWishlist()->getId()
        );
    }

    /**
     * Clean events by old visitors
     *
     * @see Global Log Clean Settings
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function eventClean(Varien_Event_Observer $observer)
    {
        /* @var $event Mage_Reports_Model_Event */
        $event = Mage::getModel('reports/event');
        $event->clean();

        Mage::getModel('reports/product_index_compared')->clean();
        Mage::getModel('reports/product_index_viewed')->clean();

        return $this;
    }
}
