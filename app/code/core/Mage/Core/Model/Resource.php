<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Resources and connections registry and factory
 *
 * @package    Mage_Core
 */
class Mage_Core_Model_Resource
{
    public const AUTO_UPDATE_CACHE_KEY  = 'DB_AUTOUPDATE';
    public const AUTO_UPDATE_ONCE       = 0;
    public const AUTO_UPDATE_NEVER      = -1;
    public const AUTO_UPDATE_ALWAYS     = 1;

    public const DEFAULT_READ_RESOURCE  = 'core_read';
    public const DEFAULT_WRITE_RESOURCE = 'core_write';
    public const DEFAULT_SETUP_RESOURCE = 'core_setup';

    /**
     * Instances of classes for connection types
     *
     * @var array
     */
    protected $_connectionTypes    = [];

    /**
     * Instances of actual connections
     *
     * @var Varien_Db_Adapter_Interface[]|false
     */
    protected $_connections        = [];

    /**
     * Names of actual connections that wait to set cache
     *
     * @var array
     */
    protected $_skippedConnections = [];

    /**
     * Registry of resource entities
     *
     * @var array
     */
    protected $_entities           = [];

    /**
     * Mapped tables cache array
     *
     * @var array
     */
    protected $_mappedTableNames;

    /**
     * Creates a connection to resource whenever needed
     *
     * @param string $name
     * @return Varien_Db_Adapter_Interface|false
     */
    public function getConnection($name)
    {
        if (isset($this->_connections[$name])) {
            $connection = $this->_connections[$name];
            if (isset($this->_skippedConnections[$name]) && !Mage::app()->getIsCacheLocked()) {
                $connection->setCacheAdapter(Mage::app()->getCache());
                unset($this->_skippedConnections[$name]);
            }
            return $connection;
        }
        /** @var Mage_Core_Model_Config_Element $connConfig */
        $connConfig = Mage::getConfig()->getResourceConnectionConfig($name);

        if (!$connConfig) {
            $this->_connections[$name] = $this->_getDefaultConnection($name);
            return $this->_connections[$name];
        }
        if (!$connConfig->is('active', 1)) {
            return false;
        }

        $origName = $connConfig->getParent()->getName();
        if (isset($this->_connections[$origName])) {
            $this->_connections[$name] = $this->_connections[$origName];
            return $this->_connections[$origName];
        }

        $connection = $this->_newConnection((string) $connConfig->type, $connConfig);
        if ($connection) {
            if (Mage::app()->getIsCacheLocked()) {
                $this->_skippedConnections[$name] = true;
            } else {
                $connection->setCacheAdapter(Mage::app()->getCache());
            }
        }

        $this->_connections[$name] = $connection;
        if ($origName !== $name) {
            $this->_connections[$origName] = $connection;
        }

        return $connection;
    }

    /**
     * Get Instances of actual connections
     *
     * @return Varien_Db_Adapter_Interface[]|false
     */
    public function getConnections()
    {
        return $this->_connections;
    }

    /**
     * Retrieve connection adapter class name by connection type
     *
     * @param string $type  the connection type
     * @return string|false
     */
    protected function _getConnectionAdapterClassName($type)
    {
        $config = Mage::getConfig()->getResourceTypeConfig($type);
        if (!empty($config->adapter)) {
            return (string) $config->adapter;
        }
        return false;
    }

    /**
     * Create new connection adapter instance by connection type and config
     *
     * @param string $type the connection type
     * @param Mage_Core_Model_Config_Element|array $config the connection configuration
     * @return Varien_Db_Adapter_Interface|false
     */
    protected function _newConnection($type, $config)
    {
        if ($config instanceof Mage_Core_Model_Config_Element) {
            $config = $config->asArray();
        }
        if (!is_array($config)) {
            return false;
        }

        $connection = false;
        // try to get adapter and create connection
        $className  = $this->_getConnectionAdapterClassName($type);
        if ($className) {
            // define profiler settings
            $config['profiler'] = isset($config['profiler']) && $config['profiler'] != 'false';

            $connection = new $className($config);
            if ($connection instanceof Varien_Db_Adapter_Interface) {
                // run after initialization statements
                if (!empty($config['initStatements'])) {
                    $connection->query($config['initStatements']);
                }
            } else {
                $connection = false;
            }
        }

        // try to get connection from type
        if (!$connection) {
            $typeInstance = $this->getConnectionTypeInstance($type);
            $connection = $typeInstance->getConnection($config);
            if (!$connection instanceof Varien_Db_Adapter_Interface) {
                $connection = false;
            }
        }

        return $connection;
    }

    /**
     * Retrieve default connection name by required connection name
     *
     * @param string $requiredConnectionName
     * @return Varien_Db_Adapter_Interface|false
     */
    protected function _getDefaultConnection($requiredConnectionName)
    {
        if (str_contains($requiredConnectionName, 'read')) {
            return $this->getConnection(self::DEFAULT_READ_RESOURCE);
        }
        return $this->getConnection(self::DEFAULT_WRITE_RESOURCE);
    }

    /**
     * Get connection type instance
     *
     * Creates new if doesn't exist
     *
     * @param string $type
     * @return Mage_Core_Model_Resource_Type_Abstract
     */
    public function getConnectionTypeInstance($type)
    {
        if (!isset($this->_connectionTypes[$type])) {
            $config = Mage::getConfig()->getResourceTypeConfig($type);
            $typeClass = $config->getClassName();
            $this->_connectionTypes[$type] = new $typeClass();
        }
        return $this->_connectionTypes[$type];
    }

