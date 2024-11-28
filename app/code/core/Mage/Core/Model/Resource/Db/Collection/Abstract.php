<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract Core Resource Collection
 *
 * @category   Mage
 * @package    Mage_Core
 */
abstract class Mage_Core_Model_Resource_Db_Collection_Abstract extends Varien_Data_Collection_Db
{
    public const CACHE_TAG = 'COLLECTION_DATA';

    /**
     * Model name
     *
     * @var string
     */
    protected $_model;

    /**
     * Resource model name
     *
     * @var string
     */
    protected $_resourceModel;

    /**
     * Resource instance
     *
     * @var Mage_Core_Model_Resource_Db_Abstract
     */
    protected $_resource;

    /**
     * Fields to select in query
     *
     * @var array|null
     */
    protected $_fieldsToSelect         = null;

    /**
     * Fields initial fields to select like id_field
     *
     * @var array|null
     */
    protected $_initialFieldsToSelect  = null;

    /**
     * Fields to select changed flag
     *
     * @var bool
     */
    protected $_fieldsToSelectChanged  = false;

    /**
     * Store joined tables here
     *
     * @var array
     */
    protected $_joinedTables           = [];

    /**
     * Collection main table
     *
     * @var string
     */
    protected $_mainTable              = null;

    /**
     * Reset items data changed flag
     *
     * @var bool
     */
    protected $_resetItemsDataChanged   = false;

    /**
     * Name prefix of events that are dispatched by model
     *
     * @var string
     */
    protected $_eventPrefix = '';

    /**
     * Name of event parameter
     *
     * @var string
     */
    protected $_eventObject = '';

    /**
     * Use analytic function flag
     * If true - allows to prepare final select with analytic function
     *
     * @var bool
     */
    protected $_useAnalyticFunction         = false;

    /**
     * Collection constructor
     *
     * @param Mage_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct($resource = null)
    {
        parent::__construct();
        $this->_construct();
        $this->_resource = $resource;
        $this->setConnection($this->getResource()->getReadConnection());
        $this->_initSelect();
    }

    /**
     * Initialization here
     *
     * @return void
     */
    protected function _construct()
    {
    }

    /**
     * Retrieve main table
     *
     * @return string
     */
    public function getMainTable()
    {
        if ($this->_mainTable === null) {
            $this->setMainTable($this->getResource()->getMainTable());
        }

        return $this->_mainTable;
    }

    /**
     * Set main collection table
     *
     * @param string $table
     * @return $this
     */
    public function setMainTable($table)
    {
        if (str_contains($table, '/')) {
            $table = $this->getTable($table);
        }

        if ($this->_mainTable !== null && $table !== $this->_mainTable && $this->getSelect() !== null) {
            $from = $this->getSelect()->getPart(Zend_Db_Select::FROM);
            if (isset($from['main_table'])) {
                $from['main_table']['tableName'] = $table;
            }
            $this->getSelect()->setPart(Zend_Db_Select::FROM, $from);
        }

        $this->_mainTable = $table;
        return $this;
    }

    /**
     * Init collection select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(['main_table' => $this->getMainTable()]);
        return $this;
    }

    /**
     * Get Zend_Db_Select instance and applies fields to select if needed
     *
     * @return Varien_Db_Select
     */
    public function getSelect()
    {
        if ($this->_select && $this->_fieldsToSelectChanged) {
            $this->_fieldsToSelectChanged = false;
            $this->_initSelectFields();
        }
        return parent::getSelect();
    }

    /**
     * Init fields for select
     *
     * @return $this
     */
    protected function _initSelectFields()
    {
        $columns = $this->_select->getPart(Zend_Db_Select::COLUMNS);
        $columnsToSelect = [];
        foreach ($columns as $columnEntry) {
            list($correlationName, $column, $alias) = $columnEntry;
            if ($correlationName !== 'main_table') { // Add joined fields to select
                if ($column instanceof Zend_Db_Expr) {
                    $column = $column->__toString();
                }
                $key = $alias ?? $column;
                $columnsToSelect[$key] = $columnEntry;
            }
        }

        $columns = $columnsToSelect;

        $columnsToSelect = array_keys($columnsToSelect);

        if ($this->_fieldsToSelect !== null) {
            $insertIndex = 0;
            foreach ($this->_fieldsToSelect as $alias => $field) {
                if (!is_string($alias)) {
                    $alias = null;
                }

                if ($field instanceof Zend_Db_Expr) {
                    $column = $field->__toString();
                } else {
                    $column = $field;
                }

                if (($alias !== null && in_array($alias, $columnsToSelect)) ||
                    // If field already joined from another table
                    ($alias === null && isset($alias, $columnsToSelect))
                ) {
                    continue;
                }

                $columnEntry = ['main_table', $field, $alias];
                array_splice($columns, $insertIndex, 0, [$columnEntry]); // Insert column
                $insertIndex++;
            }
        } else {
            array_unshift($columns, ['main_table', '*', null]);
        }

        $this->_select->setPart(Zend_Db_Select::COLUMNS, $columns);

        return $this;
    }

