<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 */

/**
 * CatalogInventory Stock source model
 *
 * @category   Mage
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
