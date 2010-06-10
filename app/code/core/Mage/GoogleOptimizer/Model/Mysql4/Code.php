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
 * @package     Mage_GoogleOptimizer
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Google Optimizer resource model
 *
 * @category   Mage
 * @package    Mage_GoogleOptimizer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleOptimizer_Model_Mysql4_Code extends Mage_Core_Model_Mysql4_Abstract
{
    protected function  _construct()
    {
        $this->_init('googleoptimizer/code', 'code_id');
    }

    /**
     * Load scripts by entity and store
     *
     * @param Mage_GoogleOptimizer_Model_Code $object
     * @param integer $storeId
     * @return Mage_GoogleOptimizer_Model_Mysql4_Code
     */
    public function loadbyEntityType($object, $storeId)
    {
        $read = $this->_getReadAdapter();
        if ($read) {
            //preapre colums to fetch, except scope columns
            $_columns = array_keys($read->describeTable($this->getMainTable()));
            $columnsToFetch = array();
            foreach ($_columns as $_column) {
                if (in_array($_column, array('entity_id', 'entity_type'))) {
                    $columnsToFetch[] = $_column;
                }
            }
            $select = $read->select()
                ->from(array('_default_table' => $this->getMainTable()), $columnsToFetch)
                ->joinLeft(array('_store_table' => $this->getMainTable()),
                    "_store_table.entity_id = _default_table.entity_id AND _store_table.entity_type = _default_table.entity_type AND _store_table.store_id = {$storeId}",
                    array('code_id' => new Zend_Db_Expr("IFNULL(_store_table.code_id, _default_table.code_id)"),
                        'store_id' => new Zend_Db_Expr("IFNULL(_store_table.store_id, _default_table.store_id)"),
                        'control_script' => new Zend_Db_Expr("IFNULL(_store_table.control_script, _default_table.control_script)"),
                        'tracking_script' => new Zend_Db_Expr("IFNULL(_store_table.tracking_script, _default_table.tracking_script)"),
                        'conversion_script' => new Zend_Db_Expr("IFNULL(_store_table.conversion_script, _default_table.conversion_script)"),
                        'conversion_page' => new Zend_Db_Expr("IFNULL(_store_table.conversion_page, _default_table.conversion_page)"),
                        'additional_data' => new Zend_Db_Expr("IFNULL(_store_table.additional_data, _default_table.additional_data)")))
                ->where('_default_table.entity_id=?', $object->getEntity()->getId())
                ->where('_default_table.entity_type=?', $object->getEntityType())
                ->where('_default_table.store_id IN (0, ?)', $storeId)
                ->order('_default_table.store_id DESC')
                ->limit(1);
            $data = $read->fetchRow($select);
            if ($data) {
                $object->setData($data);
            }
        }
        $this->_afterLoad($object);
        return $this;
    }

    /**
     * Delete scripts by entity and store
     *
     * @param Mage_GoogleOptimizer_Model_Code $object
     * @param integer $store_id
     * @return Mage_GoogleOptimizer_Model_Mysql4_Code
     */
    public function deleteByEntityType($object, $store_id)
    {
        $write = $this->_getWriteAdapter();
        if ($write) {
            $entityIds = $object->getEntityIds();
            if (!empty($entityIds)) {
                $where = $write->quoteInto($this->getMainTable().'.entity_id IN (?)', $entityIds);
            } else {
                $where = $write->quoteInto($this->getMainTable().'.entity_id=?', $object->getEntity()->getId());
            }
            $where.= ' AND ' . $write->quoteInto($this->getMainTable().'.entity_type=?', $object->getEntityType()) .
                ' AND ' . $write->quoteInto($this->getMainTable().'.store_id=?', $store_id);
            $write->delete($this->getMainTable(), $where);
        }

        $this->_afterDelete($object);
        return $this;
    }
}
