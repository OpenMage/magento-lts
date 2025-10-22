<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Data
 */

/**
 * Data collection
 *
 * @package    Varien_Data
 */
class Varien_Data_Collection implements IteratorAggregate, Countable
{
    public const SORT_ORDER_ASC    = 'ASC';

    public const SORT_ORDER_DESC   = 'DESC';

    /**
     * Collection items
     *
     * @var array
     */
    protected $_items = [];

    /**
     * Item object class name
     *
     * @var string
     */
    protected $_itemObjectClass = 'Varien_Object';

    /**
     * Order configuration
     *
     * @var array
     */
    protected $_orders = [];

    /**
     * Filters configuration
     *
     * @var array
     */
    protected $_filters = [];

    /**
     * Filter rendered flag
     *
     * @var bool
     */
    protected $_isFiltersRendered = false;

    /**
     * Current page number for items pager
     *
     * @var int|null
     */
    protected $_curPage = 1;

    /**
     * Pager page size
     *
     * if page size is false, then we works with all items
     *
     * @var int|false|null
     */
    protected $_pageSize = false;

    /**
     * Total items number
     *
     * @var int|null
     */
    protected $_totalRecords;

    /**
     * Loading state flag
     *
     * @var bool
     */
    protected $_isCollectionLoaded;

    protected $_cacheKey;

    protected $_cacheTags = [];

    protected $_cacheLifetime = 86400;

    /**
     * Additional collection flags
     *
     * @var array
     */
    protected $_flags = [];

    public function __construct() {}

    /**
     * Add collection filter
     *
     * @param string $field
     * @param string|array $value
     * @param string $type and|or|string
     * @return $this
     */
    public function addFilter($field, $value, $type = 'and')
    {
        $filter = new Varien_Object(); // implements ArrayAccess
        $filter['field']   = $field;
        $filter['value']   = $value;
        $filter['type']    = strtolower($type);

        $this->_filters[] = $filter;
        $this->_isFiltersRendered = false;
        return $this;
    }

    /**
     * Search for a filter by specified field
     *
     * Multiple filters can be matched if an array is specified:
     * - 'foo' -- get the first filter with field name 'foo'
     * - array('foo') -- get all filters with field name 'foo'
     * - array('foo', 'bar') -- get all filters with field name 'foo' or 'bar'
     * - array() -- get all filters
     *
     * @param string|array $field
     * @return Varien_Object|array|null
     */
    public function getFilter($field)
    {
        if (is_array($field)) {
            // empty array: get all filters
            if (empty($field)) {
                return $this->_filters;
            }

            // non-empty array: collect all filters that match specified field names
            $result = [];
            foreach ($this->_filters as $filter) {
                if (in_array($filter['field'], $field)) {
                    $result[] = $filter;
                }
            }

            return $result;
        }

        // get a first filter by specified name
        foreach ($this->_filters as $filter) {
            if ($filter['field'] === $field) {
                return $filter;
            }
        }
    }

    /**
     * Retrieve collection loading status
     *
     * @return bool
     */
    public function isLoaded()
    {
        return $this->_isCollectionLoaded;
    }

    /**
     * Set collection loading status flag
     *
     * @param bool $flag
     * @return $this
     */
    protected function _setIsLoaded($flag = true)
    {
        $this->_isCollectionLoaded = $flag;
        return $this;
    }

    /**
     * Get current collection page
     *
     * @param  int $displacement
     * @return int
     */
    public function getCurPage($displacement = 0)
    {
        if ($this->_curPage + $displacement <= 1) {
            return 1;
        } elseif ($this->_curPage + $displacement > $this->getLastPageNumber()) {
            return $this->getLastPageNumber();
        } else {
            return $this->_curPage + $displacement;
        }
    }

    /**
     * Retrieve collection last page number
     *
     * @return int
     */
    public function getLastPageNumber()
    {
        $collectionSize = (int) $this->getSize();
        if (0 === $collectionSize) {
            return 1;
        } elseif ($this->_pageSize) {
            return ceil($collectionSize / $this->_pageSize);
        } else {
            return 1;
        }
    }

    /**
     * Retrieve collection page size
     *
     * @return int
     */
    public function getPageSize()
    {
        return $this->_pageSize;
    }

    /**
     * Retrieve collection all items count
     *
     * @return int
     */
    public function getSize()
    {
        $this->load();
        if (is_null($this->_totalRecords)) {
            $this->_totalRecords = count($this->getItems());
        }

        return (int) $this->_totalRecords;
    }

    /**
     * Retrieve collection first item
     *
     * @return Varien_Object
     */
    public function getFirstItem()
    {
        $this->load();

        if (count($this->_items)) {
            reset($this->_items);
            return current($this->_items);
        }

        return new $this->_itemObjectClass();
    }

