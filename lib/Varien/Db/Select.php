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
 * @category    Varien
 * @package     Varien_Db
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class for SQL SELECT generation and results.
 *
 * @method Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract getAdapter()
 * @property Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract $_adapter
 * @method Varien_Db_Select from($name, $cols = '*', $schema = null)
 * @method Varien_Db_Select join($name, $cond, $cols = '*', $schema = null)
 * @method Varien_Db_Select joinInner($name, $cond, $cols = '*', $schema = null)
 * @method Varien_Db_Select joinLeft($name, $cond, $cols = '*', $schema = null)
 * @method Varien_Db_Select joinNatural($name, $cond, $cols = '*', $schema = null)
 * @method Varien_Db_Select joinFull($name, $cond, $cols = '*', $schema = null)
 * @method Varien_Db_Select joinRight($name, $cond, $cols = '*', $schema = null)
 * @method Varien_Db_Select joinCross($name, $cols = '*', $schema = null)
 * @method Varien_Db_Select orWhere($cond, $value = null, $type = null)
 * @method Varien_Db_Select group($spec)
 * @method Varien_Db_Select order($spec)
 * @method Varien_Db_Select limitPage($page, $rowCount)
 * @method Varien_Db_Select forUpdate($flag = true)
 * @method Varien_Db_Select distinct($flag = true)
 * @method Varien_Db_Select reset($part = null)
 * @method Varien_Db_Select columns($cols = '*', $correlationName = null)
 *
 * @category    Varien
 * @package     Varien_Db
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Db_Select extends Zend_Db_Select
{
    const TYPE_CONDITION    = 'TYPE_CONDITION';

    const STRAIGHT_JOIN     = 'straightjoin';

    const SQL_STRAIGHT_JOIN = 'STRAIGHT_JOIN';

    /**
     * Class constructor
     * Add straight join support
     *
     * @param Zend_Db_Adapter_Abstract $adapter
     */
    public function __construct(Zend_Db_Adapter_Abstract $adapter)
    {
        if (!isset(self::$_partsInit[self::STRAIGHT_JOIN])) {
            self::$_partsInit = array(self::STRAIGHT_JOIN => false) + self::$_partsInit;
        }

        parent::__construct($adapter);
    }

    /**
     * Adds a WHERE condition to the query by AND.
     *
     * If a value is passed as the second param, it will be quoted
     * and replaced into the condition wherever a question-mark
     * appears. Array values are quoted and comma-separated.
     *
     * <code>
     * // simplest but non-secure
     * $select->where("id = $id");
     *
     * // secure (ID is quoted but matched anyway)
     * $select->where('id = ?', $id);
     *
     * // alternatively, with named binding
     * $select->where('id = :id');
     * </code>
     *
     * Note that it is more correct to use named bindings in your
     * queries for values other than strings. When you use named
     * bindings, don't forget to pass the values when actually
     * making a query:
     *
     * <code>
     * $db->fetchAll($select, array('id' => 5));
     * </code>
     *
     * @param string   $cond  The WHERE condition.
     * @param string   $value OPTIONAL A single value to quote into the condition.
     * @param null|string $type  OPTIONAL The type of the given value
     * @return Varien_Db_Select This Zend_Db_Select object.
     */
    public function where($cond, $value = null, $type = null)
    {
        if (is_null($value) && is_null($type)) {
            $value = '';
        }
        /**
         * Additional internal type used for really null value
         */
        if ($type == self::TYPE_CONDITION) {
            $type = null;
        }
        if (is_array($value)) {
            $cond = $this->_adapter->quoteInto($cond, $value);
            $value = null;
        }
        return parent::where($cond, $value, $type);
    }

    /**
     * Reset unused LEFT JOIN(s)
     *
     * @return Varien_Db_Select
     */
    public function resetJoinLeft()
    {
        foreach ($this->_parts[self::FROM] as $tableId => $tableProp) {
            if ($tableProp['joinType'] == self::LEFT_JOIN) {
                $useJoin = false;
                foreach ($this->_parts[self::COLUMNS] as $columnEntry) {
                    list($correlationName, $column) = $columnEntry;
                    if ($column instanceof Zend_Db_Expr) {
                        if ($this->_findTableInCond($tableId, $column)
                            || $this->_findTableInCond($tableProp['tableName'], $column)) {
                            $useJoin = true;
                        }
                    } else {
                        if ($correlationName == $tableId) {
                            $useJoin = true;
                        }
                    }
                }
                foreach ($this->_parts[self::WHERE] as $where) {
                    if ($this->_findTableInCond($tableId, $where)
                        || $this->_findTableInCond($tableProp['tableName'], $where)) {
                        $useJoin = true;
                    }
                }

                $joinUseInCond  = $useJoin;
                $joinInTables   = array();

                foreach ($this->_parts[self::FROM] as $tableCorrelationName => $table) {
                    if ($tableCorrelationName == $tableId) {
                        continue;
                    }
                    if (!empty($table['joinCondition'])) {
                        if ($this->_findTableInCond($tableId, $table['joinCondition'])
                        || $this->_findTableInCond($tableProp['tableName'], $table['joinCondition'])) {
                            $useJoin = true;
                            $joinInTables[] = $tableCorrelationName;
                        }
                    }
                }

                if (!$useJoin) {
                    unset($this->_parts[self::FROM][$tableId]);
                } else {
                    $this->_parts[self::FROM][$tableId]['useInCond'] = $joinUseInCond;
                    $this->_parts[self::FROM][$tableId]['joinInTables'] = $joinInTables;
                }
            }
        }

        $this->_resetJoinLeft();

        return $this;
    }

    /**
     * Validate LEFT joins, and remove it if not exists
     *
     * @return Varien_Db_Select
     */
    protected function _resetJoinLeft()
    {
        foreach ($this->_parts[self::FROM] as $tableId => $tableProp) {
            if ($tableProp['joinType'] == self::LEFT_JOIN) {
                if ($tableProp['useInCond']) {
                    continue;
                }

                $used = false;
                foreach ($tableProp['joinInTables'] as $table) {
                    if (isset($this->_parts[self::FROM][$table])) {
                        $used = true;
                        break;
                    }
                }

                if (!$used) {
                    unset($this->_parts[self::FROM][$tableId]);
                    return $this->_resetJoinLeft();
                }
            }
        }

        return $this;
    }

    /**
     * Find table name in condition (where, column)
     *
     * @param string $table
     * @param string $cond
     * @return bool
     */
    protected function _findTableInCond($table, $cond)
    {
        $quote = $this->_adapter->getQuoteIdentifierSymbol();

        if (strpos($cond, $quote . $table . $quote . '.') !== false) {
            return true;
        }

        $position = 0;
        $result   = 0;
        $needle   = array();
        while (is_integer($result)) {
            $result = strpos($cond, $table . '.', $position);

            if (is_integer($result)) {
                $needle[] = $result;
                $position = ($result + strlen($table) + 1);
            }
        }

        if (!$needle) {
            return false;
        }

        foreach ($needle as $position) {
            if ($position == 0) {
                return true;
            }
            if (!preg_match('#[a-z0-9_]#is', substr($cond, $position - 1, 1))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Populate the {@link $_parts} 'join' key
     *
     * Does the dirty work of populating the join key.
     *
     * The $name and $cols parameters follow the same logic
     * as described in the from() method.
     *
     * @param  null|string $type Type of join; inner, left, and null are currently supported
     * @param  array|string|Zend_Db_Expr $name Table name
     * @param  string $cond Join on this condition
     * @param  array|string $cols The columns to select from the joined table
     * @param  string $schema The database name to specify, if any.
     * @return Zend_Db_Select This Zend_Db_Select object
     * @throws Zend_Db_Select_Exception
     */
    protected function _join($type, $name, $cond, $cols, $schema = null)
    {
        if ($type == self::INNER_JOIN && empty($cond)) {
            $type = self::CROSS_JOIN;
        }
        return parent::_join($type, $name, $cond, $cols, $schema);
    }

    /**
     * Sets a limit count and offset to the query.
     *
     * @param int $count OPTIONAL The number of rows to return.
     * @param int $offset OPTIONAL Start returning after this many rows.
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    public function limit($count = null, $offset = null)
    {
        if ($count === null) {
            $this->reset(self::LIMIT_COUNT);
        } else {
            $this->_parts[self::LIMIT_COUNT]  = (int) $count;
        }
        if ($offset === null) {
            $this->reset(self::LIMIT_OFFSET);
        } else {
            $this->_parts[self::LIMIT_OFFSET] = (int) $offset;
        }
        return $this;
    }

    /**
     * Cross Table Update From Current select
     *
     * @param string|array $table
     * @return string
     */
    public function crossUpdateFromSelect($table)
    {
        return $this->getAdapter()->updateFromSelect($this, $table);
    }

    /**
     * Insert to table from current select
     *
     * @param string $tableName
     * @param array $fields
     * @param bool $onDuplicate
     * @return string
     */
    public function insertFromSelect($tableName, $fields = array(), $onDuplicate = true)
    {
        $mode = $onDuplicate ? Varien_Db_Adapter_Interface::INSERT_ON_DUPLICATE : false;
        return $this->getAdapter()->insertFromSelect($this, $tableName, $fields, $mode);
    }

    /**
     * Generate INSERT IGNORE query to the table from current select
     *
     * @param string $tableName
     * @param array $fields
     * @return string
     */
    public function insertIgnoreFromSelect($tableName, $fields = array())
    {
        return $this->getAdapter()
            ->insertFromSelect($this, $tableName, $fields, Varien_Db_Adapter_Interface::INSERT_IGNORE);
    }

    /**
     * Retrieve DELETE query from select
     *
     * @param string $table The table name or alias
     * @return string
     */
    public function deleteFromSelect($table)
    {
        return $this->getAdapter()->deleteFromSelect($this, $table);
    }

    /**
     * Modify (hack) part of the structured information for the currect query
     *
     * @param string $part
     * @param mixed $value
     * @return Varien_Db_Select
     * @throws Zend_Db_Select_Exception
     */
    public function setPart($part, $value)
    {
        $part = strtolower($part);
        if (!array_key_exists($part, $this->_parts)) {
            throw new Zend_Db_Select_Exception("Invalid Select part '{$part}'");
        }
        $this->_parts[$part] = $value;
        return $this;
    }

    /**
     * Use a STRAIGHT_JOIN for the SQL Select
     *
     * @param bool $flag Whether or not the SELECT use STRAIGHT_JOIN (default true).
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    public function useStraightJoin($flag = true)
    {
        $this->_parts[self::STRAIGHT_JOIN] = (bool) $flag;
        return $this;
    }

    /**
     * Render STRAIGHT_JOIN clause
     *
     * @param string   $sql SQL query
     * @return string
     */
    protected function _renderStraightjoin($sql)
    {
        if ($this->_adapter->supportStraightJoin() && !empty($this->_parts[self::STRAIGHT_JOIN])) {
            $sql .= ' ' . self::SQL_STRAIGHT_JOIN;
        }

        return $sql;
    }

    /**
     * Adds to the internal table-to-column mapping array.
     *
     * @param  string $tbl The table/join the columns come from.
     * @param  array|string $cols The list of columns; preferably as
     * an array, but possibly as a string containing one column.
     * @param  bool|string True if it should be prepended, a correlation name if it should be inserted
     * @return void
     */
    protected function _tableCols($correlationName, $cols, $afterCorrelationName = null)
    {
        if (!is_array($cols)) {
            $cols = array($cols);
        }

        foreach ($cols as $k => $v) {
            if ($v instanceof Varien_Db_Select) {
                $cols[$k] = new Zend_Db_Expr(sprintf('(%s)', $v->assemble()));
            }
        }

        return parent::_tableCols($correlationName, $cols, $afterCorrelationName);
    }

    /**
     * Adds the random order to query
     *
     * @param string $field     integer field name
     * @return Varien_Db_Select
     */
    public function orderRand($field = null)
    {
        $this->_adapter->orderRand($this, $field);
        return $this;
    }

    /**
     * Render FOR UPDATE clause
     *
     * @param string   $sql SQL query
     * @return string
     */
    protected function _renderForupdate($sql)
    {
        if ($this->_parts[self::FOR_UPDATE]) {
            $sql = $this->_adapter->forUpdate($sql);
        }

        return $sql;
    }
    /**
     * Add EXISTS clause
     *
     * @param  Varien_Db_Select $select
     * @param  string           $joinCondition
     * @param   bool            $isExists
     * @return Varien_Db_Select
     */
    public function exists($select, $joinCondition, $isExists = true)
    {
        if ($isExists) {
            $exists = 'EXISTS (%s)';
        } else {
            $exists = 'NOT EXISTS (%s)';
        }
        $select->reset(self::COLUMNS)
            ->columns(array(new Zend_Db_Expr('1')))
            ->where($joinCondition);

        $exists = sprintf($exists, $select->assemble());

        $this->where($exists);
        return $this;
    }
}
