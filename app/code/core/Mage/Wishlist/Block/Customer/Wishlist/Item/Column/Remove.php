<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
                $this->__('Are you sure you want to remove this product from your wishlist?')
            )
            . "');
        }
        ";
    }
}
