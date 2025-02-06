<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Wishlist
 */

/**
 * Wishlist item option resource model
 *
 * @category   Mage
 * @package    Mage_Wishlist
 */
class Mage_Wishlist_Model_Resource_Item_Option extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('wishlist/item_option', 'option_id');
    }
}
