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
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
        if (!$connConfig || !$connConfig->is('active', 1)) {
            return false;
        }
        $origName = $connConfig->getParent()->getName();

        if (isset($this->_connections[$origName])) {
            return $this->_connections[$origName];
        }

        $typeInstance = $this->getConnectionTypeInstance((string)$connConfig->type);
        $conn = $typeInstance->getConnection($connConfig);

        $this->_connections[$name] = $conn;
        if ($origName!==$name) {
            $this->_connections[$origName] = $conn;
        }
        return $conn;
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
     * @param   string $model
     * @param   string $entity
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
                Mage::throwException(Mage::helper('core')->__('Can\'t retrieve entity config: %s', $modelEntity));
            }
        } else {
            $tableName = $modelEntity;
        }

        $tablePrefix = (string)Mage::getConfig()->getTablePrefix();
        return $tablePrefix . $tableName;
    }

    public function cleanDbRow(&$row) {
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
