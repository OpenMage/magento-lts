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
 * @package     Mage_CatalogRule
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Enter description here ...
 *
 * @method Mage_CatalogRule_Model_Resource_Rule _getResource()
 * @method Mage_CatalogRule_Model_Resource_Rule getResource()
 * @method string getName()
 * @method Mage_CatalogRule_Model_Rule setName(string $value)
 * @method string getDescription()
 * @method Mage_CatalogRule_Model_Rule setDescription(string $value)
 * @method string getFromDate()
 * @method Mage_CatalogRule_Model_Rule setFromDate(string $value)
 * @method string getToDate()
 * @method Mage_CatalogRule_Model_Rule setToDate(string $value)
 * @method Mage_CatalogRule_Model_Rule setCustomerGroupIds(string $value)
 * @method int getIsActive()
 * @method Mage_CatalogRule_Model_Rule setIsActive(int $value)
 * @method string getConditionsSerialized()
 * @method Mage_CatalogRule_Model_Rule setConditionsSerialized(string $value)
 * @method string getActionsSerialized()
 * @method Mage_CatalogRule_Model_Rule setActionsSerialized(string $value)
 * @method int getStopRulesProcessing()
 * @method Mage_CatalogRule_Model_Rule setStopRulesProcessing(int $value)
 * @method int getSortOrder()
 * @method Mage_CatalogRule_Model_Rule setSortOrder(int $value)
 * @method string getSimpleAction()
 * @method Mage_CatalogRule_Model_Rule setSimpleAction(string $value)
 * @method float getDiscountAmount()
 * @method Mage_CatalogRule_Model_Rule setDiscountAmount(float $value)
 * @method string getWebsiteIds()
 * @method Mage_CatalogRule_Model_Rule setWebsiteIds(string $value)
 *
 * @category    Mage
 * @package     Mage_CatalogRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogRule_Model_Rule extends Mage_Rule_Model_Rule
{
    const XML_NODE_RELATED_CACHE = 'global/catalogrule/related_cache_types';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'catalogrule_rule';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getRule() in this case
     *
     * @var string
     */
    protected $_eventObject = 'rule';

    /**
     * Matched product ids array
     *
     * @var array
     */
    protected $_productIds;

    /**
     * Limitation for products collection
     *
     * @var int|array|null
     */
    protected $_productsFilter = null;

    protected $_now;

    /**
     * Cached data of prices calculated by price rules
     *
     * @var array
     */
    protected static $_priceRulesData = array();

    /**
     * Init resource model and id field
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('catalogrule/rule');
        $this->setIdFieldName('rule_id');
    }

    /**
     * Filtering products that must be checked for matching with rule
     *
     * @param  int|array $productIds
     */
    public function setProductsFilter($productIds)
    {
        $this->_productsFilter = $productIds;
    }

    /**
     * Returns products filter
     *
     * @return array|int|null
     */
    public function getProductsFilter()
    {
        return $this->_productsFilter;
    }

    public function getConditionsInstance()
    {
        return Mage::getModel('catalogrule/rule_condition_combine');
    }

    public function getActionsInstance()
    {
        return Mage::getModel('catalogrule/rule_action_collection');
    }

    public function getNow()
    {
        if (!$this->_now) {
            return now();
        }
        return $this->_now;
    }

    public function setNow($now)
    {
        $this->_now = $now;
    }

    public function toString($format='')
    {
        $str = Mage::helper('catalogrule')->__("Name: %s", $this->getName()) ."\n"
             . Mage::helper('catalogrule')->__("Start at: %s", $this->getStartAt()) ."\n"
             . Mage::helper('catalogrule')->__("Expire at: %s", $this->getExpireAt()) ."\n"
             . Mage::helper('catalogrule')->__("Customer Registered: %s", $this->getCustomerRegistered()) ."\n"
             . Mage::helper('catalogrule')->__("Customer is a New Buyer: %s", $this->getCustomerNewBuyer()) ."\n"
             . Mage::helper('catalogrule')->__("Description: %s", $this->getDescription()) ."\n\n"
             . $this->getConditions()->toStringRecursive() ."\n\n"
             . $this->getActions()->toStringRecursive() ."\n\n";
        return $str;
    }

    /**
     * Returns rule as an array for admin interface
     *
     * Output example:
     * array(
     *   'name'=>'Example rule',
     *   'conditions'=>{condition_combine::toArray}
     *   'actions'=>{action_collection::toArray}
     * )
     *
     * @return array
     */
    public function toArray(array $arrAttributes = array())
    {
        $out = parent::toArray($arrAttributes);
        $out['customer_registered'] = $this->getCustomerRegistered();
        $out['customer_new_buyer'] = $this->getCustomerNewBuyer();

        return $out;
    }

    /**
     * Invalidate related cache types
     *
     * @return Mage_CatalogRule_Model_Rule
     */
    protected function _invalidateCache()
    {
        $types = Mage::getConfig()->getNode(self::XML_NODE_RELATED_CACHE);
        if ($types) {
            $types = $types->asArray();
            Mage::app()->getCacheInstance()->invalidateType(array_keys($types));
        }
        return $this;
    }

    /**
     * Process rule related data after rule save
     *
     * @return Mage_CatalogRule_Model_Rule
     */
    protected function _afterSave()
    {
        $this->_getResource()->updateRuleProductData($this);
        parent::_afterSave();
    }

    /**
     * Get array of product ids which are matched by rule
     *
     * @return array
     */
    public function getMatchingProductIds()
    {
        if (is_null($this->_productIds)) {
            $this->_productIds = array();
            $this->setCollectedAttributes(array());
            $websiteIds = $this->getWebsiteIds();
            if (!is_array($websiteIds)) {
                $websiteIds = explode(',', $websiteIds);
            }

            if ($websiteIds) {
                $productCollection = Mage::getResourceModel('catalog/product_collection')
                    ->addWebsiteFilter($websiteIds);
                if ($this->_productsFilter) {
                    $productCollection->addIdFilter($this->_productsFilter);
                }

                $this->getConditions()->collectValidatedAttributes($productCollection);

                Mage::getSingleton('core/resource_iterator')->walk(
                    $productCollection->getSelect(),
                    array(array($this, 'callbackValidateProduct')),
                    array(
                        'attributes' => $this->getCollectedAttributes(),
                        'product'    => Mage::getModel('catalog/product'),
                    )
                );
            }
        }

        return $this->_productIds;
    }

    /**
     * Callback function for product matching
     *
     * @param $args
     * @return void
     */
    public function callbackValidateProduct($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);

        if ($this->getConditions()->validate($product)) {
            $this->_productIds[] = $product->getId();
        }
    }

    /**
     * Apply rule to product
     *
     * @param int|Mage_Catalog_Model_Product $product
     * @param array $websiteIds
     * @return void
     */
    public function applyToProduct($product, $websiteIds=null)
    {
        if (is_numeric($product)) {
            $product = Mage::getModel('catalog/product')->load($product);
        }
        if (is_null($websiteIds)) {
            $websiteIds = explode(',', $this->getWebsiteIds());
        }
        $this->getResource()->applyToProduct($this, $product, $websiteIds);
    }

    /**
     * Get array of assigned customer group ids
     *
     * @return array
     */
    public function getCustomerGroupIds()
    {
        $ids = $this->getData('customer_group_ids');
        if (($ids && !$this->getCustomerGroupChecked()) || is_string($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            $groupIds = Mage::getModel('customer/group')->getCollection()->getAllIds();
            $ids = array_intersect($ids, $groupIds);
            $this->setData('customer_group_ids', $ids);
            $this->setCustomerGroupChecked(true);
        }
        return $ids;
    }

    /**
     * Apply all price rules, invalidate related cache and refresh price index
     *
     * @return Mage_CatalogRule_Model_Rule
     */
    public function applyAll()
    {
        $this->_getResource()->applyAllRulesForDateRange();
        $this->_invalidateCache();
        $indexProcess = Mage::getSingleton('index/indexer')->getProcessByCode('catalog_product_price');
        if ($indexProcess) {
            $indexProcess->reindexAll();
        }
    }

    /**
     * Apply all price rules to product
     *
     * @param  int|Mage_Catalog_Model_Product $product
     * @return Mage_CatalogRule_Model_Rule
     */
    public function applyAllRulesToProduct($product)
    {
        $this->_getResource()->applyAllRulesForDateRange(NULL, NULL, $product);
        $this->_invalidateCache();

        if ($product instanceof Mage_Catalog_Model_Product) {
            $productId = $product->getId();
        } else {
            $productId = $product;
        }

        if ($productId) {
            Mage::getResourceSingleton('catalog/product_indexer_price')->reindexProductIds(array($productId));
        }
    }

    /**
     * Calculate price using catalog price rule of product
     *
     * @param  Mage_Catalog_Model_Product $product
     * @param  float $price
     * @return float|null
     */
    public function calcProductPriceRule(Mage_Catalog_Model_Product $product, $price)
    {
        $priceRules = null;
        $productId  = $product->getId();
        $storeId    = $product->getStoreId();
        $websiteId  = Mage::app()->getStore($storeId)->getWebsiteId();
        if ($product->hasCustomerGroupId()) {
            $customerGroupId = $product->getCustomerGroupId();
        } else {
            $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        }
        $dateTs     = Mage::app()->getLocale()->storeTimeStamp($storeId);
        $cacheKey   = date('Y-m-d', $dateTs) . "|$websiteId|$customerGroupId|$productId|$price";

        if (!array_key_exists($cacheKey, self::$_priceRulesData)) {
            $rulesData = $this->_getResource()->getRulesFromProduct($dateTs, $websiteId, $customerGroupId, $productId);
            if ($rulesData) {
                foreach ($rulesData as $ruleData) {
                    $priceRules = Mage::helper('catalogrule')->calcPriceRule(
                        $ruleData['simple_action'],
                        $ruleData['discount_amount'],
                        $priceRules ? $priceRules :$price);
                    if ($ruleData['stop_rules_processing']) {
                        break;
                    }
                }
                return self::$_priceRulesData[$cacheKey] = $priceRules;
            } else {
                self::$_priceRulesData[$cacheKey] = null;
            }
        } else {
            return self::$_priceRulesData[$cacheKey];
        }
        return null;
    }
}
