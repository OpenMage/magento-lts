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
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Tax rate resource model
 *
 * @category    Mage
 * @package     Mage_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tax_Model_Resource_Calculation_Rule extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('tax/tax_calculation_rule', 'tax_calculation_rule_id');
    }

    /**
     * Initialize unique fields
     *
     * @return Mage_Tax_Model_Resource_Calculation_Rule
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(array(
            'field' => array('code'),
            'title' => Mage::helper('tax')->__('Code'),
        ));
        return $this;
    }

    /**
     * Fetches rules by rate, customer tax class and product tax class
     * Returns array of rule codes
     *
     * @param array $rateId
     * @param array $customerTaxClassId
     * @param array $productTaxClassId
     * @return array
     */
    public function fetchRuleCodes($rateId, $customerTaxClassId, $productTaxClassId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from(array('main' => $this->getTable('tax/tax_calculation')), null)
            ->joinLeft(
            array('d' => $this->getTable('tax/tax_calculation_rule')),
            'd.tax_calculation_rule_id = main.tax_calculation_rule_id',
            array('d.code'))
            ->where('main.tax_calculation_rate_id in (?)', $rateId)
            ->where('main.customer_tax_class_id in (?)', $customerTaxClassId)
            ->where('main.product_tax_class_id in (?)', $productTaxClassId)
            ->distinct(true);

        return $adapter->fetchCol($select);
    }
}
