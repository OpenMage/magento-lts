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
 * @package     Mage_CatalogIndex
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

    /**
     * Delete index data by specific conditions
     *
     * @param   bool $eav clear eav index data flag
     * @param   bool $price clear price index data flag
     * @param   bool $minimal clear minimal price index data flag
     * @param   bool $finalPrice clear final price index data flag
     * @param   bool $tierPrice clear tier price index data flag
     * @param   mixed $products applicable products
     * @param   mixed $store applicable stores
     */
    public function clear($eav = true, $price = true, $minimal = true, $finalPrice = true, $tierPrice = true, $products = null, $store = null)
    {
        $suffix = '';
        $priceSuffix = '';
        $tables = array('eav'=>'catalogindex/eav', 'price'=>'catalogindex/price');
        if (!is_null($products)) {
            if ($products instanceof Mage_Catalog_Model_Product) {
                $products = $products->getId();
            } elseif ($products instanceof Mage_Catalog_Model_Product_Condition_Interface) {
                $suffix = 'entity_id IN ('.$products->getIdsSelect($this->_getWriteAdapter())->__toString().')';
            }
            else if (!is_numeric($products) && !is_array($products)) {
                Mage::throwException('Invalid products supplied for indexing');
            }
            if (empty($suffix)) {
                $suffix = $this->_getWriteAdapter()->quoteInto('entity_id in (?)', $products);
            }
        }
        if (!is_null($store)) {
            $websiteIds = array();

            if ($store instanceof Mage_Core_Model_Store) {
                $store = $store->getId();
                $websiteIds[] = Mage::app()->getStore($store)->getWebsiteId();
            } else if ($store instanceof Mage_Core_Model_Mysql4_Store_Collection) {
                $store = $store->getAllIds();
                foreach ($store as $one) {
                    $websiteIds[] = Mage::app()->getStore($one)->getWebsiteId();
                }
            } else if (is_array($store)) {
                $resultStores = array();
                foreach ($store as $s) {
                    if ($s instanceof Mage_Core_Model_Store) {
                        $resultStores[] = $s->getId();
                        $websiteIds[] = $s->getWebsiteId();
                    } elseif (is_numeric($s)) {
                        $websiteIds[] = Mage::app()->getStore($s)->getWebsiteId();
                        $resultStores[] = $s;
                    }
                }
                $store = $resultStores;
            }

            if ($suffix) {
                $suffix .= ' AND ';
            }

            $priceSuffix = $suffix . $this->_getWriteAdapter()->quoteInto('website_id in (?)', $websiteIds);
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
            $suffixToInsert = $suffix;
            if (in_array($table, $this->_getPriceTables())) {
                $suffixToInsert = $priceSuffix;
            }

            if ($variable === true) {
                $query = "DELETE FROM {$this->getTable($table)} ";
                if ($suffixToInsert) {
                    $query .= "WHERE {$suffixToInsert}";
                }

                $this->_getWriteAdapter()->query($query);
            } else if (is_array($variable) && count($variable)) {
                $query  = "DELETE FROM {$this->getTable($table)} WHERE ";
                $query .= $this->_getWriteAdapter()->quoteInto("attribute_id in (?)", $variable);
                if ($suffixToInsert) {
                    $query .= " AND {$suffixToInsert}";
                }

                $this->_getWriteAdapter()->query($query);
            }
        }
    }

    /**
     * Get tables which are used for index related with price
     *
     * @return array
     */
    protected function _getPriceTables()
    {
        return array('catalogindex/price', 'catalogindex/minimal_price');
    }

    /**
     * Reindex data for tier prices
     *
     * @param   array $products array of product ids
     * @param   Mage_Core_Model_Store $store
     * @param   int | null $forcedId identifier of "parent" product
     *
     * @return  Mage_CatalogIndex_Model_Mysql4_Indexer
     */
    public function reindexTiers($products, $store, $forcedId = null)
    {
        $websiteId = $store->getWebsiteId();
        $attribute = Mage::getSingleton('eav/entity_attribute')->getIdByCode('catalog_product', 'tier_price');
        $this->_beginInsert(
            'catalogindex/price',
            array('entity_id', 'attribute_id', 'value', 'website_id', 'customer_group_id', 'qty')
        );

        /**
         * Get information about product types
         * array (
         *      $productType => array()
         * )
         */
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
                    foreach (Mage::getSingleton('catalogindex/retreiver')->getCustomerGroups() as $group) {
                        $this->_insert('catalogindex/price', array(
                            $id,
                            $attribute,
                            $index['value'],
                            $websiteId,
                            (int) $group->getId(),
                            (int) $index['qty']
                        ));
                    }
                } else {
                    $this->_insert('catalogindex/price', array(
                        $id,
                        $attribute,
                        $index['value'],
                        $websiteId,
                        (int) $index['customer_group_id'],
                        (int) $index['qty']
                    ));
                }
            }
        }
        $this->_commitInsert('catalogindex/price');
        return $this;
    }

    /**
     * Reindex product prices
     *
     * @param   array | int $products product ids
     * @param   array $attributeIds
     * @param   Mage_Core_Model_Store $store
     * @return  Mage_CatalogIndex_Model_Mysql4_Indexer
     */
    public function reindexPrices($products, $attributeIds, $store)
    {
        $this->reindexAttributes($products, $attributeIds, $store, null, 'catalogindex/price', true);
        return $this;
    }

    /**
     * Reindex product final prices
     *
     * @param   array $products array of product ids
     * @param   Mage_Core_Model_Store $store
     * @param   int | null $forcedId identifier of "parent" product
     *
     * @return  Mage_CatalogIndex_Model_Mysql4_Indexer
     */
    public function reindexFinalPrices($products, $store, $forcedId = null)
    {
        $priceAttribute = Mage::getSingleton('eav/entity_attribute')->getIdByCode('catalog_product', 'price');
        $this->_beginInsert('catalogindex/price', array(
            'entity_id',
            'website_id',
            'customer_group_id',
            'value',
            'attribute_id',
            'tax_class_id'
        ));

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
                foreach (Mage::getSingleton('catalogindex/retreiver')->getCustomerGroups() as $group) {
                    $finalPrice = $retreiver->getFinalPrice($product, $store, $group);
                    $taxClassId = $retreiver->getTaxClassId($product, $store);
                    $id = $product;
                    if (!is_null($forcedId)) {
                        $id = $forcedId;
                    }

                    if (false !== $finalPrice && false !== $id && false !== $priceAttribute) {
                        $this->_insert('catalogindex/price', array(
                            $id,
                            $store->getWebsiteId(),
                            $group->getId(),
                            $finalPrice,
                            $priceAttribute,
                            $taxClassId
                        ));
                    }
                }
            }
        }
        $this->_commitInsert('catalogindex/price');
        return $this;
    }

    /**
     * Reindex product minimal prices
     *
     * @param   array $products array of product ids
     * @param   Mage_Core_Model_Store $store
     *
     * @return  Mage_CatalogIndex_Model_Mysql4_Indexer
     */
    public function reindexMinimalPrices($products, $store)
    {
        $this->_beginInsert('catalogindex/minimal_price', array(
            'website_id',
            'entity_id',
            'customer_group_id',
            'value',
            'tax_class_id'
        ));
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
                        $this->_insert('catalogindex/minimal_price', array(
                            $store->getWebsiteId(),
                            $id,
                            $price['customer_group_id'],
                            $price['minimal_value'],
                            $price['tax_class_id']
                        ));
                    }
                }
            }
        }

        $this->_commitInsert('catalogindex/minimal_price');
        return $this;
    }

    /**
     * Reindex attributes data
     *
     * @param   array $products
     * @param   array $attributeIds
     * @param   mixed $store
     * @param   int|null $forcedId
     * @param   string $table
     * @param   bool $storeIsWebsite
     * @return  Mage_CatalogIndex_Model_Mysql4_Indexer
     */
    public function reindexAttributes($products, $attributeIds, $store, $forcedId = null, $table = 'catalogindex/eav', $storeIsWebsite = false)
    {
        $storeField = 'store_id';
        $websiteId = null;
        if ($storeIsWebsite) {
            $storeField = 'website_id';
            if ($store instanceof Mage_Core_Model_Store) {
                $websiteId = $store->getWebsiteId();
            } else {
                $websiteId = Mage::app()->getStore($store)->getWebsiteId();
            }
        }

        $this->_beginInsert($table, array('entity_id', 'attribute_id', 'value', $storeField));

        $products = Mage::getSingleton('catalogindex/retreiver')->assignProductTypes($products);

        if (is_null($forcedId)) {
            foreach ($products as $type=>$typeIds) {
                $retreiver = Mage::getSingleton('catalogindex/retreiver')->getRetreiver($type);
                if ($retreiver->areChildrenIndexable(Mage_CatalogIndex_Model_Retreiver::CHILDREN_FOR_ATTRIBUTES)) {
                    foreach ($typeIds as $id) {
                        $children = $retreiver->getChildProductIds($store, $id);
                        if ($children) {
                            $this->reindexAttributes($children, $attributeIds, $store, $id, $table, $storeIsWebsite);
                        }
                    }
                }
            }
        }

        $attributeIndex = $this->getProductData($products, $attributeIds, $store);
        foreach ($attributeIndex as $index) {
            $type = $index['type_id'];
            $id = (is_null($forcedId) ? $index['entity_id'] : $forcedId);

            if ($id && $index['attribute_id'] && isset($index['value'])) {
                $attribute = $this->_loadAttribute($index['attribute_id']);
                if ($attribute->getFrontendInput() == 'multiselect') {
                    $index['value'] = explode(',', $index['value']);
                }

                if (is_array($index['value'])) {
                    foreach ($index['value'] as $value) {
                        $this->_insert($table, array(
                            $id,
                            $index['attribute_id'],
                            $value,
                            (is_null($websiteId) ? $store->getId() : $websiteId)
                        ));
                    }
                } else {
                    $this->_insert($table, array(
                        $id,
                        $index['attribute_id'],
                        $index['value'],
                        (is_null($websiteId) ? $store->getId() : $websiteId)
                    ));
                }
            }
        }

        $this->_commitInsert($table);
        return $this;
    }

    /**
     * Get tier prices data by set of products
     *
     * @param   array $products
     * @param   Mage_Core_Model_Store $store
     * @return  array
     */
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

    /**
     * Get minimal prices by set of the products
     *
     * @param   arary $products
     * @param   Mage_Core_Model_Store $store
     * @return  array
     */
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

    /**
     * Get data for products
     *
     * @param   array $products
     * @param   array $attributeIds
     * @param   Mage_Core_Model_Store $store
     * @return  array
     */
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

    /**
     * Prepare base information for data insert
     *
     * @param   string $table
     * @param   array $fields
     * @return  Mage_CatalogIndex_Model_Mysql4_Indexer
     */
    protected function _beginInsert($table, $fields){
        $this->_tableFields[$table] = $fields;
        return $this;
    }

    /**
     * Put data into table
     *
     * @param   string $table
     * @param   bool $forced
     * @return  Mage_CatalogIndex_Model_Mysql4_Indexer
     */
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
        return $this;
    }

    /**
     * Insert data to table
     *
     * @param   string $table
     * @param   array $data
     * @return  Mage_CatalogIndex_Model_Mysql4_Indexer
     */
    protected function _insert($table, $data) {
        $this->_insertData[$table][] = $data;
        $this->_commitInsert($table, false);
        return $this;
    }

    /**
     * Add price columns for catalog product flat table
     *
     * @param Varien_Object $object
     * @return Mage_CatalogIndex_Model_Mysql4_Indexer
     */
    public function prepareCatalogProductFlatColumns(Varien_Object $object)
    {
        $columns = $object->getColumns();

        foreach (Mage::getSingleton('catalogindex/retreiver')->getCustomerGroups() as $group) {
            $columnName = 'display_price_group_' . $group->getId();
            $columns[$columnName] = array(
                'type'      => 'decimal(12,4)',
                'unsigned'  => false,
                'is_null'   => true,
                'default'   => null,
                'extra'     => null
            );
        }

        $object->setColumns($columns);

        return $this;
    }

    /**
     * Add price indexes for catalog product flat table
     *
     * @param Varien_Object $object
     * @return Mage_CatalogIndex_Model_Mysql4_Indexer
     */
    public function prepareCatalogProductFlatIndexes(Varien_Object $object)
    {
        $indexes = $object->getIndexes();

        foreach (Mage::getSingleton('catalogindex/retreiver')->getCustomerGroups() as $group) {
            $columnName = 'display_price_group_' . $group->getId();
            $indexName  = 'IDX_DISPLAY_PRICE_GROUP_' . $group->getId();
            $indexes[$indexName] = array(
                'type'   => 'index',
                'fields' => array($columnName)
            );
        }

        $object->setIndexes($indexes);

        return $this;
    }

    /**
     * Update prices for Catalog Product flat
     *
     * @param int $storeId
     * @param string $tableName
     * @return Mage_CatalogIndex_Model_Mysql4_Indexer
     */
    public function updateCatalogProductFlat($storeId, $productIds = null, $tableName = null)
    {
        if (is_null($tableName)) {
            $tableName = $this->getTable('catalog/product_flat') . '_' . $storeId;
        }
        $addChildData = Mage::helper('catalog/product_flat')->isAddChildData();

        $priceAttribute = Mage::getSingleton('eav/entity_attribute')
            ->getIdByCode('catalog_product', 'price');
        $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();

        foreach (Mage::getSingleton('catalogindex/retreiver')->getCustomerGroups() as $group) {
            $columnName = 'display_price_group_' . $group->getId();

            /**
             * Update prices of main products in flat table
             */
            $select = $this->_getWriteAdapter()->select()
                ->join(
                    array('p' => $this->getTable('catalogindex/price')),
                    "`e`.`entity_id`=`p`.`entity_id`"
                        . " AND `p`.`attribute_id`={$priceAttribute}"
                        . " AND `p`.`customer_group_id`={$group->getId()}"
                        . " AND `p`.`website_id`={$websiteId}",
                    array($columnName => 'value'));
            if ($addChildData) {
                $select->where('e.is_child=?', 0);
            }

            if ($productIds instanceof Mage_Catalog_Model_Product_Condition_Interface) {
                $select->where('e.entity_id IN ('.$productIds->getIdsSelect($this->_getWriteAdapter())->__toString().')');
            } elseif (!is_null($productIds)) {
                $select->where("e.entity_id IN(?)", $productIds);
            }

            $sql = $select->crossUpdateFromSelect(array('e' => $tableName));
            $this->_getWriteAdapter()->query($sql);

            if ($addChildData) {
                /**
                 * Update prices for children products in flat table
                 */
                $select = $this->_getWriteAdapter()->select()
                    ->join(
                        array('p' => $this->getTable('catalogindex/price')),
                        "`e`.`child_id`=`p`.`entity_id`"
                            . " AND `p`.`attribute_id`={$priceAttribute}"
                            . " AND `p`.`customer_group_id`={$group->getId()}"
                            . " AND `p`.`website_id`={$websiteId}",
                        array($columnName => 'value'))
                    ->where('e.is_child=?', 1);

                if ($productIds instanceof Mage_Catalog_Model_Product_Condition_Interface) {
                    $select->where('e.child_id IN ('.$productIds->getIdsSelect($this->_getWriteAdapter())->__toString().')');
                } elseif (!is_null($productIds)) {
                    $select->where("e.child_id IN(?)", $productIds);
                }

                $sql = $select->crossUpdateFromSelect(array('e' => $tableName));
                $this->_getWriteAdapter()->query($sql);
            }

        }

        return $this;
    }
}
