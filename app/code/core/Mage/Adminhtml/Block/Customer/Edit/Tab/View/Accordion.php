<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml customer recent orders grid block
 *
 * @package    Mage_Adminhtml
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
