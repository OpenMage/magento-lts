<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Mage_Eav_Model_Config
 *
 * @category   Mage
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Config
{
    /**
     * Cache key for storing:
     * - EAV entities
     * - EAV attributes
     * - EAV attribute extension table data
     */
    public const EAV_DATA_CACHE = 'EAV_DATA_CACHE';

    public const ENTITIES_CACHE_ID = 'EAV_ENTITY_TYPES';

    public const NUMERIC_ATTRIBUTE_COLUMNS = [
        // from table eav_attribute
        'attribute_id',
        'entity_type_id',
        'is_required',
        'is_user_defined',
        'is_unique',
    ];

    protected $_storeInitialized = [];

    /**
     * @var Mage_Eav_Model_Entity_Type[]|null
     */
    protected $_entityTypes;

    /**
     * @var Mage_Eav_Model_Entity_Type[]|null
     */
    protected $_entityTypeByCode;

    /**
     * @var Mage_Eav_Model_Entity_Attribute_Abstract[][][]|mixed[][][]|null
     */
    protected $_entityTypeAttributes;

    /**
     * @var Mage_Eav_Model_Entity_Attribute_Abstract[][][]|null
     */
    protected $_entityTypeAttributeIdByCode;

    /**
     * Attribute set relation information. Structure:
     * <br/>
     * [
     *  int attribute_id => [
     *      int set_id => [
     *          int group_id,
     *          int group_sort,
     *          int sort,
     *      ]
     * ]
     * @var mixed[][][][]|null
     */
    protected $_attributeSetInfo;

    /**
     * Special local cache for default attributes to avoid re-hydrating them
     * @var Mage_Eav_Model_Entity_Attribute_Abstract[][][]|false[][][]
     */
    protected $_defaultAttributes = [];

    /**
     * Cache flag
     *
     * @var bool
     */
    protected $_isCacheEnabled = null;

    /**
     * @var int|false|null
     */
    protected $_currentStoreId;


    /**
     * Reset object state
     *
     * @return $this
     * @deprecated
     */
    public function clear()
    {
        $this->_storeInitialized = [];
        $this->_entityTypes = null;
        $this->_entityTypeByCode = null;
        $this->_entityTypeAttributes = null;
        $this->_entityTypeAttributeIdByCode = null;
        $this->_attributeSetInfo = null;
        $this->_defaultAttributes = [];

        Mage::app()->cleanCache([self::ENTITIES_CACHE_ID]);

        return $this;
    }

    /**
     * @param int|false|null $storeId
     * @return void
     */
    public function setCurrentStoreId($storeId)
    {
        $this->_currentStoreId = $storeId;
    }

    /**
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _storeId()
    {
        if (isset($this->_currentStoreId) && $this->_currentStoreId !== false) {
            return $this->_currentStoreId;
        }
        return Mage::app()->getStore()->getId();
    }

    /**
     * @throws Exception
     */
    protected function _initializeStore($storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->_storeId();
        } else {
            // ensure store id is consistent
            $storeId = (int) $storeId;
        }
        if (isset($this->_storeInitialized[$storeId]) && $this->_storeInitialized[$storeId]) {
            return;
        }

        Varien_Profiler::start('EAV: ' . __METHOD__);

        if ($this->_isCacheEnabled() && $this->_loadFromCache($storeId)) {
            $this->_storeInitialized[$storeId] = true;
            return;
        }

        if (empty($this->_entityTypes)) {
            $this->_loadEntityTypes();
            $this->_loadAttributeSetInfo();
        }

        // load each entity attributes for given storeId
        if (empty($this->_entityTypeAttributes[$storeId])) {
            $this->_entityTypeAttributes[$storeId] = [];
            foreach ($this->_entityTypes as $entityType) {
                $this->_loadEntityAttributes($entityType, $storeId);
            }
        }

        if ($this->_isCacheEnabled()) {
            $this->_saveToCache($storeId);
        }

        $this->_storeInitialized[$storeId] = true;

        Varien_Profiler::stop('EAV: ' . __METHOD__);
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function _loadEntityTypes()
    {
        // load entity types
        $this->_entityTypes = [];
        $this->_entityTypeByCode = [];
        $entityTypeCollection = Mage::getResourceModel('eav/entity_type_collection');

        /** @var Mage_Eav_Model_Entity_Type $entityType */
        foreach ($entityTypeCollection as $entityType) {
            // Ensure eav entity type model class is defined, otherwise skip processing it.
            // This check prevents leftover eav_entity_type entries from disabled/removed modules creating errors and
            // is necessary because the entire EAV model is now loaded eagerly for performance optimization.
            $entityModelClass = $entityType['entity_model'];
            $fqEntityModelClass = Mage::getConfig()->getModelClassName($entityModelClass);
            if (!class_exists($fqEntityModelClass)) {
                if (Mage::getIsDeveloperMode()) {
                    throw new Exception('Failed loading of eav entity type because it does not exist: ' . $entityModelClass);
                } else {
                    Mage::log('Skipped loading of eav entity type because it does not exist: ' . $entityModelClass);
                }
                continue;
            }

            $this->_entityTypes[$entityType->getId()] = $entityType;
            $this->_entityTypeByCode[$entityType->getEntityTypeCode()] = $entityType;
        }
    }

    /**
     * @param Mage_Eav_Model_Entity_Type $entityType
     * @param int $storeId
     * @return void
     * @throws Exception
     */
    protected function _loadEntityAttributes($entityType, $storeId)
    {
        // preload attributes in array form to avoid instantiating
        // models for every attribute even if it is never accessed
        $collection = $entityType->newAttributeCollection()
            ->addStoreLabel($storeId);

        // if collection supports per-website values, set website id
        if ($collection instanceof Mage_Eav_Model_Resource_Attribute_Collection) {
            $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
            $collection->setWebsite($websiteId);
        }

        $entityAttributes = $collection->getData();

        $this->_entityTypeAttributes[$storeId][$entityType->getId()] = [];
        $attributeCodes = [];

        /** @var Mage_Eav_Model_Entity_Attribute_Abstract $entityAttributeData */
        foreach ($entityAttributes as $entityAttributeData) {
            $attributeId = $entityAttributeData['attribute_id'];
            $attributeCode = $entityAttributeData['attribute_code'];

            // workaround for newAttributeCollection()->getData() returning all columns as string
            foreach (self::NUMERIC_ATTRIBUTE_COLUMNS as $key) {
                if (!isset($entityAttributeData[$key])) {
                    continue;
                }
                $entityAttributeData[$key] = (int) $entityAttributeData[$key];
            }

            $this->_entityTypeAttributes[$storeId][$entityType->getId()][$attributeId] = $entityAttributeData;
            $this->_entityTypeAttributeIdByCode[$storeId][$entityType->getId()][$attributeCode] = $attributeId;
            $attributeCodes[] = $attributeCode;
        }

        $entityType->setAttributeCodes($attributeCodes);
    }

    /**
     * @param $storeId
     * @return void
     * @throws Exception
     */
    protected function _loadAttributeSetInfo()
    {
        $this->_attributeSetInfo = Mage::getResourceModel('eav/entity_attribute_set')->getSetInfo();
    }

    /**
     * @param int $storeId
     * @return bool true if successfully loaded from cache, false otherwise
     * @throws Exception
     */
    protected function _loadFromCache($storeId)
    {
        Varien_Profiler::start('EAV: ' . __METHOD__);

        $cacheData = Mage::app()->loadCache(self::ENTITIES_CACHE_ID . '_' . $storeId);
        if ($cacheData === false) {
            Varien_Profiler::stop('EAV: ' . __METHOD__);
            return false;
        }
        $cacheData = unserialize($cacheData);

        $this->_entityTypes = [];
        $this->_entityTypeByCode = [];
        /** @var array $entityTypeData */
        foreach ($cacheData['_entityTypes'] as $entityTypeData) {
            $entityType = Mage::getModel('eav/entity_type')
                ->setData($entityTypeData);
            $this->_entityTypes[$entityType->getId()] = $entityType;
            $this->_entityTypeByCode[$entityType->getEntityTypeCode()] = $entityType;
        }

        $this->_entityTypeAttributes[$storeId] = [];
        /** @var int $entityTypeId */
        /** @var array $entityTypeAttributes */
        foreach ($cacheData['_entityTypeAttributes'] as $entityTypeId => $entityTypeAttributes) {
            /** @var array $attributeData */
            foreach ($entityTypeAttributes as $attributeData) {
                $attributeId = $attributeData['attribute_id'];
                $attributeCode = $attributeData['attribute_code'];
                $this->_entityTypeAttributes[$storeId][$entityTypeId][$attributeId] = $attributeData;
                $this->_entityTypeAttributeIdByCode[$storeId][$entityTypeId][$attributeCode] = $attributeId;
            }
        }

        $this->_attributeSetInfo = $cacheData['_attributeSetInfo'];

        Varien_Profiler::stop('EAV: ' . __METHOD__);
        return true;
    }

    protected function _saveToCache($storeId)
    {
        $cacheData = [
            '_entityTypes' => [],
            '_entityTypeAttributes' => [],
            '_attributeSetInfo' => $this->_attributeSetInfo,
        ];

        foreach ($this->_entityTypes as $entityType) {
            $cacheData['_entityTypes'][$entityType->getId()] = $entityType->getData();
        }

        foreach ($this->_entityTypeAttributes[$storeId] as $entityTypeId => $attributes) {
            $cacheData['_entityTypeAttributes'][$entityTypeId] = [];
            foreach ($attributes as $attribute) {
                $attributeId = is_array($attribute) ? $attribute['attribute_id'] : $attribute->getId();
                $attributeData = is_array($attribute) ? $attribute : $attribute->getData();
                $cacheData['_entityTypeAttributes'][$entityTypeId][$attributeId] = $attributeData;
            }
        }

        Mage::app()->saveCache(
            serialize($cacheData),
            self::ENTITIES_CACHE_ID . '_' . $storeId,
            ['eav', self::ENTITIES_CACHE_ID, Mage_Eav_Model_Entity_Attribute::CACHE_TAG],
        );
    }

    /**
     * Check EAV cache availability
     *
     * @return bool
     */
    protected function _isCacheEnabled()
    {
        if ($this->_isCacheEnabled === null) {
            $this->_isCacheEnabled = Mage::app()->useCache('eav');
        }
        return $this->_isCacheEnabled;
    }

    /**
     * Create model instance from array
     *
     * @param array $attributeData
     * @return Mage_Eav_Model_Entity_Attribute_Abstract|false
     * @throws Mage_Core_Exception
     */
    protected function _hydrateAttribute($attributeData)
    {
        $entityType = $this->getEntityType($attributeData['entity_type_id']);
        if (!empty($attributeData['attribute_model'])) {
            $model = $attributeData['attribute_model'];
        } else {
            $model = $entityType->getAttributeModel();
        }
        /** @var Mage_Eav_Model_Entity_Attribute_Abstract|false $attribute */
        $attribute = Mage::getModel($model);
        if ($attribute) {
            $attribute->setData($attributeData);

            $entity = $entityType->getEntity();
            if (method_exists($entity, 'getDefaultAttributes')
                && in_array($attribute->getAttributeCode(), $entity->getDefaultAttributes())
            ) {
                $attribute
                    ->setBackendType(Mage_Eav_Model_Entity_Attribute_Abstract::TYPE_STATIC)
                    ->setIsGlobal(1);
            }
            $attribute
                ->setEntityType($entityType)
                ->setEntityTypeId($entityType->getId());
        }

        return $attribute;
    }

    /**
     * Get entity type object by entity type code/identifier
     *
     * @param mixed $code
     * @param string|null $field
     * @return Mage_Eav_Model_Entity_Type
     * @throws Mage_Core_Exception
     * @throws Exception
     */
    public function getEntityType($code, $field = null)
    {
        if ($code instanceof Mage_Eav_Model_Entity_Type) {
            return $code;
        }

        // initialize entity type cache
        if (!isset($this->_entityTypes)) {
            $this->_initializeStore();
        }

        // lookup by id
        if (empty($field) && is_numeric($code)) {
            if (isset($this->_entityTypes[$code])) {
                return $this->_entityTypes[$code];
            } else {
                Mage::throwException('Invalid entity type: ' . $code);
            }
        }

        // lookup by code
        if (empty($field) || $field == 'entity_type_code') {
            if (isset($this->_entityTypeByCode[$code])) {
                return $this->_entityTypeByCode[$code];
            } else {
                Mage::throwException('Invalid entity type: ' . $code);
            }
        }

        // lookup by other field
        foreach ($this->_entityTypes as $entityType) {
            if ($entityType->getData($field) == $code) {
                return $entityType;
            }
        }

        Mage::throwException('Failed to find entity eav/entity_type for ' . $field . '=' . $code);
    }

    /**
     * Default attributes are loaded only on getAttribute(...) call to avoid infinite loading loop between
     * Entity_Type->getEntity() which itself requires this class and re-triggers loading.
     *
     * @param Mage_Eav_Model_Entity_Type $entityType
     * @param int $storeId
     * @param string $attributeCode
     * @return Mage_Eav_Model_Entity_Attribute_Abstract|false
     */
    protected function _getDefaultAttributeIfExists($entityType, $attributeCode, $storeId)
    {
        if (isset($this->_defaultAttributes[$storeId][$entityType->getId()][$attributeCode])) {
            return $this->_defaultAttributes[$storeId][$entityType->getId()][$attributeCode];
        }

        $entity = $entityType->getEntity();
        if (method_exists($entity, 'getDefaultAttributes')
            && in_array($attributeCode, $entity->getDefaultAttributes())
        ) {
            $attributeData = [
                'entity_type_id' => $entityType->getId(),
                'attribute_code' => $attributeCode,
            ];
            $attribute = $this->_hydrateAttribute($attributeData);
            $this->_defaultAttributes[$storeId][$entityType->getId()][$attributeCode] = $attribute;
            return $attribute;
        }

        // cache a miss as well
        $this->_defaultAttributes[$storeId][$entityType->getId()][$attributeCode] = false;
        return false;
    }

    /**
     * Get attribute by code for entity type
     *
     * @param mixed $entityType
     * @param mixed $code
     * @param int|null $storeId
     * @return Mage_Eav_Model_Entity_Attribute_Abstract|false
     * @throws Mage_Core_Exception
     * @throws Exception
     */
    public function getAttribute($entityType, $code, $storeId = null)
    {
        if ($code instanceof Mage_Eav_Model_Entity_Attribute_Interface) {
            return $code;
        }

        $storeId = $storeId ?? $this->_storeId();
        $this->_initializeStore($storeId);
        $entityType = $this->getEntityType($entityType);

        // lookup id by code
        if (!is_numeric($code) && isset($this->_entityTypeAttributeIdByCode[$storeId][$entityType->getId()][$code])) {
            $code = $this->_entityTypeAttributeIdByCode[$storeId][$entityType->getId()][$code];
        }

        // get model
        $attribute = null;
        if (isset($this->_entityTypeAttributes[$storeId][$entityType->getId()][$code])) {
            $attributeData = $this->_entityTypeAttributes[$storeId][$entityType->getId()][$code];
            if (is_array($attributeData)) {
                $attribute = $this->_hydrateAttribute($attributeData);
                $this->_entityTypeAttributes[$storeId][$entityType->getId()][$attribute->getId()] = $attribute;
            } else {
                $attribute = $attributeData;
            }
        } else {
            $attribute = $this->_getDefaultAttributeIfExists($entityType, $code, $storeId);
        }

        // return an empty model to avoid breaking compatibility
        if (!$attribute) {
            $attribute = $this->_hydrateAttribute(['entity_type_id' => $entityType->getId()]);
        }

        return $attribute;
    }

    /**
     * @param mixed $entityType
     * @return Mage_Eav_Model_Entity_Attribute_Abstract[]
     * @throws Mage_Core_Exception
     */
    public function getAttributes($entityType)
    {
        Varien_Profiler::start('EAV: ' . __METHOD__);

        $entityType = $this->getEntityType($entityType);
        $attributes = [];
        $storeId = $this->_storeId();
        // need to access attributes to ensure they are hydrated and initialized
        foreach (array_keys($this->_entityTypeAttributes[$storeId][$entityType->getId()]) as $attributeId) {
            $attributes[] = $this->getAttribute($entityType, $attributeId, $storeId);
        }

        Varien_Profiler::stop('EAV: ' . __METHOD__);

        return $attributes;
    }

    /**
     * Get codes of all entity type attributes
     *
     * @param Mage_Eav_Model_Entity_Type $entityType
     * @param Varien_Object $object
     * @return array
     * @throws Mage_Core_Exception
     * @throws Exception
     */
    public function getEntityAttributeCodes($entityType, $object = null)
    {
        $entityType = $this->getEntityType($entityType);
        $attributeSetId = 0;
        if (($object instanceof Varien_Object) && $object->getAttributeSetId()) {
            $attributeSetId = $object->getAttributeSetId();
        }

        // Technically store id is irrelevant for attribute sets, they are the same in all store scopes.
        // Use current store id when not specified to avoid loading two store-scope attribute data sets from cache
        $storeId = $this->_storeId();
        if (($object instanceof Varien_Object) && $object->getStoreId()) {
            $storeId = $object->getStoreId();
        }
        $this->_initializeStore($storeId);

        if ($attributeSetId) {
            $attributeIds = $this->getAttributeSetAttributeIds($attributeSetId);
            $attributeCodes = [];
            foreach ($attributeIds as $attributeId) {
                $attribute = $this->getAttribute($entityType, $attributeId, $storeId);
                // need to verify attribute actually exists to avoid problems
                // with deleted attributes that left behind some remnants
                if ($attribute) {
                    $attributeCodes[] = $attribute->getAttributeCode();
                }
            }
            return $attributeCodes;
        } else {
            return isset($this->_entityTypeAttributeIdByCode[$storeId][$entityType->getId()])
                ? array_keys($this->_entityTypeAttributeIdByCode[$storeId][$entityType->getId()])
                : [];
        }
    }

    /**
     * @param int|int[] $attributeSetId
     * @return int[]
     */
    public function getAttributeSetAttributeIds($attributeSetId)
    {
        if (!is_array($attributeSetId)) {
            $attributeSetId = [$attributeSetId];
        }

        $attributes = [];

        foreach ($attributeSetId as $setId) {
            foreach ($this->_attributeSetInfo as $attributeId => $sets) {
                if (isset($sets[$setId])) {
                    $attributes[$attributeId] = true;
                }
            }
        }

        return array_keys($attributes);
    }

    /**
     * Return first attribute sorting information found for a given list of attribute sets
     * @param int $attributeId
     * @param int|int[] $attributeSetIds
     * @return false|array
     */
    public function getAttributeSetGroupInfo($attributeId, $attributeSetIds)
    {
        if (!is_array($attributeSetIds)) {
            $attributeSetIds = [$attributeSetIds];
        }

        foreach ($attributeSetIds as $attributeSetId) {
            if (isset($this->_attributeSetInfo[$attributeId][$attributeSetId])) {
                return $this->_attributeSetInfo[$attributeId][$attributeSetId];
            }
        }

        return false;
    }

    /**
     * @param mixed $entityType
     * @param string $attribute
     * @return  Mage_Eav_Model_Entity_Attribute_Abstract|null
     * @throws Mage_Core_Exception
     * @deprecated Equivalent to getAttribute(...), use getAttribute(...) instead
     * Get attribute object for collection usage
     *
     */
    public function getCollectionAttribute($entityType, $attribute)
    {
        return $this->getAttribute($entityType, $attribute);
    }

    /**
     * @param mixed $entityType
     * @param array $attributes
     * @return  Mage_Eav_Model_Config
     * @deprecated No longer required to preload only collection attributes explicitly
     * Prepare attributes for usage in EAV collection
     *
     */
    public function loadCollectionAttributes($entityType, $attributes)
    {
        return $this;
    }

    /**
     * @param string|Mage_Eav_Model_Entity_Type $entityType
     * @return $this
     * @deprecated No longer required. All attribute data is cached on-access.
     */
    public function importAttributesData($entityType, array $attributes)
    {
        return $this;
    }
}
