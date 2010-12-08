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
 * @package     Mage_CatalogSearch
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_CatalogSearch_Model_Mysql4_Advanced_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{
    /**
     * Add not indexable fields to search
     *
     * @param array $fields
     * @return Mage_CatalogSearch_Model_Mysql4_Advanced_Collection
     */
    public function addFieldsToFilter($fields)
    {
        if ($fields) {
            $previousSelect = null;
            foreach ($fields as $table => $conditions) {
                foreach ($conditions as $attributeId => $conditionValue) {
                    $bindVarName = 'attribute_'.$attributeId;
                    $select = $this->getConnection()->select();
                    $select->from(array('t1' => $table), 'entity_id');
                    $conditionData = array();

                    if (is_array($conditionValue)) {
                        if (isset($conditionValue['in'])){
                            $conditionData[] = array('IN (?)', $conditionValue['in']);
                        }
                        elseif (isset($conditionValue['in_set'])) {
                            $conditionData[] = array('REGEXP \'(^|,)('.join('|', $conditionValue['in_set']).')(,|$)\'', $conditionValue['in_set']);
                        }
                        elseif (isset($conditionValue['like'])) {
                            $this->addBindParam($bindVarName, $conditionValue['like']);
                            $conditionData[] = 'LIKE :'.$bindVarName;
                        }
                        elseif (isset($conditionValue['from']) && isset($conditionValue['to'])) {
                            if ($conditionValue['from']) {
                                if (!is_numeric($conditionValue['from'])){
                                    $conditionValue['from'] = date("Y-m-d H:i:s", strtotime($conditionValue['from']));
                                }
                                $conditionData[] = array('>= ?', $conditionValue['from']);
                            }
                            if ($conditionValue['to']) {
                                if (!is_numeric($conditionValue['to'])){
                                    $conditionValue['to'] = date("Y-m-d H:i:s", strtotime($conditionValue['to']));
                                }
                                $conditionData[] = array('<= ?', $conditionValue['to']);
                            }
                        }
                    } else {
                        $conditionData[] = array('= ?', $conditionValue);
                    }

                    if (!is_numeric($attributeId)) {
                        foreach ($conditionData as $data) {
                            if (is_array($data)) {
                                $select->where('t1.'.$attributeId . ' ' . $data[0], $data[1]);
                            }
                            else {
                                $select->where('t1.'.$attributeId . ' ' . $data);
                            }
                        }
                    }
                    else {
                        $storeId = $this->getStoreId();
                        $select->joinLeft(
                            array('t2' => $table),
                            $this->getConnection()->quoteInto('t1.entity_id = t2.entity_id AND t1.attribute_id = t2.attribute_id AND t2.store_id=?', $storeId),
                            array()
                        );
                        $select->where('t1.store_id = ?', 0);
                        $select->where('t1.attribute_id = ?', $attributeId);

                        foreach ($conditionData as $data) {
                            if (is_array($data)) {
                                $select->where('IF(t2.value_id>0, t2.value, t1.value) ' . $data[0], $data[1]);
                            }
                            else {
                                $select->where('IF(t2.value_id>0, t2.value, t1.value) ' . $data);
                            }
                        }
                    }

                    if (!is_null($previousSelect)) {
                        $select->where('t1.entity_id IN(?)', new Zend_Db_Expr($previousSelect));
                    }
                    $previousSelect = $select;
                }
            }

            $this->addFieldToFilter('entity_id', array('in' => new Zend_Db_Expr($select)));
        }

        return $this;
    }
}