    /**
     * Get resource entity
     *
     * @param string $model
     * @param string $entity
     * @return SimpleXMLElement|Varien_Simplexml_Config
     */
    public function getEntity($model, $entity)
    {
        $modelsNode = Mage::getConfig()->getNode()->global->models;
        $entityConfig = $modelsNode->$model->entities->{$entity};

        /**
         * Backwards compatibility for pre-MMDB extensions.
         * In MMDB release resource nodes <..._mysql4> were renamed to <..._resource>. So <deprecatedNode> is left
         * to keep name of previously used nodes, that still may be used by non-updated extensions.
         */
        if (isset($modelsNode->$model->deprecatedNode)) {
            $deprecatedNode = (string) $modelsNode->$model->deprecatedNode;
            if (isset($modelsNode->$deprecatedNode->entities->$entity)) {
                $entityConfig = $modelsNode->$deprecatedNode->entities->$entity;
            }
        }

        return $entityConfig;
    }

    /**
     * Get resource table name, validated by db adapter
     *
     * @param   string|array $modelEntity
     * @return  string
     */
    public function getTableName($modelEntity)
    {
        $tableSuffix = null;
        if (is_array($modelEntity)) {
            [$modelEntity, $tableSuffix] = $modelEntity;
        }

        $parts = explode('/', $modelEntity);
        if (isset($parts[1])) {
            [$model, $entity] = $parts;
            $entityConfig = false;
            if (!empty(Mage::getConfig()->getNode()->global->models->{$model}->resourceModel)) {
                $resourceModel = (string) Mage::getConfig()->getNode()->global->models->{$model}->resourceModel;
                $entityConfig  = $this->getEntity($resourceModel, $entity);
            }

            if ($entityConfig && !empty($entityConfig->table)) {
                $tableName = (string) $entityConfig->table;
            } else {
                Mage::throwException(Mage::helper('core')->__('Can\'t retrieve entity config: %s', $modelEntity));
            }
        } else {
            $tableName = $modelEntity;
        }

        Mage::dispatchEvent('resource_get_tablename', [
            'resource'      => $this,
            'model_entity'  => $modelEntity,
            'table_name'    => $tableName,
            'table_suffix'  => $tableSuffix,
        ]);

        $mappedTableName = $this->getMappedTableName($tableName);
        if ($mappedTableName) {
            $tableName = $mappedTableName;
        } else {
            $tablePrefix = (string) Mage::getConfig()->getTablePrefix();
            $tableName = $tablePrefix . $tableName;
        }

        if (!is_null($tableSuffix)) {
            $tableName .= '_' . $tableSuffix;
        }
        return $this->getConnection(self::DEFAULT_READ_RESOURCE)->getTableName($tableName);
    }

    /**
     * Set mapped table name
     *
     * @param string $tableName
     * @param string $mappedName
     * @return $this
     */
    public function setMappedTableName($tableName, $mappedName)
    {
        $this->_mappedTableNames[$tableName] = $mappedName;
        return $this;
    }

    /**
     * Get mapped table name
     *
     * @param string $tableName
     * @return bool|string
     */
    public function getMappedTableName($tableName)
    {
        return $this->_mappedTableNames[$tableName] ?? false;
    }

    /**
     * Clean db row
     *
     * @param array $row
     * @return $this
     */
    public function cleanDbRow(&$row)
    {
        $zeroDate = $this->getConnection(self::DEFAULT_READ_RESOURCE)->getSuggestedZeroDate();
        if (!empty($row) && is_array($row)) {
            foreach ($row as $key => &$value) {
                if (is_string($value) && $value === $zeroDate) {
                    $value = '';
                }
            }
        }
        return $this;
    }

    /**
     * Create new connection with custom config
     *
     * @param string $name
     * @param string $type
     * @param array $config
     * @return Varien_Db_Adapter_Interface
     */
    public function createConnection($name, $type, $config)
    {
        if (!isset($this->_connections[$name])) {
            $connection = $this->_newConnection($type, $config);

            $this->_connections[$name] = $connection;
        }
        return $this->_connections[$name];
    }

    public function checkDbConnection()
    {
        if (!$this->getConnection('core_read')) {
            //Mage::app()->getResponse()->setRedirect(Mage::getUrl('install'));
        }
    }

    /**
     * @return int
     */
    public function getAutoUpdate()
    {
        return self::AUTO_UPDATE_ALWAYS;
        #return Mage::app()->loadCache(self::AUTO_UPDATE_CACHE_KEY);
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setAutoUpdate($value)
    {
        #Mage::app()->saveCache($value, self::AUTO_UPDATE_CACHE_KEY);
        return $this;
    }
    /**
     * Retrieve 32bit UNIQUE HASH for a Table index
     *
     * @param string $tableName
     * @param array|string $fields
     * @param string $indexType
     * @return string
     */
    public function getIdxName($tableName, $fields, $indexType = Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
    {
        return $this->getConnection(self::DEFAULT_READ_RESOURCE)
            ->getIndexName($this->getTableName($tableName), $fields, $indexType);
    }

    /**
     * Retrieve 32bit UNIQUE HASH for a Table foreign key
     *
     * @param array|string $priTableName  the target table name
     * @param string $priColumnName the target table column name
     * @param string $refTableName  the reference table name
     * @param string $refColumnName the reference table column name
     * @return string
     */
    public function getFkName($priTableName, $priColumnName, $refTableName, $refColumnName)
    {
        return $this->getConnection(self::DEFAULT_READ_RESOURCE)
            ->getForeignKeyName(
                $this->getTableName($priTableName),
                $priColumnName,
                $this->getTableName($refTableName),
                $refColumnName,
            );
    }
}
