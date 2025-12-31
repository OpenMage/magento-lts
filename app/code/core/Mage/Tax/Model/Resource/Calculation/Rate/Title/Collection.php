<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 */

/**
 * Tax Rate Title Collection
 *
 * @package    Mage_Tax
 *
 * @method Mage_Tax_Model_Calculation_Rate_Title[] getItems()
 */
class Mage_Tax_Model_Resource_Calculation_Rate_Title_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('tax/calculation_rate_title', 'tax/calculation_rate_title');
    }

    /**
     * Add rate id filter
     *
     * @param  int   $rateId
     * @return $this
     */
    public function loadByRateId($rateId)
    {
        $this->addFieldToFilter('main_table.tax_calculation_rate_id', $rateId);
        return $this->load();
    }
}
