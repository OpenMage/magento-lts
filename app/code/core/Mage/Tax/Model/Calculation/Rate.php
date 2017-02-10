<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax Rate Model
 *
 * @method Mage_Tax_Model_Resource_Calculation_Rate _getResource()
 * @method Mage_Tax_Model_Resource_Calculation_Rate getResource()
 * @method string getTaxCountryId()
 * @method Mage_Tax_Model_Calculation_Rate setTaxCountryId(string $value)
 * @method int getTaxRegionId()
 * @method Mage_Tax_Model_Calculation_Rate setTaxRegionId(int $value)
 * @method string getTaxPostcode()
 * @method Mage_Tax_Model_Calculation_Rate setTaxPostcode(string $value)
 * @method string getCode()
 * @method Mage_Tax_Model_Calculation_Rate setCode(string $value)
 * @method float getRate()
 * @method Mage_Tax_Model_Calculation_Rate setRate(float $value)
 * @method int getZipIsRange()
 * @method Mage_Tax_Model_Calculation_Rate setZipIsRange(int $value)
 * @method int getZipFrom()
 * @method Mage_Tax_Model_Calculation_Rate setZipFrom(int $value)
 * @method int getZipTo()
 * @method Mage_Tax_Model_Calculation_Rate setZipTo(int $value)
 *
 * @category    Mage
 * @package     Mage_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tax_Model_Calculation_Rate extends Mage_Core_Model_Abstract
{
    /**
     * List of tax titles
     *
     * @var array
     */
    protected $_titles = null;

    /**
     * The Mage_Tax_Model_Calculation_Rate_Title
     *
     * @var Mage_Tax_Model_Calculation_Rate_Title
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
     * @return Mage_Tax_Model_Calculation_Rate
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
     * @return Mage_Tax_Model_Calculation_Rate
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
     * @return Mage_Tax_Model_Calculation_Rate
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
     * @return Mage_Tax_Model_Calculation_Rate
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
     * @return Mage_Tax_Model_Calculation_Rate
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
