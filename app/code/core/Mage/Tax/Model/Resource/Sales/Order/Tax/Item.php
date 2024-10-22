<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
                ['title', 'percent', 'base_amount']
            )
            ->where('item_id = ?', $itemId);

        // phpcs:ignore Ecg.Performance.FetchAll.Found
        return $adapter->fetchAll($select);
    }
}