    /**
     * Retrieve collection last item
     *
     * @return Varien_Object
     */
    public function getLastItem()
    {
        $this->load();

        if (count($this->_items)) {
            return end($this->_items);
        }

        return new $this->_itemObjectClass();
    }

    /**
     * Retrieve collection items
     *
     * @return array
     */
    public function getItems()
    {
        $this->load();
        return $this->_items;
    }

    /**
     * Retrieve field values from all items
     *
     * @param   string $colName
     * @return  array
     */
    public function getColumnValues($colName)
    {
        $this->load();

        $col = [];
        foreach ($this->getItems() as $item) {
            $col[] = $item->getData($colName);
        }

        return $col;
    }

    /**
     * Search all items by field value
     *
     * @param   string $column
     * @param   mixed $value
     * @return  array
     */
    public function getItemsByColumnValue($column, $value)
    {
        $this->load();

        $res = [];
        foreach ($this as $item) {
            if ($item->getData($column) == $value) {
                $res[] = $item;
            }
        }

        return $res;
    }

    /**
     * Search first item by field value
     *
     * @param   string $column
     * @param   mixed $value
     * @return  Varien_Object|null
     */
    public function getItemByColumnValue($column, $value)
    {
        $this->load();

        foreach ($this as $item) {
            if ($item->getData($column) == $value) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Adding item to item array
     *
     * @return  $this
     */
    public function addItem(Varien_Object $item)
    {
        $itemId = $this->_getItemId($item);

        if (!is_null($itemId)) {
            if (isset($this->_items[$itemId])) {
                throw new Exception('Item (' . $item::class . ') with the same id "' . $item->getId() . '" already exist');
            }

            $this->_items[$itemId] = $item;
        } else {
            $this->_addItem($item);
        }

        return $this;
    }

    /**
     * Add item that has no id to collection
     *
     * @param Varien_Object $item
     * @return $this
     */
    protected function _addItem($item)
    {
        $this->_items[] = $item;
        return $this;
    }

    /**
     * Retrieve item id
     *
     * @return mixed
     */
    protected function _getItemId(Varien_Object $item)
    {
        return $item->getId();
    }

    /**
     * Retrieve ids of all tems
     *
     * @return array
     */
    public function getAllIds()
    {
        $ids = [];
        foreach ($this->getItems() as $item) {
            $ids[] = $this->_getItemId($item);
        }

        return $ids;
    }

    /**
     * Remove item from collection by item key
     *
     * @param   mixed $key
     * @return  $this
     */
    public function removeItemByKey($key)
    {
        if (isset($this->_items[$key])) {
            unset($this->_items[$key]);
        }

        return $this;
    }

    /**
     * Clear collection
     *
     * @return $this
     */
    public function clear()
    {
        $this->_setIsLoaded(false);
        $this->_items = [];
        return $this;
    }

    /**
     * Walk through the collection and run model method or external callback
     * with optional arguments
     *
     * Returns array with results of callback for each item
     *
     * @param string|callable $callback
     * @return array
     */
    public function walk($callback, array $args = [])
    {
        $results = [];
        $useItemCallback = is_string($callback) && !str_contains($callback, '::');
        foreach ($this->getItems() as $id => $item) {
            if ($useItemCallback) {
                $cb = [$item, $callback];
            } else {
                $cb = $callback;
                array_unshift($args, $item);
            }

            $results[$id] = call_user_func_array($cb, $args);
        }

        return $results;
    }

    /**
     * @param callable $obj_method
     * @param array $args
     */
    public function each($obj_method, $args = [])
    {
        foreach ($args->_items as $k => $item) {
            $args->_items[$k] = call_user_func($obj_method, $item);
        }
    }

    /**
     * Setting data for all collection items
     *
     * @param   mixed $key
     * @param   mixed $value
     * @return  $this
     */
    public function setDataToAll($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->setDataToAll($k, $v);
            }

            return $this;
        }

        foreach ($this->getItems() as $item) {
            $item->setData($key, $value);
        }

        return $this;
    }

    /**
     * Set current page
     *
     * @param   int|null $page
     * @return  $this
     */
    public function setCurPage($page)
    {
        $this->_curPage = $page;
        return $this;
    }

    /**
     * Set collection page size
     *
     * @param   int|null $size
     * @return  $this
     */
    public function setPageSize($size)
    {
        $this->_pageSize = $size;
        return $this;
    }

    /**
     * Set select order
     *
     * @param   string $field
     * @param   string $direction
     * @return  $this
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $this->_orders[$field] = $direction;
        return $this;
    }

    /**
     * Set collection item class name
     *
     * @param   string $className
     * @return  $this
     */
    public function setItemObjectClass($className)
    {
        $className = Mage::getConfig()->getModelClassName($className);
        /**
         * is_subclass_of($className, 'Varien_Object') - Segmentation fault in php 5.2.3
         */
        /*if (!is_subclass_of($className, 'Varien_Object')) {
            throw new Exception($className.' does not extends from Varien_Object');
        }*/
        $this->_itemObjectClass = $className;
        return $this;
    }

