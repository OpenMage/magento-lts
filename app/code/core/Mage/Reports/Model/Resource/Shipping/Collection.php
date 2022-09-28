<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Reports orders collection
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Resource_Shipping_Collection extends Mage_Sales_Model_Entity_Order_Collection
{
    /**
     * Set date range
     *
     * @param string $from
     * @param string $to
     * @return $this
     */
    public function setDateRange($from, $to)
    {
        $this->_reset()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('created_at', ['from' => $from, 'to' => $to])
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
     * @param array $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds)
    {
        if ($storeIds) {
            $this->addAttributeToFilter('store_id', ['in' => (array)$storeIds]);
            $this->addExpressionAttributeToSelect(
                'total',
                'SUM({{base_shipping_amount}})',
                ['base_shipping_amount']
            );
        } else {
            $this->addExpressionAttributeToSelect(
                'total',
                'SUM({{base_shipping_amount}}*{{base_to_global_rate}})',
                ['base_shipping_amount', 'base_to_global_rate']
            );
        }

        return $this;
    }
}
