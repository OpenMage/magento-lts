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
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax Setup Model
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tax_Model_Mysql4_Setup extends Mage_Core_Model_Resource_Setup
{
    public function convertOldTaxData()
    {
        $oldRules = $this->_loadTableData('tax_rule');

        $oldRateTypes = $this->_loadTableData('tax_rate_type');

        $rateById = array();
        foreach ($oldRateTypes as $type) {
            $rateById[$type['type_id']] = $type['type_name'];
        }

        $oldRates = $this->_loadOldRates($oldRateTypes);

        $oldToNewRateIds = array();

        foreach ($oldRates as $rate) {
            foreach ($oldRateTypes as $type) {
                if (isset($rate["data_{$type['type_id']}"])) {
                    $rateValue = $rate["data_{$type['type_id']}"];
                } else {
                    continue;
                }

                $region = Mage::getModel('directory/region')->load($rate['tax_region_id']);
                $regionName = $region->getCode() ? $region->getCode() : '*';
                $code = "{$rate['tax_country_id']}-{$regionName}-{$rate['tax_postcode']}-{$type['type_name']}";

                if ($rateValue > 0) {
                    $insertData = array(
                                        'tax_country_id'=>$rate['tax_country_id'],
                                        'tax_region_id'=>$rate['tax_region_id'],
                                        'tax_postcode'=>$rate['tax_postcode'],
                                        'code'=>$code,
                                        'rate'=>$rateValue,
                                        );

                    $newRateModel = Mage::getModel('tax/calculation_rate');

                    $newRateModel->setData($insertData)->save();
                    $oldToNewRateIds[$rate['tax_rate_id']] = $newRateModel->getId();
                    $ratesByType[$type['type_id']][] = $newRateModel->getId();
                }
            }
        }

        foreach ($oldRules as $rule) {
            if (!isset($ratesByType[$rule['tax_rate_type_id']]) || !count($ratesByType[$rule['tax_rate_type_id']])){
                continue;
            }

            $customerTaxClasses = array($rule['tax_customer_class_id']);
            $productTaxClasses = array($rule['tax_product_class_id']);

            $ctc = Mage::getModel('tax/class')->load($rule['tax_customer_class_id']);
            $ptc = Mage::getModel('tax/class')->load($rule['tax_product_class_id']);
            $type = $rateById[$rule['tax_rate_type_id']];

            $rates = $ratesByType[$rule['tax_rate_type_id']];
            $code = "{$ctc->getClassName()}-{$ptc->getClassName()}-{$type}";

            $ruleData = array(
                            'tax_rate'=>$rates,
                            'tax_product_class'=>$productTaxClasses,
                            'tax_customer_class'=>$customerTaxClasses,
                            'code'=>$code,
                            'priority'=>1,
                            'position'=>1
                            );
            Mage::getModel('tax/calculation_rule')->setData($ruleData)->save();
        }
    }

    protected function _loadTableData($table)
    {
        $table = $this->getTable($table);
        $select = $this->_conn->select();
        $select->from($table);
        return $this->_conn->fetchAll($select);
    }

    protected function _loadOldRates($oldRateTypes)
    {

        $table = $this->getTable('tax_rate');
        $select = $this->_conn->select();
        $select->from(array('main_table'=>$table));
        foreach ($oldRateTypes as $type){
            $id = $type['type_id'];
            $select->joinLeft(
                        array("data_{$id}"=>$this->getTable('tax_rate_data')),
                        "data_{$id}.rate_type_id = {$id} AND data_{$id}.tax_rate_id = main_table.tax_rate_id",
                        array("data_{$id}"=>'rate_value')
                        );
        }
        return $this->_conn->fetchAll($select);
    }
}