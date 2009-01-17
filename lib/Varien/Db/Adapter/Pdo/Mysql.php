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


class Varien_Db_Adapter_Pdo_Mysql extends Zend_Db_Adapter_Pdo_Mysql
{
    const DEBUG_CONNECT         = 0;
    const DEBUG_TRANSACTION     = 1;
    const DEBUG_QUERY           = 2;

    const ISO_DATE_FORMAT       = 'yyyy-MM-dd';
    const ISO_DATETIME_FORMAT   = 'yyyy-MM-dd HH-mm-ss';

    protected $_transactionLevel    = 0;
    protected $_connectionFlagsSet  = false;

    /**
     * Write SQL debug data to file
     *
     * @var bool
     */
    protected $_debug               = false;

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

    public function rollback()
    {
        if ($this->_transactionLevel===1) {
            $this->_debugTimer();
            return parent::rollback();
            $this->_debugStat(self::DEBUG_TRANSACTION, 'ROLLBACK');
        }
        $this->_transactionLevel--;
        return $this;
    }

    public function convertDate($date)
    {
        if ($date instanceof Zend_Date) {
            return $date->toString(self::ISO_DATE_FORMAT);
        }
        return strftime('%Y-%m-%d', strtotime($date));
    }

    public function convertDateTime($datetime)
    {
        if ($datetime instanceof Zend_Date) {
            return $datetime->toString(self::ISO_DATETIME_FORMAT);
        }
        return strftime('%Y-%m-%d %H:%M:%S', strtotime($datetime));
    }


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
            #$this->_connection->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
            $this->_connectionFlagsSet = true;
        }
    }

    public function raw_query($sql)
    {
        do {
            $retry = false;
            $tries = 0;
            try {
                $this->_debugTimer();
                $result = $this->getConnection()->query($sql);
                $this->_debugStat(self::DEBUG_QUERY, $sql, array(), $result);
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
            $result = parent::query($sql, $bind);
        }
        catch (Exception $e) {
            $this->_debugStat(self::DEBUG_QUERY, $sql, $bind);
            $this->_debugException($e);
        }
        $this->_debugStat(self::DEBUG_QUERY, $sql, $bind, $result);
        return $result;
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
 * @author      Magento Core Team <core@magentocommerce.com>
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

    public function dropForeignKey($table, $fk)
    {
        $create = $this->raw_fetchRow("show create table `$table`", 'Create Table');
        if (strpos($create, "CONSTRAINT `$fk` FOREIGN KEY (")!==false) {
            return $this->raw_query("ALTER TABLE `$table` DROP FOREIGN KEY `$fk`");
        }
        return true;
    }

    public function dropKey($table, $key)
    {
        $create = $this->raw_fetchRow("show create table `$table`", 'Create Table');
        if (strpos($create, "KEY `$key` (")!==false) {
            return $this->raw_query("ALTER TABLE `$table` DROP KEY `$key`");
        }
        return true;
    }

    /**
     * ADD CONSTRAINT
     *
     *
     * @param string $fkName
     * @param string $tableName
     * @param string $keyName
     * @param string $refTableName
     * @param string $refKeyName
     * @param string $onUpdate
     * @param string $onDelete
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

        return $this->raw_query($sql);
    }

    public function tableColumnExists($tableName, $columnName)
    {
        foreach ($this->fetchAll('DESCRIBE `'.$tableName.'`') as $row) {
            if ($row['Field'] == $columnName) {
                return true;
            }
        }
        return false;
    }

    public function addColumn($tableName, $columnName, $definition)
    {
        if ($this->tableColumnExists($tableName, $columnName)) {
            return true;
        }
        $result = $this->raw_query("alter table `$tableName` add column `$columnName` ".$definition);
        return $result;
    }

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

        return $this->raw_query('ALTER TABLE `'.$tableName.'` ' . join(', ', $alterDrop));
    }

    /**
     * Change column
     *
     * @param string $tableName
     * @param string $oldColumnName
     * @param string $newColumnName
     * @param string $definition
     */
    public function changeColumn($tableName, $oldColumnName, $newColumnName, $definition)
    {
        if (!$this->tableColumnExists($tableName, $oldColumnName)) {
            Mage::throwException('Column "' . $oldColumnName . '" does not exists on table "' . $tableName . '"');
        }

        $sql = 'ALTER TABLE ' . $this->quoteIdentifier($tableName)
            . 'CHANGE COLUMN ' . $this->quoteIdentifier($oldColumnName)
            . ' ' . $this->quoteIdentifier($newColumnName) . ' ' . $definition;
        return $this->raw_query($sql);
    }

    public function getKeyList($tableName)
    {
        $keyList = array();
        $create  = $this->raw_fetchRow('SHOW CREATE TABLE ' . $this->quoteIdentifier($tableName), 'Create Table');
        $matches = array();
        preg_match_all('#KEY `([^`]+)` \(([^)]+)\)#s', $create, $matches, PREG_SET_ORDER);

        foreach ($matches as $v) {
            $keyList[$v[1]] = split(',', str_replace($this->getQuoteIdentifierSymbol(), '', $v[2]));
        }

        return $keyList;
    }

    /**
     * Add Index Key
     *
     * @param string $tableName
     * @param string $indexName
     * @param string|array $fields
     * @return
     */
    public function addKey($tableName, $indexName, $fields)
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

        $sql .= ' ADD INDEX ' . $this->quoteIdentifier($indexName) . ' (' . $fieldSql . ')';

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
}