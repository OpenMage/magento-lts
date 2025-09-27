<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_SalesRule
 */

/**
 * Rule report resource model
 *
 * @package    Mage_SalesRule
 */
class Mage_SalesRule_Model_Resource_Report_Rule extends Mage_Reports_Model_Resource_Report_Abstract
{
    protected function _construct()
    {
        $this->_setResource('salesrule');
    }

    /**
     * Aggregate Coupons data
     *
     * @param mixed $from
     * @param mixed $to
     * @return $this
     */
    public function aggregate($from = null, $to = null)
    {
        Mage::getResourceModel('salesrule/report_rule_createdat')->aggregate($from, $to);
        Mage::getResourceModel('salesrule/report_rule_updatedat')->aggregate($from, $to);
        $this->_setFlagData(Mage_Reports_Model_Flag::REPORT_COUPONS_FLAG_CODE);

        return $this;
    }

    /**
     * Get all unique Rule Names from aggregated coupons usage data
     *
     * @return array
     */
    public function getUniqRulesNamesList()
    {
        $adapter = $this->_getReadAdapter();
        $tableName = $this->getTable('salesrule/coupon_aggregated');
        $select = $adapter->select()
            ->from(
                $tableName,
                new Zend_Db_Expr('DISTINCT rule_name'),
            )
            ->where('rule_name IS NOT NULL')
            ->where('rule_name <> ""')
            ->order('rule_name ASC');

        $rulesNames = $adapter->fetchAll($select);

        $result = [];

        foreach ($rulesNames as $row) {
            $result[] = $row['rule_name'];
        }

        return $result;
    }

    /**
     * Aggregate coupons reports by order created at as range
     *
     * @deprecated after 1.6.0.0-rc2
     *
     * @param mixed $from
     * @param mixed $to
     * @return $this
     */
    protected function _aggregateByOrderCreatedAt($from, $to)
    {
        Mage::getResourceModel('salesrule/report_rule_createdat')->aggregate($from, $to);
        return $this;
    }
}
