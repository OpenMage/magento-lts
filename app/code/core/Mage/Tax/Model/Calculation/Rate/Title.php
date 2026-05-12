<?php

declare(strict_types=1);

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
 * @method Mage_Tax_Model_Resource_Calculation_Rate_Title            _getResource()
 * @method Mage_Tax_Model_Resource_Calculation_Rate_Title_Collection getCollection()
 * @method Mage_Tax_Model_Resource_Calculation_Rate_Title            getResource()
 * @method Mage_Tax_Model_Resource_Calculation_Rate_Title_Collection getResourceCollection()
 */
class Mage_Tax_Model_Calculation_Rate_Title extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('tax/calculation_rate_title');
    }

    /**
     * @param  int   $rateId
     * @return $this
     */
    public function deleteByRateId($rateId)
    {
        $this->getResource()->deleteByRateId($rateId);
        return $this;
    }

    public function getStoreId(): int
    {
        return (int) $this->_getData('store_id');
    }

    public function setStoreId(int $value): static
    {
        return $this->setData('store_id', $value);
    }

    public function getTaxCalculationRateId(): int
    {
        return (int) $this->_getData('tax_calculation_rate_id');
    }

    public function setTaxCalculationRateId(int $value): static
    {
        return $this->setData('tax_calculation_rate_id', $value);
    }

    public function getValue(): string
    {
        return (string) $this->_getData('value');
    }

    public function setValue(string $value): static
    {
        return $this->setData('value', $value);
    }
}
