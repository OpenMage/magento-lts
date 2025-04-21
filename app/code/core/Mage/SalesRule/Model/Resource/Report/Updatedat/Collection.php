<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_SalesRule
 */

/**
 * Sales report coupons collection
 *
 * @package    Mage_SalesRule
 */
class Mage_SalesRule_Model_Resource_Report_Updatedat_Collection extends Mage_SalesRule_Model_Resource_Report_Collection
{
    /**
     * Aggregated Data Table
     *
     * @var string
     */
    protected $_aggregationTable = 'salesrule/coupon_aggregated_updated';
}
