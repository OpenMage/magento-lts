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
 * @package    Mage_CatalogIndex
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Reindexer resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogIndex_Model_Mysql4_Indexer extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_insertData = array();
    protected $_tableFields = array();
    protected $_attributeCache = array();

    protected function _construct()
    {
        $this->_init('catalog/product', 'entity_id');
    }

    protected function _loadAttribute($id)
    {
        if (!isset($this->_attributeCache[$id])) {
            $this->_attributeCache[$id] = Mage::getModel('eav/entity_attribute')->load($id);
        }

        return $this->_attributeCache[$id];
    }

    public function clear($eav = true, $price = true, $minimal = true, $finalPrice = true, $tierPrice = true, $products = null, $store = null)
    {
        $suffix = '';
        $tables = array('eav'=>'catalogindex/eav', 'price'=>'catalogindex/price');
        if (!is_null($products)) {
            if ($products instanceof Mage_Catalog_Model_Product) {
                $products = $products->getId();
            } else if (!is_numeric($products) && !is_array($products)) {
                Mage::throwException('Invalid products supplied for indexing');
            }
            $suffix = $this->_getWriteAdapter()->quoteInto('entity_id in (?)', $products);
        }
        if (!is_null($store)) {
            if ($store instanceof Mage_Core_Model_Store) {
                $store = $store->getId();
            } else if ($store instanceof Mage_Core_Model_Mysql4_Store_Collection) {
                $store = $store->getAllIds();
            } else if (is_array($store)) {
                $resultStores = array();
                foreach ($store as $s) {
                    if ($s instanceof Mage_Core_Model_Store) {
                        $resultStores[] = $s->getId();
                    } elseif (is_numeric($s)) {
                        $resultStores[] = $s;
                    }
                }
                $store = $resultStores;
            }


            if ($suffix) {
                $suffix .= ' AND ';
            }
            $suffix .= $this->_getWriteAdapter()->quoteInto('store_id in (?)', $store);

        }

        if ($tierPrice) {
            $tables['tierPrice'] = 'catalogindex/price';
            $tierPrice = array(Mage::getSingleton('eav/entity_attribute')->getIdByCode('catalog_product', 'tier_price'));
        }
        if ($finalPrice) {
            $tables['finalPrice'] = 'catalogindex/price';
            $tierPrice = array(Mage::getSingleton('eav/entity_attribute')->getIdByCode('catalog_product', 'price'));
        }
        if ($minimal) {
            $tables['minimal'] = 'catalogindex/minimal_price';
        }


        foreach ($tables as $variable=>$table) {
            $variable = $$variable;

            if ($variable === true) {
                $query = "DELETE FROM {$this->getTable($table)} ";
                if ($suffix) {
                    $query .= "WHERE {$suffix}";
                }

                $this->_getWriteAdapter()->query($query);
            } else if (is_array($variable) && count($variable)) {
                $query  = "DELETE FROM {$this->getTable($table)} WHERE ";
                $query .= $this->_getWriteAdapter()->quoteInto("attribute_id in (?)", $variable);
                if ($suffix) {
                    $query .= " AND {$suffix}";
                }

                $this->_getWriteAdapter()->query($query);
            }
        }
    }

    public function reindexTiers($products, $store, $forcedId = null)
    {
        $attribute = Mage::getSingleton('eav/entity_attribute')->getIdByCode('catalog_product', 'tier_price');
        $this->_beginInsert('catalogindex/price', array('entity_id', 'attribute_id', 'value', 'store_id', 'customer_group_id', 'qty'));

        $products = Mage::getSingleton('catalogindex/retreiver')->assignProductTypes($products);
        if (is_null($forcedId)) {
            foreach ($products as $type=>$typeIds) {
                $retreiver = Mage::getSingleton('catalogindex/retreiver')->getRetreiver($type);
                if ($retreiver->areChildrenIndexable(Mage_CatalogIndex_Model_Retreiver::CHILDREN_FOR_TIERS)) {
                    foreach ($typeIds as $id) {
                        $children = $retreiver->getChildProductIds($store, $id);
                        if ($children) {
                            $this->reindexTiers($children, $store, $id);
                        }
                    }
                }
            }
        }
        $attributeIndex = $this->getTierData($products, $store);
        foreach ($attributeIndex as $index) {
            $type = $index['type_id'];
            $id = (is_null($forcedId) ? $index['entity_id'] : $forcedId);
            if ($id && $index['value']) {
                if ($index['all_groups'] == 1) {
                    foreach (Mage::getModel('catalogindex/retreiver')->getCustomerGroups() as $group) {
                        $this->_insert('catalogindex/price', array($id, $attribute, $index['value'], $store->getId(), (int) $group->getId(), (int) $index['qty']));
                    }
                } else {
                    $this->_insert('catalogindex/price', array($id, $attribute, $index['value'], $store->getId(), (int) $index['customer_group_id'], (int) $index['qty']));
                }
            }
        }
        $this->_commitInsert('catalogindex/price');
    }

    public function reindexPrices($products, $attributeIds, $store)
    {
        $this->reindexAttributes($products, $attributeIds, $store, null, 'catalogindex/price');
    }

    public function reindexFinalPrices($products, $store, $forcedId = null)
    {
        $priceAttribute = Mage::getSingleton('eav/entity_attribute')->getIdByCode('catalog_product', 'price');
        $this->_beginInsert('catalogindex/price', array('entity_id', 'store_id', 'customer_group_id', 'value', 'attribute_id', 'tax_class_id'));

        $productTypes = Mage::getSingleton('catalogindex/retreiver')->assignProductTypes($products);
        foreach ($productTypes as $type=>$products) {
            $retreiver = Mage::getSingleton('catalogindex/retreiver')->getRetreiver($type);
            foreach ($products as $product) {
                if (is_null($forcedId)) {
                    if ($retreiver->areChildrenIndexable(Mage_CatalogIndex_Model_Retreiver::CHILDREN_FOR_PRICES)) {
                        $children = $retreiver->getChildProductIds($store, $product);
                        if ($children) {
                            $this->reindexFinalPrices($children, $store, $product);
                        }
                    }
                }
                foreach (Mage::getModel('catalogindex/retreiver')->getCustomerGroups() as $group) {
                    $finalPrice = $retreiver->getFinalPrice($product, $store, $group);
                    $taxClassId = $retreiver->getTaxClassId($product, $store);
                    $id = $product;
                    if (!is_null($forcedId))
                        $id = $forcedId;

                    if (false !== $finalPrice && false !== $id && false !== $store->getId() && false !== $group->getId() && false !== $priceAttribute) {
                        $this->_insert('catalogindex/price', array($id, $store->getId(), $group->getId(), $finalPrice, $priceAttribute, $taxClassId));
                    }
                }
            }
        }
        $this->_commitInsert('catalogindex/price');
    }

    public function reindexMinimalPrices($products, $store)
    {
        $this->_beginInsert('catalogindex/minimal_price', array('store_id', 'entity_id', 'customer_group_id', 'value', 'tax_class_id'));
        $this->clear(false, false, true, false, false, $products, $store);
        $products = Mage::getSingleton('catalogindex/retreiver')->assignProductTypes($products);

        foreach ($products as $type=>$typeIds) {
            $retreiver = Mage::getSingleton('catalogindex/retreiver')->getRetreiver($type);

            foreach ($typeIds as $id) {
                $minimal = array();
                if ($retreiver->areChildrenIndexable(Mage_CatalogIndex_Model_Retreiver::CHILDREN_FOR_PRICES)) {
                    $children = $retreiver->getChildProductIds($store, $id);
                    if ($children) {
                        $minimal = $this->getMinimalPrice(array($type=>$children), $store);
                    }
                } else {
                    $minimal = $this->getMinimalPrice(array($type=>array($id)), $store);
                }

                if (is_array($minimal)) {
                    foreach ($minimal as $price) {
                        if (!isset($price['tax_class_id'])) {
                            $price['tax_class_id'] = 0;
                        }
                        $this->_insert('catalogindex/minimal_price', array($store->getId(), $id, $price['customer_group_id'], $price['minimal_value'], $price['tax_class_id']));
                    }
                }
            }
        }

        $this->_commitInsert('catalogindex/minimal_price');
    }

    public function reindexAttributes($products, $attributeIds, $store, $forcedId = null, $table = 'catalogindex/eav')
    {
        $this->_beginInsert($table, array('entity_id', 'attribute_id', 'value', 'store_id'));

        $products = Mage::getSingleton('catalogindex/retreiver')->assignProductTypes($products);

        if (is_null($forcedId)) {
            foreach ($products as $type=>$typeIds) {
                $retreiver = Mage::getSingleton('catalogindex/retreiver')->getRetreiver($type);
                if ($retreiver->areChildrenIndexable(Mage_CatalogIndex_Model_Retreiver::CHILDREN_FOR_ATTRIBUTES)) {
                    foreach ($typeIds as $id) {
                        $children = $retreiver->getChildProductIds($store, $id);
                        if ($children) {
                            $this->reindexAttributes($children, $attributeIds, $store, $id, $table);
                        }
                    }
                }
            }
        }

        $attributeIndex = $this->getProductData($products, $attributeIds, $store);
        foreach ($attributeIndex as $index) {
            $type = $index['type_id'];
            $id = (is_null($forcedId) ? $index['entity_id'] : $forcedId);

            if ($id && $index['attribute_id'] && $index['value']) {
                $attribute = $this->_loadAttribute($index['attribute_id']);
                if ($attribute->getFrontendInput() == 'multiselect') {
                    $index['value'] = explode(',', $index['value']);
                }

                if (is_array($index['value'])) {
                    foreach ($index['value'] as $value) {
                        $this->_insert($table, array($id, $index['attribute_id'], $value, $store->getId()));
                    }
                } else {
                    $this->_insert($table, array($id, $index['attribute_id'], $index['value'], $store->getId()));
                }
            }
        }

        $this->_commitInsert($table);
    }

    public function getTierData($products, $store){
        $result = array();
        foreach ($products as $type=>$typeIds) {
            $retreiver = Mage::getSingleton('catalogindex/retreiver')->getRetreiver($type);
            $byType = $retreiver->getTierPrices($typeIds, $store);
            if ($byType) {
                $result = array_merge($result, $byType);
            }
        }
        return $result;
    }

    public function getMinimalPrice($products, $store)
    {
        $result = array();
        foreach ($products as $type=>$typeIds) {
            $retreiver = Mage::getSingleton('catalogindex/retreiver')->getRetreiver($type);
            $byType = $retreiver->getMinimalPrice($typeIds, $store);
            if ($byType) {
                $result = array_merge($result, $byType);
            }
        }
        return $result;
    }

    public function getProductData($products, $attributeIds, $store){
        $result = array();
        foreach ($products as $type=>$typeIds) {
            $retreiver = Mage::getSingleton('catalogindex/retreiver')->getRetreiver($type);
            $byType = $retreiver->getAttributeData($typeIds, $attributeIds, $store);
            if ($byType) {
                $result = array_merge($result, $byType);
            }
        }
        return $result;
    }

    protected function _beginInsert($table, $fields){
        $this->_tableFields[$table] = $fields;
    }

    protected function _commitInsert($table, $forced = true){
        if (isset($this->_insertData[$table]) && count($this->_insertData[$table]) && ($forced || count($this->_insertData[$table]) >= 100)) {
            $query = 'REPLACE INTO ' . $this->getTable($table) . ' (' . implode(', ', $this->_tableFields[$table]) . ') VALUES ';
            $separator = '';
            foreach ($this->_insertData[$table] as $row) {
                $rowString = $this->_getWriteAdapter()->quoteInto('(?)', $row);
                $query .= $separator . $rowString;
                $separator = ', ';
            }
            $this->_getWriteAdapter()->query($query);
            $this->_insertData[$table] = array();
        }
    }

    protected function _insert($table, $data) {
        $this->_insertData[$table][] = $data;
        $this->_commitInsert($table, false);
    }
}