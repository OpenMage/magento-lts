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
 * Tax rate resource
 *
 * @category   Mage
 * @package    Mage_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tax_Model_Mysql4_Rate extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('tax/tax_rate', 'tax_rate_id');
    }

    /**
     * Initialize unique fields
     *
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(array(
            'field' => array('tax_country_id', 'tax_region_id', 'tax_postcode'),
            'title' => Mage::helper('tax')->__('Country/Region/Postal code combination'),
        ));
        return $this;
    }

//    public function loadWithAttributes($rateId = 0)
//    {
//        $select = Mage::getModel('tax/rate')->getCollection()
//            ->joinTypeData()
//            ->joinRegionTable();
//        if (is_int($rateId) && $rateId > 0) {
//            $select->addRateFilter($rateId);
//            return $this->_getReadAdapter()->fetchRow($select->getSelect());
//        } else {
//            return $this->_getReadAdapter()->fetchAll($select->getSelect());
//        }
//    }

//    protected function _beforeSave(Mage_Core_Model_Abstract $object)
//    {
//        $rateArray = array(
//            'tax_county_id' => $object->getTaxCountyId(),
//            'tax_region_id' => $object->getTaxRegionId(),
//        );
//        if ($object->getTaxPostcode()) {
//        	$rateArray['tax_postcode'] = $object->getTaxPostcode();
//        } else {
//            $rateArray['tax_postcode'] = new Zend_Db_Expr('NULL');
//        }
//
//        if (intval($object->getTaxRateId()) <= 0) {
//            $this->_getWriteAdapter()->insert($this->getMainTable(), $rateArray);
//            $rateId = $this->_getWriteAdapter()->lastInsertId();
//        }
//        else {
//            $rateId = $object->getTaxRateId();
//            $condition = $this->_getWriteAdapter()->quoteInto("{$this->getMainTable()}.tax_rate_id=?", $rateId);
//            $this->_getWriteAdapter()->update($this->getMainTable(), $rateArray, $condition);
//
//            $condition = $this->_getWriteAdapter()->quoteInto("{$this->getTable('tax/tax_rate_data')}.tax_rate_id=?", $rateId);
//            $this->_getWriteAdapter()->delete($this->getTable('tax/tax_rate_data'), $condition);
//        }
//
//        return $this;
//    }

//    protected function _afterSave(Mage_Core_Model_Abstract $object)
//    {
//        foreach ($object->getRateData() as $rateType => $rateValue) {
//            $rateValueArray = array(
//                'tax_rate_id' => $rateId,
//                'rate_value' => $rateValue,
//                'rate_type_id' => $rateType
//            );
//            $this->_getWriteAdapter()->insert($this->_rateDataTable, $rateValueArray);
//        }
//    }


//    /**
//     * resource tables
//     */
//    protected $_rateTable;
//
//    protected $_rateDataTable;
//
//    /**
//     * resources
//     */
//    protected $_write;
//
//    protected $_read;
//
//
//    public function __construct()
//    {
//        $this->_rateTable = Mage::getSingleton('core/resource')->getTableName('tax/tax_rate');
//        $this->_rateDataTable = Mage::getSingleton('core/resource')->getTableName('tax/tax_rate_data');
//
//        $this->_read = Mage::getSingleton('core/resource')->getConnection('tax_read');
//        $this->_write = Mage::getSingleton('core/resource')->getConnection('tax_write');
//    }
//
//    public function getIdFieldName()
//    {
//        return 'tax_rate_id';
//    }
//
//    public function load($model, $rateId)
//    {
//        $model->setData(array());
//    }
//

//
//    public function save($rateObject)
//    {
//        $rateArray = array(
//            'tax_county_id' => $rateObject->getTaxCountyId(),
//            'tax_region_id' => $rateObject->getTaxRegionId(),
//        );
//        if ($rateObject->getTaxPostcode()) {
//        	$rateArray['tax_postcode'] = $rateObject->getTaxPostcode();
//        } else {
//            $rateArray['tax_postcode'] = new Zend_Db_Expr('NULL');
//        }
//        if( intval($rateObject->getTaxRateId()) <= 0 ) {
//            $this->_write->insert($this->_rateTable, $rateArray);
//            $rateId = $this->_write->lastInsertId();
//
//        } else {
//            $rateId = $rateObject->getTaxRateId();
//            $condition = $this->_write->quoteInto("{$this->_rateTable}.tax_rate_id=?", $rateId);
//            $this->_write->update($this->_rateTable, $rateArray, $condition);
//
//            $condition = $this->_write->quoteInto("{$this->_rateDataTable}.tax_rate_id=?", $rateId);
//            $this->_write->delete($this->_rateDataTable, $condition);
//        }
//
//        foreach ($rateObject->getRateData() as $rateType => $rateValue) {
//            $rateValueArray = array(
//                'tax_rate_id' => $rateId,
//                'rate_value' => $rateValue,
//                'rate_type_id' => $rateType
//            );
//            $this->_write->insert($this->_rateDataTable, $rateValueArray);
//        }
//    }
//
//    public function delete($rateObject)
//    {
//        $condition = $this->_write->quoteInto("{$this->_rateTable}.tax_rate_id=?", $rateObject->getTaxRateId());
//        $this->_write->delete($this->_rateTable, $condition);
//    }
//
    public function deleteAllRates()
    {
    	$this->_getWriteAdapter()->delete($this->getMainTable());
    }
}