<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Product Flat Indexer Model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_Catalog_Model_Resource_Product_Flat_Indexer _getResource()
 * @method Mage_Catalog_Model_Resource_Product_Flat_Indexer getResource()
 * @method int getEntityTypeId()
 * @method $this setEntityTypeId(int $value)
 * @method int getAttributeSetId()
 * @method $this setAttributeSetId(int $value)
 * @method string getTypeId()
 * @method $this setTypeId(string $value)
 * @method string getSku()
 * @method $this setSku(string $value)
 * @method int getHasOptions()
 * @method $this setHasOptions(int $value)
 * @method int getRequiredOptions()
 * @method $this setRequiredOptions(int $value)
 * @method string getCreatedAt()
 * @method $this setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method $this setUpdatedAt(string $value)
 */
class Mage_Catalog_Model_Product_Flat_Indexer extends Mage_Core_Model_Abstract
{
    /**
     * Catalog product flat entity for indexers
     */
    public const ENTITY = 'catalog_product_flat';

    /**
     * Indexers rebuild event type
     */
    public const EVENT_TYPE_REBUILD = 'catalog_product_flat_rebuild';

    /**
     * Standart model resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/product_flat_indexer');
    }

    /**
     * Rebuild Catalog Product Flat Data
     *
     * @param mixed $store
     * @return $this
     */
    public function rebuild($store = null)
    {
        if (is_null($store)) {
            $this->_getResource()->prepareFlatTables();
        } else {
            $this->_getResource()->prepareFlatTable($store);
        }
        Mage::getSingleton('index/indexer')->processEntityAction(
            new Varien_Object(['id' => $store]),
            self::ENTITY,
            self::EVENT_TYPE_REBUILD
        );
        return $this;
    }

    /**
     * Update attribute data for flat table
     *
     * @param string $attributeCode
     * @param int $store
     * @param int|array $productIds
     * @return $this
     */
    public function updateAttribute($attributeCode, $store = null, $productIds = null)
    {
        if (is_null($store)) {
            foreach (Mage::app()->getStores() as $store) {
                $this->updateAttribute($attributeCode, $store->getId(), $productIds);
            }

            return $this;
        }

        $this->_getResource()->prepareFlatTable($store);
        $attribute = $this->_getResource()->getAttribute($attributeCode);
        $this->_getResource()->updateAttribute($attribute, $store, $productIds);
        $this->_getResource()->updateChildrenDataFromParent($store, $productIds);

        return $this;
    }

    /**
     * Prepare datastorage for catalog product flat
     *
     * @param int $store
     * @return $this
     */
    public function prepareDataStorage($store = null)
    {
        if (is_null($store)) {
            foreach (Mage::app()->getStores() as $store) {
                $this->prepareDataStorage($store->getId());
            }

            return $this;
        }

        $this->_getResource()->prepareFlatTable($store);

        return $this;
    }

    /**
     * Update events observer attributes
     *
     * @param int $store
     * @return $this
     */
    public function updateEventAttributes($store = null)
    {
        if (is_null($store)) {
            foreach (Mage::app()->getStores() as $store) {
                $this->updateEventAttributes($store->getId());
            }

            return $this;
        }

        $this->_getResource()->prepareFlatTable($store);
        $this->_getResource()->updateEventAttributes($store);
        $this->_getResource()->updateRelationProducts($store);

        return $this;
    }

    /**
     * Update product status
     *
     * @param int $productId
     * @param int $status
     * @param int $store
     * @return $this
     */
    public function updateProductStatus($productId, $status, $store = null)
    {
        if (is_null($store)) {
            foreach (Mage::app()->getStores() as $store) {
                $this->updateProductStatus($productId, $status, $store->getId());
            }
            return $this;
        }

        if ($status == Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
            $this->_getResource()->updateProduct($productId, $store);
            $this->_getResource()->updateChildrenDataFromParent($store, $productId);
        } else {
            $this->_getResource()->removeProduct($productId, $store);
        }

        return $this;
    }

    /**
     * Update Catalog Product Flat data
     *
     * @param int|array $productIds
     * @param int $store
     * @return $this
     */
    public function updateProduct($productIds, $store = null)
    {
        if (is_null($store)) {
            foreach (Mage::app()->getStores() as $store) {
                $this->updateProduct($productIds, $store->getId());
            }
            return $this;
        }

        $resource = $this->_getResource();
        $resource->beginTransaction();
        try {
            $resource->removeProduct($productIds, $store);
            $resource->updateProduct($productIds, $store);
            $resource->updateRelationProducts($store, $productIds);
            $resource->commit();
        } catch (Exception $e) {
            $resource->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Save Catalog Product(s) Flat data
     *
     * @param int|array $productIds
     * @param int $store
     * @return $this
     */
    public function saveProduct($productIds, $store = null)
    {
        if (is_null($store)) {
            foreach (Mage::app()->getStores() as $store) {
                $this->saveProduct($productIds, $store->getId());
            }
            return $this;
        }

        $resource = $this->_getResource();
        $resource->beginTransaction();
        try {
            $resource->removeProduct($productIds, $store);
            $resource->saveProduct($productIds, $store);
            $resource->updateRelationProducts($store, $productIds);
            $resource->commit();
        } catch (Exception $e) {
            $resource->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Remove product from flat
     *
     * @param int|array $productIds
     * @param int $store
     * @return $this
     */
    public function removeProduct($productIds, $store = null)
    {
        if (is_null($store)) {
            foreach (Mage::app()->getStores() as $store) {
                $this->removeProduct($productIds, $store->getId());
            }
            return $this;
        }

        $this->_getResource()->removeProduct($productIds, $store);

        return $this;
    }

    /**
     * Delete store process
     *
     * @param int $store
     * @return $this
     */
    public function deleteStore($store)
    {
        $this->_getResource()->deleteFlatTable($store);
        return $this;
    }

    /**
     * Rebuild Catalog Product Flat Data for all stores
     *
     * @return $this
     */
    public function reindexAll()
    {
        $this->_getResource()->reindexAll();
        return $this;
    }

    /**
     * Retrieve list of attribute codes for flat
     *
     * @return array
     */
    public function getAttributeCodes()
    {
        return $this->_getResource()->getAttributeCodes();
    }
}
