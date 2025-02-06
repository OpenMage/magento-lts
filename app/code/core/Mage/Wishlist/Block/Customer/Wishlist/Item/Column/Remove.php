<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Wishlist
 */

/**
 * Delete item column in customer wishlist table
 *
 * @category   Mage
 * @package    Mage_Wishlist
 */
class Mage_Wishlist_Block_Customer_Wishlist_Item_Column_Remove extends Mage_Wishlist_Block_Customer_Wishlist_Item_Column
{
    /**
     * Retrieve block javascript
     *
     * @return string
     */
    public function getJs()
    {
        return parent::getJs() . "
        function confirmRemoveWishlistItem() {
            return confirm('"
            . Mage::helper('core')->jsQuoteEscape(
                $this->__('Are you sure you want to remove this product from your wishlist?'),
            )
            . "');
        }
        ";
    }
}
