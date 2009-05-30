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
 * Catalog product tier price backend attribute model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Attribute_Backend_Tierprice extends Mage_Catalog_Model_Product_Attribute_Backend_Price
{
    /**
     * Website currency codes and rates
     *
     * @var array
     */
    protected $_rates;

    /**
     * Retrieve resource model
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Backend_Tierprice
     */
    protected function _getResource()
    {
        return Mage::getResourceSingleton('catalog/product_attribute_backend_tierprice');
    }

    /**
     * Retrieve websites rates and base currency codes
     *
     * @return array
     */
    public function _getWebsiteRates()
    {
        if (is_null($this->_rates)) {
            $this->_rates = array();
            $baseCurrency = Mage::app()->getBaseCurrencyCode();
            foreach (Mage::app()->getWebsites() as $website) {
                /* @var $website Mage_Core_Model_Website */
                if ($website->getBaseCurrencyCode() != $baseCurrency) {
                    $rate = Mage::getModel('directory/currency')
                        ->load($baseCurrency)
                        ->getRate($website->getBaseCurrencyCode());
                    if (!$rate) {
                        $rate = 1;
                    }
                    $this->_rates[$website->getId()] = array(
                        'code' => $website->getBaseCurrencyCode(),
                        'rate' => $rate
                    );
                }
                else {
                    $this->_rates[$website->getId()] = array(
                        'code' => $baseCurrency,
                        'rate' => 1
                    );
                }
            }
        }
        return $this->_rates;
    }

    /**
     * Validate data
     *
     * @param   Mage_Catalog_Model_Product $object
     * @return  this
     */
    public function validate($object)
    {
        $tiers = $object->getData($this->getAttribute()->getName());
        if (empty($tiers)) {
            return $this;
        }

        // validate per website
        $duplicates = array();
        foreach ($tiers as $tier) {
            if (!empty($tier['delete'])) {
                continue;
            }
            $compare = join('-', array($tier['website_id'], $tier['cust_group'], $tier['price_qty']));
            if (isset($duplicates[$compare])) {
                Mage::throwException(
                    Mage::helper('catalog')->__('Duplicate website tier price customer group and quantity.')
                );
            }
            $duplicates[$compare] = true;
        }

        // validate currency
        $baseCurrency = Mage::app()->getBaseCurrencyCode();
        $rates = $this->_getWebsiteRates();
        foreach ($tiers as $tier) {
            if (!empty($tier['delete'])) {
                continue;
            }
            if ($tier['website_id'] == 0) {
                continue;
            }

            $compare = join('-', array($tier['website_id'], $tier['cust_group'], $tier['price_qty']));
            $globalCompare = join('-', array(0, $tier['cust_group'], $tier['price_qty']));
            $websiteCurrency = $rates[$tier['website_id']]['code'];

            if ($baseCurrency == $websiteCurrency && isset($duplicates[$globalCompare])) {
                Mage::throwException(
                    Mage::helper('catalog')->__('Duplicate website tier price customer group and quantity.')
                );
            }
        }
        return $this;
    }

    /**
     * Assign tier prices to product data
     *
     * @param Mage_Catalog_Model_Product $object
     * @return Mage_Catalog_Model_Product_Attribute_Backend_Tierprice
     */
    public function afterLoad($object)
    {
        $data = $this->_getResource()
            ->loadProductPrices($object, $this->getAttribute());
        foreach ($data as $k => $v) {
            if ($v['all_groups']) {
                $data[$k]['cust_group'] = Mage_Customer_Model_Group::CUST_GROUP_ALL;
                $data[$k]['website_price'] = $v['price'];
            }
        }

        if (!$object->getData('_edit_mode')
            && $this->getAttribute()->getIsGlobal() == Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE
            && ($storeId = $object->getStoreId()))
        {
            $websiteId    = Mage::app()->getStore($storeId)->getWebsiteId();
            $rates        = $this->_getWebsiteRates();

            $full = $data;
            $data = array();
            foreach ($full as $v) {
                $key = join('-', array($v['cust_group'], $v['price_qty']));
                if ($v['website_id'] == $websiteId) {
                    $data[$key] = $v;
                    $data[$key]['website_price'] = $v['price'];
                }
                elseif ($v['website_id'] == 0 && !isset($data[$key])) {
                    $data[$key] = $v;
                    $data[$key]['website_id'] = $websiteId;
                    $data[$key]['price'] = $v['price'] * $rates[$websiteId]['rate'];
                    $data[$key]['website_price'] = $v['price'] * $rates[$websiteId]['rate'];
                }
            }
        }

//        foreach ($data as $i => $row) {
//            if (!empty($row['all_groups'])) {
//                $data[$i]['cust_group'] = Mage_Customer_Model_Group::CUST_GROUP_ALL;
//            }
//            if ($data[$i]['website_id'] == 0) {
//                $rate = Mage::app()->getStore()->getBaseCurrency()
//                    ->getRate(Mage::app()->getBaseCurrencyCode());
//                if ($rate) {
//                    $data[$i]['website_price'] = $data[$i]['price']/$rate;
//                }
//                else {
//                    /**
//                     * Remove tier price if rate not available
//                     */
//                    unset($data[$i]);
//                }
//            }
//            else {
//                $data[$i]['website_price'] = $data[$i]['price'];
//            }
//        }
        $object->setData($this->getAttribute()->getName(), $data);
        return $this;
    }

    /**
     * After Save Attribute manipulation
     *
     * @param Mage_Catalog_Model_Product $object
     * @return Mage_Catalog_Model_Product_Attribute_Backend_Tierprice
     */
    public function afterSave($object)
    {
        $this->_getResource()->deleteProductPrices($object, $this->getAttribute());
        $tierPrices = $object->getData($this->getAttribute()->getName());

        if (!is_array($tierPrices)) {
            return $this;
        }

        $prices = array();
        foreach ($tierPrices as $tierPrice) {
            if (empty($tierPrice['price_qty']) || !isset($tierPrice['price']) || !empty($tierPrice['delete'])) {
                continue;
            }

            $useForAllGroups = $tierPrice['cust_group'] == Mage_Customer_Model_Group::CUST_GROUP_ALL;
            $customerGroupId = !$useForAllGroups ? $tierPrice['cust_group'] : 0;
            $priceKey = join('-', array(
                $tierPrice['website_id'],
                intval($useForAllGroups),
                $customerGroupId,
                $tierPrice['price_qty']
            ));

            $prices[$priceKey] = array(
                'website_id'        => $tierPrice['website_id'],
                'all_groups'        => intval($useForAllGroups),
                'customer_group_id' => $customerGroupId,
                'qty'               => $tierPrice['price_qty'],
                'value'             => $tierPrice['price'],
            );
        }

        if ($this->getAttribute()->getIsGlobal() == Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE) {
            if ($storeId = $object->getStoreId()) {
                $websites = array(Mage::app()->getStore($storeId)->getWebsite());
            }
            else {
                $websites = Mage::app()->getWebsites();
            }

            $baseCurrency   = Mage::app()->getBaseCurrencyCode();
            $rates          = $this->_getWebsiteRates();
            foreach ($websites as $website) {
                /* @var $website Mage_Core_Model_Website */
                if (!is_array($object->getWebsiteIds()) || !in_array($website->getId(), $object->getWebsiteIds())) {
                    continue;
                }
                if ($rates[$website->getId()]['code'] != $baseCurrency) {
                    foreach ($prices as $data) {
                        $priceKey = join('-', array(
                            $website->getId(),
                            $data['all_groups'],
                            $data['customer_group_id'],
                            $data['qty']
                        ));
                        if (!isset($prices[$priceKey])) {
                            $prices[$priceKey] = $data;
                            $prices[$priceKey]['website_id'] = $website->getId();
                            $prices[$priceKey]['value'] = $data['value'] * $rates[$website->getId()]['rate'];
                        }
                    }
                }
            }
        }

        foreach ($prices as $data) {
            $this->_getResource()->insertProductPrice($object, $data);
        }

        return $this;
    }

    /**
     * After delete object remove additional data
     *
     * @param Mage_Catalog_Model_Product $object
     * @return Mage_Catalog_Model_Product_Attribute_Backend_Tierprice
     */
    public function afterDelete($object)
    {
        $this->_getResource()->deleteProductPrices($object, $this->getAttribute());
        return $this;
    }
}