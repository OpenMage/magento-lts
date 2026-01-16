<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * Entity/Attribute/Model - entity abstract
 *
 * @package    Mage_Eav
 */
abstract class Mage_Eav_Model_Entity_Abstract extends Mage_Core_Model_Resource_Abstract implements Mage_Eav_Model_Entity_Interface
{
    /**
     * Read connection
     *
     * @var string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract
     */
    protected $_read;

    /**
     * Write connection
     *
     * @var string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract
     */
    protected $_write;

    /**
     * Entity type configuration
     *
     * @var Mage_Eav_Model_Entity_Type
     */
    protected $_type;

    /**
     * Attributes array by attribute id
     *
     * @var array
     */
    protected $_attributesById              = [];

    /**
     * Attributes array by attribute name
     *
     * @var array
     */
    protected $_attributesByCode            = [];

    /**
     * 2-dimensional array by table name and attribute name
     *
     * @var array
     */
    protected $_attributesByTable           = [];

    /**
     * Attributes that are static fields in entity table
     *
     * @var array
     */
    protected $_staticAttributes = [];

    /**
     * Default Attributes that are static
     *
     * @var array
     */
    protected static $_defaultAttributes    = [];

    /**
     * Entity table
     *
     * @var string
     */
    protected $_entityTable;

    /**
     * Describe data for tables
     *
     * @var array
     */
    protected $_describeTable               = [];

    /**
     * Entity table identification field name
     *
     * @var string
     */
    protected $_entityIdField;

    /**
     * Entity values table identification field name
     *
     * @var string
     */
    protected $_valueEntityIdField;

    /**
     * Entity value table prefix
     *
     * @var string
     */
    protected $_valueTablePrefix;

    /* Entity table string
     *
     * @var string
     */
    protected $_entityTablePrefix;

    /**
     * Partial load flag
     *
     * @var bool
     */
    protected $_isPartialLoad = false;

    /**
     * Partial save flag
     *
     * @var bool
     */
    protected $_isPartialSave = false;

    /**
     * Attribute set id which used for get sorted attributes
     *
     * @var int
     */
    protected $_sortingSetId = null;

    /**
     * Entity attribute values per backend table to delete
     *
     * @var array
     */
    protected $_attributeValuesToDelete = [];

    /**
     * Entity attribute values per backend table to save
     *
     * @var array
     */
    protected $_attributeValuesToSave   = [];

    /**
     * Array of describe attribute backend tables
     * The table name as key
     *
     * @var array
     */
    protected static $_attributeBackendTables   = [];

    /**
     * Set connections for entity operations
     *
     * @param  string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract      $read
     * @param  null|string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract $write
     * @return $this
     */
    public function setConnection($read, $write = null)
    {
        $this->_read  = $read;
        $this->_write = $write ? $write : $read;

        return $this;
    }

    protected function _construct() {}

    /**
     * Retrieve connection for read data
     *
     * @return false|Varien_Db_Adapter_Interface
     */
    protected function _getReadAdapter()
    {
        if (is_string($this->_read)) {
            $this->_read = Mage::getSingleton('core/resource')->getConnection($this->_read);
        }

        return $this->_read;
    }

    /**
     * Retrieve connection for write data
     *
     * @return false|Varien_Db_Adapter_Interface
     */
    protected function _getWriteAdapter()
    {
        if (is_string($this->_write)) {
            $this->_write = Mage::getSingleton('core/resource')->getConnection($this->_write);
        }

        return $this->_write;
    }

    /**
     * Retrieve read DB connection
     *
     * @return false|Varien_Db_Adapter_Interface
     */
    public function getReadConnection()
    {
        return $this->_getReadAdapter();
    }

    /**
     * Retrieve write DB connection
     *
     * @return Varien_Db_Adapter_Interface
     */
    public function getWriteConnection()
    {
        return $this->_getWriteAdapter();
    }

    /**
     * For compatibility with Mage_Core_Model_Abstract
     *
     * @return string
     * @throws Mage_Core_Exception
     */
    public function getIdFieldName()
    {
        return $this->getEntityIdField();
    }

    /**
     * Retrieve table name
     *
     * @param  array|string $alias
     * @return string
     */
    public function getTable($alias)
    {
        return Mage::getSingleton('core/resource')->getTableName($alias);
    }

    /**
     * Set configuration for the entity
     *
     * Accepts config node or name of entity type
     *
     * @param  Mage_Eav_Model_Entity_Type|string $type
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function setType($type)
    {
        $this->_type = Mage::getSingleton('eav/config')->getEntityType($type);
        $this->_afterSetConfig();
        return $this;
    }

    /**
     * Retrieve current entity config
     *
     * @return Mage_Eav_Model_Entity_Type
     * @throws Mage_Core_Exception
     */
    public function getEntityType()
    {
        if (is_null($this->_type)) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Entity is not initialized'));
        }

