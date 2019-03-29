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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Price rules observer model
 */
class Mage_CatalogRule_Model_Observer
{
    /**
     * Preload price rules for all items in quote
     *
     * @var array
     */
    protected $_preloadedPrices = array();

    /**
     * Store calculated catalog rules prices for products
     * Prices collected per website, customer group, date and product
     *
     * @var array
     */
    protected $_rulePrices = array();

    /**
     * Apply all catalog price rules for specific product
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_CatalogRule_Model_Observer
     */
    public function applyAllRulesOnProduct($observer)
    {
        $product = $observer->getEvent()->getProduct();
        if ($product->getIsMassupdate()) {
            return;
        }

        Mage::getModel('catalogrule/rule')->applyAllRulesToProduct($product);

        return $this;
    }

    /**
     * Load matched catalog price rules for specific product.
     * Is used for comparison in Mage_CatalogRule_Model_Resource_Rule::applyToProduct method
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_CatalogRule_Model_Observer
     */
    public function loadProductRules($observer)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $observer->getEvent()->getProduct();
        if (!$product instanceof Mage_Catalog_Model_Product) {
            return $this;
        }
        Mage::getModel('catalogrule/rule')->loadProductRules($product);
        return $this;
    }

    /**
     * Apply all price rules for current date.
     * Handle catalog_product_import_after event
     *
     * @param   Varien_Event_Observer $observer
     *
     * @return  Mage_CatalogRule_Model_Observer
     */
    public function applyAllRules($observer)
    {
        /** @var $resource Mage_CatalogRule_Model_Resource_Rule */
        $resource = Mage::getResourceSingleton('catalogrule/rule');
        $resource->applyAllRules();
        Mage::getModel('catalogrule/flag')->loadSelf()
            ->setState(0)
            ->save();

        return $this;
    }

    /**
     * Preload all price rules for all items in quote
     *
     * @param   Varien_Event_Observer $observer
     *
     * @return  Mage_CatalogRule_Model_Observer
     */
    public function preloadPriceRules(Varien_Event_Observer $observer)
    {
        $quote = $observer->getQuote();
        $date = Mage::app()->getLocale()->storeTimeStamp($quote->getStoreId());
        $wId = $quote->getStore()->getWebsiteId();
        $gId = $quote->getCustomerGroupId();

        $productIds = array();
        foreach ($quote->getAllItems() as $item) {
            $productIds[] = $item->getProductId();
        }

        $cacheKey = spl_object_hash($quote);

        if (!isset($this->_preloadedPrices[$cacheKey])) {
            $this->_preloadedPrices[$cacheKey] = Mage::getResourceSingleton('catalogrule/rule')
                 ->getRulePrices($date, $wId, $gId, $productIds);
        }

        foreach ($this->_preloadedPrices[$cacheKey] as $pId => $price) {
            $key = $this->_getRulePricesKey(array($date, $wId, $gId, $pId));
            $this->_rulePrices[$key] = $price;
        }

        return $this;
    }

    /**
     * Apply catalog price rules to product on frontend
     *
     * @param   Varien_Event_Observer $observer
     *
     * @return  Mage_CatalogRule_Model_Observer
     */
    public function processFrontFinalPrice($observer)
    {
        $product    = $observer->getEvent()->getProduct();
        $pId        = $product->getId();
        $storeId    = $product->getStoreId();

        if ($observer->hasDate()) {
            $date = $observer->getEvent()->getDate();
        } else {
            $date = Mage::app()->getLocale()->storeTimeStamp($storeId);
        }

        if ($observer->hasWebsiteId()) {
            $wId = $observer->getEvent()->getWebsiteId();
        } else {
            $wId = Mage::app()->getStore($storeId)->getWebsiteId();
        }

        if ($observer->hasCustomerGroupId()) {
            $gId = $observer->getEvent()->getCustomerGroupId();
        } elseif ($product->hasCustomerGroupId()) {
            $gId = $product->getCustomerGroupId();
        } else {
            $gId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        }

        $key = $this->_getRulePricesKey(array($date, $wId, $gId, $pId));
        if (!isset($this->_rulePrices[$key])) {
            $rulePrice = Mage::getResourceModel('catalogrule/rule')
                ->getRulePrice($date, $wId, $gId, $pId);
            $this->_rulePrices[$key] = $rulePrice;
        }
        if ($this->_rulePrices[$key]!==false) {
            $finalPrice = min($product->getData('final_price'), $this->_rulePrices[$key]);
            $product->setFinalPrice($finalPrice);
        }
        return $this;
    }

    /**
     * Apply catalog price rules to product in admin
     *
     * @param   Varien_Event_Observer $observer
     *
     * @return  Mage_CatalogRule_Model_Observer
     */
    public function processAdminFinalPrice($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $storeId = $product->getStoreId();
        $date = Mage::app()->getLocale()->storeDate($storeId);
        $key = false;

        if ($ruleData = Mage::registry('rule_data')) {
            $wId = $ruleData->getWebsiteId();
            $gId = $ruleData->getCustomerGroupId();
            $pId = $product->getId();

            $key = $this->_getRulePricesKey(array($date, $wId, $gId, $pId));
        }
        elseif (!is_null($storeId) && !is_null($product->getCustomerGroupId())) {
            $wId = Mage::app()->getStore($storeId)->getWebsiteId();
            $gId = $product->getCustomerGroupId();
            $pId = $product->getId();
            $key = $this->_getRulePricesKey(array($date, $wId, $gId, $pId));
        }

        if ($key) {
            if (!isset($this->_rulePrices[$key])) {
                $rulePrice = Mage::getResourceModel('catalogrule/rule')
                    ->getRulePrice($date, $wId, $gId, $pId);
                $this->_rulePrices[$key] = $rulePrice;
            }
            if ($this->_rulePrices[$key]!==false) {
                $finalPrice = min($product->getData('final_price'), $this->_rulePrices[$key]);
                $product->setFinalPrice($finalPrice);
            }
        }

        return $this;
    }

    /**
     * Calculate price using catalog price rules of configurable product
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Mage_CatalogRule_Model_Observer
     */
    public function catalogProductTypeConfigurablePrice(Varien_Event_Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        if ($product instanceof Mage_Catalog_Model_Product
            && $product->getConfigurablePrice() !== null
        ) {
            $configurablePrice = $product->getConfigurablePrice();
            $productPriceRule = Mage::getModel('catalogrule/rule')->calcProductPriceRule($product, $configurablePrice);
            if ($productPriceRule !== null) {
                $product->setConfigurablePrice($productPriceRule);
            }
        }

        return $this;
    }

    /**
     * Daily update catalog price rule by cron
     * Update include interval 3 days - current day - 1 days before + 1 days after
     * This method is called from cron process, cron is working in UTC time and
     * we should generate data for interval -1 day ... +1 day
     *
     * @param   Varien_Event_Observer $observer
     *
     * @return  Mage_CatalogRule_Model_Observer
     */
    public function dailyCatalogUpdate($observer)
    {
        /** @var $model Mage_CatalogRule_Model_Rule */
        $model = Mage::getSingleton('catalogrule/rule');
        $model->applyAll();

        return $this;
    }

    /**
     * Clean out calculated catalog rule prices for products
     */
    public function flushPriceCache()
    {
        $this->_rulePrices = array();
    }

    /**
     * Calculate minimal final price with catalog rule price
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_CatalogRule_Model_Observer
     */
    public function prepareCatalogProductPriceIndexTable(Varien_Event_Observer $observer)
    {
        $select             = $observer->getEvent()->getSelect();

        $indexTable         = $observer->getEvent()->getIndexTable();
        $entityId           = $observer->getEvent()->getEntityId();
        $customerGroupId    = $observer->getEvent()->getCustomerGroupId();
        $websiteId          = $observer->getEvent()->getWebsiteId();
        $websiteDate        = $observer->getEvent()->getWebsiteDate();
        $updateFields       = $observer->getEvent()->getUpdateFields();

        Mage::getSingleton('catalogrule/rule_product_price')
            ->applyPriceRuleToIndexTable($select, $indexTable, $entityId, $customerGroupId, $websiteId,
                $updateFields, $websiteDate);

        return $this;
    }

    /**
     * Check rules that contains affected attribute
     * If rules were found they will be set to inactive and notice will be add to admin session
     *
     * @param string $attributeCode
     *
     * @return Mage_CatalogRule_Model_Observer
     */
    protected function _checkCatalogRulesAvailability($attributeCode)
    {
        /* @var $collection Mage_CatalogRule_Model_Mysql4_Rule_Collection */
        $collection = Mage::getResourceModel('catalogrule/rule_collection')
            ->addAttributeInConditionFilter($attributeCode);

        $disabledRulesCount = 0;
        foreach ($collection as $rule) {
            /* @var $rule Mage_CatalogRule_Model_Rule */
            $rule->setIsActive(0);
            /* @var $rule->getConditions() Mage_CatalogRule_Model_Rule_Condition_Combine */
            $this->_removeAttributeFromConditions($rule->getConditions(), $attributeCode);
            $rule->save();

            $disabledRulesCount++;
        }

        if ($disabledRulesCount) {
            Mage::getModel('catalogrule/rule')->applyAll();
            Mage::getSingleton('adminhtml/session')->addWarning(
                Mage::helper('catalogrule')->__('%d Catalog Price Rules based on "%s" attribute have been disabled.', $disabledRulesCount, $attributeCode));
        }

        return $this;
    }

    /**
     * Remove catalog attribute condition by attribute code from rule conditions
     *
     * @param Mage_CatalogRule_Model_Rule_Condition_Combine $combine
     *
     * @param string $attributeCode
     */
    protected function _removeAttributeFromConditions($combine, $attributeCode)
    {
        $conditions = $combine->getConditions();
        foreach ($conditions as $conditionId => $condition) {
            if ($condition instanceof Mage_CatalogRule_Model_Rule_Condition_Combine) {
                $this->_removeAttributeFromConditions($condition, $attributeCode);
            }
            if ($condition instanceof Mage_Rule_Model_Condition_Product_Abstract) {
                if ($condition->getAttribute() == $attributeCode) {
                    unset($conditions[$conditionId]);
                }
            }
        }
        $combine->setConditions($conditions);
    }

    /**
     * After save attribute if it is not used for promo rules already check rules for containing this attribute
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Mage_CatalogRule_Model_Observer
     */
    public function catalogAttributeSaveAfter(Varien_Event_Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute->dataHasChangedFor('is_used_for_promo_rules') && !$attribute->getIsUsedForPromoRules()) {
            $this->_checkCatalogRulesAvailability($attribute->getAttributeCode());
        }

        return $this;
    }

    /**
     * After delete attribute check rules that contains deleted attribute
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_CatalogRule_Model_Observer
     */
    public function catalogAttributeDeleteAfter(Varien_Event_Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute->getIsUsedForPromoRules()) {
            $this->_checkCatalogRulesAvailability($attribute->getAttributeCode());
        }

        return $this;
    }

    public function prepareCatalogProductCollectionPrices(Varien_Event_Observer $observer)
    {
        /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */
        $collection = $observer->getEvent()->getCollection();
        $store      = Mage::app()->getStore($observer->getEvent()->getStoreId());
        $websiteId  = $store->getWebsiteId();
        if ($observer->getEvent()->hasCustomerGroupId()) {
            $groupId = $observer->getEvent()->getCustomerGroupId();
        } else {
            /* @var $session Mage_Customer_Model_Session */
            $session = Mage::getSingleton('customer/session');
            if ($session->isLoggedIn()) {
                $groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
            } else {
                $groupId = Mage_Customer_Model_Group::NOT_LOGGED_IN_ID;
            }
        }
        if ($observer->getEvent()->hasDate()) {
            $date = $observer->getEvent()->getDate();
        } else {
            $date = Mage::app()->getLocale()->storeTimeStamp($store);
        }

        $productIds = array();
        /* @var $product Mage_Core_Model_Product */
        foreach ($collection as $product) {
            $key = $this->_getRulePricesKey(array($date, $websiteId, $groupId, $product->getId()));
            if (!isset($this->_rulePrices[$key])) {
                $productIds[] = $product->getId();
            }
        }

        if ($productIds) {
            $rulePrices = Mage::getResourceModel('catalogrule/rule')
                ->getRulePrices($date, $websiteId, $groupId, $productIds);
            foreach ($productIds as $productId) {
                $key = $this->_getRulePricesKey(array($date, $websiteId, $groupId, $productId));
                $this->_rulePrices[$key] = isset($rulePrices[$productId]) ? $rulePrices[$productId] : false;
            }
        }

        return $this;
    }

    /**
     * Create catalog rule relations for imported products
     *
     * @param Varien_Event_Observer $observer
     */
    public function createCatalogRulesRelations(Varien_Event_Observer $observer)
    {
        $adapter = $observer->getEvent()->getAdapter();
        $affectedEntityIds = $adapter->getAffectedEntityIds();

        if (empty($affectedEntityIds)) {
            return;
        }

        $rules = Mage::getModel('catalogrule/rule')->getCollection()
            ->addFieldToFilter('is_active', 1);

        foreach ($rules as $rule) {
            $rule->setProductsFilter($affectedEntityIds);
            Mage::getResourceSingleton('catalogrule/rule')->updateRuleProductData($rule);
        }
    }

    /**
     * Runs Catalog Product Price Reindex
     *
     * @param Varien_Event_Observer $observer
     */
    public function runCatalogProductPriceReindex(Varien_Event_Observer $observer)
    {
        $indexProcess = Mage::getSingleton('index/indexer')->getProcessByCode('catalog_product_price');
        if ($indexProcess) {
            $indexProcess->reindexAll();
        }
    }

    /**
     * Generate key for rule prices
     *
     * @param array
     */
    protected function _getRulePricesKey($keyInfo)
    {
        return implode('|', $keyInfo);
    }
}
