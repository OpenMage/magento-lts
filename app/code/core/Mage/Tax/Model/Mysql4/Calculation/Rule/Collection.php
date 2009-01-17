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
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax rule collection
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tax_Model_Mysql4_Calculation_Rule_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('tax/calculation_rule');
    }

    public function joinCalculationData($alias)
    {
        $this->getSelect()->joinLeft(array($alias=>$this->getTable('tax_calculation')), "main_table.tax_calculation_rule_id = {$alias}.tax_calculation_rule_id", array());
        $this->getSelect()->group('main_table.tax_calculation_rule_id');
    }

    protected function _add($itemTable, $primaryJoinField, $secondaryJoinField, $titleField, $dataField)
    {
        $children = array();
        foreach ($this as $rule) {
            $children[$rule->getId()] = array();
        }
        if (!empty($children)) {
            $select = $this->getConnection()->select()
                ->from(array('calculation'=>$this->getTable('tax_calculation')), array('calculation.tax_calculation_rule_id'))
                ->join(
                    array('item'=>$this->getTable($itemTable)),
                    "item.{$secondaryJoinField} = calculation.{$primaryJoinField}",
                    array("item.{$titleField}"))
                ->where('calculation.tax_calculation_rule_id IN (?)', array_keys($children))
                ->distinct(true);

            $data = $this->getConnection()->fetchAll($select);
            foreach ($data as $row) {
        	   $children[$row['tax_calculation_rule_id']][] = $row[$titleField];
            }
        }
        foreach ($this as $rule) {
            if (isset($children[$rule->getId()])) {
                $rule->setData($dataField, $children[$rule->getId()]);
            }
        }
        return $this;
    }

    public function addProductTaxClassesToResult()
    {
        return $this->_add('tax_class', 'product_tax_class_id', 'class_id', 'class_name', 'product_tax_classes');
    }
    public function addCustomerTaxClassesToResult()
    {
        return $this->_add('tax_class', 'customer_tax_class_id', 'class_id', 'class_name', 'customer_tax_classes');
    }
    public function addRatesToResult()
    {
        return $this->_add('tax_calculation_rate', 'tax_calculation_rate_id', 'tax_calculation_rate_id', 'code', 'tax_rates');
    }

    public function setClassTypeFilter($type, $id)
    {
        switch ($type) {
            case 'PRODUCT':
                $field = 'cd.product_tax_class_id';
                break;
            case 'CUSTOMER':
                $field = 'cd.customer_tax_class_id';
                break;
            default:
                Mage::throwException('Invalid type supplied');
        }

        $this->joinCalculationData('cd');
        $this->addFieldToFilter($field, $id);
        return $this;
    }
}