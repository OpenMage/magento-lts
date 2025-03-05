<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract API2 class for product instance
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
abstract class Mage_Catalog_Model_Api2_Product_Rest extends Mage_Catalog_Model_Api2_Product
{
    /**
     * Current loaded product
     *
     * @var Mage_Catalog_Model_Product|null
     */
    protected $_product;

    protected ?array $allowedAttributes = null;

    /**
     * Retrieve product data
     *
     * @return array
     */
    protected function _retrieve()
    {
        $product = $this->_getProduct();

        $this->_prepareProductForResponse($product);
        return $product->getData();
    }

    /**
     * Retrieve list of products
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = Mage::getResourceModel('catalog/product_collection');
        $store = $this->_getStore();
        $entityOnlyAttributes = $this->getEntityOnlyAttributes(
            $this->getUserType(),
            Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ,
        );
        $availableAttributes = array_keys($this->getAvailableAttributes(
            $this->getUserType(),
            Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ,
        ));
        // available attributes not contain image attribute, but it needed for get image_url
        $availableAttributes[] = 'image';
        $collection->addStoreFilter($store->getId())
            ->addPriceData($this->_getCustomerGroupId(), $store->getWebsiteId())
            ->addAttributeToSelect(array_diff($availableAttributes, $entityOnlyAttributes))
            ->addAttributeToFilter('visibility', [
                'neq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE])
            ->addAttributeToFilter('status', ['eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED]);
        $this->_applyCategoryFilter($collection);
        $this->_applyCollectionModifiers($collection);
        $products = $collection->load();

        /** @var Mage_Catalog_Model_Product $product */
        foreach ($products as $product) {
            $this->_setProduct($product);
            $this->_prepareProductForResponse($product);
        }
        return $products->toArray();
    }

    /**
     * Apply filter by category id
     */
    protected function _applyCategoryFilter(Mage_Catalog_Model_Resource_Product_Collection $collection)
    {
        $categoryId = $this->getRequest()->getParam('category_id');
        if ($categoryId) {
            $category = $this->_getCategoryById($categoryId);
            if (!$category->getId()) {
                $this->_critical('Category not found.', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
            $collection->addCategoryFilter($category);
        }
    }

    /**
     * Add special fields to product get response
     */
    protected function _prepareProductForResponse(Mage_Catalog_Model_Product $product)
    {
        $productData = $product->getData();
        $product->setWebsiteId($this->_getStore()->getWebsiteId());
        // customer group is required in product for correct prices calculation
        $product->setCustomerGroupId($this->_getCustomerGroupId());

        $this->addAttribute('image_url', $productData, $product);
        $this->addAttribute('is_saleable', $productData, $product);
        $this->addPrices($productData, $product);

        if ($this->getActionType() == self::ACTION_TYPE_ENTITY) {
            $this->addAttribute('buy_now_url', $productData, $product);
            $this->addAttribute('has_custom_options', $productData, $product);
            $this->addAttribute('is_in_stock', $productData, $product);
            $this->addAttribute('tier_price', $productData, $product);
            $this->addAttribute('total_reviews_count', $productData, $product);
            $this->addAttribute('url', $productData, $product);
        } else {
            // remove tier price from response
            $product->unsetData('tier_price');
            unset($productData['tier_price']);
        }
        $product->addData($productData);
    }

    /**
     * Add custom attributes to product data
     *
     * Apply custom logig before add data to product.
     */
    protected function addAttribute(string $attribute, array &$productData, Mage_Catalog_Model_Product $product): void
    {
        if (!$this->isAllowedAttribute($attribute)) {
            return;
        }

        switch ($attribute) {
            case 'buy_now_url':
                /** @var Mage_Checkout_Helper_Cart $cartHelper */
                $cartHelper = Mage::helper('checkout/cart');
                $productData[$attribute] = $cartHelper->getAddUrl($product);
                break;
            case 'has_custom_options':
                $productData[$attribute] = count($product->getOptions()) > 0;
                break;
            case 'image_url':
                $productData[$attribute] = (string) Mage::helper('catalog/image')->init($product, 'image');
                break;
            case 'is_in_stock':
                $stockItem = $product->getStockItem();
                if (!$stockItem) {
                    $stockItem = Mage::getModel('cataloginventory/stock_item');
                    $stockItem->loadByProduct($product);
                }
                $productData[$attribute] = $stockItem->getIsInStock();
                break;
            case 'is_saleable':
                $productData[$attribute] = $product->getIsSalable();
                break;
            case 'tier_price':
                $productData[$attribute] = $this->_getTierPrices();
                break;
            case 'total_reviews_count':
                /** @var Mage_Review_Model_Review $reviewModel */
                $reviewModel = Mage::getModel('review/review');
                $productData[$attribute] = $reviewModel->getTotalReviews(
                    $product->getId(),
                    true,
                    $this->_getStore()->getId(),
                );
                break;
            case 'url':
                $productData[$attribute] = $product->getProductUrl();
                break;
        }
    }

    /**
     * Add price attributes to product data
     */
    protected function addPrices(array &$productData, Mage_Catalog_Model_Product $product): void
    {
        $isPriceRequired = false;
        if ($this->isAllowedAttribute('regular_price_with_tax') ||
            $this->isAllowedAttribute('regular_price_without_tax') ||
            $this->isAllowedAttribute('final_price_with_tax') ||
            $this->isAllowedAttribute('final_price_without_tax')
        ) {
            $isPriceRequired = true;
        }

        // calculate prices
        if ($isPriceRequired) {
            $finalPrice = $product->getFinalPrice();
            if ($this->isAllowedAttribute('regular_price_with_tax')) {
                $productData['regular_price_with_tax'] = $this->_applyTaxToPrice($product->getPrice(), true);
            }
            if ($this->isAllowedAttribute('regular_price_without_tax')) {
                $productData['regular_price_without_tax'] = $this->_applyTaxToPrice($product->getPrice(), false);
            }
            if ($this->isAllowedAttribute('final_price_with_tax')) {
                $productData['final_price_with_tax'] = $this->_applyTaxToPrice($finalPrice, true);
            }
            if ($this->isAllowedAttribute('final_price_without_tax')) {
                $productData['final_price_without_tax'] = $this->_applyTaxToPrice($finalPrice, false);
            }
        }
    }

    /**
     * Get allowed attributes for output
     */
    /**
     * @return string[]
     */
    protected function getAllowedAttributes(): array
    {
        if (is_null($this->allowedAttributes)) {
            $attributes = Mage::helper('api2')->getAllowedAttributes(
                $this->getUserType(),
                'product',
                Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ,
            );
            $this->allowedAttributes = $attributes;
        }
        return $this->allowedAttributes;
    }

    protected function isAllowedAttribute(string $attribute): bool
    {
        return in_array($attribute, $this->getAllowedAttributes());
    }

    /**
     * Product create only available for admin
     */
    protected function _create(array $data)
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Product update only available for admin
     */
    protected function _update(array $data)
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Product delete only available for admin
     */
    protected function _delete()
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Load product by its SKU or ID provided in request
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        if (is_null($this->_product)) {
            $productId = $this->getRequest()->getParam('id');
            /** @var Mage_Catalog_Helper_Product $productHelper */
            $productHelper = Mage::helper('catalog/product');
            $product = $productHelper->getProduct($productId, $this->_getStore()->getId());
            if (!($product->getId())) {
                $this->_critical(self::RESOURCE_NOT_FOUND);
            }
            // check if product belongs to website current
            if ($this->_getStore()->getId()) {
                $isValidWebsite = in_array($this->_getStore()->getWebsiteId(), $product->getWebsiteIds());
                if (!$isValidWebsite) {
                    $this->_critical(self::RESOURCE_NOT_FOUND);
                }
            }
            // Check display settings for customers & guests
            if ($this->getApiUser()->getType() != Mage_Api2_Model_Auth_User_Admin::USER_TYPE) {
                // check if product assigned to any website and can be shown
                if ((!Mage::app()->isSingleStoreMode() && !count($product->getWebsiteIds()))
                    || !$productHelper->canShow($product)
                ) {
                    $this->_critical(self::RESOURCE_NOT_FOUND);
                }
            }
            $this->_product = $product;
        }
        return $this->_product;
    }

    /**
     * Set product
     */
    protected function _setProduct(Mage_Catalog_Model_Product $product)
    {
        $this->_product = $product;
    }

    /**
     * Load category by id
     *
     * @param int $categoryId
     * @return Mage_Catalog_Model_Category
     */
    protected function _getCategoryById($categoryId)
    {
        return Mage::getModel('catalog/category')->load($categoryId);
    }

    /**
     * Get product price with all tax settings processing
     *
     * @param float $price inputted product price
     * @param bool $includingTax return price include tax flag
     * @param null|Mage_Customer_Model_Address $shippingAddress
     * @param null|Mage_Customer_Model_Address $billingAddress
     * @param null|int $ctc customer tax class
     * @param bool $priceIncludesTax flag that price parameter contain tax
     * @return float
     * @see Mage_Tax_Helper_Data::getPrice()
     */
    protected function _getPrice(
        $price,
        $includingTax = null,
        $shippingAddress = null,
        $billingAddress = null,
        $ctc = null,
        $priceIncludesTax = null
    ) {
        $product = $this->_getProduct();
        $store = $this->_getStore();

        if (is_null($priceIncludesTax)) {
            /** @var Mage_Tax_Model_Config $config */
            $config = Mage::getSingleton('tax/config');
            $priceIncludesTax = $config->priceIncludesTax($store) || $config->getNeedUseShippingExcludeTax();
        }

        $percent = $product->getTaxPercent();
        $includingPercent = null;

        $taxClassId = $product->getTaxClassId();
        if (is_null($percent)) {
            if ($taxClassId) {
                $request = Mage::getSingleton('tax/calculation')
                    ->getRateRequest($shippingAddress, $billingAddress, $ctc, $store);
                $percent = Mage::getSingleton('tax/calculation')->getRate($request->setProductClassId($taxClassId));
            }
        }
        if ($taxClassId && $priceIncludesTax) {
            $taxHelper = Mage::helper('tax');
            if ($taxHelper->isCrossBorderTradeEnabled($store)) {
                $includingPercent = $percent;
            } else {
                $request = Mage::getSingleton('tax/calculation')->getDefaultRateRequest($store);
                $includingPercent = Mage::getSingleton('tax/calculation')
                    ->getRate($request->setProductClassId($taxClassId));
            }
        }

        if ($percent === false || is_null($percent)) {
            if ($priceIncludesTax && !$includingPercent) {
                return $price;
            }
        }
        $product->setTaxPercent($percent);

        if (!is_null($includingTax)) {
            if ($priceIncludesTax) {
                if ($includingTax) {
                    /**
                     * Recalculate price include tax in case of different rates
                     */
                    if ($includingPercent != $percent) {
                        $price = $this->_calculatePrice($price, $includingPercent, false);
                        /**
                         * Using regular rounding. Ex:
                         * price incl tax   = 52.76
                         * store tax rate   = 19.6%
                         * customer tax rate= 19%
                         *
                         * price excl tax = 52.76 / 1.196 = 44.11371237 ~ 44.11
                         * tax = 44.11371237 * 0.19 = 8.381605351 ~ 8.38
                         * price incl tax = 52.49531773 ~ 52.50 != 52.49
                         *
                         * that why we need round prices excluding tax before applying tax
                         * this calculation is used for showing prices on catalog pages
                         */
                        if ($percent != 0) {
                            $price = Mage::getSingleton('tax/calculation')->round($price);
                            $price = $this->_calculatePrice($price, $percent, true);
                        }
                    }
                } else {
                    $price = $this->_calculatePrice($price, $includingPercent, false);
                }
            } else {
                if ($includingTax) {
                    $price = $this->_calculatePrice($price, $percent, true);
                }
            }
        } else {
            if ($priceIncludesTax) {
                if ($includingTax) {
                    $price = $this->_calculatePrice($price, $includingPercent, false);
                    $price = $this->_calculatePrice($price, $percent, true);
                } else {
                    $price = $this->_calculatePrice($price, $includingPercent, false);
                }
            } else {
                if ($includingTax) {
                    $price = $this->_calculatePrice($price, $percent, true);
                }
            }
        }

        return $store->roundPrice($price);
    }

    /**
     * Calculate price imcluding/excluding tax base on tax rate percent
     *
     * @param float $price
     * @param float $percent
     * @param bool $includeTax true - for calculate price including tax and false if price excluding tax
     * @return float
     */
    protected function _calculatePrice($price, $percent, $includeTax)
    {
        /** @var Mage_Tax_Model_Calculation $calculator */
        $calculator = Mage::getSingleton('tax/calculation');
        $taxAmount = $calculator->calcTaxAmount($price, $percent, !$includeTax, false);

        return $includeTax ? $price + $taxAmount : $price - $taxAmount;
    }

    /**
     * Retrieve tier prices in special format
     *
     * @return array
     */
    protected function _getTierPrices()
    {
        $tierPrices = [];
        foreach ($this->_getProduct()->getTierPrice() as $tierPrice) {
            $tierPrices[] = [
                'qty' => $tierPrice['price_qty'],
                'price_with_tax' => $this->_applyTaxToPrice($tierPrice['price']),
                'price_without_tax' => $this->_applyTaxToPrice($tierPrice['price'], false),
            ];
        }
        return $tierPrices;
    }

    /**
     * Default implementation. May be different for customer/guest/admin role.
     *
     * @return int|null
     */
    protected function _getCustomerGroupId()
    {
        return null;
    }

    /**
     * Default implementation. May be different for customer/guest/admin role.
     *
     * @param float $price
     * @param bool $withTax
     * @return float
     */
    protected function _applyTaxToPrice($price, $withTax = true)
    {
        return $price;
    }
}
