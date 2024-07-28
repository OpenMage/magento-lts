<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_CatalogRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Price rules observer model
 *
 * @category   Mage
 * @package    Mage_CatalogRule
 */
class Mage_CatalogRule_Model_Observer
{
    /**
     * Preload price rules for all items in quote
     *
     * @var array
     */
    protected $_preloadedPrices = [];

    /**
     * Store calculated catalog rules prices for products
     * Prices collected per website, customer group, date and product
     *
     * @var array
     */
    protected $_rulePrices = [];

    /**
     * Apply all catalog price rules for specific product
     *
     * @param   Varien_Event_Observer $observer
     * @return  $this
     */
    public function applyAllRulesOnProduct($observer)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $observer->getEvent()->getProduct();
        if ($product->getIsMassupdate()) {
            return $this;
        }

        Mage::getModel('catalogrule/rule')->applyAllRulesToProduct($product);

        return $this;
    }

    /**
     * Load matched catalog price rules for specific product.
     * Is used for comparison in Mage_CatalogRule_Model_Resource_Rule::applyToProduct method
     *
     * @param   Varien_Event_Observer $observer
     * @return  $this
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
     * @return  $this
     */
    public function applyAllRules($observer)
    {
        /** @var Mage_CatalogRule_Model_Resource_Rule $resource */
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
     * @return  $this
     */
    public function preloadPriceRules(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $observer->getQuote();
        $date = Mage::app()->getLocale()->storeTimeStamp($quote->getStoreId());
        $wId = $quote->getStore()->getWebsiteId();
        $gId = $quote->getCustomerGroupId();

        $productIds = [];
        foreach ($quote->getAllItems() as $item) {
            $productIds[] = $item->getProductId();
        }

        $cacheKey = spl_object_hash($quote);

        if (!isset($this->_preloadedPrices[$cacheKey])) {
            $this->_preloadedPrices[$cacheKey] = Mage::getResourceSingleton('catalogrule/rule')
                 ->getRulePrices($date, $wId, $gId, $productIds);
        }

        foreach ($this->_preloadedPrices[$cacheKey] as $pId => $price) {
            $key = $this->_getRulePricesKey([$date, $wId, $gId, $pId]);
            $this->_rulePrices[$key] = $price;
        }

        return $this;
    }

    /**
     * Apply catalog price rules to product on frontend
     *
     * @param   Varien_Event_Observer $observer
     *
     * @return  $this
     */
    public function processFrontFinalPrice($observer)
    {
        /** @var Mage_Catalog_Model_Product $product */
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

        $key = $this->_getRulePricesKey([$date, $wId, $gId, $pId]);
        if (!isset($this->_rulePrices[$key])) {
            $rulePrice = Mage::getResourceModel('catalogrule/rule')
                ->getRulePrice($date, $wId, $gId, $pId);
            $this->_rulePrices[$key] = $rulePrice;
        }
        if ($this->_rulePrices[$key] !== false) {
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
     * @return  $this
     */
    public function processAdminFinalPrice($observer)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $observer->getEvent()->getProduct();
        $storeId = $product->getStoreId();
        $date = Mage::app()->getLocale()->storeDate($storeId);
        $key = false;

        if ($ruleData = Mage::registry('rule_data')) {
            $wId = $ruleData->getWebsiteId();
            $gId = $ruleData->getCustomerGroupId();
            $pId = $product->getId();

            $key = $this->_getRulePricesKey([$date, $wId, $gId, $pId]);
        } elseif (!is_null($storeId) && !is_null($product->getCustomerGroupId())) {
            $wId = Mage::app()->getStore($storeId)->getWebsiteId();
            $gId = $product->getCustomerGroupId();
            $pId = $product->getId();
            $key = $this->_getRulePricesKey([$date, $wId, $gId, $pId]);
        }

        if ($key) {
            if (!isset($this->_rulePrices[$key])) {
                $rulePrice = Mage::getResourceModel('catalogrule/rule')
                    ->getRulePrice($date, $wId, $gId, $pId);
                $this->_rulePrices[$key] = $rulePrice;
            }
            if ($this->_rulePrices[$key] !== false) {
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
     * @return $this
     */
    public function catalogProductTypeConfigurablePrice(Varien_Event_Observer $observer)
    {
        /** @var Mage_Catalog_Model_Product $product */
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
     * @return  $this
     */
    public function dailyCatalogUpdate($observer)
    {
        /** @var Mage_CatalogRule_Model_Rule $model */
        $model = Mage::getSingleton('catalogrule/rule');
        $model->applyAll();

        return $this;
    }

    /**
     * Clean out calculated catalog rule prices for products
     */
    public function flushPriceCache()
    {
        $this->_rulePrices = [];
    }

    /**
     * Calculate minimal final price with catalog rule price
     *
     * @param Varien_Event_Observer $observer
     * @return $this
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
            ->applyPriceRuleToIndexTable(
                $select,
                $indexTable,
                $entityId,
                $customerGroupId,
                $websiteId,
                $updateFields,
                $websiteDate
            );

        return $this;
    }

    /**
     * Check rules that contains affected attribute
     * If rules were found they will be set to inactive and notice will be add to admin session
     *
     * @param string $attributeCode
     *
     * @return $this
     */
    protected function _checkCatalogRulesAvailability($attributeCode)
    {
        /** @var Mage_CatalogRule_Model_Resource_Rule_Collection $collection */
        $collection = Mage::getResourceModel('catalogrule/rule_collection')
            ->addAttributeInConditionFilter($attributeCode);

        $disabledRulesCount = 0;
        foreach ($collection as $rule) {
            /** @var Mage_CatalogRule_Model_Rule $rule */
            $rule->setIsActive(0);
            /** @var $rule->getConditions() Mage_CatalogRule_Model_Rule_Condition_Combine */
            $this->_removeAttributeFromConditions($rule->getConditions(), $attributeCode);
            $rule->save();

            $disabledRulesCount++;
        }

        if ($disabledRulesCount) {
            Mage::getModel('catalogrule/rule')->applyAll();
            Mage::getSingleton('adminhtml/session')->addWarning(
                Mage::helper('catalogrule')->__('%d Catalog Price Rules based on "%s" attribute have been disabled.', $disabledRulesCount, $attributeCode)
            );
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
     * @return $this
     */
    public function catalogAttributeSaveAfter(Varien_Event_Observer $observer)
    {
        /** @var Mage_Catalog_Model_Entity_Attribute $attribute */
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
     * @return $this
     */
    public function catalogAttributeDeleteAfter(Varien_Event_Observer $observer)
    {
        /** @var Mage_Catalog_Model_Entity_Attribute $attribute */
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute->getIsUsedForPromoRules()) {
            $this->_checkCatalogRulesAvailability($attribute->getAttributeCode());
        }

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     * @throws Mage_Core_Model_Store_Exception
     */
    public function prepareCatalogProductCollectionPrices(Varien_Event_Observer $observer)
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = $observer->getEvent()->getCollection();
        $store      = Mage::app()->getStore($observer->getEvent()->getStoreId());
        $websiteId  = $store->getWebsiteId();
        if ($observer->getEvent()->hasCustomerGroupId()) {
            $groupId = $observer->getEvent()->getCustomerGroupId();
        } else {
            /** @var Mage_Customer_Model_Session $session */
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

        $productIds = [];
        /** @var Mage_Catalog_Model_Product $product */
        foreach ($collection as $product) {
            $key = $this->_getRulePricesKey([$date, $websiteId, $groupId, $product->getId()]);
            if (!isset($this->_rulePrices[$key])) {
                $productIds[] = $product->getId();
            }
        }

        if ($productIds) {
            $rulePrices = Mage::getResourceModel('catalogrule/rule')
                ->getRulePrices($date, $websiteId, $groupId, $productIds);
            foreach ($productIds as $productId) {
                $key = $this->_getRulePricesKey([$date, $websiteId, $groupId, $productId]);
                $this->_rulePrices[$key] = $rulePrices[$productId] ?? false;
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
        /** @var Mage_ImportExport_Model_Import_Entity_Product $adapter */
        $adapter = $observer->getEvent()->getAdapter();
        $affectedEntityIds = $adapter->getAffectedEntityIds();

        if (empty($affectedEntityIds)) {
            return;
        }

        $rules = Mage::getModel('catalogrule/rule')->getCollection()
            ->addFieldToFilter('is_active', 1);

        /** @var Mage_CatalogRule_Model_Rule $rule */
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
     * @param array $keyInfo
     * @return string
     */
    protected function _getRulePricesKey($keyInfo)
    {
        return implode('|', $keyInfo);
    }
}
