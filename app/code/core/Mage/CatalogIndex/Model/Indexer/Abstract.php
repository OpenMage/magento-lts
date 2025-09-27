<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogIndex
 */

/**
 * Catalog indexer abstract class
 *
 * @package    Mage_CatalogIndex
 */
abstract class Mage_CatalogIndex_Model_Indexer_Abstract extends Mage_Core_Model_Abstract implements Mage_CatalogIndex_Model_Indexer_Interface
{
    protected $_processChildren = true;
    protected $_processChildrenForConfigurable = true;
    protected $_runOnce = false;

    /**
     * @param int|string $forceId
     */
    public function processAfterSave(Mage_Catalog_Model_Product $object, $forceId = null)
    {
        $associated = [];
        switch ($object->getTypeId()) {
            case Mage_Catalog_Model_Product_Type::TYPE_GROUPED:
                /** @var Mage_Catalog_Model_Product_Type_Grouped $productType */
                $productType = $object->getTypeInstance(true);
                $associated = $productType->getAssociatedProducts($object);
                break;

            case Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE:
                /** @var Mage_Catalog_Model_Product_Type_Configurable $productType */
                $productType = $object->getTypeInstance(true);
                $associated = $productType->getUsedProducts(null, $object);
                break;
        }

        if (!$this->_isObjectIndexable($object) && is_null($forceId)) {
            return;
        }

        $data = [];

        if ($this->_runOnce) {
            $data = $this->createIndexData($object);
        } else {
            $attributes = $object->getAttributes();
            foreach ($attributes as $attribute) {
                if ($this->_isAttributeIndexable($attribute) && $object->getData($attribute->getAttributeCode()) != null) {
                    $row = $this->createIndexData($object, $attribute);
                    if ($row && is_array($row)) {
                        if (isset($row[0]) && is_array($row[0])) {
                            $data = array_merge($data, $row);
                        } else {
                            $data[] = $row;
                        }
                    }
                }
            }
        }
        $function = 'saveIndex';
        if ($data && is_array($data)) {
            if (isset($data[0]) && is_array($data[0])) {
                $function = 'saveIndices';
            }

            $this->$function($data, $object->getStoreId(), ($forceId != null ? $forceId : $object->getId()));
        }

        if (!$this->_processChildrenForConfigurable && $object->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            return;
        }

        if ($associated && $this->_processChildren) {
            foreach ($associated as $child) {
                $child
                    ->setStoreId($object->getStoreId())
                    ->setWebsiteId($object->getWebsiteId());
                $this->processAfterSave($child, $object->getId());
            }
        }
    }

    /**
     * @param array $data
     * @param int $storeId
     * @param int $productId
     */
    public function saveIndex($data, $storeId, $productId)
    {
        $this->_getResource()->saveIndex($data, $storeId, $productId);
    }

    /**
     * @param int $storeId
     * @param int $productId
     */
    public function saveIndices(array $data, $storeId, $productId)
    {
        $this->_getResource()->saveIndices($data, $storeId, $productId);
    }

    /**
     * @return bool
     */
    protected function _isObjectIndexable(Mage_Catalog_Model_Product $object)
    {
        if ($object->getStatus() != Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
            return false;
        }

        if ($object->getVisibility() != Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG &&
            $object->getVisibility() != Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH
        ) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isAttributeIndexable(Mage_Eav_Model_Entity_Attribute_Abstract $attribute)
    {
        return $this->_isAttributeIndexable($attribute);
    }

    /**
     * @return bool
     */
    protected function _isAttributeIndexable(Mage_Eav_Model_Entity_Attribute_Abstract $attribute)
    {
        return true;
    }

    /**
     * @return array
     */
    public function getIndexableAttributeCodes()
    {
        return $this->_getResource()->loadAttributeCodesByCondition($this->_getIndexableAttributeConditions());
    }

    /**
     * @return array
     */
    protected function _getIndexableAttributeConditions()
    {
        return [];
    }

    /**
     * @param int $productId
     * @param int $storeId
     */
    public function cleanup($productId, $storeId = null)
    {
        $this->_getResource()->cleanup($productId, $storeId);
    }

    /**
     * @return bool
     */
    public function isAttributeIdUsed()
    {
        return true;
    }
}
