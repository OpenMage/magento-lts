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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogRule
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog rules resource model
 *
 * @category    Mage
 * @package     Mage_CatalogRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogRule_Model_Resource_Rule extends Mage_Rule_Model_Resource_Abstract
{
    /**
     * Store number of seconds in a day
     */
    const SECONDS_IN_DAY = 86400;

    /**
     * Number of products in range for insert
     */
    const RANGE_PRODUCT_STEP = 1000000;

    /**
     * Store associated with rule entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = array(
        'website' => array(
            'associations_table' => 'catalogrule/website',
            'rule_id_field'      => 'rule_id',
            'entity_id_field'    => 'website_id'
        ),
        'customer_group' => array(
            'associations_table' => 'catalogrule/customer_group',
            'rule_id_field'      => 'rule_id',
            'entity_id_field'    => 'customer_group_id'
        )
    );

    /**
     * Factory instance
     *
     * @var Mage_Core_Model_Factory
     */
    protected $_factory;

    /**
     * App instance
     *
     * @var Mage_Core_Model_App
     */
    protected $_app;

    /**
     * Constructor with parameters
     * Array of arguments with keys
     *  - 'factory' Mage_Core_Model_Factory
     *
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        $this->_factory = !empty($args['factory']) ? $args['factory'] : Mage::getSingleton('core/factory');
        $this->_app     = !empty($args['app']) ? $args['app'] : Mage::app();

        parent::__construct();
    }

    /**
     * Initialize main table and table id field
     */
    protected function _construct()
    {
        $this->_init('catalogrule/rule', 'rule_id');
    }

    /**
     * Add customer group ids and website ids to rule data after load
     *
     * @param Mage_Core_Model_Abstract $object
     *
     * @return Mage_CatalogRule_Model_Resource_Rule
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $object->setData('customer_group_ids', (array)$this->getCustomerGroupIds($object->getId()));
        $object->setData('website_ids', (array)$this->getWebsiteIds($object->getId()));

        return parent::_afterLoad($object);
    }

    /**
     * Bind catalog rule to customer group(s) and website(s).
     * Update products which are matched for rule.
     *
     * @param Mage_Core_Model_Abstract $object
     *
     * @return Mage_CatalogRule_Model_Resource_Rule
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->hasWebsiteIds()) {
            $websiteIds = $object->getWebsiteIds();
            if (!is_array($websiteIds)) {
                $websiteIds = explode(',', (string)$websiteIds);
            }
            $this->bindRuleToEntity($object->getId(), $websiteIds, 'website');
        }

        if ($object->hasCustomerGroupIds()) {
            $customerGroupIds = $object->getCustomerGroupIds();
            if (!is_array($customerGroupIds)) {
                $customerGroupIds = explode(',', (string)$customerGroupIds);
            }
            $this->bindRuleToEntity($object->getId(), $customerGroupIds, 'customer_group');
        }

        parent::_afterSave($object);
        return $this;
    }

    /**
     * Deletes records in catalogrule/product_data by rule ID and product IDs
     *
     * @param int $ruleId
     * @param array $productIds
     */
    public function cleanProductData($ruleId, array $productIds = array())
    {
        /** @var $write Varien_Db_Adapter_Interface */
        $write = $this->_getWriteAdapter();

        $conditions = array('rule_id = ?' => $ruleId);

        if (count($productIds) > 0) {
            $conditions['product_id IN (?)'] = $productIds;
        }

        $write->delete($this->getTable('catalogrule/rule_product'), $conditions);
    }

    /**
     * Return whether the product fits the rule
     *
     * @param Mage_CatalogRule_Model_Rule $rule
     * @param Varien_Object $product
     * @param array $websiteIds
     * @return bool
     */
    public function validateProduct(Mage_CatalogRule_Model_Rule $rule, Varien_Object $product, $websiteIds = array())
    {
        /** @var $helper Mage_Catalog_Helper_Product_Flat */
        $helper = $this->_factory->getHelper('catalog/product_flat');
        if ($helper->isEnabled() && $helper->isBuiltAllStores()) {
            /** @var $store Mage_Core_Model_Store */
            foreach ($this->_app->getStores(false) as $store) {
                if (count($websiteIds) == 0 || in_array($store->getWebsiteId(), $websiteIds)) {
                    /** @var $selectByStore Varien_Db_Select */
                    $selectByStore = $rule->getProductFlatSelect($store->getId());
                    $selectByStore->where('p.entity_id = ?', $product->getId());
                    $selectByStore->limit(1);
                    if ($this->_getReadAdapter()->fetchOne($selectByStore)) {
                        return true;
                    }
                }
            }
            return false;
        } else {
            return $rule->getConditions()->validate($product);
        }
    }

    /**
     * Inserts rule data into catalogrule/rule_product table
     *
     * @param Mage_CatalogRule_Model_Rule $rule
     * @param array $websiteIds
     * @param array $productIds
     */
    public function insertRuleData(Mage_CatalogRule_Model_Rule $rule, array $websiteIds, array $productIds = array())
    {
        /** @var $write Varien_Db_Adapter_Interface */
        $write = $this->_getWriteAdapter();

        $customerGroupIds = $rule->getCustomerGroupIds();

        $fromTime = (int) strtotime($rule->getFromDate());
        $toTime = (int) strtotime($rule->getToDate());
        $toTime = $toTime ? ($toTime + self::SECONDS_IN_DAY - 1) : 0;

        /** @var Mage_Core_Model_Date $coreDate */
        $coreDate  = $this->_factory->getModel('core/date');
        $timestamp = $coreDate->gmtTimestamp('Today');
        if ($fromTime > $timestamp
            || ($toTime && $toTime < $timestamp)
        ) {
            return;
        }
        $sortOrder = (int) $rule->getSortOrder();
        $actionOperator = $rule->getSimpleAction();
        $actionAmount = (float) $rule->getDiscountAmount();
        $subActionOperator = $rule->getSubIsEnable() ? $rule->getSubSimpleAction() : '';
        $subActionAmount = (float) $rule->getSubDiscountAmount();
        $actionStop = (int) $rule->getStopRulesProcessing();
        /** @var $helper Mage_Catalog_Helper_Product_Flat */
        $helper = $this->_factory->getHelper('catalog/product_flat');

        if ($helper->isEnabled() && $helper->isBuiltAllStores()) {
            /** @var $store Mage_Core_Model_Store */
            foreach ($this->_app->getStores(false) as $store) {
                if (in_array($store->getWebsiteId(), $websiteIds)) {
                    /** @var $selectByStore Varien_Db_Select */
                    $selectByStore = $rule->getProductFlatSelect($store->getId())
                        ->joinLeft(array('cg' => $this->getTable('customer/customer_group')),
                            $write->quoteInto('cg.customer_group_id IN (?)', $customerGroupIds),
                            array('cg.customer_group_id'))
                        ->reset(Varien_Db_Select::COLUMNS)
                        ->columns(array(
                            new Zend_Db_Expr($store->getWebsiteId()),
                            'cg.customer_group_id',
                            'p.entity_id',
                            new Zend_Db_Expr($rule->getId()),
                            new Zend_Db_Expr($fromTime),
                            new Zend_Db_Expr($toTime),
                            new Zend_Db_Expr("'" . $actionOperator . "'"),
                            new Zend_Db_Expr($actionAmount),
                            new Zend_Db_Expr($actionStop),
                            new Zend_Db_Expr($sortOrder),
                            new Zend_Db_Expr("'" . $subActionOperator . "'"),
                            new Zend_Db_Expr($subActionAmount),
                        ));

                    if (count($productIds) > 0) {
                        $selectByStore->where('p.entity_id IN (?)', array_keys($productIds));
                    }

                    $selects = $write->selectsByRange('entity_id', $selectByStore, self::RANGE_PRODUCT_STEP);
                    foreach ($selects as $select) {
                        $write->query(
                            $write->insertFromSelect(
                                $select, $this->getTable('catalogrule/rule_product'), array(
                                    'website_id',
                                    'customer_group_id',
                                    'product_id',
                                    'rule_id',
                                    'from_time',
                                    'to_time',
                                    'action_operator',
                                    'action_amount',
                                    'action_stop',
                                    'sort_order',
                                    'sub_simple_action',
                                    'sub_discount_amount',
                                ), Varien_Db_Adapter_Interface::INSERT_IGNORE
                            )
                        );
                    }
                }
            }
        } else {
            if (count($productIds) == 0) {
                Varien_Profiler::start('__MATCH_PRODUCTS__');
                $productIds = $rule->getMatchingProductIds();
                Varien_Profiler::stop('__MATCH_PRODUCTS__');
            }

            $rows = array();
            foreach ($productIds as $productId => $validationByWebsite) {
                foreach ($websiteIds as $websiteId) {
                    foreach ($customerGroupIds as $customerGroupId) {
                        if (empty($validationByWebsite[$websiteId])) {
                            continue;
                        }
                        $rows[] = array(
                            'rule_id'             => $rule->getId(),
                            'from_time'           => $fromTime,
                            'to_time'             => $toTime,
                            'website_id'          => $websiteId,
                            'customer_group_id'   => $customerGroupId,
                            'product_id'          => $productId,
                            'action_operator'     => $actionOperator,
                            'action_amount'       => $actionAmount,
                            'action_stop'         => $actionStop,
                            'sort_order'          => $sortOrder,
                            'sub_simple_action'   => $subActionOperator,
                            'sub_discount_amount' => $subActionAmount,
                        );

                        if (count($rows) == 1000) {
                            $write->insertMultiple($this->getTable('catalogrule/rule_product'), $rows);
                            $rows = array();
                        }
                    }
                }
            }

            if (!empty($rows)) {
                $write->insertMultiple($this->getTable('catalogrule/rule_product'), $rows);
            }
        }
    }

    /**
     * Update products which are matched for rule
     *
     * @param Mage_CatalogRule_Model_Rule $rule
     *
     * @throws Exception
     * @return Mage_CatalogRule_Model_Resource_Rule
     */
    public function updateRuleProductData(Mage_CatalogRule_Model_Rule $rule)
    {
        $ruleId = $rule->getId();
        $write  = $this->_getWriteAdapter();
        $write->beginTransaction();
        if ($rule->getProductsFilter()) {
            $this->cleanProductData($ruleId, $rule->getProductsFilter());
        } else {
            $this->cleanProductData($ruleId);
        }

        if (!$rule->getIsActive()) {
            $write->commit();
            return $this;
        }

        $websiteIds = $rule->getWebsiteIds();
        if (!is_array($websiteIds)) {
            $websiteIds = explode(',', $websiteIds);
        }
        if (empty($websiteIds)) {
            return $this;
        }

        try {
            $this->insertRuleData($rule, $websiteIds);
            $write->commit();
        } catch (Exception $e) {
            $write->rollback();
            throw $e;
        }

        return $this;
    }

    /**
     * Get all product ids matched for rule
     *
     * @param int $ruleId
     *
     * @return array
     */
    public function getRuleProductIds($ruleId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getTable('catalogrule/rule_product'), 'product_id')
            ->where('rule_id=?', $ruleId);

        return $read->fetchCol($select);
    }

    /**
     * Remove catalog rules product prices for specified date range and product
     *
     * @param int|string $fromDate
     * @param int|string $toDate
     * @param int|null $productId
     *
     * @return Mage_CatalogRule_Model_Resource_Rule
     */
    public function removeCatalogPricesForDateRange($fromDate, $toDate, $productId = null)
    {
        $write = $this->_getWriteAdapter();
        $conds = array();
        $cond = $write->quoteInto('rule_date between ?', $this->formatDate($fromDate));
        $cond = $write->quoteInto($cond.' and ?', $this->formatDate($toDate));
        $conds[] = $cond;
        if (!is_null($productId)) {
            $conds[] = $write->quoteInto('product_id=?', $productId);
        }

        /**
         * Add information about affected products
         * It can be used in processes which related with product price (like catalog index)
         */
        $select = $this->_getWriteAdapter()->select()
            ->from($this->getTable('catalogrule/rule_product_price'), 'product_id')
            ->where(implode(' AND ', $conds))
            ->group('product_id');

        $replace = $write->insertFromSelect(
            $select,
            $this->getTable('catalogrule/affected_product'),
            array('product_id'),
            true
        );
        $write->query($replace);
        $write->delete($this->getTable('catalogrule/rule_product_price'), $conds);
        return $this;
    }

    /**
     * Delete old price rules data
     *
     * @param string $date
     * @param int|null $productId
     *
     * @return Mage_CatalogRule_Model_Resource_Rule
     */
    public function deleteOldData($date, $productId = null)
    {
        $write = $this->_getWriteAdapter();
        $conds = array();
        $conds[] = $write->quoteInto('rule_date<?', $this->formatDate($date));
        if (!is_null($productId)) {
            $conds[] = $write->quoteInto('product_id=?', $productId);
        }
        $write->delete($this->getTable('catalogrule/rule_product_price'), $conds);
        return $this;
    }

    /**
     * Get DB resource statement for processing query result
     *
     * @param int $fromDate
     * @param int $toDate
     * @param int|null $productId
     * @param int|null $websiteId
     *
     * @return Zend_Db_Statement_Interface
     */
    protected function _getRuleProductsStmt($fromDate, $toDate, $productId = null, $websiteId = null)
    {
        $read = $this->_getReadAdapter();
        /**
         * Sort order is important
         * It used for check stop price rule condition.
         * website_id   customer_group_id   product_id  sort_order
         *  1           1                   1           0
         *  1           1                   1           1
         *  1           1                   1           2
         * if row with sort order 1 will have stop flag we should exclude
         * all next rows for same product id from price calculation
         */
        $select = $read->select()
            ->from(array('rp' => $this->getTable('catalogrule/rule_product')))
            ->where($read->quoteInto('rp.from_time = 0 or rp.from_time <= ?', $toDate)
            . ' OR ' . $read->quoteInto('rp.to_time = 0 or rp.to_time >= ?', $fromDate))
            ->order(array('rp.website_id', 'rp.customer_group_id', 'rp.product_id', 'rp.sort_order', 'rp.rule_id'));

        if (!is_null($productId)) {
            $select->where('rp.product_id=?', $productId);
        }

        /**
         * Join default price and websites prices to result
         */
        $priceAttr  = Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'price');
        $priceTable = $priceAttr->getBackend()->getTable();
        $attributeId= $priceAttr->getId();

        $joinCondition = '%1$s.entity_id=rp.product_id AND (%1$s.attribute_id=' . $attributeId
            . ') and %1$s.store_id=%2$s';

        $select->join(
            array('pp_default'=>$priceTable),
            sprintf($joinCondition, 'pp_default', Mage_Core_Model_App::ADMIN_STORE_ID),
            array('default_price'=>'pp_default.value')
        );

        if ($websiteId !== null) {
            $website  = Mage::app()->getWebsite($websiteId);
            $defaultGroup = $website->getDefaultGroup();
            if ($defaultGroup instanceof Mage_Core_Model_Store_Group) {
                $storeId = $defaultGroup->getDefaultStoreId();
            } else {
                $storeId = Mage_Core_Model_App::ADMIN_STORE_ID;
            }

            $select->joinInner(
                array('product_website' => $this->getTable('catalog/product_website')),
                'product_website.product_id=rp.product_id ' .
                'AND rp.website_id=product_website.website_id ' .
                'AND product_website.website_id='.$websiteId,
                array()
            );

            $tableAlias = 'pp'.$websiteId;
            $fieldAlias = 'website_'.$websiteId.'_price';
            $select->joinLeft(
                array($tableAlias=>$priceTable),
                sprintf($joinCondition, $tableAlias, $storeId),
                array($fieldAlias=>$tableAlias.'.value')
            );
        } else {
            foreach (Mage::app()->getWebsites() as $website) {
                $websiteId  = $website->getId();
                $defaultGroup = $website->getDefaultGroup();
                if ($defaultGroup instanceof Mage_Core_Model_Store_Group) {
                    $storeId = $defaultGroup->getDefaultStoreId();
                } else {
                    $storeId = Mage_Core_Model_App::ADMIN_STORE_ID;
                }

                $tableAlias = 'pp' . $websiteId;
                $fieldAlias = 'website_' . $websiteId . '_price';
                $select->joinLeft(
                    array($tableAlias => $priceTable),
                    sprintf($joinCondition, $tableAlias, $storeId),
                    array($fieldAlias => $tableAlias.'.value')
                );
            }
        }

        return $read->query($select);
    }

    /**
     * Generate catalog price rules prices for specified date range
     * If from date is not defined - will be used previous day by UTC
     * If to date is not defined - will be used next day by UTC
     *
     * @param int|Mage_Catalog_Model_Product $product
     *
     * @throws Exception
     * @return Mage_CatalogRule_Model_Resource_Rule
     */
    public function applyAllRules($product = null)
    {
        $this->_reindexCatalogRule($product);
        return $this;
    }

    /**
     * Generate catalog price rules prices for specified date range
     * If from date is not defined - will be used previous day by UTC
     * If to date is not defined - will be used next day by UTC
     *
     * @param int|string|null $fromDate
     * @param int|string|null $toDate
     * @param int $productId
     *
     * @deprecated after 1.7.0.2 use method applyAllRules
     *
     * @return Mage_CatalogRule_Model_Resource_Rule
     */
    public function applyAllRulesForDateRange($fromDate = null, $toDate = null, $productId = null)
    {
        return $this->applyAllRules($productId);
    }

    /**
     * Run reindex
     *
     * @param int|Mage_Catalog_Model_Product $product
     */
    protected function _reindexCatalogRule($product = null)
    {
        $indexerCode = 'catalogrule/action_index_refresh';
        $value = null;
        if ($product) {
            $value = $product instanceof Mage_Catalog_Model_Product ? $product->getId() : $product;
            $indexerCode = 'catalogrule/action_index_refresh_row';
        }

        /** @var $indexer Mage_CatalogRule_Model_Action_Index_Refresh */
        $indexer = Mage::getModel(
            $indexerCode,
            array(
                'connection' => $this->_getWriteAdapter(),
                'factory'    => Mage::getModel('core/factory'),
                'resource'   => $this,
                'app'        => Mage::app(),
                'value'      => $value
            )
        );
        $indexer->execute();
    }

    /**
     * Calculate product price based on price rule data and previous information
     *
     * @param array $ruleData
     * @param null|array $productData
     *
     * @return float
     */
    protected function _calcRuleProductPrice($ruleData, $productData = null)
    {
        if ($productData !== null && isset($productData['rule_price'])) {
            $productPrice = $productData['rule_price'];
        } else {
            $websiteId = $ruleData['website_id'];
            if (isset($ruleData['website_'.$websiteId.'_price'])) {
                $productPrice = $ruleData['website_'.$websiteId.'_price'];
            } else {
                $productPrice = $ruleData['default_price'];
            }
        }

        $productPrice = Mage::helper('catalogrule')->calcPriceRule(
            $ruleData['action_operator'],
            $ruleData['action_amount'],
            $productPrice);

        return Mage::app()->getStore()->roundPrice($productPrice);
    }

    /**
     * Save rule prices for products to DB
     *
     * @param array $arrData
     *
     * @return Mage_CatalogRule_Model_Resource_Rule
     */
    protected function _saveRuleProductPrices($arrData)
    {
        if (empty($arrData)) {
            return $this;
        }

        $adapter    = $this->_getWriteAdapter();
        $productIds = array();

        $adapter->beginTransaction();
        try {
            foreach ($arrData as $key => $data) {
                $productIds['product_id'] = $data['product_id'];
                $arrData[$key]['rule_date'] = $this->formatDate($data['rule_date'], false);
                $arrData[$key]['latest_start_date'] = $this->formatDate($data['latest_start_date'], false);
                $arrData[$key]['earliest_end_date'] = $this->formatDate($data['earliest_end_date'], false);
            }
            $adapter->insertOnDuplicate($this->getTable('catalogrule/affected_product'), array_unique($productIds));
            $adapter->insertOnDuplicate($this->getTable('catalogrule/rule_product_price'), $arrData);

        } catch (Exception $e) {
            $adapter->rollback();
            throw $e;

        }
        $adapter->commit();

        return $this;
    }

    /**
     * Get catalog rules product price for specific date, website and
     * customer group
     *
     * @param int|string $date
     * @param int $wId
     * @param int $gId
     * @param int $pId
     *
     * @return float|bool
     */
    public function getRulePrice($date, $wId, $gId, $pId)
    {
        $data = $this->getRulePrices($date, $wId, $gId, array($pId));
        if (isset($data[$pId])) {
            return $data[$pId];
        }

        return false;
    }

    /**
     * Retrieve product prices by catalog rule for specific date, website and customer group
     * Collect data with  product Id => price pairs
     *
     * @param int|string $date
     * @param int $websiteId
     * @param int $customerGroupId
     * @param array $productIds
     *
     * @return array
     */
    public function getRulePrices($date, $websiteId, $customerGroupId, $productIds)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getTable('catalogrule/rule_product_price'), array('product_id', 'rule_price'))
            ->where('rule_date = ?', $this->formatDate($date, false))
            ->where('website_id = ?', $websiteId)
            ->where('customer_group_id = ?', $customerGroupId)
            ->where('product_id IN(?)', $productIds);
        return $adapter->fetchPairs($select);
    }

    /**
     * Get active rule data based on few filters
     *
     * @param int|string $date
     * @param int $websiteId
     * @param int $customerGroupId
     * @param int $productId
     * @return array
     */
    public function getRulesFromProduct($date, $websiteId, $customerGroupId, $productId)
    {
        $adapter = $this->_getReadAdapter();
        if (is_string($date)) {
            $date = strtotime($date);
        }
        $select = $adapter->select()
            ->from($this->getTable('catalogrule/rule_product'))
            ->where('website_id = ?', $websiteId)
            ->where('customer_group_id = ?', $customerGroupId)
            ->where('product_id = ?', $productId)
            ->where('from_time = 0 or from_time < ?', $date)
            ->where('to_time = 0 or to_time > ?', $date);

        return $adapter->fetchAll($select);
    }

    /**
     * Retrieve product price data for all customer groups
     *
     * @param int|string $date
     * @param int $wId
     * @param int $pId
     *
     * @return array
     */
    public function getRulesForProduct($date, $wId, $pId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from($this->getTable('catalogrule/rule_product_price'), '*')
            ->where('rule_date=?', $this->formatDate($date, false))
            ->where('website_id=?', $wId)
            ->where('product_id=?', $pId);

        return $read->fetchAll($select);
    }

    /**
     * Apply catalog rule to product
     *
     * @param Mage_CatalogRule_Model_Rule $rule
     * @param Mage_Catalog_Model_Product $product
     * @param array $websiteIds
     *
     * @throws Exception
     * @return Mage_CatalogRule_Model_Resource_Rule
     */
    public function applyToProduct($rule, $product, $websiteIds)
    {
        if (!$rule->getIsActive()) {
            return $this;
        }

        $ruleId    = $rule->getId();
        $productId = $product->getId();

        $write = $this->_getWriteAdapter();
        $write->beginTransaction();

        if ($this->_isProductMatchedRule($ruleId, $product)) {
            $this->cleanProductData($ruleId, array($productId));
        }
        if ($this->validateProduct($rule, $product, $websiteIds)) {
            try {
                $this->insertRuleData($rule, $websiteIds, array(
                    $productId => array_combine(array_values($websiteIds), array_values($websiteIds)))
                );
            } catch (Exception $e) {
                $write->rollback();
                throw $e;
            }
        } else {
            $write->delete($this->getTable('catalogrule/rule_product_price'), array(
                $write->quoteInto('product_id = ?', $productId),
            ));
        }

        $write->commit();
        return $this;
    }

    /**
     * Get ids of matched rules for specific product
     *
     * @param int $productId
     * @return array
     */
    public function getProductRuleIds($productId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getTable('catalogrule/rule_product'), 'rule_id');
        $select->where('product_id = ?', $productId);
        return array_flip($read->fetchCol($select));
    }

    /**
     * Is product has been matched the rule
     *
     * @param int $ruleId
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    protected function _isProductMatchedRule($ruleId, $product)
    {
        $rules = $product->getMatchedRules();
        return isset($rules[$ruleId]);
    }
}
