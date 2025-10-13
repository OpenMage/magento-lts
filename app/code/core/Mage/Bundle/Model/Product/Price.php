<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

/**
 * Bundle Price Model
 *
 * @package    Mage_Bundle
 */
class Mage_Bundle_Model_Product_Price extends Mage_Catalog_Model_Product_Type_Price
{
    /**
     * Fixed price type
     */
    public const PRICE_TYPE_FIXED = 1;

    /**
     * Dynamic price type
     */
    public const PRICE_TYPE_DYNAMIC = 0;

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
     * @return string|float|int
     */
    public function getPrice($product)
    {
        if ($product->getPriceType() == self::PRICE_TYPE_FIXED) {
            return $product->getData('price');
        }

        return 0;
    }

    /**
     * Get Total price  for Bundle items
     *
     * @param Mage_Catalog_Model_Product $product
     * @param null|float $qty
     * @return float
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getTotalBundleItemsPrice($product, $qty = null)
    {
        $price = 0.0;
        if ($product->hasCustomOptions()) {
            $customOption = $product->getCustomOption('bundle_selection_ids');
            if ($customOption) {
                $selectionIds = unserialize($customOption->getValue(), ['allowed_classes' => false]);
                /** @var Mage_Bundle_Model_Product_Type $productType */
                $productType = $product->getTypeInstance(true);
                $selections = $productType->getSelectionsByIds($selectionIds, $product);
                $selections->addTierPriceData();
                Mage::dispatchEvent('prepare_catalog_product_collection_prices', [
                    'collection' => $selections,
                    'store_id' => $product->getStoreId(),
                ]);
                foreach ($selections->getItems() as $selection) {
                    if ($selection->isSalable()) {
                        $selectionQty = $product->getCustomOption('selection_qty_' . $selection->getSelectionId());
                        if ($selectionQty) {
                            $price += $this->getSelectionFinalTotalPrice(
                                $product,
                                $selection,
                                $qty,
                                $selectionQty->getValue(),
                            );
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
     * @param float|null $qty
     * @param Mage_Catalog_Model_Product $product
     * @return float
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getFinalPrice($qty, $product)
    {
        if (is_null($qty) && !is_null($product->getCalculatedFinalPrice())) {
            return $product->getCalculatedFinalPrice();
        }

        $finalPrice = $this->getBasePrice($product, $qty);
        $product->setFinalPrice($finalPrice);
        Mage::dispatchEvent('catalog_product_get_final_price', ['product' => $product, 'qty' => $qty]);
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
     * @param float $productQty
     * @param Mage_Catalog_Model_Product $childProduct
     * @param float $childProductQty
     * @return float
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getChildFinalPrice($product, $productQty, $childProduct, $childProductQty)
    {
        return $this->getSelectionFinalTotalPrice($product, $childProduct, $productQty, $childProductQty, false);
    }

    /**
     * Retrieve Price
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $which
     * @return float|array
     * @throws Mage_Core_Model_Store_Exception
     * @deprecated after 1.5.1.0
     * @see Mage_Bundle_Model_Product_Price::getTotalPrices()
     *
     */
    public function getPrices($product, $which = null)
    {
        return $this->getTotalPrices($product, $which);
    }

    /**
     * Retrieve Prices depending on tax
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $which
     * @param bool|null $includeTax
     * @return float|array
     * @throws Mage_Core_Model_Store_Exception
     * @see Mage_Bundle_Model_Product_Price::getTotalPrices()
     *
     * @deprecated after 1.5.1.0
     */
    public function getPricesDependingOnTax($product, $which = null, $includeTax = null)
    {
        return $this->getTotalPrices($product, $which, $includeTax);
    }

    /**
     * Retrieve Price considering tier price
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string|null $which
     * @param bool|null $includeTax
     * @param bool $takeTierPrice
     * @return float|array
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getTotalPrices($product, $which = null, $includeTax = null, $takeTierPrice = true)
    {
        $this->_isPricesCalculatedByIndex = ($product->getData('min_price') && $product->getData('max_price'));
        /** @var Mage_Tax_Helper_Data $taxHelper */
        $taxHelper = $this->_getHelperData('tax');

        if ($this->_isPricesCalculatedByIndex) {
            $minimalPrice = $taxHelper->getPrice(
                $product,
                $product->getData('min_price'),
                $includeTax,
                null,
                null,
                null,
                null,
                null,
                false,
            );
            $maximalPrice = $taxHelper->getPrice(
                $product,
                $product->getData('max_price'),
                $includeTax,
                null,
                null,
                null,
                null,
                null,
                false,
            );
        } else {
            $isPriceFixedType = ($product->getPriceType() == self::PRICE_TYPE_FIXED);
            /**
             * Check if product price is fixed
             */
            $finalPrice = $product->getFinalPrice();
            if ($isPriceFixedType) {
                $minimalPrice = $maximalPrice = $taxHelper->getPrice(
                    $product,
                    $finalPrice,
                    $includeTax,
                    null,
                    null,
                    null,
                    null,
                    null,
                    false,
                );
            } else { // PRICE_TYPE_DYNAMIC
                $minimalPrice = $maximalPrice = 0;
            }

            $minimalPrice += $this->_getMinimalBundleOptionsPrice($product, $includeTax, $takeTierPrice);
            $maximalPrice += $this->_getMaximalBundleOptionsPrice($product, $includeTax, $takeTierPrice);

            $customOptions = $product->getOptions();
            if ($isPriceFixedType && $customOptions) {
                foreach ($customOptions as $customOption) {
                    $minimalPrice += $taxHelper->getPrice(
                        $product,
                        $this->_getMinimalCustomOptionPrice($customOption),
                        $includeTax,
                    );
                    $maximalPrice += $taxHelper->getPrice(
                        $product,
                        $this->_getMaximalCustomOptionPrice($customOption),
                        $includeTax,
                    );
                }
            }
        }

        $minimalPrice = $product->getStore()->roundPrice($minimalPrice);
        $maximalPrice = $product->getStore()->roundPrice($maximalPrice);

        if ($which === 'max') {
            return $maximalPrice;
        }

        if ($which === 'min') {
            return $minimalPrice;
        }

        return [$minimalPrice, $maximalPrice];
    }

    /**
     * Get minimal possible price for bundle option
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool|null $includeTax
     * @param bool $takeTierPrice
     * @return int|mixed
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _getMinimalBundleOptionsPrice($product, $includeTax, $takeTierPrice)
    {
        $options = $this->getOptions($product);
        $minimalPrice = 0;
        $minimalPriceWithTax = 0;
        $hasRequiredOptions = $this->_hasRequiredOptions($product);
        $selectionMinimalPrices = [];
        $selectionMinimalPricesWithTax = [];

        if (!$options) {
            return $minimalPrice;
        }

        foreach ($options as $option) {
            /** @var Mage_Bundle_Model_Option $option */
            $selectionPrices = $this->_getSelectionPrices($product, $option, $takeTierPrice, $includeTax);
            $selectionPricesWithTax = $this->_getSelectionPrices($product, $option, $takeTierPrice, true);

            if (count($selectionPrices)) {
                $selectionMinPrice = is_array($selectionPrices) ? min($selectionPrices) : $selectionPrices;
                $selectMinPriceWithTax = is_array($selectionPricesWithTax) ?
                    min($selectionPricesWithTax) : $selectionPricesWithTax;
                if ($option->getRequired()) {
                    $minimalPrice += $selectionMinPrice;
                    $minimalPriceWithTax += $selectMinPriceWithTax;
                } elseif (!$hasRequiredOptions) {
                    $selectionMinimalPrices[] = $selectionMinPrice;
                    $selectionMinimalPricesWithTax[] = $selectMinPriceWithTax;
                }
            }
        }

        // condition is TRUE when all product options are NOT required
        if (!$hasRequiredOptions) {
            $minimalPrice = empty($selectionMinimalPrices) ? 0 : min($selectionMinimalPrices);
            $minimalPriceWithTax = empty($selectionMinimalPricesWithTax) ? max(0, $minimalPrice) : min($selectionMinimalPricesWithTax);
        }

        /** @var Mage_Tax_Helper_Data $taxHelper */
        $taxHelper = $this->_getHelperData('tax');
        $taxConfig = $taxHelper->getConfig();

        //In the case of total base calculation we round the tax first and
        //deduct the tax from the price including tax
        if ($taxConfig->priceIncludesTax($product->getStore())
            && Mage_Tax_Model_Calculation::CALC_TOTAL_BASE == $taxConfig->getAlgorithm($product->getStore())
            && ($minimalPriceWithTax > $minimalPrice)
        ) {
            //We convert the value to string to maintain the precision
            $tax = (string) ($minimalPriceWithTax - $minimalPrice);
            $roundedTax = $this->_getApp()->getStore()->roundPrice($tax);
            $minimalPrice = $minimalPriceWithTax - $roundedTax;
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
     * @throws Mage_Core_Model_Store_Exception
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
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _getSelectionPrices($product, $option, $takeTierPrice, $includeTax)
    {
        $selectionPrices = [];
        /** @var Mage_Tax_Helper_Data $taxHelper */
        $taxHelper = $this->_getHelperData('tax');
        $taxCalcMethod = $taxHelper->getConfig()->getAlgorithm($product->getStore());
        $isPriceFixedType = ($product->getPriceType() == self::PRICE_TYPE_FIXED);

        $selections = $option->getSelections();
        if (!$selections) {
            return $selectionPrices;
        }

        foreach ($selections as $selection) {
            /** @var Mage_Bundle_Model_Selection $selection */
            if (!$selection->isSalable()) {
                /**
                 * @todo CatalogInventory Show out of stock Products
                 */
                continue;
            }

            $item = $isPriceFixedType ? $product : $selection;

            $selectionUnitPrice = $this->getSelectionFinalTotalPrice(
                $product,
                $selection,
                1,
                null,
                false,
                $takeTierPrice,
            );
            $selectionQty = $selection->getSelectionQty();
            if ($isPriceFixedType || $taxCalcMethod == Mage_Tax_Model_Calculation::CALC_TOTAL_BASE) {
                $selectionPrice = $selectionQty * $taxHelper->getPrice(
                    $item,
                    $selectionUnitPrice,
                    $includeTax,
                    null,
                    null,
                    null,
                    null,
                    null,
                    false,
                );
                $selectionPrices[] = $selectionPrice;
            } elseif ($taxCalcMethod == Mage_Tax_Model_Calculation::CALC_ROW_BASE) {
                $selectionPrice = $taxHelper->getPrice($item, $selectionUnitPrice * $selectionQty, $includeTax);
                $selectionPrices[] = $selectionPrice;
            } else { //dynamic price and Mage_Tax_Model_Calculation::CALC_UNIT_BASE
                $selectionPrice = $taxHelper->getPrice($item, $selectionUnitPrice, $includeTax) * $selectionQty;
                $selectionPrices[] = $selectionPrice;
            }
        }

        return $selectionPrices;
    }

    /**
     * Calculate Minimal price of bundle (counting all required options)
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return float
     */
    public function getMinimalPrice($product)
    {
        return $this->getPricesTierPrice($product, 'min');
    }

    /**
     * Calculate maximal price of bundle
     *
     * @param Mage_Catalog_Model_Product $product
     * @return float
     */
    public function getMaximalPrice($product)
    {
        return $this->getPricesTierPrice($product, 'max');
    }

    /**
     * Get Options with attached Selections collection
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Bundle_Model_Resource_Option_Collection
     */
    public function getOptions($product)
    {
        /** @var Mage_Bundle_Model_Product_Type $productType */
        $productType = $product->getTypeInstance(true);
        $productType->setStoreFilter($product->getStoreId(), $product);

        $optionCollection = $productType->getOptionsCollection($product);

        $selectionCollection = $productType->getSelectionsCollection(
            $productType->getOptionsIds($product),
            $product,
        );

        return $optionCollection->appendSelections($selectionCollection, false, false);
    }

    /**
     * Calculate price of selection
     *
     * @param Mage_Catalog_Model_Product $bundleProduct
     * @param Mage_Catalog_Model_Product $selectionProduct
     * @param float|null $selectionQty
     * @param null|bool $multiplyQty Whether to multiply selection's price by its quantity
     * @return float
     * @throws Mage_Core_Model_Store_Exception
     * @deprecated after 1.6.2.0
     * @see Mage_Bundle_Model_Product_Price::getSelectionFinalTotalPrice()
     *
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
     * @param float $qty
     * @return float
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getSelectionPreFinalPrice($bundleProduct, $selectionProduct, $qty = null)
    {
        return $this->getSelectionPrice($bundleProduct, $selectionProduct, $qty);
    }

    /**
     * Calculate final price of selection
     *
     * @param Mage_Catalog_Model_Product $bundleProduct
     * @param Mage_Catalog_Model_Product $selectionProduct
     * @param float $bundleQty
     * @param float $selectionQty
     * @param bool $multiplyQty
     * @return float
     * @throws Mage_Core_Model_Store_Exception
     * @see Mage_Bundle_Model_Product_Price::getSelectionFinalTotalPrice()
     *
     * @deprecated after 1.5.1.0
     */
    public function getSelectionFinalPrice(
        $bundleProduct,
        $selectionProduct,
        $bundleQty,
        $selectionQty = null,
        $multiplyQty = true
    ) {
        return $this->getSelectionFinalTotalPrice(
            $bundleProduct,
            $selectionProduct,
            $bundleQty,
            $selectionQty,
            $multiplyQty,
        );
    }

    /**
     * Calculate final price of selection
     * with take into account tier price
     *
     * @param Mage_Catalog_Model_Product $bundleProduct
     * @param Mage_Catalog_Model_Product $selectionProduct
     * @param float $bundleQty
     * @param float|null $selectionQty
     * @param bool $multiplyQty
     * @param bool $takeTierPrice
     * @return float
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getSelectionFinalTotalPrice(
        $bundleProduct,
        $selectionProduct,
        $bundleQty,
        $selectionQty,
        $multiplyQty = true,
        $takeTierPrice = true
    ) {
        if (is_null($selectionQty)) {
            $selectionQty = $selectionProduct->getSelectionQty();
        }

        if ($bundleProduct->getPriceType() == self::PRICE_TYPE_DYNAMIC) {
            $price = $selectionProduct->getFinalPrice($takeTierPrice ? $selectionQty : 1);
        } elseif ($selectionProduct->getSelectionPriceType()) {
            // percent
            $product = clone $bundleProduct;
            $product->setFinalPrice($this->getPrice($product));
            Mage::dispatchEvent(
                'catalog_product_get_final_price',
                ['product' => $product, 'qty' => $bundleQty],
            );
            $price = $product->getData('final_price') * ($selectionProduct->getSelectionPriceValue() / 100);
        } else { // fixed
            $price = $selectionProduct->getSelectionPriceValue();
        }

        $price = $this->getLowestPrice($bundleProduct, $price, $bundleQty);

        if ($multiplyQty) {
            $price *= $selectionQty;
        }

        return $price;
    }

    /**
     * Returns the lowest price after applying any applicable bundle discounts
     *
     * @param Mage_Catalog_Model_Product $bundleProduct
     * @param float|string $price
     * @param int $bundleQty
     * @return float
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getLowestPrice($bundleProduct, $price, $bundleQty = 1)
    {
        $price *= 1;
        return min(
            $this->_getApp()->getStore()->roundPrice($price),
            $this->_applyGroupPrice($bundleProduct, $price),
            $this->_applyTierPrice($bundleProduct, $bundleQty, $price),
            $this->_applySpecialPrice($bundleProduct, $price),
        );
    }

    /**
     * Apply group price for bundle product
     *
     * @param Mage_Catalog_Model_Product $product
     * @param float $finalPrice
     * @return float
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _applyGroupPrice($product, $finalPrice)
    {
        $result = $finalPrice;
        $groupPrice = $product->getGroupPrice();

        if (is_numeric($groupPrice)) {
            $groupPrice = $finalPrice - ($finalPrice * ($groupPrice / 100));
            $groupPrice = $this->_getApp()->getStore()->roundPrice($groupPrice);
            $result = min($finalPrice, $groupPrice);
        }

        return $result;
    }

    /**
     * Get product group price
     *
     * @param Mage_Catalog_Model_Product $product
     * @return float|null
     * @throws Mage_Core_Exception
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
     * @param Mage_Catalog_Model_Product $product
     * @param float $qty
     * @param float $finalPrice
     * @return  float
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _applyTierPrice($product, $qty, $finalPrice)
    {
        if (is_null($qty)) {
            return $finalPrice;
        }

        $tierPrice = $product->getTierPrice($qty);

        if (is_numeric($tierPrice)) {
            $tierPrice = $finalPrice - ($finalPrice * ($tierPrice / 100));
            $tierPrice = $this->_getApp()->getStore()->roundPrice($tierPrice);
            $finalPrice = min($finalPrice, $tierPrice);
        }

        return $finalPrice;
    }

    /**
     * Get product tier price by qty
     *
     * @param float|null $qty
     * @param Mage_Catalog_Model_Product $product
     * @return float|array
     * @throws Mage_Core_Exception
     */
    public function getTierPrice($qty, $product)
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
                return 0;
            }

            return [[
                'price' => 0,
                'website_price' => 0,
                'price_qty' => 1,
                'cust_group' => $allGroups,
            ]];
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
                    $prevPrice = $price['website_price'];
                    $prevQty = $price['price_qty'];
                    $prevGroup = $price['cust_group'];
                }
            }

