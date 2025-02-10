<?php
/**
 * Downloadable Product link purchased items resource model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Downloadable
 */
class Mage_Downloadable_Model_Resource_Link_Purchased_Item extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('downloadable/link_purchased_item', 'item_id');
    }
}
