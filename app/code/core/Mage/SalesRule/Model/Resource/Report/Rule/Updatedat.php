<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_SalesRule
 */

/**
 * Rule report resource model with aggregation by updated at
 *
 * @package    Mage_SalesRule
 */
class Mage_SalesRule_Model_Resource_Report_Rule_Updatedat extends Mage_SalesRule_Model_Resource_Report_Rule_Createdat
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('salesrule/coupon_aggregated_updated', 'id');
    }

    /**
     * Aggregate Coupons data by order updated at
     *
     * @param  mixed $from
     * @param  mixed $to
     * @return $this
     */
    public function aggregate($from = null, $to = null)
    {
        return $this->_aggregateByOrder('updated_at', $from, $to);
    }
}
