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
 * @package    Mage_Admin
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Core_Model_Mysql4_Design extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('core/design_change', 'design_change_id');
    }

    public function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        if ($date = $object->getDateFrom()) {
            $date = Mage::app()->getLocale()->date($date, $format, null, false);
            $object->setDateFrom($date->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
        } else {
            $object->setDateFrom(null);
        }

        if ($date = $object->getDateTo()) {
            $date = Mage::app()->getLocale()->date($date, $format, null, false);
            $object->setDateTo($date->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
        } else {
            $object->setDateTo(null);
        }

        if (!is_null($object->getDateFrom()) && !is_null($object->getDateTo()) && strtotime($object->getDateFrom()) > strtotime($object->getDateTo())){
            Mage::throwException(Mage::helper('core')->__('Start date can\'t be greater than end date'));
        }

        $check = $this->_checkIntersection(
            $object->getStoreId(),
            $object->getDateFrom(),
            $object->getDateTo(),
            $object->getId()
        );

        if ($check){
            Mage::throwException(Mage::helper('core')->__('Your design change for the specified store intersects with another one, please specify another date range'));
        }

        if (is_null($object->getDateFrom()))
            $object->setDateFrom(new Zend_Db_Expr('null'));
        if (is_null($object->getDateTo()))
            $object->setDateTo(new Zend_Db_Expr('null'));

        parent::_beforeSave($object);
    }

    private function _checkIntersection($storeId, $dateFrom, $dateTo, $currentId)
    {
        $condition = '(date_to is null AND date_from is null)';
        if (!is_null($dateFrom)) {
            $condition .= '
                 OR
                (? between date_from and date_to)
                 OR
                (? >= date_from and date_to is null)
                 OR
                (? <= date_to and date_from is null)
                ';
        } else {
            $condition .= '
                 OR
                (date_from is null)
                ';
        }

        if (!is_null($dateTo)) {
            $condition .= '
                 OR
                (# between date_from and date_to)
                 OR
                (# >= date_from and date_to is null)
                 OR
                (# <= date_to and date_from is null)
                ';
        } else {
            $condition .= '
                 OR
                (date_to is null)
                ';
        }

        if (is_null($dateFrom) && !is_null($dateTo)) {
            $condition .= '
                 OR
                (date_to <= # or date_from <= #)
                ';
        }
        if (!is_null($dateFrom) && is_null($dateTo)) {
            $condition .= '
                 OR
                (date_to >= ? or date_from >= ?)
                ';
        }

        if (!is_null($dateFrom) && !is_null($dateTo)) {
            $condition .= '
                 OR
                (date_from between ? and #)
                 OR
                (date_to between ? and #)
                ';
        } else if (is_null($dateFrom) && is_null($dateTo)) {
            $condition = false;
        }

        $select = $this->_getReadAdapter()->select()
            ->from(array('main_table'=>$this->getTable('design_change')))
            ->where('main_table.store_id = ?', $storeId)
            ->where('main_table.design_change_id <> ?', $currentId);

        if ($condition) {
            $condition = $this->_getReadAdapter()->quoteInto($condition, $dateFrom);
            $condition = str_replace('#', '?', $condition);
            $condition = $this->_getReadAdapter()->quoteInto($condition, $dateTo);

            $select->where($condition);
        }

        return $this->_getReadAdapter()->fetchOne($select);
    }

    public function loadChange($storeId, $date = null)
    {
        if (is_null($date)) {
            //$date = new Zend_Db_Expr('NOW()');
            $date = now();
        }

        $select = $this->_getReadAdapter()->select()
            ->from(array('main_table'=>$this->getTable('design_change')))
            ->where('store_id = ?', $storeId)
            ->where('(date_from <= ? or date_from is null)', $date)
            ->where('(date_to >= ? or date_to is null)', $date);

        return $this->_getReadAdapter()->fetchRow($select);
    }
}
