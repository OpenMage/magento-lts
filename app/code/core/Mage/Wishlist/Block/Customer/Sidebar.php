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
 * @category    Mage
 * @package     Mage_Wishlist
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Wishlist sidebar block
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Block_Customer_Sidebar extends Mage_Wishlist_Block_Abstract
{
    /**
     * Add sidebar conditions to collection
     *
     * @param Mage_Wishlist_Model_Mysql4_Product_Collection $collection
     * @return Mage_Wishlist_Block_Customer_Wishlist
     */
    protected function _prepareCollection($collection)
    {
        $collection->setPage(1, 3);
        $collection->addAttributeToSort('added_at', 'desc');

        return $this;
    }

    /**
     * Prepare before to html
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_getHelper()->hasItems()) {
            return parent::_toHtml();
        }

        return '';
    }

    /**
     * Can Display wishlist
     *
     * @return bool
     */
    public function getCanDisplayWishlist()
    {
        return $this->_getCustomerSession()->isLoggedIn();
    }

    /**
     * Retrieve URL for removing item from wishlist
     *
     * @deprecated back compatibility alias for getItemRemoveUrl
     * @param Mage_Wishlist_Model_Item $item
     * @return string
     */
    public function getRemoveItemUrl($item)
    {
        return $this->getItemRemoveUrl($item);
    }

    /**
     * Retrieve URL for adding product to shopping cart and remove item from wishlist
     *
     * @deprecated
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getAddToCartItemUrl($product)
    {
        return $this->getItemAddToCartUrl($product);
    }
}
