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
 * @package     Mage_Bundle
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
    const PRICE_TYPE_FIXED      = 1;
    const PRICE_TYPE_DYNAMIC    = 0;

    /**
     * Return product base price
     *
     * @return string
     */
    public function getPrice($product)
    {
        if ($product->getPriceType() == self::PRICE_TYPE_FIXED) {
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
        $basePrice  = $finalPrice;

        /**
         * Just product with fixed price calculation has price
         */
        if ($finalPrice) {
            $tierPrice      = $this->_applyTierPrice($product, $qty, $finalPrice);
            $specialPrice   = $this->_applySpecialPrice($product, $finalPrice);
            $finalPrice     = min(array($tierPrice, $specialPrice));

            $product->setFinalPrice($finalPrice);
            Mage::dispatchEvent('catalog_product_get_final_price', array('product'=>$product));
            $finalPrice = $product->getData('final_price');
        }
        $basePrice = $finalPrice;

        if ($product->hasCustomOptions()) {
            $customOption = $product->getCustomOption('bundle_option_ids');
            $customOption = $product->getCustomOption('bundle_selection_ids');
            $selectionIds = unserialize($customOption->getValue());
            $selections = $product->getTypeInstance(true)->getSelectionsByIds($selectionIds, $product);
            foreach ($selections->getItems() as $selection) {
                if ($selection->isSalable()) {
                    $selectionQty = $product->getCustomOption('selection_qty_' . $selection->getSelectionId());
                    if ($selectionQty) {
                        $finalPrice = $finalPrice + $this->getSelectionFinalPrice($product, $selection, $qty, $selectionQty->getValue());
                    }
                }
            }
        } else {
//            if ($options = $this->getOptions($product)) {
//                /* some strange thing
//                foreach ($options as $option) {
//                    $selectionCount = count($option->getSelections());
//                    if ($selectionCount) {
//                        foreach ($option->getSelections() as $selection) {
//                            if ($selection->isSalable() && ($selection->getIsDefault() || ($option->getRequired() &&)) {
//                                $finalPrice = $finalPrice + $this->getSelectionPrice($product, $selection);
//                            }
//                        }
//                    }
//                }
//                */
//            }
        }

        $finalPrice = $finalPrice + $this->_applyOptionsPrice($product, $qty, $basePrice) - $basePrice;
        $product->setFinalPrice($finalPrice);

        return max(0, $product->getData('final_price'));
    }

    public function getChildFinalPrice($product, $productQty, $childProduct, $childProductQty)
    {
        return $this->getSelectionFinalPrice($product, $childProduct, $productQty, $childProductQty, false);
    }

    /**
     * Retrieve Price
     *
     * @param unknown_type $product
     * @param unknown_type $which
     * @return unknown
     */
    public function getPrices($product, $which = null)
    {
        // check calculated price index
        if ($product->getData('min_price') && $product->getData('max_price')) {
            $minimalPrice = $product->getData('min_price');
            $maximalPrice = $product->getData('max_price');
        } else {
            /**
             * Check if product price is fixed
             */
            $finalPrice = $product->getFinalPrice();
            if ($product->getPriceType() == self::PRICE_TYPE_FIXED) {
                $minimalPrice = $maximalPrice = $finalPrice;
            } else { // PRICE_TYPE_DYNAMIC
                $minimalPrice = $maximalPrice = 0;
            }

            $options = $this->getOptions($product);
            $minPriceFounded = false;

            if ($options) {
                foreach ($options as $option) {
                    /* @var $option Mage_Bundle_Model_Option */
                    $selections = $option->getSelections();
                    if ($selections) {
                        $selectionMinimalPrices = array();
                        $selectionMaximalPrices = array();

                        foreach ($option->getSelections() as $selection) {
                            /* @var $selection Mage_Bundle_Model_Selection */
                            if (!$selection->isSalable()) {
                                /**
                                 * @todo CatalogInventory Show out of stock Products
                                 */
                                continue;
                            }

                            $qty = $selection->getSelectionQty();
                            if ($selection->getSelectionCanChangeQty() && $option->isMultiSelection()) {
                                $qty = min(1, $qty);
                            }

                            $selectionMinimalPrices[] = $this->getSelectionFinalPrice($product, $selection, 1, $qty);
                            $selectionMaximalPrices[] = $this->getSelectionFinalPrice($product, $selection, 1);
                        }

                        if (count($selectionMinimalPrices)) {
                            $selMinPrice = min($selectionMinimalPrices);
                            if ($option->getRequired()) {
                                $minimalPrice += $selMinPrice;
                                $minPriceFounded = true;
                            } elseif (true !== $minPriceFounded) {
                                $minPriceFounded = false === $minPriceFounded ? $selMinPrice : min($minPriceFounded, $selMinPrice);
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
            // condition is TRUE when all product options are NOT required
            if (! is_bool($minPriceFounded)) {
                $minimalPrice = $minPriceFounded;
            }

            if ($product->getPriceType() == self::PRICE_TYPE_DYNAMIC) {
                $minimalPrice = $this->_applySpecialPrice($product, $minimalPrice);
                $maximalPrice = $this->_applySpecialPrice($product, $maximalPrice);
            }

            $customOptions = $product->getOptions();

            if ($product->getPriceType() == self::PRICE_TYPE_FIXED && $customOptions) {
                foreach ($customOptions as $customOption) {
                    /* @var $customOption Mage_Catalog_Model_Product_Option */
                    $values = $customOption->getValues();
                    if ($values) {
                        $prices = array();
                        foreach ($values as $value) {
                            /* @var $value Mage_Catalog_Model_Product_Option_Value */
                            $valuePrice = $value->getPrice(true);

                            $prices[] = $valuePrice;
                        }
                        if (count($prices)) {
                            if ($customOption->getIsRequire()) {
                                $minimalPrice += min($prices);
                            }
                            $maximalPrice += max($prices);
                        }
                    } else {
                        $valuePrice = $customOption->getPrice(true);

                        if ($customOption->getIsRequire()) {
                            $minimalPrice += $valuePrice;
                        }
                        $maximalPrice += $valuePrice;
                    }
                }
            }
        }

        if ($which == 'max') {
            return $maximalPrice;
        } else if ($which == 'min') {
            return $minimalPrice;
        }

        return array($minimalPrice, $maximalPrice);
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
        $product->getTypeInstance(true)
            ->setStoreFilter($product->getStoreId(), $product);

        $optionCollection = $product->getTypeInstance(true)
            ->getOptionsCollection($product);

        $selectionCollection = $product->getTypeInstance(true)
            ->getSelectionsCollection(
                $product->getTypeInstance(true)->getOptionsIds($product),
                $product
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

        if ($bundleProduct->getPriceType() == self::PRICE_TYPE_DYNAMIC) {
            if ($multiplyQty) {
                $selectionPrice = $selectionProduct->getFinalPrice($selectionQty) * $selectionQty;
            } else {
                $selectionPrice = $selectionProduct->getFinalPrice($selectionQty);
            }
            return $selectionPrice;
        } else {
            if ($selectionProduct->getSelectionPriceType()) { // percent
                return $bundleProduct->getPrice() * ($selectionProduct->getSelectionPriceValue() / 100) * $selectionQty;
            } else {
                return $selectionProduct->getSelectionPriceValue() * $selectionQty;
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
        $selectionPrice = $this->getSelectionPrice($bundleProduct, $selectionProduct, $selectionQty, $multiplyQty);
        // apply bundle tier price
        $tierPrice = $this->_applyTierPrice($bundleProduct, $bundleQty, $selectionPrice);

        // apply bundle special price
        $specialPrice = $this->_applySpecialPrice($bundleProduct, $selectionPrice);

        return min(array($tierPrice, $specialPrice));
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
            $tierPrice = $finalPrice - ($finalPrice * ($tierPrice / 100));
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

                if ($price['website_price'] > $prevPrice) {
                    $prevPrice  = $price['website_price'];
                    $prevQty    = $price['price_qty'];
                    $prevGroup  = $price['cust_group'];
                }
            }

            return $prevPrice;
        } else {
            $qtyCache = array();
            foreach ($prices as $i => $price) {
                if ($price['cust_group'] != $custGroup && $price['cust_group'] != $allGroups) {
                    unset($prices[$i]);
                } else if (isset($qtyCache[$price['price_qty']])) {
                    $j = $qtyCache[$price['price_qty']];
                    if ($prices[$j]['website_price'] < $price['website_price']) {
                        unset($prices[$j]);
                        $qtyCache[$price['price_qty']] = $i;
                    } else {
                        unset($prices[$i]);
                    }
                } else {
                    $qtyCache[$price['price_qty']] = $i;
                }
            }
        }

        return ($prices) ? $prices : array();
    }

    /**
     * Calculate product price based on special price data and price rules
     *
     * @param   float $basePrice
     * @param   float $specialPrice
     * @param   string $specialPriceFrom
     * @param   string $specialPriceTo
     * @param   float|null|false $rulePrice
     * @param   mixed $wId
     * @param   mixed $gId
     * @param   null|int $productId
     * @return  float
     */
    public static function calculatePrice($basePrice, $specialPrice, $specialPriceFrom, $specialPriceTo, $rulePrice = false, $wId = null, $gId = null, $productId = null)
    {
        $resource = Mage::getResourceSingleton('bundle/bundle');
        $selectionResource = Mage::getResourceSingleton('bundle/selection');
        $productPriceTypeId = Mage::getSingleton('eav/entity_attribute')->getIdByCode('catalog_product', 'price_type');

        if ($wId instanceof Mage_Core_Model_Store) {
            $store = $wId->getId();
            $wId = $wId->getWebsiteId();
        } else {
            $store = Mage::app()->getStore($wId)->getId();
            $wId = Mage::app()->getStore($wId)->getWebsiteId();
            //$store = Mage::app()->getWebsite($wId)->getDefaultGroup()->getDefaultStoreId();
        }

        if (!$gId) {
            $gId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        } else if ($gId instanceof Mage_Customer_Model_Group) {
            $gId = $gId->getId();
        }

        if (!isset(self::$attributeCache[$productId]['price_type'])) {
            $attributes = $resource->getAttributeData($productId, $productPriceTypeId, $store);
            self::$attributeCache[$productId]['price_type'] = $attributes;
        } else {
            $attributes = self::$attributeCache[$productId]['price_type'];
        }

        $options = array(0);
        $results = $resource->getSelectionsData($productId);

        if (!$attributes || !$attributes[0]['value']) { //dynamic
            foreach ($results as $result) {
                if (!$result['product_id']) {
                    continue;
                }

                if ($result['selection_can_change_qty'] && $result['type'] != 'multi' && $result['type'] != 'checkbox') {
                    $qty = 1;
                } else {
                    $qty = $result['selection_qty'];
                }

                $result['final_price'] = $selectionResource->getPriceFromIndex($result['product_id'], $qty, $store, $gId);

                $selectionPrice = $result['final_price']*$qty;

                if (isset($options[$result['option_id']])) {
                    $options[$result['option_id']] = min($options[$result['option_id']], $selectionPrice);
                } else {
                    $options[$result['option_id']] = $selectionPrice;
                }
            }
            $basePrice = array_sum($options);
        }
        else { //fixed
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

        $finalPrice = self::calculateSpecialPrice($basePrice, $specialPrice, $specialPriceFrom, $specialPriceTo, $store);

        /**
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
            $rulePrice = Mage::getResourceModel('catalogrule/rule')->getRulePrice(Mage::app()->getLocale()->storeTimeStamp($store), $wId, $gId, $productId);
        }

        if ($rulePrice !== null && $rulePrice !== false) {
            $finalPrice = min($finalPrice, $rulePrice);
        }

        $finalPrice = max($finalPrice, 0);

        return $finalPrice;
    }

    /**
     * Calculate and apply special price
     *
     * @param float $finalPrice
     * @param float $specialPrice
     * @param string $specialPriceFrom
     * @param string $specialPriceTo
     * @param mixed $store
     * @return float
     */
    public static function calculateSpecialPrice($finalPrice, $specialPrice, $specialPriceFrom, $specialPriceTo, $store = null)
    {
        if (!is_null($specialPrice) && $specialPrice != false) {
            if (Mage::app()->getLocale()->isStoreDateInInterval($store, $specialPriceFrom, $specialPriceTo)) {
                $specialPrice   = Mage::app()->getStore()->roundPrice($finalPrice * $specialPrice / 100);
                $finalPrice     = min($finalPrice, $specialPrice);
            }
        }
        return $finalPrice;
    }

    /**
     * Check is tier price value fixed or percent of original price
     *
     * @return bool
     */
    public function isTierPriceFixed()
    {
        return false;
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
