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
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Category flat model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_storeId = null;

    protected $_loaded = false;

    protected $_nodes = array();

    /**
     * Inactive categories ids
     *
     * @var array
     */
    protected $_inactiveCategoryIds = null;

    protected $_isRebuilt = null;

    protected function  _construct()
    {
        $this->_init('catalog/category_flat', 'entity_id');
    }

    /**
     * Set store id
     *
     * @param integer $storeId
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Return store id
     *
     * @return integer
     */
    public function getStoreId()
    {
        if (is_null($this->_storeId)) {
            return Mage::app()->getStore()->getId();
        }
        return $this->_storeId;
    }

    /**
     * Get main table name
     *
     * @return string
     */
    public function getMainTable()
    {
        return $this->getMainStoreTable($this->getStoreId());
    }

    /**
     * Return name of table for given $storeId.
     *
     * @param integer $storeId
     * @return string
     */
    public function getMainStoreTable($storeId = 0)
    {
        $table = parent::getMainTable();
        if (is_string($storeId)) {
            $storeId = intval($storeId);
        }
        if ($this->getUseStoreTables() && $storeId) {
            $table .= '_store_'.$storeId;
        }
        return $table;
    }

    /**
     * Return true if need use for each store different table of flat categoris data.
     *
     * @return boolean
     */
    public function getUseStoreTables()
    {
        return true;
    }

    /**
     * Add inactive categories ids
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat
     */
    public function addInactiveCategoryIds($ids)
    {
        if (!is_array($this->_inactiveCategoryIds)) {
            $this->_initInactiveCategoryIds();
        }
        $this->_inactiveCategoryIds = array_merge($ids, $this->_inactiveCategoryIds);
        return $this;
    }

    /**
     * Retreive inactive categories ids
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat
     */
    protected function _initInactiveCategoryIds()
    {
        $this->_inactiveCategoryIds = array();
        Mage::dispatchEvent('catalog_category_tree_init_inactive_category_ids', array('tree'=>$this));
        return $this;
    }

    /**
     * Retreive inactive categories ids
     *
     * @return array
     */
    public function getInactiveCategoryIds()
    {
        if (!is_array($this->_inactiveCategoryIds)) {
            $this->_initInactiveCategoryIds();
        }

        return $this->_inactiveCategoryIds;
    }

    /**
     * Load nodes by parent id
     *
     * @param integer $parentId
     * @param integer $recursionLevel
     * @param integer $storeId
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat
     */
    protected function _loadNodes($parentNode = null, $recursionLevel = 0, $storeId = 0)
    {
        $_conn = $this->_getReadAdapter();
        $startLevel = 1;
        $parentPath = '';
        if ($parentNode instanceof Mage_Catalog_Model_Category) {
            $parentPath = $parentNode->getPath();
            $startLevel = $parentNode->getLevel();
        } elseif (is_numeric($parentNode)) {
            $selectParent = $_conn->select()
                ->from($this->getMainStoreTable())
                ->where('entity_id = ?', $parentNode)
                ->where('store_id = ?', '0');
            if ($parentNode = $_conn->fetchRow($selectParent)) {
                $parentPath = $parentNode['path'];
                $startLevel = $parentNode['level'];
            }
        }
        $select = $_conn->select()
            ->from(array('main_table'=>$this->getMainStoreTable($storeId)), array('main_table.entity_id', 'main_table.name', 'main_table.path', 'main_table.is_active', 'main_table.is_anchor'))
            ->joinLeft(
                array('url_rewrite'=>$this->getTable('core/url_rewrite')),
                'url_rewrite.category_id=main_table.entity_id AND url_rewrite.is_system=1 AND url_rewrite.product_id IS NULL AND url_rewrite.store_id="'.$storeId.'" AND url_rewrite.id_path LIKE "category/%"',
                array('request_path' => 'url_rewrite.request_path'))
            ->where('main_table.is_active = ?', '1')
//            ->order('main_table.path', 'ASC')
            ->order('main_table.position', 'ASC');



        if ($parentPath) {
            $select->where($_conn->quoteInto("main_table.path like ?", "$parentPath/%"));
        }
        if ($recursionLevel != 0) {
            $select->where("main_table.level <= ?", $startLevel + $recursionLevel);
        }

        $inactiveCategories = $this->getInactiveCategoryIds();

        if (!empty($inactiveCategories)) {
            $select->where('main_table.entity_id NOT IN (?)', $inactiveCategories);
        }

        $arrNodes = $_conn->fetchAll($select);
        $nodes = array();
        foreach ($arrNodes as $node) {
            $node['id'] = $node['entity_id'];
            $nodes[$node['id']] = Mage::getModel('catalog/category')->setData($node);
        }

        return $nodes;
    }

    /**
     * Creating sorted array of nodes
     *
     * @param array $children
     * @param string $path
     * @param Varien_Object $parent
     */
    public function addChildNodes($children, $path, $parent)
    {
        if (isset($children[$path])) {
            foreach ($children[$path] as $child) {
                $childrenNodes = $parent->getChildrenNodes();
                if ($childrenNodes && isset($childrenNodes[$child->getId()])) {
                    $childrenNodes[$child['entity_id']]->setChildrenNodes(array($child->getId()=>$child));
                } else {
                    if ($childrenNodes) {
                        $childrenNodes[$child->getId()] = $child;
                    } else {
                        $childrenNodes = array($child->getId()=>$child);
                    }
                    $parent->setChildrenNodes($childrenNodes);
                }

                if ($path) {
                    $childrenPath = explode('/', $path);
                } else {
                    $childrenPath = array();
                }
                $childrenPath[] = $child->getId();
                $childrenPath = implode('/', $childrenPath);
                $this->addChildNodes($children, $childrenPath, $child);
            }
        }
    }

    /**
     * Return sorted array of nodes
     *
     * @param integer|null $parentId
     * @param integer $recursionLevel
     * @param integer $storeId
     * @return array
     */
    public function getNodes($parentId, $recursionLevel = 0, $storeId = 0)
    {
        if (!$this->_loaded) {
            $selectParent = $this->_getReadAdapter()->select()
                ->from($this->getMainStoreTable())
                ->where('entity_id = ?', $parentId);
            if ($parentNode = $this->_getReadAdapter()->fetchRow($selectParent)) {
                $parentNode['id'] = $parentNode['entity_id'];
                $parentNode = Mage::getModel('catalog/category')->setData($parentNode);
                $this->_nodes[$parentNode->getId()] = $parentNode;
                $nodes = $this->_loadNodes($parentNode, $recursionLevel, $storeId);
                $childrenItems = array();
                foreach ($nodes as $node) {
                    $pathToParent = explode('/', $node->getPath());
                    array_pop($pathToParent);
                    $pathToParent = implode('/', $pathToParent);
                    $childrenItems[$pathToParent][] = $node;
                }
                $this->addChildNodes($childrenItems, $parentNode->getPath(), $parentNode);
                $childrenNodes = $this->_nodes[$parentNode->getId()];
                if ($childrenNodes->getChildrenNodes()) {
                    $this->_nodes = $childrenNodes->getChildrenNodes();
                }
                else {
                    $this->_nodes = array();
                }
                $this->_loaded = true;
            }
        }
        return $this->_nodes;
    }

    /**
     * Return array or collection of categories
     *
     * @param integer $parent
     * @param integer $recursionLevel
     * @param boolean|string $sorted
     * @param boolean $asCollection
     * @param boolean $toLoad
     * @return array|Varien_Data_Collection
     */
    public function getCategories($parent, $recursionLevel = 0, $sorted=false, $asCollection=false, $toLoad=true)
    {
        if ($asCollection) {
            $parentPath = $this->_getReadAdapter()->fetchOne(new Zend_Db_Expr("
                SELECT path FROM {$this->getMainStoreTable()} WHERE entity_id = {$parent}
            "));
            $collection = Mage::getModel('catalog/category')->getCollection()
                ->addNameToResult()
                ->addUrlRewriteToResult()
                ->addParentPathFilter($parentPath)
                ->addStoreFilter()
                ->addSortedField($sorted);
            if ($toLoad) {
                return $collection->load();
            }
            return $collection;
        }
        return $this->getNodes($parent, $recursionLevel, Mage::app()->getStore()->getId());
    }

    /**
     * Return node with id $nodeId
     *
     * @param integer $nodeId
     * @param array $nodes
     * @return Varien_Object
     */
    public function getNodeById($nodeId, $nodes = null)
    {
        if (is_null($nodes)) {
            $nodes = $this->getNodes();
        }
        if (isset($nodes[$nodeId])) {
            return $nodes[$nodeId];
        }
        foreach ($nodes as $node) {
//            if ($node->getId() == $nodeId) {
//                return $node;
//            }
            if ($node->getChildrenNodes()) {
                return $this->getNodeById($nodeId, $node->getChildrenNodes());
            }
        }
        return array();
    }

    /**
     * Check if category flat data is rebuilt
     *
     * @return bool
     */
    public function isRebuilt()
    {
        if ($this->_isRebuilt === null) {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getMainStoreTable($this->getStoreId()), 'entity_id')
                ->limit(1);
            try {
                $this->_isRebuilt = (bool) $this->_getReadAdapter()->fetchOne($select);
            } catch (Exception $e) {
                $this->_isRebuilt = false;
            }
        }
        return $this->_isRebuilt;
    }

    protected function _getTableSqlSchema($storeId = 0)
    {
        $storeId = Mage::app()->getStore($storeId)->getId();
        $schema = "CREATE TABLE `{$this->getMainStoreTable($storeId)}` (
                `entity_id` int(10) unsigned not null,
                `store_id` smallint(5) unsigned not null default '0',
                `parent_id` int(10) unsigned not null default '0',
                `path` varchar(255) not null default '',
                `level` int(11) not null default '0',
                `position` int(11) not null default '0',
                `children_count` int(11) not null,
                `created_at` datetime not null default '0000-00-00 00:00:00',
                `updated_at` datetime not null default '0000-00-00 00:00:00',
                KEY `CATEGORY_FLAT_CATEGORY_ID` (`entity_id`),
                KEY `CATEGORY_FLAT_STORE_ID` (`store_id`),
                KEY `path` (`path`),
                KEY `IDX_LEVEL` (`level`),
                CONSTRAINT `FK_CATEGORY_FLAT_CATEGORY_ID_STORE_{$storeId}` FOREIGN KEY (`entity_id`)
                    REFERENCES `{$this->getTable('catalog/category')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `FK_CATEGORY_FLAT_STORE_ID_STORE_{$storeId}` FOREIGN KEY (`store_id`)
                    REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        return $schema;
    }

    /**
     * Rebuild flat data from eav
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat
     */
    public function rebuild()
    {
        $_read  = $this->_getReadAdapter();
        if ($this->getUseStoreTables()) {
            $stores = array();
            $selectStores = $_read->select()
                ->from($this->getTable('core/store'), 'store_id');
            $stores = array();
            foreach ($_read->fetchAll($selectStores) as $store) {
                $stores[] = $store['store_id'];
            }
            $this->_createTable($stores);
        } else {
            $this->_createTable(0);
        }
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('catalog/category'), 'entity_id');
        $_categories = $this->_getReadAdapter()->fetchAll($select);
        foreach ($_categories as $_category) {
            foreach ($stores as $store) {
                $_tmpCategory = Mage::getModel('catalog/category')
                    ->setStoreId($store)
                    ->load($_category['entity_id']);
                if ($_tmpCategory->getId()) {
                    $this->_synchronize($_tmpCategory, 'insert');
                }
            }
        }
        $_tmpCategory = null;
        return $this;
    }

    /**
     * Creating table and adding attributes as fields to table
     *
     * @param array|integer $stores
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat
     */
    protected function _createTable($stores)
    {
        if (!is_array($stores)) {
            $stores = (int) $stores;
        }
        $_read  = $this->_getReadAdapter();
        $_write = $this->_getWriteAdapter();
        if ($this->getUseStoreTables() && is_array($stores)) {
            foreach ($stores as $store) {
                $_read->query("DROP TABLE IF EXISTS `{$this->getMainStoreTable($store)}`");
                $_read->query($this->_getTableSqlSchema($store));
            }
        } else {
            $_read->query("DROP TABLE IF EXISTS `{$this->getMainStoreTable($stores)}`");
            $_read->query($this->_getTableSqlSchema($stores));
        }
        $selectAttribute = $_read->select()
            ->from($this->getTable('eav/entity_type'), array())
            ->join(
                $this->getTable('eav/attribute'),
                $this->getTable('eav/attribute').'.entity_type_id = '.$this->getTable('eav/entity_type').'.entity_type_id',
                $this->getTable('eav/attribute').'.*'
            )
            ->where($this->getTable('eav/entity_type').'.entity_type_code=?', 'catalog_category');
        $resultAttribute = $_read->fetchAll($selectAttribute);
        foreach ($resultAttribute as $attribute) {
            $type = '';
            switch ($attribute['backend_type']) {
                case 'varchar':
                    $type = 'varchar(255) not null default \'\'';
                    break;
                case 'int':
                    $type = 'int(10) not null default \'0\'';
                    break;
                case 'text':
                    $type = 'text';
                    break;
                case 'datetime':
                    $type = 'datetime not null default \'0000-00-00 00:00:00\'';
                    break;
                case 'decimal':
                    $type = 'decimal(10,2) not null default \'0.00\'';
                    break;
            }
            if ($type) {
                if ($this->getUseStoreTables() && is_array($stores)) {
                    foreach ($stores as $store) {
                        $_write->addColumn($this->getMainStoreTable($store), $attribute['attribute_code'], $type);
                    }
                } else {
                    $_write->addColumn($this->getMainStoreTable($stores), $attribute['attribute_code'], $type);
                }
            }
        }
        return $this;
    }

    /**
     * Synchronize flat data with eav model.
     *
     * @param Mage_Catalog_Model_Category $category
     * @param null|string $action
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat
     */
    protected function _synchronize($category, $action = null)
    {
        if (is_null($action)) {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getMainStoreTable($category->getStoreId()), 'entity_id')
                ->where('entity_id = ?', $category->getId());
            if ($result = $this->_getReadAdapter()->fetchOne($select)) {
                $action = 'update';
            } else {
                $action = 'insert';
            }
        }

        if ($action == 'update') {
            // update
            $this->_getWriteAdapter()->update(
                $this->getMainStoreTable($category->getStoreId()),
                $this->_prepareDataForAllFields($category),
                $this->_getReadAdapter()->quoteInto('entity_id = ?', $category->getId())
            );
        } elseif ($action == 'insert') {
            // insert
            $this->_getWriteAdapter()->insert(
                $this->getMainStoreTable($category->getStoreId()),
                $this->_prepareDataForAllFields($category)
            );
        }
        return $this;
    }

    /**
     * Synchronize flat data with eav model when category was moved.
     *
     * @param string $prevParentPath
     * @param string $parentPath
     * @param array $storeIds
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat
     */
    protected function _move($prevParentPath, $parentPath)
    {
        $_staticFields = array(
            'parent_id',
            'path',
            'level',
            'position',
            'children_count',
            'updated_at'
        );
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('core/store'), 'store_id');
        $stores = $this->_getReadAdapter()->fetchAll($select);
        foreach ($stores as $store) {
            $update = "UPDATE {$this->getMainStoreTable($store['store_id'])}, {$this->getTable('catalog/category')} SET";
            foreach ($_staticFields as $field) {
                $update .= " {$this->getMainStoreTable($store['store_id'])}.".$field."={$this->getTable('catalog/category')}.".$field.",";
            }
            $update = substr($update, 0, -1);
            $update .= " WHERE {$this->getMainStoreTable($store['store_id'])}.entity_id = {$this->getTable('catalog/category')}.entity_id AND " .
                "({$this->getTable('catalog/category')}.path like '$parentPath/%' OR " .
                "{$this->getTable('catalog/category')}.path like '$prevParentPath/%')";
            $this->_getWriteAdapter()->query($update);
        }
        return $this;
    }

    /**
     * Synchronize flat data with eav model.
     *
     * @param Mage_Catalog_Model_Category|array $category
     * @param array $storeIds
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat
     */
    public function synchronize($category = null, $storeIds = array())
    {
        if (is_null($category)) {
            $storesCondition = '';
            if (!empty($storeIds)) {
                $storesCondition = $this->_getReadAdapter()->quoteInto(
                    ' AND s.store_id IN (?)', $storeIds
                );
            }
            $stores = $this->_getReadAdapter()->fetchAll("
                SELECT
                    s.store_id, s.website_id, c.path AS root_path
                FROM
                    {$this->getTable('core/store')} AS s,
                    {$this->getTable('core/store_group')} AS sg,
                    {$this->getTable('catalog/category')} AS c
                WHERE
                    sg.group_id=s.group_id
                    AND c.entity_id=sg.root_category_id
                    {$storesCondition}
            ");
            foreach ($stores as $store) {
                $select = $this->_getReadAdapter()->select()
                    ->from($this->getTable('catalog/category'), 'entity_id')
                    ->where('path LIKE ?', "{$store['root_path']}/%")
                    ->orWhere('path = ?', $store['root_path']);
                $_categories = $this->_getReadAdapter()->fetchAll($select);
                if (!$this->_getReadAdapter()->showTableStatus($this->getMainStoreTable($store['store_id']))) {
                    $this->_createTable($store['store_id']);
                }
                $this->_getWriteAdapter()->delete(
                    $this->getMainStoreTable($store['store_id']),
                    $this->_getReadAdapter()->quoteInto('store_id = ?', $store['store_id'])
                );
                foreach ($_categories as $_category) {
                    $_tmpCategory = Mage::getModel('catalog/category')
                        ->setStoreId($store['store_id'])
                        ->load($_category['entity_id']);
                    $this->_synchronize($_tmpCategory, 'insert');
                }
            }
            $_tmpCategory = null;
        } elseif ($category instanceof Mage_Catalog_Model_Category) {
            foreach ($category->getStoreIds() as $storeId) {
                $_tmpCategory = Mage::getModel('catalog/category')
                    ->setStoreId($storeId)
                    ->load($category->getId());
                $_tmpCategory->setStoreId($storeId);
                $this->_synchronize($_tmpCategory);
            }
            $_tmpCategory = null;
        }
        return $this;
    }

    /**
     * Synchronize flat data with eav after moving category
     *
     * @param integer $categoryId
     * @param integer $prevParentId
     * @param integer $parentId
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat
     */
    public function move($categoryId, $prevParentId, $parentId)
    {
        $_staticFields = array(
            'parent_id',
            'path',
            'level',
            'position',
            'children_count',
            'updated_at'
        );
        $prevParent = Mage::getModel('catalog/category')->load($prevParentId);
        $parent = Mage::getModel('catalog/category')->load($parentId);
        if ($prevParent->getStore()->getWebsiteId() != $parent->getStore()->getWebsiteId()) {
            foreach ($prevParent->getStoreIds() as $storeId) {
                $this->_getWriteAdapter()->delete(
                    $this->getMainStoreTable($storeId),
                    $this->_getReadAdapter()->quoteInto('entity_id = ?', $categoryId)
                );
            }
            $categoryPath = $this->_getReadAdapter()->fetchOne("
                SELECT
                    path
                FROM
                    {$this->getTable('catalog/category')}
                WHERE
                    entity_id = '$categoryId'
            ");
            $select = $this->_getReadAdapter()->select()
                ->from($this->getTable('catalog/category'), 'entity_id')
                ->where('path LIKE ?', "$categoryPath/%")
                ->orWhere('path = ?', $categoryPath);
            $_categories = $this->_getReadAdapter()->fetchAll($select);
            foreach ($_categories as $_category) {
                foreach ($parent->getStoreIds() as $storeId) {
                    $_tmpCategory = Mage::getModel('catalog/category')
                        ->setStoreId($storeId)
                        ->load($_category['entity_id']);
                    $this->_synchronize($_tmpCategory);
                }
            }
        } else {
            foreach ($parent->getStoreIds() as $store) {
                $update = "UPDATE {$this->getMainStoreTable($store)}, {$this->getTable('catalog/category')} SET";
                foreach ($_staticFields as $field) {
                    $update .= " {$this->getMainStoreTable($store)}.".$field."={$this->getTable('catalog/category')}.".$field.",";
                }
                $update = substr($update, 0, -1);
                $update .= " WHERE {$this->getMainStoreTable($store)}.entity_id = {$this->getTable('catalog/category')}.entity_id AND " .
                    "({$this->getTable('catalog/category')}.path like '{$parent->getPath()}/%' OR " .
                    "{$this->getTable('catalog/category')}.path like '{$prevParent->getPath()}/%')";
                $this->_getWriteAdapter()->query($update);
            }
        }
        $prevParent = null;
        $parent = null;
        $_tmpCategory = null;
//        $this->_move($categoryId, $prevParentPath, $parentPath);
        return $this;
    }

    /**
     * Prepare array of category data to insert or update.
     *
     * array(
     *  'field_name' => 'value'
     * )
     *
     * @param Mage_Catalog_Model_Category $category
     * @param array $replaceFields
     * @return array
     */
    protected function _prepareDataForAllFields($category, $replaceFields = array())
    {
        $table = $this->_getReadAdapter()->describeTable($this->getMainStoreTable($category->getStoreId()));
        $data = array();
        foreach ($table as $column=>$columnData) {
            if (null !== $category->getData($column)) {
                if (key_exists($column, $replaceFields)) {
                    $value = $category->getData($replaceFields[$column]);
                } else {
                    $value = $category->getData($column);
                }
                if (is_array($value)) {
                    $value = implode(',', $value);
                }
                $data[$column] = $value;
            }
        }
        return $data;
    }

    /**
     * Get count of active/not active children categories
     *
     * @param   Mage_Catalog_Model_Category $category
     * @param   bool $isActiveFlag
     * @return  integer
     */
    public function getChildrenAmount($category, $isActiveFlag = true)
    {
        $_table = $this->getMainStoreTable($category->getStoreId());
        $select = $this->_getReadAdapter()->select()
            ->from($_table, "COUNT({$_table}.entity_id)")
            ->where("{$_table}.path LIKE ?", $category->getPath() . '/%')
            ->where("{$_table}.is_active = ?", (int) $isActiveFlag);
        return (int) $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Get products count in category
     *
     * @param Mage_Catalog_Model_Category $category
     * @return integer
     */
    public function getProductCount($category)
    {
        $select =  $this->_getReadAdapter()->select()
            ->from($this->getTable('catalog/category_product'), "COUNT({$this->getTable('catalog/category_product')}.product_id)")
            ->where("{$this->getTable('catalog/category_product')}.category_id = ?", $category->getId())
            ->group("{$this->getTable('catalog/category_product')}.category_id");
        return (int) $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Return parent categories of category
     *
     * @param Mage_Catalog_Model_Category $category
     * @return array
     */
    public function getParentCategories($category, $isActive = true)
    {
        $categories = array();
        $select = $this->_getReadAdapter()->select()
            ->from(array('main_table' => $this->getMainStoreTable($category->getStoreId())), array('main_table.entity_id', 'main_table.name'))
            ->joinLeft(
                array('url_rewrite'=>$this->getTable('core/url_rewrite')),
                'url_rewrite.category_id=main_table.entity_id AND url_rewrite.is_system=1 AND url_rewrite.product_id IS NULL AND url_rewrite.store_id="'.$category->getStoreId().'" AND url_rewrite.id_path LIKE "category/%"',
                array('request_path' => 'url_rewrite.request_path'))
            ->where('main_table.entity_id IN (?)', array_reverse(explode(',', $category->getPathInStore())));
        if ($isActive) {
            $select->where('main_table.is_active = ?', '1');
        }
        $select->order('main_table.path ASC');
        $result = $this->_getReadAdapter()->fetchAll($select);
        foreach ($result as $row) {
            $row['id'] = $row['entity_id'];
            $categories[$row['entity_id']] = Mage::getModel('catalog/category')->setData($row);
        }
        return $categories;
    }

    /**
     * Return children categories of category
     *
     * @param Mage_Catalog_Model_Category $category
     * @return array
     */
    public function getChildrenCategories($category)
    {
//        $node = $this->getNodeById($category->getId());
//        if ($node && $node->getChildrenNodes()) {
//            return $node->getChildrenNodes();
//        }
        $categories = $this->_loadNodes($category, 1, $category->getStoreId());
        return $categories;
    }

    /**
     * Check is category in list of store categories
     *
     * @param Mage_Catalog_Model_Category $category
     * @return boolean
     */
    public function isInRootCategoryList($category)
    {
        $innerSelect = $this->_getReadAdapter()->select()
            ->from($this->getMainStoreTable($category->getStoreId()), new Zend_Db_Expr("CONCAT(path, '/%')"))
            ->where('entity_id = ?', Mage::app()->getStore()->getRootCategoryId());
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainStoreTable($category->getStoreId()), 'entity_id')
            ->where('entity_id = ?', $category->getId())
            ->where(new Zend_Db_Expr("path LIKE ({$innerSelect->__toString()})"));
        return (bool) $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Return children ids of category
     *
     * @param Mage_Catalog_Model_Category $category
     * @param integer $level
     * @return array
     */
    public function getChildren($category, $recursive = true, $isActive = true)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainStoreTable($category->getStoreId()), 'entity_id')
            ->where('path LIKE ?', "{$category->getPath()}/%");
        if (!$recursive) {
            $select->where('level <= ?', $category->getLevel() + 1);
        }
        if ($isActive) {
            $select->where('is_active = ?', '1');
        }
        $_categories = $this->_getReadAdapter()->fetchAll($select);
        $categoriesIds = array();
        foreach ($_categories as $_category) {
            $categoriesIds[] = $_category['entity_id'];
        }
        return $categoriesIds;
    }

    /**
     * Return all children ids of category (with category id)
     *
     * @param Mage_Catalog_Model_Category $category
     * @return array
     */
    public function getAllChildren($category)
    {
        $categoriesIds = $this->getChildren($category);
        $myId = array($category->getId());
        $categoriesIds = array_merge($myId, $categoriesIds);

        return $categoriesIds;
    }

/**
     * Check if category id exist
     *
     * @param   int $id
     * @return  bool
     */
    public function checkId($id)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainStoreTable(), 'entity_id')
            ->where('entity_id=?', $id);
        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Get design update data of parent categories
     *
     * @param Mage_Catalog_Model_Category $category
     * @return array
     */
    public function getDesignUpdateData($category)
    {
        $categories = array();
        $pathIds = array();
        foreach (array_reverse($category->getParentIds()) as $pathId) {
            if ($pathId == Mage::app()->getStore()->getRootCategoryId()) {
                $pathIds[] = $pathId;
                break;
            }
            $pathIds[] = $pathId;
        }
        $select = $this->_getReadAdapter()->select()
            ->from(
                array('main_table' => $this->getMainStoreTable($category->getStoreId())),
                array(
                    'main_table.entity_id',
                    'main_table.custom_design',
                    'main_table.custom_design_apply',
                    'main_table.custom_design_from',
                    'main_table.custom_design_to',
                )
            )
            ->where('main_table.entity_id IN (?)', $pathIds)
            ->where('main_table.is_active = ?', '1')
            ->order('main_table.path DESC');
        $result = $this->_getReadAdapter()->fetchAll($select);
        foreach ($result as $row) {
            $row['id'] = $row['entity_id'];
            $categories[$row['entity_id']] = Mage::getModel('catalog/category')->setData($row);
        }
        return $categories;
    }

    /**
     * Retrieve anchors above
     *
     * @param array $filterIds
     * @param int $storeId
     * @return array
     */
    public function getAnchorsAbove(array $filterIds, $storeId = 0)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('e' => $this->getMainStoreTable($storeId)), 'entity_id')
            ->where('is_anchor = ?', 1)
            ->where('entity_id IN (?)', $filterIds);

        return $this->_getReadAdapter()->fetchCol($select);
    }
}
