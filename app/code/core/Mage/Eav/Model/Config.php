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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Eav_Model_Config
{
    const ENTITIES_CACHE_ID     = 'EAV_ENTITY_TYPES';
    const ATTRIBUTES_CACHE_ID   = 'EAV_ENTITY_ATTRIBUTES';
    const CACHE_TAG = 'EAV';

    /**
     * Entity types cache
     *
     * @var Mage_Eav_Model_Entity_Type[]
     */
    protected $_entityTypes = null;

    /**
     * Entity types data
     *
     * @var array
     */
    protected $_entityData;

    /**
     * Attributes cache
     *
     * @var Mage_Eav_Model_Entity_Attribute_Abstract[]
     */
    protected $_attributes = null;

    /**
     * Attribute sets cache
     *
     * @var Mage_Eav_Model_Entity_Attribute_Set[]
     */
    protected $_attributeSets = null;

    /**
     * Attributes data
     *
     * @var array
     */
    protected $_attributeData;

    /**
     * Information about preloaded attributes
     *
     * @var array
     */
    protected $_preloadedAttributes              = array();

    /**
     * Information about entity types with initialized attributes
     *
     * @var array
     */
    protected $_initializedAttributes            = array();

    /**
     * Product Attributes used in product listing
     *
     * @var array
     */
    protected $_attributesUsedInProductListing;

    /**
     * Product Attributes For Sort By
     *
     * @var array
     */
    protected $_attributesUsedForSortInProductListing;

    /**
     * filtered product attributes in sets for layered navigation
     *
     * @var array
     */
    protected $_filteredAttributeSetsUsedInProductListing;

    /**
     * Attribute codes cache array
     *
     * @var array
     */
    protected $_attributeCodes                   = array();

    /**
     * Initialized objects
     *
     * array ($objectId => $object)
     *
     * @var array
     */
    protected $_objects;

    /**
     * References between codes and identifiers
     *
     * array (
     *      'attributes'=> array ($attributeId => $attributeCode),
     *      'entities'  => array ($entityId => $entityCode)
     * )
     *
     * @var array
     */
    protected $_references;

    /**
     * Cache flag
     *
     * @var boolean
     */
    protected $_isCacheEnabled                    = null;

    /**
     * Array of attributes objects used in collections
     *
     * @var array
     */
    protected $_collectionAttributes              = array();

    /**
     * Check EAV cache availability
     *
     * @return bool
     */
    protected function _isCacheEnabled()
    {
        if ($this->_isCacheEnabled === null) {
            if (false === Mage::isInstalled() || Mage::app()->getUpdateMode() == true) {
                $this->_isCacheEnabled = false;
            } else {
                $this->_isCacheEnabled = Mage::app()->useCache('eav');
            }
        }
        return $this->_isCacheEnabled;
    }

    /**
     * Load data from cache
     *
     * @param string $cacheId
     *
     * @return bool|string
     */
    protected function _loadDataFromCache($cacheId)
    {
        if (true === $this->_isCacheEnabled()) {
            return Mage::app()->loadCache($cacheId);
        }
        return false;
    }

    /**
     * save data to cache
     *
     * @param string $cacheId
     * @param string $data
     */
    protected function _saveDataToCache($cacheId, $data)
    {
        Mage::app()->saveCache($data, $cacheId, array(self::CACHE_TAG, Mage_Eav_Model_Entity_Attribute::CACHE_TAG));
    }


    /**
     * Reset object state
     *
     * @deprecated
     * @return Mage_Eav_Model_Config
     */
    public function clear()
    {
        $this->_references    = null;
        $this->_entityTypes   = null;
        $this->_attributeSets = null;
        $this->_attributes    = null;
        $this->_attributesUsedForSortInProductListing = null;
        $this->_attributesUsedInProductListing = null;
        $this->_filteredAttributeSetsUsedInProductListing = null;
        return $this;
    }

    /**
     * Get object by identifier
     *
     * @param   mixed $id
     * @return  mixed
     */
    protected function _load($id)
    {
        return isset($this->_objects[$id]) ? $this->_objects[$id] : null;
    }

    /**
     * Associate object with identifier
     *
     * @param   mixed $obj
     * @param   mixed $id
     * @return  Mage_Eav_Model_Config
     */
    protected function _save($obj, $id)
    {
        $this->_objects[$id] = $obj;
        return $this;
    }

    /**
     * Specify reference for entity type id
     *
     * @param   int $id
     * @param   string $code
     * @return  Mage_Eav_Model_Config
     */
    protected function _addEntityTypeReference($id, $code)
    {
        $this->_references['entity'][$id] = $code;
        return $this;
    }

    /**
     * Get entity type code by id
     *
     * @param   int $id
     * @return  string
     */
    protected function _getEntityTypeReference($id)
    {
        return isset($this->_references['entity'][$id]) ? $this->_references['entity'][$id] : null;
    }

    /**
     * Specify reference between entity attribute id and attribute code
     *
     * @param   int $id
     * @param   string $code
     * @param   string $entityTypeCode
     * @return  Mage_Eav_Model_Config
     */
    protected function _addAttributeReference($id, $code, $entityTypeCode)
    {
        $this->_references['attribute'][$entityTypeCode][$id] = $code;
        return $this;
    }

    /**
     * Get attribute code by attribute id
     *
     * @param   int $id
     * @param   string $entityTypeCode
     * @return  string
     */
    protected function _getAttributeReference($id, $entityTypeCode)
    {
        if (isset($this->_references['attribute'][$entityTypeCode][$id])) {
            return $this->_references['attribute'][$entityTypeCode][$id];
        }
        return null;
    }

    /**
     * Get internal cache key for entity type code
     *
     * @param   string $code
     * @return  string
     */
    protected function _getEntityKey($code)
    {
        return 'ENTITY/' . $code;
    }

    /**
     * Get internal cache key for attribute object cache
     *
     * @param   string $entityTypeCode
     * @param   string $attributeCode
     * @return  string
     */
    protected function _getAttributeKey($entityTypeCode, $attributeCode)
    {
        return 'ATTRIBUTE/' . $entityTypeCode . '/' . $attributeCode;
    }

    /**
     * Initialize all entity types data
     *
     * @return Mage_Eav_Model_Config
     */
    protected function _initEntityTypes()
    {
        if (true === is_array($this->_entityTypes)) {
            return $this;
        }
        Varien_Profiler::start('EAV: ' . __METHOD__);
        //try load information about entity types from cache
        $cache = $this->_loadDataFromCache(self::ENTITIES_CACHE_ID);
        if ($cache) {
            list($this->_entityTypes, $this->_references['entity']) = unserialize($cache);
            Varien_Profiler::stop('EAV: ' . __METHOD__);
            return $this;
        }
        $this->_entityTypes   = array();
        $entityTypeCollection = Mage::getResourceModel('eav/entity_type_collection');
        if ($entityTypeCollection->count() > 0) {
            /** @var $entityType Mage_Eav_Model_Entity_Type */
            foreach ($entityTypeCollection as $entityType) {
                $entityTypeCode                      = $entityType->getData('entity_type_code');
                $this->_entityTypes[$entityTypeCode] = $entityType;
                $this->_addEntityTypeReference($entityType->getData('entity_type_id'), $entityTypeCode);
            }
        }
        Varien_Profiler::stop('EAV: ' . __METHOD__);
        return $this;
    }

    /**
     * Initialize all attribute sets
     *
     * @return Mage_Eav_Model_Config
     */
    protected function _initAllAttributeSets()
    {
        if (true === is_array($this->_attributeSets)) {
            return $this;
        }
        Varien_Profiler::start('EAV: ' . __METHOD__);
        $this->_attributeSets   = array();
        $attributeSetCollection = Mage::getResourceModel('eav/entity_attribute_set_collection');
        if ($attributeSetCollection->count() > 0) {
            /** @var $attributeSet Mage_Eav_Model_Entity_Attribute_Set */
            foreach ($attributeSetCollection as $attributeSet) {
                $this->_attributeSets[$attributeSet->getData('attribute_set_id')] = $attributeSet;
            }
        }
        Varien_Profiler::stop('EAV: ' . __METHOD__);
        return $this;
    }

    /**
     * preload all attributes used for sort in product listing
     *
     * @return Mage_Eav_Model_Config
     */
    protected function _initFilterableAttributesInAttributeSetsUsedInProductListing()
    {
        $productEntityType = Mage_Catalog_Model_Product::ENTITY;
        $productEntityTypeId = $this->getEntityType(Mage_Catalog_Model_Product::ENTITY)->getEntityTypeId();
        $productAttributeSets = array_filter($this->_attributeSets, function ($attributeSet) use ($productEntityTypeId) {
            /** @var Mage_Eav_Model_Entity_Attribute_Set $attributeSet */
            return (int)$attributeSet->getEntityTypeId() == $productEntityTypeId;
        });
        $this->_filteredAttributeSetsUsedInProductListing = array();
        foreach ($productAttributeSets as $attributeSet) {
            /** @var Mage_Eav_Model_Entity_Attribute_Set $attributeSet */
            $this->_filteredAttributeSetsUsedInProductListing[$attributeSet->getAttributeSetId()] = array();
            $this->_filteredAttributeSetsUsedInProductListing[$attributeSet->getAttributeSetId()] = array_filter(
                $this->_attributeSets[$attributeSet->getAttributeSetId()]->getAttributeCodes(), function ($attribute) use ($productEntityType) {
                return (int)$this->_attributes[$productEntityType][$attribute]->getIsFilterable() == 1;
            });
        }
        return $this;
    }

    /**
     * preload all attributes used for sort in product listing
     *
     * @return Mage_Eav_Model_Config
     */
    protected function _initAttributesUsedForSortInProductListing()
    {
        $this->_attributesUsedForSortInProductListing = array_filter($this->_attributes[Mage_Catalog_Model_Product::ENTITY], function ($attribute) {
            /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
            return (int)$attribute->getUsedForSortBy() == 1;
        });
        return $this;
    }

    /**
     * preload all attributes used in product listing
     *
     * @return Mage_Eav_Model_Config
     */
    protected function _initAttributesUsedInProductListing()
    {
        $this->_attributesUsedInProductListing = array_filter($this->_attributes[Mage_Catalog_Model_Product::ENTITY], function ($attribute) {
            /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
            return (int)$attribute->getUsedInProductListing() == 1;
        });
        return $this;
    }

    /**
     * Initialize all attributes for all entity types
     *
     * @return Mage_Eav_Model_Config
     */
    protected function _initAllAttributes()
    {
        if (true === is_array($this->_attributes)) {
            return $this;
        }
        Varien_Profiler::start('EAV: ' . __METHOD__);
        $this->_initEntityTypes();
        //try load information about attributes and attribute sets from cache
        $cache = $this->_loadDataFromCache(self::ATTRIBUTES_CACHE_ID);
        if ($cache) {
            list(
                $this->_attributeSets,
                $this->_attributes,
                $this->_references['attribute'],
                $this->_attributesUsedInProductListing,
                $this->_attributesUsedForSortInProductListing,
                $this->_filteredAttributeSetsUsedInProductListing
                ) = unserialize($cache);
            Varien_Profiler::stop('EAV: ' . __METHOD__);
            return $this;
        }
        $this->_initAllAttributeSets();
        if (true === is_array($this->_entityTypes) && count($this->_entityTypes) > 0) {
            foreach ($this->_entityTypes as $entityType) {
                $this->_initAttributes($entityType);
            }
        }

        // during setup the product entity is not full populated
        if(true === Mage::isInstalled()) {
            // try Catch around init for product attributes is needed during setup scripts
            $this->_initAttributesUsedForSortInProductListing();
            $this->_initAttributesUsedInProductListing();
            $this->_initFilterableAttributesInAttributeSetsUsedInProductListing();
        }

        if (true === $this->_isCacheEnabled()) {
            $this->_saveDataToCache(self::ATTRIBUTES_CACHE_ID,
                serialize(array(
                    $this->_attributeSets,
                    $this->_attributes,
                    $this->_references['attribute'],
                    $this->_attributesUsedInProductListing,
                    $this->_attributesUsedForSortInProductListing,
                    $this->_filteredAttributeSetsUsedInProductListing
                ))
            );
            // save entities types to cache because they are fully set now
            // (we've just added attribute_codes to each entity type object)
            $this->_saveDataToCache(self::ENTITIES_CACHE_ID,
                serialize(array($this->_entityTypes, $this->_references['entity']))
            );
        }
        Varien_Profiler::stop('EAV: ' . __METHOD__);
        return $this;
    }


    /**
     * Init attribute store labels
     *
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @param array                      $attributeData
     */
    protected function _initAttributeStoreLabels(Mage_Eav_Model_Entity_Attribute $attribute){
        if(true === method_exists($attribute, 'getStoreLabels')){
            $attribute->setData('store_labels', $attribute->getStoreLabels());
        }else{
            $attribute->setData('store_labels', array());
        }
    }

    /**
     * Init attribute from attribute data array
     *
     * @param Mage_Eav_Model_Entity_Type $entityType
     * @param array                      $attributeData
     */
    protected function _initAttribute($entityType, $attributeData)
    {
        $entityTypeCode = $entityType->getEntityTypeCode();
        if (false === empty($attributeData['attribute_model'])) {
            $model = $attributeData['attribute_model'];
        } else {
            $model = $entityType->getAttributeModel();
        }
        $attributeCode = $attributeData['attribute_code'];
        $attribute     = Mage::getModel($model)->setData($attributeData);
        $this->_initAttributeStoreLabels($attribute);
        $entity        = $entityType->getEntity();
        $isFlatTableEntity = false === method_exists($entity, 'getDefaultAttributes');
        if ($entity && (true === $isFlatTableEntity || true === in_array($attributeCode, $entity->getDefaultAttributes()))) {
            $attribute->setBackendType(Mage_Eav_Model_Entity_Attribute_Abstract::TYPE_STATIC)->setIsGlobal(1);
        }
        $this->_attributes[$entityTypeCode][$attributeCode] = $attribute;
        $this->_addAttributeReference($attributeData['attribute_id'], $attributeCode, $entityTypeCode);
    }

    /**
     * Get entity type object by entity type code/identifier
     *
     * @param   mixed $code
     * @return  Mage_Eav_Model_Entity_Type
     */
    public function getEntityType($code)
    {
        if ($code instanceof Mage_Eav_Model_Entity_Type) {
            return $code;
        }
        Varien_Profiler::start('EAV: ' . __METHOD__);
        $this->_initEntityTypes();
        if (true === is_numeric($code)) {
            $entityCode = $this->_getEntityTypeReference($code);
            if ($entityCode !== null) {
                $code = $entityCode;
            }
        }
        if (false === isset($this->_entityTypes[$code])) {
            Mage::throwException(Mage::helper('eav')->__('Invalid entity_type specified: %s', $code));
        }
        Varien_Profiler::stop('EAV: ' . __METHOD__);
        return $this->_entityTypes[$code];
    }

    /**
     * Initialize all attributes for entity type
     *
     * @param   string $entityType
     * @return  Mage_Eav_Model_Config
     */
    protected function _initAttributes($entityType)
    {
        Varien_Profiler::start('EAV: ' . __METHOD__);
        /** @var Mage_Eav_Model_Resource_Entity_Attribute_Collection $attributesCollection */
        $attributesCollection = Mage::getResourceModel($entityType->getEntityAttributeCollection());
        if ($attributesCollection) {
            $attributesData = $attributesCollection->setEntityTypeFilter($entityType)->addSetInfo()->getData();
            $entityTypeAttributeCodes    = array();
            $attributeSetsAttributeCodes = array();
            foreach ($attributesData as $attributeData) {
                $this->_initAttribute($entityType, $attributeData);
                $entityTypeAttributeCodes[] = $attributeData['attribute_code'];
                $attributeSetIds            = array_keys($attributeData['attribute_set_info']);
                unset($attributeData['attribute_set_info']);
                foreach ($attributeSetIds as $attributeSetId) {
                    if (false === isset($attributeSetsAttributeCodes[$attributeSetId])) {
                        $attributeSetsAttributeCodes[$attributeSetId] = array();
                    }
                    $attributeSetsAttributeCodes[$attributeSetId][] = $attributeData['attribute_code'];
                }
            }
            $entityType->setData('attribute_codes', $entityTypeAttributeCodes);
            if (count($attributeSetsAttributeCodes) > 0) {
                foreach ($attributeSetsAttributeCodes as $attributeSetId => $attributeCodes) {
                    if (true === isset($this->_attributeSets[$attributeSetId])) {
                        $this->_attributeSets[$attributeSetId]->setData('attribute_codes', $attributeCodes);
                    }
                }
            }
        }
        Varien_Profiler::stop('EAV: ' . __METHOD__);
        return $this;
    }

    /**
     * Get attribute by code for entity type
     *
     * @param   mixed $entityType
     * @param   mixed $code
     * @return  Mage_Eav_Model_Entity_Attribute_Abstract|false
     */
    public function getAttribute($entityType, $code)
    {
        if ($code instanceof Mage_Eav_Model_Entity_Attribute_Interface) {
            return $code;
        }
        Varien_Profiler::start('EAV: ' . __METHOD__);
        $this->_initAllAttributes();
        $entityType     = $this->getEntityType($entityType);
        $entityTypeCode = $entityType->getEntityTypeCode();
        /**
         * Validate attribute code
         */
        if (true === is_numeric($code)) {
            $attributeCode = $this->_getAttributeReference($code, $entityTypeCode);
            if ($attributeCode) {
                $code = $attributeCode;
            }
        }
        if (false === isset($this->_attributes[$entityTypeCode][$code])) {
            // backward compatibility with attributes which are absent in db but present in xml config for some reason
            // for example type_id attribute in app/code/core/Mage/Sales/etc/config.xml
            $attribute = Mage::getModel($entityType->getAttributeModel())->setAttributeCode($code);
        } else {
            $attribute = $this->_attributes[$entityTypeCode][$code];
            $attribute->setEntityType($entityType);
        }
        Varien_Profiler::stop('EAV: ' . __METHOD__);
        return $attribute;
    }

    /**
     * Get codes of all entity type attributes
     *
     * @param  mixed $entityType
     * @param  Varien_Object $object
     * @return array
     */
    public function getEntityAttributeCodes($entityType, $object = null)
    {
        Varien_Profiler::start('EAV: ' . __METHOD__);
        $this->_initAllAttributes();
        $attributeSetId = 0;
        if (($object instanceof Varien_Object) && $object->getAttributeSetId()) {
            $attributeSetId = $object->getAttributeSetId();
        }
        if ($attributeSetId && isset($this->_attributeSets[$attributeSetId])) {
            $attributes = $this->_attributeSets[$attributeSetId]->getData('attribute_codes');
        } else {
            $entityType = $this->getEntityType($entityType);
            $attributes = $entityType->getAttributeCodes();
        }
        Varien_Profiler::stop('EAV: ' . __METHOD__);
        // it might happen that entity type doesn't have attributes (some custom one) or
        // there are no attributes in attribute set
        return empty($attributes) ? array() : $attributes;
    }

    /**
     * return a list of all attributes used in given attribute sets
     *
     * @return array
     */
    public function getFilterableProductAttributesUsedInSets(array $setIds)
    {
        $this->_initAllAttributes();
        Varien_Profiler::start('EAV: ' . __METHOD__);
        $out = array();
        foreach ($setIds as $setId) {
            if (true === isset($this->_filteredAttributeSetsUsedInProductListing[$setId])) {
                $out = array_merge($out, $this->_filteredAttributeSetsUsedInProductListing[$setId]);
            }
        }
        $out = array_unique($out);
        $return = array();
        foreach ($out as $attributeCode) {
            $return[$attributeCode] = $this->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
        }
        Varien_Profiler::stop('EAV: ' . __METHOD__);
        return $return;
    }

    /**
     * return a list of all attributes used for sort by
     *
     * @return array
     */
    public function getProductAttributesUsedForSortBy()
    {
        $this->_initAllAttributes();
        return $this->_attributesUsedForSortInProductListing;
    }

    /**
     * return a list of all attributes used in product listing
     *
     * @return array
     */
    public function getAttributesUsedInProductListing()
    {
        $this->_initAllAttributes();
        return $this->_attributesUsedInProductListing;
    }

    /**
     * Preload entity type attributes for performance optimization
     *
     * @param   mixed $entityType
     * @param   mixed $attributes
     * @return  Mage_Eav_Model_Config
     */
    public function preloadAttributes($entityType, $attributes)
    {
        // with fixed cached eav attributes this method is obsolete
        return $this;
    }

    /**
     * Get attribute object for colection usage
     *
     * @param   mixed $entityType
     * @param   string $attribute
     * @return  Mage_Eav_Model_Entity_Attribute_Abstract|null
     */
    public function getCollectionAttribute($entityType, $attribute)
    {
        return $this->getAttribute($entityType, $attribute);
    }

    /**
     * Prepare attributes for usage in EAV collection
     *
     * @param   mixed $entityType
     * @param   array $attributes
     * @return  Mage_Eav_Model_Config
     */
    public function loadCollectionAttributes($entityType, $attributes)
    {
        // with cached eav attributes we don't need this method
        return $this;
    }

    /**
     * Create attribute from attribute data array
     *
     * @param string $entityType
     * @param array $attributeData
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    protected function _createAttribute($entityType, $attributeData)
    {
        $entityType     = $this->getEntityType($entityType);
        $entityTypeCode = $entityType->getEntityTypeCode();

        $attributeKey = $this->_getAttributeKey($entityTypeCode, $attributeData['attribute_code']);
        $attribute = $this->_load($attributeKey);
        if ($attribute) {
            $existsFullAttribute = $attribute->hasIsRequired();
            $fullAttributeData   = array_key_exists('is_required', $attributeData);

            if ($existsFullAttribute || (!$existsFullAttribute && !$fullAttributeData)) {
                return $attribute;
            }
        }

        if (!empty($attributeData['attribute_model'])) {
            $model = $attributeData['attribute_model'];
        } else {
            $model = $entityType->getAttributeModel();
        }
        $attribute = Mage::getModel($model)->setData($attributeData);
        $this->_addAttributeReference(
            $attributeData['attribute_id'],
            $attributeData['attribute_code'],
            $entityTypeCode
        );
        $attributeKey = $this->_getAttributeKey($entityTypeCode, $attributeData['attribute_code']);
        $this->_save($attribute, $attributeKey);

        return $attribute;
    }

    /**
     * Validate attribute data from import
     *
     * @param array $attributeData
     * @return bool
     */
    protected function _validateAttributeData($attributeData = null)
    {
        if (!is_array($attributeData)) {
            return false;
        }
        $requiredKeys = array(
            'attribute_id',
            'attribute_code',
            'entity_type_id',
            'attribute_model'
        );
        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $attributeData)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Import attributes data from external source
     *
     * @param string|Mage_Eav_Model_Entity_Type $entityType
     * @param array $attributes
     * @return Mage_Eav_Model_Config
     */
    public function importAttributesData($entityType, array $attributes)
    {
        // with cached eav attributes we don't need this method
        return $this;
    }
}
