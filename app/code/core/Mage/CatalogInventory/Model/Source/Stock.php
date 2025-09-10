<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogInventory
 */

/**
 * CatalogInventory Stock source model
 *
 * @package    Mage_CatalogInventory
 */
class Mage_CatalogInventory_Model_Source_Stock
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => Mage_CatalogInventory_Model_Stock::STOCK_IN_STOCK,
                'label' => Mage::helper('cataloginventory')->__('In Stock'),
            ],
            [
                'value' => Mage_CatalogInventory_Model_Stock::STOCK_OUT_OF_STOCK,
                'label' => Mage::helper('cataloginventory')->__('Out of Stock'),
            ],
        ];
    }
}
