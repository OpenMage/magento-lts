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
    protected $_productWebsiteTable;
    protected $_productCategoryTable;

    protected $_addUrlRewrite = false;
    protected $_urlRewriteCategory = '';

    protected $_addMinimalPrice = false;
    protected $_addFinalPrice = false;
    protected $_allIdsCache = null;
    protected $_addTaxPercents = false;

    /**
     * Initialize resources
     */
    protected function _construct()
    {
        $this->_init('catalog/product');
        $this->_productWebsiteTable = $this->getResource()->getTable('catalog/product_website');
        $this->_productCategoryTable= $this->getResource()->getTable('catalog/category_product');
    }

    protected function _beforeLoad()
    {
        if ($this->_addFinalPrice) {
            $this->_joinPriceRules();
        }
        $this->addAttributeToSelect('tax_class_id');

        parent::_beforeLoad();
    }

    /**
     * Processing collection items after loading
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
        if ($this->_addTaxPercents) {
            $this->_addTaxPercents();
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

        if (!$store) {
            return $this;
        }

        if ($store instanceof Mage_Core_Model_Store) {
            $websiteId = $store->getWebsite()->getId();
        }
        else {
            $websiteId = Mage::app()->getStore($store)->getWebsite()->getId();
        }
        $this->joinField('website_id', 'catalog/product_website', 'website_id', 'product_id=entity_id',
                '{{table}}.website_id='.$websiteId
        );
        return $this;
    }

    public function addCategoryFilter(Mage_Catalog_Model_Category $category, $renderAlias=false)
    {
        if ($category->getIsAnchor()) {
            $categoryCondition = $this->getConnection()->quoteInto('{{table}}.category_id=?', $category->getId());
        }
        else {
            $categoryCondition = $this->getConnection()->quoteInto('{{table}}.category_id=? AND {{table}}.is_parent=1', $category->getId());
        }

//        if ($category->getIsAnchor()) {
//            $categoryCondition = $this->getConnection()->quoteInto('{{table}}.category_id IN (?)', explode(',', $category->getAllChildren()));
//            $this->getSelect()->group('e.entity_id');
//        }
//        else {
//            $categoryCondition = $this->getConnection()->quoteInto('{{table}}.category_id=?', $category->getId());
//        }
        if ($renderAlias) {
            $alias = 'category_'.$category->getId();
        }
        else {
            $alias = 'position';
        }

        $this->joinField($alias, 'catalog/category_product_index', 'position', 'product_id=entity_id', $categoryCondition);
        return $this;
    }

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
     * Render SQL for retrieve product count
     *
     * @return string
     */
    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::GROUP);

        $sql = $countSelect->__toString();
        $sql = preg_replace('/^select\s+.+?\s+from\s+/is', 'select count(DISTINCT e.entity_id) from ', $sql);
        return $sql;
    }

    /**
     * Adding product count to categories collection
     *
     * @param   Mage_Eav_Model_Entity_Collection_Abstract $categoryCollection
     * @return  Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addCountToCategories($categoryCollection)
    {
        foreach ($categoryCollection as $category) {
            $select     = clone $this->getSelect();
            $select->reset(Zend_Db_Select::COLUMNS);
            $select->reset(Zend_Db_Select::GROUP);
            $select->distinct(false);
            $select->join(
                    array('category_count_table' => $this->_productCategoryTable),
                    'category_count_table.product_id=e.entity_id',
                    array('count_in_category'=>new Zend_Db_Expr('COUNT(DISTINCT e.entity_id)'))
                );

            if ($category->getIsAnchor()) {
                $select->where($this->getConnection()->quoteInto('category_count_table.category_id IN(?)', explode(',', $category->getAllChildren())));
            }
            else {
                $select->where($this->getConnection()->quoteInto('category_count_table.category_id=?', $category->getId()));
            }

            $category->setProductCount((int) $this->getConnection()->fetchOne($select));
        }
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
        $this->joinTable('core/url_rewrite', 'entity_id=entity_id', array('request_path'), '{{table}}.type='.Mage_Core_Model_Url_Rewrite::TYPE_PRODUCT, 'left');

        return $this;
    }


    public function addUrlRewrite($categoryId = '')
    {
        $this->_addUrlRewrite = true;
        //~ $this->_urlRewriteCategory = $categoryId;
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

    protected function _joinPriceRules()
    {
        $wId = Mage::app()->getWebsite()->getId();
        $gId = Mage::getSingleton('customer/session')->getCustomerGroupId();

        $conditions  = "_price_rule.product_id = e.entity_id AND ";
        $conditions .= "_price_rule.rule_date = '".date('Y-m-d H:i:s', mktime(0, 0, 0))."' AND ";
        $conditions .= "_price_rule.website_id = '{$wId}' AND ";
        $conditions .= "_price_rule.customer_group_id = '{$gId}'";

        //$productIds = $this->getAllIds();
        $productIds = $this->getAllIdsCache();
        $this->getSelect()
            ->joinLeft(array('_price_rule'=>$this->getTable('catalogrule/rule_product_price')), $conditions, array('_rule_price'=>'rule_price'));
    }

    protected function _addFinalPrice()
    {
        foreach ($this->_items as $product) {
            $basePrice = $product->getPrice();
            $specialPrice = $product->getSpecialPrice();
            $specialPriceFrom = $product->getSpecialFromDate();
            $specialPriceTo = $product->getSpecialToDate();
            $rulePrice = $product->getData('_rule_price');

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

    public function getAllIdsCache($resetCache = false){
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

    public function setAllIdsCache($value){
        $this->_allIdsCache = $value;
        return $this;
    }

    public function addAttributeToFilter($attribute, $condition=null, $joinType='inner'){
        $this->_allIdsCache = null;
        return parent::addAttributeToFilter($attribute, $condition, $joinType);
    }

    public function addTaxPercents(){
        $this->_addTaxPercents = true;
        $this->addAttributeToSelect('tax_class_id');

        return $this;
    }

    protected function _addTaxPercents(){
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
     * Set cillection product visibility filter for enabled products
     *
     * @param   array $visibility
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function setVisibility($visibility)
    {
        $condition = $this->getConnection()->quoteInto('enabled_index.visibility IN (?)', $visibility);
        $storeCondition = $this->getConnection()->quoteInto('enabled_index.store_id=?', $this->getStoreId());
        $this->getSelect()->join(
            array('enabled_index'=>$this->getTable('catalog/product_enabled_index')),
            'enabled_index.product_id=e.entity_id AND '.$storeCondition.' AND '.$condition,
            array()
        );
        return $this;
    }

    public function setOrder($attribute, $dir='desc')
    {
        $storeId = Mage::app()->getStore()->getId();
        if ($attribute == 'price' && $storeId != 0) {
            $customerGroup = Mage::getSingleton('customer/session')->getCustomer()->getCustomerGroupId();

            $priceAttributeId = $this->getAttribute('price')->getId();

            $entityCondition = '_price_order_table.entity_id = e.entity_id';
            $storeCondition = $this->getConnection()->quoteInto('_price_order_table.store_id = ?', $storeId);
            $groupCondition = $this->getConnection()->quoteInto('_price_order_table.customer_group_id = ?', $customerGroup);
            $attributeCondition = $this->getConnection()->quoteInto('_price_order_table.attribute_id = ?', $priceAttributeId);

            $this->getSelect()->joinLeft(
                array('_price_order_table'=>$this->getTable('catalogindex/price')),
                "{$entityCondition} AND {$storeCondition} AND {$groupCondition} AND {$attributeCondition}",
                array()
            );
            $this->getSelect()->order('_price_order_table.value ' . $dir);
            $this->getSelect()->group('e.entity_id');
        } else {
            parent::setOrder($attribute, $dir);
        }

        return $this;
    }
}