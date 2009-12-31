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
 * @package    Varien_Db
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Mysql PDO DB adapter
 */
class Varien_Db_Adapter_Pdo_Mysql extends Zend_Db_Adapter_Pdo_Mysql
{
    const DEBUG_CONNECT         = 0;
    const DEBUG_TRANSACTION     = 1;
    const DEBUG_QUERY           = 2;

    const ISO_DATE_FORMAT       = 'yyyy-MM-dd';
    const ISO_DATETIME_FORMAT   = 'yyyy-MM-dd HH-mm-ss';

    const DDL_DESCRIBE          = 1;
    const DDL_CREATE            = 2;
    const DDL_INDEX             = 3;
    const DDL_FOREIGN_KEY       = 4;

    /**
     * Current Transaction Level
     *
     * @var int
     */
    protected $_transactionLevel    = 0;

    /**
     * Set attribute to connection flag
     *
     * @var bool
     */
    protected $_connectionFlagsSet  = false;

    /**
     * Tables DDL cache
     *
     * @var array
     */
    protected $_ddlCache            = array();

    /**
     * SQL bind params
     *
     * @var array
     */
    protected $_bindParams          = array();

    /**
     * Autoincrement for bind value
     *
     * @var int
     */
    protected $_bindIncrement       = 0;

    /**
     * Write SQL debug data to file
     *
     * @var bool
     */
    protected $_debug               = false;

    /**
     * Minimum query duration time to be logged
     *
     * @var unknown_type
     */
    protected $_logQueryTime        = 0.05;

    /**
     * Path to SQL debug data log
     *
     * @var string
     */
    protected $_debugFile           = 'var/debug/sql.txt';

    /**
     * Io File Adapter
     *
     * @var Varien_Io_File
     */
    protected $_debugIoAdapter;

    /**
     * Debug timer start value
     *
     * @var float
     */
    protected $_debugTimer          = 0;

    /**
     * Begin new DB transaction for connection
     *
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function beginTransaction()
    {
        if ($this->_transactionLevel===0) {
            $this->_debugTimer();
            parent::beginTransaction();
            $this->_debugStat(self::DEBUG_TRANSACTION, 'BEGIN');
        }
        $this->_transactionLevel++;
        return $this;
    }

    /**
     * Commit DB transaction
     *
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function commit()
    {
        if ($this->_transactionLevel===1) {
            $this->_debugTimer();
            parent::commit();
            $this->_debugStat(self::DEBUG_TRANSACTION, 'COMMIT');
        }
        $this->_transactionLevel--;
        return $this;
    }

    /**
     * Rollback DB transaction
     *
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function rollback()
    {
        if ($this->_transactionLevel===1) {
            $this->_debugTimer();
            parent::rollback();
            $this->_debugStat(self::DEBUG_TRANSACTION, 'ROLLBACK');
        }
        $this->_transactionLevel--;
        return $this;
    }

    /**
     * Convert date to DB format
     *
     * @param   mixed $date
     * @return  string
     */
    public function convertDate($date)
    {
        if ($date instanceof Zend_Date) {
            return $date->toString(self::ISO_DATE_FORMAT);
        }
        return strftime('%Y-%m-%d', strtotime($date));
    }

    /**
     * Convert date and time to DB format
     *
     * @param   mixed $date
     * @return  string
     */
    public function convertDateTime($datetime)
    {
        if ($datetime instanceof Zend_Date) {
            return $datetime->toString(self::ISO_DATETIME_FORMAT);
        }
        return strftime('%Y-%m-%d %H:%M:%S', strtotime($datetime));
    }

    /**
     * Creates a PDO object and connects to the database.
     */
    protected function _connect()
    {
        if ($this->_connection) {
            return;
        }

        if (!extension_loaded('pdo_mysql')) {
            throw new Zend_Db_Adapter_Exception('pdo_mysql extension is not installed');
        }

        if (strpos($this->_config['host'], '/')!==false) {
            $this->_config['unix_socket'] = $this->_config['host'];
            unset($this->_config['host']);
        } elseif (strpos($this->_config['host'], ':')!==false) {
            list($this->_config['host'], $this->_config['port']) = explode(':', $this->_config['host']);
        }

        $this->_debugTimer();
        parent::_connect();
        $this->_debugStat(self::DEBUG_CONNECT, '');

        /** @link http://bugs.mysql.com/bug.php?id=18551 */
        $this->_connection->query("SET SQL_MODE=''");

        if (!$this->_connectionFlagsSet) {
            $this->_connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
            $this->_connection->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
            $this->_connectionFlagsSet = true;
        }
    }

