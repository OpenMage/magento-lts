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
 * @package    Mage_Wishlist
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Wishlist Item Configure block
 * Serves for configuring item on product view page
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @module     Wishlist
 */
class Mage_Wishlist_Block_Item_Configure extends Mage_Core_Block_Template
{
    /**
     * Returns product being edited
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function getProduct()
    {
        return Mage::registry('product');
    }

    /**
     * Returns wishlist item being configured
     *
     * @return Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item
     */
    protected function getWishlistItem()
    {
        return Mage::registry('wishlist_item');
    }

    /**
     * Configure product view blocks
     *
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        // Set custom add to cart url
        $block = $this->getLayout()->getBlock('product.info');
        if ($block) {
            $url = Mage::helper('wishlist')->getAddToCartUrl($this->getWishlistItem());
            $postUrl = Mage::helper('wishlist')->getAddToCartUrlCustom($this->getWishlistItem(), false);
            $block->setCustomAddToCartUrl($url);
            $block->setCustomAddToCartPostUrl($postUrl);
        }

        return parent::_prepareLayout();
    }
}
