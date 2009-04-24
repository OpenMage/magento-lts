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
 * Product collection
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
    extends Mage_Catalog_Model_Resource_Eav_Mysql4_Collection_Abstract
{
    /**
     * Catalog Product Flat is enabled cache per store
     *
     * @var array
     */
    protected $_flatEnabled = array();

    /**
     * Product websites table name
     *
     * @var string
     */
    protected $_productWebsiteTable;

    /**
     * Product categories table name
     *
     * @var string
     */
    protected $_productCategoryTable;

    /**
     * Is add URL rewrites to collection flag
     *
     * @var bool
     */
    protected $_addUrlRewrite = false;

    /**
     * Add URL rewrite for category
     *
     * @var int
     */
    protected $_urlRewriteCategory = '';

    /**
     * Is add minimal price to product collection flag
     *
     * @var bool
     */
    protected $_addMinimalPrice = false;

    /**
     * Is add final price to product collection flag
     *
     * @var unknown_type
     */
    protected $_addFinalPrice = false;

    /**
     * Cache for all ids
     *
     * @var array
     */
    protected $_allIdsCache = null;

    /**
     * Is add tax percents to product collection flag
     *
     * @var bool
     */
    protected $_addTaxPercents = false;

    /**
     * Category index is a joined flag
     *
     * @var bool
     */
    protected $_categoryIndexJoined = false;

    /**
     * Category product count select
     *
     * @var Zend_Db_Select
     */
    protected $_productCountSelect = null;

    /**
     * Retrieve Catalog Product Flat Helper object
     *
     * @return Mage_Catalog_Helper_Product_Flat
     */
    public function getFlatHelper()
    {
        return Mage::helper('catalog/product_flat');
    }

    /**
     * Retrieve is flat enabled flag
     * Return alvays false if magento run admin
     *
     * @return bool
     */
    public function isEnabledFlat()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return false;
        }
        if (!isset($this->_flatEnabled[$this->getStoreId()])) {
            $this->_flatEnabled[$this->getStoreId()] = $this->getFlatHelper()
                ->isEnabled($this->getStoreId());
        }
        return $this->_flatEnabled[$this->getStoreId()];
    }

    /**
     * Initialize resources
     *
     */
    protected function _construct()
    {
        if ($this->isEnabledFlat()) {
            $this->_init('catalog/product', 'catalog/product_flat');
        }
        else {
            $this->_init('catalog/product');
        }

        $this->_productWebsiteTable = $this->getResource()->getTable('catalog/product_website');
        $this->_productCategoryTable= $this->getResource()->getTable('catalog/category_product');
    }

    /**
     * Standard resource collection initalization
     *
     * @param string $model
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    protected function _init($model, $entityModel=null)
    {
        if ($this->isEnabledFlat()) {
            $entityModel = 'catalog/product_flat';
        }

        return parent::_init($model, $entityModel);
    }

    /**
     * Prepare static entity fields
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    protected function _prepareStaticFields()
    {
        if ($this->isEnabledFlat()) {
            return $this;
        }
        return parent::_prepareStaticFields();
    }

    /**
     * Retrieve collection empty item
     * Redeclared for specifying id field name without getting resource model inside model
     *
     * @return Varien_Object
     */
    public function getNewEmptyItem()
    {
        $object = parent::getNewEmptyItem();
        if ($this->isEnabledFlat()) {
            $object->setIdFieldName($this->getEntity()->getIdFieldName());
        }
        return $object;
    }

    /**
     * Set entity to use for attributes
     *
     * @param Mage_Eav_Model_Entity_Abstract $entity
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function setEntity($entity)
    {
        if ($this->isEnabledFlat() && ($entity instanceof Mage_Core_Model_Mysql4_Abstract)) {
            $this->_entity = $entity;
            return $this;
        }
        return parent::setEntity($entity);
    }

    /**
     * Set Store scope for collection
     *
     * @param mixed $store
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function setStore($store)
    {
        parent::setStore($store);
        if ($this->isEnabledFlat()) {
            $this->getEntity()->setStoreId($this->getStoreId());
        }
        return $this;
    }

    /**
     * Initialize collection select
     * Redeclared for remove entity_type_id condition
     * in catalog_product_entity we store just products
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    protected function _initSelect()
    {
        if ($this->isEnabledFlat()) {
            $this->getSelect()
                ->from(array('e' => $this->getEntity()->getFlatTableName()), null)
                ->from(null, array('status' => new Zend_Db_Expr(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)));
            $this->addAttributeToSelect(array('entity_id', 'type_id', 'attribute_set_id'));
            if ($this->getFlatHelper()->isAddChildData()) {
                $this->getSelect()
                    ->where('e.is_child=?', 0);
                $this->addAttributeToSelect(array('child_id', 'is_child'));
            }
        }
        else {
            $this->getSelect()->from(array('e'=>$this->getEntity()->getEntityTable()));
        }
        return $this;
    }

    /**
     * Load attributes into loaded entities
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function _loadAttributes($printQuery = false, $logQuery = false)
    {
        if ($this->isEnabledFlat()) {
            return $this;
        }
        return parent::_loadAttributes($printQuery, $logQuery);
    }

    /**
     * Add attribute to entities in collection
     *
     * If $attribute=='*' select all attributes
     *
     * @param array|string|integer|Mage_Core_Model_Config_Element $attribute
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addAttributeToSelect($attribute, $joinType = false)
    {
        if ($this->isEnabledFlat()) {
            if (!is_array($attribute)) {
                $attribute = array($attribute);
            }
            foreach ($attribute as $attributeCode) {
                if ($attributeCode == '*') {
                    foreach ($this->getEntity()->getAllTableColumns() as $column) {
                        $this->getSelect()->from(null, 'e.'.$column);
                        $this->_selectAttributes[$column] = $column;
                        $this->_staticFields[$column] = $column;
                    }
                }
                else {
                    if ($columns = $this->getEntity()->getAttributeForSelect($attributeCode)) {
                        foreach ($columns as $alias => $column) {
                            $this->getSelect()->from(null, array($alias => 'e.'.$column));
                            $this->_selectAttributes[$column] = $column;
                            $this->_staticFields[$column] = $column;
                        }
                    }
                }
            }
            return $this;
        }
        return parent::addAttributeToSelect($attribute, $joinType);
    }

    /**
     * Add tax class id attribute to select and join price rules data if needed
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    protected function _beforeLoad()
    {
        if ($this->_addFinalPrice) {
            $this->_joinPriceRules();
        }
        Mage::dispatchEvent('catalog_product_collection_load_before', array('collection'=>$this));
        return parent::_beforeLoad();
    }

    /**
     * Processing collection items after loading
     * Adding url rewrites, minimal prices, final prices, tax percents
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    protected function _afterLoad()
    {
        if ($this->_addUrlRewrite) {
           $this->_addUrlRewrite($this->_urlRewriteCategory);
        }
        if ($this->_addMinimalPrice) {
           $this->_addMinimalPrice();
        }
        if ($this->_addFinalPrice) {
           $this->_addFinalPrice();
        }
        if (count($this)>0) {
            Mage::dispatchEvent('catalog_product_collection_load_after', array('collection'=>$this));
        }
        return $this;
    }

    /**
     * Add collection filters by identifiers
     *
     * @param   mixed $productId
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function addIdFilter($productId, $exclude = false)
    {
        if (empty($productId)) {
            $this->_setIsLoaded(true);
            return $this;
        }
        if (is_array($productId)) {
            if (!empty($productId)) {
                if ($exclude) {
                    $condition = array('nin'=>$productId);
                } else {
                    $condition = array('in'=>$productId);
                }
            }
            else {
                $condition = '';
            }
        }
        else {
            if ($exclude) {
                $condition = array('neq'=>$productId);
            } else {
                $condition = $productId;
            }
        }
        $this->addFieldToFilter('entity_id', $condition);
        return $this;
    }

    /**
     * Adding product website names to result collection
     * Add for each product websites information
     *
     * @return Mage_Catalog_Model_Entity_Product_Collection
     */
    public function addWebsiteNamesToResult()
    {
        $productStores = array();
        foreach ($this as $product) {
            $productWebsites[$product->getId()] = array();
        }

        if (!empty($productWebsites)) {
            $select = $this->getConnection()->select()
                ->from(array('product_website'=>$this->_productWebsiteTable))
                ->join(
                    array('website'=>$this->getResource()->getTable('core/website')),
                    'website.website_id=product_website.website_id',
                    array('name'))
                ->where($this->getConnection()->quoteInto(
                    'product_website.product_id IN (?)',
                    array_keys($productWebsites))
                )
                ->where('website.website_id>0');

            $data = $this->getConnection()->fetchAll($select);
            foreach ($data as $row) {
                $productWebsites[$row['product_id']][] = $row['website_id'];
            }
        }

        foreach ($this as $product) {
            if (isset($productWebsites[$product->getId()])) {
                $product->setData('websites', $productWebsites[$product->getId()]);
            }
        }
        return $this;
    }

    /**
     * Add store availability filter. Include availability product
     * for store website
     *
     * @param   mixed $store
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function addStoreFilter($store=null)
    {
        if (is_null($store)) {
            $store = $this->getStoreId();
        }
        $store = Mage::app()->getStore($store);

        if ($this->isEnabledFlat()) {
            if ($store->getId() != $this->getStoreId()) {
                $this->setStoreId($store);
            }
            return $this;
        }

        /**
         * For admin store we display all products
         */
        if (!$store->isAdmin()) {
            $websiteId = $store->getWebsite()->getId();
            $this->joinField('website_id', 'catalog/product_website', 'website_id', 'product_id=entity_id',
                    '{{table}}.website_id='.$websiteId
            );
        }
        return $this;
    }

    public function addWebsiteFilter($website=null)
    {
        if (is_null($website)) {
            return $this;
        }

        if (is_array($website)) {
            $this->joinField('website_id', 'catalog/product_website', 'website_id', 'product_id=entity_id',
                    '{{table}}.website_id IN('.$this->getConnection()->quoteInto('?', $website) . ')');
        }

        return $this;
    }

    /**
     * Specify category filter for product collection
     *
     * @param   Mage_Catalog_Model_Category $category
     * @param   bool $renderAlias instruction for build category table alias based on category id
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function addCategoryFilter(Mage_Catalog_Model_Category $category, $renderAlias=false)
    {
        if ($renderAlias) {
            $alias = 'cat_index_'.$category->getId();
        }
        else {
            $alias = 'cat_index';
        }

        $categoryCondition = $this->getConnection()->quoteInto(
            $alias.'.product_id=e.entity_id AND '.$alias.'.store_id=? AND ',
            $this->getStoreId()
        );

        if ($category->getIsAnchor()) {
            $categoryCondition.= $this->getConnection()->quoteInto(
                $alias.'.category_id=?',
                $category->getId()
            );
        } else {
            $categoryCondition.= $this->getConnection()->quoteInto(
                $alias.'.category_id=? AND '.$alias.'.is_parent=1',
                $category->getId()
            );
        }


        $this->getSelect()->joinInner(
            array($alias => $this->getTable('catalog/category_product_index')),
            $categoryCondition,
            array('position'=>'position')
        );
        $this->_categoryIndexJoined = true;
        $this->_joinFields['position'] = array('table'=>$alias, 'field'=>'position' );
//        $this->joinField(
//            $alias,
//            'catalog/category_product_index',
//            'position',
//            'product_id=entity_id',
//            $categoryCondition
//        );
        return $this;
    }

    /**
     * Join minimal price attribute to result
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function joinMinimalPrice()
    {
        $this->addAttributeToSelect('price')
            ->addAttributeToSelect('minimal_price');
        return $this;
    }

    /**
     * Retrieve max value by attribute
     *
     * @param   string $attribute
     * @return  mixed
     */
    public function getMaxAttributeValue($attribute)
    {
        $select     = clone $this->getSelect();
        $attribute  = $this->getEntity()->getAttribute($attribute);
        $attributeCode = $attribute->getAttributeCode();
        $tableAlias = $attributeCode.'_max_value';

        $condition  = 'e.entity_id='.$tableAlias.'.entity_id
            AND '.$this->_getConditionSql($tableAlias.'.attribute_id', $attribute->getId())
            //.' AND '.$this->_getConditionSql($tableAlias.'.store_id', $this->getEntity()->getStoreId())
            ;

        $select->join(
                array($tableAlias => $attribute->getBackend()->getTable()),
                $condition,
                array('max_'.$attributeCode=>new Zend_Db_Expr('MAX('.$tableAlias.'.value)'))
            )
            ->group('e.entity_type_id');

        $data = $this->getConnection()->fetchRow($select);
        if (isset($data['max_'.$attributeCode])) {
            return $data['max_'.$attributeCode];
        }
        return null;
    }

    /**
     * Retrieve ranging product count for arrtibute range
     *
     * @param   string $attribute
     * @param   int $range
     * @return  array
     */
    public function getAttributeValueCountByRange($attribute, $range)
    {
        $select     = clone $this->getSelect();
        $attribute  = $this->getEntity()->getAttribute($attribute);
        $attributeCode = $attribute->getAttributeCode();
        $tableAlias = $attributeCode.'_range_count_value';

        $condition  = 'e.entity_id='.$tableAlias.'.entity_id
            AND '.$this->_getConditionSql($tableAlias.'.attribute_id', $attribute->getId())
            //.' AND '.$this->_getConditionSql($tableAlias.'.store_id', $this->getEntity()->getStoreId())
            ;

        $select->reset(Zend_Db_Select::GROUP);
        $select->join(
                array($tableAlias => $attribute->getBackend()->getTable()),
                $condition,
                array(
                        'count_'.$attributeCode=>new Zend_Db_Expr('COUNT(DISTINCT e.entity_id)'),
                        'range_'.$attributeCode=>new Zend_Db_Expr('CEIL(('.$tableAlias.'.value+0.01)/'.$range.')')
                     )
            )
            ->group('range_'.$attributeCode);

        $data   = $this->getConnection()->fetchAll($select);
        $res    = array();

        foreach ($data as $row) {
            $res[$row['range_'.$attributeCode]] = $row['count_'.$attributeCode];
        }
        return $res;
    }

    /**
     * Retrieve product count by some value of attribute
     *
     * @param   string $attribute
     * @return  array($value=>$count)
     */
    public function getAttributeValueCount($attribute)
    {
        $select     = clone $this->getSelect();
        $attribute  = $this->getEntity()->getAttribute($attribute);
        $attributeCode = $attribute->getAttributeCode();
        $tableAlias = $attributeCode.'_value_count';

        $select->reset(Zend_Db_Select::GROUP);
        $condition  = 'e.entity_id='.$tableAlias.'.entity_id
            AND '.$this->_getConditionSql($tableAlias.'.attribute_id', $attribute->getId())
            //.' AND '.$this->_getConditionSql($tableAlias.'.store_id', $this->getEntity()->getStoreId())
            ;

        $select->join(
                array($tableAlias => $attribute->getBackend()->getTable()),
                $condition,
                array(
                        'count_'.$attributeCode=>new Zend_Db_Expr('COUNT(DISTINCT e.entity_id)'),
                        'value_'.$attributeCode=>new Zend_Db_Expr($tableAlias.'.value')
                     )
            )
            ->group('value_'.$attributeCode);

        $data   = $this->getConnection()->fetchAll($select);
        $res    = array();

        foreach ($data as $row) {
            $res[$row['value_'.$attributeCode]] = $row['count_'.$attributeCode];
        }
        return $res;
    }

    /**
     * Get SQL for get record count
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);

        $countSelect->from('', 'COUNT(DISTINCT e.entity_id)');
        $countSelect->resetJoinLeft();

        return $countSelect;
    }

    /**
     * Retrive all ids for collection
     *
     * @return array
     */
    public function getAllIds($limit=null, $offset=null)
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->from(null, 'e.'.$this->getEntity()->getIdFieldName());
        $idsSelect->limit($limit, $offset);
        $idsSelect->resetJoinLeft();

        return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
    }

    /**
     * Retreive product count select for categories
     *
     * @return Varien_Db_Select
     */
    public function getProductCountSelect()
    {
        if ($this->_productCountSelect === null) {
            $this->_productCountSelect = clone $this->getSelect();
            $this->_productCountSelect->reset(Zend_Db_Select::COLUMNS)
                ->reset(Zend_Db_Select::GROUP)
                ->reset(Zend_Db_Select::ORDER)
                ->distinct(false)
                ->join(array('count_table' => $this->getTable('catalog/category_product_index')),
                    'count_table.product_id = e.entity_id',
                    array('count_table.category_id', 'product_count' => new Zend_Db_Expr('COUNT(DISTINCT count_table.product_id)'))
                )
                ->where('count_table.store_id = ?', $this->getStoreId())
                ->group('count_table.category_id');
        }

        return $this->_productCountSelect;
    }

    /**
     * Destruct product count select
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function unsProductCountSelect()
    {
        $this->_productCountSelect = null;
        return $this;
    }

    /**
     * Adding product count to categories collection
     *
     * @param   Mage_Eav_Model_Entity_Collection_Abstract $categoryCollection
     * @return  Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addCountToCategories($categoryCollection)
    {
        $isAnchor = array();
        $isNotAnchor = array();
        foreach ($categoryCollection as $category) {
            if ($category->getIsAnchor()) {
                $isAnchor[] = $category->getId();
            } else {
                $isNotAnchor[] = $category->getId();
            }
        }
        $productCounts = array();
        if ($isAnchor || $isNotAnchor) {
            $select = $this->getProductCountSelect();

            Mage::dispatchEvent('catalog_product_collection_before_add_count_to_categories', array('collection'=>$this));

            if ($isAnchor) {
                $anchorStmt = clone $select;
                $anchorStmt->where('count_table.category_id in (?)', $isAnchor);
                $productCounts += $this->getConnection()->fetchPairs($anchorStmt, array('category_id'=>'product_count'));
                $anchorStmt = null;
            }
            if ($isNotAnchor) {
                $notAnchorStmt = clone $select;
                $notAnchorStmt->where('count_table.category_id in (?)', $isNotAnchor);
                $productCounts += $this->getConnection()->fetchPairs($notAnchorStmt, array('category_id'=>'product_count'));
                $notAnchorStmt = null;
            }
            $select = null;
            $this->unsProductCountSelect();
        }

        foreach ($categoryCollection as $category) {
            $_count = 0;
            if (isset($productCounts[$category->getId()])) {
                $_count = $productCounts[$category->getId()];
            }
            $category->setProductCount($_count);
        }
//        foreach ($categoryCollection as $category) {
//            $select     = clone $this->getSelect();
//            $select->reset(Zend_Db_Select::COLUMNS);
//            $select->reset(Zend_Db_Select::GROUP);
//            $select->reset(Zend_Db_Select::ORDER);
//            $select->distinct(false);
//            $select->join(
//                    array('category_count_table' => $this->_productCategoryTable),
//                    'category_count_table.product_id=e.entity_id',
//                    array('count_in_category'=>new Zend_Db_Expr('COUNT(DISTINCT e.entity_id)'))
//                );
//
//            if ($category->getIsAnchor()) {
//                $select->where($this->getConnection()->quoteInto(
//                    'category_count_table.category_id IN(?)',
//                    explode(',', $category->getAllChildren())
//                ));
//            }
//            else {
//                $select->where($this->getConnection()->quoteInto(
//                    'category_count_table.category_id=?',
//                    $category->getId()
//                ));
//            }
//            $category->setProductCount((int) $this->getConnection()->fetchOne($select));
//        }
        return $this;
    }

    public function getSetIds()
    {
        $select = clone $this->getSelect();
        /* @var $select Zend_Db_Select */
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->distinct(true);
        $select->from('', 'attribute_set_id');
        return $this->getConnection()->fetchCol($select);
    }

    /**
     * Joins url rewrite rules to collection
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection
     */
    public function joinUrlRewrite()
    {
        $this->joinTable(
            'core/url_rewrite',
            'entity_id=entity_id',
            array('request_path'),
            '{{table}}.type='.Mage_Core_Model_Url_Rewrite::TYPE_PRODUCT,
            'left'
        );

        return $this;
    }


    public function addUrlRewrite($categoryId = '')
    {
        $this->_addUrlRewrite = true;
        $this->_urlRewriteCategory = Mage::getStoreConfig('catalog/seo/product_use_categories') ? $categoryId : 0;
        return $this;
    }

    protected function _addUrlRewrite()
    {
        $urlRewrites = null;
        if ($this->_cacheConf) {
            if (!($urlRewrites = Mage::app()->loadCache($this->_cacheConf['prefix'].'urlrewrite'))) {
                $urlRewrites = null;
            } else {
                $urlRewrites = unserialize($urlRewrites);
            }
        }

        if (!$urlRewrites) {
            $productIds = array();
            foreach($this->getItems() as $item) {
                $productIds[] = $item->getEntityId();
            }
            if (!count($productIds)) {
                return;
            }

            $select = $this->getConnection()->select()
                ->from($this->getTable('core/url_rewrite'), array('product_id', 'request_path'))
                ->where('store_id=?', Mage::app()->getStore()->getId())
                ->where('is_system=?', 1)
                ->where('category_id=? OR category_id is NULL', $this->_urlRewriteCategory)
                ->where('product_id IN(?)', $productIds)
                ->order('category_id DESC'); // more priority is data with category id
            $urlRewrites = array();

            foreach ($this->getConnection()->fetchAll($select) as $row) {
                if (!isset($urlRewrites[$row['product_id']])) {
                    $urlRewrites[$row['product_id']] = $row['request_path'];
                }
            }

            if ($this->_cacheConf) {
                Mage::app()->saveCache(
                    serialize($urlRewrites),
                    $this->_cacheConf['prefix'].'urlrewrite',
                    array_merge($this->_cacheConf['tags'], array(Mage_Catalog_Model_Product_Url::CACHE_TAG)),
                    $this->_cacheLifetime
                );
            }
        }

        foreach($this->getItems() as $item) {
            if (isset($urlRewrites[$item->getEntityId()])) {
                $item->setData('request_path', $urlRewrites[$item->getEntityId()]);
            }
        }
    }

    public function addMinimalPrice()
    {
        $this->_addMinimalPrice = true;
        return $this;
    }

    protected function _addMinimalPrice()
    {
        Mage::getSingleton('catalogindex/price')->addMinimalPrices($this);
        return $this;
    }

    public function addFinalPrice()
    {
        $this->_addFinalPrice = true;
        $this->addAttributeToSelect('price')
            ->addAttributeToSelect('special_price')
            ->addAttributeToSelect('special_from_date')
            ->addAttributeToSelect('special_to_date');

        return $this;
    }

    /**
     * Join prices from price rules to products collection
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    protected function _joinPriceRules()
    {
        if ($this->isEnabledFlat()) {
            $customerGroup = Mage::getSingleton('customer/session')->getCustomerGroupId();
            $priceColumn = 'e.display_price_group_' . $customerGroup;
            $this->getSelect()->from(null, array('_rule_price' => $priceColumn));

            return $this;
        }
        $wId = Mage::app()->getWebsite()->getId();
        $gId = Mage::getSingleton('customer/session')->getCustomerGroupId();

        $storeDate = Mage::app()->getLocale()->storeTimeStamp($this->getStoreId());
        $conditions  = "_price_rule.product_id = e.entity_id AND ";
        $conditions .= "_price_rule.rule_date = '".$this->getResource()->formatDate($storeDate, false)."' AND ";
        $conditions .= "_price_rule.website_id = '{$wId}' AND ";
        $conditions .= "_price_rule.customer_group_id = '{$gId}'";

        $this->getSelect()->joinLeft(
            array('_price_rule'=>$this->getTable('catalogrule/rule_product_price')),
            $conditions,
            array('_rule_price'=>'rule_price')
        );
        return $this;
    }

    protected function _addFinalPrice()
    {
        foreach ($this->_items as $product) {
            $basePrice = $product->getPrice();
            $specialPrice = $product->getSpecialPrice();
            $specialPriceFrom = $product->getSpecialFromDate();
            $specialPriceTo = $product->getSpecialToDate();
            if ($this->isEnabledFlat()) {
                $rulePrice = null;
                if ($product->getData('_rule_price') != $basePrice) {
                    $rulePrice = $product->getData('_rule_price');
                }
            }
            else {
                $rulePrice = $product->getData('_rule_price');
            }

            $finalPrice = $product->getPriceModel()->calculatePrice(
                $basePrice,
                $specialPrice,
                $specialPriceFrom,
                $specialPriceTo,
                $rulePrice,
                null,
                null,
                $product->getId()
            );

            $product->setCalculatedFinalPrice($finalPrice);
        }
    }

    public function getAllIdsCache($resetCache = false)
    {
        $ids = null;
        if (!$resetCache) {
            $ids = $this->_allIdsCache;
        }

        if (is_null($ids)) {
            $ids = $this->getAllIds();
            $this->setAllIdsCache($ids);
        }

        return $ids;
    }

    public function setAllIdsCache($value)
    {
        $this->_allIdsCache = $value;
        return $this;
    }

    /**
     * Add attribute to filter
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract|string $attribute
     * @param array $condition
     * @param string $joinType
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function addAttributeToFilter($attribute, $condition=null, $joinType='inner')
    {
        if ($this->isEnabledFlat()) {
            if ($attribute instanceof Mage_Eav_Model_Entity_Attribute_Abstract) {
                $attribute = $attribute->getAttributeCode();
            }

            if (is_array($attribute)) {
                $sqlArr = array();
                foreach ($attribute as $condition) {
                    $sqlArr[] = $this->_getAttributeConditionSql($condition['attribute'], $condition, $joinType);
                }
                $conditionSql = '('.join(') OR (', $sqlArr).')';
                $this->getSelect()->where($conditionSql);
                return $this;
            }

            if (!isset($this->_selectAttributes[$attribute])) {
                $this->addAttributeToSelect($attribute);
            }

            if (isset($this->_selectAttributes[$attribute])) {
                $this->getSelect()->where($this->_getConditionSql('e.'.$attribute, $condition));
            }

            return $this;
        }

        $this->_allIdsCache = null;
        if (is_string($attribute) && $attribute == 'is_saleable') {
            return $this->getSelect()->where($this->_getConditionSql('(IF(manage_stock, is_in_stock, 1))', $condition));
        }
        else {
            return parent::addAttributeToFilter($attribute, $condition, $joinType);
        }
    }

    /**
     * Add requere tax percent flag for product collection
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function addTaxPercents()
    {
        $this->_addTaxPercents = true;
        return $this;
    }

    /**
     * Get require tax percent flag value
     *
     * @return bool
     */
    public function requireTaxPercent()
    {
        return $this->_addTaxPercents;
    }

    /**
     * @deprecated from 1.3.0
     */
    protected function _addTaxPercents()
    {
        $classToRate = array();
        $request = Mage::getSingleton('tax/calculation')->getRateRequest();
        foreach ($this as &$item) {
            if (null === $item->getTaxClassId()) {
                $item->setTaxClassId($item->getMinimalTaxClassId());
            }
            if (!isset($classToRate[$item->getTaxClassId()])) {
                $request->setProductClassId($item->getTaxClassId());
                $classToRate[$item->getTaxClassId()] = Mage::getSingleton('tax/calculation')->getRate($request);
            }
            $item->setTaxPercent($classToRate[$item->getTaxClassId()]);
        }
    }

    /**
     * Adding product custom options to result collection
     *
     * @return Mage_Catalog_Model_Entity_Product_Collection
     */
    public function addOptionsToResult()
    {
        $productIds = array();
        foreach ($this as $product) {
            $productIds[] = $product->getId();
        }
        if (!empty($productIds)) {
            $options = Mage::getModel('catalog/product_option')
                ->getCollection()
                ->addTitleToResult(Mage::app()->getStore()->getId())
                ->addPriceToResult(Mage::app()->getStore()->getId())
                ->addProductToFilter($productIds)
                ->addValuesToResult();

            foreach ($options as $option) {
                if($this->getItemById($option->getProductId())) {
                    $this->getItemById($option->getProductId())->addOption($option);
                }
            }
        }

        return $this;
    }

    /**
     * Filter products with required options
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function addFilterByRequiredOptions()
    {
        $this->addAttributeToFilter('required_options', array(array('neq'=>'1'), array('null'=>true)), 'left');
        return $this;
    }

    /**
     * Set product visibility filter for enabled products
     *
     * @param   array $visibility
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function setVisibility($visibility)
    {
        if ($this->_categoryIndexJoined) {
            $this->getSelect()->where('cat_index.visibility IN (?)', $visibility);
        } else {
            $condition = $this->getConnection()->quoteInto('enabled_index.visibility IN (?)', $visibility);
            $storeCondition = $this->getConnection()->quoteInto('enabled_index.store_id=?', $this->getStoreId());
            $this->getSelect()->join(
                array('enabled_index'=>$this->getTable('catalog/product_enabled_index')),
                'enabled_index.product_id=e.entity_id AND '.$storeCondition.' AND '.$condition,
                array()
            );
        }

        Mage::dispatchEvent('catalog_product_collection_set_visibility_after', array('collection' => $this));

        return $this;
    }

    public function setOrder($attribute, $dir='desc')
    {
        $storeId = Mage::app()->getStore()->getId();
        $websiteId = Mage::app()->getStore()->getWebsiteId();

        if ($attribute == 'price' && $storeId != 0) {
            $customerGroup = Mage::getSingleton('customer/session')->getCustomerGroupId();
            if ($this->isEnabledFlat()) {
                $priceColumn = 'e.display_price_group_' . $customerGroup;
                $this->getSelect()->order("{$priceColumn} {$dir}");
            }
            else {
                $priceAttributeId = $this->getAttribute('price')->getId();

                $entityCondition = '_price_order_table.entity_id = e.entity_id';
                $storeCondition = $this->getConnection()->quoteInto(
                    '_price_order_table.website_id = ?',
                    $websiteId
                );
                $groupCondition = $this->getConnection()->quoteInto(
                    '_price_order_table.customer_group_id = ?',
                    $customerGroup
                );
                $attributeCondition = $this->getConnection()->quoteInto(
                    '_price_order_table.attribute_id = ?',
                    $priceAttributeId
                );

                $this->getSelect()->joinLeft(
                    array('_price_order_table'=>$this->getTable('catalogindex/price')),
                    "{$entityCondition} AND {$storeCondition} AND {$groupCondition} AND {$attributeCondition}",
                    array()
                );
                $this->getSelect()->order('_price_order_table.value ' . $dir);

                /**
                 * Distinct we are using for remove duplicates of products which have
                 * several rows in price index (like grouped products)
                 */
                $this->getSelect()->distinct(true);
            }
        } else {
            if ($this->isEnabledFlat()) {
                if ($attribute == 'position') {
                    $this->getSelect()->order("{$attribute} {$dir}");
                }
                elseif ($sortColumn = $this->getEntity()->getAttributeSortColumn($attribute)) {
                    $this->getSelect()->order("e.{$sortColumn} {$dir}");
                }
            }
            else {
                parent::setOrder($attribute, $dir);
            }
        }

        return $this;
    }

    /**
     * Add attribute to sort order
     *
     * @param string $attribute
     * @param string $dir
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function addAttributeToSort($attribute, $dir='asc')
    {
        if ($this->isEnabledFlat()) {
            if ($attribute == 'position') {
                $column = $attribute;
            }
            else {
                $column = $this->getEntity()->getAttributeSortColumn($attribute);
            }

            if ($column) {
                $this->getSelect()->order("{$column} {$dir}");
            }

            return $this;
        }

        if ($attribute !== 'position') {
            $attrInstance = $this->getEntity()->getAttribute($attribute);
            if ($attrInstance && $attrInstance->usesSource()) {
                $attrInstance->getSource()
                    ->addValueSortToCollection($this, $dir);
                return $this;
            }
        }

        return parent::addAttributeToSort($attribute, $dir);
    }
}
