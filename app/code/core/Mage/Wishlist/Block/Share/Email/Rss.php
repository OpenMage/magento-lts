<?php
/**
 * Wishlist RSS URL to Email Block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Wishlist
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