    public function raw_query($sql)
    {
        do {
            $retry = false;
            $tries = 0;
            try {
                $result = $this->getConnection()->query($sql);
            } catch (PDOException $e) {
                if ($e->getMessage()=='SQLSTATE[HY000]: General error: 2013 Lost connection to MySQL server during query') {
                    $retry = true;
                } else {
                    throw $e;
                }
                $tries++;
            }
        } while ($retry && $tries<10);

        return $result;
    }

    public function raw_fetchRow($sql, $field=null)
    {
        if (!$result = $this->raw_query($sql)) {
            return false;
        }
        if (!$row = $result->fetch(PDO::FETCH_ASSOC)) {
            return false;
        }
        if (empty($field)) {
            return $row;
        } else {
            return isset($row[$field]) ? $row[$field] : false;
        }
    }

    /**
     * Special handling for PDO query().
     * All bind parameter names must begin with ':'
     *
     * @param string|Zend_Db_Select $sql The SQL statement with placeholders.
     * @param array $bind An array of data to bind to the placeholders.
     * @return Zend_Db_Pdo_Statement
     * @throws Zend_Db_Adapter_Exception To re-throw PDOException.
     */
    public function query($sql, $bind = array())
    {
        $this->_debugTimer();
        try {
            $sql = (string)$sql;
            if (strpos($sql, ':') !== false || strpos($sql, '?') !== false) {
                $this->_bindParams = $bind;
                $sql = preg_replace_callback('#(([\'"])((\\2)|((.*?[^\\\\])\\2)))#', array($this, 'proccessBindCallback'), $sql);
                $bind = $this->_bindParams;
            }



            $result = parent::query($sql, $bind);
        }
        catch (Exception $e) {
            $this->_debugStat(self::DEBUG_QUERY, $sql, $bind);
            $this->_debugException($e);
        }
        $this->_debugStat(self::DEBUG_QUERY, $sql, $bind, $result);
        return $result;
    }

    public function proccessBindCallback($matches)
    {
        if (isset($matches[6]) && (
            strpos($matches[6], "'") !== false ||
            strpos($matches[6], ":") !== false ||
            strpos($matches[6], "?") !== false)) {
            $bindName = ':_mage_bind_var_' . ( ++ $this->_bindIncrement );
            $this->_bindParams[$bindName] = $this->_unQuote($matches[6]);
            return ' ' . $bindName;
        }
        return $matches[0];
    }

    /**
     * Unquote raw string (use for auto-bind)
     *
     * @param string $string
     * @return string
     */
    protected function _unQuote($string)
    {
        $translate = array(
            "\\000" => "\000",
            "\\n"   => "\n",
            "\\r"   => "\r",
            "\\\\"  => "\\",
            "\'"    => "'",
            "\\\""  => "\"",
            "\\032" => "\032"
        );
        return strtr($string, $translate);
    }

    /**
     * Run Multi Query
     *
     * @param string $sql
     * @return array
     */
    public function multi_query($sql)
    {
        ##$result = $this->raw_query($sql);

        #$this->beginTransaction();
        try {
            $stmts = $this->_splitMultiQuery($sql);
            $result = array();
            foreach ($stmts as $stmt) {
                $result[] = $this->raw_query($stmt);
            }
            #$this->commit();
        } catch (Exception $e) {
            #$this->rollback();
            throw $e;
        }

        $this->resetDdlCache();

        return $result;
    }

