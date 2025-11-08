<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * Entity/Attribute/Model - attribute abstract
 *
 * @package    Mage_Eav
 *
 * @method Mage_Eav_Model_Resource_Entity_Attribute _getResource()
 * @method array getAttributeSetInfo()
 * @method bool getFlatAddChildData()
 * @method array getFlatAddFilterableAttributes()
 * @method string getFrontendClass()
 * @method string getFrontendInput()
 * @method string getFrontendLabel()
 * @method string getFrontendModel()
 * @method bool getIsConfigurable()
 * @method bool getIsFilterable()
 * @method bool getIsFilterableInSearch()
 * @method bool getIsRequired()
 * @method bool getIsSearchable()
 * @method bool getIsUnique()
 * @method bool getIsUserDefined()
 * @method bool getIsVisible()
 * @method bool getIsVisibleInAdvancedSearch()
 * @method string getSortOrder()
 * @method string getSourceModel()
 * @method int getStoreId()
 * @method string getStoreLabel()
 * @method string getUsedForSortBy()
 * @method array getValidateRules()
 * @method bool hasAttributeSetInfo()
 * @method bool hasIsVisible()
 * @method $this setAttributeGroupId(int $value)
 * @method $this setAttributeSetInfo(array $value)
 * @method $this setFrontendModel(string $value)
 * @method $this setIsGlobal(int $value)
 * @method $this setSortOrder(string $value)
 * @method $this setSourceModel(string $value)
 * @method $this setStoreId(int $value)
 */
abstract class Mage_Eav_Model_Entity_Attribute_Abstract extends Mage_Core_Model_Abstract implements Mage_Eav_Model_Entity_Attribute_Interface
{
    public const TYPE_STATIC = 'static';

    /**
     * Attribute name
     *
     * @var string
     */
    protected $_name;

    /**
     * Entity instance
     *
     * @var Mage_Eav_Model_Entity_Abstract
     */
    protected $_entity;

    /**
     * Backend instance
     *
     * @var Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    protected $_backend;

    /**
     * Frontend instance
     *
     * @var Mage_Eav_Model_Entity_Attribute_Frontend_Abstract
     */
    protected $_frontend;

    /**
     * Source instance
     *
     * @var Mage_Eav_Model_Entity_Attribute_Source_Abstract
     */
    protected $_source;

    /**
     * Attribute id cache
     *
     * @var array
     */
    protected $_attributeIdCache            = [];

    /**
     * Attribute data table name
     *
     * @var string
     */
    protected $_dataTable                   = null;

    /**
     * Attribute validation flag
     *
     * @var bool
     */
    protected $_attributeValidationPassed   = false;

    protected function _construct()
    {
        $this->_init('eav/entity_attribute');
    }

    /**
     * Load attribute data by code
     *
     * @param   mixed $entityType
     * @param   string $code
     * @return  $this
     */
    public function loadByCode($entityType, $code)
    {
        Varien_Profiler::start('_LOAD_ATTRIBUTE_BY_CODE__');
        $model = Mage::getSingleton('eav/config')->getAttribute($entityType, $code);
        if ($model) {
            $this->setData($model->getData());
        } else {
            if (is_numeric($entityType)) {
                $entityTypeId = $entityType;
            } elseif (is_string($entityType)) {
                $entityType = Mage::getSingleton('eav/config')->getEntityType($entityType);
            }

            if ($entityType instanceof Mage_Eav_Model_Entity_Type) {
                $entityTypeId = $entityType->getId();
            }

            if (empty($entityTypeId)) {
                throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Invalid entity supplied.'));
            }

            $this->_getResource()->loadByCode($this, $entityTypeId, $code);
        }

        $this->_afterLoad();
        $this->setOrigData();
        $this->_hasDataChanges = false;
        Varien_Profiler::stop('_LOAD_ATTRIBUTE_BY_CODE__');
        return $this;
    }

    /**
     * Mark current attribute as passed validation
     */
    public function setAttributeValidationAsPassed()
    {
        $this->_attributeValidationPassed = true;
    }

    /**
     * Retrieve attribute configuration (deprecated)
     *
     * @return $this
     */
    public function getConfig()
    {
        return $this;
    }

