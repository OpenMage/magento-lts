<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Wishlist
 */

/**
 * Wishlist session model
 *
 * @category   Mage
 * @package    Mage_Wishlist
 *
 * @method $this setSharingForm(array $value)
 */
class Mage_Wishlist_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $this->init('wishlist');
    }
}
