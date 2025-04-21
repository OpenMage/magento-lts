<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Order entity resource model with aggregation by updated at
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Report_Order_Updatedat extends Mage_Sales_Model_Resource_Report_Order_Createdat
{
    protected function _construct()
    {
        $this->_init('sales/order_aggregated_updated', 'id');
    }

    /**
     * Aggregate Orders data by order updated at
     *
     * @param mixed $from
     * @param mixed $to
     * @return $this
     */
    public function aggregate($from = null, $to = null)
    {
        return $this->_aggregateByField('updated_at', $from, $to);
    }
}
