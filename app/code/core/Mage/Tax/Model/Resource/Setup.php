<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 */

/**
 * Tax Setup Resource Model
 *
 * @package    Mage_Tax
 */
class Mage_Tax_Model_Resource_Setup extends Mage_Sales_Model_Resource_Setup
{
    /**
     * Convert old Tax data
     * @deprecated since 1.5.0.0
     */
    public function convertOldTaxData()
    {
        $oldRules = $this->_loadTableData('tax_rule');

        $oldRateTypes = $this->_loadTableData('tax_rate_type');

        $rateById = [];
        foreach ($oldRateTypes as $type) {
            $rateById[$type['type_id']] = $type['type_name'];
        }

        $oldRates = $this->_loadOldRates($oldRateTypes);

        $oldToNewRateIds = [];

        foreach ($oldRates as $rate) {
            foreach ($oldRateTypes as $type) {
                $rateIndex = sprintf('data_%s', $type['type_id']);
                if (isset($rate[$rateIndex])) {
                    $rateValue = $rate[$rateIndex];
                } else {
                    continue;
                }

                $region     = Mage::getModel('directory/region')->load($rate['tax_region_id']);
                $regionName = $region->getCode() ? $region->getCode() : '*';
                $code       = "{$rate['tax_country_id']}-{$regionName}-{$rate['tax_postcode']}-{$type['type_name']}";

                if ($rateValue > 0) {
                    $insertData = [
                        'tax_country_id'    => $rate['tax_country_id'],
                        'tax_region_id'     => $rate['tax_region_id'],
                        'tax_postcode'      => $rate['tax_postcode'],
                        'code'              => $code,
                        'rate'              => $rateValue,
                    ];

                    $newRateModel = Mage::getModel('tax/calculation_rate');

                    $newRateModel->setData($insertData)->save();
                    $oldToNewRateIds[$rate['tax_rate_id']] = $newRateModel->getId();
                    $ratesByType[$type['type_id']][] = $newRateModel->getId();
                }
            }
        }

        foreach ($oldRules as $rule) {
            if (!isset($ratesByType[$rule['tax_rate_type_id']]) || $ratesByType[$rule['tax_rate_type_id']] === []) {
                continue;
            }

            $customerTaxClasses = [$rule['tax_customer_class_id']];
            $productTaxClasses = [$rule['tax_product_class_id']];

            $ctc    = Mage::getModel('tax/class')->load($rule['tax_customer_class_id']);
            $ptc    = Mage::getModel('tax/class')->load($rule['tax_product_class_id']);
            $type   = $rateById[$rule['tax_rate_type_id']];

            $rates  = $ratesByType[$rule['tax_rate_type_id']];
            $code   = "{$ctc->getClassName()}-{$ptc->getClassName()}-{$type}";

            $ruleData = [
                'tax_rate'              => $rates,
                'tax_product_class'     => $productTaxClasses,
                'tax_customer_class'    => $customerTaxClasses,
                'code'                  => $code,
                'priority'              => 1,
                'position'              => 1,
            ];
            Mage::getModel('tax/calculation_rule')->setData($ruleData)->save();
        }

        return $this;
    }

    /**
     * Load Tax Table Data
     *
     * @param  string $table
     * @return array
     */
    protected function _loadTableData($table)
    {
        $table = $this->getTable($table);
        $select = $this->_conn->select();
        $select->from($table);
        return $this->_conn->fetchAll($select);
    }

    /**
     * Load Old Rate Data
     *
     * @param  array $oldRateTypes
     * @return array
     * @deprecated since 1.5.0.0
     */
    protected function _loadOldRates($oldRateTypes)
    {
        $table  = $this->getTable('tax_rate');
        $select = $this->_conn->select()
            ->from(['main_table' => $table]);
        foreach ($oldRateTypes as $type) {
            $id = $type['type_id'];
            $select->joinLeft(
                ["data_{$id}" => $this->getTable('tax_rate_data')],
                "data_{$id}.rate_type_id = {$id} AND data_{$id}.tax_rate_id = main_table.tax_rate_id",
                ["data_{$id}" => 'rate_value'],
            );
        }

        return $this->_conn->fetchAll($select);
    }
}
