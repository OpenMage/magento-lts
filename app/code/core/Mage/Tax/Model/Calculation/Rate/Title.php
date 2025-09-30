<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 */

/**
 * Tax Rate Title Model
 *
 * @package    Mage_Tax
 *
 * @method Mage_Tax_Model_Resource_Calculation_Rate_Title _getResource()
 * @method Mage_Tax_Model_Resource_Calculation_Rate_Title getResource()
 * @method Mage_Tax_Model_Resource_Calculation_Rate_Title_Collection getCollection()
 *
 * @method int getTaxCalculationRateId()
 * @method $this setTaxCalculationRateId(int $value)
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method string getValue()
 * @method $this setValue(string $value)
 */
class Mage_Tax_Model_Calculation_Rate_Title extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('tax/calculation_rate_title');
    }

    /**
     * @param int $rateId
     * @return $this
     */
    public function deleteByRateId($rateId)
    {
        $this->getResource()->deleteByRateId($rateId);
        return $this;
    }
}