    /**
     * Split multi statement query
     *
     * @param $sql string
     * @return array
     */
    protected function _splitMultiQuery($sql)
    {
        $parts = preg_split('#(;|\'|"|\\\\|//|--|\n|/\*|\*/)#', $sql, null, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        $q = false;
        $c = false;
        $stmts = array();
        $s = '';

        foreach ($parts as $i=>$part) {
            // strings
            if (($part==="'" || $part==='"') && ($i===0 || $parts[$i-1]!=='\\')) {
                if ($q===false) {
                    $q = $part;
                } elseif ($q===$part) {
                    $q = false;
                }
            }

            // single line comments
            if (($part==='//' || $part==='--') && ($i===0 || $parts[$i-1]==="\n")) {
                $c = $part;
            } elseif ($part==="\n" && ($c==='//' || $c==='--')) {
                $c = false;
            }

            // multi line comments
            if ($part==='/*' && $c===false) {
                $c = '/*';
            } elseif ($part==='*/' && $c==='/*') {
                $c = false;
            }

            // statements
            if ($part===';' && $q===false && $c===false) {
                if (trim($s)!=='') {
                    $stmts[] = trim($s);
                    $s = '';
                }
            } else {
                $s .= $part;
            }
        }
        if (trim($s)!=='') {
            $stmts[] = trim($s);
        }

        return $stmts;
    }

    /**
     * Delete foreign key if it exist
     *
     * @param string $tableName
     * @param string $foreignKey
     * @param string $shemaName
     * @return mixed
     */
    public function dropForeignKey($tableName, $foreignKey, $schemaName = null)
    {
        $foreignKeys = $this->getForeignKeys($tableName, $schemaName);
        if (isset($foreignKeys[strtoupper($foreignKey)])) {
            $sql = sprintf('ALTER TABLE %s DROP FOREIGN KEY %s',
                $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
                $this->quoteIdentifier($foreignKeys[strtoupper($foreignKey)]['FK_NAME']));
//            echo '<pre>';
//            var_dump($tableName, $schemaName, $foreignKey, $foreignKeys, $sql);
//            echo '</pre>';

            $this->resetDdlCache($tableName, $schemaName);

            return $this->raw_query($sql);
        }

        return true;
    }

    /**
     * Delete index from a table if it exist
     *
     * @param string $tableName
     * @param string $keyName
     * @param string $shemaName
     * @return bool
     */
    public function dropKey($tableName, $keyName, $shemaName = null)
    {
        $indexList = $this->getIndexList($tableName, $shemaName);
        $keyName = strtoupper($keyName);
        if (!isset($indexList[$keyName])) {
            return true;
        }

        if ($keyName == 'PRIMARY') {
            $cond = 'DROP PRIMARY KEY';
        }
        else {
            $cond = sprintf('DROP KEY %s', $this->quoteIdentifier($indexList[$keyName]['KEY_NAME']));
        }
        $sql = sprintf('ALTER TABLE %s %s',
            $this->quoteIdentifier($this->_getTableName($tableName, $shemaName)),
            $cond);

        $this->resetDdlCache($tableName, $shemaName);
        return $this->raw_query($sql);
    }

    /**
     * Prepare table before add constraint foreign key
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $refTableName
     * @param string $refColumnName
     * @param string $onDelete
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function purgeOrphanRecords($tableName, $columnName, $refTableName, $refColumnName, $onDelete = 'cascade')
    {
        if (strtoupper($onDelete) == 'CASCADE'
            || strtoupper($onDelete) == 'RESTRICT') {
            $sql = "DELETE `p`.* FROM `{$tableName}` AS `p`"
                . " LEFT JOIN `{$refTableName}` AS `r`"
                . " ON `p`.`{$columnName}` = `r`.`{$refColumnName}`"
                . " WHERE `r`.`{$refColumnName}` IS NULL";
            $this->raw_query($sql);
        }
        elseif (strtoupper($onDelete) == 'SET NULL') {
            $sql = "UPDATE `{$tableName}` AS `p`"
                . " LEFT JOIN `{$refTableName}` AS `r`"
                . " ON `p`.`{$columnName}` = `r`.`{$refColumnName}`"
                . " SET `p`.`{$columnName}`=NULL"
                . " WHERE `r`.`{$refColumnName}` IS NULL";
            $this->raw_query($sql);
        }

        return $this;
    }

    /**
     * Add foreign key to table. If FK with same name exist - it will be deleted
     *
     * @param string $fkName foreign key name
     * @param string $tableName main table name
     * @param string $keyName main table field name
     * @param string $refTableName refered table name
     * @param string $refKeyName refered table field name
     * @param string $onUpdate on update statement
     * @param string $onDelete on delete statement
     * @param bool $purge
     * @return mixed
     */
    public function addConstraint($fkName, $tableName, $columnName,
        $refTableName, $refColumnName, $onDelete = 'cascade', $onUpdate = 'cascade', $purge = false)
    {
        if (substr($fkName, 0, 3) != 'FK_') {
            $fkName = 'FK_' . $fkName;
        }

        $this->dropForeignKey($tableName, $fkName);

        if ($purge) {
            $this->purgeOrphanRecords($tableName, $columnName, $refTableName, $refColumnName, $onDelete);
        }

        $sql = 'ALTER TABLE `'.$tableName.'` ADD CONSTRAINT `'.$fkName.'`'
            . ' FOREIGN KEY (`'.$columnName.'`) REFERENCES `'.$refTableName.'` (`'.$refColumnName.'`)';
        if (!is_null($onDelete)) {
            $sql .= ' ON DELETE ' . strtoupper($onDelete);
        }
        if (!is_null($onUpdate)) {
            $sql .= ' ON UPDATE ' . strtoupper($onUpdate);
        }

        $this->resetDdlCache($tableName);
        return $this->raw_query($sql);
    }

    /**
     * Check table column exist
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $schemaName
     * @return bool
     */
    public function tableColumnExists($tableName, $columnName, $schemaName = null)
    {
        $describe = $this->describeTable($tableName, $schemaName);
        foreach ($describe as $column) {
            if ($column['COLUMN_NAME'] == $columnName) {
                return true;
            }
        }
        return false;
    }

    /**
     * Add new column to table
     *
     * @param   string $tableName
     * @param   string $columnName
     * @param   string $definition
     * @return  bool
     */
    public function addColumn($tableName, $columnName, $definition)
    {
        if ($this->tableColumnExists($tableName, $columnName)) {
            return true;
        }

        $sql = sprintf('ALTER TABLE %s ADD COLUMN %s %s',
            $this->quoteIdentifier($tableName),
            $this->quoteIdentifier($columnName),
            $definition
        );
        $result = $this->raw_query($sql);
        $this->resetDdlCache($tableName);
        return $result;
    }

    /**
     * Delete table column
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $shemaName
     * @return bool
     */
    public function dropColumn($tableName, $columnName, $shemaName = null)
    {
        if (!$this->tableColumnExists($tableName, $columnName, $shemaName)) {
            return true;
        }

        $alterDrop = array();

        $foreignKeys = $this->getForeignKeys($tableName, $shemaName);
        foreach ($foreignKeys as $fkProp) {
            if ($fkProp['COLUMN_NAME'] == $columnName) {
                $alterDrop[] = sprintf('DROP FOREIGN KEY %s', $this->quoteIdentifier($fkProp['FK_NAME']));
            }
        }

        $alterDrop[] = sprintf('DROP COLUMN %s', $this->quoteIdentifier($columnName));
        $sql = sprintf('ALTER TABLE %s %s',
            $this->quoteIdentifier($this->_getTableName($tableName, $shemaName)),
            join(', ', $alterDrop));

        $this->resetDdlCache($tableName, $shemaName);
        return $this->raw_query($sql);
    }

    /**
     * Change column
     *
     * @param string $tableName
     * @param string $oldColumnName
     * @param string $newColumnName
     * @param string $definition
     * @param bool $showStatus
     *
     * @return mixed
     */
    public function changeColumn($tableName, $oldColumnName, $newColumnName, $definition,  $showStatus = false)
    {
        if (!$this->tableColumnExists($tableName, $oldColumnName)) {
            throw new Exception(sprintf('Column "%s" does not exists on table "%s"', $oldColumnName, $tableName));
        }

        $sql = sprintf('ALTER TABLE %s CHANGE COLUMN %s %s %s',
            $this->quoteIdentifier($tableName),
            $this->quoteIdentifier($oldColumnName),
            $this->quoteIdentifier($newColumnName),
            $definition);

        $result = $this->raw_query($sql);
        if ($showStatus) {
            $this->showTableStatus($tableName);
        }

        $this->resetDdlCache($tableName);
        return $result;
    }

    /**
     * Modify column defination or position
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $definition
     * @param bool $showStatus
     *
     * @return mixed
     */
    public function modifyColumn($tableName, $columnName, $definition, $showStatus = false)
    {
        if (!$this->tableColumnExists($tableName, $columnName)) {
            throw new Exception(sprintf('Column "%s" does not exists on table "%s"', $columnName, $tableName));
        }

        $sql = sprintf('ALTER TABLE %s MODIFY COLUMN %s %s',
            $this->quoteIdentifier($tableName),
            $this->quoteIdentifier($columnName),
            $definition);
        $result = $this->raw_query($sql);
        if ($showStatus) {
            $this->showTableStatus($tableName);
        }

        $this->resetDdlCache($tableName);
        return $result;
    }

    /**
     * Show table status
     *
     * @param string $tableName
     * @return array
     */
    public function showTableStatus($tableName)
    {
        $sql = $this->quoteInto('SHOW TABLE STATUS LIKE ?', $tableName);
        return $this->raw_fetchRow($sql);
    }

    /**
     * Retrieve table index key list
     *
     * @deprecated use getIndexList(
     * @param string $tableName
     * @param string $schemaName
     * @return array
     */
    public function getKeyList($tableName, $schemaName = null)
    {
        $keyList   = array();
        $indexList = $this->getIndexList($tableName, $schemaName);

        foreach ($indexList as $indexProp) {
            $keyList[$indexProp['KEY_NAME']] = $indexProp['COLUMNS_LIST'];
        }

        return $keyList;
    }

    /**
     * Retrieve Create Table SQL
     *
     * @param string $tableName
     * @param string $schemaName
     * @return string
     */
    public function getCreateTable($tableName, $schemaName = null)
    {
        $tableName = $this->_getTableName($tableName, $schemaName);
        if (!isset($this->_ddlCache[self::DDL_CREATE][$tableName])) {
            $sql = sprintf('SHOW CREATE TABLE %s', $this->quoteIdentifier($tableName));
            $this->_ddlCache[self::DDL_CREATE][$tableName] = $this->raw_fetchRow($sql, 'Create Table');
        }
        return $this->_ddlCache[self::DDL_CREATE][$tableName];
    }

    /**
     * Retrieve the foreign keys descriptions for a table.
     *
     * The return value is an associative array keyed by the UPPERCASE foreign key,
     * as returned by the RDBMS.
     *
     * The value of each array element is an associative array
     * with the following keys:
     *
     * FK_NAME          => string; original foreign key name
     * SCHEMA_NAME      => string; name of database or schema
     * TABLE_NAME       => string;
     * COLUMN_NAME      => string; column name
     * REF_SCHEMA_NAME  => string; name of reference database or schema
     * REF_TABLE_NAME   => string; reference table name
     * REF_COLUMN_NAME  => string; reference column name
     * ON_DELETE        => string; action type on delete row
     * ON_UPDATE        => string; action type on update row
     *
     * @param string $tableName
     * @param string $schemaName
     * @return array
     */
    public function getForeignKeys($tableName, $schemaName = null)
    {
        $cacheKey = $this->_getTableName($tableName, $schemaName);
        if (!isset($this->_ddlCache[self::DDL_FOREIGN_KEY][$cacheKey])) {
            $foreignKeys = array();
            $createSql = $this->getCreateTable($tableName, $schemaName);

            // collect CONSTRAINT
            $regExp  = '#,\s+CONSTRAINT `([^`]*)` FOREIGN KEY \(`([^`]*)`\) '
                . 'REFERENCES (`[^`]*\.)?`([^`]*)` \(`([^`]*)`\)'
                . '( ON DELETE (RESTRICT|CASCADE|SET NULL|NO ACTION))?'
                . '( ON UPDATE (RESTRICT|CASCADE|SET NULL|NO ACTION))?#';
            $matches = array();
            preg_match_all($regExp, $createSql, $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                $foreignKeys[strtoupper($match[1])] = array(
                    'FK_NAME'           => $match[1],
                    'SCHEMA_NAME'       => $schemaName,
                    'TABLE_NAME'        => $tableName,
                    'COLUMN_NAME'       => $match[2],
                    'REF_SHEMA_NAME'    => isset($match[3]) ? $match[3] : $schemaName,
                    'REF_TABLE_NAME'    => $match[4],
                    'REF_COLUMN_NAME'   => $match[5],
                    'ON_DELETE'         => isset($match[6]) ? $match[7] : '',
                    'ON_UPDATE'         => isset($match[8]) ? $match[9] : ''
                );
            }

            $this->_ddlCache[self::DDL_FOREIGN_KEY][$cacheKey] = $foreignKeys;
        }

        return $this->_ddlCache[self::DDL_FOREIGN_KEY][$cacheKey];
    }

    /**
     * Retrieve table index information
     *
     * The return value is an associative array keyed by the UPPERCASE index key,
     * as returned by the RDBMS.
     *
     * The value of each array element is an associative array
     * with the following keys:
     *
     * SCHEMA_NAME      => string; name of database or schema
     * TABLE_NAME       => string; name of the table
     * KEY_NAME         => string; the original index name
     * COLUMNS_LIST     => array; array of index column names
     * INDEX_TYPE       => string; create index type
     * INDEX_METHOD     => string; index method using
     * type             => string; see INDEX_TYPE
     * fields           => array; see COLUMNS_LIST
     *
     * @param string $tableName
     * @param string $schemaName
     * @return array
     */
    public function getIndexList($tableName, $schemaName = null)
    {
        $cacheKey = $this->_getTableName($tableName, $schemaName);
        if (!isset($this->_ddlCache[self::DDL_INDEX][$cacheKey])) {
            $indexList = array();

            $sql = sprintf('SHOW INDEX FROM %s',
                $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)));
            foreach ($this->fetchAll($sql) as $row) {
                $fieldKeyName   = 'Key_name';
                $fieldNonUnique = 'Non_unique';
                $fieldColumn    = 'Column_name';
                $fieldIndexType = 'Index_type';

                if ($row[$fieldKeyName] == 'PRIMARY') {
                    $indexType  = 'primary';
                }
                elseif ($row[$fieldNonUnique] == 0) {
                    $indexType  = 'unique';
                }
                elseif ($row[$fieldIndexType] == 'FULLTEXT') {
                    $indexType  = 'fulltext';
                }
                else {
                    $indexType  = 'index';
                }

                if (isset($indexList[$row[$fieldKeyName]])) {
                    $indexList[$row[$fieldKeyName]]['fields'][] = $row[$fieldColumn]; // for compatible
                    $indexList[$row[$fieldKeyName]]['COLUMNS_LIST'][] = $row[$fieldColumn];
                }
                else {
                    $indexList[strtoupper($row[$fieldKeyName])] = array(
                        'SCHEMA_NAME'   => $schemaName,
                        'TABLE_NAME'    => $tableName,
                        'KEY_NAME'      => $row[$fieldKeyName],
                        'COLUMNS_LIST'  => array($row[$fieldColumn]),
                        'INDEX_TYPE'    => strtoupper($indexType),
                        'INDEX_METHOD'  => $row[$fieldIndexType],
                        'type'          => $indexType, // for compatible
                        'fields'        => array($row[$fieldColumn]) // for compatible
                    );
                }
            }

            $this->_ddlCache[self::DDL_INDEX][$cacheKey] = $indexList;
        }

