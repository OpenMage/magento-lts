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
 * @package     Mage_GoogleCheckout
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_GoogleCheckout_Model_Mysql4_Tax extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('tax/tax_rule', 'rule_id');
    }

    public function fetchRuleRatesForCustomerTaxClass($customerTaxClass)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from(array('rule'=>$this->getTable('tax/tax_rule')))
            ->join(array('rd'=>$this->getTable('tax/tax_rate_data')), "rd.rate_type_id=rule.tax_rate_type_id", array('value'=>new Zend_Db_Expr('rate_value/100')))
            ->join(array('r'=>$this->getTable('tax/tax_rate')), "r.tax_rate_id=rd.tax_rate_id", array('country'=>'tax_country_id', 'postcode'=>'tax_postcode'))
            ->joinLeft(array('reg'=>$this->getTable('directory/country_region')), "reg.region_id=r.tax_region_id", array('state'=>'code'))
            ->where('rule.tax_customer_class_id=?', $customerTaxClass);
        $rows = $read->fetchAll($select);

        return $rows;
    }
}
