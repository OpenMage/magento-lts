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
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bundle Price Model
 *
 * @category Mage
 * @package  Mage_Bundle
 * @author   Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Model_Product_Price extends Mage_Catalog_Model_Product_Type_Price
{
    /**
     * Fixed price type
     */
    const PRICE_TYPE_FIXED      = 1;

    /**
     * Dynamic price type
     */
    const PRICE_TYPE_DYNAMIC    = 0;

    /**
     * Flag which indicates - is min/max prices have been calculated by index
     *
     * @var bool
     */
    protected $_isPricesCalculatedByIndex;

    /**
     * Is min/max prices have been calculated by index
     *
     * @return bool
     */
    public function getIsPricesCalculatedByIndex()
    {
        return $this->_isPricesCalculatedByIndex;
    }

    /**
     * Return product base price
     *
     * @param Mage_Catalog_Model_Product $product
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
     * Get Total price  for Bundle items
     *
     * @param Mage_Catalog_Model_Product $product
     * @param null|float $qty
     * @return float
     */
    public function getTotalBundleItemsPrice($product, $qty = null)
    {
        $price = 0.0;
        if ($product->hasCustomOptions()) {
            $customOption = $product->getCustomOption('bundle_selection_ids');
            if ($customOption) {
                $selectionIds = unserialize($customOption->getValue());
                $selections = $product->getTypeInstance(true)->getSelectionsByIds($selectionIds, $product);
                $selections->addTierPriceData();
                Mage::dispatchEvent('prepare_catalog_product_collection_prices', array(
                    'collection' => $selections,
                    'store_id' => $product->getStoreId(),
                ));
                foreach ($selections->getItems() as $selection) {
                    if ($selection->isSalable()) {
                        $selectionQty = $product->getCustomOption('selection_qty_' . $selection->getSelectionId());
                        if ($selectionQty) {
                            $price += $this->getSelectionFinalTotalPrice($product, $selection, $qty,
                                $selectionQty->getValue());
                        }
                    }
                }
            }
        }
        return $price;
    }

    /**
     * Get product final price
     *
     * @param   double                     $qty
     * @param   Mage_Catalog_Model_Product $product
     * @return  double
     */
    public function getFinalPrice($qty = null, $product)
    {
        if (is_null($qty) && !is_null($product->getCalculatedFinalPrice())) {
            return $product->getCalculatedFinalPrice();
        }

        $finalPrice = $this->getBasePrice($product, $qty);
        $product->setFinalPrice($finalPrice);
        Mage::dispatchEvent('catalog_product_get_final_price', array('product' => $product, 'qty' => $qty));
        $finalPrice = $product->getData('final_price');

        $finalPrice = $this->_applyOptionsPrice($product, $qty, $finalPrice);
        $finalPrice += $this->getTotalBundleItemsPrice($product, $qty);

        $product->setFinalPrice($finalPrice);
        return max(0, $product->getData('final_price'));
    }

    /**
     * Returns final price of a child product
     *
     * @param Mage_Catalog_Model_Product $product
     * @param float                      $productQty
     * @param Mage_Catalog_Model_Product $childProduct
     * @param float                      $childProductQty
     * @return decimal
     */
    public function getChildFinalPrice($product, $productQty, $childProduct, $childProductQty)
    {
        return $this->getSelectionFinalTotalPrice($product, $childProduct, $productQty, $childProductQty, false);
    }

    /**
     * Retrieve Price
     *
     * @deprecated after 1.5.1.0
     * @see Mage_Bundle_Model_Product_Price::getTotalPrices()
     *
     * @param  Mage_Catalog_Model_Product $product
     * @param  string                     $which
     * @return decimal|array
     */
    public function getPrices($product, $which = null)
    {
        return $this->getTotalPrices($product, $which);
    }

    /**
     * Retrieve Prices depending on tax
     *
     * @deprecated after 1.5.1.0
     * @see Mage_Bundle_Model_Product_Price::getTotalPrices()
     *
     * @param  Mage_Catalog_Model_Product $product
     * @param  string                     $which
     * @param  bool|null                  $includeTax
     * @return decimal|array
     */
    public function getPricesDependingOnTax($product, $which = null, $includeTax = null)
    {
        return $this->getTotalPrices($product, $which, $includeTax);
    }

    /**
     * Retrieve Price considering tier price
     *
     * @param  Mage_Catalog_Model_Product $product
     * @param  string|null                $which
     * @param  bool|null                  $includeTax
     * @param  bool                       $takeTierPrice
     * @return decimal|array
     */
    public function getTotalPrices($product, $which = null, $includeTax = null, $takeTierPrice = true)
    {
        $isPriceFixedType = ($product->getPriceType() == self::PRICE_TYPE_FIXED);
        $this->_isPricesCalculatedByIndex = ($product->getData('min_price') && $product->getData('max_price'));
        $taxHelper = $this->_getHelperData('tax');

        if ($this->_isPricesCalculatedByIndex && !$includeTax) {
            $minimalPrice = $taxHelper->getPrice($product, $product->getData('min_price'), $includeTax);
            $maximalPrice = $taxHelper->getPrice($product, $product->getData('max_price'), $includeTax);
        } else {
            /**
             * Check if product price is fixed
             */
            $finalPrice = $product->getFinalPrice();
            if ($isPriceFixedType) {
                $minimalPrice = $maximalPrice = $taxHelper->getPrice($product, $finalPrice, $includeTax);
            } else { // PRICE_TYPE_DYNAMIC
                $minimalPrice = $maximalPrice = 0;
            }

            $minimalPrice += $this->_getMinimalBundleOptionsPrice($product, $includeTax, $takeTierPrice);
            $maximalPrice += $this->_getMaximalBundleOptionsPrice($product, $includeTax, $takeTierPrice);

            $customOptions = $product->getOptions();
            if ($isPriceFixedType && $customOptions) {
                foreach ($customOptions as $customOption) {
                    /* @var $customOption Mage_Catalog_Model_Product_Option */
                    $minimalPrice += $taxHelper->getPrice(
                        $product,
                        $this->_getMinimalCustomOptionPrice($customOption),
                        $includeTax);
                    $maximalPrice += $taxHelper->getPrice(
                        $product,
                        $this->_getMaximalCustomOptionPrice($customOption),
                        $includeTax);
                }
            }
        }

        if ('max' == $which) {
            return $maximalPrice;
        } elseif ('min' == $which) {
            return $minimalPrice;
        }

        return array($minimalPrice, $maximalPrice);
    }

    /**
     * Get minimal possible price for bundle option
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool|null $includeTax
     * @param bool $takeTierPrice
     * @return int|mixed
     */
    protected function _getMinimalBundleOptionsPrice($product, $includeTax, $takeTierPrice)
    {
        $options = $this->getOptions($product);
        $minimalPrice = 0;
        $hasRequiredOptions = $this->_hasRequiredOptions($product);
        $selectionMinimalPrices = array();

        if (!$options) {
            return $minimalPrice;
        }

        foreach ($options as $option) {
            /* @var $option Mage_Bundle_Model_Option */
           $selectionPrices = $this->_getSelectionPrices($product, $option, $takeTierPrice, $includeTax);

            if (count($selectionPrices)) {
                $selectionMinPrice = min($selectionPrices);
                if ($option->getRequired()) {
                    $minimalPrice += $selectionMinPrice;
                } elseif (!$hasRequiredOptions) {
                    $selectionMinimalPrices[] = $selectionMinPrice;
                }
            }
        }
        // condition is TRUE when all product options are NOT required
        if (!$hasRequiredOptions) {
            $minimalPrice = min($selectionMinimalPrices);
        }
        return $minimalPrice;
    }

    /**
     * Get maximal possible price for bundle option
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool|null $includeTax
     * @param bool $takeTierPrice
     * @return float
     */
    protected function _getMaximalBundleOptionsPrice($product, $includeTax, $takeTierPrice)
    {
        $maximalPrice = 0;
        $options = $this->getOptions($product);

        if (!$options) {
            return $maximalPrice;
        }

        foreach ($options as $option) {
            $selectionPrices = $this->_getSelectionPrices($product, $option, $takeTierPrice, $includeTax);
            if (count($selectionPrices)) {
                $maximalPrice += ($option->isMultiSelection())
                    ? array_sum($selectionPrices)
                    : max($selectionPrices);
            }
        }
        return $maximalPrice;
    }

    /**
     * Get all prices for bundle option selection
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_Bundle_Model_Option $option
     * @param bool $takeTierPrice
     * @param bool|null $includeTax
     * @return array
     */
    protected function _getSelectionPrices($product, $option, $takeTierPrice, $includeTax)
    {
        $selectionPrices = array();
        $taxHelper = $this->_getHelperData('tax');
        $isPriceFixedType = ($product->getPriceType() == self::PRICE_TYPE_FIXED);

        $selections = $option->getSelections();
        if (!$selections) {
            return $selectionPrices;
        }

        foreach ($selections as $selection) {
            /* @var $selection Mage_Bundle_Model_Selection */
            if (!$selection->isSalable()) {
                /**
                 * @todo CatalogInventory Show out of stock Products
                 */
                continue;
            }

            $item = $isPriceFixedType ? $product : $selection;

            $selectionPrices[] = $taxHelper->getPrice(
                $item,
                $this->getSelectionFinalTotalPrice($product, $selection, 1, null, true, $takeTierPrice),
                $includeTax
            );
        }
        return $selectionPrices;
    }

    /**
     * Calculate Minimal price of bundle (counting all required options)
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return decimal
     */
    public function getMinimalPrice($product)
    {
        return $this->getPricesTierPrice($product, 'min');
    }

    /**
     * Calculate maximal price of bundle
     *
     * @param Mage_Catalog_Model_Product $product
     * @return decimal
     */
    public function getMaximalPrice($product)
    {
        return $this->getPricesTierPrice($product, 'max');
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
     * @deprecated after 1.6.2.0
     * @see Mage_Bundle_Model_Product_Price::getSelectionFinalTotalPrice()
     *
     * @param Mage_Catalog_Model_Product $bundleProduct
     * @param Mage_Catalog_Model_Product $selectionProduct
     * @param float|null                 $selectionQty
     * @param null|bool                  $multiplyQty      Whether to multiply selection's price by its quantity
     * @return float
     */
    public function getSelectionPrice($bundleProduct, $selectionProduct, $selectionQty = null, $multiplyQty = true)
    {
        return $this->getSelectionFinalTotalPrice($bundleProduct, $selectionProduct, 0, $selectionQty, $multiplyQty);
    }

    /**
     * Calculate selection price for front view (with applied special of bundle)
     *
     * @param Mage_Catalog_Model_Product $bundleProduct
     * @param Mage_Catalog_Model_Product $selectionProduct
     * @param decimal                    $qty
     * @return decimal
     */
    public function getSelectionPreFinalPrice($bundleProduct, $selectionProduct, $qty = null)
    {
        return $this->getSelectionPrice($bundleProduct, $selectionProduct, $qty);
    }

    /**
     * Calculate final price of selection
     *
     * @deprecated after 1.5.1.0
     * @see Mage_Bundle_Model_Product_Price::getSelectionFinalTotalPrice()
     *
     * @param  Mage_Catalog_Model_Product $bundleProduct
     * @param  Mage_Catalog_Model_Product $selectionProduct
     * @param  decimal                    $bundleQty
     * @param  decimal                    $selectionQty
     * @param  bool                       $multiplyQty
     * @return decimal
     */
    public function getSelectionFinalPrice($bundleProduct, $selectionProduct, $bundleQty, $selectionQty = null,
       $multiplyQty = true)
    {
        return $this->getSelectionFinalTotalPrice($bundleProduct, $selectionProduct, $bundleQty, $selectionQty,
            $multiplyQty);
    }

    /**
     * Calculate final price of selection
     * with take into account tier price
     *
     * @param  Mage_Catalog_Model_Product $bundleProduct
     * @param  Mage_Catalog_Model_Product $selectionProduct
     * @param  decimal                    $bundleQty
     * @param  decimal                    $selectionQty
     * @param  bool                       $multiplyQty
     * @param  bool                       $takeTierPrice
     * @return decimal
     */
    public function getSelectionFinalTotalPrice($bundleProduct, $selectionProduct, $bundleQty, $selectionQty,
        $multiplyQty = true, $takeTierPrice = true)
    {
        if (is_null($selectionQty)) {
            $selectionQty = $selectionProduct->getSelectionQty();
        }

        if ($bundleProduct->getPriceType() == self::PRICE_TYPE_DYNAMIC) {
            $price = $selectionProduct->getFinalPrice($takeTierPrice ? $selectionQty : 1);
        } else {
            if ($selectionProduct->getSelectionPriceType()) { // percent
                $product = clone $bundleProduct;
                $product->setFinalPrice($this->getPrice($product));
                Mage::dispatchEvent(
                    'catalog_product_get_final_price',
                    array('product' => $product, 'qty' => $bundleQty)
                );
                $price = $product->getData('final_price') * ($selectionProduct->getSelectionPriceValue() / 100);

            } else { // fixed
                $price = $selectionProduct->getSelectionPriceValue();
            }
        }

        $price = min($price,
            $this->_applyGroupPrice($bundleProduct, $price),
            $this->_applyTierPrice($bundleProduct, $bundleQty, $price),
            $this->_applySpecialPrice($bundleProduct, $price)
        );

        if ($multiplyQty) {
            $price *= $selectionQty;
        }

        return $price;
    }

    /**
     * Apply group price for bundle product
     *
     * @param Mage_Catalog_Model_Product $product
     * @param float $finalPrice
     * @return float
     */
    protected function _applyGroupPrice($product, $finalPrice)
    {
        $result = $finalPrice;
        $groupPrice = $product->getGroupPrice();

        if (is_numeric($groupPrice)) {
            $groupPrice = $finalPrice - ($finalPrice * ($groupPrice / 100));
            $result = min($finalPrice, $groupPrice);
        }

        return $result;
    }

    /**
     * Get product group price
     *
     * @param Mage_Catalog_Model_Product $product
     * @return float|null
     */
    public function getGroupPrice($product)
    {
        $groupPrices = $product->getData('group_price');

        if (is_null($groupPrices)) {
            $attribute = $product->getResource()->getAttribute('group_price');
            if ($attribute) {
                $attribute->getBackend()->afterLoad($product);
                $groupPrices = $product->getData('group_price');
            }
        }

        if (is_null($groupPrices) || !is_array($groupPrices)) {
            return null;
        }

        $customerGroup = $this->_getCustomerGroupId($product);

        $matchedPrice = 0;

        foreach ($groupPrices as $groupPrice) {
            if ($groupPrice['cust_group'] == $customerGroup && $groupPrice['website_price'] > $matchedPrice) {
                $matchedPrice = $groupPrice['website_price'];
                break;
            }
        }

        return $matchedPrice;
    }

    /**
     * Apply tier price for bundle
     *
     * @param   Mage_Catalog_Model_Product $product
     * @param   decimal                    $qty
     * @param   decimal                    $finalPrice
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
     * @param   decimal                    $qty
     * @param   Mage_Catalog_Model_Product $product
     * @return  decimal
     */
    public function getTierPrice($qty = null, $product)
    {
        $allGroups = Mage_Customer_Model_Group::CUST_GROUP_ALL;
        $prices = $product->getData('tier_price');

        if (is_null($prices)) {
            $attribute = $product->getResource()->getAttribute('tier_price');
            if ($attribute) {
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
                'cust_group'    => $allGroups
            ));
        }

        $custGroup = $this->_getCustomerGroupId($product);
        if ($qty) {
            $prevQty = 1;
            $prevPrice = 0;
            $prevGroup = $allGroups;

            foreach ($prices as $price) {
                if ($price['cust_group'] != $custGroup && $price['cust_group'] != $allGroups) {
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
     * @param   float            $basePrice
     * @param   float            $specialPrice
     * @param   string           $specialPriceFrom
     * @param   string           $specialPriceTo
     * @param   float|null|false $rulePrice
     * @param   mixed            $wId
     * @param   mixed            $gId
     * @param   null|int         $productId
     * @return  float
     */
    public static function calculatePrice($basePrice, $specialPrice, $specialPriceFrom, $specialPriceTo,
        $rulePrice = false, $wId = null, $gId = null, $productId = null)
    {
        $resource = Mage::getResourceSingleton('bundle/bundle');
        $selectionResource = Mage::getResourceSingleton('bundle/selection');
        $productPriceTypeId = Mage::getSingleton('eav/entity_attribute')->getIdByCode(
            Mage_Catalog_Model_Product::ENTITY,
            'price_type'
        );

        if ($wId instanceof Mage_Core_Model_Store) {
            $store = $wId->getId();
            $wId = $wId->getWebsiteId();
        } else {
            $store = Mage::app()->getStore($wId)->getId();
            $wId = Mage::app()->getStore($wId)->getWebsiteId();
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

                if ($result['selection_can_change_qty'] && $result['type'] != 'multi'
                    && $result['type'] != 'checkbox'
                ) {
                    $qty = 1;
                } else {
                    $qty = $result['selection_qty'];
                }

                $result['final_price'] = $selectionResource->getPriceFromIndex($result['product_id'], $qty, $store,
                    $gId);

                $selectionPrice = $result['final_price']*$qty;

                if (isset($options[$result['option_id']])) {
                    $options[$result['option_id']] = min($options[$result['option_id']], $selectionPrice);
                } else {
                    $options[$result['option_id']] = $selectionPrice;
                }
            }
            $basePrice = array_sum($options);
        } else {
            foreach ($results as $result) {
                if (!$result['product_id']) {
                    continue;
                }
                if ($result['selection_price_type']) {
                    $selectionPrice = $basePrice*$result['selection_price_value']/100;
                } else {
                    $selectionPrice = $result['selection_price_value'];
                }

                if ($result['selection_can_change_qty'] && $result['type'] != 'multi'
                    && $result['type'] != 'checkbox'
                ) {
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

        $finalPrice = self::calculateSpecialPrice($basePrice, $specialPrice, $specialPriceFrom, $specialPriceTo,
            $store);

        /**
         * adding customer defined options price
         */
        $customOptions = Mage::getResourceSingleton('catalog/product_option_collection')->reset();
        $customOptions->addFieldToFilter('is_require', '1')
            ->addProductToFilter($productId)
            ->addPriceToResult($store, 'price')
            ->addValuesToResult();

        foreach ($customOptions as $customOption) {
            $values = $customOption->getValues();
            if ($values) {
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
            $rulePrice = Mage::getResourceModel('catalogrule/rule')
                ->getRulePrice(Mage::app()->getLocale()->storeTimeStamp($store), $wId, $gId, $productId);
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
     * @param float  $finalPrice
     * @param float  $specialPrice
     * @param string $specialPriceFrom
     * @param string $specialPriceTo
     * @param mixed  $store
     * @return float
     */
    public static function calculateSpecialPrice($finalPrice, $specialPrice, $specialPriceFrom, $specialPriceTo,
         $store = null)
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
     * Check is group price value fixed or percent of original price
     *
     * @return bool
     */
    public function isGroupPriceFixed()
    {
        return false;
    }

    /**
     * Get data helper
     *
     * @param string $name
     * @return Mage_Core_Helper_Abstract
     */
    protected function _getHelperData($name)
    {
        return Mage::helper($name);
    }

    /**
     * Check if product has required options
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    protected function _hasRequiredOptions($product)
    {
        $options = $this->getOptions($product);
        if ($options) {
            foreach ($options as $option) {
                if ($option->getRequired()) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Get minimum possible price of custom options
     *
     * @param Mage_Catalog_Model_Product_Option $option
     * @return float
     */
    protected function _getMinimalCustomOptionPrice($option)
    {
        $prices = $this->_getCustomOptionValuesPrices($option);
        $minimalOptionPrice = ($prices) ? min($prices) : (float)$option->getPrice(true);
        $minimalPrice = ($option->getIsRequire()) ? $minimalOptionPrice : 0;
        return $minimalPrice;
    }

    /**
     * Get maximum possible price of custom options
     *
     * @param Mage_Catalog_Model_Product_Option $option
     * @return float
     */
    protected function _getMaximalCustomOptionPrice($option)
    {
        $prices = $this->_getCustomOptionValuesPrices($option);
        $maximalOptionPrice = ($option->isMultipleType()) ? array_sum($prices) : max($prices);
        $maximalPrice = ($prices) ? $maximalOptionPrice : (float)($option->getPrice(true));
        return $maximalPrice;
    }

    /**
     * Get all custom option values prices
     *
     * @param Mage_Catalog_Model_Product_Option $option
     * @return array
     */
    protected function _getCustomOptionValuesPrices($option)
    {
        $values = $option->getValues();
        $prices = array();
        if ($values) {
            foreach ($values as $value) {
                /* @var $value Mage_Catalog_Model_Product_Option_Value */
                $prices[] = $value->getPrice(true);
            }
        }
        return $prices;
    }
}
