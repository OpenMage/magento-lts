<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Wishlist
 */

/**
 * Wishlist session model
 *
 * @package    Mage_Wishlist
 *
 * @method $this setSharingForm(string[] $value)
 */
class Mage_Wishlist_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $this->init('wishlist');
    }
}
