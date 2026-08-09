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
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_setResource('salesrule');
    }

    /**
     * Aggregate Coupons data
     *
     * @param  null|string $dateFrom
     * @param  null|string $dateTo
     * @return $this
     */
    public function aggregate($dateFrom = null, $dateTo = null)
    {
        Mage::getResourceModel('salesrule/report_rule_createdat')->aggregate($dateFrom, $dateTo);
        Mage::getResourceModel('salesrule/report_rule_updatedat')->aggregate($dateFrom, $dateTo);
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
     * @param  null|string $dateFrom
     * @param  null|string $dateTo
     * @return $this
     */
    #[Deprecated(since: OpenMageVersionInterface::VERSION_1_6_0_0_RC_2)]
    protected function _aggregateByOrderCreatedAt($dateFrom, $dateTo)
    {
        Mage::getResourceModel('salesrule/report_rule_createdat')->aggregate($dateFrom, $dateTo);
        return $this;
    }
}
