<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogSearch
 */

/**
 * Collection Advanced
 *
 * @package    Mage_CatalogSearch
 */
class Mage_CatalogSearch_Model_Resource_Advanced_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{
    /**
     * Add not indexable fields to search
     *
     * @param array $fields
     * @return $this
     */
    public function addFieldsToFilter($fields)
    {
        if ($fields) {
            $previousSelect = null;
            $conn = $this->getConnection();
            foreach ($fields as $table => $conditions) {
                foreach ($conditions as $attributeId => $conditionValue) {
                    $select = $conn->select();
                    $select->from(['t1' => $table], 'entity_id');
                    $conditionData = [];

                    if (!is_numeric($attributeId)) {
                        $field = 't1.' . $attributeId;
                    } else {
                        $storeId = $this->getStoreId();
                        $onCondition = 't1.entity_id = t2.entity_id'
                                . ' AND t1.attribute_id = t2.attribute_id'
                                . ' AND t2.store_id=?';

                        $select->joinLeft(
                            ['t2' => $table],
                            $conn->quoteInto($onCondition, $storeId),
                            [],
                        );
                        $select->where('t1.store_id = ?', 0);
                        $select->where('t1.attribute_id = ?', $attributeId);

                        if (array_key_exists('price_index', $this->getSelect()->getPart(Varien_Db_Select::FROM))) {
                            $select->where('t1.entity_id = price_index.entity_id');
                        }

                        $field = $this->getConnection()->getIfNullSql('t2.value', 't1.value');
                    }

                    if (is_array($conditionValue)) {
                        if (isset($conditionValue['in'])) {
                            $conditionData[] = ['in' => $conditionValue['in']];
                        } elseif (isset($conditionValue['in_set'])) {
                            $conditionParts = [];
                            foreach ($conditionValue['in_set'] as $value) {
                                $conditionParts[] = ['finset' => $value];
                            }

                            $conditionData[] = $conditionParts;
                        } elseif (isset($conditionValue['like'])) {
                            $conditionData[] = ['like' => $conditionValue['like']];
                        } elseif (isset($conditionValue['from']) && isset($conditionValue['to'])) {
                            $invalidDateMessage = Mage::helper('catalogsearch')->__('Specified date is invalid.');
                            if ($conditionValue['from']) {
                                if (!Zend_Date::isDate($conditionValue['from'])) {
                                    Mage::throwException($invalidDateMessage);
                                }

                                if (!is_numeric($conditionValue['from'])) {
                                    $conditionValue['from'] = Mage::getSingleton('core/date')
                                        ->gmtDate(null, $conditionValue['from']);
                                    if (!$conditionValue['from']) {
                                        $conditionValue['from'] = Mage::getSingleton('core/date')->gmtDate();
                                    }
                                }

                                $conditionData[] = ['gteq' => $conditionValue['from']];
                            }

                            if ($conditionValue['to']) {
                                if (!Zend_Date::isDate($conditionValue['to'])) {
                                    Mage::throwException($invalidDateMessage);
                                }

                                if (!is_numeric($conditionValue['to'])) {
                                    $conditionValue['to'] = Mage::getSingleton('core/date')
                                        ->gmtDate(null, $conditionValue['to']);
                                    if (!$conditionValue['to']) {
                                        $conditionValue['to'] = Mage::getSingleton('core/date')->gmtDate();
                                    }
                                }

                                $conditionData[] = ['lteq' => $conditionValue['to']];
                            }
                        }
                    } else {
                        $conditionData[] = ['eq' => $conditionValue];
                    }

                    foreach ($conditionData as $data) {
                        $select->where($conn->prepareSqlCondition($field, $data));
                    }

                    if (!is_null($previousSelect)) {
                        $select->where('t1.entity_id IN (?)', new Zend_Db_Expr($previousSelect));
                    }

                    $previousSelect = $select;
                }
            }

            if (isset($select)) {
                $this->addFieldToFilter('entity_id', ['in' => new Zend_Db_Expr($select)]);
            }
        }

        return $this;
    }
}
