<?php

declare(strict_types=1);

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
 * @method Mage_Tax_Model_Resource_Calculation_Rate            _getResource()
 * @method Mage_Tax_Model_Resource_Calculation_Rate_Collection getCollection()
 * @method Mage_Tax_Model_Resource_Calculation_Rate            getResource()
 * @method Mage_Tax_Model_Resource_Calculation_Rate_Collection getResourceCollection()
 * @method array                                               getTitle()
 * @method bool                                                hasTaxCountryId()
 * @method bool                                                hasTaxPostcode()
 * @method bool                                                hasTaxRegionId()
 * @method $this                                               setRegionName(string $value)
 * @method $this                                               setTitle(array $value)
 */
class Mage_Tax_Model_Calculation_Rate extends Mage_Core_Model_Abstract
{
    /**
     * List of tax titles
     *
     * @var null|Mage_Tax_Model_Resource_Calculation_Rate_Title_Collection
     */
    protected $_titles = null;

    /**
     * The Mage_Tax_Model_Calculation_Rate_Title
     *
     * @var null|Mage_Tax_Model_Calculation_Rate_Title
     */
    protected $_titleModel = null;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('tax/calculation_rate');
    }

    /**
     * Prepare location settings and tax postcode before save rate
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    #[Override]
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
    #[Override]
    protected function _afterSave()
    {
        $this->saveTitles();
        Mage::dispatchEvent('tax_settings_change_after');
        return parent::_afterSave();
    }

    /**
     * Processing object before delete data
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    #[Override]
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
    #[Override]
    protected function _afterDelete()
    {
        Mage::dispatchEvent('tax_settings_change_after');
        return parent::_afterDelete();
    }

    /**
     * Saves the tax titles
     *
     * @param  null|array          $titles
     * @throws Mage_Core_Exception
     * @throws Throwable
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
     * @return Mage_Tax_Model_Resource_Calculation_Rate_Title_Collection
     * @throws Mage_Core_Exception
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
     * @throws Mage_Core_Exception
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
     * @param  string              $code
     * @return $this
     * @throws Mage_Core_Exception
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
     * @throws Mage_Core_Exception
     */
    protected function _isInRule()
    {
        return $this->getResource()->isInRule($this->getId());
    }

    public function getCode(): string
    {
        return (string) $this->_getData('code');
    }

    public function setCode(string $value): static
    {
        return $this->setData('code', $value);
    }

    public function getRate(): string
    {
        return (string) $this->_getData('rate');
    }

    public function setRate(string $value): static
    {
        return $this->setData('rate', $value);
    }

    public function getTaxCalculationRateId(): int
    {
        return (int) $this->_getData('tax_calculation_rate_id');
    }

    public function getTaxCountryId(): string
    {
        return (string) $this->_getData('tax_country_id');
    }

    public function setTaxCountryId(string $value): static
    {
        return $this->setData('tax_country_id', $value);
    }

    public function getTaxPostcode(): ?string
    {
        $value = $this->_getData('tax_postcode');
        return $value !== null ? (string) $value : null;
    }

    public function setTaxPostcode(string $value): static
    {
        return $this->setData('tax_postcode', $value);
    }

    public function getTaxRegionId(): int
    {
        return (int) $this->_getData('tax_region_id');
    }

    public function setTaxRegionId(int $value): static
    {
        return $this->setData('tax_region_id', $value);
    }

    public function getZipFrom(): ?string
    {
        $value = $this->_getData('zip_from');
        return $value !== null ? (string) $value : null;
    }

    public function setZipFrom(?string $value): static
    {
        return $this->setData('zip_from', $value);
    }

    public function getZipIsRange(): ?int
    {
        $value = $this->_getData('zip_is_range');
        return $value !== null ? (int) $value : null;
    }

    public function setZipIsRange(?int $value): static
    {
        return $this->setData('zip_is_range', $value);
    }

    public function getZipTo(): ?string
    {
        $value = $this->_getData('zip_to');
        return $value !== null ? (string) $value : null;
    }

    public function setZipTo(?string $value): static
    {
        return $this->setData('zip_to', $value);
    }
}
