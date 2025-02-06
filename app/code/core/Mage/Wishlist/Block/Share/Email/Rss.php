<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Wishlist
 */

/**
 * Wishlist RSS URL to Email Block
 *
 * @category   Mage
 * @package    Mage_Wishlist
 *
 * @method $this setWishlistId(int $value)
 */
class Mage_Wishlist_Block_Share_Email_Rss extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('wishlist/email/rss.phtml');
    }
}
