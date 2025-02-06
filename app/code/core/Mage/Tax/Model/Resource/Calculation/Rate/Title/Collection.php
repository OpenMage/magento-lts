<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Tax
 */

/**
 * Tax Rate Title Collection
 *
 * @category   Mage
 * @package    Mage_Tax
 *
 * @method Mage_Tax_Model_Calculation_Rate_Title[] getItems()
 */
class Mage_Tax_Model_Resource_Calculation_Rate_Title_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('tax/calculation_rate_title', 'tax/calculation_rate_title');
    }

    /**
     * Add rate id filter
     *
     * @param int $rateId
     * @return $this
     */
    public function loadByRateId($rateId)
    {
        $this->addFieldToFilter('main_table.tax_calculation_rate_id', $rateId);
        return $this->load();
    }
}