            return $prevPrice;
        }

        $qtyCache = [];
        foreach ($prices as $i => $price) {
            if ($price['cust_group'] != $custGroup && $price['cust_group'] != $allGroups) {
                unset($prices[$i]);
            } elseif (isset($qtyCache[$price['price_qty']])) {
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

        return $prices ?: [];
    }

    /**
     * Calculate product price based on special price data and price rules
     *
     * @param float $basePrice
     * @param float $specialPrice
     * @param string $specialPriceFrom
     * @param string $specialPriceTo
     * @param float|null|false $rulePrice
     * @param mixed $wId
     * @param mixed $gId
     * @param null|int $productId
     * @return float
     * @throws Mage_Core_Model_Store_Exception
     */
    public static function calculatePrice(
        $basePrice,
        $specialPrice,
        $specialPriceFrom,
        $specialPriceTo,
        $rulePrice = false,
        $wId = null,
        $gId = null,
        $productId = null
    ) {
        $resource = Mage::getResourceSingleton('bundle/bundle');
        $selectionResource = Mage::getResourceSingleton('bundle/selection');
        $productPriceTypeId = Mage::getSingleton('eav/entity_attribute')->getIdByCode(
            Mage_Catalog_Model_Product::ENTITY,
            'price_type',
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
        } elseif ($gId instanceof Mage_Customer_Model_Group) {
            $gId = $gId->getId();
        }

        if (!isset(self::$attributeCache[$productId]['price_type'])) {
            $attributes = $resource->getAttributeData($productId, $productPriceTypeId, $store);
            self::$attributeCache[$productId]['price_type'] = $attributes;
        } else {
            $attributes = self::$attributeCache[$productId]['price_type'];
        }

        $options = [0];
        $results = $resource->getSelectionsData($productId);

        if (!$attributes || !$attributes[0]['value']) { //dynamic
            foreach ($results as $result) {
                if (!$result['product_id']) {
                    continue;
                }

                if ($result['selection_can_change_qty'] && $result['type'] !== 'multi'
                    && $result['type'] !== 'checkbox'
                ) {
                    $qty = 1;
                } else {
                    $qty = $result['selection_qty'];
                }

                $result['final_price'] = $selectionResource->getPriceFromIndex(
                    $result['product_id'],
                    $qty,
                    $store,
                    $gId,
                );

                $selectionPrice = $result['final_price'] * $qty;

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
                    $selectionPrice = $basePrice * $result['selection_price_value'] / 100;
                } else {
                    $selectionPrice = $result['selection_price_value'];
                }

                if ($result['selection_can_change_qty'] && $result['type'] !== 'multi'
                    && $result['type'] !== 'checkbox'
                ) {
                    $qty = 1;
                } else {
                    $qty = $result['selection_qty'];
                }

                $selectionPrice *= $qty;

                if (isset($options[$result['option_id']])) {
                    $options[$result['option_id']] = min($options[$result['option_id']], $selectionPrice);
                } else {
                    $options[$result['option_id']] = $selectionPrice;
                }
            }

            $basePrice += array_sum($options);
        }

        $finalPrice = self::calculateSpecialPrice(
            $basePrice,
            $specialPrice,
            $specialPriceFrom,
            $specialPriceTo,
            $store,
        );

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
                $prices = [];
                foreach ($values as $value) {
                    $prices[] = $value->getPrice();
                }

                if ($prices !== []) {
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

        return max($finalPrice, 0);
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
     * @throws Mage_Core_Model_Store_Exception
     */
    public static function calculateSpecialPrice(
        $finalPrice,
        $specialPrice,
        $specialPriceFrom,
        $specialPriceTo,
        $store = null
    ) {
        if (!is_null($specialPrice) && $specialPrice != false) {
            if (Mage::app()->getLocale()->isStoreDateInInterval($store, $specialPriceFrom, $specialPriceTo)) {
                $specialPrice = Mage::app()->getStore()->roundPrice($finalPrice * $specialPrice / 100);
                $finalPrice = min($finalPrice, $specialPrice);
            }
        }

        return $finalPrice;
    }

    /**
     * Check is group price value fixed or percent of original price
     *
     * @return false
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
     * Get Magento App instance
     *
     * @return Mage_Core_Model_App
     */
    protected function _getApp()
    {
        return Mage::app();
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
        $minimalOptionPrice = ($prices) ? min($prices) : (float) $option->getPrice(true);
        return ($option->getIsRequire()) ? $minimalOptionPrice : 0;
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
        if ($prices) {
            $maximalPrice = ($option->isMultipleType()) ? array_sum($prices) : max($prices);
        } else {
            $maximalPrice = (float) ($option->getPrice(true));
        }

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
        $prices = [];
        if ($values) {
            foreach ($values as $value) {
                $prices[] = $value->getPrice(true);
            }
        }

        return $prices;
    }
}