    /**
     * Retrieve collection empty item
     *
     * @return Varien_Object
     */
    public function getNewEmptyItem()
    {
        return new $this->_itemObjectClass();
    }

    /**
     * Render sql select conditions
     *
     * @return  Varien_Data_Collection
     */
    protected function _renderFilters()
    {
        return $this;
    }

    /**
     * Render sql select orders
     *
     * @return  Varien_Data_Collection
     */
    protected function _renderOrders()
    {
        return $this;
    }

    /**
     * Render sql select limit
     *
     * @return  Varien_Data_Collection
     */
    protected function _renderLimit()
    {
        return $this;
    }

    /**
     * Set select distinct
     *
     * @param bool $flag
     * @return $this
     */
    public function distinct($flag)
    {
        return $this;
    }

    /**
     * Load data
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        return $this;
    }

    /**
     * Load data
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
     */
    public function load($printQuery = false, $logQuery = false)
    {
        return $this->loadData($printQuery, $logQuery);
    }

    /**
     * Convert collection to XML
     *
     * @return string
     */
    public function toXml()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
        <collection>
           <totalRecords>' . $this->_totalRecords . '</totalRecords>
           <items>';

        foreach ($this as $item) {
            $xml .= $item->toXml();
        }

        return $xml . '</items>
        </collection>';
    }

    /**
     * Convert collection to array
     *
     * @param array $arrRequiredFields
     * @return array
     */
    public function toArray($arrRequiredFields = [])
    {
        $arrItems = [];
        $arrItems['totalRecords'] = $this->getSize();

        $arrItems['items'] = [];
        foreach ($this as $item) {
            $arrItems['items'][] = $item->toArray($arrRequiredFields);
        }

        return $arrItems;
    }

    /**
     * Convert items array to array for select options
     *
     * return items array
     * array(
     *      $index => array(
     *          'value' => mixed
     *          'label' => mixed
     *      )
     * )
     *
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     */
    protected function _toOptionArray($valueField = 'id', $labelField = 'name', $additional = [])
    {
        $data = [];
        $res = [];
        $additional['value'] = $valueField;
        $additional['label'] = $labelField;

        foreach ($this as $item) {
            foreach ($additional as $code => $field) {
                $data[$code] = $item->getData($field);
            }

            $res[] = $data;
        }

        return $res;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray();
    }

    /**
     * @return array
     */
    public function toOptionHash()
    {
        return $this->_toOptionHash();
    }

    /**
     * Convert items array to hash for select options
     *
     * return items hash
     * array($value => $label)
     *
     * @param   string $valueField
     * @param   string $labelField
     * @return  array
     */
    protected function _toOptionHash($valueField = 'id', $labelField = 'name')
    {
        $res = [];
        foreach ($this as $item) {
            $res[$item->getData($valueField)] = $item->getData($labelField);
        }

        return $res;
    }

    /**
     * Retrieve item by id
     *
     * @param   mixed $idValue
     * @return  Varien_Object|null
     */
    public function getItemById($idValue)
    {
        $this->load();
        return $this->_items[$idValue] ?? null;
    }

    /**
     * Implementation of IteratorAggregate::getIterator()
     */
    #[ReturnTypeWillChange]
    public function getIterator()
    {
        $this->load();
        return new ArrayIterator($this->_items);
    }

    /**
     * Retrieve count of collection loaded items
     *
     * @return int
     */
    #[ReturnTypeWillChange]
    public function count()
    {
        $this->load();
        return count($this->_items);
    }

    /**
     * @param string $key
     * @return $this
     */
    public function setCacheKey($key)
    {
        $this->_cacheKey = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getCacheKey()
    {
        return $this->_cacheKey;
    }

    /**
     * @param array $tags
     * @return $this
     */
    public function setCacheTags($tags)
    {
        $this->_cacheTags = $tags;
        return $this;
    }

    /**
     * @return array
     */
    public function getCacheTags()
    {
        return $this->_cacheTags;
    }

    /**
     * @return int
     */
    public function getCacheLifetime()
    {
        return $this->_cacheLifetime;
    }

    /**
     * Retrieve Flag
     *
     * @param string $flag
     * @return mixed
     */
    public function getFlag($flag)
    {
        return $this->_flags[$flag] ?? null;
    }

    /**
     * Set Flag
     *
     * @param string $flag
     * @param mixed $value
     * @return $this
     */
    public function setFlag($flag, $value = null)
    {
        $this->_flags[$flag] = $value;
        return $this;
    }

    /**
     * Has Flag
     *
     * @param string $flag
     * @return bool
     */
    public function hasFlag($flag)
    {
        return array_key_exists($flag, $this->_flags);
    }
}
