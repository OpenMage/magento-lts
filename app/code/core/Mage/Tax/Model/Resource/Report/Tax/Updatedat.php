<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 */

/**
 * Tax report resource model with aggregation by updated at
 *
 * @package    Mage_Tax
 */
class Mage_Tax_Model_Resource_Report_Tax_Updatedat extends Mage_Tax_Model_Resource_Report_Tax_Createdat
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('tax/tax_order_aggregated_updated', 'id');
    }

    /**
     * Aggregate Tax data by order updated at
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