        return $this->_type;
    }

    /**
     * Get entity type name
     *
     * @return string
     * @throws Mage_Core_Exception
     */
    public function getType()
    {
        return $this->getEntityType()->getEntityTypeCode();
    }

    /**
     * Get entity type id
     *
     * @return int
     * @throws Mage_Core_Exception
     */
    public function getTypeId()
    {
        return (int) $this->getEntityType()->getEntityTypeId();
    }

    /**
     * Unset attributes
     *
     * If NULL or not supplied removes configuration of all attributes
     * If string - removes only one, if array - all specified
     *
     * @param  null|array|string   $attributes
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function unsetAttributes($attributes = null)
    {
        if ($attributes === null) {
            $this->_attributesByCode    = [];
            $this->_attributesById      = [];
            $this->_attributesByTable   = [];
            return $this;
        }

        if (is_string($attributes)) {
            $attributes = [$attributes];
        }

        if (!is_array($attributes)) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Unknown parameter'));
        }

        foreach ($attributes as $attrCode) {
            if (!isset($this->_attributesByCode[$attrCode])) {
                continue;
            }

            $attr = $this->getAttribute($attrCode);
            unset($this->_attributesById[$attr->getId()]);
            unset($this->_attributesByTable[$attr->getBackend()->getTable()][$attrCode]);
            unset($this->_attributesByCode[$attrCode]);
        }

        return $this;
    }

    /**
     * Retrieve attribute instance by name, id or config node
     *
     * This will add the attribute configuration to entity's attributes cache
     *
     * If attribute is not found false is returned
     *
     * @param  int|Mage_Core_Model_Config_Element|Mage_Eav_Model_Entity_Attribute_Abstract|string $attribute
     * @return false|Mage_Catalog_Model_Resource_Eav_Attribute
     * @throws Mage_Core_Exception
     */
    public function getAttribute($attribute)
    {
        if (is_numeric($attribute)) {
            $attributeId = $attribute;

            if (isset($this->_attributesById[$attributeId])) {
                return $this->_attributesById[$attributeId];
            }

            $attributeInstance = Mage::getSingleton('eav/config')->getAttribute($this->getEntityType(), $attributeId);
            if ($attributeInstance) {
                $attributeCode = $attributeInstance->getAttributeCode();
            }
        } elseif (is_string($attribute)) {
            $attributeCode = $attribute;

            if (isset($this->_attributesByCode[$attributeCode])) {
                return $this->_attributesByCode[$attributeCode];
            }

            $attributeInstance = Mage::getSingleton('eav/config')
                ->getAttribute($this->getEntityType(), $attributeCode);
            if ($attributeInstance && !$attributeInstance->getAttributeCode() && in_array($attribute, $this->getDefaultAttributes())) {
                $attributeInstance
                    ->setAttributeCode($attribute)
                    ->setBackendType(Mage_Eav_Model_Entity_Attribute_Abstract::TYPE_STATIC)
                    ->setIsGlobal(1)
                    ->setEntity($this)
                    ->setEntityType($this->getEntityType())
                    ->setEntityTypeId($this->getEntityType()->getId());
            }
        } elseif ($attribute instanceof Mage_Eav_Model_Entity_Attribute_Abstract) {
            $attributeInstance = $attribute;
            $attributeCode = $attributeInstance->getAttributeCode();
            if (isset($this->_attributesByCode[$attributeCode])) {
                return $this->_attributesByCode[$attributeCode];
            }
        }

        if (empty($attributeInstance)
            || !($attributeInstance instanceof Mage_Eav_Model_Entity_Attribute_Abstract)
            || (!$attributeInstance->getId()
            && !in_array($attributeInstance->getAttributeCode(), $this->getDefaultAttributes()))
        ) {
            return false;
        }

        $attribute = $attributeInstance;

        if (empty($attributeId)) {
            $attributeId = $attribute->getAttributeId();
        }

        if (isset($attributeCode) && !$attribute->getAttributeCode()) {
            $attribute->setAttributeCode($attributeCode);
        }

        if (!$attribute->getAttributeModel()) {
            $attribute->setAttributeModel($this->_getDefaultAttributeModel());
        }

        $this->addAttribute($attribute);

        return $attribute;
    }

    /**
     * Return default static virtual attribute that doesn't exists in EAV attributes
     *
     * @param  string                          $attributeCode
     * @return Mage_Eav_Model_Entity_Attribute
     * @throws Mage_Core_Exception
     */
    protected function _getDefaultAttribute($attributeCode)
    {
        $entityTypeId = $this->getEntityType()->getId();
        if (!isset(self::$_defaultAttributes[$entityTypeId][$attributeCode])) {
            $attribute = Mage::getModel($this->getEntityType()->getAttributeModel())
                ->setAttributeCode($attributeCode)
                ->setBackendType(Mage_Eav_Model_Entity_Attribute_Abstract::TYPE_STATIC)
                ->setIsGlobal(1)
                ->setEntityType($this->getEntityType())
                ->setEntityTypeId($this->getEntityType()->getId());
            self::$_defaultAttributes[$entityTypeId][$attributeCode] = $attribute;
        }

        return self::$_defaultAttributes[$entityTypeId][$attributeCode];
    }

    /**
     * Adding attribute to entity
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function addAttribute(Mage_Eav_Model_Entity_Attribute_Abstract $attribute)
    {
        $attribute->setEntity($this);
        $attributeCode = $attribute->getAttributeCode();

        $this->_attributesByCode[$attributeCode] = $attribute;

        if ($attribute->isStatic()) {
            $this->_staticAttributes[$attributeCode] = $attribute;
        } else {
            $this->_attributesById[$attribute->getId()] = $attribute;
            $this->_attributesByTable[$attribute->getBackendTable()][$attributeCode] = $attribute;
        }

        return $this;
    }

    /**
     * Retrieve partial load flag
     *
     * @param  bool $flag
     * @return bool
     */
    public function isPartialLoad($flag = null)
    {
        $result = $this->_isPartialLoad;
        if ($flag !== null) {
            $this->_isPartialLoad = (bool) $flag;
        }

        return $result;
    }

    /**
     * Retrieve partial save flag
     *
     * @param  bool $flag
     * @return bool
     */
    public function isPartialSave($flag = null)
    {
        $result = $this->_isPartialSave;
        if ($flag !== null) {
            $this->_isPartialSave = (bool) $flag;
        }

        return $result;
    }

    /**
     * Retrieve configuration for all attributes
     *
     * @param  object              $object
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function loadAllAttributes($object = null)
    {
        $attributeCodes = Mage::getSingleton('eav/config')
            ->getEntityAttributeCodes($this->getEntityType(), $object);

        /**
         * Check and init default attributes
         */
        $defaultAttributes = $this->getDefaultAttributes();
        foreach ($defaultAttributes as $attributeCode) {
            $attributeIndex = array_search($attributeCode, $attributeCodes);
            if ($attributeIndex !== false) {
                $this->getAttribute($attributeCodes[$attributeIndex]);
                unset($attributeCodes[$attributeIndex]);
            } else {
                $this->addAttribute($this->_getDefaultAttribute($attributeCode));
            }
        }

        foreach ($attributeCodes as $code) {
            $this->getAttribute($code);
        }

        return $this;
    }

    /**
     * Retrieve sorted attributes
     *
     * @param  int                 $setId
     * @return array
     * @throws Mage_Core_Exception
     */
    public function getSortedAttributes($setId = null)
    {
        $attributes = $this->getAttributesByCode();
        if ($setId === null) {
            $setId = $this->getEntityType()->getDefaultAttributeSetId();
        }

        // initialize set info
        Mage::getSingleton('eav/entity_attribute_set')
            ->addSetInfo($this->getEntityType(), $attributes, $setId);

        foreach ($attributes as $code => $attribute) {
            /** @var Mage_Eav_Model_Entity_Attribute_Abstract $attribute */
            if (!$attribute->isInSet($setId)) {
                unset($attributes[$code]);
            }
        }

        $this->_sortingSetId = $setId;
        uasort($attributes, [$this, 'attributesCompare']);
        return $attributes;
    }

    /**
     * Compare attributes
     *
     * @param  Mage_Eav_Model_Entity_Attribute $attribute1
     * @param  Mage_Eav_Model_Entity_Attribute $attribute2
     * @return int
     */
    public function attributesCompare($attribute1, $attribute2)
    {
        $sortPath      = sprintf('attribute_set_info/%s/sort', $this->_sortingSetId);
        $groupSortPath = sprintf('attribute_set_info/%s/group_sort', $this->_sortingSetId);

        $sort1 =  ($attribute1->getData($groupSortPath) * 1000) + ($attribute1->getData($sortPath) * 0.0001);
        $sort2 =  ($attribute2->getData($groupSortPath) * 1000) + ($attribute2->getData($sortPath) * 0.0001);
        if ($sort1 > $sort2) {
            return 1;
        }

        if ($sort1 < $sort2) {
            return -1;
        }

        return 0;
    }

    /**
     * Check whether the attribute is Applicable to the object
     *
     * @param  Varien_Object                            $object
     * @param  Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return bool
     */
    protected function _isApplicableAttribute($object, $attribute)
    {
        return true;
    }

    /**
     * Walk through the attributes and run method with optional arguments
     *
     * Returns array with results for each attribute
     *
     * if $method is in format "part/method" will run method on specified part
     * for example: $this->walkAttributes('backend/validate');
     *
     * @param  string                                    $partMethod
     * @return array
     * @throws Mage_Eav_Model_Entity_Attribute_Exception
     */
    public function walkAttributes($partMethod, array $args = [])
    {
        $methodArr = explode('/', $partMethod);
        $part = '';
        switch (count($methodArr)) {
            case 1:
                $part   = 'attribute';
                $method = $methodArr[0];
                break;

            case 2:
                $part   = $methodArr[0];
                $method = $methodArr[1];
                break;
        }

        $results = [];
        foreach ($this->getAttributesByCode() as $attrCode => $attribute) {
            if (isset($args[0]) && is_object($args[0]) && !$this->_isApplicableAttribute($args[0], $attribute)) {
                continue;
            }

            switch ($part) {
                case 'attribute':
                    $instance = $attribute;
                    break;

                case 'backend':
                    $instance = $attribute->getBackend();
                    break;

                case 'frontend':
                    $instance = $attribute->getFrontend();
                    break;

                case 'source':
                    $instance = $attribute->getSource();
                    break;
            }

            if (!isset($instance, $method) || !$this->_isCallableAttributeInstance($instance, $method, $args)) {
                continue;
            }

            try {
                $results[$attrCode] = call_user_func_array([$instance, $method], $args);
            } catch (Mage_Eav_Model_Entity_Attribute_Exception $e) {
                throw $e;
            } catch (Exception $exception) {
                $exception = Mage::getModel('eav/entity_attribute_exception', $exception->getMessage());
                $exception->setAttributeCode($attrCode)->setPart($part);
                throw $exception;
            }
        }

        return $results;
    }

    /**
     * Check whether attribute instance (attribute, backend, frontend or source) has method and applicable
     *
     * @param  Mage_Eav_Model_Entity_Attribute_Abstract|Mage_Eav_Model_Entity_Attribute_Backend_Abstract|Mage_Eav_Model_Entity_Attribute_Frontend_Abstract|Mage_Eav_Model_Entity_Attribute_Source_Abstract $instance
     * @param  string                                                                                                                                                                                      $method
     * @param  array                                                                                                                                                                                       $args     array of arguments
     * @return bool
     */
    protected function _isCallableAttributeInstance($instance, $method, $args)
    {
        if (!is_object($instance) || !method_exists($instance, $method)) {
            return false;
        }

        return true;
    }

    /**
     * Get attributes by name array
     *
     * @return array
     */
    public function getAttributesByCode()
    {
        return $this->_attributesByCode;
    }

    /**
     * Get attributes by id array
     *
     * @return array
     */
    public function getAttributesById()
    {
        return $this->_attributesById;
    }

    /**
     * Get attributes by table and name array
     *
     * @return array
     */
    public function getAttributesByTable()
    {
        return $this->_attributesByTable;
    }

    /**
     * Get entity table name
     *
     * @return string
     * @throws Mage_Core_Exception
     */
    public function getEntityTable()
    {
        if (!$this->_entityTable) {
            $table = $this->getEntityType()->getEntityTable();
            if (!$table) {
                $table = Mage_Eav_Model_Entity::DEFAULT_ENTITY_TABLE;
            }

            $this->_entityTable = Mage::getSingleton('core/resource')->getTableName($table);
        }

        return $this->_entityTable;
    }

    /**
     * Get entity id field name in entity table
     *
     * @return string
     * @throws Mage_Core_Exception
     */
    public function getEntityIdField()
    {
        if (!$this->_entityIdField) {
            $this->_entityIdField = $this->getEntityType()->getEntityIdField();
            if (!$this->_entityIdField) {
                $this->_entityIdField = Mage_Eav_Model_Entity::DEFAULT_ENTITY_ID_FIELD;
            }
        }

        return $this->_entityIdField;
    }

    /**
     * Get default entity id field name in attribute values tables
     *
     * @return string
     * @throws Mage_Core_Exception
     */
    public function getValueEntityIdField()
    {
        return $this->getEntityIdField();
    }

    /**
     * Get prefix for value tables
     *
     * @return string
     * @throws Mage_Core_Exception
     */
    public function getValueTablePrefix()
    {
        if (!$this->_valueTablePrefix) {
            $prefix = (string) $this->getEntityType()->getValueTablePrefix();
            if (!empty($prefix)) {
                $this->_valueTablePrefix = $prefix;
            } else {
                $this->_valueTablePrefix = $this->getEntityTable();
            }
        }

        return $this->_valueTablePrefix;
    }

    /**
     * Get entity table prefix for value
     *
     * @return string
     * @throws Mage_Core_Exception
     */
    public function getEntityTablePrefix()
    {
        if (empty($this->_entityTablePrefix)) {
            $prefix = $this->getEntityType()->getEntityTablePrefix();
            if (empty($prefix)) {
                $prefix = $this->getEntityType()->getEntityTable();
                if (empty($prefix)) {
                    $prefix = Mage_Eav_Model_Entity::DEFAULT_ENTITY_TABLE;
                }
            }

            $this->_entityTablePrefix = $prefix;
        }

        return $this->_entityTablePrefix;
    }

    /**
     * Check whether the attribute is a real field in entity table
     *
     * @param int|Mage_Eav_Model_Entity_Attribute_Abstract|string $attribute
     *
     * @return bool
     * @throws Mage_Core_Exception
     * @see Mage_Eav_Model_Entity_Abstract::getAttribute for $attribute format
     */
    public function isAttributeStatic($attribute)
    {
        $attrInstance = $this->getAttribute($attribute);
        return $attrInstance && $attrInstance->getBackend()->isStatic();
    }

    /**
     * Validate all object's attributes against configuration
     *
     * @param  Varien_Object                             $object
     * @return array|true
     * @throws Mage_Core_Exception
     * @throws Mage_Eav_Model_Entity_Attribute_Exception
     */
    public function validate($object)
    {
        $this->loadAllAttributes($object);
        $result = $this->walkAttributes('backend/validate', [$object]);
        $errors = [];
        foreach ($result as $attributeCode => $error) {
            if ($error === false) {
                $errors[$attributeCode] = true;
            } elseif (is_string($error)) {
                $errors[$attributeCode] = $error;
            }
        }

        if (!$errors) {
            return true;
        }

        return $errors;
    }

    /**
     * Set new increment id to object
     *
     * @return $this
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    public function setNewIncrementId(Varien_Object $object)
    {
        if ($object->getIncrementId()) {
            return $this;
        }

        $incrementId = $this->getEntityType()->fetchNewIncrementId($object->getStoreId());

        if ($incrementId !== false) {
            $object->setIncrementId($incrementId);
        }

        return $this;
    }

    /**
     * Check attribute unique value
     *
     * @param  Varien_Object       $object
     * @return bool
     * @throws Mage_Core_Exception
     * @throws Zend_Date_Exception
     */
    public function checkAttributeUniqueValue(Mage_Eav_Model_Entity_Attribute_Abstract $attribute, $object)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select();
        if ($attribute->getBackend()->getType() === 'static') {
            $value = $object->getData($attribute->getAttributeCode());
            $bind = [
                'entity_type_id' => $this->getTypeId(),
                'attribute_code' => trim($value),
            ];

            $select
                ->from($this->getEntityTable(), $this->getEntityIdField())
                ->where('entity_type_id = :entity_type_id')
                ->where($attribute->getAttributeCode() . ' = :attribute_code');
        } else {
            $value = $object->getData($attribute->getAttributeCode());
            if ($attribute->getBackend()->getType() === 'datetime') {
                $date  = new Zend_Date($value, Varien_Date::DATE_INTERNAL_FORMAT);
                $value = $date->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
            }

            $bind = [
                'entity_type_id' => $this->getTypeId(),
                'attribute_id'   => $attribute->getId(),
                'value'          => trim($value),
            ];
            $select
                ->from($attribute->getBackend()->getTable(), $attribute->getBackend()->getEntityIdField())
                ->where('entity_type_id = :entity_type_id')
                ->where('attribute_id = :attribute_id')
                ->where('value = :value');
        }

        $data = $adapter->fetchCol($select, $bind);

        if ($object->getId()) {
            if (isset($data[0])) {
                return $data[0] == $object->getId();
            }

            return true;
        }

        return !count($data);
    }

    /**
     * Retrieve default source model
     *
     * @return string
     */
    public function getDefaultAttributeSourceModel()
    {
        return Mage_Eav_Model_Entity::DEFAULT_SOURCE_MODEL;
    }

    /**
     * Load entity's attributes into the object
     *
     * @param  Mage_Core_Model_Abstract                  $object
     * @param  int                                       $entityId
     * @param  null|array                                $attributes
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Mage_Eav_Model_Entity_Attribute_Exception
     * @throws Zend_Db_Select_Exception
     */
    public function load($object, $entityId, $attributes = [])
    {
        Varien_Profiler::start('__EAV_LOAD_MODEL__');
        /**
         * Load object base row data
         */
        $select  = $this->_getLoadRowSelect($object, $entityId);
        $row     = $this->_getReadAdapter()->fetchRow($select);

        if (is_array($row)) {
            $object->addData($row);
        } else {
            $object->isObjectNew(true);
        }

        if (empty($attributes)) {
            $this->loadAllAttributes($object);
        } else {
            foreach ($attributes as $attrCode) {
                $this->getAttribute($attrCode);
            }
        }

        $this->_loadModelAttributes($object);

        $object->setOrigData();
        Varien_Profiler::start('__EAV_LOAD_MODEL_AFTER_LOAD__');

        $this->_afterLoad($object);
        Varien_Profiler::stop('__EAV_LOAD_MODEL_AFTER_LOAD__');

        Varien_Profiler::stop('__EAV_LOAD_MODEL__');
        return $this;
    }

    /**
     * Load model attributes data
     *
     * @param  Mage_Core_Model_Abstract|Varien_Object $object
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Zend_Db_Select_Exception
     */
    protected function _loadModelAttributes($object)
    {
        if (!$object->getId()) {
            return $this;
        }

        Varien_Profiler::start('__EAV_LOAD_MODEL_ATTRIBUTES__');

        $selects = [];
        foreach (array_keys($this->getAttributesByTable()) as $table) {
            $attribute = current($this->_attributesByTable[$table]);
            $eavType = $attribute->getBackendType();
            $select = $this->_getLoadAttributesSelect($object, $table);
            $selects[$eavType][] = $this->_addLoadAttributesSelectFields($select, $table, $eavType);
        }

        /** @var Mage_Eav_Model_Resource_Helper_Mysql4 $helper */
        $helper = Mage::getResourceHelper('eav');
        $selectGroups = $helper->getLoadAttributesSelectGroups($selects);
        foreach ($selectGroups as $selects) {
            if (!empty($selects)) {
                if (is_array($selects)) {
                    $select = $this->_prepareLoadSelect($selects);
                } else {
                    $select = $selects;
                }

                $values = $this->_getReadAdapter()->fetchAll($select);
                foreach ($values as $valueRow) {
                    $this->_setAttributeValue($object, $valueRow);
                }
            }
        }

        Varien_Profiler::stop('__EAV_LOAD_MODEL_ATTRIBUTES__');

        return $this;
    }

    /**
     * Prepare select object for loading entity attributes values
     *
     * @return Varien_Db_Select
     * @throws Zend_Db_Select_Exception
     */
    protected function _prepareLoadSelect(array $selects)
    {
        return $this->_getReadAdapter()->select()->union($selects, Zend_Db_Select::SQL_UNION_ALL);
    }

    /**
     * Retrieve select object for loading base entity row
     *
     * @param  Varien_Object       $object
     * @param  mixed               $rowId
     * @return Zend_Db_Select
     * @throws Mage_Core_Exception
     */
    protected function _getLoadRowSelect($object, $rowId)
    {
        return $this->_getReadAdapter()->select()
            ->from($this->getEntityTable())
            ->where($this->getEntityIdField() . ' =?', $rowId);
    }

    /**
     * Retrieve select object for loading entity attributes values
     *
     * @param  Varien_Object       $object
     * @param  string              $table
     * @return Varien_Db_Select
     * @throws Mage_Core_Exception
     */
    protected function _getLoadAttributesSelect($object, $table)
    {
        return $this->_getReadAdapter()->select()
            ->from($table, [])
            ->where($this->getEntityIdField() . ' =?', $object->getId());
    }

    /**
     * Adds Columns prepared for union
     *
     * @param  Varien_Db_Select $select
     * @param  string           $table
     * @param  string           $type
     * @return Varien_Db_Select
     */
    protected function _addLoadAttributesSelectFields($select, $table, $type)
    {
        /** @var Mage_Eav_Model_Resource_Helper_Mysql4 $helper */
        $helper = Mage::getResourceHelper('eav');
        $select->columns($helper->attributeSelectFields($table, $type));
        return $select;
    }

    /**
     * Initialize attribute value for object
     *
     * @param  Varien_Object       $object
     * @param  array               $valueRow
     * @return $this
     * @throws Mage_Core_Exception
     */
    protected function _setAttributeValue($object, $valueRow)
    {
        $attribute = $this->getAttribute($valueRow['attribute_id']);
        if ($attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $object->setData($attributeCode, $valueRow['value']);
            $attribute->getBackend()->setEntityValueId($object, $valueRow['value_id']);
        }

        return $this;
    }

    /**
     * Save entity's attributes into the object's resource
     *
     * @param  Mage_Core_Model_Abstract $object
     * @return $this
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    public function save(Varien_Object $object)
    {
        if ($object->isDeleted()) {
            return $this->delete($object);
        }

        if (!$this->isPartialSave()) {
            $this->loadAllAttributes($object);
        }

        if (!$object->getEntityTypeId()) {
            $object->setEntityTypeId($this->getTypeId());
        }

        $object->setParentId((int) $object->getParentId());

        $this->_beforeSave($object);
        $this->_processSaveData($this->_collectSaveData($object));
        $this->_afterSave($object);

        return $this;
    }

    /**
     * Retrieve Object instance with original data
     *
     * @param  Mage_Core_Model_Abstract $object
     * @return Varien_Object
     * @throws Mage_Core_Exception
     */
    protected function _getOrigObject($object)
    {
        $className  = $object::class;
        $origObject = new $className();
        $origObject->setData([]);
        $this->load($origObject, $object->getData($this->getEntityIdField()));

        return $origObject;
    }

    /**
     * Prepare entity object data for save
     *
     * result array structure:
     * array (
     *  'newObject', 'entityRow', 'insert', 'update', 'delete'
     * )
     *
     * @param  Mage_Core_Model_Abstract $newObject
     * @return array
     * @throws Mage_Core_Exception
     */
    protected function _collectSaveData($newObject)
    {
        $newData   = $newObject->getData();
        $entityId  = $newObject->getData($this->getEntityIdField());

        // define result data
        $entityRow  = [];
        $insert     = [];
        $update     = [];
        $delete     = [];

        if (!empty($entityId)) {
            $origData = $newObject->getOrigData();
            /**
             * get current data in db for this entity if original data is empty
             */
            if (empty($origData)) {
                $origData = $this->_getOrigObject($newObject)->getOrigData();
            }

            /**
             * drop attributes that are unknown in new data
             * not needed after introduction of partial entity loading
             */
            foreach ($origData as $key => $value) {
                if (!array_key_exists($key, $newData)) {
                    unset($origData[$key]);
                }
            }
        } else {
            $origData = [];
        }

        $staticFields   = $this->_getReadAdapter()->describeTable($this->getEntityTable());
        $staticFields   = array_keys($staticFields);

        $attributeCodes = array_keys($this->_attributesByCode);

        foreach ($newData as $key => $value) {
            /**
             * Check attribute information
             */
            if (is_numeric($key) || is_array($value)) {
                continue;
            }

            /**
             * Check if data key is presented in static fields or attribute codes
             */
            if (!in_array($key, $staticFields) && !in_array($key, $attributeCodes)) {
                continue;
            }

            $attribute = $this->getAttribute($key);
            if (empty($attribute)) {
                continue;
            }

            $attrId = $attribute->getAttributeId();

            /**
             * if attribute is static add to entity row and continue
             */
            if ($this->isAttributeStatic($key)) {
                $entityRow[$key] = $this->_prepareStaticValue($key, $value);
                continue;
            }

            /**
             * Check comparability for attribute value
             */
            if ($this->_canUpdateAttribute($attribute, $value, $origData)) {
                if ($this->_isAttributeValueEmpty($attribute, $value)) {
                    $delete[$attribute->getBackend()->getTable()][] = [
                        'attribute_id'  => $attrId,
                        'value_id'      => $attribute->getBackend()->getEntityValueId($newObject),
                    ];
                } elseif ($value !== $origData[$key]) {
                    $update[$attrId] = [
                        'value_id' => $attribute->getBackend()->getEntityValueId($newObject),
                        'value'    => $value,
                    ];
                }
            } elseif (!$this->_isAttributeValueEmpty($attribute, $value)) {
                $insert[$attrId] = $value;
            }
        }

        return [
            'newObject' => $newObject,
            'entityRow' => $entityRow,
            'insert'    => $insert,
            'update'    => $update,
            'delete'    => $delete,
        ];
    }

    /**
     * Return if attribute exists in original data array.
     *
     * @param  mixed $value New value of the attribute. Can be used in subclasses.
     * @return bool
     */
    protected function _canUpdateAttribute(Mage_Eav_Model_Entity_Attribute_Abstract $attribute, $value, array &$origData)
    {
        return array_key_exists($attribute->getAttributeCode(), $origData);
    }

    /**
     * Retrieve static field properties
     *
     * @param  string              $field
     * @return array|false
     * @throws Mage_Core_Exception
     */
    protected function _getStaticFieldProperties($field)
    {
        if (empty($this->_describeTable[$this->getEntityTable()])) {
            $this->_describeTable[$this->getEntityTable()] = $this->_getWriteAdapter()
                ->describeTable($this->getEntityTable());
        }

        return $this->_describeTable[$this->getEntityTable()][$field] ?? false;
    }

    /**
     * Prepare static value for save
     *
     * @param  string              $key
     * @param  mixed               $value
     * @return mixed
     * @throws Mage_Core_Exception
     */
    protected function _prepareStaticValue($key, $value)
    {
        $fieldProp = $this->_getStaticFieldProperties($key);

        if (!$fieldProp) {
            return $value;
        }

        if ($fieldProp['DATA_TYPE'] === 'decimal') {
            return Mage::app()->getLocale()->getNumber($value);
        }

        return $value;
    }

    /**
     * Save object collected data
     *
     * @param  array               $saveData array('newObject', 'entityRow', 'insert', 'update', 'delete')
     * @return $this
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    protected function _processSaveData($saveData)
    {
        $this->_attributeValuesToSave   = [];
        $this->_attributeValuesToDelete = [];

        /**
         * Import variables from save data array
         *
         * @see Mage_Eav_Model_Entity_Attribute_Abstract::_collectSaveData()
         */
        /**
         * @var array{
         *   newObject: Mage_Core_Model_Abstract,
         *   entityRow: array,
         *   insert: array,
         *   update: array,
         *   delete: array
         *   } $saveData
         */
        $newObject = $saveData['newObject'];
        $entityRow = $saveData['entityRow'];
        $insert    = $saveData['insert'];
        $update    = $saveData['update'];
        $delete    = $saveData['delete'];

        $adapter        = $this->_getWriteAdapter();
        $insertEntity   = true;
        $entityTable    = $this->getEntityTable();
        $entityIdField  = $this->getEntityIdField();
        $entityId       = $newObject->getId();

        unset($entityRow[$entityIdField]);
        if (!empty($entityId) && is_numeric($entityId)) {
            $bind   = ['entity_id' => $entityId];
            $select = $adapter->select()
                ->from($entityTable, $entityIdField)
                ->where("{$entityIdField} = :entity_id");
            $result = $adapter->fetchOne($select, $bind);
            if ($result) {
                $insertEntity = false;
            }
        } else {
            $entityId = null;
        }

        /**
         * Process base row
         */
        $entityObject = new Varien_Object($entityRow);
        $entityRow    = $this->_prepareDataForTable($entityObject, $entityTable);
        if ($insertEntity) {
            if (!empty($entityId)) {
                $entityRow[$entityIdField] = $entityId;
                $adapter->insertForce($entityTable, $entityRow);
            } else {
                $adapter->insert($entityTable, $entityRow);
                $entityId = $adapter->lastInsertId($entityTable);
            }

            $newObject->setId($entityId);
        } else {
            $where = sprintf('%s=%d', $adapter->quoteIdentifier($entityIdField), $entityId);
            $adapter->update($entityTable, $entityRow, $where);
        }

        /**
         * insert attribute values
         */
        if (!empty($insert)) {
            foreach ($insert as $attributeId => $value) {
                $attribute = $this->getAttribute($attributeId);
                $this->_insertAttribute($newObject, $attribute, $value);
            }
        }

        /**
         * update attribute values
         */
        if (!empty($update)) {
            foreach ($update as $attributeId => $value) {
                $attribute = $this->getAttribute($attributeId);
                $this->_updateAttribute($newObject, $attribute, $value['value_id'], $value['value']);
            }
        }

        /**
         * delete empty attribute values
         */
        if (!empty($delete)) {
            foreach ($delete as $table => $values) {
                $this->_deleteAttributes($newObject, $table, $values);
            }
        }

        $this->_processAttributeValues();

        $newObject->isObjectNew(false);

        return $this;
    }

    /**
     * Insert entity attribute value
     *
     * @param  Varien_Object                            $object
     * @param  Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param  mixed                                    $value
     * @return $this
     * @throws Mage_Core_Exception
     */
    protected function _insertAttribute($object, $attribute, $value)
    {
        return $this->_saveAttribute($object, $attribute, $value);
    }

    /**
     * Update entity attribute value
     *
     * @param  Varien_Object                            $object
     * @param  Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param  mixed                                    $valueId
     * @param  mixed                                    $value
     * @return $this
     * @throws Mage_Core_Exception
     */
    protected function _updateAttribute($object, $attribute, $valueId, $value)
    {
        return $this->_saveAttribute($object, $attribute, $value);
    }

    /**
     * Save entity attribute value
     *
     * Collect for mass save
     *
     * @param  Mage_Core_Model_Abstract|Varien_Object   $object
     * @param  Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param  mixed                                    $value
     * @return $this
     * @throws Mage_Core_Exception
     */
    protected function _saveAttribute($object, $attribute, $value)
    {
        $table = $attribute->getBackend()->getTable();
        if (!isset($this->_attributeValuesToSave[$table])) {
            $this->_attributeValuesToSave[$table] = [];
        }

        $entityIdField = $attribute->getBackend()->getEntityIdField();

        $data   = [
            'entity_type_id'    => $object->getEntityTypeId(),
            $entityIdField      => $object->getId(),
            'attribute_id'      => $attribute->getId(),
            'value'             => $this->_prepareValueForSave($value, $attribute),
        ];

        $this->_attributeValuesToSave[$table][] = $data;

        return $this;
    }

    /**
     * Save and detele collected attribute values
     *
     * @return $this
     * @throws Exception
     */
    protected function _processAttributeValues()
    {
        try {
            $adapter = $this->_getWriteAdapter();
            foreach ($this->_attributeValuesToSave as $table => $data) {
                $adapter->insertOnDuplicate($table, $data, ['value']);
            }

            foreach ($this->_attributeValuesToDelete as $table => $valueIds) {
                // phpcs:ignore Ecg.Performance.Loop.ModelLSD
                $adapter->delete($table, ['value_id IN (?)' => $valueIds]);
            }

            // reset data arrays
            $this->_attributeValuesToSave   = [];
            $this->_attributeValuesToDelete = [];
        } catch (Exception $exception) {
            $this->_attributeValuesToSave   = [];
            $this->_attributeValuesToDelete = [];
            throw $exception;
        }

        return $this;
    }

    /**
     * Prepare value for save
     *
     * @param  mixed               $value
     * @return mixed
     * @throws Mage_Core_Exception
     */
    protected function _prepareValueForSave($value, Mage_Eav_Model_Entity_Attribute_Abstract $attribute)
    {
        if ($attribute->getBackendType() === 'decimal') {
            return Mage::app()->getLocale()->getNumber($value);
        }

        $backendTable = $attribute->getBackendTable();
        if (!isset(self::$_attributeBackendTables[$backendTable])) {
            self::$_attributeBackendTables[$backendTable] = $this->_getReadAdapter()->describeTable($backendTable);
        }

        $describe = self::$_attributeBackendTables[$backendTable];
        return $this->_getReadAdapter()->prepareColumnValue($describe['value'], $value);
    }

    /**
     * Delete entity attribute values
     *
     * @param  Varien_Object $object
     * @param  string        $table
     * @param  array         $info
     * @return $this
     */
    protected function _deleteAttributes($object, $table, $info)
    {
        $valueIds = [];
        foreach ($info as $itemData) {
            $valueIds[] = $itemData['value_id'];
        }

        if (empty($valueIds)) {
            return $this;
        }

        if (isset($this->_attributeValuesToDelete[$table])) {
            $this->_attributeValuesToDelete[$table] = array_merge($this->_attributeValuesToDelete[$table], $valueIds);
        } else {
            $this->_attributeValuesToDelete[$table] = $valueIds;
        }

        return $this;
    }

    /**
     * Save attribute
     *
     * @param  string              $attributeCode
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function saveAttribute(Varien_Object $object, $attributeCode)
    {
        $this->_attributeValuesToSave   = [];
        $this->_attributeValuesToDelete = [];

        $attribute      = $this->getAttribute($attributeCode);
        $backend        = $attribute->getBackend();
        $table          = $backend->getTable();
        $entity         = $attribute->getEntity();
        $entityIdField  = $entity->getEntityIdField();
        $adapter        = $this->_getWriteAdapter();

        $row = [
            'entity_type_id' => $entity->getTypeId(),
            'attribute_id'   => $attribute->getId(),
            $entityIdField   => $object->getData($entityIdField),
        ];

        $newValue = $object->getData($attributeCode);
        if ($attribute->isValueEmpty($newValue)) {
            $newValue = null;
        }

        $whereArr = [];
        foreach ($row as $field => $value) {
            $whereArr[] = $adapter->quoteInto($field . '=?', $value);
        }

        $where = implode(' AND ', $whereArr);

        $adapter->beginTransaction();

        try {
            $select = $adapter->select()
                ->from($table, 'value_id')
                ->where($where);
            $origValueId = $adapter->fetchOne($select);

            if ($origValueId === false && ($newValue !== null)) {
                $this->_insertAttribute($object, $attribute, $newValue);
            } elseif ($origValueId !== false && ($newValue !== null)) {
                $this->_updateAttribute($object, $attribute, $origValueId, $newValue);
            } elseif ($origValueId !== false && ($newValue === null)) {
                $adapter->delete($table, $where);
            }

            $this->_processAttributeValues();
            $adapter->commit();
        } catch (Exception $exception) {
            $adapter->rollBack();
            throw $exception;
        }

        return $this;
    }

    /**
     * Delete entity using current object's data
     *
     * @param  int|string|Varien_Object $object
     * @return $this
     * @throws Exception
     */
    public function delete($object)
    {
        if (is_numeric($object)) {
            $objectId = (int) $object;
        } elseif ($object instanceof Varien_Object) {
            $objectId = (int) $object->getId();
        }

        $this->_beforeDelete($object);

        if (isset($objectId)) {
            try {
                $where = [
                    $this->getEntityIdField() . '=?' => $objectId,
                ];
                $this->_getWriteAdapter()->delete($this->getEntityTable(), $where);
                $this->loadAllAttributes($object);
                foreach (array_keys($this->getAttributesByTable()) as $table) {
                    // phpcs:ignore Ecg.Performance.Loop.ModelLSD
                    $this->_getWriteAdapter()->delete($table, $where);
                }
            } catch (Exception $exception) {
                throw $exception;
            }
        }

        $this->_afterDelete($object);
        return $this;
    }

    /**
     * After Load Entity process
     *
     * @return $this
     * @throws Mage_Eav_Model_Entity_Attribute_Exception
     */
    protected function _afterLoad(Varien_Object $object)
    {
        $this->walkAttributes('backend/afterLoad', [$object]);
        return $this;
    }

    /**
     * Before delete Entity process
     *
     * @return $this
     * @throws Mage_Eav_Model_Entity_Attribute_Exception
     */
    protected function _beforeSave(Varien_Object $object)
    {
        $this->walkAttributes('backend/beforeSave', [$object]);
        return $this;
    }

    /**
     * After Save Entity process
     *
     * @return $this
     * @throws Mage_Eav_Model_Entity_Attribute_Exception
     */
    protected function _afterSave(Varien_Object $object)
    {
        $this->walkAttributes('backend/afterSave', [$object]);
        return $this;
    }

    /**
     * Before Delete Entity process
     *
     * @return $this
     * @throws Mage_Eav_Model_Entity_Attribute_Exception
     */
    protected function _beforeDelete(Varien_Object $object)
    {
        $this->walkAttributes('backend/beforeDelete', [$object]);
        return $this;
    }

    /**
     * After delete entity process
     *
     * @return $this
     * @throws Mage_Eav_Model_Entity_Attribute_Exception
     */
    protected function _afterDelete(Varien_Object $object)
    {
        $this->walkAttributes('backend/afterDelete', [$object]);
        return $this;
    }

    /**
     * Retrieve Default attribute model
     *
     * @return string
     */
    protected function _getDefaultAttributeModel()
    {
        return Mage_Eav_Model_Entity::DEFAULT_ATTRIBUTE_MODEL;
    }

    /**
     * Retrieve default entity attributes
     *
     * @return array
     */
    protected function _getDefaultAttributes()
    {
        return ['entity_type_id', 'attribute_set_id', 'created_at', 'updated_at', 'parent_id', 'increment_id'];
    }

    /**
     * Retrieve default entity static attributes
     *
     * @return array
     * @throws Mage_Core_Exception
     */
    public function getDefaultAttributes()
    {
        return array_unique(array_merge($this->_getDefaultAttributes(), [$this->getEntityIdField()]));
    }

    /**
     * After set config process
     *
     * @return $this
     * @deprecated
     */
    protected function _afterSetConfig()
    {
        return $this;
    }

    /**
     * Check is attribute value empty
     *
     * @param  mixed               $value
     * @return bool
     * @throws Mage_Core_Exception
     */
    protected function _isAttributeValueEmpty(Mage_Eav_Model_Entity_Attribute_Abstract $attribute, $value)
    {
        return $attribute->isValueEmpty($value);
    }
}
