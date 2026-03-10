<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Db
 */

/**
 * Class for SQL SELECT generation and results.
 *
 * @property Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract $_adapter
 * @method   $this                                                columns($cols = '*', $correlationName = null)
 * @method   $this                                                distinct($flag = true)
 * @method   $this                                                forUpdate($flag = true)
 * @method   $this                                                from($name, $cols = '*', $schema = null)
 * @method   Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract getAdapter()
 * @method   $this                                                group($spec)
 * @method   $this                                                join($name, $cond, $cols = '*', $schema = null)
 * @method   $this                                                joinCross($name, $cols = '*', $schema = null)
 * @method   $this                                                joinFull($name, $cond, $cols = '*', $schema = null)
 * @method   $this                                                joinInner($name, $cond, $cols = '*', $schema = null)
 * @method   $this                                                joinLeft($name, $cond, $cols = '*', $schema = null)
 * @method   $this                                                joinNatural($name, $cond, $cols = '*', $schema = null)
 * @method   $this                                                joinRight($name, $cond, $cols = '*', $schema = null)
 * @method   $this                                                limitPage($page, $rowCount)
 * @method   $this                                                order($spec)
 * @method   $this                                                orWhere($cond, $value = null, $type = null)
 * @method   $this                                                reset($part = null)
 *
 * @package    Varien_Db
 */
class Varien_Db_Select extends Zend_Db_Select
{
    public const TYPE_CONDITION    = 'TYPE_CONDITION';

    public const STRAIGHT_JOIN     = 'straightjoin';

    public const SQL_STRAIGHT_JOIN = 'STRAIGHT_JOIN';

