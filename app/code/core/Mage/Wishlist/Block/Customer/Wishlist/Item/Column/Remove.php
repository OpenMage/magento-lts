<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Wishlist
 */

/**
 * Delete item column in customer wishlist table
 *
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
