<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2017-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bundle Product Price Index Resource model
 *
 * @category   Mage
 * @package    Mage_Bundle
 */
class Mage_Bundle_Model_Resource_Price_Index extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * EAV attributes cache
     *
     * @var array
     */
    protected $_attributes       = [];

    /**
     * Websites cache
     *
     * @var array|null
     */
    protected $_websites;

    /**
     * Customer Groups cache
     *
     * @var array|null
     */
    protected $_customerGroups;

    protected function _construct()
    {
        $this->_init('bundle/price_index', 'entity_id');
    }

    /**
     * Retrieve attribute object
     *
     * @param string $attributeCode
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    protected function _getAttribute($attributeCode)
    {
        if (!isset($this->_attributes[$attributeCode])) {
            $this->_attributes[$attributeCode] = Mage::getSingleton('catalog/config')
                ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
        }
        return $this->_attributes[$attributeCode];
    }

    /**
     * Retrieve websites collection array
     *
     * @return array
     */
    protected function _getWebsites()
    {
        if (is_null($this->_websites)) {
            $this->_websites = Mage::app()->getWebsites(false);
        }
        return $this->_websites;
    }

    /**
     * Retrieve customer groups collection array
     *
     * @return array
     * @throws Mage_Core_Exception
     */
    protected function _getCustomerGroups()
    {
        if (is_null($this->_customerGroups)) {
            $this->_customerGroups = [];
            foreach (Mage::getModel('customer/group')->getCollection() as $group) {
                $this->_customerGroups[$group->getId()] = $group;
            }
        }
        return $this->_customerGroups;
    }

    /**
     * Retrieve product ids array by product condition
     *
     * @param Mage_Catalog_Model_Product|Mage_Catalog_Model_Product_Condition_Interface|array|int $product
     * @param int $lastEntityId
     * @param int $limit
     * @return array
     * @throws Mage_Core_Exception
     */
    public function getProducts($product = null, $lastEntityId = 0, $limit = 100)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(
                ['e' => $this->getTable('catalog/product')],
                ['entity_id'],
            )
            ->where('e.type_id=?', Mage_Catalog_Model_Product_Type::TYPE_BUNDLE);
        if ($product instanceof Mage_Catalog_Model_Product) {
            $select->where('e.entity_id=?', $product->getId());
        } elseif ($product instanceof Mage_Catalog_Model_Product_Condition_Interface) {
            $value = new Zend_Db_Expr($product->getIdsSelect($this->_getReadAdapter()));
            $select->where('e.entity_id IN(?)', $value);
        } elseif (is_numeric($product) || is_array($product)) {
            $select->where('e.entity_id IN(?)', $product);
        }

        $priceType = $this->_getAttribute('price_type');
        $priceTypeAlias = 't_' . $priceType->getAttributeCode();
        $joinConds = [
            $priceTypeAlias . '.attribute_id=:attribute_id',
            $priceTypeAlias . '.store_id=0',
            $priceTypeAlias . '.entity_id=e.entity_id',
        ];

        $select->joinLeft(
            [$priceTypeAlias => $priceType->getBackend()->getTable()],
            implode(' AND ', $joinConds),
            ['price_type' => $priceTypeAlias . '.value'],
        );

        $select->where('e.entity_id>:last_entity_id', $lastEntityId)
            ->order('e.entity_id')
            ->limit($limit);
        $bind = [
            'attribute_id'   => $priceType->getAttributeId(),
            'last_entity_id' => $lastEntityId,
        ];
        return $this->_getReadAdapter()->fetchPairs($select, $bind);
    }

    /**
     * Reindex Bundle product Price Index
     *
     * @param Mage_Catalog_Model_Product|Mage_Catalog_Model_Product_Condition_Interface|array|int $products
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function reindex($products = null)
    {
        $lastEntityId = 0;
        while (true) {
            $productsData = $this->getProducts($products, $lastEntityId);
            if (!$productsData) {
                break;
            }

            foreach ($productsData as $productId => $priceType) {
                $this->_reindexProduct($productId, $priceType);
                $lastEntityId = $productId;
            }
        }

        return $this;
    }

    /**
     * Reindex product price
     *
     * @param int $productId
     * @param int $priceType
     * @return $this
     * @throws Mage_Core_Exception
     */
    protected function _reindexProduct($productId, $priceType)
    {
        $options = $this->getSelections($productId);
        $selectionProducts = [];
        foreach ($options as $option) {
            foreach ($option['selections'] as $selection) {
                $selectionProducts[$selection['product_id']] = $selection['product_id'];
            }
        }

        $priceIndex = [];
        if ($priceType == Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC) {
            // load selection product prices from index for dynamic bundle
            $priceIndex = $this->getProductsPriceFromIndex($selectionProducts);
        }

        /** @var Mage_Core_Model_Website $website */
        foreach ($this->_getWebsites() as $website) {
            if (!$website->getDefaultStore()) {
                continue;
            }
            $salableStatus = $this->getProductsSalableStatus($selectionProducts, $website);
            $priceData = $this->getProductsPriceData($productId, $website);
            $priceData = $priceData[$productId];

            /** @var Mage_Customer_Model_Group $group */
            foreach ($this->_getCustomerGroups() as $group) {
                if ($priceType == Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED) {
                    $basePrice     = $this->_getBasePrice($productId, $priceData, $website, $group);
                    $customOptions = $this->getCustomOptions($productId, $website);
                } elseif ($priceType == Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC) {
                    $basePrice = 0;
                }

                [$minPrice, $maxPrice] = $this->_calculateBundleSelections(
                    $options,
                    $salableStatus,
                    $productId,
                    $priceType,
                    $basePrice,
                    $priceData,
                    $priceIndex,
                    $website,
                    $group,
                );

                if ($priceType == Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED) {
                    [$minPrice, $maxPrice] =
                        $this->_calculateCustomOptions($customOptions, $basePrice, $minPrice, $maxPrice);
                }

                $this->_savePriceIndex($productId, $website->getId(), $group->getId(), $minPrice, $maxPrice);
            }
        }

        return $this;
    }

    /**
     * Save price index
     *
     * @param int $productId
     * @param int $websiteId
     * @param int $groupId
     * @param float $minPrice
     * @param float $maxPrice
     * @return $this
     * @throws Zend_Db_Exception
     */
    protected function _savePriceIndex($productId, $websiteId, $groupId, $minPrice, $maxPrice)
    {
        $adapter = $this->_getWriteAdapter();
        $adapter->beginTransaction();
        try {
            $bind = [$productId, $websiteId, $groupId, $minPrice, $maxPrice];
            $adapter->insertOnDuplicate($this->getMainTable(), $bind, ['min_price', 'max_price']);
            $adapter->commit();
        } catch (Exception $e) {
            $adapter->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Retrieve bundle options with selections and prices by product
     *
     * @param int $productId
     * @return array
     * @throws Zend_Db_Adapter_Exception|Zend_Db_Statement_Exception
     */
    public function getSelections($productId)
    {
        $options = [];
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from(
                ['option_table' => $this->getTable('bundle/option')],
                ['option_id', 'required', 'type'],
            )
            ->join(
                ['selection_table' => $this->getTable('bundle/selection')],
                'selection_table.option_id=option_table.option_id',
                ['selection_id', 'product_id', 'selection_price_type',
                    'selection_price_value', 'selection_qty', 'selection_can_change_qty'],
            )
            ->join(
                ['e' => $this->getTable('catalog/product')],
                'e.entity_id=selection_table.product_id AND e.required_options=0',
                [],
            )
            ->where('option_table.parent_id=:product_id');

        $query = $read->query($select, ['product_id' => $productId]);
        while ($row = $query->fetch()) {
            if (!isset($options[$row['option_id']])) {
                $options[$row['option_id']] = [
                    'option_id'     => $row['option_id'],
                    'required'      => $row['required'],
                    'type'          => $row['type'],
                    'selections'    => [],
                ];
            }
            $options[$row['option_id']]['selections'][$row['selection_id']] = [
                'selection_id'      => $row['selection_id'],
                'product_id'        => $row['product_id'],
                'price_type'        => $row['selection_price_type'],
                'price_value'       => $row['selection_price_value'],
                'qty'               => $row['selection_qty'],
                'can_change_qty'    => $row['selection_can_change_qty'],
            ];
        }

        return $options;
    }

    /**
     * Retrieve salable product statuses
     *
     * @param int|array $products
     * @return array
     * @throws Mage_Core_Exception
     */
    public function getProductsSalableStatus($products, Mage_Core_Model_Website $website)
    {
        $read = $this->_getReadAdapter();
        $productsData = [];
        $select = $read->select()
            ->from(['e' => $this->getTable('catalog/product')], 'entity_id')
            ->where('e.entity_id IN(?)', $products);
        // add belong to website
        $select->joinLeft(
            ['pw' => $this->getTable('catalog/product_website')],
            'e.entity_id=pw.product_id AND pw.website_id=:website_id',
            ['pw.website_id'],
        );

        $store = $website->getDefaultStore();

        // add product status
        $status = $this->_getAttribute('status');
        if ($status->isScopeGlobal()) {
            $select->join(
                ['t_status' => $status->getBackend()->getTable()],
                'e.entity_id=t_status.entity_id'
                . ' AND t_status.attribute_id=:status_attribute_id'
                . ' AND t_status.store_id=0',
                ['status' => 't_status.value'],
            );
        } else {
            $statusField = $read->getCheckSql(
                't2_status.value_id > 0',
                't2_status.value',
                't1_status.value',
            );

            $statusTable = $status->getBackend()->getTable();
            $select->join(
                ['t1_status' => $statusTable],
                'e.entity_id=t1_status.entity_id'
                . ' AND t1_status.attribute_id=:status_attribute_id'
                . ' AND t1_status.store_id=0',
                ['status' => $statusField],
            )
            ->joinLeft(
                ['t2_status' => $statusTable],
                't1_status.entity_id = t2_status.entity_id'
                . ' AND t1_status.attribute_id = t2_status.attribute_id'
                . ' AND t2_status.store_id=:store_id',
                [],
            );
        }

        $bind = [
            'status_attribute_id' => $status->getAttributeId(),
            'website_id'   => $website->getId(),
            'store_id'     => $store->getId(),
        ];

        Mage::dispatchEvent('catalog_product_prepare_index_select', [
            'website'   => $website,
            'select'    => $select,
            'bind'      => $bind,
        ]);

        $query = $read->query($select, $bind);
        while ($row = $query->fetch()) {
            $salable = $row['salable'] ?? true;
            $website = $row['website_id'] > 0;
            $status  = $row['status'];

            $productsData[$row['entity_id']] = $salable && $status && $website;
        }

        return $productsData;
    }

    /**
     * Retrieve Selection Product price from Price Index
     * Return index key {entity_id}-{website_id}-{customer_group_id}
     *
     * @param int|array $productIds
     * @return array
     */
    public function getProductsPriceFromIndex($productIds)
    {
        $price  = $this->_getAttribute('price');
        $read = $this->_getReadAdapter();
        $key = $read->getConcatSql(['entity_id', 'customer_group_id', 'website_id'], '-');

        $select = $read->select()
            ->from(
                ['price_index' => $this->getTable('catalogindex/price')],
                ['index_key' => $key, 'value'],
            )
            ->where('entity_id IN(?)', $productIds)
            ->where('attribute_id= :attribute_id');
        return $read->fetchPairs($select, ['attribute_id' => $price->getAttributeId()]);
    }

    /**
     * Retrieve product(s) price data
     *
     * @param int|array $products
     * @return array
     * @throws Zend_Db_Adapter_Exception|Zend_Db_Statement_Exception
     */
    public function getProductsPriceData($products, Mage_Core_Model_Website $website)
    {
        $productsData = [];
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from(['e' => $this->getTable('catalog/product')], 'entity_id')
            ->where('e.entity_id IN(?)', $products);

        $this->_addAttributeDataToSelect($select, 'price', $website);
        $this->_addAttributeDataToSelect($select, 'special_price', $website);
        $this->_addAttributeDataToSelect($select, 'special_from_date', $website);
        $this->_addAttributeDataToSelect($select, 'special_to_date', $website);

        $query = $read->query($select);
        while ($row = $query->fetch()) {
            $productsData[$row['entity_id']] = [
                'price'             => $row['price'],
                'special_price'     => $row['special_price'],
                'special_from_date' => $row['special_from_date'],
                'special_to_date'   => $row['special_to_date'],
            ];
        }

        return $productsData;
    }

    /**
     * Add attribute data to select
     *
     * @param string $attributeCode
     * @return $this
     * @throws Mage_Core_Exception
     */
    protected function _addAttributeDataToSelect(
        Varien_Db_Select $select,
        $attributeCode,
        Mage_Core_Model_Website $website
    ) {
        $attribute  = $this->_getAttribute($attributeCode);
        $store      = $website->getDefaultStore();
        if ($attribute->isScopeGlobal()) {
            $table = 't_' . $attribute->getAttributeCode();
            $select->joinLeft(
                [$table => $attribute->getBackend()->getTable()],
                "e.entity_id={$table}.entity_id"
                . " AND {$table}.attribute_id={$attribute->getAttributeId()}"
                . " AND {$table}.store_id=0",
                [$attribute->getAttributeCode() => $table . '.value'],
            );
        } else {
            $tableName   = $attribute->getBackend()->getTable();
            $tableGlobal = 't1_' . $attribute->getAttributeCode();
            $tableStore  = 't2_' . $attribute->getAttributeCode();

            $attributeCond = $this->getReadConnection()->getCheckSql(
                $tableStore . '.value_id > 0',
                $tableStore . '.value',
                $tableGlobal . '.value',
            );
            $select->joinLeft(
                [$tableGlobal => $tableName],
                "e.entity_id = {$tableGlobal}.entity_id"
                . " AND {$tableGlobal}.attribute_id = {$attribute->getAttributeId()}"
                . " AND {$tableGlobal}.store_id = 0",
                [$attribute->getAttributeCode() => $attributeCond],
            )
            ->joinLeft(
                [$tableStore => $tableName],
                "{$tableGlobal}.entity_id = {$tableStore}.entity_id"
                . " AND {$tableGlobal}.attribute_id = {$tableStore}.attribute_id"
                . " AND {$tableStore}.store_id = " . $store->getId(),
                [],
            );
        }
        return $this;
    }

    /**
     * Retrieve fixed bundle base price (with special price and rules)
     *
     * @param int $productId
     * @param Mage_Core_Model_Website $website
     * @param Mage_Customer_Model_Group $customerGroup
     * @return float
     */
    protected function _getBasePrice($productId, array $priceData, $website, $customerGroup)
    {
        $store          = $website->getDefaultStore();
        $storeTimeStamp = Mage::app()->getLocale()->storeTimeStamp($store);
        $finalPrice     = $this->_calculateSpecialPrice($priceData['price'], $priceData, $website);

        $rulePrice = Mage::getResourceModel('catalogrule/rule')
            ->getRulePrice($storeTimeStamp, $website->getId(), $customerGroup->getId(), $productId);

        if ($rulePrice !== null && $rulePrice !== false) {
            $finalPrice = min($finalPrice, $rulePrice);
        }

        return $finalPrice;
    }

    /**
     * Retrieve custom options for product
     *
     * @param int $productId
     * @return array
     * @throws Zend_Db_Adapter_Exception|Zend_Db_Statement_Exception
     */
    public function getCustomOptions($productId, Mage_Core_Model_Website $website)
    {
        $options = [];
        $store   = $website->getDefaultStore();
        $price   = $this->_getAttribute('price');
        $adapter = $this->_getReadAdapter();

        $bind = [
            ':product_id' => $productId,
            ':store_id'   => $store->getId(),

        ];
        $select = $adapter->select()
            ->from(
                ['option_table' => $this->getTable('catalog/product_option')],
                ['option_id', 'is_require', 'type'],
            )
            ->where('option_table.product_id=:product_id');

        if ($price->isScopeGlobal()) {
            $select->join(
                ['price_table' => $this->getTable('catalog/product_option_price')],
                'option_table.option_id = price_table.option_id' .
                ' AND price_table.store_id = 0',
                ['value_id' => 'option_price_id', 'price', 'price_type'],
            );
        } else {
            $valueIdCond = $adapter->getCheckSql(
                'price_store_table.option_price_id IS NOT NULL',
                'price_store_table.option_price_id',
                'price_global_table.option_price_id',
            );
            $priceCond = $adapter->getCheckSql(
                'price_store_table.price IS NOT NULL',
                'price_store_table.price',
                'price_global_table.price',
            );
            $priceTypeCond = $adapter->getCheckSql(
                'price_store_table.price_type IS NOT NULL',
                'price_store_table.price_type',
                'price_global_table.price_type',
            );

            $select
                ->join(
                    ['price_global_table' => $this->getTable('catalog/product_option_price')],
                    'option_table.option_id=price_global_table.option_id' .
                    ' AND price_global_table.store_id=0',
                    [
                        'value_id'   => $valueIdCond,
                        'price'      => $priceCond,
                        'price_type' => $priceTypeCond,
                    ],
                )
                ->joinLeft(
                    ['price_store_table' => $this->getTable('catalog/product_option_price')],
                    'option_table.option_id = price_store_table.option_id' .
                    ' AND price_store_table.store_id=:store_id',
                    [],
                );
        }

        $query = $adapter->query($select, $bind);
        while ($row = $query->fetch()) {
            if (!isset($options[$row['option_id']])) {
                $options[$row['option_id']] = [
                    'option_id'     => $row['option_id'],
                    'is_require'    => $row['is_require'],
                    'type'          => $row['type'],
                    'values'        => [],
                ];
            }
            $options[$row['option_id']]['values'][$row['value_id']] = [
                'price_type'        => $row['price_type'],
                'price_value'       => $row['price'],
            ];
        }

        $select = $adapter->select()
            ->from(
                ['option_table' => $this->getTable('catalog/product_option')],
                ['option_id', 'is_require', 'type'],
            )
            ->join(
                ['type_table' => $this->getTable('catalog/product_option_type_value')],
                'option_table.option_id=type_table.option_id',
                [],
            )
            ->where('option_table.product_id=:product_id');

        if ($price->isScopeGlobal()) {
            $select->join(
                ['price_table' => $this->getTable('catalog/product_option_type_price')],
                'type_table.option_type_id=price_table.option_type_id' .
                ' AND price_table.store_id=0',
                ['value_id' => 'option_type_id', 'price', 'price_type'],
            );
        } else {
            $select
                ->join(
                    ['price_global_table' => $this->getTable('catalog/product_option_type_price')],
                    'type_table.option_type_id=price_global_table.option_type_id' .
                    ' AND price_global_table.store_id=0',
                    [
                        'value_id'   => $valueIdCond,
                        'price'      => $priceCond,
                        'price_type' => $priceTypeCond,
                    ],
                )
                ->joinLeft(
                    ['price_store_table' => $this->getTable('catalog/product_option_type_price')],
                    'type_table.option_type_id=price_store_table.option_type_id' .
                    ' AND price_store_table.store_id=:store_id',
                    [],
                );
        }

        $query = $adapter->query($select, $bind);
        while ($row = $query->fetch()) {
            if (!isset($options[$row['option_id']])) {
                $options[$row['option_id']] = [
                    'option_id'     => $row['option_id'],
                    'is_require'    => $row['is_require'],
                    'type'          => $row['type'],
                    'values'        => [],
                ];
            }
            $options[$row['option_id']]['values'][$row['value_id']] = [
                'price_type'        => $row['price_type'],
                'price_value'       => $row['price'],
            ];
        }

        return $options;
    }

    /**
     * Calculate custom options price
     * Return array with indexes(0 -> min_price, 1 -> max_price)
     *
     * @param float $basePrice
     * @param float $minPrice
     * @param float $maxPrice
     * @return array
     */
    public function _calculateCustomOptions(array $options, $basePrice, $minPrice, $maxPrice)
    {
        foreach ($options as $option) {
            $optionPrices = [];
            foreach ($option['values'] as $value) {
                if ($value['price_type'] == 'percent') {
                    $valuePrice = $basePrice * $value['price_value'] / 100;
                } else {
                    $valuePrice = $value['price_value'];
                }
                $optionPrices[] = $valuePrice;
            }
            if ($option['is_require']) {
                $minPrice += min($optionPrices);
            }
            $multiTypes = [
                Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN,
                Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX,
                Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE,
            ];
            if ($optionPrices) {
                if (in_array($option['type'], $multiTypes)) {
                    $maxPrice += array_sum($optionPrices);
                } else {
                    $maxPrice += max($optionPrices);
                }
            }
        }

        return [$minPrice, $maxPrice];
    }

    /**
     * Calculate minimal and maximal price for bundle selections
     * Return array with prices (0 -> min_price, 1 -> max_price)
     *
     * @param int $productId
     * @param int $priceType
     * @param float $basePrice
     * @param array $priceData
     * @param array $priceIndex
     * @param Mage_Core_Model_Website $website
     * @param Mage_Customer_Model_Group $group
     * @return array
     */
    public function _calculateBundleSelections(
        array $options,
        array $salableStatus,
        $productId,
        $priceType,
        $basePrice,
        $priceData,
        $priceIndex,
        $website,
        $group
    ) {
        $minPrice = $maxPrice = $basePrice;
        $optPrice = 0;

        foreach ($options as $option) {
            $optionPrices = [];
            foreach ($option['selections'] as $selection) {
                if (!$selection['product_id']) {
                    continue;
                }

                if (!$salableStatus[$selection['product_id']]) {
                    continue;
                }

                if ($priceType == Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED) {
                    $basePrice = $this->_getBasePrice($productId, $priceData, $website, $group);
                }

                // calculate selection price
                if ($priceType == Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC) {
                    $priceIndexKey = implode('-', [
                        $selection['product_id'],
                        $website->getId(),
                        $group->getId(),
                    ]);

                    $selectionPrice = $priceIndex[$priceIndexKey] ?? 0;
                    $selectionPrice = $this->_calculateSpecialPrice($selectionPrice, $priceData, $website);
                } else {
                    if ($selection['price_type']) { // percent
                        $selectionPrice = $basePrice * $selection['price_value'] / 100;
                    } else {
                        $selectionPrice = $this->_calculateSpecialPrice(
                            $selection['price_value'],
                            $priceData,
                            $website,
                        );
                    }
                }

                // calculate selection qty
                if ($selection['can_change_qty'] && $option['type'] != 'multi' && $option['type'] != 'checkbox') {
                    $qty = 1;
                } else {
                    $qty = $selection['qty'];
                }

                $selectionPrice = $selectionPrice * $qty;
                $optionPrices[$selection['selection_id']] = $selectionPrice;
            }

            if ($optionPrices) {
                if ($option['required']) {
                    $minPrice += min($optionPrices);
                } else {
                    $optPrice = $optPrice && $optPrice < min($optionPrices) ? $optPrice : min($optionPrices);
                }
                if (in_array($option['type'], ['multi', 'checkbox'])) {
                    $maxPrice += array_sum($optionPrices);
                } else {
                    $maxPrice += max($optionPrices);
                }
            }
        }

        if ($minPrice == 0) {
            $minPrice = $optPrice;
        }
        return [$minPrice, $maxPrice];
    }

    /**
     * Apply special price
     *
     * @param float $finalPrice
     * @return float
     */
    public function _calculateSpecialPrice($finalPrice, array $priceData, Mage_Core_Model_Website $website)
    {
        $store              = $website->getDefaultStore();
        $specialPrice       = $priceData['special_price'];

        if (!is_null($specialPrice) && $specialPrice != false) {
            if (Mage::app()->getLocale()->isStoreDateInInterval($store, $priceData['special_from_date'], $priceData['special_to_date'])) {
                $specialPrice   = ($finalPrice * $specialPrice) / 100;
                $finalPrice     = min($finalPrice, $specialPrice);
            }
        }

        return $finalPrice;
    }

    /**
     * Retrieve price index for products
     *
     * @param int|array $productIds
     * @param int $websiteId
     * @param int $groupId
     * @return array
     * @throws Zend_Db_Adapter_Exception|Zend_Db_Statement_Exception
     */
    public function loadPriceIndex($productIds, $websiteId, $groupId)
    {
        $prices = [];
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from(
                ['pi' => $this->getMainTable()],
                ['entity_id', 'min_price', 'max_price'],
            )
            ->where('entity_id IN(?)', $productIds)
            ->where('website_id=:website_id')
            ->where('customer_group_id=:group_id');
        $bind = [
            'website_id' => $websiteId,
            'group_id'   => $groupId,
        ];
        $query = $adapter->query($select, $bind);
        while ($row = $query->fetch()) {
            $prices[$row['entity_id']] = [
                'min_price' => $row['min_price'],
                'max_price' => $row['max_price'],
            ];
        }

        return $prices;
    }
}
