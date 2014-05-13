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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax Rule Model
 *
 * @method Mage_Tax_Model_Resource_Calculation_Rule _getResource()
 * @method Mage_Tax_Model_Resource_Calculation_Rule getResource()
 * @method string getCode()
 * @method Mage_Tax_Model_Calculation_Rule setCode(string $value)
 * @method int getPriority()
 * @method Mage_Tax_Model_Calculation_Rule setPriority(int $value)
 * @method int getPosition()
 * @method Mage_Tax_Model_Calculation_Rule setPosition(int $value)
 *
 * @category    Mage
 * @package     Mage_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tax_Model_Calculation_Rule extends Mage_Core_Model_Abstract
{
    /**
     * No references found in the project. Variable kept for backward compatibility
     *
     * @var null
     */
    protected $_ctcs                = null;

    /**
     * No references found in the project. Variable kept for backward compatibility
     *
     * @var null
     */
    protected $_ptcs                = null;

    /**
     * No references found in the project. Variable kept for backward compatibility
     *
     * @var null
     */
    protected $_rates               = null;

    /**
     * No references found in the project. Variable kept for backward compatibility
     *
     * @var null
     */
    protected $_ctcModel            = null;

    /**
     * No references found in the project. Variable kept for backward compatibility
     *
     * @var null
     */
    protected $_ptcModel            = null;

    /**
     * No references found in the project. Variable kept for backward compatibility
     *
     * @var Mage_Tax_Model_Calculation_Rate
     */
    protected $_rateModel           = null;

    /**
     * Holds the tax Calculation model
     *
     * @var Mage_Tax_Model_Calculation
     */
    protected $_calculationModel    = null;

    /**
     * Varien model constructor
     */
    protected function _construct()
    {
        $this->_init('tax/calculation_rule');
    }

    /**
     * After save rule
     * Re - declared for populate rate calculations
     *
     * @return Mage_Tax_Model_Calculation_Rule
     */
    protected function _afterSave()
    {
        parent::_afterSave();
        $this->saveCalculationData();
        Mage::dispatchEvent('tax_settings_change_after');
        return $this;
    }

    /**
     * After rule delete
     * redeclared for dispatch tax_settings_change_after event
     *
     * @return Mage_Tax_Model_Calculation_Rule
     */
    protected function _afterDelete()
    {
        Mage::dispatchEvent('tax_settings_change_after');
        return parent::_afterDelete();
    }

    /**
     * Saves the Calculation Data
     */
    public function saveCalculationData()
    {
        $ctc = $this->getData('tax_customer_class');
        $ptc = $this->getData('tax_product_class');
        $rates = $this->getData('tax_rate');

        Mage::getSingleton('tax/calculation')->deleteByRuleId($this->getId());
        foreach ($ctc as $c) {
            foreach ($ptc as $p) {
                foreach ($rates as $r) {
                    $dataArray = array(
                        'tax_calculation_rule_id'   =>$this->getId(),
                        'tax_calculation_rate_id'   =>$r,
                        'customer_tax_class_id'     =>$c,
                        'product_tax_class_id'      =>$p,
                    );
                    Mage::getSingleton('tax/calculation')->setData($dataArray)->save();
                }
            }
        }
    }

    /**
     * @return Mage_Core_Model_Abstract|Mage_Tax_Model_Calculation|null
     */
    public function getCalculationModel()
    {
        if (is_null($this->_calculationModel)) {
            $this->_calculationModel = Mage::getSingleton('tax/calculation');
        }
        return $this->_calculationModel;
    }

    /**
     * @return mixed
     */
    public function getRates()
    {
        return $this->getCalculationModel()->getRates($this->getId());
    }

    /**
     * @return mixed
     */
    public function getCustomerTaxClasses()
    {
        return $this->getCalculationModel()->getCustomerTaxClasses($this->getId());
    }

    /**
     * @return mixed
     */
    public function getProductTaxClasses()
    {
        return $this->getCalculationModel()->getProductTaxClasses($this->getId());
    }


    /**
     * Fetches rules by rate, customer tax class and product tax class
     * and product tax class combination
     *
     * @param array $rateId
     * @param array $customerTaxClassId
     * @param array $productTaxClassId
     * @return array
     */
    public function fetchRuleCodes($rateId, $customerTaxClassId, $productTaxClassId)
    {
        return $this->getResource()->fetchRuleCodes($rateId, $customerTaxClassId, $productTaxClassId);
    }
}

