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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Price rules observer model
 */
class Mage_CatalogRule_Model_Observer
{
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

        $productWebsiteIds = $product->getWebsiteIds();

        $rules = Mage::getModel('catalogrule/rule')->getCollection()
            ->addFieldToFilter('is_active', 1);

        foreach ($rules as $rule) {
            if (!is_array($rule->getWebsiteIds())) {
                $ruleWebsiteIds = (array)explode(',', $rule->getWebsiteIds());
            } else {
                $ruleWebsiteIds = $rule->getWebsiteIds();
            }
            $websiteIds = array_intersect($productWebsiteIds, $ruleWebsiteIds);
            $rule->applyToProduct($product, $websiteIds);
        }
        return $this;
    }

    /**
     * Apply all price rules for current date.
     * Handle cataolg_product_import_after event
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_CatalogRule_Model_Observer
     */
    public function applyAllRules($observer)
    {
        $resource = Mage::getResourceSingleton('catalogrule/rule');
        $resource->applyAllRulesForDateRange($resource->formatDate(mktime(0,0,0)));
        Mage::app()->removeCache('catalog_rules_dirty');
        return $this;
    }

    /**
     * Apply catalog price rules to product on frontend
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
        } else {
            $gId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        }

        $key = "$date|$wId|$gId|$pId";
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

            $key = "$date|$wId|$gId|$pId";
        }
        elseif ($product->getWebsiteId() != null && $product->getCustomerGroupId() != null) {
            $wId = $product->getWebsiteId();
            $gId = $product->getCustomerGroupId();
            $pId = $product->getId();
            $key = "$date|$wId|$gId|$pId";
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
     * Daily update catalog price rule by cron
     * Update include interval 3 days - current day - 1 days before + 1 days after
     * This method is called from cron process, cron is workink in UTC time and
     * we shold generate data for interval -1 day ... +1 day
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_CatalogRule_Model_Observer
     */
    public function dailyCatalogUpdate($observer)
    {
        Mage::getResourceSingleton('catalogrule/rule')->applyAllRulesForDateRange();
        return $this;
    }

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
}
