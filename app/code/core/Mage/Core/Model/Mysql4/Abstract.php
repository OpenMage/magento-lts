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
 * @package    Mage_Core
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Abstract resource model class
 *
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Core_Model_Mysql4_Abstract extends Mage_Core_Model_Resource_Abstract
{
    const CHECKSUM_KEY_NAME = 'Checksum';

    /**
     * Cached resources singleton
     *
     * @var Mage_Core_Model_Resource
     */
    protected $_resources;

    /**
     * Prefix for resources that will be used in this resource model
     *
     * @var string
     */
    protected $_resourcePrefix;

    /**
     * Connections cache for this resource model
     *
     * @var array
     */
    protected $_connections = array();

    /**
     * Resource model name that contains entities (names of tables)
     *
     * @var string
     */
    protected $_resourceModel;

    /**
     * Tables used in this resource model
     *
     * @var array
     */
    protected $_tables = array();

    /**
     * Main table name
     *
     * @var string
     */
    protected $_mainTable;

    /**
     * Main table primary key field name
     *
     * @var string
     */
    protected $_idFieldName;

    protected $_mainTableFields;

    /**
     * Main table unique keys field names
     *
     * could array(
     *   array('field' => 'db_field_name1', 'title' => 'Field 1 should be unique')
     *   array('field' => 'db_field_name2', 'title' => 'Field 2 should be unique')
     *   array(
     *      'field' => array('db_field_name3', 'db_field_name3'),
     *      'title' => 'Field 3 and Field 4 combination should be unique'
     *   )
     * )
     *
     * or string 'my_field_name' - will be autoconverted to
     *      array( array( 'field' => 'my_field_name', 'title' => 'my_field_name' ) )
     *
     * @var array
     */
    protected $_uniqueFields = null;

    /**
     * Standard resource model initialization
     *
     * @param string $mainTable
     * @param string $idFieldName
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    protected function _init($mainTable, $idFieldName)
    {
        $this->_setMainTable($mainTable, $idFieldName);
    }

    /**
     * Initialize connections and tables for this resource model
     *
     * If one or both arguments are string, will be used as prefix
     * If $tables is null and $connections is string, $tables will be the same
     *
     * @param string|array $connections
     * @param string|array|null $tables
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    protected function _setResource($connections, $tables=null)
    {
        $this->_resources = Mage::getSingleton('core/resource');

        if (is_array($connections)) {
            foreach ($connections as $k=>$v) {
                $this->_connections[$k] = $this->_resources->getConnection($v);
            }
        }
        elseif (is_string($connections)) {
            $this->_resourcePrefix = $connections;
        }

        if (is_null($tables) && is_string($connections)) {
            $this->_resourceModel = $this->_resourcePrefix;
        }
        elseif (is_array($tables)) {
            foreach ($tables as $k=>$v) {
                $this->_tables[$k] = $this->_resources->getTableName($v);
            }
        }
        elseif (is_string($tables)) {
            $this->_resourceModel = $tables;
        }
        return $this;
    }

    /**
     * Set main entity table name and primary key field name
     *
     * If field name is ommited {table_name}_id will be used
     *
     * @param string $mainTable
     * @param string|null $idFieldName
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    protected function _setMainTable($mainTable, $idFieldName=null)
    {
        $mainTableArr = explode('/', $mainTable);

        if (!empty($mainTableArr[1])) {
            if (empty($this->_resourceModel)) {
                $this->_setResource($mainTableArr[0]);
            }
            $this->_setMainTable($mainTableArr[1], $idFieldName);
        } else {
            $this->_mainTable = $mainTable;
            if (is_null($idFieldName)) {
                $idFieldName = $mainTable.'_id';
            }
            $this->_idFieldName = $idFieldName;
        }

        return $this;
    }

    /**
     * Get primary key field name
     *
     * @return string
     */
    public function getIdFieldName()
    {
        if (empty($this->_idFieldName)) {
            Mage::throwException(Mage::helper('core')->__('Empty identifier field name'));
        }
        return $this->_idFieldName;
    }

    /**
     * Get main table name
     *
     * @return string
     */
    public function getMainTable()
    {
        if (empty($this->_mainTable)) {
            Mage::throwException(Mage::helper('core')->__('Empty main table name'));
        }
        return $this->getTable($this->_mainTable);
    }

    /**
     * Get table name for the entity
     *
     * @param string $entityName
     */
    public function getTable($entityName)
    {
        if (isset($this->_tables[$entityName])) {
            return $this->_tables[$entityName];
        }
        if (strpos($entityName, '/')) {
            $this->_tables[$entityName] = $this->_resources->getTableName($entityName);
        } elseif (!empty($this->_resourceModel)) {
            $this->_tables[$entityName] = $this->_resources->getTableName(
                $this->_resourceModel.'/'.$entityName);
        } else {
            $this->_tables[$entityName] = $entityName;
        }
        return $this->_tables[$entityName];
    }

    /**
     * Get connection by name or type
     *
     * @param   string $connectionName
     * @return  Zend_Db_Adapter_Abstract
     */
    protected function _getConnection($connectionName)
    {
        if (isset($this->_connections[$connectionName])) {
            return $this->_connections[$connectionName];
        }
        if (!empty($this->_resourcePrefix)) {
            $this->_connections[$connectionName] = $this->_resources->getConnection(
                $this->_resourcePrefix.'_'.$connectionName);
        } else {
            $this->_connections[$connectionName] = $this->_resources->getConnection($connectionName);
        }

        return $this->_connections[$connectionName];
    }

    /**
     * Retrieve connection for read data
     *
     * @return  Zend_Db_Adapter_Abstract
     */
    protected function _getReadAdapter()
    {
        return $this->_getConnection('read');
    }

    /**
     * Retrieve connection for write data
     *
     * @return  Zend_Db_Adapter_Abstract
     */
    protected function _getWriteAdapter()
    {
        return $this->_getConnection('write');
    }

    /**
     * Temporary resolving collection compatibility
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function getReadConnection()
    {
        return $this->_getReadAdapter();
    }

    /**
     * Load an object
     *
     * @param   Mage_Core_Model_Abstract $object
     * @param   mixed $value
     * @param   string $field field to load by (defaults to model id)
     * @return  Mage_Core_Model_Mysql4_Abstract
     */
    public function load(Mage_Core_Model_Abstract $object, $value, $field=null)
    {
        if (is_null($field)) {
            $field = $this->getIdFieldName();
        }

        $read = $this->_getReadAdapter();
        if ($read && !is_null($value)) {
            $select = $this->_getLoadSelect($field, $value, $object);
            $data = $read->fetchRow($select);

            if ($data) {
                $object->setData($data);
            }
        }

        $this->_afterLoad($object);

        return $this;
    }

    /**
     * Retrieve select object for load object data
     *
     * @param   string $field
     * @param   mixed $value
     * @return  Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
           $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where($this->getMainTable().'.'.$field.'=?', $value);
        return $select;
    }

    /**
     * Save object object data
     *
     * @param   Mage_Core_Model_Abstract $object
     * @return  Mage_Core_Model_Mysql4_Abstract
     */
    public function save(Mage_Core_Model_Abstract $object)
    {
        if ($object->isDeleted()) {
            return $this->delete($object);
        }

        $this->_beforeSave($object);
        $this->_checkUnique($object);

        if (!is_null($object->getId())) {
            $condition = $this->_getWriteAdapter()->quoteInto($this->getIdFieldName().'=?', $object->getId());
            $this->_getWriteAdapter()->update($this->getMainTable(), $this->_prepareDataForSave($object), $condition);
        } else {
            $this->_getWriteAdapter()->insert($this->getMainTable(), $this->_prepareDataForSave($object));
            $object->setId($this->_getWriteAdapter()->lastInsertId($this->getMainTable()));
        }

        $this->_afterSave($object);

        return $this;
    }

    /**
     * Delete the object
     *
     * @param Varien_Object $object
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    public function delete(Mage_Core_Model_Abstract $object)
    {
        $this->_beforeDelete($object);
        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            $this->_getWriteAdapter()->quoteInto($this->getIdFieldName().'=?', $object->getId())
        );
        $this->_afterDelete($object);
        return $this;
    }

    /**
     * Add unique field restriction
     *
     * @param   array|string $field
     * @return  Mage_Core_Model_Mysql4_Abstract
     */
    public function addUniqueField($field)
    {
        $this->_initUniqueFields();
        if(is_array($this->_uniqueFields) ) {
            $this->_uniqueFields[] = $field;
        }
        return $this;
    }

    /**
     * Reset unique fields restrictions
     *
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    public function resetUniqueField()
    {
         $this->_uniqueFields = array();
         return $this;
    }

    /**
     * Initialize unique fields
     *
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array();
        return $this;
    }

    /**
     * Get configuration of all unique fields
     *
     * @return array
     */
    public function getUniqueFields()
    {
        if (is_null($this->_uniqueFields)) {
            $this->_initUniqueFields();
        }
        return $this->_uniqueFields;
    }

    /**
     * Prepare data for save
     *
     * @param   Mage_Core_Model_Abstract $object
     * @return  array
     */
    protected function _prepareDataForSave(Mage_Core_Model_Abstract $object)
    {
        $data = array();
        $fields = $this->_getWriteAdapter()->describeTable($this->getMainTable());
        foreach (array_keys($fields) as $field) {
            if ($object->hasData($field)) {
                $fieldValue = $object->getData($field);
                if ($fieldValue instanceof Zend_Db_Expr) {
                    $data[$field] = $fieldValue;
                }
                else {
                    if (null !== $fieldValue) {
                        $data[$field] = $this->_prepareValueForSave($fieldValue, $fields[$field]['DATA_TYPE']);
                    }
                    elseif (!empty($fields[$field]['NULLABLE'])) {
                        $data[$field] = null;
                    }
                }
            }
        }
        return $data;
    }

    /**
     * Prepare value for save
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    protected function _prepareValueForSave($value, $type)
    {
        if ($type == 'decimal') {
            $value = Mage::app()->getLocale()->getNumber($value);
        }
        return $value;
    }

    /**
     * Check for unique values existence
     *
     * @param   Varien_Object $object
     * @return  Mage_Core_Model_Mysql4_Abstract
     * @throws  Mage_Core_Exception
     */
    protected function _checkUnique(Mage_Core_Model_Abstract $object)
    {
        $existent = array();
        $fields = $this->getUniqueFields();
        if (!empty($fields)) {
            if (!is_array($fields)) {
                $this->_uniqueFields = array(
                    array(
                        'field' => $fields,
                        'title' => $fields
                ));
            }

            $data = new Varien_Object($this->_prepareDataForSave($object));
            $select = $this->_getWriteAdapter()->select()
                ->from($this->getMainTable());

            foreach ($fields as $unique) {
                $select->reset(Zend_Db_Select::WHERE);

                if (is_array($unique['field'])) {
                    foreach ($unique['field'] as $field) {
                        $select->where($field.'=?', $data->getData($field));
                    }
                }
                else {
                    $select->where( $unique['field'] . ' = ?', $data->getData($unique['field']) );
                }

                if ($object->getId()) {
                    $select->where($this->getIdFieldName().' != ?', $object->getId());
                }

                if ( $test = $this->_getWriteAdapter()->fetchRow($select) ) {
                    $existent[] = $unique['title'];
                }
            }
        }

        if (!empty($existent)) {
            if (count($existent) == 1 ) {
                $error = Mage::helper('core')->__('%s already exist', $existent[0]);
            }
            else {
                $error = Mage::helper('core')->__('%s already exists', implode(', ', $existent));
            }
            Mage::throwException($error);
        }
        return $this;
    }

    public function afterLoad(Mage_Core_Model_Abstract $object)
    {
        $this->_afterLoad($object);
    }

    /**
     * Perform actions after object load
     *
     * @param Varien_Object $object
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        return $this;
    }

    /**
     * Perform actions before object save
     *
     * @param Varien_Object $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        return $this;
    }

    /**
     * Perform actions after object save
     *
     * @param Varien_Object $object
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        return $this;
    }

    /**
     * Perform actions before object delete
     *
     * @param Varien_Object $object
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        return $this;
    }

    /**
     * Perform actions after object delete
     *
     * @param Varien_Object $object
     */
    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
        return $this;
    }

    /**
     * Retrieve table checksum
     *
     * @param   string $table
     * @return  int
     */
    public function getChecksum($table)
    {
        if (!$this->_getConnection('read')) {
            return false;
        }

        if (is_array($table)) {
            $table = implode(',', $table);
        }

        $data = $this->_getConnection('read')->fetchAll('checksum table '.$table);
        $checksum = 0;
        foreach ($data as $row) {
            $checksum+= $row[self::CHECKSUM_KEY_NAME];
        }
        return $checksum;
    }
}
