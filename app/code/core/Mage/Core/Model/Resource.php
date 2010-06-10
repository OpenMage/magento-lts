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
 * @category    Mage
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Resources and connections registry and factory
 *
 */
class Mage_Core_Model_Resource
{

    const AUTO_UPDATE_CACHE_KEY = 'DB_AUTOUPDATE';
    const AUTO_UPDATE_ONCE      = 0;
    const AUTO_UPDATE_NEVER     = -1;
    const AUTO_UPDATE_ALWAYS    = 1;

    const DEFAULT_READ_RESOURCE = 'core_read';
    const DEFAULT_WRITE_RESOURCE= 'core_write';

    /**
     * Instances of classes for connection types
     *
     * @var array
     */
    protected $_connectionTypes = array();

    /**
     * Instances of actual connections
     *
     * @var array
     */
    protected $_connections = array();

    /**
     * Registry of resource entities
     *
     * @var array
     */
    protected $_entities = array();

    protected $_mappedTableNames;

    /**
     * Creates a connection to resource whenever needed
     *
     * @param string $name
     * @return mixed
     */
    public function getConnection($name)
    {
        if (isset($this->_connections[$name])) {
            return $this->_connections[$name];
        }
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

        $typeInstance = $this->getConnectionTypeInstance((string)$connConfig->type);
        $conn = $typeInstance->getConnection($connConfig);
        if (method_exists($conn, 'setCacheAdapter')) {
            $conn->setCacheAdapter(Mage::app()->getCache());
        }

        $this->_connections[$name] = $conn;
        if ($origName!==$name) {
            $this->_connections[$origName] = $conn;
        }
        return $conn;
    }

    protected function _getDefaultConnection($requiredConnectionName)
    {
        if (strpos($requiredConnectionName, 'read') !== false) {
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
     * @param string $resource
     * @param string $entity
     * @return Varien_Simplexml_Config
     */
    public function getEntity($model, $entity)
    {
        //return Mage::getConfig()->getNode("global/models/$model/entities/$entity");
        return Mage::getConfig()->getNode()->global->models->{$model}->entities->{$entity};
    }

    /**
     * Get resource table name
     *
     * @param   string $modelEntity
     * @return  string
     */
    public function getTableName($modelEntity)
    {
        $arr = explode('/', $modelEntity);
        if (isset($arr[1])) {
            list($model, $entity) = $arr;
            //$resourceModel = (string)Mage::getConfig()->getNode('global/models/'.$model.'/resourceModel');
            $resourceModel = (string) Mage::getConfig()->getNode()->global->models->{$model}->resourceModel;
            $entityConfig = $this->getEntity($resourceModel, $entity);
            if ($entityConfig) {
                $tableName = (string)$entityConfig->table;
            } else {
                Mage::throwException(Mage::helper('core')->__('Cannot retrieve entity config: %s', $modelEntity));
            }
        } else {
            $tableName = $modelEntity;
        }

        Mage::dispatchEvent('resource_get_tablename', array('resource' => $this, 'model_entity' => $modelEntity, 'table_name' => $tableName));
        $mappedTableName = $this->getMappedTableName($tableName);
        if ($mappedTableName) {
            $tableName = $mappedTableName;
        } else {
            $tablePrefix = (string)Mage::getConfig()->getTablePrefix();
            $tableName = $tablePrefix . $tableName;
        }

        return $tableName;
    }

    public function setMappedTableName($tableName, $mappedName)
    {
        $this->_mappedTableNames[$tableName] = $mappedName;
        return $this;
    }

    public function getMappedTableName($tableName)
    {
        if (isset($this->_mappedTableNames[$tableName])) {
            return $this->_mappedTableNames[$tableName];
        } else {
            return false;
        }
    }

    public function cleanDbRow(&$row)
    {
        if (!empty($row) && is_array($row)) {
            foreach ($row as $key=>&$value) {
                if (is_string($value) && $value==='0000-00-00 00:00:00') {
                    $value = '';
                }
            }
        }
        return $this;
    }

    public function createConnection($name, $type, $config)
    {
        if (!isset($this->_connections[$name])) {
            $typeObj = $this->getConnectionTypeInstance($type);
            $this->_connections[$name] = $typeObj->getConnection($config);
        }
        return $this->_connections[$name];
    }

    public function checkDbConnection()
    {
        if (!$this->getConnection('core_read')) {
            //Mage::app()->getResponse()->setRedirect(Mage::getUrl('install'));
        }
    }

    public function getAutoUpdate()
    {
        return self::AUTO_UPDATE_ALWAYS;
        #return Mage::app()->loadCache(self::AUTO_UPDATE_CACHE_KEY);
    }

    public function setAutoUpdate($value)
    {
        #Mage::app()->saveCache($value, self::AUTO_UPDATE_CACHE_KEY);
        return $this;
    }

}
