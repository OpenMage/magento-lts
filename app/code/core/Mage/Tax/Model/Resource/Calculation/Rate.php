<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Tax
 */

/**
 * Tax rate resource model
 *
 * @category   Mage
 * @package    Mage_Tax
 */
class Mage_Tax_Model_Resource_Calculation_Rate extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('tax/tax_calculation_rate', 'tax_calculation_rate_id');
    }

    /**
     * Initialize unique fields
     *
     * @return $this
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = [[
            'field' => ['code'],
            'title' => Mage::helper('tax')->__('Code'),
        ]];
        return $this;
    }

    /**
     * Delete all rates
     *
     * @return $this
     */
    public function deleteAllRates()
    {
        $this->_getWriteAdapter()->delete($this->getMainTable());
        return $this;
    }

    /**
     * Check if this rate exists in rule
     *
     * @param  int $rateId
     * @return array
     */
    public function isInRule($rateId)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getTable('tax/tax_calculation'), ['tax_calculation_rate_id'])
            ->where('tax_calculation_rate_id = ?', $rateId);
        return $adapter->fetchCol($select);
    }
}
