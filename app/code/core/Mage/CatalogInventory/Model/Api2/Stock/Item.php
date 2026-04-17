<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogInventory
 */

/**
 * API2 class for stock item
 *
 * @package    Mage_CatalogInventory
 */
class Mage_CatalogInventory_Model_Api2_Stock_Item extends Mage_Api2_Model_Resource
{
    /**
     * Load stock item by id
     *
     * @param  int                                    $id
     * @return Mage_CatalogInventory_Model_Stock_Item
     * @throws Mage_Api2_Exception
     */
    protected function _loadStockItemById($id)
    {
        /** @var Mage_CatalogInventory_Model_Stock_Item $stockItem */
        $stockItem = Mage::getModel('cataloginventory/stock_item')->load($id);
        if (!$stockItem->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        return $stockItem;
    }
}
