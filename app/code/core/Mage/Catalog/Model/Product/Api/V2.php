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
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product api V2
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Api_V2 extends Mage_Catalog_Model_Product_Api
{
    /**
     * Retrieve product info
     *
     * @param int|string $productId
     * @param string|int $store
     * @param stdClass   $attributes
     * @param string     $identifierType
     * @return array
     */
    public function info($productId, $store = null, $attributes = null, $identifierType = null)
    {
        // make sku flag case-insensitive
        if (!empty($identifierType)) {
            $identifierType = strtolower($identifierType);
        }

        $product = $this->_getProduct($productId, $store, $identifierType);

        $result = [ // Basic product data
            'product_id' => $product->getId(),
            'sku'        => $product->getSku(),
            'set'        => $product->getAttributeSetId(),
            'type'       => $product->getTypeId(),
            'categories' => $product->getCategoryIds(),
            'websites'   => $product->getWebsiteIds()
        ];

        $allAttributes = [];
        if (!empty($attributes->attributes)) {
            $allAttributes = array_merge($allAttributes, $attributes->attributes);
        } else {
            foreach ($product->getTypeInstance(true)->getEditableAttributes($product) as $attribute) {
                if ($this->_isAllowedAttribute($attribute, $attributes)) {
                    $allAttributes[] = $attribute->getAttributeCode();
                }
            }
        }

        $_additionalAttributeCodes = [];
        if (!empty($attributes->additional_attributes)) {
            foreach ($attributes->additional_attributes as $attributeCode) {
                $allAttributes[] = $attributeCode;
                $_additionalAttributeCodes[] = $attributeCode;
            }
        }

        $_additionalAttribute = 0;
        foreach ($product->getTypeInstance(true)->getEditableAttributes($product) as $attribute) {
            if ($this->_isAllowedAttribute($attribute, $allAttributes)) {
                if (in_array($attribute->getAttributeCode(), $_additionalAttributeCodes)) {
                    $result['additional_attributes'][$_additionalAttribute]['key'] = $attribute->getAttributeCode();
                    $result['additional_attributes'][$_additionalAttribute]['value'] = $product
                        ->getData($attribute->getAttributeCode());
                    $_additionalAttribute++;
                } else {
                    $result[$attribute->getAttributeCode()] = $product->getData($attribute->getAttributeCode());
                }
            }
        }

        return $result;
    }

    /**
     * Create new product.
     *
     * @param string $type
     * @param int $set
     * @param string $sku
     * @param array|stdClass $productData
     * @param string $store
     * @return int
     */
    public function create($type, $set, $sku, $productData, $store = null)
    {
        if (!$type || !$set || !$sku || !is_object($productData)) {
            $this->_fault('data_invalid');
        }

        $this->_checkProductTypeExists($type);
        $this->_checkProductAttributeSet($set);

        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product');
        $product->setStoreId($this->_getStoreId($store))
            ->setAttributeSetId($set)
            ->setTypeId($type)
            ->setSku($sku);

        if (!property_exists($productData, 'stock_data')) {
            //Set default stock_data if not exist in product data
            $stockData = ['use_config_manage_stock' => 0];
            $product->setStockData($stockData);
        }

        foreach ($product->getMediaAttributes() as $mediaAttribute) {
            $mediaAttrCode = $mediaAttribute->getAttributeCode();
            $product->setData($mediaAttrCode, 'no_selection');
        }

        $this->_prepareDataForSave($product, $productData);

        try {
            /**
             * @todo implement full validation process with errors returning which are ignoring now
             * @todo see Mage_Catalog_Model_Product::validate()
             */
            if (is_array($errors = $product->validate())) {
                $strErrors = [];
                foreach ($errors as $code => $error) {
                    if ($error === true) {
                        $error = Mage::helper('catalog')->__('Attribute "%s" is invalid.', $code);
                    }
                    $strErrors[] = $error;
                }
                $this->_fault('data_invalid', implode("\n", $strErrors));
            }

            $product->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return $product->getId();
    }

    /**
     * Update product data
     *
     * @param int|string $productId
     * @param array|stdClass $productData
     * @param string|int $store
     * @param string|null $identifierType
     * @return bool
     * @throws Mage_Api_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function update($productId, $productData, $store = null, $identifierType = null)
    {
        $product = $this->_getProduct($productId, $store, $identifierType);

        $this->_prepareDataForSave($product, $productData);

        try {
            /**
             * @todo implement full validation process with errors returning which are ignoring now
             * @todo see Mage_Catalog_Model_Product::validate()
             */
            if (is_array($errors = $product->validate())) {
                $strErrors = [];
                foreach ($errors as $code => $error) {
                    if ($error === true) {
                        $error = Mage::helper('catalog')->__('Value for "%s" is invalid.', $code);
                    } else {
                        $error = Mage::helper('catalog')->__('Value for "%s" is invalid: %s', $code, $error);
                    }
                    $strErrors[] = $error;
                }
                $this->_fault('data_invalid', implode("\n", $strErrors));
            }

            $product->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return true;
    }

    /**
     * Update multiple products information at once
     *
     * @param array      $productIds
     * @param array      $productData
     * @param string|int $store
     * @param string     $identifierType
     * @return true|void
     */
    public function multiUpdate($productIds, $productData, $store = null, $identifierType = null)
    {
        if (count($productIds) != count($productData)) {
            $this->_fault('multi_update_not_match');
        }

        $productData = (array)$productData;
        $failMessages = [];

        foreach ($productIds as $index => $productId) {
            try {
                $this->update($productId, $productData[$index], $store, $identifierType);
            } catch (Mage_Api_Exception $e) {
                $failMessages[] = sprintf("Product ID %d:\n %s", $productId, $e->getMessage());
            }
        }

        if (empty($failMessages)) {
            return true;
        }

        $this->_fault('partially_updated', implode("\n", $failMessages));
    }

    /**
     *  Set additional data before product saved
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array|stdClass $productData
     * @throws Mage_Api_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _prepareDataForSave($product, $productData)
    {
        if (!is_object($productData)) {
            $this->_fault('data_invalid');
        }
        if (property_exists($productData, 'website_ids') && is_array($productData->website_ids)) {
            $product->setWebsiteIds($productData->website_ids);
        }

        if (property_exists($productData, 'additional_attributes')) {
            if (property_exists($productData->additional_attributes, 'single_data')) {
                foreach ($productData->additional_attributes->single_data as $_attribute) {
                    $attributeCode = $_attribute->key;
                    $productData->$attributeCode = $_attribute->value;
                }
            }
            if (property_exists($productData->additional_attributes, 'multi_data')) {
                foreach ($productData->additional_attributes->multi_data as $_attribute) {
                    $attributeCode = $_attribute->key;
                    $productData->$attributeCode = $_attribute->value;
                }
            }
            unset($productData->additional_attributes);
        }

        // phpcs:ignore: Ecg.Performance.Loop.DataLoad
        foreach ($product->getTypeInstance(true)->getEditableAttributes($product) as $attribute) {
            $attributeCode = $attribute->getAttributeCode();

            //Unset data if object attribute has no value in current store
            if (Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID !== (int) $product->getStoreId()
                && !$product->getExistsStoreValueFlag($attributeCode)
                && !$attribute->isScopeGlobal()
            ) {
                $product->setData($attributeCode, false);
            }

            if ($this->_isAllowedAttribute($attribute) && (isset($productData->$attributeCode))) {
                $product->setData(
                    $attributeCode,
                    $productData->$attributeCode
                );
            }
        }

        if (property_exists($productData, 'categories') && is_array($productData->categories)) {
            $product->setCategoryIds($productData->categories);
        }

        if (property_exists($productData, 'websites') && is_array($productData->websites)) {
            foreach ($productData->websites as &$website) {
                if (is_string($website)) {
                    try {
                        $website = Mage::app()->getWebsite($website)->getId();
                    } catch (Exception $e) {
                    }
                }
            }
            $product->setWebsiteIds($productData->websites);
        }

        if (Mage::app()->isSingleStoreMode()) {
            $product->setWebsiteIds([Mage::app()->getStore(true)->getWebsite()->getId()]);
        }

        if (property_exists($productData, 'stock_data')) {
            $_stockData = [];
            foreach ($productData->stock_data as $key => $value) {
                $_stockData[$key] = $value;
            }
            $product->setStockData($_stockData);
        }

        if (property_exists($productData, 'tier_price')) {
            $tierPrices = Mage::getModel('catalog/product_attribute_tierprice_api_V2')
                 ->prepareTierPrices($product, $productData->tier_price);
            $product->setData(Mage_Catalog_Model_Product_Attribute_Tierprice_Api_V2::ATTRIBUTE_CODE, $tierPrices);
        }
    }

    /**
     * Update product special priceim
     *
     * @param int|string $productId
     * @param float $specialPrice
     * @param string $fromDate
     * @param string $toDate
     * @param string|int $store
     * @param string $identifierType OPTIONAL If 'sku' - search product by SKU, if any except for NULL - search by ID,
     *                                        otherwise - try to determine identifier type automatically
     * @return bool
     */
    public function setSpecialPrice(
        $productId,
        $specialPrice = null,
        $fromDate = null,
        $toDate = null,
        $store = null,
        $identifierType = null
    ) {
        $obj = new stdClass();
        $obj->special_price = $specialPrice;
        $obj->special_from_date = $fromDate;
        $obj->special_to_date = $toDate;
        return $this->update($productId, $obj, $store, $identifierType);
    }
}
