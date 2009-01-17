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
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bundle Price Model
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Model_Product_Price extends Mage_Catalog_Model_Product_Type_Price
{
    /**
     * Return product base price
     *
     * @return string
     */
    public function getPrice($product)
    {
        if ($product->getPriceType()) {
            return $product->getData('price');
        } else {
            return 0;
        }
    }

    /**
     * Get product final price
     *
     * @param   double $qty
     * @param   Mage_Catalog_Model_Product $product
     * @return  double
     */
    public function getFinalPrice($qty=null, $product)
    {
        if (is_null($qty) && !is_null($product->getCalculatedFinalPrice())) {
            return $product->getCalculatedFinalPrice();
        }

        $finalPrice = $product->getPrice();
        if ($product->hasCustomOptions()) {
            $customOption = $product->getCustomOption('bundle_option_ids');
            $optionIds = unserialize($customOption->getValue());
            $customOption = $product->getCustomOption('bundle_selection_ids');
            $selectionIds = unserialize($customOption->getValue());
            $selections = $product->getTypeInstance()->getSelectionsByIds($selectionIds);
            foreach ($selections->getItems() as $selection) {
                if ($selection->isSalable()) {
                    $selectionQty = $product->getCustomOption('selection_qty_' . $selection->getSelectionId());
                    if ($selectionQty) {
                        $finalPrice = $finalPrice + $this->getSelectionPrice($product, $selection, $selectionQty->getValue());
                    }
                }
            }
        } else {
            if ($options = $this->getOptions($product)) {
                /* some strange thing
                foreach ($options as $option) {
                    $selectionCount = count($option->getSelections());
                    if ($selectionCount) {
                        foreach ($option->getSelections() as $selection) {
                            if ($selection->isSalable() && ($selection->getIsDefault() || ($option->getRequired() &&)) {
                                $finalPrice = $finalPrice + $this->getSelectionPrice($product, $selection);
                            }
                        }
                    }
                }
                */
            }
        }

        $finalPrice = $this->_applyTierPrice($product, $qty, $finalPrice);
        $finalPrice = $this->_applySpecialPrice($product, $finalPrice);
        $finalPrice = $this->_applyOptionsPrice($product, $qty, $finalPrice);

        $product->setFinalPrice($finalPrice);
        Mage::dispatchEvent('catalog_product_get_final_price', array('product'=>$product));
        return max(0, $product->getData('final_price'));
    }

    public function getChildFinalPrice($product, $productQty, $childProduct, $childProductQty)
    {
        return $this->getSelectionFinalPrice($product, $childProduct, $productQty, $childProductQty, false);
    }

    public function getPrices($product, $which = null)
    {
        $minimalPrice = $maximalPrice = $product->getPrice();
        if ($options = $this->getOptions($product)) {
            foreach ($options as $option) {
                if ($option->getSelections()) {

                    $selectionMinimalPrices = array();
                    $selectionMaximalPrices = array();

                    foreach ($option->getSelections() as $selection) {
                        if (!$selection->isSalable()) {
                            continue;
                        }

                        if ($selection->getSelectionCanChangeQty() && $option->getType() != 'multi' && $option->getType() != 'checkbox') {
                            $qty = 1;
                        } else {
                            $qty = $selection->getSelectionQty();
                        }

                        $selectionMinimalPrices[] = $this->getSelectionPrice($product, $selection, $qty);
                        $selectionMaximalPrices[] = $this->getSelectionPrice($product, $selection);
                    }

                    if (count($selectionMinimalPrices)) {
                        if ($option->getRequired()) {
                            $minimalPrice += min($selectionMinimalPrices);
                        }

                        if ($option->isMultiSelection()) {
                            $maximalPrice += array_sum($selectionMaximalPrices);
                        } else {
                            $maximalPrice += max($selectionMaximalPrices);
                        }
                    }
                }
            }
        }


        $minimalPrice = $this->_applySpecialPrice($product, $minimalPrice);
        $maximalPrice = $this->_applySpecialPrice($product, $maximalPrice);

        if ($customOptions = $product->getOptions()) {
            foreach ($customOptions as $customOption) {
                if ($values = $customOption->getValues()) {
                    $prices = array();
                    foreach ($values as $value) {
                        $prices[] = $value->getPrice();
                    }
                    if (count($prices)) {
                        if ($customOption->getIsRequire()) {
                            $minimalPrice += min($prices);
                        }
                        $maximalPrice += max($prices);
                    }
                } else {
                    if ($customOption->getIsRequire()) {
                        $minimalPrice += $customOption->getPrice();
                    }
                    $maximalPrice += $customOption->getPrice();
                }
            }
        }

        if (is_null($which)) {
            return array($minimalPrice, $maximalPrice);
        } else if ($which = 'max') {
            return $maximalPrice;
        } else if ($which = 'min') {
            return $minimalPrice;
        }
        return 0;
    }

    /**
     * Calculate Minimal price of bundle (counting all required options)
     *
     * @param Mage_Catalog_Model_Product $product
     * @return decimal
     */
    public function getMinimalPrice($product)
    {
        return $this->getPrices($product, 'min');
    }

    /**
     * Calculate maximal price of bundle
     *
     * @param Mage_Catalog_Model_Product $product
     * @return decimal
     */
    public function getMaximalPrice($product)
    {
        return $this->getPrice($product, 'max');
    }

    /**
     * Get Options with attached Selections collection
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Bundle_Model_Mysql4_Option_Collection
     */
    public function getOptions($product)
    {
        $product->getTypeInstance()->setStoreFilter($product->getStoreId());

        $optionCollection = $product->getTypeInstance()->getOptionsCollection();

        $selectionCollection = $product->getTypeInstance()->getSelectionsCollection(
                $product->getTypeInstance()->getOptionsIds()
            );

        return $optionCollection->appendSelections($selectionCollection, false, false);
    }

    /**
     * Calculate price of selection
     *
     * @param Mage_Catalog_Model_Product $bundleProduct
     * @param Mage_Catalog_Model_Product $selectionProduct
     * @param decimal $selectionQty
     * @return decimal
     */
    public function getSelectionPrice($bundleProduct, $selectionProduct, $selectionQty = null, $multiplyQty = true)
    {
        if (is_null($selectionQty)) {
            $selectionQty = $selectionProduct->getSelectionQty();
        }

        if ($bundleProduct->getPriceType() == Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes_Extend::DYNAMIC){
            if ($multiplyQty) {
                return $selectionProduct->getFinalPrice($selectionQty)*$selectionQty;
            } else {
                return $selectionProduct->getFinalPrice($selectionQty);
            }
        } else {
            if ($selectionProduct->getSelectionPriceType()) {
                return ($bundleProduct->getPrice()*$selectionProduct->getSelectionPriceValue()/100)*$selectionQty;
            } else {
                return $selectionProduct->getSelectionPriceValue()*$selectionQty;
            }
        }
    }

    /**
     * Calculate selection price for front view (with applied special of bundle)
     *
     * @param Mage_Catalog_Model_Product $bundleProduct
     * @param Mage_Catalog_Model_Product $selectionProduct
     * @param decimal
     * @return decimal
     */
    public function getSelectionPreFinalPrice($bundleProduct, $selectionProduct, $qty = null)
    {
        return $this->_applySpecialPrice($bundleProduct, $this->getSelectionPrice($bundleProduct, $selectionProduct, $qty));
    }


    /**
     * Calculate final price of selection
     *
     * @param Mage_Catalog_Model_Product $bundleProduct
     * @param Mage_Catalog_Model_Product $selectionProduct
     * @param decimal $bundleQty
     * @param decimal $selectionQty
     * @return decimal
     */
    public function getSelectionFinalPrice($bundleProduct, $selectionProduct, $bundleQty, $selectionQty = null, $multiplyQty = true)
    {
        // apply bundle tier price
        $finalPrice = $this->_applyTierPrice($bundleProduct, $bundleQty, $this->getSelectionPrice($bundleProduct, $selectionProduct, $selectionQty, $multiplyQty));

        // apply bundle special price
        $finalPrice = $this->_applySpecialPrice($bundleProduct, $finalPrice);

        return $finalPrice;
    }

    /**
     * Apply tier price for bundle
     *
     * @param   Mage_Catalog_Model_Product $product
     * @param   decimal $qty
     * @param   decimal $finalPrice
     * @return  decimal
     */
    protected function _applyTierPrice($product, $qty, $finalPrice)
    {
        if (is_null($qty)) {
            return $finalPrice;
        }

        $tierPrice  = $product->getTierPrice($qty);
        if (is_numeric($tierPrice)) {
            $tierPrice = $finalPrice - ($finalPrice*$tierPrice)/100;
            $finalPrice = min($finalPrice, $tierPrice);
        }
        return $finalPrice;
    }

    /**
     * Get product tier price by qty
     *
     * @param   decimal $qty
     * @param   Mage_Catalog_Model_Product $product
     * @return  decimal
     */
    public function getTierPrice($qty=null, $product)
    {
        $allGroups = Mage_Customer_Model_Group::CUST_GROUP_ALL;
        $prices = $product->getData('tier_price');

        if (is_null($prices)) {
            if ($attribute = $product->getResource()->getAttribute('tier_price')) {
                $attribute->getBackend()->afterLoad($product);
                $prices = $product->getData('tier_price');
            }
        }

        if (is_null($prices) || !is_array($prices)) {
            if (!is_null($qty)) {
                return $product->getPrice();
            }
            return array(array(
                'price'         => $product->getPrice(),
                'website_price' => $product->getPrice(),
                'price_qty'     => 1,
                'cust_group'    => $allGroups,
            ));
        }

        $custGroup = $this->_getCustomerGroupId($product);
        if ($qty) {
            $prevQty = 1;
            $prevPrice = 0;
            $prevGroup = $allGroups;

            foreach ($prices as $price) {
                if ($price['cust_group']!=$custGroup && $price['cust_group']!=$allGroups) {
                    // tier not for current customer group nor is for all groups
                    continue;
                }
                if ($qty < $price['price_qty']) {
                    // tier is higher than product qty
                    continue;
                }
                if ($price['price_qty'] < $prevQty) {
                    // higher tier qty already found
                    continue;
                }
                if ($price['price_qty'] == $prevQty && $prevGroup != $allGroups && $price['cust_group'] == $allGroups) {
                    // found tier qty is same as current tier qty but current tier group is ALL_GROUPS
                    continue;
                }
                $prevPrice  = $price['website_price'];
                $prevQty    = $price['price_qty'];
                $prevGroup  = $price['cust_group'];
            }
            return $prevPrice;
        } else {
            foreach ($prices as $i=>$price) {
                if ($price['cust_group']!=$custGroup && $price['cust_group']!=$allGroups) {
                    unset($prices[$i]);
                }
            }
        }

        return ($prices) ? $prices : array();
    }

    /**
     * Apply special price for bundle
     *
     * @param   Mage_Catalog_Model_Product $product
     * @param   decimal $finalPrice
     * @return  decimal
     */
    protected function _applySpecialPrice($product, $finalPrice)
    {
        $specialPrice = $product->getSpecialPrice();
        if (is_numeric($specialPrice)) {
            $today = floor(time()/86400)*86400;
            $from = floor(strtotime($product->getSpecialFromDate())/86400)*86400;
            $to = floor(strtotime($product->getSpecialToDate())/86400)*86400;

            if ($product->getSpecialFromDate() && $today < $from) {
            } elseif ($product->getSpecialToDate() && $today > $to) {
            } else {
                // special price in percents
                $specialPrice = ($finalPrice*$specialPrice)/100;
                $finalPrice = min($finalPrice, $specialPrice);
            }
        }
        return $finalPrice;
    }

    public static function calculatePrice($basePrice, $specialPrice, $specialPriceFrom, $specialPriceTo, $rulePrice = false, $wId = null, $gId = null, $productId = null)
    {
        $resource = Mage::getResourceSingleton('bundle/bundle');
        $productPriceTypeId = Mage::getSingleton('eav/entity_attribute')->getIdByCode('catalog_product', 'price_type');
        if ($wId instanceof Mage_Core_Model_Store) {
            $store = $wId->getId();
        } else {
            $wId = Mage::app()->getStore();
            $store = Mage::app()->getStore()->getId();
        }

        if (!$gId) {
            $gId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        }

        $attributes = $resource->getAttributeData($productId, $productPriceTypeId, $store);

        $options = array(0);

        $results = $resource->getSelectionsData($productId);

        if (!$attributes[0]['value']) { //dynamic
            $dataRetreiver = Mage::getSingleton('catalogindex/data_simple');
            $childIds = array();

            foreach ($results as $key => $result) {
                if (!$result['product_id']) {
                    continue;
                }
                $results[$key]['final_price'] = $dataRetreiver->getFinalPrice($result['product_id'], $wId, $gId);
                $tiers = $resource->getTierPrices($result['product_id'], $wId->getWebsiteId());

                if ($result['selection_can_change_qty'] && $result['type'] != 'multi' && $result['type'] != 'checkbox') {
                    $qty = 1;
                } else {
                    $qty = $result['selection_qty'];
                }

                $allGroups = Mage_Customer_Model_Group::CUST_GROUP_ALL;

                $prevQty = 1;
                $prevPrice = $results[$key]['final_price'];
                $prevGroup = $allGroups;

                foreach ($tiers as $tier) {

                    if (!is_numeric($gId)) {
                        $_gId = $gId->getId();
                    } else {
                        $_gId = $gId;
                    }

                    if ($tier['customer_group_id'] != $_gId && $tier['all_groups']!=1) {
                        // tier not for current customer group nor is for all groups
                        continue;
                    }

                    if ($qty < $tier['qty']) {
                        // tier is higher than product qty
                        continue;
                    }

                    if ($tier['qty'] < $prevQty) {
                        // higher tier qty already found
                        continue;
                    }

                    if ($tier['qty'] == $prevQty && $prevGroup != $allGroups && $tier['all_groups'] == 1) {
                        // found tier qty is same as current tier qty but current tier group is ALL_GROUPS
                        continue;
                    }

                    $prevPrice  = $tier['value'];
                    $prevQty    = $tier['qty'];
                    $prevGroup  = $tier['customer_group_id'];
                }
                $results[$key]['final_price'] = $prevPrice;
            }

            foreach ($results as $result) {
                if (!$result['product_id']) {
                    continue;
                }
                if ($result['selection_can_change_qty'] && $result['type'] != 'multi' && $result['type'] != 'checkbox') {
                    $qty = 1;
                } else {
                    $qty = $result['selection_qty'];
                }

                $selectionPrice = $result['final_price']*$qty;

                if (isset($options[$result['option_id']])) {
                    $options[$result['option_id']] = min($options[$result['option_id']], $selectionPrice);
                } else {
                    $options[$result['option_id']] = $selectionPrice;
                }
            }

            $basePrice = array_sum($options);

        } else { //fixed
            foreach ($results as $result) {
                if (!$result['product_id']) {
                    continue;
                }
                if ($result['selection_price_type']) {
                    $selectionPrice = $basePrice*$result['selection_price_value']/100;
                } else {
                    $selectionPrice = $result['selection_price_value'];
                }

                if ($result['selection_can_change_qty'] && $result['type'] != 'multi' && $result['type'] != 'checkbox') {
                    $qty = 1;
                } else {
                    $qty = $result['selection_qty'];
                }

                $selectionPrice = $selectionPrice*$qty;

                if (isset($options[$result['option_id']])) {
                    $options[$result['option_id']] = min($options[$result['option_id']], $selectionPrice);
                } else {
                    $options[$result['option_id']] = $selectionPrice;
                }
            }

            $basePrice = $basePrice + array_sum($options);
        }

        $finalPrice = $basePrice;

        $today = floor(time()/86400)*86400;
        $from = floor(strtotime($specialPriceFrom)/86400)*86400;
        $to = floor(strtotime($specialPriceTo)/86400)*86400;

        if ($specialPrice !== null && $specialPrice !== false) {
            if ($specialPriceFrom && $today < $from) {
            } elseif ($specialPriceTo && $today > $to) {
            } else {
                // special price in percents
                $specialPrice = ($finalPrice*$specialPrice)/100;
                $finalPrice = min($finalPrice, $specialPrice);
            }
        }

        /*
         * adding customer defined options price
         */
        $customOptions = Mage::getResourceSingleton('catalog/product_option_collection')->reset();
        $customOptions->addFieldToFilter('is_require', '1')
            ->addProductToFilter($productId)
            ->addPriceToResult($store, 'price')
            ->addValuesToResult();

        foreach ($customOptions as $customOption) {
            if ($values = $customOption->getValues()) {
                $prices = array();
                foreach ($values as $value) {
                    $prices[] = $value->getPrice();
                }
                if (count($prices)) {
                    $finalPrice += min($prices);
                }
            } else {
                $finalPrice += $customOption->getPrice();
            }
        }

        if ($rulePrice === false) {
            $date = mktime(0,0,0);
            $rulePrice = Mage::getResourceModel('catalogrule/rule')->getRulePrice($date, $wId, $gId, $productId);
        }

        if ($rulePrice !== null && $rulePrice !== false) {
            $finalPrice = min($finalPrice, $rulePrice);
        }

        $finalPrice = max($finalPrice, 0);

        return $finalPrice;
    }


    /*
    public function getCustomOptionPrices($productId, $storeId, $which = null) {

        $optionsCollection = Mage::getResourceModel('catalog/product_option_collection')
            ->addProductToFilter($productId)
            ->;

        if (is_null($which)) {
            return array($minimalPrice, $maximalPrice);
        } else if ($which = 'max') {
            return $maximalPrice;
        } else if ($which = 'min') {
            return $minimalPrice;
        }
        return 0;
    }
    */

}