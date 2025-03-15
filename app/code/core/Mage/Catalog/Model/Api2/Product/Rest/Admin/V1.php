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
 * API2 for catalog_product (Admin)
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Api2_Product_Rest_Admin_V1 extends Mage_Catalog_Model_Api2_Product_Rest
{
    /**
     * The greatest decimal value which could be stored. Corresponds to DECIMAL (12,4) SQL type
     */
    public const MAX_DECIMAL_VALUE = 99999999.9999;

    /**
     * Add special fields to product get response
     */
    protected function _prepareProductForResponse(Mage_Catalog_Model_Product $product)
    {
        $pricesFilterKeys = ['price_id', 'all_groups', 'website_price'];
        $groupPrice = $product->getData('group_price');
        $product->setData('group_price', $this->_filterOutArrayKeys($groupPrice, $pricesFilterKeys, true));
        $tierPrice = $product->getData('tier_price');
        $product->setData('tier_price', $this->_filterOutArrayKeys($tierPrice, $pricesFilterKeys, true));

        $stockData = $product->getStockItem()->getData();
        $stockDataFilterKeys = ['item_id', 'product_id', 'stock_id', 'low_stock_date', 'type_id',
            'stock_status_changed_auto', 'stock_status_changed_automatically', 'product_name', 'store_id',
            'product_type_id', 'product_status_changed', 'product_changed_websites',
            'use_config_enable_qty_increments'];
        $product->setData('stock_data', $this->_filterOutArrayKeys($stockData, $stockDataFilterKeys));
        $product->setData('product_type_name', $product->getTypeId());
    }

    /**
     * Remove specified keys from associative or indexed array
     *
     * @param bool $dropOrigKeys if true - return array as indexed array
     * @return array
     */
    protected function _filterOutArrayKeys(array $array, array $keys, $dropOrigKeys = false)
    {
        $isIndexedArray = is_array(reset($array));
        if ($isIndexedArray) {
            foreach ($array as &$value) {
                if (is_array($value)) {
                    $value = array_diff_key($value, array_flip($keys));
                }
            }
            if ($dropOrigKeys) {
                $array = array_values($array);
            }
            unset($value);
        } else {
            $array = array_diff_key($array, array_flip($keys));
        }

        return $array;
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
        $collection->setStoreId($store->getId());
        $collection->addAttributeToSelect(array_keys(
            $this->getAvailableAttributes($this->getUserType(), Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ),
        ));
        $this->_applyCategoryFilter($collection);
        $this->_applyCollectionModifiers($collection);
        return $collection->load()->toArray();
    }

    /**
     * Delete product by its ID
     *
     * @throws Mage_Api2_Exception
     */
    protected function _delete()
    {
        $product = $this->_getProduct();
        try {
            $product->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }
    }

    /**
     * Create product
     *
     * @return string
     */
    protected function _create(array $data)
    {
        /** @var Mage_Catalog_Model_Api2_Product_Validator_Product $validator */
        $validator = Mage::getModel('catalog/api2_product_validator_product', [
            'operation' => self::OPERATION_CREATE,
        ]);

        if (!$validator->isValidData($data)) {
            foreach ($validator->getErrors() as $error) {
                $this->_error($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
            $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }

        $type = $data['type_id'];
        if ($type !== 'simple') {
            $this->_critical(
                "Creation of products with type '$type' is not implemented",
                Mage_Api2_Model_Server::HTTP_METHOD_NOT_ALLOWED,
            );
        }
        $set = $data['attribute_set_id'];
        $sku = $data['sku'];

        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product')
            ->setStoreId(Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID)
            ->setAttributeSetId($set)
            ->setTypeId($type)
            ->setSku($sku);

        foreach ($product->getMediaAttributes() as $mediaAttribute) {
            $mediaAttrCode = $mediaAttribute->getAttributeCode();
            $product->setData($mediaAttrCode, 'no_selection');
        }

        $this->_prepareDataForSave($product, $data);
        try {
            $product->validate();
            $product->save();
            $this->_multicall($product->getId());
        } catch (Mage_Eav_Model_Entity_Attribute_Exception $e) {
            $this->_critical(
                sprintf('Invalid attribute "%s": %s', $e->getAttributeCode(), $e->getMessage()),
                Mage_Api2_Model_Server::HTTP_BAD_REQUEST,
            );
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_critical(self::RESOURCE_UNKNOWN_ERROR);
        }

        return $this->_getLocation($product);
    }

    /**
     * Update product by its ID
     */
    protected function _update(array $data)
    {
        $product = $this->_getProduct();
        /** @var Mage_Catalog_Model_Api2_Product_Validator_Product $validator */
        $validator = Mage::getModel('catalog/api2_product_validator_product', [
            'operation' => self::OPERATION_UPDATE,
            'product'   => $product,
        ]);

        if (!$validator->isValidData($data)) {
            foreach ($validator->getErrors() as $error) {
                $this->_error($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
            $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }
        if (isset($data['sku'])) {
            $product->setSku($data['sku']);
        }
        // attribute set and product type cannot be updated
        unset($data['attribute_set_id']);
        unset($data['type_id']);
        $this->_prepareDataForSave($product, $data);
        try {
            $product->validate();
            $product->save();
        } catch (Mage_Eav_Model_Entity_Attribute_Exception $e) {
            $this->_critical(
                sprintf('Invalid attribute "%s": %s', $e->getAttributeCode(), $e->getMessage()),
                Mage_Api2_Model_Server::HTTP_BAD_REQUEST,
            );
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_critical(self::RESOURCE_UNKNOWN_ERROR);
        }
    }

    /**
     * Determine if stock management is enabled
     *
     * @param array $stockData
     * @return bool
     */
    protected function _isManageStockEnabled($stockData)
    {
        if (!(isset($stockData['use_config_manage_stock']) && $stockData['use_config_manage_stock'])) {
            $manageStock = isset($stockData['manage_stock']) && $stockData['manage_stock'];
        } else {
            $manageStock = Mage::getStoreConfig(
                Mage_CatalogInventory_Model_Stock_Item::XML_PATH_ITEM . 'manage_stock',
            );
        }
        return (bool) $manageStock;
    }

    /**
     * Check if value from config is used
     *
     * @param array $data
     * @param string $field
     * @return bool
     */
    protected function _isConfigValueUsed($data, $field)
    {
        return isset($data["use_config_$field"]) && $data["use_config_$field"];
    }

    /**
     * Set additional data before product save
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $productData
     */
    protected function _prepareDataForSave($product, $productData)
    {
        if (isset($productData['stock_data'])) {
            if (!$product->isObjectNew() && !isset($productData['stock_data']['manage_stock'])) {
                $productData['stock_data']['manage_stock'] = $product->getStockItem()->getManageStock();
            }
            $this->_filterStockData($productData['stock_data']);
        } else {
            $productData['stock_data'] = [
                'use_config_manage_stock' => 1,
                'use_config_min_sale_qty' => 1,
                'use_config_max_sale_qty' => 1,
            ];
        }
        $product->setStockData($productData['stock_data']);
        // save gift options
        $this->_filterConfigValueUsed($productData, ['gift_message_available', 'gift_wrapping_available']);
        if (isset($productData['use_config_gift_message_available'])) {
            $product->setData('use_config_gift_message_available', $productData['use_config_gift_message_available']);
            if (!$productData['use_config_gift_message_available']
                && ($product->getData('gift_message_available') === null)
            ) {
                $product->setData('gift_message_available', Mage::getStoreConfigAsInt(
                    Mage_GiftMessage_Helper_Message::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ITEMS,
                    $product->getStoreId(),
                ));
            }
        }
        if (isset($productData['use_config_gift_wrapping_available'])) {
            $product->setData('use_config_gift_wrapping_available', $productData['use_config_gift_wrapping_available']);
            if (!$productData['use_config_gift_wrapping_available']
                && ($product->getData('gift_wrapping_available') === null)
            ) {
                $xmlPathGiftWrappingAvailable = 'sales/gift_options/wrapping_allow_items';
                $product->setData('gift_wrapping_available', Mage::getStoreConfigAsInt(
                    $xmlPathGiftWrappingAvailable,
                    $product->getStoreId(),
                ));
            }
        }

        if (isset($productData['website_ids']) && is_array($productData['website_ids'])) {
            $product->setWebsiteIds($productData['website_ids']);
        }
        // Create Permanent Redirect for old URL key
        if (!$product->isObjectNew()  && isset($productData['url_key'])
            && isset($productData['url_key_create_redirect'])
        ) {
            $product->setData('save_rewrites_history', (bool) $productData['url_key_create_redirect']);
        }
        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
        foreach ($product->getTypeInstance(true)->getEditableAttributes($product) as $attribute) {
            //Unset data if object attribute has no value in current store
            if (Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID !== (int) $product->getStoreId()
                && !$product->getExistsStoreValueFlag($attribute->getAttributeCode())
                && !$attribute->isScopeGlobal()
            ) {
                $product->setData($attribute->getAttributeCode(), false);
            }

            if ($this->_isAllowedAttribute($attribute)) {
                if (array_key_exists($attribute->getAttributeCode(), $productData)) {
                    $product->setData(
                        $attribute->getAttributeCode(),
                        $productData[$attribute->getAttributeCode()],
                    );
                }
            }
        }
    }

    /**
     * Filter stock data values
     *
     * @param array $stockData
     */
    protected function _filterStockData(&$stockData)
    {
        $fieldsWithPossibleDefautlValuesInConfig = ['manage_stock', 'min_sale_qty', 'max_sale_qty', 'backorders',
            'qty_increments', 'notify_stock_qty', 'min_qty', 'enable_qty_increments'];
        $this->_filterConfigValueUsed($stockData, $fieldsWithPossibleDefautlValuesInConfig);

        if ($this->_isManageStockEnabled($stockData)) {
            if (isset($stockData['qty']) && (float) $stockData['qty'] > self::MAX_DECIMAL_VALUE) {
                $stockData['qty'] = self::MAX_DECIMAL_VALUE;
            }
            if (isset($stockData['min_qty']) && (int) $stockData['min_qty'] < 0) {
                $stockData['min_qty'] = 0;
            }
            if (!isset($stockData['is_decimal_divided']) || $stockData['is_qty_decimal'] == 0) {
                $stockData['is_decimal_divided'] = 0;
            }
        } else {
            $nonManageStockFields = ['manage_stock', 'use_config_manage_stock', 'min_sale_qty',
                'use_config_min_sale_qty', 'max_sale_qty', 'use_config_max_sale_qty'];
            foreach (array_keys($stockData) as $field) {
                if (!in_array($field, $nonManageStockFields)) {
                    unset($stockData[$field]);
                }
            }
        }
    }

    /**
     * Filter out fields if Use Config Settings option used
     *
     * @param array $data
     * @param array $fields
     */
    protected function _filterConfigValueUsed(&$data, $fields)
    {
        foreach ($fields as $field) {
            if ($this->_isConfigValueUsed($data, $field)) {
                unset($data[$field]);
            }
        }
    }

    /**
     * Check if attribute is allowed
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param array $attributes
     * @return bool
     */
    protected function _isAllowedAttribute($attribute, $attributes = null)
    {
        $isAllowed = true;
        if (is_array($attributes)
            && !(in_array($attribute->getAttributeCode(), $attributes)
            || in_array($attribute->getAttributeId(), $attributes))
        ) {
            $isAllowed = false;
        }
        return $isAllowed;
    }
}
