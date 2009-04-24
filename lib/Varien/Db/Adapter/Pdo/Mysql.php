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

    protected $_transactionLevel    = 0;
    protected $_connectionFlagsSet  = false;
    protected $_describesCache      = array();

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
     * @param   string $table
     * @param   string $fk
     * @return  bool
     */
    public function dropForeignKey($table, $fk)
    {
        $create = $this->raw_fetchRow("show create table `$table`", 'Create Table');
        if (strpos($create, "CONSTRAINT `$fk` FOREIGN KEY (")!==false) {
            $this->resetDescribesCache($table);
            return $this->raw_query("ALTER TABLE `$table` DROP FOREIGN KEY `$fk`");
        }
        return true;
    }

    /**
     * Delte index if it exist
     *
     * @param   string $table
     * @param   string $key
     * @return  bool
     */
    public function dropKey($table, $key)
    {
        $create = $this->raw_fetchRow("show create table `$table`", 'Create Table');
        if (strpos($create, "KEY `$key` (")!==false) {
            $this->resetDescribesCache($table);
            return $this->raw_query("ALTER TABLE `$table` DROP KEY `$key`");
        }
        return true;
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
     */
    public function addConstraint($fkName, $tableName, $keyName, $refTableName, $refKeyName, $onDelete = 'cascade', $onUpdate = 'cascade')
    {
        if (substr($fkName, 0, 3) != 'FK_') {
            $fkName = 'FK_' . $fkName;
        }

        $this->dropForeignKey($tableName, $fkName);

        $sql = 'ALTER TABLE `'.$tableName.'` ADD CONSTRAINT `'.$fkName.'`'
            . 'FOREIGN KEY (`'.$keyName.'`) REFERENCES `'.$refTableName.'` (`'.$refKeyName.'`)';
        if (!is_null($onDelete)) {
            $sql .= ' ON DELETE ' . strtoupper($onDelete);
        }
        if (!is_null($onUpdate)) {
            $sql .= ' ON UPDATE ' . strtoupper($onUpdate);
        }
        $this->resetDescribesCache($tableName);
        return $this->raw_query($sql);
    }

    /**
     * Check table column exist
     *
     * @param   string $tableName
     * @param   string $columnName
     * @return  bool
     */
    public function tableColumnExists($tableName, $columnName)
    {
        foreach ($this->fetchAll('DESCRIBE `'.$tableName.'`') as $row) {
            if ($row['Field'] == $columnName) {
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
        $this->resetDescribesCache($tableName);
        $result = $this->raw_query("alter table `$tableName` add column `$columnName` ".$definition);
        return $result;
    }

    /**
     * Delete table column
     *
     * @param   string $tableName
     * @param   string $columnName
     * @return  bool
     */
    public function dropColumn($tableName, $columnName)
    {
        if (!$this->tableColumnExists($tableName, $columnName)) {
            return true;
        }

        $create = $this->raw_fetchRow('SHOW CREATE TABLE `'.$tableName.'`', 'Create Table');

        $alterDrop = array();

        /**
         * find foreign keys for column
         */
        $matches = array();
        preg_match_all('/CONSTRAINT `([^`]*)` FOREIGN KEY \(`([^`]*)`\)/', $create, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            if ($match[2] == $columnName) {
                $alterDrop[] = 'DROP FOREIGN KEY `'.$match[1].'`';
            }
        }

        $alterDrop[] = 'DROP COLUMN `'.$columnName.'`';
        $this->resetDescribesCache($tableName);
        return $this->raw_query('ALTER TABLE `'.$tableName.'` ' . join(', ', $alterDrop));
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
            Mage::throwException('Column "' . $oldColumnName . '" does not exists on table "' . $tableName . '"');
        }

        $sql = 'ALTER TABLE ' . $this->quoteIdentifier($tableName)
            . ' CHANGE COLUMN ' . $this->quoteIdentifier($oldColumnName)
            . ' ' . $this->quoteIdentifier($newColumnName) . ' ' . $definition;
        $this->resetDescribesCache($tableName);
        $result = $this->raw_query($sql);
        if ($showStatus) {
            $this->showTableStatus($tableName);
        }
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
            Mage::throwException('Column "' . $columnName . '" does not exists on table "' . $tableName . '"');
        }

        $sql = 'ALTER TABLE ' . $this->quoteIdentifier($tableName)
            . ' MODIFY COLUMN ' . $this->quoteIdentifier($columnName)
            . ' ' . $definition;
        $this->resetDescribesCache($tableName);
        $result = $this->raw_query($sql);
        if ($showStatus) {
            $this->showTableStatus($tableName);
        }
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

    public function getKeyList($tableName)
    {
        $keyList = array();
        $create  = $this->raw_fetchRow('SHOW CREATE TABLE ' . $this->quoteIdentifier($tableName), 'Create Table');
        $matches = array();
        preg_match_all('#KEY `([^`]+)` (USING (BTREE|HASH) )?\(([^)]+)\)#s', $create, $matches, PREG_SET_ORDER);

        foreach ($matches as $v) {
            $keyList[$v[1]] = split(',', str_replace($this->getQuoteIdentifierSymbol(), '', $v[2]));
        }

        return $keyList;
    }

    /**
     * Retrieve INDEX list for table
     *
     * @param string $tableName
     * @return array
     */
    public function getIndexList($tableName)
    {
        $indexList = array();

        $sql = "SHOW INDEX FROM " . $this->quoteIdentifier($tableName);
        foreach ($this->fetchAll($sql) as $row) {
            $fieldKeyName   = 'Key_name';
            $fieldNonUnique = 'Non_unique';
            $fieldColumn    = 'Column_name';
            $fieldIndexType = 'Index_type';

            if ($row[$fieldKeyName] == 'PRIMARY') {
                $indexType  = 'primary';
            }
            elseif ($row[$fieldNonUnique] == 1) {
                $indexType  = 'unique';
            }
            elseif ($row[$fieldIndexType] == 'FULLTEXT') {
                $indexType  = 'fulltext';
            }
            else {
                $indexType  = 'index';
            }

            if (isset($indexList[$row[$fieldKeyName]])) {
                $indexList[$row[$fieldKeyName]]['fields'][] = $row[$fieldColumn];
            }
            else {
                $indexList[$row[$fieldKeyName]] = array(
                    'type'   => $indexType,
                    'fields' => array($row[$fieldColumn])
                );
            }
        }

        return $indexList;
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
        $keyList = $this->getKeyList($tableName);

        $sql = 'ALTER TABLE '.$this->quoteIdentifier($tableName);
        if (isset($keyList[$indexName])) {
            $sql .= ' DROP INDEX ' . $this->quoteIdentifier($indexName) . ',';
        }

        if (is_array($fields)) {
            $fieldSql = array();
            foreach ($fields as $field) {
                $fieldSql[] = $this->quoteIdentifier($field);
            }
            $fieldSql = join(',', $fieldSql);
        }
        else {
            $fieldSql = $this->quoteIdentifier($fields);
        }

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
        $this->resetDescribesCache($tableName);
        return $this->raw_query($sql);
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
     * Reset table describe cache data
     *
     * @param   string $tableName
     * @param   string $schemaName OPTIONAL
     * @return  Varien_Db_Adapter_Pdo_Mysql
     */
    public function resetDescribesCache($tableName = null, $schemaName = null)
    {
        if ($tableName) {
            $key = $tableName;
            if ($schemaName) {
                $key = $schemaName . '.' . $key;
            }
            unset($this->_describesCache[$key]);
        } else {
            $this->_describesCache = array();
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
        $key = $tableName;
        if ($schemaName) {
            $key = $schemaName . '.' . $key;
        }

        if (!isset($this->_describesCache[$key])) {
            $this->_describesCache[$key] = parent::describeTable($tableName, $schemaName);
        }

        return $this->_describesCache[$key];
    }

    /**
     * Retrieve Database limitation
     *
     * @return mixed
     */
    public function getLimitation($code)
    {
        switch ($code) {
            case 'index':
                $value = 64;
                break;
            case 'join':
                $value = 61;
                break;
            case 'column':
                $value = 1000;
                break;
            case 'columns_per_index':
                $value = 16;
                break;
            default:
                $value = null;
                break;
        }

        return $value;
    }

    /**
     * Truncate table
     *
     * @param string $tableName
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function truncate($tableName)
    {
        $sql = 'TRUNCATE ' . $this->quoteIdentifier($tableName);
        $this->raw_query($sql);

        return $this;
    }
}
