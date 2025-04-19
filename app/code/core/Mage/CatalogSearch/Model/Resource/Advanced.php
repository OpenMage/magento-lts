<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Advanced Catalog Search resource model
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 */
class Mage_CatalogSearch_Model_Resource_Advanced extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('catalog/product', 'entity_id');
    }

    /**
     * Prepare response object and dispatch prepare price event
     * Return response object
     *
     * @param Varien_Db_Select $select
     * @return Varien_Object
     * @throws Mage_Core_Model_Store_Exception
     *
     * @uses Mage_Tax_Model_Observer_PrepareCatalogIndexPriceSelect::execute()
     * @uses Mage_Wee_Model_Observer_PrepareCatalogIndexSelect::execute()
     */
    protected function _dispatchPreparePriceEvent($select)
    {
        // prepare response object for event
        $response = new Varien_Object();
        $response->setData('additional_calculations', []);

        // prepare event arguments
        $eventArgs = [
            'select'          => $select,
            'table'           => 'price_index',
            'store_id'        => Mage::app()->getStore()->getId(),
            'response_object' => $response,
        ];

        Mage::dispatchEvent('catalog_prepare_price_select', $eventArgs);

        return $response;
    }

    /**
     * Prepare search condition for attribute
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param string|array $value
     * @param Mage_CatalogSearch_Model_Resource_Advanced_Collection $collection
     * @return array|false|string|string[]
     */
    public function prepareCondition($attribute, $value, $collection)
    {
        $condition = false;

        if (is_array($value)) {
            if (!empty($value['from']) || !empty($value['to'])) { // range
                $condition = $value;
            } elseif (in_array($attribute->getBackendType(), ['varchar', 'text'])) { // multiselect
                $condition = ['in_set' => $value];
            } elseif (!isset($value['from']) && !isset($value['to'])) { // select
                $condition = ['in' => $value];
            }
        } elseif (strlen($value) > 0) {
            if (in_array($attribute->getBackendType(), ['varchar', 'text', 'static'])) {
                $condition = ['like' => '%' . $value . '%']; // text search
            } else {
                $condition = $value;
            }
        }

        return $condition;
    }

    /**
     * Add filter by attribute rated price
     *
     * @param Mage_CatalogSearch_Model_Resource_Advanced_Collection $collection
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param string|array $value
     * @param int $rate
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     */
    public function addRatedPriceFilter($collection, $attribute, $value, $rate = 1)
    {
        $adapter = $this->_getReadAdapter();

        $conditions = [];
        if (strlen($value['from']) > 0) {
            $conditions[] = $adapter->quoteInto(
                'price_index.min_price %s * %s >= ?',
                $value['from'],
                Zend_Db::FLOAT_TYPE,
            );
        }
        if (strlen($value['to']) > 0) {
            $conditions[] = $adapter->quoteInto(
                'price_index.min_price %s * %s <= ?',
                $value['to'],
                Zend_Db::FLOAT_TYPE,
            );
        }

        if (!$conditions) {
            return false;
        }

        $collection->addPriceData();
        $select     = $collection->getSelect();
        $response = $this->_dispatchPreparePriceEvent($select);
        $additional = implode('', $response->getDataByKey('additional_calculations'));

        foreach ($conditions as $condition) {
            $select->where(sprintf($condition, $additional, $rate));
        }

        return true;
    }

    /**
     * Add filter by indexable attribute
     *
     * @param Mage_CatalogSearch_Model_Resource_Advanced_Collection $collection
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param string|array $value
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     */
    public function addIndexableAttributeModifiedFilter($collection, $attribute, $value)
    {
        if ($attribute->getIndexType() == 'decimal') {
            $table = $this->getTable('catalog/product_index_eav_decimal');
        } else {
            $table = $this->getTable('catalog/product_index_eav');
        }

        $tableAlias = 'a_' . $attribute->getAttributeId();
        $storeId    = Mage::app()->getStore()->getId();
        $select     = $collection->getSelect();

        if (is_array($value)) {
            if (isset($value['from']) && isset($value['to'])) {
                if (empty($value['from']) && empty($value['to'])) {
                    return false;
                }
            }
        }

        $select->distinct(true);
        $select->join(
            [$tableAlias => $table],
            "e.entity_id={$tableAlias}.entity_id "
                . " AND {$tableAlias}.attribute_id={$attribute->getAttributeId()}"
                . " AND {$tableAlias}.store_id={$storeId}",
            [],
        );

        if (is_array($value) && (isset($value['from']) || isset($value['to']))) {
            if (!empty($value['from'])) {
                $select->where("{$tableAlias}.value >= ?", $value['from']);
            }
            if (!empty($value['to'])) {
                $select->where("{$tableAlias}.value <= ?", $value['to']);
            }
            return true;
        }

        $select->where("{$tableAlias}.value IN(?)", $value);

        return true;
    }
}
