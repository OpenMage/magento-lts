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
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract resource model
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Core_Model_Resource_Abstract
{
    const READ_UNCOMMITTED = 0;
    const READ_COMMITTED = 1;
    const REPEATABLE_READ = 2;
    const SERIALIZABLE = 3;

    const SUPPORTED_ISOLATION_LEVELS = [
        self::READ_UNCOMMITTED => "READ UNCOMMITTED",
        self::READ_COMMITTED   => "READ COMMITTED",
        self::REPEATABLE_READ  => "REPEATABLE READ",
        self::SERIALIZABLE     => "SERIALIZABLE",
    ];

    /**
     * Main constructor
     */
    public function __construct()
    {
        /**
         * Please override this one instead of overriding real __construct constructor
         */
        $this->_construct();
    }

    /**
     * Default transaction isolation level defined in the database.
     *
     * @todo Default this value with whatever the database reports in SELECT @@GLOBAL.TX_ISOLATION
     *
     * @var int
     */
    static protected $_defaultIsolationLevel = self::READ_COMMITTED;

    /**
     * Current transaction isolation level changed in prepareTransactionIsolationLevel
     *
     * @todo Default this value with whatever the database reports in SELECT @@GLOBAL.TX_ISOLATION
     *
     * @var int
     */
    static protected $_currentIsolationLevel = self::READ_COMMITTED;

    /**
     * Array of callbacks subscribed to commit transaction commit
     *
     * @var array
     */
    static protected $_commitCallbacks = array();

    /**
     * Resource initialization
     */
    abstract protected function _construct();

    /**
     * Retrieve connection for read data
     * @return Varien_Db_Adapter_Interface
     */
    abstract protected function _getReadAdapter();

    /**
     * Retrieve connection for write data
     * @return Varien_Db_Adapter_Interface
     */
    abstract protected function _getWriteAdapter();

    /**
     * Start resource transaction
     *
     * @param int $isolationLevel Any of the supported isolation levels defined in this class.
     * @return $this
     */
    public function beginTransaction($isolationLevel=null)
    {
        $this->prepareTransactionIsolationLevel($isolationLevel);
        $this->_getWriteAdapter()->beginTransaction();
        return $this;
    }

    /**
     * Subscribe some callback to transaction commit
     *
     * @param callable $callback
     * @return $this
     */
    public function addCommitCallback($callback)
    {
        $adapterKey = spl_object_hash($this->_getWriteAdapter());
        self::$_commitCallbacks[$adapterKey][] = $callback;
        return $this;
    }

    /**
     * Commit resource transaction
     *
     * @return $this
     */
    public function commit()
    {
        $this->_getWriteAdapter()->commit();
        $this->restoreTransactionIsolationLevel();
        /**
         * Process after commit callbacks
         */
        if ($this->_getWriteAdapter()->getTransactionLevel() === 0) {
            $adapterKey = spl_object_hash($this->_getWriteAdapter());
            if (isset(self::$_commitCallbacks[$adapterKey])) {
                $callbacks = self::$_commitCallbacks[$adapterKey];
                self::$_commitCallbacks[$adapterKey] = array();
                foreach ($callbacks as $index => $callback) {
                    call_user_func($callback);
                }
            }
        }
        return $this;
    }

    /**
     * Roll back resource transaction
     *
     * @return $this
     */
    public function rollBack()
    {
        $this->_getWriteAdapter()->rollBack();
        $this->restoreTransactionIsolationLevel();
        if ($this->_getWriteAdapter()->getTransactionLevel() === 0) {
            $adapterKey = spl_object_hash($this->_getWriteAdapter());
            if (isset(self::$_commitCallbacks[$adapterKey])) {
                self::$_commitCallbacks[$adapterKey] = array();
            }
        }
        return $this;
    }

    /**
     * Format date to internal format
     *
     * @param string|Zend_Date|true|null $date
     * @param bool $includeTime
     * @return string|null
     */
    public function formatDate($date, $includeTime = true)
    {
         return Varien_Date::formatDate($date, $includeTime);
    }

    /**
     * Convert internal date to UNIX timestamp
     *
     * @param string $str
     * @return int
     */
    public function mktime($str)
    {
        return Varien_Date::toTimestamp($str);
    }

    /**
     * Serialize specified field in an object
     *
     * @param Varien_Object $object
     * @param string $field
     * @param mixed $defaultValue
     * @param bool $unsetEmpty
     * @return $this
     */
    protected function _serializeField(Varien_Object $object, $field, $defaultValue = null, $unsetEmpty = false)
    {
        $value = $object->getData($field);
        if (empty($value)) {
            if ($unsetEmpty) {
                $object->unsetData($field);
            } else {
                if (is_object($defaultValue) || is_array($defaultValue)) {
                    $defaultValue = serialize($defaultValue);
                }
                $object->setData($field, $defaultValue);
            }
        } elseif (is_array($value) || is_object($value)) {
            $object->setData($field, serialize($value));
        }

        return $this;
    }

    /**
     * Unserialize Varien_Object field in an object
     *
     * @param Varien_Object $object
     * @param string $field
     * @param mixed $defaultValue
     */
    protected function _unserializeField(Varien_Object $object, $field, $defaultValue = null)
    {
        $value = $object->getData($field);
        if (empty($value)) {
            $object->setData($field, $defaultValue);
        } elseif (!is_array($value) && !is_object($value)) {
            $object->setData($field, unserialize($value));
        }
    }

    /**
     * Prepare data for passed table
     *
     * @param Varien_Object $object
     * @param string $table
     * @return array
     */
    protected function _prepareDataForTable(Varien_Object $object, $table)
    {
        $data = array();
        $fields = $this->_getWriteAdapter()->describeTable($table);
        foreach (array_keys($fields) as $field) {
            if ($object->hasData($field)) {
                $fieldValue = $object->getData($field);
                if ($fieldValue instanceof Zend_Db_Expr) {
                    $data[$field] = $fieldValue;
                } else {
                    if (null !== $fieldValue) {
                        $fieldValue   = $this->_prepareTableValueForSave($fieldValue, $fields[$field]['DATA_TYPE']);
                        $data[$field] = $this->_getWriteAdapter()->prepareColumnValue($fields[$field], $fieldValue);
                    } elseif (!empty($fields[$field]['NULLABLE'])) {
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
    protected function _prepareTableValueForSave($value, $type)
    {
        $type = strtolower($type);
        if ($type == 'decimal' || $type == 'numeric' || $type == 'float') {
            $value = Mage::app()->getLocale()->getNumber($value);
        }
        return $value;
    }

    /**
     * Prepare transaction isolation level for session.
     *
     * @param int $isolationLevel Any of the supported isolation levels defined in this class.
     * @return void
     */
    public function prepareTransactionIsolationLevel($isolationLevel)
    {
        if( isset(self::SUPPORTED_ISOLATION_LEVELS[$isolationLevel]) && self::$_currentIsolationLevel != $isolationLevel) {
            $this->_getWriteAdapter()->query('SET SESSION TRANSACTION ISOLATION LEVEL '.self::SUPPORTED_ISOLATION_LEVELS[$isolationLevel].';');
            self::$_currentIsolationLevel = $isolationLevel;
        }
    }

    /**
     * Restore transaction isolation level for session
     *
     * @return void
     */
    public function restoreTransactionIsolationLevel()
    {
        if( self::$_currentIsolationLevel != self::$_defaultIsolationLevel ) {
            $this->_getWriteAdapter()->query('SET SESSION TRANSACTION ISOLATION LEVEL '.self::SUPPORTED_ISOLATION_LEVELS[self::$_defaultIsolationLevel].';');
            self::$_currentIsolationLevel = self::$_defaultIsolationLevel;
        }
    }
}
