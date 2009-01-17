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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Eav_Model_Config
{
    const ENTITIES_CACHE_ID     = 'EAV_ENTITY_TYPES';
    const ATTRIBUTES_CACHE_ID   = 'EAV_ENTITY_ATTRIBUTES';

    /**
     * Entity types data
     *
     * @var array
     */
    protected $_entityData;

    /**
     * Attributes data
     *
     * @var unknown_type
     */
    protected $_attributeData;

    /**
     * Information about preloaded attributes
     *
     * @var array
     */
    protected $_preloadedAttributes = array();

    /**
     * Information about entity types with initialized attributes
     *
     * @var array
     */
    protected $_initializedAttributes = array();

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
     * @var unknown_type
     */
    protected $_isCacheEnabled = null;

    /**
     * Reset object state
     *
     * @return Mage_Eav_Model_Config
     */
    public function clear()
    {
        $this->_entityData  = null;
        $this->_attributeData = null;
        $this->_objects     = null;
        $this->_references  = null;
        $this->_preloadedAttributes = array();
        $this->_initializedAttributes = array();
        return $this;
    }

    /**
     * Get object by idetifier
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

    protected function _getEntityTypeReference($id)
    {
        return isset($this->_references['entity'][$id]) ? $this->_references['entity'][$id] : null;
    }

    protected function _addAttributeReference($id, $code, $entityTypeCode)
    {
        $this->_references['attribute'][$entityTypeCode][$id] = $code;
        return $this;
    }

    protected function _getAttributeReference($id, $entityTypeCode)
    {
        if (isset($this->_references['attribute'][$entityTypeCode][$id])) {
            return $this->_references['attribute'][$entityTypeCode][$id];
        }
        return null;
    }

    protected function _getEntityKey($code)
    {
        return 'ENTITY/'.$code;
    }

    protected function _getAttributeKey($entityTypeCode, $attributeCode)
    {
        return 'ATTRIBUTE/'. $entityTypeCode .'/' . $attributeCode;
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
     * Initialize all entity types data
     *
     * @return Mage_Eav_Model_Config
     */
    protected function _initEntityTypes()
    {
        if (is_array($this->_entityData)) {
            return $this;
        }
        Varien_Profiler::start('EAV: '.__METHOD__);

        /**
         * try load information about entity types from cache
         */
        if ($this->_isCacheEnabled()
            && ($cache = Mage::app()->loadCache(self::ENTITIES_CACHE_ID))) {

            $this->_entityData = unserialize($cache);
            foreach ($this->_entityData as $typeCode => $data) {
                $typeId = $data['entity_type_id'];
                $this->_addEntityTypeReference($typeId, $typeCode);
            }
            Varien_Profiler::stop('EAV: '.__METHOD__);
            return $this;
        }

        $entityTypesData = Mage::getModel('eav/entity_type')->getCollection()->getData();
        $types = array();

        /**
         * prepare entity type data
         */
        foreach ($entityTypesData as $typeData) {
            if (!isset($typeData['attribute_model'])) {
                $typeData['attribute_model'] = 'eav/entity_attribute';
            }

            $typeCode   = $typeData['entity_type_code'];
            $typeId     = $typeData['entity_type_id'];

            $this->_addEntityTypeReference($typeId, $typeCode);
            $types[$typeCode] = $typeData;
        }

        $this->_entityData = $types;

        if ($this->_isCacheEnabled()) {
            Mage::app()->saveCache(serialize($this->_entityData), self::ENTITIES_CACHE_ID,
                array('eav', Mage_Eav_Model_Entity_Attribute::CACHE_TAG)
            );
        }
        Varien_Profiler::stop('EAV: '.__METHOD__);
        return $this;
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
        Varien_Profiler::start('EAV: '.__METHOD__);
        $this->_initEntityTypes();

        if (is_numeric($code)) {
            $code = $this->_getEntityTypeReference($code);
            if ($code === null) {
                Mage::throwException(Mage::helper('eav')->__('Invalid entity_type specified: %s', $code));
            }
        }

        $entityKey = $this->_getEntityKey($code);
        if ($entityType = $this->_load($entityKey)) {
            Varien_Profiler::stop('EAV: '.__METHOD__);
            return $entityType;
        }


        $entityType = Mage::getModel('eav/entity_type');
        if (isset($this->_entityData[$code])) {
            $entityType->setData($this->_entityData[$code]);
        }
        else {
            $entityType->loadByCode($code);
            if (!$entityType->getId()) {
                Mage::throwException(Mage::helper('eav')->__('Invalid entity_type specified: %s', $code));
            }
        }
        $this->_save($entityType, $entityKey);

        Varien_Profiler::stop('EAV: '.__METHOD__);
        return $entityType;
    }

    /**
     * Initialize all attributes for entity type
     *
     * @param   string $entityType
     * @return  Mage_Eav_Model_Config
     */
    protected function _initAttributes($entityType)
    {
        $entityType     = $this->getEntityType($entityType);
        $entityTypeCode = $entityType->getEntityTypeCode();

        if (isset($this->_initializedAttributes[$entityTypeCode])) {
            return $this;
        }
        Varien_Profiler::start('EAV: '.__METHOD__);

        $attributesInfo = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($entityType->getId())
            ->addSetInfo()
            ->getData();

        $attributesData = array();
        $codes = array();
        foreach ($attributesInfo as $attribute) {
            if (!isset($attribute['attribute_model'])) {
                $attribute['attribute_model'] = $entityType->getAttributeModel();
            }

            $attributeCode  = $attribute['attribute_code'];
            $attributeId    = $attribute['attribute_id'];

            $this->_addAttributeReference($attributeId, $attributeCode, $entityTypeCode);
            $attributesData[$attributeCode] = $attribute;
            $codes[] = $attributeCode;
        }

        $entityType->setAttributeCodes($codes);
        $this->_attributeData[$entityTypeCode] = $attributesData;
        $this->_initializedAttributes[$entityTypeCode] = true;

        Varien_Profiler::stop('EAV: '.__METHOD__);
        return $this;
    }

    /**
     * Get attribute by code for entity type
     *
     * @param   mixed $entityType
     * @param   mixed $code
     * @return  Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getAttribute($entityType, $code)
    {
        if ($code instanceof Mage_Eav_Model_Entity_Attribute_Interface) {
            return $code;
        }

        Varien_Profiler::start('EAV: '.__METHOD__);

        $entityTypeCode = $this->getEntityType($entityType)->getEntityTypeCode();
        $entityType     = $this->getEntityType($entityType);

        /**
         * Validate attribute code
         */
        if (is_numeric($code)) {
            $code = $this->_getAttributeReference($code, $entityTypeCode);
            if (!$code) {
                return false;
            }
        }
        $attributeKey = $this->_getAttributeKey($entityTypeCode, $code);

        /**
         * Try use loaded attribute
         */
        if ($attribute = $this->_load($attributeKey)) {
            Varien_Profiler::stop('EAV: '.__METHOD__);
            return $attribute;
        }

        if (!isset($this->_preloadedAttributes[$entityTypeCode])
            || !in_array($code, $this->_preloadedAttributes[$entityTypeCode])) {
            $this->_initAttributes($entityType);
        }

        $attribute = false;
        if (isset($this->_attributeData[$entityTypeCode][$code])) {
            $data = $this->_attributeData[$entityTypeCode][$code];
            $attribute = Mage::getModel($data['attribute_model'], $data);
        }
        else {
            $attribute = Mage::getModel($entityType->getAttributeModel())->loadByCode($entityType, $code);
        }

        if ($attribute) {
            $attribute->setEntityType($entityType);
            $this->_save($attribute, $attributeKey);
        }
        Varien_Profiler::stop('EAV: '.__METHOD__);
        return $attribute;
    }

    /**
     * Get codes of all entity type attributes
     *
     * @param   string $entityType
     * @return  array
     */
    public function getEntityAttributeCodes($entityType, $object=null)
    {
        if (($object instanceof Varien_Object) && $object->getAttributeSetId()) {
            $attributesInfo = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setEntityTypeFilter($this->getEntityType($entityType)->getId())
                ->setAttributeSetFilter($object->getAttributeSetId())
                ->getData();
            $attributes = array();
            foreach ($attributesInfo as $attributeData) {
                $attributes[] = $attributeData['attribute_code'];
            }

            $this->preloadAttributes($entityType, $attributes);
            return $attributes;
        }
        else {
            $this->_initAttributes($entityType);
        }
        return $this->getEntityType($entityType)->getAttributeCodes();
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
        if (is_string($attributes)) {
            $attributes = array($attributes);
        }

        $entityType     = $this->getEntityType($entityType);
        $entityTypeCode = $entityType->getEntityTypeCode();

        if (!isset($this->_preloadedAttributes[$entityTypeCode])) {
            $this->_preloadedAttributes[$entityTypeCode] = $attributes;
        }
        else {
            $attributes = array_diff($attributes, $this->_preloadedAttributes[$entityTypeCode]);
            $this->_preloadedAttributes[$entityTypeCode] = array_merge($this->_preloadedAttributes[$entityTypeCode], $attributes);
        }

        if (empty($attributes)) {
            return $this;
        }
        Varien_Profiler::start('EAV: '.__METHOD__);

        $attributesInfo = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($entityType->getId())
            ->setCodeFilter($attributes)
            ->addSetInfo()
            ->getData();

        if (!$attributesInfo) {
            return $this;
        }

        $attributesData = array();
        $codes = array();

        foreach ($attributesInfo as $attribute) {
            if (!isset($attribute['attribute_model'])) {
                $attribute['attribute_model'] = $entityType->getAttributeModel();
            }

            $attributeCode  = $attribute['attribute_code'];
            $attributeId    = $attribute['attribute_id'];

            $this->_addAttributeReference($attributeId, $attributeCode, $entityTypeCode);
            $attributesData[$attributeCode] = $attribute;
            $codes[] = $attributeCode;
        }

        $this->_attributeData[$entityTypeCode] = $attributesData;

        Varien_Profiler::stop('EAV: '.__METHOD__);
        return $this;
   }
}