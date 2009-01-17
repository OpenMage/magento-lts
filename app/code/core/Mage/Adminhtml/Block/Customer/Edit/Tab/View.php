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
 * Customer account form block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_View extends Mage_Adminhtml_Block_Template
{

    protected $_customer;

    protected $_customerLog;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('customer/tab/view.phtml');
    }

    protected function _prepareLayout()
    {
        $customer = Mage::registry('current_customer');

        $this->setChild('sales', $this->getLayout()->createBlock('adminhtml/customer_edit_tab_view_sales'));

        $accordion = $this->getLayout()->createBlock('adminhtml/widget_accordion')
            ->setId('customerViewAccordion');
            //->setShowOnlyOne(0)

        /* @var $accordion Mage_Adminhtml_Block_Widget_Accordion */
        $accordion->addItem('lastOrders', array(
            'title'       => Mage::helper('customer')->__('Recent Orders'),
            'ajax'        => true,
            'content_url' => $this->getUrl('*/*/lastOrders', array('_current' => true)),
        ));

        // add shopping cart block of each website
        foreach (Mage::registry('current_customer')->getSharedWebsiteIds() as $websiteId) {
            $website = Mage::app()->getWebsite($websiteId);

            // count cart items
            $cartItemsCount = Mage::getModel('sales/quote')
                ->setWebsite($website)->loadByCustomer($customer)
                ->getItemsCollection(false)->getSize();
            // prepare title for cart
            $title = Mage::helper('customer')->__('Shopping Cart - %d item(s)', $cartItemsCount);
            if (count($customer->getSharedWebsiteIds()) > 1) {
                $title = Mage::helper('customer')->__('Shopping Cart of %1$s - %2$d item(s)',
                    $website->getName(), $cartItemsCount
                );
            }

            // add cart ajax accordion
            $accordion->addItem('shopingCart' . $websiteId, array(
                'title'   => $title,
                'ajax'    => true,
                'content_url' => $this->getUrl('*/*/viewCart', array('_current' => true, 'website_id' => $websiteId)),
            ));
        }

        // count wishlist items
        $wishlistCount = Mage::getModel('wishlist/wishlist')->loadByCustomer($customer)
            ->getProductCollection()
            ->addStoreData()
            ->getSize();
        // add wishlist ajax accordion
        $accordion->addItem('wishlist', array(
            'title' => Mage::helper('customer')->__('Wishlist - %d item(s)', $wishlistCount),
            'ajax'  => true,
            'content_url' => $this->getUrl('*/*/viewWishlist', array('_current' => true)),
        ));

        $this->setChild('accordion', $accordion);
        return parent::_prepareLayout();
    }

    public function getCustomer()
    {
        if (!$this->_customer) {
            $this->_customer = Mage::registry('current_customer');
        }
        return $this->_customer;
    }

    public function getGroupName()
    {
        if ($groupId = $this->getCustomer()->getGroupId()) {
            return Mage::getModel('customer/group')
                ->load($groupId)
                ->getCustomerGroupCode();
        }
    }

    public function getCustomerLog()
    {
        if (!$this->_customerLog) {
            $this->_customerLog = Mage::getModel('log/customer')
                ->load($this->getCustomer()->getId());

        }
        return $this->_customerLog;
    }

    public function getCreateDate()
    {
        return $this->formatDate($this->getCustomer()->getCreatedAt(), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true);
    }

    public function getLastLoginDate()
    {
        if ($date = $this->getCustomerLog()->getLoginAt()) {
            return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true);
        }
        return Mage::helper('customer')->__('Never');
    }

    public function getCurrentStatus()
    {
        $log = $this->getCustomerLog();
        if ($log->getLogoutAt() ||
            strtotime(now())-strtotime($log->getLastVisitAt())>Mage_Log_Model_Visitor::getOnlineMinutesInterval()*60) {
            return Mage::helper('customer')->__('Offline');
        }
        return Mage::helper('customer')->__('Online');
    }

    public function getIsConfirmedStatus()
    {
        $this->getCustomer();
        if (!$this->_customer->getConfirmation()) {
            return Mage::helper('customer')->__('Confirmed');
        }
        if ($this->_customer->isConfirmationRequired()) {
            return Mage::helper('customer')->__('Not confirmed, cannot login');
        }
        return Mage::helper('customer')->__('Not confirmed, can login');
    }

    public function getCreatedInStore()
    {
        return Mage::app()->getStore($this->getCustomer()->getStoreId())->getName();
    }

    public function getBillingAddressHtml()
    {
        $html = '';
        if ($address = $this->getCustomer()->getPrimaryBillingAddress()) {
            $html = $address->format('html');
        }
        else {
            $html = Mage::helper('customer')->__("Customer doesn't have primary billing address");
        }
        return $html;
    }

    public function getAccordionHtml()
    {
        return $this->getChildHtml('accordion');
    }

    public function getSalesHtml()
    {
        return $this->getChildHtml('sales');
    }

}
