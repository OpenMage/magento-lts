<?php
/**
 * Downloadable links purchased items resource collection
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Downloadable
 */
class Mage_Downloadable_Model_Resource_Link_Purchased_Item_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Init resource model
     *
     */
    protected function _construct()
    {
        $this->_init('downloadable/link_purchased_item');
    }
}
