<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Tax
 */

/**
 * Sales order tax resource model
 *
 * @category   Mage
 * @package    Mage_Tax
 */
class Mage_Tax_Model_Resource_Sales_Order_Tax_Item extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('tax/sales_order_tax_item', 'tax_item_id');
    }

    /**
     * Get Tax Items with order tax information
     *
     * @param int $itemId
     * @return array
     */
    public function getTaxItemsByItemId($itemId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from(['item' => $this->getTable('tax/sales_order_tax_item')], ['tax_id', 'tax_percent'])
            ->join(
                ['tax' => $this->getTable('tax/sales_order_tax')],
                'item.tax_id = tax.tax_id',
                ['title', 'percent', 'base_amount'],
            )
            ->where('item_id = ?', $itemId);

        // phpcs:ignore Ecg.Performance.FetchAll.Found
        return $adapter->fetchAll($select);
    }
}
