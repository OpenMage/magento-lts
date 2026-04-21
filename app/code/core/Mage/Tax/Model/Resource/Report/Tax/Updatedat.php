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
    #[Override]
    protected function _construct()
    {
        $this->_init('tax/tax_order_aggregated_updated', 'id');
    }

    /**
     * Aggregate Tax data by order updated at
     *
     * @inheritDoc
     */
    #[Override]
    public function aggregate($dateFrom = null, $dateTo = null)
    {
        return $this->_aggregateByOrder('updated_at', $dateFrom, $dateTo);
    }
}
