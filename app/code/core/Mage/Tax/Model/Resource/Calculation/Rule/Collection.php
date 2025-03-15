<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax rule collection
 *
 * @category   Mage
 * @package    Mage_Tax
 *
 * @method Mage_Tax_Model_Calculation_Rule[] getItems()
 */
class Mage_Tax_Model_Resource_Calculation_Rule_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('tax/calculation_rule');
    }

    /**
     * Join calculation data to result
     *
     * @param string $alias table alias
     * @return $this
     */
    public function joinCalculationData($alias)
    {
        $this->getSelect()->joinLeft(
            [$alias => $this->getTable('tax/tax_calculation')],
            "main_table.tax_calculation_rule_id = {$alias}.tax_calculation_rule_id",
            [],
        );
        $this->getSelect()->group('main_table.tax_calculation_rule_id');

        return $this;
    }

    /**
     * Join tax data to collection
     *
     * @param string $itemTable
     * @param string $primaryJoinField
     * @param string $secondaryJoinField
     * @param string $titleField
     * @param string $dataField
     * @return $this
     */
    protected function _add($itemTable, $primaryJoinField, $secondaryJoinField, $titleField, $dataField)
    {
        $children = [];
        foreach ($this as $rule) {
            $children[$rule->getId()] = [];
        }
        if (!empty($children)) {
            $joinCondition = sprintf('item.%s = calculation.%s', $secondaryJoinField, $primaryJoinField);
            $select = $this->getConnection()->select()
                ->from(
                    ['calculation' => $this->getTable('tax/tax_calculation')],
                    ['calculation.tax_calculation_rule_id'],
                )
                ->join(
                    ['item' => $this->getTable($itemTable)],
                    $joinCondition,
                    ["item.{$titleField}", "item.{$secondaryJoinField}"],
                )
                ->where('calculation.tax_calculation_rule_id IN (?)', array_keys($children))
                ->distinct(true);

            $data = $this->getConnection()->fetchAll($select);
            foreach ($data as $row) {
                $children[$row['tax_calculation_rule_id']][$row[$secondaryJoinField]] = $row[$titleField];
            }
        }

        foreach ($this as $rule) {
            if (isset($children[$rule->getId()])) {
                $rule->setData($dataField, array_keys($children[$rule->getId()]));
            }
        }

        return $this;
    }

    /**
     * Add product tax classes to result
     *
     * @return $this
     */
    public function addProductTaxClassesToResult()
    {
        return $this->_add('tax_class', 'product_tax_class_id', 'class_id', 'class_name', 'product_tax_classes');
    }

    /**
     * Add customer tax classes to result
     *
     * @return $this
     */
    public function addCustomerTaxClassesToResult()
    {
        return $this->_add('tax_class', 'customer_tax_class_id', 'class_id', 'class_name', 'customer_tax_classes');
    }

    /**
     * Add rates to result
     *
     * @return $this
     */
    public function addRatesToResult()
    {
        return $this->_add('tax_calculation_rate', 'tax_calculation_rate_id', 'tax_calculation_rate_id', 'code', 'tax_rates');
    }

    /**
     * Add class type filter
     *
     * @param string $type
     * @param int $id
     * @throws Mage_Core_Exception
     * @return $this
     */
    public function setClassTypeFilter($type, $id)
    {
        switch ($type) {
            case Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT:
                $field = 'cd.product_tax_class_id';
                break;
            case Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER:
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