    /**
     * Retrieve initial fields to select like id field
     *
     * @return array
     */
    protected function _getInitialFieldsToSelect()
    {
        if ($this->_initialFieldsToSelect === null) {
            $this->_initialFieldsToSelect = [];
            $this->_initInitialFieldsToSelect();
        }

        return $this->_initialFieldsToSelect;
    }

    /**
     * Initialize initial fields to select like id field
     *
     * @return $this
     */
    protected function _initInitialFieldsToSelect()
    {
        $idFieldName = $this->getResource()->getIdFieldName();
        if ($idFieldName) {
            $this->_initialFieldsToSelect[] = $idFieldName;
        }
        return $this;
    }

    /**
     * Add field to select
     *
     * @param string|array $field
     * @param string|null $alias
     * @return $this
     */
    public function addFieldToSelect($field, $alias = null)
    {
        if ($field === '*') { // If we will select all fields
            $this->_fieldsToSelect = null;
            $this->_fieldsToSelectChanged = true;
            return $this;
        }

        if (is_array($field)) {
            if ($this->_fieldsToSelect === null) {
                $this->_fieldsToSelect = $this->_getInitialFieldsToSelect();
            }

            foreach ($field as $key => $value) {
                $this->addFieldToSelect(
                    $value,
                    (is_string($key) ? $key : null)
                );
            }

            $this->_fieldsToSelectChanged = true;
            return $this;
        }

        if ($alias === null) {
            $this->_fieldsToSelect[] = $field;
        } else {
            $this->_fieldsToSelect[$alias] = $field;
        }

        $this->_fieldsToSelectChanged = true;
        return $this;
    }

    /**
     * Add attribute expression (SUM, COUNT, etc)
     * Example: ('sub_total', 'SUM({{attribute}})', array('attribute' => 'revenue'))
     * Example: ('sub_total', 'SUM({{revenue}})', 'revenue')
     * For some functions like SUM use groupByAttribute.
     *
     * @param string $alias
     * @param string $expression
     * @param array|string $fields
     * @return $this
     */
    public function addExpressionFieldToSelect($alias, $expression, $fields)
    {
        // validate alias
        if (!is_array($fields)) {
            $fields = [$fields => $fields];
        }

        $fullExpression = $expression;
        foreach ($fields as $fieldKey => $fieldItem) {
            $fullExpression = str_replace('{{' . $fieldKey . '}}', $fieldItem, $fullExpression);
        }

        $this->getSelect()->columns([$alias => new Zend_Db_Expr($fullExpression)]);

        return $this;
    }

    /**
     * Removes field from select
     *
     * @param string|null $field
     * @param bool $isAlias Alias identifier
     * @return $this
     */
    public function removeFieldFromSelect($field, $isAlias = false)
    {
        if ($isAlias) {
            if (isset($this->_fieldsToSelect[$field])) {
                unset($this->_fieldsToSelect[$field]);
            }
        } else {
            foreach ($this->_fieldsToSelect as $key => $value) {
                if ($value === $field) {
                    unset($this->_fieldsToSelect[$key]);
                    break;
                }
            }
        }

        $this->_fieldsToSelectChanged = true;
        return $this;
    }

    /**
     * Removes all fields from select
     *
     * @return $this
     */
    public function removeAllFieldsFromSelect()
    {
        $this->_fieldsToSelect = $this->_getInitialFieldsToSelect();
        $this->_fieldsToSelectChanged = true;
        return $this;
    }

    /**
     * Standard resource collection initialization
     *
     * @param string $model
     * @param Mage_Core_Model_Resource_Db_Abstract $resourceModel
     * @return $this
     */
    protected function _init($model, $resourceModel = null)
    {
        $this->setModel($model);
        if (is_null($resourceModel)) {
            $resourceModel = $model;
        }
        $this->setResourceModel($resourceModel);
        return $this;
    }

    /**
     * Set model name for collection items
     *
     * @param string $model
     * @return $this
     */
    public function setModel($model)
    {
        if (is_string($model)) {
            $this->_model = $model;
            $this->setItemObjectClass(Mage::getConfig()->getModelClassName($model));
        }
        return $this;
    }

    /**
     * Get model instance
     *
     * @param array $args
     * @return string
     */
    public function getModelName($args = [])
    {
        return $this->_model;
    }

    /**
     *  Set resource model name for collection items
     *
     * @param string $model
     */
    public function setResourceModel($model)
    {
        $this->_resourceModel = $model;
    }

    /**
     *  Retrieve resource model name
     *
     * @return string
     */
    public function getResourceModelName()
    {
        return $this->_resourceModel;
    }

    /**
     * Get resource instance
     *
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    public function getResource()
    {
        if (empty($this->_resource)) {
            $this->_resource = Mage::getResourceModel($this->getResourceModelName());
        }
        return $this->_resource;
    }

    /**
     * Retrieve table name
     *
     * @param string $table
     * @return string
     */
    public function getTable($table)
    {
        return $this->getResource()->getTable($table);
    }

