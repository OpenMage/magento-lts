<?php
/**
 * CatalogInventory Stock source model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