        return $this->_ddlCache[self::DDL_INDEX][$cacheKey];
    }

    /**
     * Add Index Key
     *
     * @param string $tableName
     * @param string $indexName
     * @param string|array $fields
     * @param string $indexType
     * @return
     */
    public function addKey($tableName, $indexName, $fields, $indexType = 'index')
    {
        $columns = $this->describeTable($tableName);
        $keyList = $this->getKeyList($tableName);

        $sql = 'ALTER TABLE '.$this->quoteIdentifier($tableName);
        if (isset($keyList[$indexName])) {
            $sql .= ' DROP INDEX ' . $this->quoteIdentifier($indexName) . ',';
        }

        if (!is_array($fields)) {
            $fields = array($fields);
        }

        $fieldSql = array();
        foreach ($fields as $field) {
            if (!isset($columns[$field])) {
                $msg = sprintf('There is no field "%s" that you are trying to create an index on "%s"',
                    $field, $tableName);
                throw new Exception($msg);
            }
            $fieldSql[] = $this->quoteIdentifier($field);
        }
        $fieldSql = join(',', $fieldSql);

        switch (strtolower($indexType)) {
            case 'primary':
                $condition = 'PRIMARY KEY';
                break;
            case 'unique':
                $condition = 'UNIQUE ' . $this->quoteIdentifier($indexName);
                break;
            case 'fulltext':
                $condition = 'FULLTEXT ' . $this->quoteIdentifier($indexName);
                break;
            default:
                $condition = 'INDEX ' . $this->quoteIdentifier($indexName);
                break;
        }

        $sql .= ' ADD ' . $condition . ' (' . $fieldSql . ')';

        $cycle = true;
        while ($cycle === true) {
            try {
                $result = $this->raw_query($sql);
                $cycle  = false;
            }
            catch (PDOException $e) {
                if (in_array(strtolower($indexType), array('primary', 'unique'))) {
                    $match = array();
                    if (preg_match('#SQLSTATE\[23000\]: [^:]+: 1062[^\']+\'([\d-]+)\'#', $e->getMessage(), $match)) {
                        $ids = explode('-', $match[1]);
                        $this->_removeDuplicateEntry($tableName, $fields, $ids);
                        continue;
                    }
                }
                throw $e;
            }
            catch (Exception $e) {
                throw $e;
            }
        }

        $this->resetDdlCache($tableName);

        return $result;
    }

    /**
     * Remove duplicate entry for create key
     *
     * @param string $table
     * @param array $fields
     * @param array $ids
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    protected function _removeDuplicateEntry($table, $fields, $ids)
    {
        $where = array();
        $i = 0;
        foreach ($fields as $field) {
            $where[] = $this->quoteInto($field . '=?', $ids[$i]);
            $i ++;
        }

        if (!$where) {
            return $this;
        }
        $whereCond = join(' AND ', $where);
        $sql = sprintf('SELECT COUNT(*) as `cnt` FROM `%s` WHERE %s', $table, $whereCond);

        if ($cnt = $this->raw_fetchRow($sql, 'cnt')) {
            $sql = sprintf('DELETE FROM `%s` WHERE %s LIMIT %d',
                $table,
                $whereCond,
                $cnt - 1
            );
            $this->raw_query($sql);
        }

        return $this;
    }

    /**
     * Creates and returns a new Zend_Db_Select object for this adapter.
     *
     * @return Varien_Db_Select
     */
    public function select()
    {
        return new Varien_Db_Select($this);
    }

    /**
     * Start debug timer
     *
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    protected function _debugTimer()
    {
        if ($this->_debug) {
            $this->_debugTimer = microtime(true);
        }
        return $this;
    }

    /**
     * Start debug timer
     *
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    protected function _debugStat($type, $sql, $bind = array(), $result = null)
    {
        if (!$this->_debug) {
            return $this;
        }

        $code = '## ' . getmypid() . ' ## ';
        $nl   = "\n";
        $time = sprintf('%.4f', microtime(true) - $this->_debugTimer);

        if ($time < $this->_logQueryTime) {
            return $this;
        }
        switch ($type) {
            case self::DEBUG_CONNECT:
                $code .= 'CONNECT' . $nl;
                break;
            case self::DEBUG_TRANSACTION:
                $code .= 'TRANSACTION ' . $sql . $nl;
                break;
            case self::DEBUG_QUERY:
                $code .= 'QUERY' . $nl;
                $code .= 'SQL: ' . $sql . $nl;
                if ($bind) {
                    $code .= 'BIND: ' . print_r($bind, true) . $nl;
                }
                if ($result instanceof Zend_Db_Statement_Pdo) {
                    $code .= 'AFF: ' . $result->rowCount() . $nl;
                }
                break;
        }
        $code .= 'TIME: ' . $time . $nl . $nl;

        $this->_debugWriteToFile($code);

        return $this;
    }

    /**
     * Write exception and thow
     *
     * @param Exception $e
     * @throws Exception
     */
    protected function _debugException(Exception $e)
    {
        if (!$this->_debug) {
            throw $e;
        }

        $nl   = "\n";
        $code = 'EXCEPTION ' . $e->getMessage() . $nl
            . 'E TRACE: ' . print_r($e->getTrace(), true) . $nl . $nl;
        $this->_debugWriteToFile($code);

        throw $e;
    }

    /**
     * Debug write to file process
     *
     * @param string $str
     */
    protected function _debugWriteToFile($str)
    {
        if (!$this->_debugIoAdapter) {
            $this->_debugIoAdapter = new Varien_Io_File();
            $dir = $this->_debugIoAdapter->dirname($this->_debugFile);
            $this->_debugIoAdapter->checkAndCreateFolder($dir);
            $this->_debugIoAdapter->open(array('path' => $dir));
            $this->_debugFile = basename($this->_debugFile);
        }

        $this->_debugIoAdapter->streamOpen($this->_debugFile, 'a');
        $this->_debugIoAdapter->streamLock();
        $this->_debugIoAdapter->streamWrite($str);
        $this->_debugIoAdapter->streamUnlock();
        $this->_debugIoAdapter->streamClose();
    }

    /**
     * Quotes a value and places into a piece of text at a placeholder.
     *
     * Method revrited for handle empty arrays in value param
     *
     * @param string  $text  The text with a placeholder.
     * @param mixed   $value The value to quote.
     * @param string  $type  OPTIONAL SQL datatype
     * @param integer $count OPTIONAL count of placeholders to replace
     * @return string An SQL-safe quoted value placed into the orignal text.
     */
    public function quoteInto($text, $value, $type = null, $count = null)
    {
        if (is_array($value) && empty($value)) {
            $value = new Zend_Db_Expr('NULL');
        }
        return parent::quoteInto($text, $value, $type, $count);
    }

    /**
     * Retrieve ddl cache name
     *
     * @param string $tableName
     * @param string $schemaName
     */
    protected function _getTableName($tableName, $schemaName = null)
    {
        return ($schemaName ? $schemaName . '.' : '') . $tableName;
    }

    /**
     * Reset table DDL
     *
     * @param string $tableName
     * @param string $schemaName OPTIONAL
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function resetDdlCache($tableName = null, $schemaName = null)
    {
        if (is_null($tableName)) {
            $this->_ddlCache = array();
        }
        else {
            $cacheKey = $this->_getTableName($tableName, $schemaName);

            unset($this->_ddlCache[self::DDL_DESCRIBE][$cacheKey]);
            unset($this->_ddlCache[self::DDL_CREATE][$cacheKey]);
            unset($this->_ddlCache[self::DDL_INDEX][$cacheKey]);
            unset($this->_ddlCache[self::DDL_FOREIGN_KEY][$cacheKey]);
        }

        return $this;
    }

    /**
     * Returns the column descriptions for a table.
     *
     * The return value is an associative array keyed by the column name,
     * as returned by the RDBMS.
     *
     * The value of each array element is an associative array
     * with the following keys:
     *
     * SCHEMA_NAME      => string; name of database or schema
     * TABLE_NAME       => string;
     * COLUMN_NAME      => string; column name
     * COLUMN_POSITION  => number; ordinal position of column in table
     * DATA_TYPE        => string; SQL datatype name of column
     * DEFAULT          => string; default expression of column, null if none
     * NULLABLE         => boolean; true if column can have nulls
     * LENGTH           => number; length of CHAR/VARCHAR
     * SCALE            => number; scale of NUMERIC/DECIMAL
     * PRECISION        => number; precision of NUMERIC/DECIMAL
     * UNSIGNED         => boolean; unsigned property of an integer type
     * PRIMARY          => boolean; true if column is part of the primary key
     * PRIMARY_POSITION => integer; position of column in primary key
     * IDENTITY         => integer; true if column is auto-generated with unique values
     *
     * @param string $tableName
     * @param string $schemaName OPTIONAL
     * @return array
     */
    public function describeTable($tableName, $schemaName = null)
    {
        $cacheKey = $this->_getTableName($tableName, $schemaName);

        if (!isset($this->_ddlCache[self::DDL_DESCRIBE][$cacheKey])) {
            $this->_ddlCache[self::DDL_DESCRIBE][$cacheKey] = parent::describeTable($tableName, $schemaName);
        }

        return $this->_ddlCache[self::DDL_DESCRIBE][$cacheKey];
    }

    /**
     * Truncate table
     *
     * @param string $tableName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function truncate($tableName, $schemaName = null)
    {
        $tableName = $this->_getTableName($tableName, $schemaName);
        $sql = sprintf('TRUNCATE %s', $this->quoteIdentifier($tableName));
        $this->raw_query($sql);

        return $this;
    }

    /**
     * Change table storage engine
     *
     * @param string $tableName
     * @param string $engine
     * @param string $type
     * @return mixed
     */
    public function changeTableEngine($tableName, $engine, $schemaName = null)
    {
        $sql = sprintf('ALTER TABLE %s ENGINE=%s',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
            $engine);
        return $this->raw_query($sql);
    }
}
