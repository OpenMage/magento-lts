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
    #[Override]
    protected function _construct()
    {
        $this->_init('salesrule/coupon_aggregated_updated', 'id');
    }

    /**
     * Aggregate Coupons data by order updated at
     *
     * @inheritDoc
     */
    #[Override]
    public function aggregate($dateFrom = null, $dateTo = null)
    {
        return $this->_aggregateByOrder('updated_at', $dateFrom, $dateTo);
    }
}
