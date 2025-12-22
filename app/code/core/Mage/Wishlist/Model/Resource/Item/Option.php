<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Wishlist
 */

/**
 * Wishlist item option resource model
 *
 * @package    Mage_Wishlist
 */
class Mage_Wishlist_Model_Resource_Item_Option extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('wishlist/item_option', 'option_id');
    }
}
