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
 * @category   Varien
 * @package    Varien_Data
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Base items collection class
 *
 * @category   Varien
 * @package    Varien_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Data_Collection_Db extends Varien_Data_Collection
{
    /**
     * DB connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_conn;

    /**
     * Select oblect
     *
     * @var Zend_Db_Select
     */
    protected $_select;

    protected $_cacheConf = null;

    /**
     * Identifier fild name for collection items
     *
     * Can be used by collections with items without defined
     *
     * @var string
     */
    protected $_idFieldName;

    protected $_bindParams = array();

    public function __construct($conn=null)
    {
        parent::__construct();

        if (!is_null($conn)) {
            $this->setConnection($conn);
        }
    }

    public function addBindParam($name, $value)
    {
        $this->_bindParams[$name] = $value;
        return $this;
    }

    public function initCache($object, $idPrefix, $tags)
    {
        $this->_cacheConf = array(
            'object'    => $object,
            'prefix'    => $idPrefix,
            'tags'      => $tags
        );
        return $this;
    }

    protected function _setIdFieldName($fieldName)
    {
        $this->_idFieldName = $fieldName;
        return $this;
    }

    public function getIdFieldName()
    {
        return $this->_idFieldName;
    }

    protected function _getItemId(Varien_Object $item)
    {
        if ($field = $this->getIdFieldName()) {
            return $item->getData($field);
        }
        return parent::_getItemId($item);
    }

    public function setConnection($conn)
    {
        if (!$conn instanceof Zend_Db_Adapter_Abstract) {
            throw new Zend_Exception('dbModel read resource does not implement Zend_Db_Adapter_Abstract');
        }

        $this->_conn = $conn;
        $this->_select = $this->_conn->select();
    }

    /**
     * Get Zend_Db_Select instance
     *
     * @return Zend_Db_Select
     */
    public function getSelect()
    {
        return $this->_select;
    }

    /**
     * Retrieve connection object
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function getConnection()
    {
        return $this->_conn;
    }

    /**
     * Get collection size
     *
     * @return int
     */
    public function getSize()
    {
        if (is_null($this->_totalRecords)) {
            $sql = $this->getSelectCountSql();
            $this->_totalRecords = $this->getConnection()->fetchOne($sql, $this->_bindParams);
        }
        return intval($this->_totalRecords);
    }

    /**
     * Get sql for get record count
     *
     * @return  string
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);

        $sql = $countSelect->__toString();
        $sql = preg_replace('/^select\s+.+?\s+from\s+/is', 'select count(*) from ', $sql);
        return $sql;
    }

    /**
     * Get sql select string or object
     *
     * @param   bool $stringMode
     * @return  string || Zend_Db_Select
     */
    function getSelectSql($stringMode = false)
    {
        if ($stringMode) {
            return $this->_select->__toString();
        }
        return $this->_select;
    }

    /**
     * Add select order
     *
     * @param   string $field
     * @param   string $direction
     * @return  Varien_Data_Collection_Db
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        return $this->_setOrder($field, $direction);
    }

    /**
     * self::setOrder() alias
     *
     * @param string $field
     * @param string $direction
     * @return Varien_Data_Collection_Db
     */
    public function addOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        return $this->_setOrder($field, $direction);
    }

    /**
     * Add select order to the beginning
     *
     * @param string $field
     * @param string $direction
     * @return Varien_Data_Collection_Db
     */
    public function unshiftOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        return $this->_setOrder($field, $direction, true);
    }

    /**
     * Add ORDERBY to the end or to the beginning
     *
     * @param string $field
     * @param string $direction
     * @param bool $unshift
     * @return Varien_Data_Collection_Db
     */
    private function _setOrder($field, $direction, $unshift = false)
    {
        $direction = (strtoupper($direction) == self::SORT_ORDER_ASC) ? self::SORT_ORDER_ASC : self::SORT_ORDER_DESC;
        // emulate associative unshift
        if ($unshift) {
            $orders = array($field => new Zend_Db_Expr($field . ' ' . $direction));
            foreach ($this->_orders as $key => $expression) {
                if (!isset($orders[$key])) {
                    $orders[$key] = $expression;
                }
            }
            $this->_orders = $orders;
        }
        else {
            $this->_orders[$field] = new Zend_Db_Expr($field . ' ' . $direction);
        }
        return $this;
    }

    /**
     * Render sql select conditions
     *
     * @return  Varien_Data_Collection_Db
     */
    protected function _renderFilters()
    {
        if ($this->_isFiltersRendered) {
            return $this;
        }

        foreach ($this->_filters as $filter) {
            switch ($filter['type']) {
                case 'or' :
                    $condition = $this->_conn->quoteInto($filter['field'].'=?', $filter['value']);
                    $this->_select->orWhere($condition);
                    break;
                case 'string' :
                    $this->_select->where($filter['value']);
                    break;
                default:
                    $condition = $this->_conn->quoteInto($filter['field'].'=?', $filter['value']);
                    $this->_select->where($condition);
            }
        }
        $this->_isFiltersRendered = true;
        return $this;
    }

    /**
     * Add field filter to collection
     *
     * If $attribute is an array will add OR condition with following format:
     * array(
     *     array('attribute'=>'firstname', 'like'=>'test%'),
     *     array('attribute'=>'lastname', 'like'=>'test%'),
     * )
     *
     * @see self::_getConditionSql for $condition
     * @param string|array $attribute
     * @param null|string|array $condition
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addFieldToFilter($field, $condition=null)
    {
        $field = $this->_getMappedField($field);
        $this->_select->where($this->_getConditionSql($field, $condition));
        return $this;
    }

    /**
     * Try to get mapped field name for filter to collection
     *
     * @param string
     * @return string
     */
    protected function _getMappedField($field)
    {
        $mappedFiled = $field;

        $mapper = $this->_getMapper();

        if (isset($mapper['fields'][$field])) {
            $mappedFiled = $mapper['fields'][$field];
        }

        return $mappedFiled;
    }

    protected function _getMapper()
    {
        if (isset($this->_map)) {
            return $this->_map;
        }
        else {
            return false;
        }
    }

    /**
     * Build SQL statement for condition
     *
     * If $condition integer or string - exact value will be filtered
     *
     * If $condition is array is - one of the following structures is expected:
     * - array("from"=>$fromValue, "to"=>$toValue)
     * - array("like"=>$likeValue)
     * - array("neq"=>$notEqualValue)
     * - array("in"=>array($inValues))
     * - array("nin"=>array($notInValues))
     *
     * If non matched - sequential array is expected and OR conditions
     * will be built using above mentioned structure
     *
     * @param string $fieldName
     * @param integer|string|array $condition
     * @return string
     */
    protected function _getConditionSql($fieldName, $condition) {
    	if (is_array($fieldName)) {
    		foreach ($fieldName as $f) {
                $orSql = array();
                foreach ($condition as $orCondition) {
                    $orSql[] = "(".$this->_getConditionSql($f[0], $f[1]).")";
                }
                $sql = "(".join(" or ", $orSql).")";
    		}
    		return $sql;
    	}

        $sql = '';
        $fieldName = $this->_getConditionFieldName($fieldName);
        if (is_array($condition)) {
            if (isset($condition['from']) || isset($condition['to'])) {
                if (isset($condition['from'])) {
                    if (empty($condition['date'])) {
                        if ( empty($condition['datetime'])) {
                            $from = $condition['from'];
                        }
                        else {
                            $from = $this->getConnection()->convertDateTime($condition['from']);
                        }
                    }
                    else {
                        $from = $this->getConnection()->convertDate($condition['from']);
                    }
                    $sql.= $this->getConnection()->quoteInto("$fieldName >= ?", $from);
                }
                if (isset($condition['to'])) {
                    $sql.= empty($sql) ? '' : ' and ';

                    if (empty($condition['date'])) {
                        if ( empty($condition['datetime'])) {
                            $to = $condition['to'];
                        }
                        else {
                            $to = $this->getConnection()->convertDateTime($condition['to']);
                        }
                    }
                    else {
                        $to = $this->getConnection()->convertDate($condition['to']);
                    }

                    $sql.= $this->getConnection()->quoteInto("$fieldName <= ?", $to);
                }
            }
            elseif (isset($condition['eq'])) {
                $sql = $this->getConnection()->quoteInto("$fieldName = ?", $condition['eq']);
            }
            elseif (isset($condition['neq'])) {
                $sql = $this->getConnection()->quoteInto("$fieldName != ?", $condition['neq']);
            }
            elseif (isset($condition['like'])) {
                $sql = $this->getConnection()->quoteInto("$fieldName like ?", $condition['like']);
            }
            elseif (isset($condition['nlike'])) {
                $sql = $this->getConnection()->quoteInto("$fieldName not like ?", $condition['nlike']);
            }
            elseif (isset($condition['in'])) {
                $sql = $this->getConnection()->quoteInto("$fieldName in (?)", $condition['in']);
            }
            elseif (isset($condition['nin'])) {
                $sql = $this->getConnection()->quoteInto("$fieldName not in (?)", $condition['nin']);
            }
            elseif (isset($condition['is'])) {
                $sql = $this->getConnection()->quoteInto("$fieldName is ?", $condition['is']);
            }
            elseif (isset($condition['notnull'])) {
                $sql = "$fieldName is NOT NULL";
            }
            elseif (isset($condition['null'])) {
                $sql = "$fieldName is NULL";
            }
            elseif (isset($condition['moreq'])) {
                $sql = $this->getConnection()->quoteInto("$fieldName >= ?", $condition['moreq']);
            }
            elseif (isset($condition['gt'])) {
                $sql = $this->getConnection()->quoteInto("$fieldName > ?", $condition['gt']);
            }
            elseif (isset($condition['lt'])) {
                $sql = $this->getConnection()->quoteInto("$fieldName < ?", $condition['lt']);
            }
            elseif (isset($condition['gteq'])) {
                $sql = $this->getConnection()->quoteInto("$fieldName >= ?", $condition['gteq']);
            }
            elseif (isset($condition['lteq'])) {
                $sql = $this->getConnection()->quoteInto("$fieldName <= ?", $condition['lteq']);
            }
            elseif (isset($condition['finset'])) {
                $sql = $this->getConnection()->quoteInto("find_in_set(?,$fieldName)", $condition['finset']);
            }
            else {
                $orSql = array();
                foreach ($condition as $orCondition) {
                    $orSql[] = "(".$this->_getConditionSql($fieldName, $orCondition).")";
                }
                $sql = "(".join(" or ", $orSql).")";
            }
        } else {
            $sql = $this->getConnection()->quoteInto("$fieldName = ?", (string)$condition);
        }
        return $sql;
    }

    protected function _getConditionFieldName($fieldName)
    {
        return $fieldName;
    }

    /**
     * Build SQL statement for condition
     *
     * If $condition integer or string - exact value will be filtered
     *
     * If $condition is array is - one of the following structures is expected:
     * - array("from"=>$fromValue, "to"=>$toValue)
     * - array("like"=>$likeValue)
     * - array("neq"=>$notEqualValue)
     * - array("in"=>array($inValues))
     * - array("nin"=>array($notInValues))
     *
     * If non matched - sequential array is expected and OR conditions
     * will be built using above mentioned structure
     *
     * @param string $fieldName
     * @param integer|string|array $condition
     * @return string
     *
    protected function _getConditionSql($fieldName, $condition) {
        if (!is_array($condition)) {
            $condition = array('='=>$condition);
        }

        if (!empty($condition['datetime'])) {
            $argType = 'datetime';
        } elseif (!empty($condition['date'])) {
            $argType = 'date';
        } else {
            $argType = null;
        }

        $sql = '';
        foreach ($condition as $k=>$v) {
            $and = array();
            $or = array();

            if (is_numeric($k)) {
                $or[] = '('.$this->_getConditionSql($fieldName, $v).')';
            }

            switch ($k) {
                case 'null':
                    if ($v==true) {
                        $and[] = "$fieldName is null";
                    } elseif ($v==false) {
                        $and[] = "$fieldName is not null";
                    }
                    break;

                case 'is':
                    $and[] = $this->_read->quoteInto("$fieldName is ?", $v);
                    break;

                default:
                    if (is_scalar($v)) {
                        switch ($argType) {
                            case 'date':
                                $v = $this->_read->convertDate($v);
                                break;

                            case 'datetime':
                                $v = $this->_read->convertDateTime($v);
                                break;
                        }
                    }
            }

            switch ($k) {
                case '>=': case 'from': case 'gte': case 'gteq':
                    $and[] = $this->_read->quoteInto("$fieldName >= ?", $v);
                    break;

                case '<=': case 'to': case 'lte': case 'lteq':
                    $and[] = $this->_read->quoteInto("$fieldName <= ?", $v);
                    break;

                case '>': case 'gt':
                    $and[] = $this->_read->quoteInto("$fieldName > ?", $v);
                    break;

                case '<': case 'lt':
                    $and[] = $this->_read->quoteInto("$fieldName < ?", $v);
                    break;

                case '=': case '==': case 'eq':
                    $and[] = $this->_read->quoteInto("$fieldName = ?", $v);
                    break;

                case '<>': case '!=': case 'neq':
                    $and[] = $this->_read->quoteInto("$fieldName <> ?", $v);
                    break;

                case '%': case 'like':
                    $and[] = $this->_read->quoteInto("$fieldName like ?", $v);
                    break;

                case '!%': case 'nlike':
                    $and[] = $this->_read->quoteInto("$fieldName not like ?", $v);
                    break;

                case '()': case 'in':
                    $and[] = $this->_read->quoteInto("$fieldName in (?)", $v);
                    break;

                case '!()': case 'nin':
                    $and[] = $this->_read->quoteInto("$fieldName not in (?)", $v);
                    break;
            }
        }
        if (!empty($and)) {
            $sql = join(" and ", $and);
        }
        if (!empty($or)) {
            if (!empty($sql)) {
                array_push($or, $sql);
            }
            $sql = '('.join(" or ", $or).')';
        }
        return $sql;
    }
*/

    /**
     * Render sql select orders
     *
     * @return  Varien_Data_Collection_Db
     */
    protected function _renderOrders()
    {
        foreach ($this->_orders as $orderExpr) {
            $this->_select->order($orderExpr);
        }
        return $this;
    }

    /**
     * Render sql select limit
     *
     * @return  Varien_Data_Collection_Db
     */
    protected function _renderLimit()
    {
        if($this->_pageSize){
            $this->_select->limitPage($this->getCurPage(), $this->_pageSize);
        }

        return $this;
    }

    /**
     * Set select distinct
     *
     * @param bool $flag
     */
    public function distinct($flag)
    {
        $this->_select->distinct($flag);
        return $this;
    }

    /**
     * Load data
     *
     * @return  Varien_Data_Collection_Db
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        $this->_renderFilters()
             ->_renderOrders()
             ->_renderLimit();

        $this->printLogQuery($printQuery, $logQuery);

        $data = $this->_fetchAll($this->_select);
        if (is_array($data)) {
            foreach ($data as $row) {
                $item = $this->getNewEmptyItem();
                if ($this->getIdFieldName()) {
                    $item->setIdFieldName($this->getIdFieldName());
                }
                $item->addData($row);
                $this->addItem($item);
            }
        }

        $this->_setIsLoaded();
        $this->_afterLoad();
        return $this;
    }

    protected function _afterLoad()
    {
        return $this;
    }

    public function loadData($printQuery = false, $logQuery = false)
    {
        return $this->load($printQuery, $logQuery);
    }

    /**
     * Print and/or log query
     *
     * @param boolean $printQuery
     * @param boolean $logQuery
     * @return  Varien_Data_Collection_Db
     */
    public function printLogQuery($printQuery = false, $logQuery = false, $sql = null) {
        if ($printQuery) {
            echo is_null($sql) ? $this->getSelect()->__toString() : $sql;
        }

        if ($logQuery){
            Mage::log(is_null($sql) ? $this->getSelect()->__toString() : $sql);
        }
        return $this;
    }

    /**
     * Reset collection
     *
     * @return Varien_Data_Collection_Db
     */
    protected function _reset()
    {
        $this->getSelect()->reset();
        $this->_initSelect();
        $this->_setIsLoaded(false);
        $this->_items = array();
        return $this;
    }

    /**
     * Fetch collection data
     *
     * @param   Zend_Db_Select $select
     * @return  array
     */
    protected function _fetchAll($select)
    {
        if ($this->_canUseCache() && ($object = $this->_getCacheInstance())) {
            if ($data = $object->load($this->_getSelectCacheId($select))) {
                $data = unserialize($data);
            }
            else {
                $data = $this->getConnection()->fetchAll($select, $this->_bindParams);
                $object->save(serialize($data), $this->_getSelectCacheId($select), $this->_getCacheTags());
            }
        } else {
            $data = $this->getConnection()->fetchAll($select, $this->_bindParams);
        }
        return $data;
    }

    protected function _canUseCache()
    {
        return false;
    }

    protected function _getSelectCacheId($select)
    {
        $id = md5($select->__toString());
        if (isset($this->_cacheConf['prefix'])) {
            $id = $this->_cacheConf['prefix'].'_'.$id;
        }
        return $id;
    }

    /**
     * Retrieve cache instance
     *
     * @return Zend_Cache_Core
     */
    protected function _getCacheInstance()
    {
        if (isset($this->_cacheConf['object'])) {
            return $this->_cacheConf['object'];
        }
        return false;
    }

    protected function _getCacheTags()
    {
        if (isset($this->_cacheConf['tags'])) {
            return $this->_cacheConf['tags'];
        }
        return array();
    }
}