    /**
     * Get attribute name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_getData('attribute_code');
    }

    /**
     * Specify attribute identifier
     *
     * @param   int $data
     * @return  $this
     */
    public function setAttributeId($data)
    {
        $this->_data['attribute_id'] = $data;
        return $this;
    }

    /**
     * Get attribute identifier
     *
     * @return null|int
     */
    public function getAttributeId()
    {
        return $this->_getData('attribute_id');
    }

    /**
     * @param string $data
     * @return $this
     */
    public function setAttributeCode($data)
    {
        return $this->setData('attribute_code', $data);
    }

    /**
     * @return string
     */
    public function getAttributeCode()
    {
        return $this->_getData('attribute_code');
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public function setAttributeModel($data)
    {
        return $this->setData('attribute_model', $data);
    }

    /**
     * @return mixed
     */
    public function getAttributeModel()
    {
        return $this->_getData('attribute_model');
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public function setBackendType($data)
    {
        return $this->setData('backend_type', $data);
    }

    /**
     * @return mixed
     */
    public function getBackendType()
    {
        return $this->_getData('backend_type');
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public function setBackendModel($data)
    {
        return $this->setData('backend_model', $data);
    }

    /**
     * @return mixed
     */
    public function getBackendModel()
    {
        return $this->_getData('backend_model');
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public function setBackendTable($data)
    {
        return $this->setData('backend_table', $data);
    }

    /**
     * @return mixed
     */
    public function getIsVisibleOnFront()
    {
        return $this->_getData('is_visible_on_front');
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->_getData('default_value');
    }

    /**
     * @return mixed
     */
    public function getAttributeSetId()
    {
        return $this->_getData('attribute_set_id');
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setAttributeSetId($id)
    {
        $this->_data['attribute_set_id'] = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEntityTypeId()
    {
        return $this->_getData('entity_type_id');
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setEntityTypeId($id)
    {
        $this->_data['entity_type_id'] = $id;
        return $this;
    }

    /**
     * @param mixed $type
     * @return $this
     */
    public function setEntityType($type)
    {
        $this->setData('entity_type', $type);
        return $this;
    }

    /**
     * Return is attribute global
     *
     * @return int
     * @deprecated moved to catalog attribute model
     */
    public function getIsGlobal()
    {
        return $this->_getData('is_global');
    }

    /**
     * Get attribute alias as "entity_type/attribute_code"
     *
     * @param Mage_Eav_Model_Entity_Abstract $entity exclude this entity
     * @return string
     */
    public function getAlias($entity = null)
    {
        $alias = '';
        if (($entity === null) || ($entity->getType() !== $this->getEntity()->getType())) {
            $alias .= $this->getEntity()->getType() . '/';
        }

        return  $alias . $this->getAttributeCode();
    }

    /**
     * Set attribute name
     *
     * @param   string $name
     * @return  $this
     */
    public function setName($name)
    {
        return $this->setData('attribute_code', $name);
    }

    /**
     * Retrieve entity type
     *
     * @return Mage_Eav_Model_Entity_Type
     */
    public function getEntityType()
    {
        return Mage::getSingleton('eav/config')->getEntityType($this->getEntityTypeId());
    }

    /**
     * Set attribute entity instance
     *
     * @param Mage_Eav_Model_Entity_Abstract $entity
     * @return $this
     */
    public function setEntity($entity)
    {
        $this->_entity = $entity;
        return $this;
    }

    /**
     * Retrieve entity instance
     *
     * @return Mage_Eav_Model_Entity_Abstract
     */
    public function getEntity()
    {
        if (!$this->_entity) {
            $this->_entity = $this->getEntityType();
        }

        return $this->_entity;
    }

    /**
     * Retrieve entity type
     *
     * @return string
     */
    public function getEntityIdField()
    {
        return $this->getEntity()->getValueEntityIdField();
    }

    /**
     * Retrieve backend instance
     *
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     * @throws Mage_Core_Exception
     */
    public function getBackend()
    {
        if (is_null($this->_backend)) {
            if (!$this->getBackendModel()) {
                $this->setBackendModel($this->_getDefaultBackendModel());
            }

            $backend = Mage::getModel($this->getBackendModel());
            if (!$backend) {
                throw Mage::exception('Mage_Eav', 'Invalid backend model specified: ' . $this->getBackendModel());
            }

            $this->_backend = $backend->setAttribute($this);
        }

        return $this->_backend;
    }

    /**
     * Retrieve frontend instance
     *
     * @return Mage_Eav_Model_Entity_Attribute_Frontend_Abstract
     */
    public function getFrontend()
    {
        if (is_null($this->_frontend)) {
            if (!$this->getFrontendModel()) {
                $this->setFrontendModel($this->_getDefaultFrontendModel());
            }

            $this->_frontend = Mage::getModel($this->getFrontendModel())
                ->setAttribute($this);
        }

        return $this->_frontend;
    }

    /**
     * Retrieve source instance
     *
     * @return Mage_Eav_Model_Entity_Attribute_Source_Abstract
     * @throws Mage_Core_Exception
     */
    public function getSource()
    {
        if (is_null($this->_source)) {
            if (!$this->getSourceModel()) {
                $this->setSourceModel($this->_getDefaultSourceModel());
            }

            $source = Mage::getModel($this->getSourceModel());
            if (!$source) {
                throw Mage::exception(
                    'Mage_Eav',
                    Mage::helper('eav')->__(
                        'Source model "%s" not found for attribute "%s"',
                        $this->getSourceModel(),
                        $this->getAttributeCode(),
                    ),
                );
            }

            $this->_source = $source->setAttribute($this);
        }

        return $this->_source;
    }

    /**
     * @return bool
     */
    public function usesSource()
    {
        return $this->getFrontendInput() === 'select' || $this->getFrontendInput() === 'multiselect'
            || $this->getData('source_model') != '';
    }

    /**
     * @return string
     */
    protected function _getDefaultBackendModel()
    {
        return Mage_Eav_Model_Entity::DEFAULT_BACKEND_MODEL;
    }

    /**
     * @return string
     */
    protected function _getDefaultFrontendModel()
    {
        return Mage_Eav_Model_Entity::DEFAULT_FRONTEND_MODEL;
    }

    /**
     * @return string
     */
    protected function _getDefaultSourceModel()
    {
        return $this->getEntity()->getDefaultAttributeSourceModel();
    }

    /**
     * @param mixed $value
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function isValueEmpty($value)
    {
        $attrType = $this->getBackend()->getType();
        return is_array($value)
            || ($value === null)
            || $value === false && $attrType !== 'int'
            || $value === '' && (in_array($attrType, ['int', 'decimal', 'datetime'], true));
    }

    /**
     * Check if attribute is valid
     *
     * @return bool
     */
    public function isAttributeValidationPassed()
    {
        return $this->_attributeValidationPassed;
    }

    /**
     * Check if attribute in specified set
     *
     * @param array|int $setId
     * @return bool
     */
    public function isInSet($setId)
    {
        if (!$this->hasAttributeSetInfo()) {
            return true;
        }

        if (is_array($setId)
            && count(array_intersect($setId, array_keys($this->getAttributeSetInfo())))
        ) {
            return true;
        }

        if (!is_array($setId)
            && array_key_exists($setId, $this->getAttributeSetInfo())
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check if attribute in specified group
     *
     * @param int $setId
     * @param int $groupId
     * @return bool
     */
    public function isInGroup($setId, $groupId)
    {
        $dataPath = sprintf('attribute_set_info/%s/group_id', $setId);
        if ($this->isInSet($setId) && $this->getData($dataPath) == $groupId) {
            return true;
        }

        return false;
    }

    /**
     * Return attribute id
     *
     * @param string $entityType
     * @param string $code
     * @return int
     */
    public function getIdByCode($entityType, $code)
    {
        $k = "{$entityType}|{$code}";
        if (!isset($this->_attributeIdCache[$k])) {
            $this->_attributeIdCache[$k] = $this->getResource()->getIdByCode($entityType, $code);
        }

        return $this->_attributeIdCache[$k];
    }

    /**
     * Check if attribute is static
     *
     * @return bool
     */
    public function isStatic()
    {
        return $this->getBackendType() == self::TYPE_STATIC || $this->getBackendType() == '';
    }

    /**
     * Get attribute backend table name
     *
     * @return string
     */
    public function getBackendTable()
    {
        if ($this->_dataTable === null) {
            if ($this->isStatic()) {
                $this->_dataTable = $this->getEntityType()->getValueTablePrefix();
            } else {
                $backendTable = trim((string) $this->_getData('backend_table'));
                if (empty($backendTable)) {
                    $entityTable  = [$this->getEntity()->getEntityTablePrefix(), $this->getBackendType()];
                    $backendTable = $this->getResource()->getTable($entityTable);
                }

                $this->_dataTable = $backendTable;
            }
        }

        return $this->_dataTable;
    }

    /**
     * Retrieve flat columns definition
     *
     * @return array
     */
    public function getFlatColumns()
    {
        // If source model exists - get definition from it
        if ($this->usesSource() && $this->getBackendType() != self::TYPE_STATIC) {
            return $this->getSource()->getFlatColums();
        }

        if (Mage::helper('core')->useDbCompatibleMode()) {
            return $this->_getFlatColumnsOldDefinition();
        } else {
            return $this->_getFlatColumnsDdlDefinition();
        }
    }

    /**
     * Retrieve flat columns DDL definition
     *
     * @return array
     */
    public function _getFlatColumnsDdlDefinition()
    {
        /** @var Mage_Eav_Model_Resource_Helper_Mysql4 $helper */
        $helper  = Mage::getResourceHelper('eav');
        $columns = [];
        switch ($this->getBackendType()) {
            case 'static':
                $describe = $this->_getResource()->describeTable($this->getBackend()->getTable());
                if (!isset($describe[$this->getAttributeCode()])) {
                    break;
                }

                $prop = $describe[$this->getAttributeCode()];
                $type = $prop['DATA_TYPE'];
                $size = $prop['LENGTH'] ?: null;

                $columns[$this->getAttributeCode()] = [
                    'type'      => $helper->getDdlTypeByColumnType($type),
                    'length'    => $size,
                    'unsigned'  => $prop['UNSIGNED'] ? true : false,
                    'nullable'   => $prop['NULLABLE'],
                    'default'   => $prop['DEFAULT'],
                    'extra'     => null,
                ];
                break;
            case 'datetime':
                $columns[$this->getAttributeCode()] = [
                    'type'      => Varien_Db_Ddl_Table::TYPE_DATETIME,
                    'unsigned'  => false,
                    'nullable'  => true,
                    'default'   => null,
                    'extra'     => null,
                ];
                break;
            case 'decimal':
                $columns[$this->getAttributeCode()] = [
                    'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                    'length'    => '12,4',
                    'unsigned'  => false,
                    'nullable'  => true,
                    'default'   => null,
                    'extra'     => null,
                ];
                break;
            case 'int':
                $columns[$this->getAttributeCode()] = [
                    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                    'unsigned'  => false,
                    'nullable'  => true,
                    'default'   => null,
                    'extra'     => null,
                ];
                break;
            case 'text':
                $columns[$this->getAttributeCode()] = [
                    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                    'unsigned'  => false,
                    'nullable'  => true,
                    'default'   => null,
                    'extra'     => null,
                    'length'    => Varien_Db_Ddl_Table::MAX_TEXT_SIZE,
                ];
                break;
            case 'varchar':
                $columns[$this->getAttributeCode()] = [
                    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                    'length'    => '255',
                    'unsigned'  => false,
                    'nullable'  => true,
                    'default'   => null,
                    'extra'     => null,
                ];
                break;
        }

        return $columns;
    }

    /**
     * Retrieve flat columns definition in old format (before MMDB support)
     * Used in database compatible mode
     *
     * @return array
     */
    protected function _getFlatColumnsOldDefinition()
    {
        $columns = [];
        switch ($this->getBackendType()) {
            case 'static':
                $describe = $this->_getResource()->describeTable($this->getBackend()->getTable());
                if (!isset($describe[$this->getAttributeCode()])) {
                    break;
                }

                $prop = $describe[$this->getAttributeCode()];
                $type = $prop['DATA_TYPE'];
                if (isset($prop['PRECISION'], $prop['SCALE'])) {
                    $type .= "({$prop['PRECISION']},{$prop['SCALE']})";
                } else {
                    $type .= (isset($prop['LENGTH']) && $prop['LENGTH']) ? "({$prop['LENGTH']})" : '';
                }

                $columns[$this->getAttributeCode()] = [
                    'type'      => $type,
                    'unsigned'  => $prop['UNSIGNED'] ? true : false,
                    'is_null'   => $prop['NULLABLE'],
                    'default'   => $prop['DEFAULT'],
                    'extra'     => null,
                ];
                break;
            case 'datetime':
                $columns[$this->getAttributeCode()] = [
                    'type'      => 'datetime',
                    'unsigned'  => false,
                    'is_null'   => true,
                    'default'   => null,
                    'extra'     => null,
                ];
                break;
            case 'decimal':
                $columns[$this->getAttributeCode()] = [
                    'type'      => 'decimal(12,4)',
                    'unsigned'  => false,
                    'is_null'   => true,
                    'default'   => null,
                    'extra'     => null,
                ];
                break;
            case 'int':
                $columns[$this->getAttributeCode()] = [
                    'type'      => 'int',
                    'unsigned'  => false,
                    'is_null'   => true,
                    'default'   => null,
                    'extra'     => null,
                ];
                break;
            case 'text':
                $columns[$this->getAttributeCode()] = [
                    'type'      => 'text',
                    'unsigned'  => false,
                    'is_null'   => true,
                    'default'   => null,
                    'extra'     => null,
                ];
                break;
            case 'varchar':
                $columns[$this->getAttributeCode()] = [
                    'type'      => 'varchar(255)',
                    'unsigned'  => false,
                    'is_null'   => true,
                    'default'   => null,
                    'extra'     => null,
                ];
                break;
        }

        return $columns;
    }

    /**
     * Retrieve index data for flat table
     *
     * @return array
     */
    public function getFlatIndexes()
    {
        $condition = $this->getUsedForSortBy();
        if ($this->getFlatAddFilterableAttributes()) {
            $condition = $condition || $this->getIsFilterable();
        }

        if ($this->getAttributeCode() === 'status') {
            $condition = true;
        }

        if ($condition) {
            if ($this->usesSource() && $this->getBackendType() != self::TYPE_STATIC) {
                return $this->getSource()->getFlatIndexes();
            }

            $indexes = [];

            switch ($this->getBackendType()) {
                case 'static':
                    $describe = $this->_getResource()
                        ->describeTable($this->getBackend()->getTable());
                    if (!isset($describe[$this->getAttributeCode()])) {
                        break;
                    }

                    $indexDataTypes = [
                        'varchar',
                        'varbinary',
                        'char',
                        'date',
                        'datetime',
                        'timestamp',
                        'time',
                        'year',
                        'enum',
                        'set',
                        'bit',
                        'bool',
                        'tinyint',
                        'smallint',
                        'mediumint',
                        'int',
                        'bigint',
                        'float',
                        'double',
                        'decimal',
                    ];
                    $prop = $describe[$this->getAttributeCode()];
                    if (in_array($prop['DATA_TYPE'], $indexDataTypes)) {
                        $indexName = 'IDX_' . strtoupper($this->getAttributeCode());
                        $indexes[$indexName] = [
                            'type'      => 'index',
                            'fields'    => [$this->getAttributeCode()],
                        ];
                    }

                    break;
                case 'datetime':
                case 'decimal':
                case 'int':
                case 'varchar':
                    $indexName = 'IDX_' . strtoupper($this->getAttributeCode());
                    $indexes[$indexName] = [
                        'type'      => 'index',
                        'fields'    => [$this->getAttributeCode()],
                    ];
                    break;
            }

            return $indexes;
        }

        return [];
    }

    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param int $store
     * @return null|$this|Varien_Db_Select
     */
    public function getFlatUpdateSelect($store = null)
    {
        if ($store === null) {
            foreach (Mage::app()->getStores() as $store) {
                $this->getFlatUpdateSelect($store->getId());
            }

            return $this;
        }

        if ($this->getBackendType() == self::TYPE_STATIC) {
            return null;
        }

        if ($this->usesSource()) {
            return $this->getSource()->getFlatUpdateSelect($store);
        }

        return $this->_getResource()->getFlatUpdateSelect($this, $store);
    }

    /**
     * @return array
     */
    public function getApplyTo()
    {
        return [];
    }
}