    /**
     * Retrieve all ids for collection
     *
     * @return array
     */
    public function getAllIds()
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);

        $idsSelect->columns($this->getResource()->getIdFieldName(), 'main_table');
        return $this->getConnection()->fetchCol($idsSelect);
    }

    /**
     * @return array
     */
    public function getData()
    {
        if ($this->_data === null) {
            $this->_renderFilters()
                 ->_renderOrders()
                 ->_renderLimit();
            /**
             * Prepare select for execute
             *
             */
            $query       = $this->_prepareSelect($this->getSelect());
            $this->_data = $this->_fetchAll($query);
            $this->_afterLoadData();
        }
        return $this->_data;
    }

    /**
     * Prepare select for load
     *
     * @return string
     * @throws Zend_Db_Select_Exception
     */
    protected function _prepareSelect(Varien_Db_Select $select)
    {
        /** @var Mage_Core_Model_Resource_Helper_Mysql4 $helper */
        $helper = Mage::getResourceHelper('core');

        $unionParts = $select->getPart(Zend_Db_Select::UNION);
        if (!empty($unionParts)) {
            $select = $helper->limitUnion($select);
        }

        if ($this->_useAnalyticFunction) {
            return $helper->getQueryUsingAnalyticFunction($select);
        }

        return (string)$select;
    }
    /**
     * Join table to collection select
     *
     * @param  array|string|Zend_Db_Expr $table Table name
     * @param  string $cond Join on this condition
     * @param  array|string $cols The columns to select from the joined table
     * @return $this
     */
    public function join($table, $cond, $cols = '*')
    {
        if (is_array($table)) {
            foreach ($table as $k => $v) {
                $alias = $k;
                $table = $v;
                break;
            }
        } else {
            $alias = $table;
        }

        if (!isset($this->_joinedTables[$alias])) {
            $this->getSelect()->join(
                [$alias => $this->getTable($table)],
                $cond,
                $cols
            );
            $this->_joinedTables[$alias] = true;
        }
        return $this;
    }

    /**
     * Redeclare before load method for adding event
     *
     * @return $this
     */
    protected function _beforeLoad()
    {
        parent::_beforeLoad();
        Mage::dispatchEvent('core_collection_abstract_load_before', ['collection' => $this]);
        if ($this->_eventPrefix && $this->_eventObject) {
            Mage::dispatchEvent($this->_eventPrefix . '_load_before', [
                $this->_eventObject => $this
            ]);
        }
        return $this;
    }

    /**
     * Set reset items data changed flag
     *
     * @param bool $flag
     * @return $this
     */
    public function setResetItemsDataChanged($flag)
    {
        $this->_resetItemsDataChanged = (bool)$flag;
        return $this;
    }

    /**
     * Set flag data has changed to all collection items
     *
     * @return $this
     */
    public function resetItemsDataChanged()
    {
        /** @var Varien_Object $item */
        foreach ($this->_items as $item) {
            $item->setDataChanges(false);
        }

        return $this;
    }

    /**
     * Redeclare after load method for specifying collection items original data
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        /** @var Varien_Object $item */
        foreach ($this->_items as $item) {
            $item->setOrigData();
            if ($this->_resetItemsDataChanged) {
                $item->setDataChanges(false);
            }
        }
        Mage::dispatchEvent('core_collection_abstract_load_after', ['collection' => $this]);
        if ($this->_eventPrefix && $this->_eventObject) {
            Mage::dispatchEvent($this->_eventPrefix . '_load_after', [
                $this->_eventObject => $this
            ]);
        }
        return $this;
    }

    /**
     * Save all the entities in the collection
     *
     * @return $this
     */
    public function save()
    {
        foreach ($this->getItems() as $item) {
            $item->save();
        }
        return $this;
    }

    /**
     * Check if cache can be used for collection
     *
     * @return bool
     */
    protected function _canUseCache()
    {
        return Mage::app()->useCache('collections') && !empty($this->_cacheConf);
    }

    /**
     * Load cached data for select
     *
     * @param Zend_Db_Select $select
     * @return string | false
     */
    protected function _loadCache($select)
    {
        return Mage::app()->loadCache($this->_getSelectCacheId($select));
    }

    /**
     * Save collection data to cache
     *
     * @param array $data
     * @param Zend_Db_Select $select
     * @return $this
     */
    protected function _saveCache($data, $select)
    {
        Mage::app()->saveCache(serialize($data), $this->_getSelectCacheId($select), $this->_getCacheTags());
        return $this;
    }

    /**
     * Redeclared for processing cache tags throw application object
     *
     * @return array
     */
    protected function _getCacheTags()
    {
        $tags = parent::_getCacheTags();
        $tags[] = self::CACHE_TAG;
        return $tags;
    }

    /**
     * Format Date to internal database date format
     *
     * @param int|string|Zend_Date $date
     * @param bool $includeTime
     * @return string
     */
    public function formatDate($date, $includeTime = true)
    {
        return Varien_Date::formatDate($date, $includeTime);
    }
}
