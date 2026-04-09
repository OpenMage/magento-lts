<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */
/**
 * Reports orders collection
 *
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Shipping_Collection extends Mage_Sales_Model_Entity_Order_Collection
{
    /**
     * Set date range
     *
     * @param  null|string $dateFrom
     * @param  null|string $dateTo
     * @return $this
     */
    public function setDateRange($dateFrom, $dateTo)
    {
        $this->_reset()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('created_at', ['from' => $dateFrom, 'to' => $dateTo])
            ->addExpressionAttributeToSelect('orders', 'COUNT(DISTINCT({{entity_id}}))', ['entity_id'])
            ->addAttributeToSelect('shipping_description')
            ->groupByAttribute('shipping_description')
            ->getSelect()->order('orders ' . self::SORT_ORDER_DESC);

        $this->getSelect()->where("table_shipping_description.value <> ''");
        return $this;
    }

    /**
     * Set store filter to collection
     *
     * @param  array $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds)
    {
        if ($storeIds) {
            $this->addAttributeToFilter('store_id', ['in' => (array) $storeIds]);
            $this->addExpressionAttributeToSelect(
                'total',
                'SUM({{base_shipping_amount}})',
                ['base_shipping_amount'],
            );
        } else {
            $this->addExpressionAttributeToSelect(
                'total',
                'SUM({{base_shipping_amount}}*{{base_to_global_rate}})',
                ['base_shipping_amount', 'base_to_global_rate'],
            );
        }

        return $this;
    }
}
