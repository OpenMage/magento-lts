<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_SalesRule
 */

/**
 * SalesRule Model Resource Coupon_Collection
 *
 * @package    Mage_SalesRule
 */
class Mage_SalesRule_Model_Resource_Coupon_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('salesrule/coupon');
    }

    /**
     * Add rule to filter
     *
     * @param int|Mage_SalesRule_Model_Rule $rule
     *
     * @return $this
     */
    public function addRuleToFilter($rule)
    {
        if ($rule instanceof Mage_SalesRule_Model_Rule) {
            $ruleId = $rule->getId();
        } else {
            $ruleId = (int) $rule;
        }

        $this->addFieldToFilter('rule_id', $ruleId);

        return $this;
    }

    /**
     * Add rule IDs to filter
     *
     * @return $this
     */
    public function addRuleIdsToFilter(array $ruleIds)
    {
        $this->addFieldToFilter('rule_id', ['in' => $ruleIds]);
        return $this;
    }

    /**
     * Filter collection to be filled with auto-generated coupons only
     *
     * @return $this
     */
    public function addGeneratedCouponsFilter()
    {
        $this->addFieldToFilter('is_primary', ['null' => 1])->addFieldToFilter('type', '1');
        return $this;
    }

    /**
     * Callback function that filters collection by field "Used" from grid
     *
     * @param Mage_Core_Model_Resource_Db_Collection_Abstract $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     */
    public function addIsUsedFilterCallback($collection, $column)
    {
        $filterValue = $column->getFilter()->getCondition();

        $fieldExpression = $this->getConnection()->getCheckSql('main_table.times_used > 0', '1', '0');
        $resultCondition = $this->_getConditionSql($fieldExpression, ['eq' => $filterValue]);
        $collection->getSelect()->where($resultCondition);
    }
}
