<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Reports
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Reports orders collection
 *
 * @category    Mage
 * @package     Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Resource_Shipping_Collection extends Mage_Sales_Model_Entity_Order_Collection
{
    /**
     * Set date range
     *
     * @param string $from
     * @param string $to
     * @return Mage_Reports_Model_Resource_Shipping_Collection
     */
    public function setDateRange($from, $to)
    {
        $this->_reset()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to))
            ->addExpressionAttributeToSelect('orders', 'COUNT(DISTINCT({{entity_id}}))', array('entity_id'))
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
     * @return Mage_Reports_Model_Resource_Shipping_Collection
     */
    public function setStoreIds($storeIds)
    {
        if ($storeIds) {
            $this->addAttributeToFilter('store_id', array('in' => (array)$storeIds));
            $this->addExpressionAttributeToSelect('total',
                'SUM({{base_shipping_amount}})',
                array('base_shipping_amount'));
        } else {
            $this->addExpressionAttributeToSelect('total',
                'SUM({{base_shipping_amount}}*{{base_to_global_rate}})',
                array('base_shipping_amount', 'base_to_global_rate'));
        }

        return $this;
    }
}
