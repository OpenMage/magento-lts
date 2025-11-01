<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Abstract resource model
 *
 * @package    Mage_Core
 */
abstract class Mage_Core_Model_Resource_Abstract
{
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
     * Array of callbacks subscribed to commit transaction commit
     *
     * @var array
     */
    protected static $_commitCallbacks = [];

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
     * @return $this
     */
    public function beginTransaction()
    {
        $this->_getWriteAdapter()->beginTransaction();
        return $this;
    }

    /**
     * Subscribe some callback to transaction commit
     *
     * @param callable $callback
     * @return $this
     * @SuppressWarnings("PHPMD.CamelCaseVariableName")
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
     * @SuppressWarnings("PHPMD.CamelCaseVariableName")
     */
    public function commit()
    {
        $this->_getWriteAdapter()->commit();
        /**
         * Process after commit callbacks
         */
        if ($this->_getWriteAdapter()->getTransactionLevel() === 0) {
            $adapterKey = spl_object_hash($this->_getWriteAdapter());
            if (isset(self::$_commitCallbacks[$adapterKey])) {
                $callbacks = self::$_commitCallbacks[$adapterKey];
                self::$_commitCallbacks[$adapterKey] = [];
                foreach ($callbacks as $callback) {
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
     * @SuppressWarnings("PHPMD.CamelCaseVariableName")
     */
    public function rollBack()
    {
        $this->_getWriteAdapter()->rollBack();
        if ($this->_getWriteAdapter()->getTransactionLevel() === 0) {
            $adapterKey = spl_object_hash($this->_getWriteAdapter());
            if (isset(self::$_commitCallbacks[$adapterKey])) {
                self::$_commitCallbacks[$adapterKey] = [];
            }
        }

        return $this;
    }

    /**
     * Format date to internal format
     *
     * @param null|bool|int|string|Zend_Date $date
     * @param bool $includeTime
     * @return null|string
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
     * @param string $field
     * @param mixed $defaultValue
     */
    protected function _unserializeField(Varien_Object $object, $field, $defaultValue = null)
    {
        $value = $object->getData($field);
        if (empty($value)) {
            $object->setData($field, $defaultValue);
        } elseif (!is_array($value) && !is_object($value)) {
            $object->setData($field, unserialize($value, ['allowed_classes' => ['Varien_Object']]));
        }
    }

    /**
     * Prepare data for passed table
     *
     * @param string $table
     * @return array
     */
    protected function _prepareDataForTable(Varien_Object $object, $table)
    {
        $data = [];
        $fields = $this->_getReadAdapter()->describeTable($table);
        foreach (array_keys($fields) as $field) {
            if ($object->hasData($field)) {
                $fieldValue = $object->getData($field);
                if ($fieldValue instanceof Zend_Db_Expr) {
                    $data[$field] = $fieldValue;
                } elseif ($fieldValue !== null) {
                    $fieldValue   = $this->_prepareTableValueForSave($fieldValue, $fields[$field]['DATA_TYPE']);
                    $data[$field] = $this->_getWriteAdapter()->prepareColumnValue($fields[$field], $fieldValue);
                } elseif (!empty($fields[$field]['NULLABLE'])) {
                    $data[$field] = null;
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
        if (in_array($type, ['decimal', 'numeric', 'float'])) {
            return Mage::app()->getLocale()->getNumber($value);
        }

        return $value;
    }

    public function isModuleEnabled(string $moduleName, string $helperAlias = 'core'): bool
    {
        return Mage::helper($helperAlias)->isModuleEnabled($moduleName);
    }
}
