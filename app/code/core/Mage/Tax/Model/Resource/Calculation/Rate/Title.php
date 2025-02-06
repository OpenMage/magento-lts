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
 */
class Mage_Tax_Model_Resource_Calculation_Rate_Title extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('tax/tax_calculation_rate_title', 'tax_calculation_rate_title_id');
    }

    /**
     * Delete title by rate identifier
     *
     * @param int $rateId
     * @return $this
     */
    public function deleteByRateId($rateId)
    {
        $conn = $this->_getWriteAdapter();
        $where = $conn->quoteInto('tax_calculation_rate_id = ?', (int) $rateId);
        $conn->delete($this->getMainTable(), $where);

        return $this;
    }
}
