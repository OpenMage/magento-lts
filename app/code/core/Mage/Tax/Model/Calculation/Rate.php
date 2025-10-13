<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 */

/**
 * Tax Rate Model
 *
 * @package    Mage_Tax
 *
 * @method Mage_Tax_Model_Resource_Calculation_Rate _getResource()
 * @method Mage_Tax_Model_Resource_Calculation_Rate getResource()
 * @method Mage_Tax_Model_Resource_Calculation_Rate_Collection getCollection()
 *
 * @method string getCode()
 * @method $this setCode(string $value)
 * @method float getRate()
 * @method $this setRate(float $value)
 * @method int getTaxCalculationRateId()
 * @method bool hasTaxPostcode()
 * @method string getTaxCountryId()
 * @method $this setTaxCountryId(string $value)
 * @method int getTaxRegionId()
 * @method $this setTaxRegionId(int $value)
 * @method $this setRegionName(string $value)
 * @method string getTaxPostcode()
 * @method $this setTaxPostcode(string $value)
 * @method array getTitle()
 * @method $this setTitle(array $value)
 * @method int getZipIsRange()
 * @method $this setZipIsRange(int $value)
 * @method int getZipFrom()
 * @method $this setZipFrom(int $value)
 * @method int getZipTo()
 * @method $this setZipTo(int $value)
 */
class Mage_Tax_Model_Calculation_Rate extends Mage_Core_Model_Abstract
{
    /**
     * List of tax titles
     *
     * @var array|null
     */
    protected $_titles = null;

    /**
     * The Mage_Tax_Model_Calculation_Rate_Title
     *
     * @var Mage_Tax_Model_Calculation_Rate_Title|null
     */
    protected $_titleModel = null;

    /**
     * Varien model constructor
     */
    protected function _construct()
    {
        $this->_init('tax/calculation_rate');
    }

    /**
     * Prepare location settings and tax postcode before save rate
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        if ($this->getCode() === '' || $this->getTaxCountryId() === '' || $this->getRate() === ''
            || $this->getZipIsRange() && ($this->getZipFrom() === '' || $this->getZipTo() === '')
        ) {
            Mage::throwException(Mage::helper('tax')->__('Please fill all required fields with valid information.'));
        }

        if (!is_numeric($this->getRate()) || $this->getRate() < 0) {
            Mage::throwException(Mage::helper('tax')->__('Rate Percent should be a positive number.'));
        }

        if ($this->getZipIsRange()) {
            $zipFrom = $this->getZipFrom();
            $zipTo = $this->getZipTo();

            if (strlen($zipFrom) > 9 || strlen($zipTo) > 9) {
                Mage::throwException(Mage::helper('tax')->__('Maximum zip code length is 9.'));
            }

            if (!is_numeric($zipFrom) || !is_numeric($zipTo) || $zipFrom < 0 || $zipTo < 0) {
                Mage::throwException(Mage::helper('tax')->__('Zip code should not contain characters other than digits.'));
            }

            if ($zipFrom > $zipTo) {
                Mage::throwException(Mage::helper('tax')->__('Range To should be equal or greater than Range From.'));
            }

            $this->setTaxPostcode($zipFrom . '-' . $zipTo);
        } else {
            $taxPostCode = $this->getTaxPostcode();

            if (strlen($taxPostCode) > 10) {
                $taxPostCode = substr($taxPostCode, 0, 10);
            }

            $this->setTaxPostcode($taxPostCode)
                ->setZipIsRange(null)
                ->setZipFrom(null)
                ->setZipTo(null);
        }

        parent::_beforeSave();
        $country = $this->getTaxCountryId();
        $region = $this->getTaxRegionId();
        $regionModel = Mage::getModel('directory/region');
        $regionModel->load($region);
        if ($regionModel->getCountryId() != $country) {
            $this->setTaxRegionId('*');
        }

        return $this;
    }

    /**
     * Save rate titles
     *
     * @inheritDoc
     */
    protected function _afterSave()
    {
        $this->saveTitles();
        Mage::dispatchEvent('tax_settings_change_after');
        return parent::_afterSave();
    }

    /**
     * Processing object before delete data
     *
     * @return Mage_Core_Model_Abstract
     * @throws Mage_Core_Exception
     */
    protected function _beforeDelete()
    {
        if ($this->_isInRule()) {
            Mage::throwException(Mage::helper('tax')->__('Tax rate cannot be removed. It exists in tax rule'));
        }

        return parent::_beforeDelete();
    }

    /**
     * After rate delete
     * redeclared for dispatch tax_settings_change_after event
     *
     * @inheritDoc
     */
    protected function _afterDelete()
    {
        Mage::dispatchEvent('tax_settings_change_after');
        return parent::_afterDelete();
    }

    /**
     * Saves the tax titles
     *
     * @param array | null $titles
     */
    public function saveTitles($titles = null)
    {
        if (is_null($titles)) {
            $titles = $this->getTitle();
        }

        $this->getTitleModel()->deleteByRateId($this->getId());
        if (is_array($titles) && $titles) {
            foreach ($titles as $store => $title) {
                if ($title !== '') {
                    $this->getTitleModel()
                        ->setId(null)
                        ->setTaxCalculationRateId($this->getId())
                        ->setStoreId((int) $store)
                        ->setValue($title)
                        ->save();
                }
            }
        }
    }

    /**
     * Returns the Mage_Tax_Model_Calculation_Rate_Title
     *
     * @return Mage_Tax_Model_Calculation_Rate_Title
     */
    public function getTitleModel()
    {
        if (is_null($this->_titleModel)) {
            $this->_titleModel = Mage::getModel('tax/calculation_rate_title');
        }

        return $this->_titleModel;
    }

    /**
     * Returns the list of tax titles
     *
     * @return array
     */
    public function getTitles()
    {
        if (is_null($this->_titles)) {
            $this->_titles = $this->getTitleModel()->getCollection()->loadByRateId($this->getId());
        }

        return $this->_titles;
    }

    /**
     * Deletes all tax rates
     *
     * @return $this
     */
    public function deleteAllRates()
    {
        $this->_getResource()->deleteAllRates();
        Mage::dispatchEvent('tax_settings_change_after');
        return $this;
    }

    /**
     * Load rate model by code
     *
     * @param  string $code
     * @return $this
     */
    public function loadByCode($code)
    {
        $this->load($code, 'code');
        return $this;
    }

    /**
     * Check if rate exists in tax rule
     *
     * @return array
     */
    protected function _isInRule()
    {
        return $this->getResource()->isInRule($this->getId());
    }
}
