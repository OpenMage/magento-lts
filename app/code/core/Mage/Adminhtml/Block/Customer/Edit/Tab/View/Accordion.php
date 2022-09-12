<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml customer recent orders grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_View_Accordion extends Mage_Adminhtml_Block_Widget_Accordion
{
    protected function _prepareLayout()
    {
        $customer = Mage::registry('current_customer');

        $this->setId('customerViewAccordion');

        $this->addItem('lastOrders', [
            'title'       => Mage::helper('customer')->__('Recent Orders'),
            'ajax'        => true,
            'content_url' => $this->getUrl('*/*/lastOrders', ['_current' => true]),
        ]);

        // add shopping cart block of each website
        foreach (Mage::registry('current_customer')->getSharedWebsiteIds() as $websiteId) {
            $website = Mage::app()->getWebsite($websiteId);

            // count cart items
            $cartItemsCount = Mage::getModel('sales/quote')
                ->setWebsite($website)->loadByCustomer($customer)
                ->getItemsCollection(false)
                ->addFieldToFilter('parent_item_id', ['null' => true])
                ->getSize();
            // prepare title for cart
            $title = Mage::helper('customer')->__('Shopping Cart - %d item(s)', $cartItemsCount);
            if (count($customer->getSharedWebsiteIds()) > 1) {
                $title = Mage::helper('customer')->__('Shopping Cart of %1$s - %2$d item(s)', $website->getName(), $cartItemsCount);
            }

            // add cart ajax accordion
            $this->addItem('shopingCart' . $websiteId, [
                'title'   => $title,
                'ajax'    => true,
                'content_url' => $this->getUrl('*/*/viewCart', ['_current' => true, 'website_id' => $websiteId]),
            ]);
        }

        if (Mage::helper('wishlist')->isAllow()) {
            // count wishlist items
            $wishlistCount = Mage::getModel('wishlist/item')->getCollection()
                ->addCustomerIdFilter($customer->getId())
                ->addStoreData()
                ->getSize();
            // add wishlist ajax accordion
            $this->addItem('wishlist', [
                'title' => Mage::helper('customer')->__('Wishlist - %d item(s)', $wishlistCount),
                'ajax'  => true,
                'content_url' => $this->getUrl('*/*/viewWishlist', ['_current' => true]),
            ]);
        }
        return $this;
    }
}
