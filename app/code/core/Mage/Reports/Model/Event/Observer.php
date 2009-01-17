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
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Report event observer model
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Reports_Model_Event_Observer
{
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

    public function customerLogin(Varien_Event_Observer $observer) {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return $this;
        }
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $visitorId = Mage::getSingleton('log/visitor')->getId();
        $customerId = $customer->getId();
        $eventModel = Mage::getModel('reports/event');
        $eventModel->updateCustomerType($visitorId, $customerId);
    }

    public function catalogProductView(Varien_Event_Observer $observer)
    {
        Mage::getSingleton('reports/session')->setData('viewed_products', true);
        return $this->_event(
            Mage_Reports_Model_Event::EVENT_PRODUCT_VIEW,
            $observer->getEvent()->getProduct()->getId()
        );
    }

    public function sendfriendProduct(Varien_Event_Observer $observer)
    {
        return $this->_event(
            Mage_Reports_Model_Event::EVENT_PRODUCT_SEND,
            $observer->getEvent()->getProduct()->getId()
        );
    }

    public function catalogProductCompareRemoveProduct(Varien_Event_Observer $observer)
    {
        Mage::getSingleton('reports/session')->setData('compared_products', null);
    }

    public function catalogProductCompareAddProduct(Varien_Event_Observer $observer)
    {
        Mage::getSingleton('reports/session')->setData('compared_products', true);
        return $this->_event(
            Mage_Reports_Model_Event::EVENT_PRODUCT_COMPARE,
            $observer->getEvent()->getProduct()->getId()
        );
    }

    public function checkoutCartAddProduct(Varien_Event_Observer $observer)
    {
        $quoteItem = $observer->getEvent()->getItem();
        if (!$quoteItem->getId() && !$quoteItem->getParentItem()) {
            $productId = $quoteItem->getProductId();
            $this->_event(Mage_Reports_Model_Event::EVENT_PRODUCT_TO_CART, $productId);
        }
        return $this;
    }

    public function wishlistAddProduct(Varien_Event_Observer $observer)
    {
        return $this->_event(
            Mage_Reports_Model_Event::EVENT_PRODUCT_TO_WISHLIST,
            $observer->getEvent()->getProduct()->getId()
        );
    }

    public function wishlistShare(Varien_Event_Observer $observer)
    {
        return $this->_event(
            Mage_Reports_Model_Event::EVENT_WISHLIST_SHARE,
            $observer->getEvent()->getWishlist()->getId()
        );
    }
}