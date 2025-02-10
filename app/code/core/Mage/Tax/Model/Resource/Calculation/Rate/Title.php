<?php
/**
 * Tax Rate Title Collection
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
