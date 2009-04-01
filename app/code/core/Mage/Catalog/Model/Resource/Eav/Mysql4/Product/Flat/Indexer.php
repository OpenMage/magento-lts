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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog Product Flat Indexer Resource Model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Flat_Indexer
    extends Mage_Core_Model_Mysql4_Abstract
{
    const XML_NODE_MAX_INDEX_COUNT  = 'global/catalog/product/flat/max_index_count';
    const XML_NODE_ATTRIBUTE_NODES  = 'global/catalog/product/flat/attribute_nodes';

    /**
     * Attribute codes for flat
     *
     * @var array
     */
    protected $_attributeCodes;

    /**
     * Attribute objects for flat cache
     *
     * @var array
     */
    protected $_attributes;

    /**
     * Required system attributes for preload
     *
     * @var array
     */
    protected $_systemAttributes = array('status', 'required_options', 'tax_class_id', 'weight');

    /**
     * Eav Catalog_Product Entity Type Id
     *
     * @var int
     */
    protected $_entityTypeId;

    /**
     * Flat table columns cache
     *
     * @var array
     */
    protected $_columns;

    /**
     * Flat table indexes cache
     *
     * @var array
     */
    protected $_indexes;

    /**
     * Product Type Instances cache
     *
     * @var array
     */
    protected $_productTypes;

    /**
     * Initialize connection
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/product', 'entity_id');
    }

    /**
     * Rebuild Catalog Product Flat Data
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Flat_Indexer
     */
    public function rebuild($store = null)
    {
        if (is_null($store)) {
            foreach (Mage::app()->getStores() as $store) {
                $this->rebuild($store->getId());
            }
            $flag = $this->getFlatHelper()->getFlag();
            $flag->setIsBuild(true)->save();
            return $this;
        }

        $this->prepareFlatTable($store);
        $this->cleanNonWebsiteProducts($store);
        $this->updateStaticAttributes($store);
        $this->updateEavAttributes($store);
        $this->updateEventAttributes($store);
        $this->updateRelationProducts($store);
        $this->cleanRelationProducts($store);

        return $this;
    }

    /**
     * Retrieve Catalog Product Flat helper
     *
     * @return Mage_Catalog_Helper_Product_Flat
     */
    public function getFlatHelper()
    {
        return Mage::helper('catalog/product_flat');
    }

    /**
     * Retrieve attribute codes using for flat
     *
     * @return array
     */
    public function getAttributeCodes()
    {
        if (is_null($this->_attributeCodes)) {
            $attributeNodes = Mage::getConfig()
                ->getNode(self::XML_NODE_ATTRIBUTE_NODES)
                ->children();
            foreach ($attributeNodes as $node) {
                $attributes = Mage::getConfig()->getNode((string)$node)->asArray();
                $attributes = array_keys($attributes);
                $this->_systemAttributes = array_unique(array_merge($attributes, $this->_systemAttributes));
            }

            $this->_attributeCodes = array();
            $whereCond  = array(
                $this->_getReadAdapter()->quoteInto('backend_type=?', 'static'),
                $this->_getReadAdapter()->quoteInto('is_filterable>?', 0),
                $this->_getReadAdapter()->quoteInto('is_filterable_in_search=?', 1),
                $this->_getReadAdapter()->quoteInto('used_in_product_listing=?', 1),
                $this->_getReadAdapter()->quoteInto('used_for_sort_by=?', 1),
                $this->_getReadAdapter()->quoteInto('attribute_code IN(?)', $this->_systemAttributes)
            );

            $select = $this->_getReadAdapter()->select()
                ->from($this->getTable('eav/attribute'), array('attribute_id','attribute_code'))
                ->where('entity_type_id=?', $this->getEntityTypeId())
                ->where(join(' OR ', $whereCond));
            $this->_attributeCodes = $this->_getReadAdapter()
                ->fetchPairs($select);
        }
        return $this->_attributeCodes;
    }

    /**
     * Retrieve entity type
     *
     * @return string
     */
    public function getEntityType()
    {
        return 'catalog_product';
    }

    /**
     * Retrieve Catalog Entity Type Id
     *
     * @return int
     */
    public function getEntityTypeId()
    {
        if (is_null($this->_entityTypeId)) {
            $this->_entityTypeId = Mage::getResourceModel('catalog/config')
                ->getEntityTypeId();
        }
        return $this->_entityTypeId;
    }

    /**
     * Retrieve attribute objects for flat
     *
     * @param bool $cache
     * @return array
     */
    public function getAttributes()
    {
        if (is_null($this->_attributes)) {
            $attributeCodes = $this->getAttributeCodes(false);
            Mage::getSingleton('eav/config')
                ->preloadAttributes($this->getEntityType(), $attributeCodes);
            $entity = Mage::getSingleton('eav/config')
                ->getEntityType($this->getEntityType())
                ->getEntity();
            foreach ($attributeCodes as $attributeCode) {
                $attribute = Mage::getSingleton('eav/config')
                    ->getAttribute($this->getEntityType(), $attributeCode)
                    ->setEntity($entity);
                $this->_attributes[$attributeCode] = $attribute;
            }
        }
        return $this->_attributes;
    }

    /**
     * Retrieve loaded attribute by code
     *
     * @param string $attributeCode
     * @return Mage_Eav_Model_Entity_Attribute
     */
    public function getAttribute($attributeCode)
    {
        $attributes = $this->getAttributes();
        if (!isset($attributes[$attributeCode])) {
            $attribute = Mage::getModel('catalog/resource_eav_attribute')
                ->loadByCode($this->getEntityTypeId(), $attributeCode);
            if (!$attribute->getId()) {
                Mage::throwException(Mage::helper('catalog')->__('Invalid attribute %s', $attributeCode));
            }
            $entity = Mage::getSingleton('eav/config')
                ->getEntityType($this->getEntityType())
                ->getEntity();
            $attribute->setEntity($entity);
            return $attribute;
        }
        return $attributes[$attributeCode];
    }

    /**
     * Retrieve Catalog Product Flat Table name
     *
     * @param int $store
     * @return string
     */
    public function getFlatTableName($store)
    {
        return $this->getTable('catalog/product_flat') . '_' . $store;
    }

    /**
     * Retrieve catalog product flat table columns array
     *
     * @return array
     */
    public function getFlatColumns()
    {
        if (is_null($this->_columns)) {
            $this->_columns = array(
                'entity_id'         => array(
                    'type'      => 'int(10)',
                    'unsigned'  => true,
                    'is_null'   => false,
                    'default'   => null,
                    'extra'     => 'auto_increment'
                ),
                'child_id'          => array(
                    'type'      => 'int(10)',
                    'unsigned'  => true,
                    'is_null'   => true,
                    'default'   => null,
                    'extra'     => null
                ),
                'is_child'          => array(
                    'type'      => 'tinyint(1)',
                    'unsigned'  => true,
                    'is_null'   => false,
                    'default'   => 0,
                    'extra'     => null
                ),
                'attribute_set_id'  => array(
                    'type'      => 'smallint(5)',
                    'unsigned'  => true,
                    'is_null'   => false,
                    'default'   => 0,
                    'extra'     => null
                ),
                'type_id'           => array(
                    'type'      => 'varchar(32)',
                    'unsigned'  => false,
                    'is_null'   => false,
                    'default'   => 'simple',
                    'extra'     => null
                )
            );

            foreach ($this->getAttributes() as $attribute) {
                /* @var $attribute Mage_Eav_Model_Entity_Attribute */
                if (is_null($attribute->getFlatColumns())) {
                    continue;
                }

                $this->_columns = array_merge($this->_columns, $attribute->getFlatColumns());
            }

            $columnsObject = new Varien_Object();
            $columnsObject->setColumns($this->_columns);
            Mage::dispatchEvent('catalog_product_flat_prepare_columns', array(
                'columns'   => $columnsObject
            ));
            $this->_columns = $columnsObject->getColumns();
        }
        return $this->_columns;
    }

    /**
     * Retrieve catalog product flat table indexes array
     *
     * @return array
     */
    public function getFlatIndexes()
    {
        if (is_null($this->_indexes)) {
            $this->_indexes = array(
                'PRIMARY'       => array(
                    'type'   => 'primary',
                    'fields' => array('entity_id', 'child_id')
                ),
                'IDX_CHILD_ID'      => array(
                    'type'   => 'index',
                    'fields' => array('child_id')
                ),
                'IDX_IS_CHILD'      => array(
                    'type'   => 'index',
                    'fields' => array('entity_id', 'is_child')
                ),
                'IDX_TYPE_ID'       => array(
                    'type'   => 'index',
                    'fields' => array('type_id')
                ),
                'IDX_ATRRIBUTE_SET' => array(
                    'type'   => 'index',
                    'fields' => array('attribute_set_id')
                ),
            );

            foreach ($this->getAttributes() as $attribute) {
                /* @var $attribute Mage_Eav_Model_Entity_Attribute */
                if (is_null($attribute->getFlatColumns())) {
                    continue;
                }
                $this->_indexes = array_merge($this->_indexes, $attribute->getFlatIndexes());
            }

            $indexesObject = new Varien_Object();
            $indexesObject->setIndexes($this->_indexes);
            Mage::dispatchEvent('catalog_product_flat_prepare_indexes', array(
                'indexes'   => $indexesObject
            ));
            $this->_indexes = $indexesObject->getIndexes();
        }
        return $this->_indexes;
    }

    /**
     * Prepare flat table for store
     *
     * @param int $store
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Flat_Indexer
     */
    public function prepareFlatTable($store)
    {
        $columns = $this->getFlatColumns();
        $indexes = $this->getFlatIndexes();

        $maxIndex   = Mage::getConfig()->getNode(self::XML_NODE_MAX_INDEX_COUNT);

        if (count($indexes) > $maxIndex) {
            Mage::throwException(Mage::helper('catalog')->__("Flat Catalog module has a limit of %2\$d filterable and/or sort able attributes. Currently there are %1\$d. Please reduce the number of filterable/sort able attributes in order to use this module.", count($indexes), $maxIndex));
        }

        $tableName = $this->getFlatTableName($store);
        $tableNameQuote = $this->_getWriteAdapter()->quoteIdentifier($tableName);
        $tableExistsSql = $this->_getWriteAdapter()
            ->quoteInto("SHOW TABLE STATUS LIKE ?", $tableName);
        if (!$this->_getWriteAdapter()->fetchRow($tableExistsSql)) {
            $sql = "CREATE TABLE {$tableNameQuote} (\n";
            foreach ($columns as $field => $fieldProp) {
                $fieldNameQuote = $this->_getWriteAdapter()->quoteIdentifier($field);
                $sql .= "  {$fieldNameQuote} {$fieldProp['type']}";
                $sql .= ($fieldProp['unsigned'] ? ' UNSIGNED' : '');
                $sql .= ($fieldProp['extra'] ? ' ' . $fieldProp['extra'] : '');
                $sql .= ($fieldProp['is_null'] === false ? ' NOT NULL' : '');
                $sql .= ($fieldProp['default'] === null
                    ? ' DEFAULT NULL'
                    : $this->_getReadAdapter()->quoteInto(' DEFAULT ?', $fieldProp['default']));
                $sql .= ",\n";
            }
            foreach ($indexes as $indexName => $indexProp) {
                $fields = $indexProp['fields'];
                if (is_array($fields)) {
                    $fieldSql = array();
                    foreach ($fields as $field) {
                        $fieldSql[] = $this->_getReadAdapter()->quoteIdentifier($field);
                    }
                    $fieldSql = join(',', $fieldSql);
                }
                else {
                    $fieldSql = $this->_getReadAdapter()->quoteIdentifier($fields);
                }

                switch (strtolower($indexProp['type'])) {
                    case 'primary':
                        $condition = 'PRIMARY KEY';
                        break;
                    case 'unique':
                        $condition = 'UNIQUE ' . $this->_getReadAdapter()->quoteIdentifier($indexName);
                        break;
                    case 'fulltext':
                        $condition = 'FULLTEXT ' . $this->_getReadAdapter()->quoteIdentifier($indexName);
                        break;
                    default:
                        $condition = 'KEY ' . $this->_getReadAdapter()->quoteIdentifier($indexName);
                        break;
                }

                $sql .= "  {$condition} (" . $fieldSql . "),\n";
            }

            $sql .= "  CONSTRAINT `FK_CATALOG_PRODUCT_FLAT_{$store}_ENTITY` FOREIGN KEY (`entity_id`)"
                . " REFERENCES `{$this->getTable('catalog/product')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,\n"
                . "  CONSTRAINT `FK_CATALOG_PRODUCT_FLAT_{$store}_CHILD` FOREIGN KEY (`child_id`)"
                . " REFERENCES `{$this->getTable('catalog/product')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE\n"
                . ") ENGINE=InnoDB DEFAULT CHARSET=utf8";
            $this->_getWriteAdapter()->query($sql);
        }
        else {
            $describe   = $this->_getWriteAdapter()->describeTable($tableName);
            $indexList  = $this->_getWriteAdapter()->getIndexList($tableName);

            $addColumns     = array_diff_key($columns, $describe);
            $dropColumns    = array_diff_key($describe, $columns);

            $addIndexes     = array_diff_key($indexes, $indexList);
            $dropIndexes    = array_diff_key($indexList, $indexes);

            if ($addColumns or $dropColumns or $addIndexes or $dropIndexes) {
                $sql = "ALTER TABLE {$tableNameQuote}";
                // add columns
                foreach ($addColumns as $columnName => $columnProp) {
                    //var_dump($columnProp);
                    $columnNameQuote = $this->_getWriteAdapter()->quoteIdentifier($columnName);
                    $sql .= " ADD COLUMN {$columnNameQuote} {$columnProp['type']}";
                    $sql .= ($columnProp['unsigned'] ? ' UNSIGNED' : '');
                    $sql .= ($columnProp['extra'] ? ' ' . $columnProp['extra'] : '');
                    $sql .= ($columnProp['is_null'] === false ? ' NOT NULL' : '');
                    $sql .= ($columnProp['default'] === null
                        ? ' DEFAULT NULL'
                        : $this->_getReadAdapter()->quoteInto(' DEFAULT ?', $columnProp['default']));
                    $sql .= ",";
                }
                // add indexes
                foreach ($addIndexes as $indexName => $indexProp) {
                    $indexNameQuote = $this->_getWriteAdapter()->quoteIdentifier($indexName);

                    switch (strtolower($indexProp['type'])) {
                        case 'primary':
                            $condition = 'PRIMARY KEY';
                            break;
                        case 'unique':
                            $condition = 'UNIQUE ' . $indexNameQuote;
                            break;
                        case 'fulltext':
                            $condition = 'FULLTEXT ' . $indexNameQuote;
                            break;
                        default:
                            $condition = 'INDEX ' . $indexNameQuote;
                            break;
                    }

                    $fields = $indexProp['fields'];
                    if (is_array($fields)) {
                        $fieldSql = array();
                        foreach ($fields as $field) {
                            $fieldSql[] = $this->_getReadAdapter()->quoteIdentifier($field);
                        }
                        $fieldSql = join(',', $fieldSql);
                    }
                    else {
                        $fieldSql = $this->_getReadAdapter()->quoteIdentifier($fields);
                    }

                    $sql .= " ADD {$condition} ({$fieldSql}),";
                }
                // drop columns
                foreach ($dropColumns as $columnName => $columnProp) {
                    $columnNameQuote = $this->_getWriteAdapter()->quoteIdentifier($columnName);
                    $sql .= " DROP COLUMN {$columnNameQuote},";
                }
                // drop indexes
                foreach ($dropIndexes as $indexName => $indexProp) {
                    $indexNameQuote = $this->_getWriteAdapter()->quoteIdentifier($indexName);
                    $sql .= " DROP INDEX {$indexNameQuote},";
                }

                $sql = rtrim($sql, ",");
                $this->_getWriteAdapter()->query($sql);
            }
        }

        return $this;
    }

    /**
     * Add or Update static attributes
     *
     * @param int $store
     * @param int|array $productIds update only product(s)
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Flat_Indexer
     */
    public function updateStaticAttributes($store, $productIds = null)
    {
        $website = Mage::app()->getStore($store)->getWebsite()->getId();
        $status = $this->getAttribute('status');
        /* @var $status Mage_Eav_Model_Entity_Attribute */
        $fieldList  = array('entity_id', 'child_id', 'is_child', 'type_id', 'attribute_set_id');
        $isChild = new Zend_Db_Expr('0');
        $columns    = $this->getFlatColumns();
        $select     = $this->_getWriteAdapter()->select()
            ->from(
                array('e' => $this->getTable('catalog/product')),
                array('entity_id', 'entity_id', $isChild, 'type_id', 'attribute_set_id'))
            ->join(
                array('wp' => $this->getTable('catalog/product_website')),
                "`e`.`entity_id`=`wp`.`product_id` AND `wp`.`website_id`={$website}",
                array())
            ->joinLeft(
                array('t1' => $status->getBackend()->getTable()),
                "`e`.`entity_id`=`t1`.`entity_id`",
                array())
            ->joinLeft(
                array('t2' => $status->getBackend()->getTable()),
                "t2.entity_id = t1.entity_id"
                    . " AND t1.entity_type_id = t2.entity_type_id"
                    . " AND t1.attribute_id = t2.attribute_id"
                    . " AND t2.store_id = {$store}",
                array())
            ->where("t1.entity_type_id=?", $status->getEntityTypeId())
            ->where("t1.attribute_id=?", $status->getId())
            ->where("t1.store_id=?", 0)
            ->where("IFNULL(`t2`.`value`, `t1`.`value`)=?", Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        foreach ($this->getAttributes() as $attributeCode => $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            if ($attribute->getBackend()->getType() == 'static') {
                if (!isset($columns[$attributeCode])) {
                    continue;
                }
                $fieldList[] = $attributeCode;
                $select->from(null, $attributeCode);
            }
        }

        if (!is_null($productIds)) {
            $select->where('e.entity_id IN(?)', $productIds);
        }

        $sql = $select->insertFromSelect($this->getFlatTableName($store), $fieldList);
        $this->_getWriteAdapter()->query($sql);
        return $this;
    }

    /**
     * Remove non website products
     *
     * @param int $store
     * @param int|array $productIds
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Flat_Indexer
     */
    public function cleanNonWebsiteProducts($store, $productIds = null)
    {
        $website = Mage::app()->getStore($store)->getWebsite()->getId();

        $select = $this->_getWriteAdapter()->select()
            ->from(
                array('e' => $this->getFlatTableName($store)),
                null)
            ->joinLeft(
                array('wp' => $this->getTable('catalog/product_website')),
                "`e`.`entity_id`=`wp`.`product_id` AND `e`.`child_id`=`wp`.`product_id` AND `wp`.`website_id`={$website}",
                array());
        if (!is_null($productIds)) {
            $cond = array(
                $this->_getWriteAdapter()->quoteInto('e.entity_id IN(?)', $productIds),
                $this->_getWriteAdapter()->quoteInto('e.child_id IN(?)', $productIds)
            );
            $select->where(join(' OR ', $cond));
        }

        $sql = $select->deleteFromSelect('e');
        $this->_getWriteAdapter()->query($sql);

        return $this;
    }

    /**
     * Update attribute flat data
     *
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @param int $store
     * @param int|array $productIds update only product(s)
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Flat_Indexer
     */
    public function updateAttribute($attribute, $store, $productIds = null)
    {
        if ($attribute->getBackend()->getType() == 'static') {
            $select = $this->_getWriteAdapter()->select()
                ->from(array('main_table' => $this->getTable('catalog/product')), $attribute->getAttributeCode());
            if (!is_null($productIds)) {
                $select->where('main_table.entity_id IN(?)', $productIds);
            }
            $sql = $select->crossUpdateFromSelect(array('e' => $this->getFlatTableName($store)));
            $this->_getWriteAdapter()->query($sql);
        }
        else {
            $select = $attribute->getFlatUpdateSelect($store);
            if ($select instanceof Varien_Db_Select) {
                if (!is_null($productIds)) {
                    $select->where('e.entity_id IN(?)', $productIds);
                }
                $sql = $select->crossUpdateFromSelect(array('e' => $this->getFlatTableName($store)));
                $this->_getWriteAdapter()->query($sql);
            }
        }
        return $this;
    }

    /**
     * Update non static EAV attributes flat data
     *
     * @param int $store
     * @param int|array $productIds update only product(s)
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Flat_Indexer
     */
    public function updateEavAttributes($store, $productIds = null)
    {
        foreach ($this->getAttributes() as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            if ($attribute->getBackend()->getType() != 'static') {
                $this->updateAttribute($attribute, $store, $productIds);
            }
        }
        return $this;
    }

    /**
     * Update events observer attributes
     *
     * @param int $store
     * @return Mage_Catalog_Model_Product_Flat_Indexer
     */
    public function updateEventAttributes($store = null)
    {
        Mage::dispatchEvent('catalog_product_flat_rebuild', array(
            'store_id' => $store,
            'table'    => $this->getFlatTableName($store)
        ));
    }

    /**
     * Retrieve Product Type Instances
     * as key - type code, value - instance model
     *
     * @return array
     */
    public function getProductTypeInstances()
    {
        if (is_null($this->_productTypes)) {
            $this->_productTypes = array();
            $productEmulator     = new Varien_Object();

            foreach (array_keys(Mage_Catalog_Model_Product_Type::getTypes()) as $typeId) {
                $productEmulator->setTypeId($typeId);
                $this->_productTypes[$typeId] = Mage::getSingleton('catalog/product_type')
                    ->factory($productEmulator);
            }
        }
        return $this->_productTypes;
    }

    /**
     * Update relation products
     *
     * @param int $store
     * @param int|array $productIds Update child product(s) only
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Flat_Indexer
     */
    public function updateRelationProducts($store, $productIds = null)
    {
//        $website = Mage::app()->getStore($store)->getWebsite()->getId();
        foreach ($this->getProductTypeInstances() as $typeInstance) {
            if (!$typeInstance->isComposite()) {
                continue;
            }
            $relation = $typeInstance->getRelationInfo();
            if ($relation
                and $relation->getTable()
                and $relation->getParentFieldName()
                and $relation->getChildFieldName()
            ) {
                $columns    = $this->getFlatColumns();
                $fieldList  = array_keys($columns);
                unset($columns['entity_id']);
                unset($columns['child_id']);
                unset($columns['is_child']);

                $select = $this->_getWriteAdapter()->select()
                    ->from(
                        array('t' => $this->getTable($relation->getTable())),
                        array($relation->getParentFieldName(), $relation->getChildFieldName(), new Zend_Db_Expr('1')))
//                    ->join(
//                        array('wp' => $this->getTable('catalog/product_website')),
//                        "`t`.`{$relation->getChildFieldName()}`=`wp`.`product_id`"
//                            . " AND `t`.`{$relation->getParentFieldName()}`=`wp`.`product_id`"
//                            . " AND `wp`.`website_id`={$website}",
//                        array())
                    ->join(
                        array('e' => $this->getFlatTableName($store)),
                        "`e`.`entity_id`=`t`.`{$relation->getChildFieldName()}`",
                        array_keys($columns)
                    );
                if (!is_null($relation->getWhere())) {
                    $select->where($relation->getWhere());
                }
                if (!is_null($productIds)) {
                    $cond = array(
                        $this->_getWriteAdapter()->quoteInto("{$relation->getChildFieldName()} IN(?)", $productIds),
                        $this->_getWriteAdapter()->quoteInto("{$relation->getParentFieldName()} IN(?)", $productIds)
                    );

                    $select->where(join(' OR ', $cond));
                }
                $sql = $select->insertFromSelect($this->getFlatTableName($store), $fieldList);
                $this->_getWriteAdapter()->query($sql);
            }
        }

        return $this;
    }

    /**
     * Update children data from parent
     *
     * @param int $store
     * @param int|array $productIds
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Flat_Indexer
     */
    public function updateChildrenDataFromParent($store, $productIds = null)
    {
        $select = $this->_getWriteAdapter()->select();
        foreach (array_keys($this->getFlatColumns()) as $columnName) {
            if ($columnName == 'entity_id' || $columnName == 'child_id' || $columnName == 'is_child') {
                continue;
            }
            $select->from(null, array($columnName => new Zend_Db_Expr('`t1`.`'. $columnName.'`')));
        }
        $select
            ->joinLeft(
                array('t1' => $this->getFlatTableName($store)),
                "`t2`.`child_id`=`t1`.`entity_id` AND `t1`.`is_child`=0",
                array())
            ->where('t2.is_child=1');

        if (!is_null($productIds)) {
            $select->where('t2.child_id IN(?)', $productIds);
        }

        $sql = $select->crossUpdateFromSelect(array('t2' => $this->getFlatTableName($store)));
        $this->_getWriteAdapter()->query($sql);

        return $this;
    }

    /**
     * Clean unused relation products
     *
     * @param int $store
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Flat_Indexer
     */
    public function cleanRelationProducts($store)
    {
        foreach ($this->getProductTypeInstances() as $typeInstance) {
            if (!$typeInstance->isComposite()) {
                continue;
            }
            $relation = $typeInstance->getRelationInfo();
            if ($relation
                and $relation->getTable()
                and $relation->getParentFieldName()
                and $relation->getChildFieldName()
            ) {
                $select = $this->_getWriteAdapter()->select()
                    ->distinct(true)
                    ->from(
                        $this->getTable($relation->getTable()),
                        "{$relation->getParentFieldName()}"
                    )
                   ;
                $joinLeftCond = null;
                if (!is_null($relation->getWhere())) {
                    $select->where($relation->getWhere());
                    $joinLeftCond = ' AND ' . $relation->getWhere();
                }

                $entitySelect = new Zend_Db_Expr($select->__toString());

                $select = $this->_getWriteAdapter()->select()
                    ->from(
                        array('e' => $this->getFlatTableName($store)),
                        null
                    )
                    ->joinLeft(
                        array('t' => $this->getTable($relation->getTable())),
                        "e.entity_id=t.{$relation->getParentFieldName()} AND e.child_id=t.{$relation->getChildFieldName()}"
                            . $joinLeftCond,
                        array())
                    ->where("e.is_child=?", 1)
                    ->where("e.entity_id IN(?)", $entitySelect)
                    ->where("t.{$relation->getChildFieldName()} IS NULL");

                $sql = $select->deleteFromSelect('e');
                $this->_getWriteAdapter()->query($sql);
            }
        }

        return $this;
    }

    /**
     * Remove product data from flat
     *
     * @param int|array $productIds
     * @param int $store
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Flat_Indexer
     */
    public function removeProduct($productIds, $store)
    {
        $cond = array(
            $this->_getWriteAdapter()->quoteInto('entity_id IN(?)', $productIds),
            $this->_getWriteAdapter()->quoteInto('child_id IN(?)', $productIds)
        );
        $cond = join(' OR ', $cond);
        $this->_getWriteAdapter()->delete($this->getFlatTableName($store), $cond);

        return $this;
    }

    /**
     * Remove children from parent product
     *
     * @param int|array $productIds
     * @param int $store
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Flat_Indexer
     */
    public function removeProductChildren($productIds, $store)
    {
        $cond = array(
            $this->_getWriteAdapter()->quoteInto('entity_id IN(?)', $productIds),
            $this->_getWriteAdapter()->quoteInto('is_child=?', 1),
        );
        $this->_getWriteAdapter()->delete($this->getFlatTableName($store), $cond);

        return $this;
    }

    /**
     * Update flat data for product
     *
     * @param int|array $productIds
     * @param int $store
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Flat_Indexer
     */
    public function updateProduct($productIds, $store)
    {
        $this->saveProduct($productIds, $store);

        Mage::dispatchEvent('catalog_product_flat_update_product', array(
            'store_id'      => $store,
            'table'         => $this->getFlatTableName($store),
            'product_ids'   => $productIds
        ));

        return $this;
    }

    /**
     * Save product(s) data for store
     *
     * @param int|array $productIds
     * @param int $store
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Flat_Indexer
     */
    public function saveProduct($productIds, $store)
    {
        $this->updateStaticAttributes($store, $productIds);
        $this->updateEavAttributes($store, $productIds);

        return $this;
    }

    /**
     * Delete flat table process
     *
     * @param int $store
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Flat_Indexer
     */
    public function deleteFlatTable($store)
    {
        $tableName = $this->getFlatTableName($store);
        $tableNameQuote = $this->_getWriteAdapter()->quoteIdentifier($tableName);
        $tableExistsSql = $this->_getWriteAdapter()
            ->quoteInto("SHOW TABLE STATUS LIKE ?", $tableName);
        if ($this->_getWriteAdapter()->query($tableExistsSql)) {
            $sql = sprintf('DROP TABLE IF EXISTS %s', $tableNameQuote);
            $this->_getWriteAdapter()->query($sql);
        }

        return $this;
    }
}
