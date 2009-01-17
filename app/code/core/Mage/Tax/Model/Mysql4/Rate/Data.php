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


class Mage_Tax_Model_Mysql4_Rate_Data extends Mage_Core_Model_Mysql4_Abstract
{

    protected function _construct()
    {
        $this->_init('tax/tax_rate_data', 'tax_rate_data_id');
    }

    public function fetchRate(Mage_Tax_Model_Rate_Data $request)
    {
        $bind = array(
            'country_id'    => $request->getCountryId(),
            'region_id'     => $request->getRegionId(),
            'tax_postcode'  => $request->getPostcode()
        );

        $select = $this->_getReadAdapter()->select()
            ->from(array('data'=>$this->getMainTable()), array('data.tax_rate_id'))
            ->join(array('rule'=>$this->getTable('tax_rule')), 'rule.tax_rate_type_id=data.rate_type_id', array())
            ->where('rule.tax_customer_class_id = ?', $request->getCustomerClassId())
            ->where('rule.tax_product_class_id = ?', $request->getProductClassId());


        $rate = clone $select;
        $rate
            ->join(array('rate'=>$this->getTable('tax_rate')), 'rate.tax_rate_id=data.tax_rate_id', array())
            ->where('rate.tax_country_id=:country_id')
            ->where('rate.tax_region_id is null or rate.tax_region_id=0 or rate.tax_region_id=:region_id')
            ->where("rate.tax_postcode is null or rate.tax_postcode in ('','*') or rate.tax_postcode=:tax_postcode")

            ->order('tax_region_id desc')->order('tax_postcode desc');

        $rateId = $this->_getReadAdapter()->fetchOne($rate, $bind);
        if (!$rateId)
            return 0;


        $priority = clone $select;
        $priority
            ->reset(Zend_Db_Select::COLUMNS)
            ->from(null, array('rule.tax_rate_type_id', 'rule.priority'))
            ->where('data.tax_rate_id = ?', $rateId)
            ->order('rule.priority');

        $priorities = $this->_getReadAdapter()->fetchAll($priority, $bind);

        $values = $this->_getReadAdapter()->select();
        $values->from(array('data'=>$this->getMainTable()), array('value'=>'data.rate_value', 'data.rate_type_id'));
        $values->where('data.tax_rate_id = ?', $rateId);
        $rows = $this->_getReadAdapter()->fetchAll($values, $bind);

        $currentRate = $rate = 0;
        if ($rows && $priorities) {
            for ($i=0; $i<count($priorities); $i++) {
                $priority = $priorities[$i];

                foreach ($rows as $row) {
                    if ($row['rate_type_id'] == $priority['tax_rate_type_id']) {
                        $row['value'] = $row['value']/100;
                        $currentRate += $row['value'];

                        if (!isset($priorities[$i+1]) || $priorities[$i+1]['priority'] != $priority['priority']) {
                            $rate += (100+$rate)*$currentRate;
                            $currentRate = 0;
                        }
                    }
                }
            }
        }

        return $rate;
    }
}