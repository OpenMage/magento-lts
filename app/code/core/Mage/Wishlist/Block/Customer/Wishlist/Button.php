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
 * Wishlist block customer item cart column
 *
 * @category   Mage
 * @package    Mage_Wishlist
 */
class Mage_Wishlist_Block_Customer_Wishlist_Button extends Mage_Core_Block_Template
{
    /**
     * Retrieve current wishlist
     *
     * @return Mage_Wishlist_Model_Wishlist
     */
    public function getWishlist()
    {
        return Mage::helper('wishlist')->getWishlist();
    }
}
