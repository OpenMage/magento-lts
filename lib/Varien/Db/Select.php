<?php
class Varien_Db_Select extends Zend_Db_Select
{
    /**
     * Class constructor
     *
     * @param Zend_Db_Adapter_Abstract $adapter
     */
    public function __construct(Zend_Db_Adapter_Abstract $adapter)
    {
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
     * @param constant $type  OPTIONAL The type of the given value
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    public function where($cond, $value = null, $type = null)
    {
        if (is_null($value) && is_null($type)) {
            $value = '';
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
                    }
                    else {
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
                }
                else {
                    $this->_parts[self::FROM][$tableId]['useInCond'] = $joinUseInCond;
                    $this->_parts[self::FROM][$tableId]['joinInTables'] = $joinInTables;
                }
            }
        }

        $this->_resetJoinLeft();

        return $this;
    }

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
     * Cross Table Update From Current select
     *
     * @param string|array $table
     * @return string
     */
    public function crossUpdateFromSelect($table) {
        if (!is_array($table)) {
            $table = array($table => $table);
        }
        $keys = array_keys($table);
        $tableAlias = $keys[0];
        $tableName  = $table[$keys[0]];

        $sql = "UPDATE `{$tableName}`";
        if ($tableAlias != $tableName) {
            $sql .= " AS `{$tableAlias}`";
        }

        // render FROM
        $from = array();

        foreach ($this->_parts[self::FROM] as $correlationName => $table) {
            $tmp = '';
            $tmp .= ' ' . strtoupper($table['joinType']) . ' ';

            $tmp .= $this->_getQuotedSchema($table['schema']);
            $tmp .= $this->_getQuotedTable($table['tableName'], $correlationName);

            // Add join conditions (if applicable)
            if (! empty($table['joinCondition'])) {
                $tmp .= ' ' . self::SQL_ON . ' ' . $table['joinCondition'];
            }

            // Add the table name and condition add to the list
            $from[] = $tmp;
        }

        // Add the list of all joins
        if (!empty($from)) {
            $sql .= implode("\n", $from);
        }

        // render UPDATE SET
        $columns = array();
        foreach ($this->_parts[self::COLUMNS] as $columnEntry) {
            list($correlationName, $column, $alias) = $columnEntry;
            if (empty($alias)) {
                $alias = $column;
            }
            if (!$column instanceof Zend_Db_Expr && !empty($correlationName)) {
                $column = $this->_adapter->quoteIdentifier(array($correlationName, $column));
            }
            $columns[] = $this->_adapter->quoteIdentifier(array($tableAlias, $alias))
                . " = {$column}";
        }

        $sql .= "\n SET " . implode(', ', $columns) . "\n";

        // render WHERE
        $sql = $this->_renderWhere($sql);

        return $sql;
    }

    /**
     * Insert to table from current select
     *
     * @param string $tableName
     * @param array $fields
     * @param bool $onDuplicate
     * @return string
     */
    public function insertFromSelect($tableName, $fields = array(), $onDuplicate = true) {
        $sql = "INSERT INTO `{$tableName}` ";
        if ($fields) {
            $sql .= "(`".join('`,`', $fields) . "`) ";
        }

        $sql .= $this->assemble();

        if ($onDuplicate && $fields) {
            $sql .= " ON DUPLICATE KEY UPDATE";
            $updateFields = array();
            foreach ($fields as $field) {
                $field = $this->_adapter->quoteIdentifier($field);
                $updateFields[] = "{$field}=VALUES({$field})";
            }
            $sql .= " " . join(', ', $updateFields);
        }

        return $sql;
    }

    /**
     * Retrieve DELETE query from select
     *
     * @param string $table The table name or alias
     * @return string
     */
    public function deleteFromSelect($table) {
        $partsInit = self::$_partsInit;
        unset($partsInit[self::DISTINCT]);
        unset($partsInit[self::COLUMNS]);

        $sql = 'DELETE ' . $table;
        foreach (array_keys($partsInit) as $part) {
            $method = '_render' . ucfirst($part);
            if (method_exists($this, $method)) {
                $sql = $this->$method($sql);
            }
        }
        return $sql;
    }

    /**
     * Modify (hack) part of the structured information for the currect query
     *
     * @param string $part
     * @param mixed $value
     * @return Varien_Db_Select
     */
    public function setPart($part, $value)
    {
        $part = strtolower($part);
        if (!array_key_exists($part, $this->_parts)) {
            throw new Zend_Db_Select_Exception("Invalid Select part '$part'");
        }
        $this->_parts[$part] = $value;
        return $this;
    }
}