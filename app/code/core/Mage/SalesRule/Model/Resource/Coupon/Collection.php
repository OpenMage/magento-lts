<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_SalesRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * SalesRule Model Resource Coupon_Collection
 *
 * @category   Mage
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
     * @param Mage_SalesRule_Model_Rule|int $rule
     *
     * @return $this
     */
    public function addRuleToFilter($rule)
    {
        if ($rule instanceof Mage_SalesRule_Model_Rule) {
            $ruleId = $rule->getId();
        } else {
            $ruleId = (int)$rule;
        }

        $this->addFieldToFilter('rule_id', $ruleId);

        return $this;
    }

    /**
     * Add rule IDs to filter
     *
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
