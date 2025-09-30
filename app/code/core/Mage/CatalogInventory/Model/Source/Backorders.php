<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogInventory
 */

/**
 * @package    Mage_CatalogInventory
 */
class Mage_CatalogInventory_Model_Source_Backorders
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => Mage_CatalogInventory_Model_Stock::BACKORDERS_NO, 'label' => Mage::helper('cataloginventory')->__('No Backorders')],
            ['value' => Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NONOTIFY, 'label' => Mage::helper('cataloginventory')->__('Allow Qty Below 0')],
            ['value' => Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NOTIFY , 'label' => Mage::helper('cataloginventory')->__('Allow Qty Below 0 and Notify Customer')],
        ];
    }
}
