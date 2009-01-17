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
 * @category   Mage
 * @package    Mage_CatalogRule
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_CatalogRule_Model_Observer
{
    protected $_rulePrices = array();

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
    }

    public function applyAllRules($observer)
    {
        $resource = Mage::getResourceSingleton('catalogrule/rule');
        $resource->applyAllRulesForDateRange($resource->formatDate(mktime(0,0,0)));
        Mage::app()->removeCache('catalog_rules_dirty');
    }

    /**
     * Processing final price on frontend
     */
    public function processFrontFinalPrice($observer)
    {
        if ($observer->hasDate()) {
            $date = $observer->getDate();
        } else {
            $date = mktime(0,0,0);
        }

        if ($observer->hasWebsiteId()) {
            $wId = $observer->getWebsiteId();
        } else {
            $wId = Mage::app()->getWebsite()->getId();
        }

        if ($observer->hasCustomerGroupId()) {
            $gId = $observer->getCustomerGroupId();
        } else {
            $gId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        }

        $product = $observer->getEvent()->getProduct();
        $pId = $product->getId();

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
     * Processing final price in admin
     */
    public function processAdminFinalPrice($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $key = false;
        if ($ruleData = Mage::registry('rule_data')) {
            $date = mktime(0,0,0);
            $wId = $ruleData->getWebsiteId();
            $gId = $ruleData->getCustomerGroupId();
            $pId = $product->getId();

            $key = "$date|$wId|$gId|$pId";
        }
        elseif ($product->getWebsiteId() != null && $product->getCustomerGroupId() != null) {
            $date = mktime(0,0,0);
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

    public function dailyCatalogUpdate($schedule)
    {
        $resource = Mage::getResourceSingleton('catalogrule/rule');
        $resource->applyAllRulesForDateRange(
            $resource->formatDate(mktime(0,0,0)),
            $resource->formatDate(mktime(0,0,0,date('m'),date('d')+1))
        );
    }

    public function flushPriceCache()
    {
        $this->_rulePrices = array();
    }
}