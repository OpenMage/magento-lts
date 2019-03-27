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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Coupons Report collection
 *
 * @category    Mage
 * @package     Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Resource_Coupons_Collection extends Mage_Sales_Model_Entity_Order_Collection
{
    /**
     * From value
     *
     * @var string
     */
    protected $_from     = '';

    /**
     * To value
     *
     * @var string
     */
    protected $_to       = '';

    /**
     * Set date range
     *
     * @param string $from
     * @param string $to
     * @return Mage_Reports_Model_Resource_Coupons_Collection
     */
    public function setDateRange($from, $to)
    {
        $this->_from = $from;
        $this->_to   = $to;
        $this->_reset();
        return $this;
    }

    /**
     * Set store ids
     *
     * @param array $storeIds
     * @return Mage_Reports_Model_Resource_Coupons_Collection
     */
    public function setStoreIds($storeIds)
    {
        $this->joinFields($this->_from, $this->_to, $storeIds);
        return $this;
    }

    /**
     * Join fields
     *
     * @param string $from
     * @param string $to
     * @param array $storeIds
     */
    public function joinFields($from, $to, $storeIds = array())
    {
        $this->groupByAttribute('coupon_code')
            ->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to, 'datetime' => true))
            ->addAttributeToFilter('coupon_code', array('neq' => ''))
            ->getSelect()->columns(array('uses' => 'COUNT(e.entity_id)'))
            ->having('uses > ?', 0)
            ->order('uses ' . self::SORT_ORDER_DESC);

        $storeIds = array_values($storeIds);
        if (count($storeIds) >= 1 && $storeIds[0] != '') {
            $this->addAttributeToFilter('store_id', array('in' => $storeIds));
            $this->addExpressionAttributeToSelect(
                'subtotal',
                'SUM({{base_subtotal}})',
                array('base_subtotal'))
            ->addExpressionAttributeToSelect(
                'discount',
                'SUM({{base_discount_amount}})',
                array('base_discount_amount'))
            ->addExpressionAttributeToSelect(
                'total',
                'SUM({{base_subtotal}}-{{base_discount_amount}})',
                array('base_subtotal', 'base_discount_amount'));
        } else {
            $this->addExpressionAttributeToSelect(
                'subtotal',
                'SUM({{base_subtotal}}*{{base_to_global_rate}})',
                array('base_subtotal', 'base_to_global_rate'))
            ->addExpressionAttributeToSelect(
                'discount',
                'SUM({{base_discount_amount}}*{{base_to_global_rate}})',
                array('base_discount_amount', 'base_to_global_rate'))
            ->addExpressionAttributeToSelect(
                'total',
                'SUM(({{base_subtotal}}-{{base_discount_amount}})*{{base_to_global_rate}})',
                array('base_subtotal', 'base_discount_amount', 'base_to_global_rate'));
        }

    }

    /**
     * Get select count sql
     *
     * @return string
     */
    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::HAVING);
        $countSelect->columns('COUNT(DISTINCT main_table.rule_id)');

        return $countSelect;
    }
}