    /**
     * Class constructor
     * Add straight join support
     */
    public function __construct(Zend_Db_Adapter_Abstract $adapter)
    {
        if (!isset(self::$_partsInit[self::STRAIGHT_JOIN])) {
            self::$_partsInit = [self::STRAIGHT_JOIN => false] + self::$_partsInit;
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
     * @param  string                                                  $cond  the WHERE condition
     * @param  null|array|float|int|string|Zend_Db_Expr|Zend_Db_Select $value OPTIONAL A single value to quote into the condition
     * @param  null|int|string                                         $type  OPTIONAL The type of the given value e.g. Zend_Db::INT_TYPE, "INT"
     * @return $this
     */
    public function where($cond, $value = null, $type = null)
    {
        if (is_null($value) && is_null($type)) {
            $value = '';
        }

        /**
         * Additional internal type used for really null value
         * cast to string, to prevent false matching 0 == "TYPE_CONDITION"
         */
        if ((string) $type === self::TYPE_CONDITION) {
            $type = null;
        }

        if (is_array($value)) {
            $cond = $this->_adapter->quoteInto($cond, $value, $type);
            $value = null;
        }

        return parent::where($cond, $value, $type);
    }

    /**
     * Reset unused LEFT JOIN(s)
     *
     * @return $this
     */
    public function resetJoinLeft()
    {
        foreach ($this->_parts[self::FROM] as $tableId => $tableProp) {
            if ($tableProp['joinType'] == self::LEFT_JOIN) {
                $useJoin = false;
                foreach ($this->_parts[self::COLUMNS] as $columnEntry) {
                    [$correlationName, $column] = $columnEntry;
                    if ($column instanceof Zend_Db_Expr) {
                        if ($this->_findTableInCond($tableId, $column)
                            || $this->_findTableInCond($tableProp['tableName'], $column)
                        ) {
                            $useJoin = true;
                        }
                    } elseif ($correlationName == $tableId) {
                        $useJoin = true;
                    }
                }

                foreach ($this->_parts[self::WHERE] as $where) {
                    if ($this->_findTableInCond($tableId, $where)
                        || $this->_findTableInCond($tableProp['tableName'], $where)
                    ) {
                        $useJoin = true;
                    }
                }

                $joinUseInCond  = $useJoin;
                $joinInTables   = [];

                foreach ($this->_parts[self::FROM] as $tableCorrelationName => $table) {
                    if ($tableCorrelationName == $tableId) {
                        continue;
                    }

                    if (!empty($table['joinCondition'])) {
                        if ($this->_findTableInCond($tableId, $table['joinCondition'])
                            || $this->_findTableInCond($tableProp['tableName'], $table['joinCondition'])
                        ) {
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
     * @return $this
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
     * @param  string $table
     * @param  string $cond
     * @return bool
     */
    protected function _findTableInCond($table, $cond)
    {
        $cond  = (string) $cond;
        $quote = $this->_adapter->getQuoteIdentifierSymbol();

        if (str_contains($cond, $quote . $table . $quote . '.')) {
            return true;
        }

        $position = 0;
        $result   = 0;
        $needle   = [];
        while (is_int($result)) {
            $result = strpos($cond, $table . '.', $position);
            if (is_int($result)) {
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
     * Populate the $_parts 'join' key
     *
     * @param  null|string               $type   Type of join; inner, left, and null are currently supported
     * @param  array|string|Zend_Db_Expr $name   Table name
     * @param  string                    $cond   Join on this condition
     * @param  array|string              $cols   The columns to select from the joined table
     * @param  string                    $schema the database name to specify, if any
     * @return $this                     This Zend_Db_Select object
     * @throws Zend_Db_Select_Exception
     * @see $_parts
     *
     * Does the dirty work of populating the join key.
     *
     * The $name and $cols parameters follow the same logic
     * as described in the from() method.
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
     * @param  int   $count  OPTIONAL The number of rows to return
     * @param  int   $offset OPTIONAL Start returning after this many rows
     * @return $this this Zend_Db_Select object
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
     * @param  array|string $table
     * @return string
     */
    public function crossUpdateFromSelect($table)
    {
        return $this->getAdapter()->updateFromSelect($this, $table);
    }

    /**
     * Insert to table from current select
     *
     * @param  string $tableName
     * @param  array  $fields
     * @param  bool   $onDuplicate
     * @return string
     */
    public function insertFromSelect($tableName, $fields = [], $onDuplicate = true)
    {
        $mode = $onDuplicate ? Varien_Db_Adapter_Interface::INSERT_ON_DUPLICATE : false;
        return $this->getAdapter()->insertFromSelect($this, $tableName, $fields, $mode);
    }

    /**
     * Generate INSERT IGNORE query to the table from current select
     *
     * @param  string $tableName
     * @param  array  $fields
     * @return string
     */
    public function insertIgnoreFromSelect($tableName, $fields = [])
    {
        return $this->getAdapter()
            ->insertFromSelect($this, $tableName, $fields, Varien_Db_Adapter_Interface::INSERT_IGNORE);
    }

    /**
     * Retrieve DELETE query from select
     *
     * @param  string $table The table name or alias
     * @return string
     */
    public function deleteFromSelect($table)
    {
        return $this->getAdapter()->deleteFromSelect($this, $table);
    }

    /**
     * Modify (hack) part of the structured information for the current query
     *
     * @param  string                   $part
     * @param  mixed                    $value
     * @return $this
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
     * @param  bool  $flag whether or not the SELECT use STRAIGHT_JOIN (default true)
     * @return $this this Zend_Db_Select object
     */
    public function useStraightJoin($flag = true)
    {
        $this->_parts[self::STRAIGHT_JOIN] = (bool) $flag;
        return $this;
    }

    /**
     * Render STRAIGHT_JOIN clause
     *
     * @param  string $sql SQL query
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
     * @inheritDoc
     */
    protected function _tableCols($correlationName, $cols, $afterCorrelationName = null)
    {
        if (!is_array($cols)) {
            $cols = [$cols];
        }

        foreach ($cols as $key => $value) {
            if ($value instanceof Varien_Db_Select) {
                $cols[$key] = new Zend_Db_Expr(sprintf('(%s)', $value->assemble()));
            }
        }

        parent::_tableCols($correlationName, $cols, $afterCorrelationName);
    }

    /**
     * Adds the random order to query
     *
     * @param  string $field integer field name
     * @return $this
     */
    public function orderRand($field = null)
    {
        $this->_adapter->orderRand($this, $field);
        return $this;
    }

    /**
     * Render FOR UPDATE clause
     *
     * @param  string $sql SQL query
     * @return string
     */
    protected function _renderForupdate($sql)
    {
        if ($this->_parts[self::FOR_UPDATE]) {
            return $this->_adapter->forUpdate($sql);
        }

        return $sql;
    }

    /**
     * Add EXISTS clause
     *
     * @param  Varien_Db_Select $select
     * @param  string           $joinCondition
     * @param  bool             $isExists
     * @return $this
     */
    public function exists($select, $joinCondition, $isExists = true)
    {
        if ($isExists) {
            $exists = 'EXISTS (%s)';
        } else {
            $exists = 'NOT EXISTS (%s)';
        }

        $select->reset(self::COLUMNS)
            ->columns([new Zend_Db_Expr('1')])
            ->where($joinCondition);

        $exists = sprintf($exists, $select->assemble());

        $this->where($exists);
        return $this;
    }
}
