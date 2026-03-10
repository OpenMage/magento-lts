<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

/**
 * Downloadable links purchased items resource collection
 *
 * @package    Mage_Downloadable
 */
class Mage_Downloadable_Model_Resource_Link_Purchased_Item_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('downloadable/link_purchased_item');
    }
}
