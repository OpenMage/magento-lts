<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog Product Flat Indexer Model
 *
 * @package    Mage_Catalog
 *
 * @method Mage_Catalog_Model_Resource_Product_Flat_Indexer _getResource()
 * @method int                                              getAttributeSetId()
 * @method int                                              getEntityTypeId()
 * @method int                                              getHasOptions()
 * @method int                                              getRequiredOptions()
 * @method Mage_Catalog_Model_Resource_Product_Flat_Indexer getResource()
 * @method string                                           getSku()
 * @method string                                           getTypeId()
 * @method $this                                            setAttributeSetId(int $value)
 * @method $this                                            setEntityTypeId(int $value)
 * @method $this                                            setHasOptions(int $value)
 * @method $this                                            setRequiredOptions(int $value)
 * @method $this                                            setSku(string $value)
 * @method $this                                            setTypeId(string $value)
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
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('catalog/product_flat_indexer');
    }

    /**
     * Rebuild Catalog Product Flat Data
     *
     * @param  mixed $store
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
            self::EVENT_TYPE_REBUILD,
        );
        return $this;
    }

    /**
     * Update attribute data for flat table
     *
     * @param  string    $attributeCode
     * @param  int       $store
     * @param  array|int $productIds
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
     * @param  int   $store
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
     * @param  int   $store
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
     * @param  int   $productId
     * @param  int   $status
     * @param  int   $store
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
     * @param  array|int $productIds
     * @param  int       $store
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
        } catch (Exception $exception) {
            $resource->rollBack();
            throw $exception;
        }

        return $this;
    }

    /**
     * Save Catalog Product(s) Flat data
     *
     * @param  array|int $productIds
     * @param  int       $store
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
        } catch (Exception $exception) {
            $resource->rollBack();
            throw $exception;
        }

        return $this;
    }

    /**
     * Remove product from flat
     *
     * @param  array|int $productIds
     * @param  int       $store
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
     * @param  int   $store
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
