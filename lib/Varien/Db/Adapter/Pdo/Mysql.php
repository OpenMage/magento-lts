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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Mysql PDO DB adapter
 */
class Varien_Db_Adapter_Pdo_Mysql extends Zend_Db_Adapter_Pdo_Mysql implements Varien_Db_Adapter_Interface
{
    const DEBUG_CONNECT         = 0;
    const DEBUG_TRANSACTION     = 1;
    const DEBUG_QUERY           = 2;

    const TIMESTAMP_FORMAT      = 'Y-m-d H:i:s';
    const DATETIME_FORMAT       = 'Y-m-d H:i:s';
    const DATE_FORMAT           = 'Y-m-d';

    const DDL_DESCRIBE          = 1;
    const DDL_CREATE            = 2;
    const DDL_INDEX             = 3;
    const DDL_FOREIGN_KEY       = 4;
    const DDL_CACHE_PREFIX      = 'DB_PDO_MYSQL_DDL';
    const DDL_CACHE_TAG         = 'DB_PDO_MYSQL_DDL';

    const LENGTH_TABLE_NAME     = 64;
    const LENGTH_INDEX_NAME     = 64;
    const LENGTH_FOREIGN_NAME   = 64;

    /**
     * Those constants are defining the possible address types
     */
    const ADDRESS_TYPE_HOSTNAME     = 'hostname';
    const ADDRESS_TYPE_UNIX_SOCKET  = 'unix_socket';
    const ADDRESS_TYPE_IPV4_ADDRESS = 'ipv4';
    const ADDRESS_TYPE_IPV6_ADDRESS = 'ipv6';

    /**
     * MEMORY engine type for MySQL tables
     */
    const ENGINE_MEMORY = 'MEMORY';

    /**
     * Default class name for a DB statement.
     *
     * @var string
     */
    protected $_defaultStmtClass = 'Varien_Db_Statement_Pdo_Mysql';

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
     * SQL bind params. Used temporarily by regexp callback.
     *
     * @var array
     */
    protected $_bindParams          = array();

    /**
     * Autoincrement for bind value. Used by regexp callback.
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
     * @var float
     */
    protected $_logQueryTime        = 0.05;

    /**
     * Log all queries (ignored minimum query duration time)
     *
     * @var bool
     */
    protected $_logAllQueries       = false;

    /**
     * Add to log call stack data (backtrace)
     *
     * @var bool
     */
    protected $_logCallStack        = false;

    /**
     * Path to SQL debug data log
     *
     * @var string
     */
    protected $_debugFile           = 'var/debug/pdo_mysql.log';

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
     * Cache frontend adapter instance
     *
     * @var Zend_Cache_Core
     */
    protected $_cacheAdapter;

    /**
     * DDL cache allowing flag
     * @var bool
     */
    protected $_isDdlCacheAllowed = true;

    /**
     * MySQL column - Table DDL type pairs
     *
     * @var array
     */
    protected $_ddlColumnTypes      = array(
        Varien_Db_Ddl_Table::TYPE_BOOLEAN       => 'bool',
        Varien_Db_Ddl_Table::TYPE_SMALLINT      => 'smallint',
        Varien_Db_Ddl_Table::TYPE_INTEGER       => 'int',
        Varien_Db_Ddl_Table::TYPE_BIGINT        => 'bigint',
        Varien_Db_Ddl_Table::TYPE_FLOAT         => 'float',
        Varien_Db_Ddl_Table::TYPE_DECIMAL       => 'decimal',
        Varien_Db_Ddl_Table::TYPE_NUMERIC       => 'decimal',
        Varien_Db_Ddl_Table::TYPE_DATE          => 'date',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP     => 'timestamp',
        Varien_Db_Ddl_Table::TYPE_DATETIME      => 'datetime',
        Varien_Db_Ddl_Table::TYPE_TEXT          => 'text',
        Varien_Db_Ddl_Table::TYPE_BLOB          => 'blob',
        Varien_Db_Ddl_Table::TYPE_VARBINARY     => 'blob'
    );

    /**
     * All possible DDL statements
     * First 3 symbols for each statement
     *
     * @var array
     */
    protected $_ddlRoutines = array('alt', 'cre', 'ren', 'dro', 'tru');

    /**
     * DDL statements for temporary tables
     *
     * @var string
     */
    protected $_tempRoutines =  '#^\w+\s+temporary\s#im';

    /**
     * Allowed interval units array
     *
     * @var array
     */
    protected $_intervalUnits = array(
        self::INTERVAL_YEAR     => 'YEAR',
        self::INTERVAL_MONTH    => 'MONTH',
        self::INTERVAL_DAY      => 'DAY',
        self::INTERVAL_HOUR     => 'HOUR',
        self::INTERVAL_MINUTE   => 'MINUTE',
        self::INTERVAL_SECOND   => 'SECOND',
    );

    /**
     * Hook callback to modify queries. Mysql specific property, designed only for backwards compatibility.
     *
     * @var array|null
     */
    protected $_queryHook = null;

    /**
     * Begin new DB transaction for connection
     *
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function beginTransaction()
    {
        if ($this->_transactionLevel === 0) {
            $this->_debugTimer();
            parent::beginTransaction();
            $this->_debugStat(self::DEBUG_TRANSACTION, 'BEGIN');
        }
        ++$this->_transactionLevel;
        return $this;
    }

    /**
     * Commit DB transaction
     *
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function commit()
    {
        if ($this->_transactionLevel === 1) {
            $this->_debugTimer();
            parent::commit();
            $this->_debugStat(self::DEBUG_TRANSACTION, 'COMMIT');
        }
        --$this->_transactionLevel;
        return $this;
    }

    /**
     * Rollback DB transaction
     *
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function rollback()
    {
        if ($this->_transactionLevel === 1) {
            $this->_debugTimer();
            parent::rollBack();
            $this->_debugStat(self::DEBUG_TRANSACTION, 'ROLLBACK');
        }
        --$this->_transactionLevel;
        return $this;
    }

    /**
     * Get adapter transaction level state. Return 0 if all transactions are complete
     *
     * @return int
     */
    public function getTransactionLevel()
    {
        return $this->_transactionLevel;
    }

    /**
     * Convert date to DB format
     *
     * @param   mixed $date
     * @return  string
     */
    public function convertDate($date)
    {
        return $this->formatDate($date, false);
    }

    /**
     * Convert date and time to DB format
     *
     * @param   mixed $date
     * @return  string
     */
    public function convertDateTime($datetime)
    {
        return $this->formatDate($datetime, true);
    }

    /**
     * Parse a source hostname and generate a host info
     * @param $hostName
     *
     * @return Varien_Object
     */
    protected function _getHostInfo($hostName)
    {
        $hostInfo = new Varien_Object();
        $matches = array();
        if (strpos($hostName, '/') !== false) {
            $hostInfo->setAddressType(self::ADDRESS_TYPE_UNIX_SOCKET)
                ->setUnixSocket($hostName);
        } elseif (
            preg_match(
                '/^\[(([0-9a-f]{1,4})?(:([0-9a-f]{1,4})?){1,}:([0-9a-f]{1,4}))(%[0-9a-z]+)?\](:([0-9]+))?$/i',
                $hostName,
                $matches
            )
        ) {
            $hostName = isset($matches[1]) ? $matches[1] : null;
            !is_null($hostName) && isset($matches[6]) && ($hostName .= $matches[6]);
            $hostInfo->setAddressType(self::ADDRESS_TYPE_IPV6_ADDRESS)
                ->setHostName($hostName)
                ->setPort(isset($matches[8]) ? $matches[8] : null);
        } elseif (
            preg_match(
                '/^(([0-9a-f]{1,4})?(:([0-9a-f]{1,4})?){1,}:([0-9a-f]{1,4}))(%[0-9a-z]+)?$/i',
                $hostName,
                $matches
            )
        ) {
            $hostName = isset($matches[1]) ? $matches[1] : null;
            !is_null($hostName) && isset($matches[6]) && ($hostName .= $matches[6]);
            $hostInfo->setAddressType(self::ADDRESS_TYPE_IPV6_ADDRESS)
                ->setHostName($hostName);
        } elseif (strpos($hostName, ':') !== false) {
            list($hostAddress, $hostPort) = explode(':', $hostName);
            $hostInfo->setAddressType(
                filter_var($hostAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
                    ? self::ADDRESS_TYPE_IPV4_ADDRESS
                    : self::ADDRESS_TYPE_HOSTNAME
            )->setHostName($hostAddress)
                ->setPort($hostPort);
        } else {
            $hostInfo->setAddressType(
                filter_var($hostName, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
                    ? self::ADDRESS_TYPE_IPV4_ADDRESS
                    : self::ADDRESS_TYPE_HOSTNAME
            )->setHostName($hostName);
        }

        return $hostInfo;
    }

    /**
     * Creates a PDO object and connects to the database.
     *
     * @throws Zend_Db_Adapter_Exception
     */
    protected function _connect()
    {
        if ($this->_connection) {
            return;
        }

        if (!extension_loaded('pdo_mysql')) {
            throw new Zend_Db_Adapter_Exception('pdo_mysql extension is not installed');
        }


        $hostInfo = $this->_getHostInfo($this->_config['host']);

        switch ($hostInfo->getAddressType()) {
            case self::ADDRESS_TYPE_UNIX_SOCKET:
                $this->_config['unix_socket'] = $hostInfo->getUnixSocket();
                unset($this->_config['host']);
                break;
            case self::ADDRESS_TYPE_IPV6_ADDRESS: // break intentionally omitted
            case self::ADDRESS_TYPE_IPV4_ADDRESS: // break intentionally omitted
            case self::ADDRESS_TYPE_HOSTNAME:
                $this->_config['host'] = $hostInfo->getHostName();
                if ($hostInfo->getPort()) {
                    $this->_config['port'] = $hostInfo->getPort();
                }
                break;
            default:
                break;
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

    /**
     * Run RAW Query
     *
     * @param string $sql
     * @return Zend_Db_Statement_Interface
     * @throws PDOException
     */
    public function raw_query($sql)
    {
        $lostConnectionMessage = 'SQLSTATE[HY000]: General error: 2013 Lost connection to MySQL server during query';
        $tries = 0;
        do {
            $retry = false;
            try {
                $result = $this->query($sql);
            } catch (Exception $e) {
                // Convert to PDOException to maintain backwards compatibility with usage of MySQL adapter
                if ($e instanceof Zend_Db_Statement_Exception) {
                    $e = $e->getPrevious();
                    if (!($e instanceof PDOException)) {
                        $e = new PDOException($e->getMessage(), $e->getCode());
                    }
                }
                // Check to reconnect
                if ($tries < 10 && $e->getMessage() == $lostConnectionMessage) {
                    $retry = true;
                    $tries++;
                } else {
                    throw $e;
                }
            }
        } while ($retry);

        return $result;
    }

    /**
     * Run RAW query and Fetch First row
     *
     * @param string $sql
     * @param string|int $field
     * @return boolean
     */
    public function raw_fetchRow($sql, $field = null)
    {
        $result = $this->raw_query($sql);
        if (!$result) {
            return false;
        }

        $row = $result->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return false;
        }

        if (empty($field)) {
            return $row;
        } else {
            return isset($row[$field]) ? $row[$field] : false;
        }
    }

    /**
     * Check transaction level in case of DDL query
     *
     * @param string|Zend_Db_Select $sql
     * @throws Zend_Db_Adapter_Exception
     */
    protected function _checkDdlTransaction($sql)
    {
        if (is_string($sql) && $this->getTransactionLevel() > 0) {
            $startSql = strtolower(substr(ltrim($sql), 0, 3));
            if (in_array($startSql, $this->_ddlRoutines)
                && (preg_match($this->_tempRoutines, $sql) !== 1)
            ) {
                trigger_error(Varien_Db_Adapter_Interface::ERROR_DDL_MESSAGE, E_USER_ERROR);
            }
        }
    }

    /**
     * Special handling for PDO query().
     * All bind parameter names must begin with ':'.
     *
     * @param string|Zend_Db_Select $sql The SQL statement with placeholders.
     * @param mixed $bind An array of data or data itself to bind to the placeholders.
     * @return Zend_Db_Statement_Pdo
     * @throws Zend_Db_Adapter_Exception To re-throw PDOException.
     */
    public function query($sql, $bind = array())
    {
        $this->_debugTimer();
        try {
            $this->_checkDdlTransaction($sql);
            $this->_prepareQuery($sql, $bind);
            $result = parent::query($sql, $bind);
        } catch (Exception $e) {
            $this->_debugStat(self::DEBUG_QUERY, $sql, $bind);
            $this->_debugException($e);
        }
        $this->_debugStat(self::DEBUG_QUERY, $sql, $bind, $result);
        return $result;
    }

    /**
     * Prepares SQL query by moving to bind all special parameters that can be confused with bind placeholders
     * (e.g. "foo:bar"). And also changes named bind to positional one, because underlying library has problems
     * with named binds.
     *
     * @param Zend_Db_Select|string $sql
     * @param mixed $bind
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    protected function _prepareQuery(&$sql, &$bind = array())
    {
        $sql = (string) $sql;
        if (!is_array($bind)) {
            $bind = array($bind);
        }

        // Mixed bind is not supported - so remember whether it is named bind, to normalize later if required
        $isNamedBind = false;
        if ($bind) {
            foreach ($bind as $k => $v) {
                if (!is_int($k)) {
                    $isNamedBind = true;
                    if ($k[0] != ':') {
                        $bind[":{$k}"] = $v;
                        unset($bind[$k]);
                    }
                }
            }
        }

        // Special query hook
        if ($this->_queryHook) {
            $object = $this->_queryHook['object'];
            $method = $this->_queryHook['method'];
            $object->$method($sql, $bind);
        }

        return $this;
    }

    /**
     * Callback function for preparation of query and bind by regexp.
     * Checks query parameters for special symbols and moves such parameters to bind array as named ones.
     * This method writes to $_bindParams, where query bind parameters are kept.
     * This method requires further normalizing, if bind array is positional.
     *
     * @param array $matches
     * @return string
     */
    public function proccessBindCallback($matches)
    {
        if (isset($matches[6]) && (
            strpos($matches[6], "'") !== false ||
            strpos($matches[6], ':') !== false ||
            strpos($matches[6], '?') !== false)
        ) {
            $bindName = ':_mage_bind_var_' . (++$this->_bindIncrement);
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
     * Normalizes mixed positional-named bind to positional bind, and replaces named placeholders in query to
     * '?' placeholders.
     *
     * @param string $sql
     * @param array $bind
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    protected function _convertMixedBind(&$sql, &$bind)
    {
        $positions  = array();
        $offset     = 0;
        // get positions
        while (true) {
            $pos = strpos($sql, '?', $offset);
            if ($pos !== false) {
                $positions[] = $pos;
                $offset      = ++$pos;
            } else {
                break;
            }
        }

        $bindResult = array();
        $map = array();
        foreach ($bind as $k => $v) {
            // positional
            if (is_int($k)) {
                if (!isset($positions[$k])) {
                    continue;
                }
                $bindResult[$positions[$k]] = $v;
            } else {
                $offset = 0;
                while (true) {
                    $pos = strpos($sql, $k, $offset);
                    if ($pos === false) {
                        break;
                    } else {
                        $offset = $pos + strlen($k);
                        $bindResult[$pos] = $v;
                    }
                }
                $map[$k] = '?';
            }
        }

        ksort($bindResult);
        $bind = array_values($bindResult);
        $sql = strtr($sql, $map);

        return $this;
    }

    /**
     * Sets (removes) query hook.
     *
     * $hook must be either array with 'object' and 'method' entries, or null to remove hook.
     * Previous hook is returned.
     *
     * @param array $hook
     * @return mixed
     */
    public function setQueryHook($hook)
    {
        $prev = $this->_queryHook;
        $this->_queryHook = $hook;
        return $prev;
    }

    /**
     * Executes a SQL statement(s)
     *
     * @param string $sql
     * @throws Zend_Db_Exception
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function multiQuery($sql)
    {
        return $this->multi_query($sql);
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
        $parts = preg_split('#(;|\'|"|\\\\|//|--|\n|/\*|\*/)#', $sql, null,
            PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
        );

        $q      = false;
        $c      = false;
        $stmts  = array();
        $s      = '';

        foreach ($parts as $i => $part) {
            // strings
            if (($part === "'" || $part === '"') && ($i === 0 || $parts[$i-1] !== '\\')) {
                if ($q === false) {
                    $q = $part;
                } elseif ($q === $part) {
                    $q = false;
                }
            }

            // single line comments
            if (($part === '//' || $part === '--') && ($i === 0 || $parts[$i-1] === "\n")) {
                $c = $part;
            } elseif ($part === "\n" && ($c === '//' || $c === '--')) {
                $c = false;
            }

            // multi line comments
            if ($part === '/*' && $c === false) {
                $c = '/*';
            } elseif ($part === '*/' && $c === '/*') {
                $c = false;
            }

            // statements
            if ($part === ';' && $q === false && $c === false) {
                if (trim($s)!=='') {
                    $stmts[] = trim($s);
                    $s = '';
                }
            } else {
                $s .= $part;
            }
        }
        if (trim($s) !== '') {
            $stmts[] = trim($s);
        }

        return $stmts;
    }

    /**
     * Drop the Foreign Key from table
     *
     * @param string $tableName
     * @param string $fkName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function dropForeignKey($tableName, $fkName, $schemaName = null)
    {
        $foreignKeys = $this->getForeignKeys($tableName, $schemaName);
        $fkName = strtoupper($fkName);
        if (substr($fkName, 0, 3) == 'FK_') {
            $fkName = substr($fkName, 3);
        }
        foreach (array($fkName, 'FK_' . $fkName) as $key) {
            if (isset($foreignKeys[$key])) {
                $sql = sprintf('ALTER TABLE %s DROP FOREIGN KEY %s',
                    $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
                    $this->quoteIdentifier($foreignKeys[$key]['FK_NAME'])
                );
                $this->resetDdlCache($tableName, $schemaName);
                $this->raw_query($sql);
            }
        }
        return $this;
    }

    /**
     * Delete index from a table if it exists
     *
     * @deprecated since 1.4.0.1
     * @param string $tableName
     * @param string $keyName
     * @param string $schemaName
     * @return bool|Zend_Db_Statement_Interface
     */
    public function dropKey($tableName, $keyName, $schemaName = null)
    {
        return $this->dropIndex($tableName, $keyName, $schemaName);
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
    public function purgeOrphanRecords($tableName, $columnName, $refTableName, $refColumnName,
                                       $onDelete = Varien_Db_Adapter_Interface::FK_ACTION_CASCADE)
    {
        $onDelete = strtoupper($onDelete);
        if ($onDelete == Varien_Db_Adapter_Interface::FK_ACTION_CASCADE
            || $onDelete == Varien_Db_Adapter_Interface::FK_ACTION_RESTRICT
        ) {
            $sql = sprintf("DELETE p.* FROM %s AS p LEFT JOIN %s AS r ON p.%s = r.%s WHERE r.%s IS NULL",
                $this->quoteIdentifier($tableName),
                $this->quoteIdentifier($refTableName),
                $this->quoteIdentifier($columnName),
                $this->quoteIdentifier($refColumnName),
                $this->quoteIdentifier($refColumnName));
            $this->raw_query($sql);
        } elseif ($onDelete == Varien_Db_Adapter_Interface::FK_ACTION_SET_NULL) {
            $sql = sprintf("UPDATE %s AS p LEFT JOIN %s AS r ON p.%s = r.%s SET p.%s = NULL WHERE r.%s IS NULL",
                $this->quoteIdentifier($tableName),
                $this->quoteIdentifier($refTableName),
                $this->quoteIdentifier($columnName),
                $this->quoteIdentifier($refColumnName),
                $this->quoteIdentifier($columnName),
                $this->quoteIdentifier($refColumnName));
            $this->raw_query($sql);
        }

        return $this;
    }

    /**
     * Add foreign key to table. If FK with same name exist - it will be deleted
     *
     * @deprecated since 1.4.0.1
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
        $refTableName, $refColumnName, $onDelete = Varien_Db_Adapter_Interface::FK_ACTION_CASCADE,
        $onUpdate = Varien_Db_Adapter_Interface::FK_ACTION_CASCADE, $purge = false)
    {
        return $this->addForeignKey($fkName, $tableName, $columnName, $refTableName, $refColumnName,
            $onDelete, $onUpdate, $purge);
    }

    /**
     * Check does table column exist
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $schemaName
     * @return boolean
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
     * Adds new column to table.
     *
     * Generally $defintion must be array with column data to keep this call cross-DB compatible.
     * Using string as $definition is allowed only for concrete DB adapter.
     * Adds primary key if needed
     *
     * @param   string $tableName
     * @param   string $columnName
     * @param   array|string $definition  string specific or universal array DB Server definition
     * @param   string $schemaName
     * @return  int|boolean
     * @throws  Zend_Db_Exception
     */
    public function addColumn($tableName, $columnName, $definition, $schemaName = null)
    {
        if ($this->tableColumnExists($tableName, $columnName, $schemaName)) {
            return true;
        }

        $primaryKey = '';
        if (is_array($definition)) {
            $definition = array_change_key_case($definition, CASE_UPPER);
            if (empty($definition['COMMENT'])) {
                throw new Zend_Db_Exception("Impossible to create a column without comment.");
            }
            if (!empty($definition['PRIMARY'])) {
                $primaryKey = sprintf(', ADD PRIMARY KEY (%s)', $this->quoteIdentifier($columnName));
            }
            $definition = $this->_getColumnDefinition($definition);
        }

        $sql = sprintf('ALTER TABLE %s ADD COLUMN %s %s %s',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
            $this->quoteIdentifier($columnName),
            $definition,
            $primaryKey
        );

        $result = $this->raw_query($sql);

        $this->resetDdlCache($tableName, $schemaName);

        return $result;
    }

    /**
     * Delete table column
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $schemaName
     * @return bool
     */
    public function dropColumn($tableName, $columnName, $schemaName = null)
    {
        if (!$this->tableColumnExists($tableName, $columnName, $schemaName)) {
            return true;
        }

        $alterDrop = array();

        $foreignKeys = $this->getForeignKeys($tableName, $schemaName);
        foreach ($foreignKeys as $fkProp) {
            if ($fkProp['COLUMN_NAME'] == $columnName) {
                $alterDrop[] = 'DROP FOREIGN KEY ' . $this->quoteIdentifier($fkProp['FK_NAME']);
            }
        }

        $alterDrop[] = 'DROP COLUMN ' . $this->quoteIdentifier($columnName);
        $sql = sprintf('ALTER TABLE %s %s',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
            implode(', ', $alterDrop));

        $result = $this->raw_query($sql);
        $this->resetDdlCache($tableName, $schemaName);

        return $result;
    }

    /**
     * Change the column name and definition
     *
     * For change definition of column - use modifyColumn
     *
     * @param string $tableName
     * @param string $oldColumnName
     * @param string $newColumnName
     * @param array $definition
     * @param boolean $flushData        flush table statistic
     * @param string $schemaName
     * @return Varien_Db_Adapter_Pdo_Mysql
     * @throws Zend_Db_Exception
     */
    public function changeColumn($tableName, $oldColumnName, $newColumnName, $definition, $flushData = false,
        $schemaName = null)
    {
        if (!$this->tableColumnExists($tableName, $oldColumnName, $schemaName)) {
            throw new Zend_Db_Exception(sprintf(
                'Column "%s" does not exist in table "%s".',
                $oldColumnName,
                $tableName
            ));
        }

        if (is_array($definition)) {
            $definition = $this->_getColumnDefinition($definition);
        }

        $sql = sprintf('ALTER TABLE %s CHANGE COLUMN %s %s %s',
            $this->quoteIdentifier($tableName),
            $this->quoteIdentifier($oldColumnName),
            $this->quoteIdentifier($newColumnName),
            $definition);

        $result = $this->raw_query($sql);

        if ($flushData) {
            $this->showTableStatus($tableName, $schemaName);
        }
        $this->resetDdlCache($tableName, $schemaName);

        return $result;
    }

    /**
     * Modify the column definition
     *
     * @param string $tableName
     * @param string $columnName
     * @param array|string $definition
     * @param boolean $flushData
     * @param string $schemaName
     * @return Varien_Db_Adapter_Pdo_Mysql
     * @throws Zend_Db_Exception
     */
    public function modifyColumn($tableName, $columnName, $definition, $flushData = false, $schemaName = null)
    {
        if (!$this->tableColumnExists($tableName, $columnName, $schemaName)) {
            throw new Zend_Db_Exception(sprintf('Column "%s" does not exist in table "%s".', $columnName, $tableName));
        }
        if (is_array($definition)) {
            $definition = $this->_getColumnDefinition($definition);
        }

        $sql = sprintf('ALTER TABLE %s MODIFY COLUMN %s %s',
            $this->quoteIdentifier($tableName),
            $this->quoteIdentifier($columnName),
            $definition);

        $this->raw_query($sql);
        if ($flushData) {
            $this->showTableStatus($tableName, $schemaName);
        }
        $this->resetDdlCache($tableName, $schemaName);

        return $this;
    }

    /**
     * Show table status
     *
     * @param string $tableName
     * @param string $schemaName
     * @return array|false
     */
    public function showTableStatus($tableName, $schemaName = null)
    {
        $fromDbName = null;
        if ($schemaName !== null) {
            $fromDbName = ' FROM ' . $this->quoteIdentifier($schemaName);
        }
        $query = sprintf('SHOW TABLE STATUS%s LIKE %s', $fromDbName,  $this->quote($tableName));

        return $this->raw_fetchRow($query);
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
        $cacheKey = $this->_getTableName($tableName, $schemaName);
        $ddl = $this->loadDdlCache($cacheKey, self::DDL_CREATE);
        if ($ddl === false) {
            $sql = 'SHOW CREATE TABLE ' . $this->quoteIdentifier($tableName);
            $ddl = $this->raw_fetchRow($sql, 'Create Table');
            $this->saveDdlCache($cacheKey, self::DDL_CREATE, $ddl);
        }

        return $ddl;
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
        $ddl = $this->loadDdlCache($cacheKey, self::DDL_FOREIGN_KEY);
        if ($ddl === false) {
            $ddl = array();
            $createSql = $this->getCreateTable($tableName, $schemaName);

            // collect CONSTRAINT
            $regExp  = '#,\s+CONSTRAINT `([^`]*)` FOREIGN KEY \(`([^`]*)`\) '
                . 'REFERENCES (`[^`]*\.)?`([^`]*)` \(`([^`]*)`\)'
                . '( ON DELETE (RESTRICT|CASCADE|SET NULL|NO ACTION))?'
                . '( ON UPDATE (RESTRICT|CASCADE|SET NULL|NO ACTION))?#';
            $matches = array();
            preg_match_all($regExp, $createSql, $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                $ddl[strtoupper($match[1])] = array(
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

            $this->saveDdlCache($cacheKey, self::DDL_FOREIGN_KEY, $ddl);
        }

        return $ddl;
    }

    /**
     * Retrieve the foreign keys tree for all tables
     *
     * @return array
     */
    public function getForeignKeysTree()
    {
        $tree = array();
        foreach ($this->listTables() as $table) {
            foreach($this->getForeignKeys($table) as $key) {
                $tree[$table][$key['COLUMN_NAME']] = $key;
            }
        }

        return $tree;
    }

    /**
     * Modify tables, used for upgrade process
     * Change columns definitions, reset foreign keys, change tables comments and engines.
     *
     * The value of each array element is an associative array
     * with the following keys:
     *
     * columns => array; list of columns definitions
     * comment => string; table comment
     * engine  => string; table engine
     *
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function modifyTables($tables)
    {
        $foreignKeys = $this->getForeignKeysTree();
        foreach ($tables as $table => $tableData) {
            if (!$this->isTableExists($table)) {
                continue;
            }
            foreach ($tableData['columns'] as $column =>$columnDefinition) {
                if (!$this->tableColumnExists($table, $column)) {
                    continue;
                }
                $droppedKeys = array();
                foreach($foreignKeys as $keyTable => $columns) {
                    foreach($columns as $columnName => $keyOptions) {
                        if ($table == $keyOptions['REF_TABLE_NAME'] && $column == $keyOptions['REF_COLUMN_NAME']) {
                            $this->dropForeignKey($keyTable, $keyOptions['FK_NAME']);
                            $droppedKeys[] = $keyOptions;
                        }
                    }
                }

                $this->modifyColumn($table, $column, $columnDefinition);

                foreach ($droppedKeys as $options) {
                    unset($columnDefinition['identity'], $columnDefinition['primary'], $columnDefinition['comment']);

                    $onDelete = $options['ON_DELETE'];
                    $onUpdate = $options['ON_UPDATE'];

                    if ($onDelete == Varien_Db_Adapter_Interface::FK_ACTION_SET_NULL
                        || $onUpdate == Varien_Db_Adapter_Interface::FK_ACTION_SET_NULL) {
                           $columnDefinition['nullable'] = true;
                    }
                    $this->modifyColumn($options['TABLE_NAME'], $options['COLUMN_NAME'], $columnDefinition);
                    $this->addForeignKey(
                        $options['FK_NAME'],
                        $options['TABLE_NAME'],
                        $options['COLUMN_NAME'],
                        $options['REF_TABLE_NAME'],
                        $options['REF_COLUMN_NAME'],
                        ($onDelete) ? $onDelete : Varien_Db_Adapter_Interface::FK_ACTION_NO_ACTION,
                        ($onUpdate) ? $onUpdate : Varien_Db_Adapter_Interface::FK_ACTION_NO_ACTION
                    );
                }
            }
            if (!empty($tableData['comment'])) {
                $this->changeTableComment($table, $tableData['comment']);
            }
            if (!empty($tableData['engine'])) {
                $this->changeTableEngine($table, $tableData['engine']);
            }
        }

        return $this;
    }

    /**
     * Retrieve table index information
     *
     * The return value is an associative array keyed by the UPPERCASE index key (except for primary key,
     * that is always stored under 'PRIMARY' key) as returned by the RDBMS.
     *
     * The value of each array element is an associative array
     * with the following keys:
     *
     * SCHEMA_NAME      => string; name of database or schema
     * TABLE_NAME       => string; name of the table
     * KEY_NAME         => string; the original index name
     * COLUMNS_LIST     => array; array of index column names
     * INDEX_TYPE       => string; lowercase, create index type
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
        $ddl = $this->loadDdlCache($cacheKey, self::DDL_INDEX);
        if ($ddl === false) {
            $ddl = array();

            $sql = sprintf('SHOW INDEX FROM %s',
                $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)));
            foreach ($this->fetchAll($sql) as $row) {
                $fieldKeyName   = 'Key_name';
                $fieldNonUnique = 'Non_unique';
                $fieldColumn    = 'Column_name';
                $fieldIndexType = 'Index_type';

                if (strtolower($row[$fieldKeyName]) == Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY) {
                    $indexType  = Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY;
                } elseif ($row[$fieldNonUnique] == 0) {
                    $indexType  = Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE;
                } elseif (strtolower($row[$fieldIndexType]) == Varien_Db_Adapter_Interface::INDEX_TYPE_FULLTEXT) {
                    $indexType  = Varien_Db_Adapter_Interface::INDEX_TYPE_FULLTEXT;
                } else {
                    $indexType  = Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX;
                }

                $upperKeyName = strtoupper($row[$fieldKeyName]);
                if (isset($ddl[$upperKeyName])) {
                    $ddl[$upperKeyName]['fields'][] = $row[$fieldColumn]; // for compatible
                    $ddl[$upperKeyName]['COLUMNS_LIST'][] = $row[$fieldColumn];
                } else {
                    $ddl[$upperKeyName] = array(
                        'SCHEMA_NAME'   => $schemaName,
                        'TABLE_NAME'    => $tableName,
                        'KEY_NAME'      => $row[$fieldKeyName],
                        'COLUMNS_LIST'  => array($row[$fieldColumn]),
                        'INDEX_TYPE'    => $indexType,
                        'INDEX_METHOD'  => $row[$fieldIndexType],
                        'type'          => strtolower($indexType), // for compatibility
                        'fields'        => array($row[$fieldColumn]) // for compatibility
                    );
                }
            }
            $this->saveDdlCache($cacheKey, self::DDL_INDEX, $ddl);
        }

        return $ddl;
    }

    /**
     * Add Index Key
     *
     * @deprecated since 1.5.0.0
     * @param string $tableName
     * @param string $indexName
     * @param string|array $fields
     * @param string $indexType
     * @param string $schemaName
     * @return Zend_Db_Statement_Interface
     */
    public function addKey($tableName, $indexName, $fields, $indexType = 'index', $schemaName = null)
    {
        return $this->addIndex($tableName, $indexName, $fields, $indexType, $schemaName);
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
            $where[] = $this->quoteInto($field . '=?', $ids[$i++]);
        }

        if (!$where) {
            return $this;
        }
        $whereCond = implode(' AND ', $where);
        $sql = sprintf('SELECT COUNT(*) as `cnt` FROM `%s` WHERE %s', $table, $whereCond);

        $cnt = $this->raw_fetchRow($sql, 'cnt');
        if ($cnt > 1) {
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
     * Logging debug information
     *
     * @param int $type
     * @param string $sql
     * @param array $bind
     * @param Zend_Db_Statement_Pdo $result
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

        if (!$this->_logAllQueries && $time < $this->_logQueryTime) {
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
                    $code .= 'BIND: ' . var_export($bind, true) . $nl;
                }
                if ($result instanceof Zend_Db_Statement_Pdo) {
                    $code .= 'AFF: ' . $result->rowCount() . $nl;
                }
                break;
        }
        $code .= 'TIME: ' . $time . $nl;

        if ($this->_logCallStack) {
            $code .= 'TRACE: ' . Varien_Debug::backtrace(true, false) . $nl;
        }

        $code .= $nl;

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
        $code = 'EXCEPTION ' . $nl . $e . $nl . $nl;
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
        $str = '## ' . date('Y-m-d H:i:s') . "\r\n" . $str;
        if (!$this->_debugIoAdapter) {
            $this->_debugIoAdapter = new Varien_Io_File();
            $dir = Mage::getBaseDir() . DS . $this->_debugIoAdapter->dirname($this->_debugFile);
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
     * Retrieve Id for cache
     *
     * @param string $tableKey
     * @param int $ddlType
     * @return string
     */
    protected function _getCacheId($tableKey, $ddlType)
    {
        return sprintf('%s_%s_%s', self::DDL_CACHE_PREFIX, $tableKey, $ddlType);
    }

    /**
     * Load DDL data from cache
     * Return false if cache does not exists
     *
     * @param string $tableCacheKey the table cache key
     * @param int $ddlType          the DDL constant
     * @return string|array|int|false
     */
    public function loadDdlCache($tableCacheKey, $ddlType)
    {
        if (!$this->_isDdlCacheAllowed) {
            return false;
        }
        if (isset($this->_ddlCache[$ddlType][$tableCacheKey])) {
            return $this->_ddlCache[$ddlType][$tableCacheKey];
        }

        if ($this->_cacheAdapter instanceof Zend_Cache_Core) {
            $cacheId = $this->_getCacheId($tableCacheKey, $ddlType);
            $data = $this->_cacheAdapter->load($cacheId);
            if ($data !== false) {
                $data = unserialize($data);
                $this->_ddlCache[$ddlType][$tableCacheKey] = $data;
            }
            return $data;
        }

        return false;
    }

    /**
     * Save DDL data into cache
     *
     * @param string $tableCacheKey
     * @param int $ddlType
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function saveDdlCache($tableCacheKey, $ddlType, $data)
    {
        if (!$this->_isDdlCacheAllowed) {
            return $this;
        }
        $this->_ddlCache[$ddlType][$tableCacheKey] = $data;

        if ($this->_cacheAdapter instanceof Zend_Cache_Core) {
            $cacheId = $this->_getCacheId($tableCacheKey, $ddlType);
            $data = serialize($data);
            $this->_cacheAdapter->save($data, $cacheId, array(self::DDL_CACHE_TAG));
        }

        return $this;
    }

    /**
     * Reset cached DDL data from cache
     * if table name is null - reset all cached DDL data
     *
     * @param string $tableName
     * @param string $schemaName OPTIONAL
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function resetDdlCache($tableName = null, $schemaName = null)
    {
        if (!$this->_isDdlCacheAllowed) {
            return $this;
        }
        if ($tableName === null) {
            $this->_ddlCache = array();
            if ($this->_cacheAdapter instanceof Zend_Cache_Core) {
                $this->_cacheAdapter->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array(self::DDL_CACHE_TAG));
            }
        } else {
            $cacheKey = $this->_getTableName($tableName, $schemaName);

            $ddlTypes = array(self::DDL_DESCRIBE, self::DDL_CREATE, self::DDL_INDEX, self::DDL_FOREIGN_KEY);
            foreach ($ddlTypes as $ddlType) {
                unset($this->_ddlCache[$ddlType][$cacheKey]);
            }

            if ($this->_cacheAdapter instanceof Zend_Cache_Core) {
                foreach ($ddlTypes as $ddlType) {
                    $cacheId = $this->_getCacheId($cacheKey, $ddlType);
                    $this->_cacheAdapter->remove($cacheId);
                }
            }
        }

        return $this;
    }

    /**
     * Disallow DDL caching
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function disallowDdlCache()
    {
        $this->_isDdlCacheAllowed = false;
        return $this;
    }

    /**
     * Allow DDL caching
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function allowDdlCache()
    {
        $this->_isDdlCacheAllowed = true;
        return $this;
    }

    /**
     * Decorate a table info by detecting and parsing the binary/varbinary fields
     * @param $tableColumnInfo
     *
     * @return mixed
     */
    public function decorateTableInfo($tableColumnInfo)
    {
        $matches = array();
        if (preg_match('/^((?:var)?binary)\((\d+)\)/', $tableColumnInfo['DATA_TYPE'], $matches)) {
            list ($fieldFullDescription, $fieldType, $fieldLength) = $matches;
            $tableColumnInfo['DATA_TYPE'] = $fieldType;
            $tableColumnInfo['LENGTH'] = $fieldLength;
        }
        return $tableColumnInfo;
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
        $ddl = $this->loadDdlCache($cacheKey, self::DDL_DESCRIBE);
        if ($ddl === false) {
            $ddl = array_map(
                array(
                     $this,
                     'decorateTableInfo'
                ),
                parent::describeTable($tableName, $schemaName)
            );
            /**
             * Remove bug in some MySQL versions, when int-column without default value is described as:
             * having default empty string value
             */
            $affected = array('tinyint', 'smallint', 'mediumint', 'int', 'bigint');
            foreach ($ddl as $key => $columnData) {
                if (($columnData['DEFAULT'] === '') && (array_search($columnData['DATA_TYPE'], $affected) !== FALSE)) {
                    $ddl[$key]['DEFAULT'] = null;
                }
            }
            $this->saveDdlCache($cacheKey, self::DDL_DESCRIBE, $ddl);
        }

        return $ddl;
    }

    /**
     * Format described column to definition, ready to be added to ddl table.
     * Return array with keys: name, type, length, options, comment
     *
     * @param  array $columnData
     * @return array
     */
    public function getColumnCreateByDescribe($columnData)
    {
        $type = $this->_getColumnTypeByDdl($columnData);
        $options = array();

        if ($columnData['IDENTITY'] === true) {
            $options['identity'] = true;
        }
        if ($columnData['UNSIGNED'] === true) {
            $options['unsigned'] = true;
        }
        if ($columnData['NULLABLE'] === false
            && !($type == Varien_Db_Ddl_Table::TYPE_TEXT && strlen($columnData['DEFAULT']) != 0)
        ) {
            $options['nullable'] = false;
        }
        if ($columnData['PRIMARY'] === true) {
            $options['primary'] = true;
        }
        if (!is_null($columnData['DEFAULT'])
            && $type != Varien_Db_Ddl_Table::TYPE_TEXT
        ) {
            $options['default'] = $this->quote($columnData['DEFAULT']);
        }
        if (strlen($columnData['SCALE']) > 0) {
            $options['scale'] = $columnData['SCALE'];
        }
        if (strlen($columnData['PRECISION']) > 0) {
            $options['precision'] = $columnData['PRECISION'];
        }

        $comment = uc_words($columnData['COLUMN_NAME'], ' ');

        $result = array(
            'name'      => $columnData['COLUMN_NAME'],
            'type'      => $type,
            'length'    => $columnData['LENGTH'],
            'options'   => $options,
            'comment'   => $comment
        );

        return $result;
    }

    /**
     * Create Varien_Db_Ddl_Table object by data from describe table
     *
     * @param $tableName
     * @param $newTableName
     * @return Varien_Db_Ddl_Table
     */
    public function createTableByDdl($tableName, $newTableName)
    {
        $describe = $this->describeTable($tableName);
        $table = $this->newTable($newTableName)
            ->setComment(uc_words($newTableName, ' '));

        foreach ($describe as $columnData) {
            $columnInfo = $this->getColumnCreateByDescribe($columnData);

            $table->addColumn(
                $columnInfo['name'],
                $columnInfo['type'],
                $columnInfo['length'],
                $columnInfo['options'],
                $columnInfo['comment']
            );
        }

        $indexes = $this->getIndexList($tableName);
        foreach ($indexes as $indexData) {
            /**
             * Do not create primary index - it is created with identity column.
             * For reliability check both name and type, because these values can start to differ in future.
             */
            if (($indexData['KEY_NAME'] == 'PRIMARY')
                || ($indexData['INDEX_TYPE'] == Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY)
            ) {
                continue;
            }

            $fields = $indexData['COLUMNS_LIST'];
            $options = array('type' => $indexData['INDEX_TYPE']);
            $table->addIndex($this->getIndexName($newTableName, $fields, $indexData['INDEX_TYPE']), $fields, $options);
        }

        $foreignKeys = $this->getForeignKeys($tableName);
        foreach ($foreignKeys as $keyData) {
            $fkName = $this->getForeignKeyName(
                $newTableName, $keyData['COLUMN_NAME'], $keyData['REF_TABLE_NAME'], $keyData['REF_COLUMN_NAME']
            );
            $onDelete = $this->_getDdlAction($keyData['ON_DELETE']);
            $onUpdate = $this->_getDdlAction($keyData['ON_UPDATE']);

            $table->addForeignKey(
                $fkName, $keyData['COLUMN_NAME'], $keyData['REF_TABLE_NAME'],
                $keyData['REF_COLUMN_NAME'], $onDelete, $onUpdate
            );
        }

        // Set additional options
        $tableData = $this->showTableStatus($tableName);
        $table->setOption('type', $tableData['Engine']);

        return $table;
    }

    /**
     * Modify the column definition by data from describe table
     *
     * @param string $tableName
     * @param string $columnName
     * @param array $definition
     * @param boolean $flushData
     * @param string $schemaName
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function modifyColumnByDdl($tableName, $columnName, $definition, $flushData = false, $schemaName = null)
    {
        $definition = array_change_key_case($definition, CASE_UPPER);
        $definition['COLUMN_TYPE'] = $this->_getColumnTypeByDdl($definition);
        if (array_key_exists('DEFAULT', $definition) && is_null($definition['DEFAULT'])) {
            unset($definition['DEFAULT']);
        }

        return $this->modifyColumn($tableName, $columnName, $definition, $flushData, $schemaName);
    }

    /**
     * Retrieve column data type by data from describe table
     *
     * @param array $column
     * @return string
     */
    protected function _getColumnTypeByDdl($column)
    {
        switch ($column['DATA_TYPE']) {
            case 'bool':
                return Varien_Db_Ddl_Table::TYPE_BOOLEAN;
            case 'tinytext':
            case 'char':
            case 'varchar':
            case 'text':
            case 'mediumtext':
            case 'longtext':
                return Varien_Db_Ddl_Table::TYPE_TEXT;
            case 'blob':
            case 'mediumblob':
            case 'longblob':
                return Varien_Db_Ddl_Table::TYPE_BLOB;
            case 'tinyint':
            case 'smallint':
                return Varien_Db_Ddl_Table::TYPE_SMALLINT;
            case 'mediumint':
            case 'int':
                return Varien_Db_Ddl_Table::TYPE_INTEGER;
            case 'bigint':
                return Varien_Db_Ddl_Table::TYPE_BIGINT;
            case 'datetime':
                return Varien_Db_Ddl_Table::TYPE_DATETIME;
            case 'timestamp':
                return Varien_Db_Ddl_Table::TYPE_TIMESTAMP;
            case 'date':
                return Varien_Db_Ddl_Table::TYPE_DATE;
            case 'float':
                return Varien_Db_Ddl_Table::TYPE_FLOAT;
            case 'decimal':
            case 'numeric':
                return Varien_Db_Ddl_Table::TYPE_DECIMAL;
            case 'varbinary':
                return Varien_Db_Ddl_Table::TYPE_VARBINARY;
                break;
        }
    }

    /**
     * Truncate table
     *
     * @deprecated since 1.4.0.1
     * @param string $tableName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function truncate($tableName, $schemaName = null)
    {
        return $this->truncateTable($tableName, $schemaName);
    }

    /**
     * Change table storage engine
     *
     * @param string $tableName
     * @param string $engine
     * @param string $schemaName
     * @return mixed
     */
    public function changeTableEngine($tableName, $engine, $schemaName = null)
    {
        $table = $this->quoteIdentifier($this->_getTableName($tableName, $schemaName));
        $sql   = sprintf('ALTER TABLE %s ENGINE=%s', $table, $engine);

        return $this->raw_query($sql);
    }

    /**
     * Change table comment
     *
     * @param string $tableName
     * @param string $comment
     * @param string $schemaName
     * @return mixed
     */
    public function changeTableComment($tableName, $comment, $schemaName = null)
    {
        $table = $this->quoteIdentifier($this->_getTableName($tableName, $schemaName));
        $sql   = sprintf("ALTER TABLE %s COMMENT='%s'", $table, $comment);

        return $this->raw_query($sql);
    }

    /**
     * Change table auto increment value
     *
     * @param string $tableName
     * @param string $increment
     * @param null|string $schemaName
     * @return Zend_Db_Statement_Interface
     */
    public function changeTableAutoIncrement($tableName, $increment, $schemaName = null)
    {
        $table = $this->quoteIdentifier($this->_getTableName($tableName, $schemaName));
        $sql = sprintf('ALTER TABLE %s AUTO_INCREMENT=%d', $table, $increment);
        return $this->raw_query($sql);
    }

    /**
     * Inserts a table row with specified data
     * Special for Zero values to identity column
     *
     * @param string $table
     * @param array $bind
     * @return int The number of affected rows.
     */
    public function insertForce($table, array $bind)
    {
        $this->raw_query("SET @OLD_INSERT_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO'");
        $result = $this->insert($table, $bind);
        $this->raw_query("SET SQL_MODE=IFNULL(@OLD_INSERT_SQL_MODE,'')");

        return $result;
    }

    /**
     * Inserts a table row with specified data.
     *
     * @param mixed $table The table to insert data into.
     * @param array $data Column-value pairs or array of column-value pairs.
     * @param array $fields update fields pairs or values
     * @return int The number of affected rows.
     * @throws Zend_Db_Exception
     */
    public function insertOnDuplicate($table, array $data, array $fields = array())
    {
        // extract and quote col names from the array keys
        $row    = reset($data); // get first element from data array
        $bind   = array(); // SQL bind array
        $values = array();

        if (is_array($row)) { // Array of column-value pairs
            $cols = array_keys($row);
            foreach ($data as $row) {
                if (array_diff($cols, array_keys($row))) {
                    throw new Zend_Db_Exception('Invalid data for insert');
                }
                $values[] = $this->_prepareInsertData($row, $bind);
            }
            unset($row);
        } else { // Column-value pairs
            $cols     = array_keys($data);
            $values[] = $this->_prepareInsertData($data, $bind);
        }

        $updateFields = array();
        if (empty($fields)) {
            $fields = $cols;
        }

        // quote column names
//        $cols = array_map(array($this, 'quoteIdentifier'), $cols);

        // prepare ON DUPLICATE KEY conditions
        foreach ($fields as $k => $v) {
            $field = $value = null;
            if (!is_numeric($k)) {
                $field = $this->quoteIdentifier($k);
                if ($v instanceof Zend_Db_Expr) {
                    $value = $v->__toString();
                } elseif (is_string($v)) {
                    $value = sprintf('VALUES(%s)', $this->quoteIdentifier($v));
                } elseif (is_numeric($v)) {
                    $value = $this->quoteInto('?', $v);
                }
            } elseif (is_string($v)) {
                $value = sprintf('VALUES(%s)', $this->quoteIdentifier($v));
                $field = $this->quoteIdentifier($v);
            }

            if ($field && $value) {
                $updateFields[] = sprintf('%s = %s', $field, $value);
            }
        }

        $insertSql = $this->_getInsertSqlQuery($table, $cols, $values);
        if ($updateFields) {
            $insertSql .= ' ON DUPLICATE KEY UPDATE ' . implode(', ', $updateFields);
        }
        // execute the statement and return the number of affected rows
        $stmt   = $this->query($insertSql, array_values($bind));
        $result = $stmt->rowCount();

        return $result;
    }

    /**
     * Inserts a table multiply rows with specified data.
     *
     * @param mixed $table The table to insert data into.
     * @param array $data Column-value pairs or array of Column-value pairs.
     * @return int The number of affected rows.
     * @throws Zend_Db_Exception
     */
    public function insertMultiple($table, array $data)
    {
        $row = reset($data);
        // support insert syntaxes
        if (!is_array($row)) {
            return $this->insert($table, $data);
        }

        // validate data array
        $cols = array_keys($row);
        $insertArray = array();
        foreach ($data as $row) {
            $line = array();
            if (array_diff($cols, array_keys($row))) {
                throw new Zend_Db_Exception('Invalid data for insert');
            }
            foreach ($cols as $field) {
                $line[] = $row[$field];
            }
            $insertArray[] = $line;
        }
        unset($row);

        return $this->insertArray($table, $cols, $insertArray);
    }

    /**
     * Insert array to table based on columns definition
     *
     * @param   string $table
     * @param   array $columns
     * @param   array $data
     * @return  int
     * @throws  Zend_Db_Exception
     */
    public function insertArray($table, array $columns, array $data)
    {
        $values       = array();
        $bind         = array();
        $columnsCount = count($columns);
        foreach ($data as $row) {
            if ($columnsCount != count($row)) {
                throw new Zend_Db_Exception('Invalid data for insert');
            }
            $values[] = $this->_prepareInsertData($row, $bind);
        }

        $insertQuery = $this->_getInsertSqlQuery($table, $columns, $values);

        // execute the statement and return the number of affected rows
        $stmt   = $this->query($insertQuery, $bind);
        $result = $stmt->rowCount();

        return $result;
    }

    /**
     * Inserts a table row with specified data.
     *
     * @param mixed $table The table to insert data into.
     * @param array $bind Column-value pairs.
     * @return int The number of affected rows.
     * @throws Zend_Db_Adapter_Exception
     */
    public function insertIgnore($table, array $bind)
    {
        // extract and quote col names from the array keys
        $cols = array();
        $vals = array();
        $i = 0;
        foreach ($bind as $col => $val) {
            $cols[] = $this->quoteIdentifier($col, true);
            if ($val instanceof Zend_Db_Expr) {
                $vals[] = $val->__toString();
                unset($bind[$col]);
            } else {
                if ($this->supportsParameters('positional')) {
                    $vals[] = '?';
                } else {
                    if ($this->supportsParameters('named')) {
                        unset($bind[$col]);
                        $bind[':col'.$i] = $val;
                        $vals[] = ':col'.$i;
                        $i++;
                    } else {
                        /** @see Zend_Db_Adapter_Exception */
                        #require_once 'Zend/Db/Adapter/Exception.php';
                        throw new Zend_Db_Adapter_Exception(
                            get_class($this) ." doesn't support positional or named binding"
                        );
                    }
                }
            }
        }

        // build the statement
        $sql = "INSERT IGNORE INTO "
            . $this->quoteIdentifier($table, true)
            . ' (' . implode(', ', $cols) . ') '
            . 'VALUES (' . implode(', ', $vals) . ')';

        // execute the statement and return the number of affected rows
        if ($this->supportsParameters('positional')) {
            $bind = array_values($bind);
        }
        $stmt = $this->query($sql, $bind);
        $result = $stmt->rowCount();
        return $result;
    }

    /**
     * Set cache adapter
     *
     * @param Zend_Cache_Backend_Interface $adapter
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function setCacheAdapter($adapter)
    {
        $this->_cacheAdapter = $adapter;
        return $this;
    }

    /**
     * Return new DDL Table object
     *
     * @param string $tableName the table name
     * @param string $schemaName the database/schema name
     * @return Varien_Db_Ddl_Table
     */
    public function newTable($tableName = null, $schemaName = null)
    {
        $table = new Varien_Db_Ddl_Table();
        if ($tableName !== null) {
            $table->setName($tableName);
        }
        if ($schemaName !== null) {
            $table->setSchema($schemaName);
        }

        return $table;
    }

    /**
     * Create table
     *
     * @param Varien_Db_Ddl_Table $table
     * @throws Zend_Db_Exception
     * @return Zend_Db_Pdo_Statement
     */
    public function createTable(Varien_Db_Ddl_Table $table)
    {
        $columns = $table->getColumns();
        foreach ($columns as $columnEntry) {
            if (empty($columnEntry['COMMENT'])) {
                throw new Zend_Db_Exception("Cannot create table without columns comments");
            }
        }

        $sqlFragment    = array_merge(
            $this->_getColumnsDefinition($table),
            $this->_getIndexesDefinition($table),
            $this->_getForeignKeysDefinition($table)
        );
        $tableOptions   = $this->_getOptionsDefinition($table);
        $sql = sprintf("CREATE TABLE %s (\n%s\n) %s",
            $this->quoteIdentifier($table->getName()),
            implode(",\n", $sqlFragment),
            implode(" ", $tableOptions));

        return $this->query($sql);
    }

    /**
     * Create temporary table
     *
     * @param Varien_Db_Ddl_Table $table
     * @throws Zend_Db_Exception
     * @return Zend_Db_Pdo_Statement
     */
    public function createTemporaryTable(Varien_Db_Ddl_Table $table)
    {
        $sqlFragment    = array_merge(
            $this->_getColumnsDefinition($table),
            $this->_getIndexesDefinition($table),
            $this->_getForeignKeysDefinition($table)
        );
        $tableOptions   = $this->_getOptionsDefinition($table);
        $sql = sprintf("CREATE TEMPORARY TABLE %s (\n%s\n) %s",
            $this->quoteIdentifier($table->getName()),
            implode(",\n", $sqlFragment),
            implode(" ", $tableOptions));

        return $this->query($sql);
    }

    /**
     * Retrieve columns and primary keys definition array for create table
     *
     * @param Varien_Db_Ddl_Table $table
     * @return array
     * @throws Zend_Db_Exception
     */
    protected function _getColumnsDefinition(Varien_Db_Ddl_Table $table)
    {
        $definition = array();
        $primary    = array();
        $columns    = $table->getColumns();
        if (empty($columns)) {
            throw new Zend_Db_Exception('Table columns are not defined');
        }

        foreach ($columns as $columnData) {
            $columnDefinition = $this->_getColumnDefinition($columnData);
            if ($columnData['PRIMARY']) {
                $primary[$columnData['COLUMN_NAME']] = $columnData['PRIMARY_POSITION'];
            }

            $definition[] = sprintf('  %s %s',
                $this->quoteIdentifier($columnData['COLUMN_NAME']),
                $columnDefinition
            );
        }

        // PRIMARY KEY
        if (!empty($primary)) {
            asort($primary, SORT_NUMERIC);
            $primary      = array_map(array($this, 'quoteIdentifier'), array_keys($primary));
            $definition[] = sprintf('  PRIMARY KEY (%s)', implode(', ', $primary));
        }

        return $definition;
    }

    /**
     * Retrieve table indexes definition array for create table
     *
     * @param Varien_Db_Ddl_Table $table
     * @return array
     */
    protected function _getIndexesDefinition(Varien_Db_Ddl_Table $table)
    {
        $definition = array();
        $indexes    = $table->getIndexes();
        if (!empty($indexes)) {
            foreach ($indexes as $indexData) {
                if (!empty($indexData['TYPE'])) {
                    switch ($indexData['TYPE']) {
                        case 'primary':
                            $indexType = 'PRIMARY KEY';
                            unset($indexData['INDEX_NAME']);
                            break;
                        default:
                            $indexType = strtoupper($indexData['TYPE']);
                            break;
                    }
                } else {
                    $indexType = 'KEY';
                }

                $columns = array();
                foreach ($indexData['COLUMNS'] as $columnData) {
                    $column = $this->quoteIdentifier($columnData['NAME']);
                    if (!empty($columnData['SIZE'])) {
                        $column .= sprintf('(%d)', $columnData['SIZE']);
                    }
                    $columns[] = $column;
                }
                $indexName = isset($indexData['INDEX_NAME']) ? $this->quoteIdentifier($indexData['INDEX_NAME']) : '';
                $definition[] = sprintf('  %s %s (%s)',
                    $indexType,
                    $indexName,
                    implode(', ', $columns)
                );
            }
        }

        return $definition;
    }

    /**
     * Retrieve table foreign keys definition array for create table
     *
     * @param Varien_Db_Ddl_Table $table
     * @return array
     */
    protected function _getForeignKeysDefinition(Varien_Db_Ddl_Table $table)
    {
        $definition = array();
        $relations  = $table->getForeignKeys();

        if (!empty($relations)) {
            foreach ($relations as $fkData) {
                $onDelete = $this->_getDdlAction($fkData['ON_DELETE']);
                $onUpdate = $this->_getDdlAction($fkData['ON_UPDATE']);

                $definition[] = sprintf('  CONSTRAINT %s FOREIGN KEY (%s) REFERENCES %s (%s) ON DELETE %s ON UPDATE %s',
                    $this->quoteIdentifier($fkData['FK_NAME']),
                    $this->quoteIdentifier($fkData['COLUMN_NAME']),
                    $this->quoteIdentifier($fkData['REF_TABLE_NAME']),
                    $this->quoteIdentifier($fkData['REF_COLUMN_NAME']),
                    $onDelete,
                    $onUpdate
                );
            }
        }

        return $definition;
    }

    /**
     * Retrieve table options definition array for create table
     *
     * @param Varien_Db_Ddl_Table $table
     * @return array
     * @throws Zend_Db_Exception
     */
    protected function _getOptionsDefinition(Varien_Db_Ddl_Table $table)
    {
        $definition = array();
        $comment    = $table->getComment();
        if (empty($comment)) {
            throw new Zend_Db_Exception('Comment for table is required and must be defined');
        }
        $definition[] = $this->quoteInto('COMMENT=?', $comment);

        $tableProps = array(
            'type'              => 'ENGINE=%s',
            'checksum'          => 'CHECKSUM=%d',
            'auto_increment'    => 'AUTO_INCREMENT=%d',
            'avg_row_length'    => 'AVG_ROW_LENGTH=%d',
            'max_rows'          => 'MAX_ROWS=%d',
            'min_rows'          => 'MIN_ROWS=%d',
            'delay_key_write'   => 'DELAY_KEY_WRITE=%d',
            'row_format'        => 'row_format=%s',
            'charset'           => 'charset=%s',
            'collate'           => 'COLLATE=%s'
        );
        foreach ($tableProps as $key => $mask) {
            $v = $table->getOption($key);
            if ($v !== null) {
                $definition[] = sprintf($mask, $v);
            }
        }

        return $definition;
    }

    /**
     * Get column definition from description
     *
     * @param  array $options
     * @param  null|string $ddlType
     * @return string
     */
    public function getColumnDefinitionFromDescribe($options, $ddlType = null)
    {
        $columnInfo = $this->getColumnCreateByDescribe($options);
        foreach ($columnInfo['options'] as $key => $value) {
            $columnInfo[$key] = $value;
        }
        return $this->_getColumnDefinition($columnInfo, $ddlType);
    }

    /**
     * Retrieve column definition fragment
     *
     * @param array $options
     * @param string $ddlType Table DDL Column type constant
     * @throws Varien_Exception
     * @return string
     * @throws Zend_Db_Exception
     */
    protected function _getColumnDefinition($options, $ddlType = null)
    {
        // convert keys to uppercase
        $options    = array_change_key_case($options, CASE_UPPER);
        $cType      = null;
        $cUnsigned  = false;
        $cNullable  = true;
        $cDefault   = false;
        $cIdentity  = false;

        // detect and validate column type
        if ($ddlType === null) {
            $ddlType = $this->_getDdlType($options);
        }

        if (empty($ddlType) || !isset($this->_ddlColumnTypes[$ddlType])) {
            throw new Zend_Db_Exception('Invalid column definition data');
        }

        // column size
        $cType = $this->_ddlColumnTypes[$ddlType];
        switch ($ddlType) {
            case Varien_Db_Ddl_Table::TYPE_SMALLINT:
            case Varien_Db_Ddl_Table::TYPE_INTEGER:
            case Varien_Db_Ddl_Table::TYPE_BIGINT:
                if (!empty($options['UNSIGNED'])) {
                    $cUnsigned = true;
                }
                break;
            case Varien_Db_Ddl_Table::TYPE_DECIMAL:
            case Varien_Db_Ddl_Table::TYPE_NUMERIC:
                $precision  = 10;
                $scale      = 0;
                $match      = array();
                if (!empty($options['LENGTH']) && preg_match('#^\(?(\d+),(\d+)\)?$#', $options['LENGTH'], $match)) {
                    $precision  = $match[1];
                    $scale      = $match[2];
                } else {
                    if (isset($options['SCALE']) && is_numeric($options['SCALE'])) {
                        $scale = $options['SCALE'];
                    }
                    if (isset($options['PRECISION']) && is_numeric($options['PRECISION'])) {
                        $precision = $options['PRECISION'];
                    }
                }
                $cType .= sprintf('(%d,%d)', $precision, $scale);
                break;
            case Varien_Db_Ddl_Table::TYPE_TEXT:
            case Varien_Db_Ddl_Table::TYPE_BLOB:
            case Varien_Db_Ddl_Table::TYPE_VARBINARY:
                if (empty($options['LENGTH'])) {
                    $length = Varien_Db_Ddl_Table::DEFAULT_TEXT_SIZE;
                } else {
                    $length = $this->_parseTextSize($options['LENGTH']);
                }
                if ($length <= 255) {
                    $cType = $ddlType == Varien_Db_Ddl_Table::TYPE_TEXT ? 'varchar' : 'varbinary';
                    $cType = sprintf('%s(%d)', $cType, $length);
                } else if ($length > 255 && $length <= 65536) {
                    $cType = $ddlType == Varien_Db_Ddl_Table::TYPE_TEXT ? 'text' : 'blob';
                } else if ($length > 65536 && $length <= 16777216) {
                    $cType = $ddlType == Varien_Db_Ddl_Table::TYPE_TEXT ? 'mediumtext' : 'mediumblob';
                } else {
                    $cType = $ddlType == Varien_Db_Ddl_Table::TYPE_TEXT ? 'longtext' : 'longblob';
                }
                break;
        }

        if (array_key_exists('DEFAULT', $options)) {
            $cDefault = $options['DEFAULT'];
        }
        if (array_key_exists('NULLABLE', $options)) {
            $cNullable = (bool)$options['NULLABLE'];
        }
        if (!empty($options['IDENTITY']) || !empty($options['AUTO_INCREMENT'])) {
            $cIdentity = true;
        }

        /*  For cases when tables created from createTableByDdl()
         *  where default value can be quoted already.
         *  We need to avoid "double-quoting" here
         */
        if ( $cDefault !== null && strlen($cDefault)) {
            $cDefault = str_replace("'", '', $cDefault);
        }

        // prepare default value string
        if ($ddlType == Varien_Db_Ddl_Table::TYPE_TIMESTAMP) {
            if ($cDefault === null) {
                $cDefault = new Zend_Db_Expr('NULL');
            } elseif ($cDefault == Varien_Db_Ddl_Table::TIMESTAMP_INIT) {
                $cDefault = new Zend_Db_Expr('CURRENT_TIMESTAMP');
            } else if ($cDefault == Varien_Db_Ddl_Table::TIMESTAMP_UPDATE) {
                $cDefault = new Zend_Db_Expr('0 ON UPDATE CURRENT_TIMESTAMP');
            } else if ($cDefault == Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE) {
                $cDefault = new Zend_Db_Expr('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
            } else if ($cNullable && !$cDefault) {
                $cDefault = new Zend_Db_Expr('NULL');
            } else {
                $cDefault = new Zend_Db_Expr('0');
            }
        } else if (is_null($cDefault) && $cNullable) {
            $cDefault = new Zend_Db_Expr('NULL');
        }

        if (empty($options['COMMENT'])) {
            $comment = '';
        } else {
            $comment = $options['COMMENT'];
        }

        //set column position
        $after = null;
        if (!empty($options['AFTER'])) {
            $after = $options['AFTER'];
        }

        return sprintf('%s%s%s%s%s COMMENT %s %s',
            $cType,
            $cUnsigned ? ' UNSIGNED' : '',
            $cNullable ? ' NULL' : ' NOT NULL',
            $cDefault !== false ? $this->quoteInto(' default ?', $cDefault) : '',
            $cIdentity ? ' auto_increment' : '',
            $this->quote($comment),
            $after ? 'AFTER ' . $this->quoteIdentifier($after) : ''
        );
    }

    /**
     * Drop table from database
     *
     * @param string $tableName
     * @param string $schemaName
     * @return boolean
     */
    public function dropTable($tableName, $schemaName = null)
    {
        $table = $this->quoteIdentifier($this->_getTableName($tableName, $schemaName));
        $query = 'DROP TABLE IF EXISTS ' . $table;
        $this->query($query);

        return true;
    }

    /**
     * Drop temporary table from database
     *
     * @param string $tableName
     * @param string $schemaName
     * @return boolean
     */
    public function dropTemporaryTable($tableName, $schemaName = null)
    {
        $table = $this->quoteIdentifier($this->_getTableName($tableName, $schemaName));
        $query = 'DROP TEMPORARY TABLE IF EXISTS ' . $table;
        $this->query($query);

        return $this;
    }

    /**
     * Truncate a table
     *
     * @param string $tableName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Pdo_Mysql
     * @throws Zend_Db_Exception
     */
    public function truncateTable($tableName, $schemaName = null)
    {
        if (!$this->isTableExists($tableName, $schemaName)) {
            throw new Zend_Db_Exception(sprintf('Table "%s" is not exists', $tableName));
        }

        $table = $this->quoteIdentifier($this->_getTableName($tableName, $schemaName));
        $query = 'TRUNCATE TABLE ' . $table;
        $this->query($query);

        return $this;
    }

    /**
     * Check is a table exists
     *
     * @param string $tableName
     * @param string $schemaName
     * @return boolean
     */
    public function isTableExists($tableName, $schemaName = null)
    {
        return $this->showTableStatus($tableName, $schemaName) !== false;
    }

    /**
     * Rename table
     *
     * @param string $oldTableName
     * @param string $newTableName
     * @param string $schemaName
     * @return boolean
     * @throws Zend_Db_Exception
     */
    public function renameTable($oldTableName, $newTableName, $schemaName = null)
    {
        if (!$this->isTableExists($oldTableName, $schemaName)) {
            throw new Zend_Db_Exception(sprintf('Table "%s" is not exists', $oldTableName));
        }
        if ($this->isTableExists($newTableName, $schemaName)) {
            throw new Zend_Db_Exception(sprintf('Table "%s" already exists', $newTableName));
        }

        $oldTable = $this->_getTableName($oldTableName, $schemaName);
        $newTable = $this->_getTableName($newTableName, $schemaName);

        $query = sprintf('ALTER TABLE %s RENAME TO %s', $oldTable, $newTable);
        $this->query($query);

        $this->resetDdlCache($oldTableName, $schemaName);

        return true;
    }

    /**
     * Rename several tables
     *
     * @param array $tablePairs array('oldName' => 'Name1', 'newName' => 'Name2')
     *
     * @return boolean
     * @throws Zend_Db_Exception
     */
    public function renameTablesBatch(array $tablePairs)
    {
        if (count($tablePairs) == 0) {
            throw new Zend_Db_Exception('Please provide tables for rename');
        }

        $renamesList = array();
        $tablesList  = array();
        foreach ($tablePairs as $pair) {
            $oldTableName  = $pair['oldName'];
            $newTableName  = $pair['newName'];
            $renamesList[] = sprintf('%s TO %s', $oldTableName, $newTableName);

            $tablesList[$oldTableName] = $oldTableName;
            $tablesList[$newTableName] = $newTableName;
        }

        $query = sprintf('RENAME TABLE %s', implode(',', $renamesList));
        $this->query($query);

        foreach ($tablesList as $table) {
            $this->resetDdlCache($table);
        }

        return true;
    }


    /**
     * Add new index to table name
     *
     * @param string $tableName
     * @param string $indexName
     * @param string|array $fields  the table column name or array of ones
     * @param string $indexType     the index type
     * @param string $schemaName
     * @return Zend_Db_Statement_Interface
     * @throws Zend_Db_Exception|Exception
     */
    public function addIndex($tableName, $indexName, $fields,
        $indexType = Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX, $schemaName = null)
    {
        $columns = $this->describeTable($tableName, $schemaName);
        $keyList = $this->getIndexList($tableName, $schemaName);

        $query = sprintf('ALTER TABLE %s', $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)));
        if (isset($keyList[strtoupper($indexName)])) {
            if ($keyList[strtoupper($indexName)]['INDEX_TYPE'] == Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY) {
                $query .= ' DROP PRIMARY KEY,';
            } else {
                $query .= sprintf(' DROP INDEX %s,', $this->quoteIdentifier($indexName));
            }
        }

        if (!is_array($fields)) {
            $fields = array($fields);
        }

        $fieldSql = array();
        foreach ($fields as $field) {
            if (!isset($columns[$field])) {
                $msg = sprintf('There is no field "%s" that you are trying to create an index on "%s"',
                    $field, $tableName);
                throw new Zend_Db_Exception($msg);
            }
            $fieldSql[] = $this->quoteIdentifier($field);
        }
        $fieldSql = implode(',', $fieldSql);

        switch (strtolower($indexType)) {
            case Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY:
                $condition = 'PRIMARY KEY';
                break;
            case Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE:
                $condition = 'UNIQUE ' . $this->quoteIdentifier($indexName);
                break;
            case Varien_Db_Adapter_Interface::INDEX_TYPE_FULLTEXT:
                $condition = 'FULLTEXT ' . $this->quoteIdentifier($indexName);
                break;
            default:
                $condition = 'INDEX ' . $this->quoteIdentifier($indexName);
                break;
        }

        $query .= sprintf(' ADD %s (%s)', $condition, $fieldSql);

        $cycle = true;
        while ($cycle === true) {
            try {
                $result = $this->raw_query($query);
                $cycle  = false;
            } catch (Exception $e) {
                if (in_array(strtolower($indexType), array('primary', 'unique'))) {
                    $match = array();
                    if (preg_match('#SQLSTATE\[23000\]: [^:]+: 1062[^\']+\'([\d-\.]+)\'#', $e->getMessage(), $match)) {
                        $ids = explode('-', $match[1]);
                        $this->_removeDuplicateEntry($tableName, $fields, $ids);
                        continue;
                    }
                }
                throw $e;
            }
        }

        $this->resetDdlCache($tableName, $schemaName);

        return $result;
    }

    /**
     * Drop the index from table
     *
     * @param string $tableName
     * @param string $keyName
     * @param string $schemaName
     * @return bool|Zend_Db_Statement_Interface
     */
    public function dropIndex($tableName, $keyName, $schemaName = null)
    {
        $indexList = $this->getIndexList($tableName, $schemaName);
        $keyName = strtoupper($keyName);
        if (!isset($indexList[$keyName])) {
            return true;
        }

        if ($keyName == 'PRIMARY') {
            $cond = 'DROP PRIMARY KEY';
        } else {
            $cond = 'DROP KEY ' . $this->quoteIdentifier($indexList[$keyName]['KEY_NAME']);
        }
        $sql = sprintf('ALTER TABLE %s %s',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
            $cond);

        $this->resetDdlCache($tableName, $schemaName);

        return $this->raw_query($sql);
    }

    /**
     * Add new Foreign Key to table
     * If Foreign Key with same name is exist - it will be deleted
     *
     * @param string $fkName
     * @param string $tableName
     * @param string $columnName
     * @param string $refTableName
     * @param string $refColumnName
     * @param string $onDelete
     * @param string $onUpdate
     * @param boolean $purge            trying remove invalid data
     * @param string $schemaName
     * @param string $refSchemaName
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function addForeignKey($fkName, $tableName, $columnName, $refTableName, $refColumnName,
        $onDelete = Varien_Db_Adapter_Interface::FK_ACTION_CASCADE,
        $onUpdate = Varien_Db_Adapter_Interface::FK_ACTION_CASCADE,
        $purge = false, $schemaName = null, $refSchemaName = null)
    {
        $this->dropForeignKey($tableName, $fkName, $schemaName);

        if ($purge) {
            $this->purgeOrphanRecords($tableName, $columnName, $refTableName, $refColumnName, $onDelete);
        }

        $query = sprintf('ALTER TABLE %s ADD CONSTRAINT %s FOREIGN KEY (%s) REFERENCES %s (%s)',
            $this->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
            $this->quoteIdentifier($fkName),
            $this->quoteIdentifier($columnName),
            $this->quoteIdentifier($this->_getTableName($refTableName, $refSchemaName)),
            $this->quoteIdentifier($refColumnName)
        );

        if ($onDelete !== null) {
            $query .= ' ON DELETE ' . strtoupper($onDelete);
        }
        if ($onUpdate  !== null) {
            $query .= ' ON UPDATE ' . strtoupper($onUpdate);
        }

        $result = $this->raw_query($query);
        $this->resetDdlCache($tableName);
        return $result;
    }

    /**
     * Format Date to internal database date format
     *
     * @param int|string|Zend_Date $date
     * @param boolean $includeTime
     * @return Zend_Db_Expr
     */
    public function formatDate($date, $includeTime = true)
    {
        $date = Varien_Date::formatDate($date, $includeTime);

        if ($date === null) {
            return new Zend_Db_Expr('NULL');
        }

        return new Zend_Db_Expr($this->quote($date));
    }

    /**
     * Run additional environment before setup
     *
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function startSetup()
    {
        $this->raw_query("SET SQL_MODE=''");
        $this->raw_query("SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0");
        $this->raw_query("SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO'");

        return $this;
    }

    /**
     * Run additional environment after setup
     *
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function endSetup()
    {
        $this->raw_query("SET SQL_MODE=IFNULL(@OLD_SQL_MODE,'')");
        $this->raw_query("SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS=0, 0, 1)");

        return $this;
    }

    /**
     * Build SQL statement for condition
     *
     * If $condition integer or string - exact value will be filtered ('eq' condition)
     *
     * If $condition is array is - one of the following structures is expected:
     * - array("from" => $fromValue, "to" => $toValue)
     * - array("eq" => $equalValue)
     * - array("neq" => $notEqualValue)
     * - array("like" => $likeValue)
     * - array("in" => array($inValues))
     * - array("nin" => array($notInValues))
     * - array("notnull" => $valueIsNotNull)
     * - array("null" => $valueIsNull)
     * - array("gt" => $greaterValue)
     * - array("lt" => $lessValue)
     * - array("gteq" => $greaterOrEqualValue)
     * - array("lteq" => $lessOrEqualValue)
     * - array("finset" => $valueInSet)
     * - array("regexp" => $regularExpression)
     * - array("seq" => $stringValue)
     * - array("sneq" => $stringValue)
     *
     * If non matched - sequential array is expected and OR conditions
     * will be built using above mentioned structure
     *
     * @param string|array $fieldName
     * @param integer|string|array $condition
     * @return string
     */
    public function prepareSqlCondition($fieldName, $condition)
    {
        $conditionKeyMap = array(
            'eq'            => "{{fieldName}} = ?",
            'neq'           => "{{fieldName}} != ?",
            'like'          => "{{fieldName}} LIKE ?",
            'nlike'         => "{{fieldName}} NOT LIKE ?",
            'in'            => "{{fieldName}} IN(?)",
            'nin'           => "{{fieldName}} NOT IN(?)",
            'is'            => "{{fieldName}} IS ?",
            'notnull'       => "{{fieldName}} IS NOT NULL",
            'null'          => "{{fieldName}} IS NULL",
            'gt'            => "{{fieldName}} > ?",
            'lt'            => "{{fieldName}} < ?",
            'gteq'          => "{{fieldName}} >= ?",
            'lteq'          => "{{fieldName}} <= ?",
            'finset'        => "FIND_IN_SET(?, {{fieldName}})",
            'regexp'        => "{{fieldName}} REGEXP ?",
            'from'          => "{{fieldName}} >= ?",
            'to'            => "{{fieldName}} <= ?",
            'seq'           => null,
            'sneq'          => null
        );

        $query = '';
        if (is_array($condition)) {
            $key = key(array_intersect_key($condition, $conditionKeyMap));

            if (isset($condition['from']) || isset($condition['to'])) {
                if (isset($condition['from'])) {
                    $from  = $this->_prepareSqlDateCondition($condition, 'from');
                    $query = $this->_prepareQuotedSqlCondition($conditionKeyMap['from'], $from, $fieldName);
                }

                if (isset($condition['to'])) {
                    $query .= empty($query) ? '' : ' AND ';
                    $to     = $this->_prepareSqlDateCondition($condition, 'to');
                    $query = $this->_prepareQuotedSqlCondition($query . $conditionKeyMap['to'], $to, $fieldName);
                }
            } elseif (array_key_exists($key, $conditionKeyMap)) {
                $value = $condition[$key];
                if (($key == 'seq') || ($key == 'sneq')) {
                    $key = $this->_transformStringSqlCondition($key, $value);
                }
                $query = $this->_prepareQuotedSqlCondition($conditionKeyMap[$key], $value, $fieldName);
            } else {
                $queries = array();
                foreach ($condition as $orCondition) {
                    $queries[] = sprintf('(%s)', $this->prepareSqlCondition($fieldName, $orCondition));
                }

                $query = sprintf('(%s)', implode(' OR ', $queries));
            }
        } else {
            $query = $this->_prepareQuotedSqlCondition($conditionKeyMap['eq'], (string)$condition, $fieldName);
        }

        return $query;
    }

    /**
     * Prepare Sql condition
     *
     * @param  $text Condition value
     * @param  mixed $value
     * @param  string $fieldName
     * @return string
     */
    protected function _prepareQuotedSqlCondition($text, $value, $fieldName)
    {
        $sql = $this->quoteInto($text, $value);
        $sql = str_replace('{{fieldName}}', $fieldName, $sql);
        return $sql;
    }

    /**
     * Transforms sql condition key 'seq' / 'sneq' that is used for comparing string values to its analog:
     * - 'null' / 'notnull' for empty strings
     * - 'eq' / 'neq' for non-empty strings
     *
     * @param string $conditionKey
     * @param mixed $value
     * @return string
     */
    protected function _transformStringSqlCondition($conditionKey, $value)
    {
        $value = (string) $value;
        if ($value == '') {
            return ($conditionKey == 'seq') ? 'null' : 'notnull';
        } else {
            return ($conditionKey == 'seq') ? 'eq' : 'neq';
        }
    }

    /**
     * Prepare value for save in column
     * Return converted to column data type value
     *
     * @param array $column     the column describe array
     * @param mixed $value
     * @return mixed
     */
    public function prepareColumnValue(array $column, $value)
    {
        if ($value instanceof Zend_Db_Expr) {
            return $value;
        }
        if ($value instanceof Varien_Db_Statement_Parameter) {
            return $value;
        }

        // return original value if invalid column describe data
        if (!isset($column['DATA_TYPE'])) {
            return $value;
        }

        // return null
        if (is_null($value) && $column['NULLABLE']) {
            return null;
        }

        switch ($column['DATA_TYPE']) {
            case 'smallint':
            case 'int':
                $value = (int)$value;
                break;
            case 'bigint':
                if (!is_integer($value)) {
                    $value = sprintf('%.0f', (float)$value);
                }
                break;

            case 'decimal':
                $precision  = 10;
                $scale      = 0;
                if (isset($column['SCALE'])) {
                    $scale = $column['SCALE'];
                }
                if (isset($column['PRECISION'])) {
                    $precision = $column['PRECISION'];
                }
                $format = sprintf('%%%d.%dF', $precision - $scale, $scale);
                $value  = (float)sprintf($format, $value);
                break;

            case 'float':
                $value  = (float)sprintf('%F', $value);
                break;

            case 'date':
                $value  = $this->formatDate($value, false);
                break;
            case 'datetime':
            case 'timestamp':
                $value  = $this->formatDate($value);
                break;

            case 'varchar':
            case 'mediumtext':
            case 'text':
            case 'longtext':
                $value  = (string)$value;
                if ($column['NULLABLE'] && $value == '') {
                    $value = null;
                }
                break;

            case 'varbinary':
            case 'mediumblob':
            case 'blob':
            case 'longblob':
                // No special processing for MySQL is needed
                break;
        }

        return $value;
    }

    /**
     * Generate fragment of SQL, that check condition and return true or false value
     *
     * @param Zend_Db_Expr|Zend_Db_Select|string $expression
     * @param string $true  true value
     * @param string $false false value
     */
    public function getCheckSql($expression, $true, $false)
    {
        if ($expression instanceof Zend_Db_Expr || $expression instanceof Zend_Db_Select) {
            $expression = sprintf("IF((%s), %s, %s)", $expression, $true, $false);
        } else {
            $expression = sprintf("IF(%s, %s, %s)", $expression, $true, $false);
        }

        return new Zend_Db_Expr($expression);
    }

    /**
     * Returns valid IFNULL expression
     *
     * @param Zend_Db_Expr|Zend_Db_Select|string $expression
     * @param string $value OPTIONAL. Applies when $expression is NULL
     * @return Zend_Db_Expr
     */
    public function getIfNullSql($expression, $value = 0)
    {
        if ($expression instanceof Zend_Db_Expr || $expression instanceof Zend_Db_Select) {
            $expression = sprintf("IFNULL((%s), %s)", $expression, $value);
        } else {
            $expression = sprintf("IFNULL(%s, %s)", $expression, $value);
        }

        return new Zend_Db_Expr($expression);
    }

    /**
     * Generate fragment of SQL, that check value against multiple condition cases
     * and return different result depends on them
     *
     * @param string $valueName Name of value to check
     * @param array $casesResults Cases and results
     * @param string $defaultValue value to use if value doesn't conform to any cases
     *
     * @return Zend_Db_Expr
     */
    public function getCaseSql($valueName, $casesResults, $defaultValue = null)
    {
        $expression = 'CASE ' . $valueName;
        foreach ($casesResults as $case => $result) {
            $expression .= ' WHEN ' . $case . ' THEN ' . $result;
        }
        if ($defaultValue !== null) {
            $expression .= ' ELSE ' . $defaultValue;
        }
        $expression .= ' END';

        return new Zend_Db_Expr($expression);
    }

    /**
     * Generate fragment of SQL, that combine together (concatenate) the results from data array
     * All arguments in data must be quoted
     *
     * @param array $data
     * @param string $separator concatenate with separator
     * @return Zend_Db_Expr
     */
    public function getConcatSql(array $data, $separator = null)
    {
        $format = empty($separator) ? 'CONCAT(%s)' : "CONCAT_WS('{$separator}', %s)";
        return new Zend_Db_Expr(sprintf($format, implode(', ', $data)));
    }

    /**
     * Generate fragment of SQL that returns length of character string
     * The string argument must be quoted
     *
     * @param string $string
     * @return Zend_Db_Expr
     */
    public function getLengthSql($string)
    {
        return new Zend_Db_Expr(sprintf('LENGTH(%s)', $string));
    }

    /**
     * Generate fragment of SQL, that compare with two or more arguments, and returns the smallest
     * (minimum-valued) argument
     * All arguments in data must be quoted
     *
     * @param array $data
     * @return Zend_Db_Expr
     */
    public function getLeastSql(array $data)
    {
        return new Zend_Db_Expr(sprintf('LEAST(%s)', implode(', ', $data)));
    }

    /**
     * Generate fragment of SQL, that compare with two or more arguments, and returns the largest
     * (maximum-valued) argument
     * All arguments in data must be quoted
     *
     * @param array $data
     * @return Zend_Db_Expr
     */
    public function getGreatestSql(array $data)
    {
        return new Zend_Db_Expr(sprintf('GREATEST(%s)', implode(', ', $data)));
    }

    /**
     * Get Interval Unit SQL fragment
     *
     * @param int $interval
     * @param string $unit
     * @return string
     * @throws Zend_Db_Exception
     */
    protected function _getIntervalUnitSql($interval, $unit)
    {
        if (!isset($this->_intervalUnits[$unit])) {
            throw new Zend_Db_Exception(sprintf('Undefined interval unit "%s" specified', $unit));
        }

        return sprintf('INTERVAL %d %s', $interval, $this->_intervalUnits[$unit]);
    }

    /**
     * Add time values (intervals) to a date value
     *
     * @see INTERVAL_* constants for $unit
     *
     * @param Zend_Db_Expr|string $date   quoted field name or SQL statement
     * @param int $interval
     * @param string $unit
     * @return Zend_Db_Expr
     */
    public function getDateAddSql($date, $interval, $unit)
    {
        $expr = sprintf('DATE_ADD(%s, %s)', $date, $this->_getIntervalUnitSql($interval, $unit));
        return new Zend_Db_Expr($expr);
    }

    /**
     * Subtract time values (intervals) to a date value
     *
     * @see INTERVAL_* constants for $expr
     *
     * @param Zend_Db_Expr|string $date   quoted field name or SQL statement
     * @param int|string $interval
     * @param string $unit
     * @return Zend_Db_Expr
     */
    public function getDateSubSql($date, $interval, $unit)
    {
        $expr = sprintf('DATE_SUB(%s, %s)', $date, $this->_getIntervalUnitSql($interval, $unit));
        return new Zend_Db_Expr($expr);
    }

    /**
     * Format date as specified
     *
     * Supported format Specifier
     *
     * %H   Hour (00..23)
     * %i   Minutes, numeric (00..59)
     * %s   Seconds (00..59)
     * %d   Day of the month, numeric (00..31)
     * %m   Month, numeric (00..12)
     * %Y   Year, numeric, four digits
     *
     * @param string $date  quoted date value or non quoted SQL statement(field)
     * @param string $format
     * @return Zend_Db_Expr
     */
    public function getDateFormatSql($date, $format)
    {
        $expr = sprintf("DATE_FORMAT(%s, '%s')", $date, $format);
        return new Zend_Db_Expr($expr);
    }

    /**
     * Extract the date part of a date or datetime expression
     *
     * @param Zend_Db_Expr|string $date   quoted field name or SQL statement
     * @return Zend_Db_Expr
     */
    public function getDatePartSql($date)
    {
        return new Zend_Db_Expr(sprintf('DATE(%s)', $date));
    }

    /**
     * Prepare substring sql function
     *
     * @param Zend_Db_Expr|string $stringExpression quoted field name or SQL statement
     * @param int|string|Zend_Db_Expr $pos
     * @param int|string|Zend_Db_Expr|null $len
     * @return Zend_Db_Expr
     */
    public function getSubstringSql($stringExpression, $pos, $len = null)
    {
        if (is_null($len)) {
            return new Zend_Db_Expr(sprintf('SUBSTRING(%s, %s)', $stringExpression, $pos));
        }
        return new Zend_Db_Expr(sprintf('SUBSTRING(%s, %s, %s)', $stringExpression, $pos, $len));
    }

    /**
     * Prepare standard deviation sql function
     *
     * @param Zend_Db_Expr|string $expressionField   quoted field name or SQL statement
     * @return Zend_Db_Expr
     */
    public function getStandardDeviationSql($expressionField)
    {
        return new Zend_Db_Expr(sprintf('STDDEV_SAMP(%s)', $expressionField));
    }

    /**
     * Extract part of a date
     *
     * @see INTERVAL_* constants for $unit
     *
     * @param Zend_Db_Expr|string $date   quoted field name or SQL statement
     * @param string $unit
     * @return Zend_Db_Expr
     * @throws Zend_Db_Exception
     */
    public function getDateExtractSql($date, $unit)
    {
        if (!isset($this->_intervalUnits[$unit])) {
            throw new Zend_Db_Exception(sprintf('Undefined interval unit "%s" specified', $unit));
        }

        $expr = sprintf('EXTRACT(%s FROM %s)', $this->_intervalUnits[$unit], $date);
        return new Zend_Db_Expr($expr);
    }

    /**
     * Minus superfluous characters from hash.
     *
     * @param  $hash
     * @param  $prefix
     * @param  $maxCharacters
     * @return string
     */
     protected function _minusSuperfluous($hash, $prefix, $maxCharacters)
     {
         $diff        = strlen($hash) + strlen($prefix) -  $maxCharacters;
         $superfluous = $diff / 2;
         $odd         = $diff % 2;
         $hash        = substr($hash, $superfluous, - ($superfluous + $odd));
         return $hash;
     }

    /**
     * Retrieve valid table name
     * Check table name length and allowed symbols
     *
     * @param string $tableName
     * @return string
     */
    public function getTableName($tableName)
    {
        $prefix = 't_';
        if (strlen($tableName) > self::LENGTH_TABLE_NAME) {
            $shortName = Varien_Db_Helper::shortName($tableName);
            if (strlen($shortName) > self::LENGTH_TABLE_NAME) {
                $hash = md5($tableName);
                if (strlen($prefix.$hash) > self::LENGTH_TABLE_NAME) {
                    $tableName = $this->_minusSuperfluous($hash, $prefix, self::LENGTH_TABLE_NAME);
                } else {
                    $tableName = $prefix . $hash;
                }
            } else {
                $tableName = $shortName;
            }
        }

        return $tableName;
    }

    /**
     * Retrieve valid index name
     * Check index name length and allowed symbols
     *
     * @param string $tableName
     * @param string|array $fields  the columns list
     * @param string $indexType
     * @return string
     */
    public function getIndexName($tableName, $fields, $indexType = '')
    {
        if (is_array($fields)) {
            $fields = implode('_', $fields);
        }

        switch (strtolower($indexType)) {
            case Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE:
                $prefix = 'unq_';
                $shortPrefix = 'u_';
                break;
            case Varien_Db_Adapter_Interface::INDEX_TYPE_FULLTEXT:
                $prefix = 'fti_';
                $shortPrefix = 'f_';
                break;
            case Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX:
            default:
                $prefix = 'idx_';
                $shortPrefix = 'i_';
        }

        $hash = $tableName . '_' . $fields;

        if (strlen($hash) + strlen($prefix) > self::LENGTH_INDEX_NAME) {
            $short = Varien_Db_Helper::shortName($prefix . $hash);
            if (strlen($short) > self::LENGTH_INDEX_NAME) {
                $hash = md5($hash);
                if (strlen($hash) + strlen($shortPrefix) > self::LENGTH_INDEX_NAME) {
                    $hash = $this->_minusSuperfluous($hash, $shortPrefix, self::LENGTH_INDEX_NAME);
                }
            } else {
                $hash = $short;
            }
        } else {
            $hash = $prefix . $hash;
        }

        return strtoupper($hash);
    }

    /**
     * Retrieve valid foreign key name
     * Check foreign key name length and allowed symbols
     *
     * @param string $priTableName
     * @param string $priColumnName
     * @param string $refTableName
     * @param string $refColumnName
     * @return string
     */
    public function getForeignKeyName($priTableName, $priColumnName, $refTableName, $refColumnName)
    {
        $prefix = 'fk_';
        $hash = sprintf('%s_%s_%s_%s', $priTableName, $priColumnName, $refTableName, $refColumnName);
        if (strlen($prefix.$hash) > self::LENGTH_FOREIGN_NAME) {
            $short = Varien_Db_Helper::shortName($prefix.$hash);
            if (strlen($short) > self::LENGTH_FOREIGN_NAME) {
                $hash = md5($hash);
                if (strlen($prefix.$hash) > self::LENGTH_FOREIGN_NAME) {
                    $hash = $this->_minusSuperfluous($hash, $prefix, self::LENGTH_FOREIGN_NAME);
                } else {
                    $hash = $prefix . $hash;
                }
            } else {
                $hash = $short;
            }
        } else {
            $hash = $prefix . $hash;
        }

        return strtoupper($hash);
    }

    /**
     * Stop updating indexes
     *
     * @param string $tableName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function disableTableKeys($tableName, $schemaName = null)
    {
        $tableName = $this->_getTableName($tableName, $schemaName);
        $query     = sprintf('ALTER TABLE %s DISABLE KEYS', $this->quoteIdentifier($tableName));
        $this->query($query);

        return $this;
    }

    /**
     * Re-create missing indexes
     *
     * @param string $tableName
     * @param string $schemaName
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function enableTableKeys($tableName, $schemaName = null)
    {
        $tableName = $this->_getTableName($tableName, $schemaName);
        $query     = sprintf('ALTER TABLE %s ENABLE KEYS', $this->quoteIdentifier($tableName));
        $this->query($query);

        return $this;
    }

    /**
     * Get insert from Select object query
     *
     * @param Varien_Db_Select $select
     * @param string $table     insert into table
     * @param array $fields
     * @param bool|int $mode
     * @return string
     */
    public function insertFromSelect(Varien_Db_Select $select, $table, array $fields = array(), $mode = false)
    {
        $query = 'INSERT';
        if ($mode == self::INSERT_IGNORE) {
            $query .= ' IGNORE';
        }
        $query = sprintf('%s INTO %s', $query, $this->quoteIdentifier($table));
        if ($fields) {
            $columns = array_map(array($this, 'quoteIdentifier'), $fields);
            $query = sprintf('%s (%s)', $query, join(', ', $columns));
        }

        $query = sprintf('%s %s', $query, $select->assemble());

        if ($mode == self::INSERT_ON_DUPLICATE) {
            if (!$fields) {
                $describe = $this->describeTable($table);
                foreach ($describe as $column) {
                    if ($column['PRIMARY'] === false) {
                        $fields[] = $column['COLUMN_NAME'];
                    }
                }
            }

            $update = array();
            foreach ($fields as $k => $v) {
                $field = $value = null;
                if (!is_numeric($k)) {
                    $field = $this->quoteIdentifier($k);
                    if ($v instanceof Zend_Db_Expr) {
                        $value = $v->__toString();
                    } elseif (is_string($v)) {
                        $value = sprintf('VALUES(%s)', $this->quoteIdentifier($v));
                    } elseif (is_numeric($v)) {
                        $value = $this->quoteInto('?', $v);
                    }
                } elseif (is_string($v)) {
                    $value = sprintf('VALUES(%s)', $this->quoteIdentifier($v));
                    $field = $this->quoteIdentifier($v);
                }

                if ($field && $value) {
                    $update[] = sprintf('%s = %s', $field, $value);
                }
            }
            if ($update) {
                $query = sprintf('%s ON DUPLICATE KEY UPDATE %s', $query, join(', ', $update));
            }
        }

        return $query;
    }

    /**
     * Get insert queries in array for insert by range with step parameter
     *
     * @param string $rangeField
     * @param Varien_Db_Select $select
     * @param int $stepCount
     * @return array
     * @throws Varien_Db_Exception
     */
    public function selectsByRange($rangeField, Varien_Db_Select $select, $stepCount = 100)
    {
        $queries = array();
        $fromSelect = $select->getPart(Varien_Db_Select::FROM);
        if (empty($fromSelect)) {
            throw new Varien_Db_Exception('Select must have correct FROM part');
        }

        $tableName = array();
        $correlationName = '';
        foreach ($fromSelect as $correlationName => $formPart) {
            if ($formPart['joinType'] == Varien_Db_Select::FROM) {
                $tableName = $formPart['tableName'];
                break;
            }
        }

        $selectRange = $this->select()
            ->from(
                $tableName,
                array(
                    new Zend_Db_Expr('MIN(' . $this->quoteIdentifier($rangeField) . ') AS min'),
                    new Zend_Db_Expr('MAX(' . $this->quoteIdentifier($rangeField) . ') AS max'),
                )
            );

        $rangeResult = $this->fetchRow($selectRange);
        $min = $rangeResult['min'];
        $max = $rangeResult['max'];

        while ($min <= $max) {
            $partialSelect = clone $select;
            $partialSelect->where(
                $this->quoteIdentifier($correlationName) . '.'
                    . $this->quoteIdentifier($rangeField) . ' >= ?', $min
            )
            ->where(
                $this->quoteIdentifier($correlationName) . '.'
                    . $this->quoteIdentifier($rangeField) . ' < ?', $min+$stepCount
            );
            $queries[] = $partialSelect;
            $min += $stepCount;
        }
        return $queries;
    }

    /**
     * Convert date format to unix time
     *
     * @param string|Zend_Db_Expr $date
     * @throws Varien_Db_Exception
     * @return Zend_Db_Expr
     */
    public function getUnixTimestamp($date)
    {
        $expr = sprintf('UNIX_TIMESTAMP(%s)', $date);
        return new Zend_Db_Expr($expr);
    }

    /**
     * Convert unix time to date format
     *
     * @param int|Zend_Db_Expr $timestamp
     * @return mixed
     */
    public function fromUnixtime($timestamp)
    {
        $expr = sprintf('FROM_UNIXTIME(%s)', $timestamp);
        return new Zend_Db_Expr($expr);
    }

    /**
     * Get update table query using select object for join and update
     *
     * @param Varien_Db_Select $select
     * @param string|array $table
     * @throws Varien_Db_Exception
     * @return string
     */
    public function updateFromSelect(Varien_Db_Select $select, $table)
    {
        if (!is_array($table)) {
            $table = array($table => $table);
        }

        // get table name and alias
        $keys       = array_keys($table);
        $tableAlias = $keys[0];
        $tableName  = $table[$keys[0]];

        $query = sprintf('UPDATE %s', $this->quoteTableAs($tableName, $tableAlias));

        // render JOIN conditions (FROM Part)
        $joinConds  = array();
        foreach ($select->getPart(Zend_Db_Select::FROM) as $correlationName => $joinProp) {
            if ($joinProp['joinType'] == Zend_Db_Select::FROM) {
                $joinType = strtoupper(Zend_Db_Select::INNER_JOIN);
            } else {
                $joinType = strtoupper($joinProp['joinType']);
            }
            $joinTable = '';
            if ($joinProp['schema'] !== null) {
                $joinTable = sprintf('%s.', $this->quoteIdentifier($joinProp['schema']));
            }
            $joinTable .= $this->quoteTableAs($joinProp['tableName'], $correlationName);

            $join = sprintf(' %s %s', $joinType, $joinTable);

            if (!empty($joinProp['joinCondition'])) {
                $join = sprintf('%s ON %s', $join, $joinProp['joinCondition']);
            }

            $joinConds[] = $join;
        }

        if ($joinConds) {
            $query = sprintf("%s\n%s", $query, implode("\n", $joinConds));
        }

        // render UPDATE SET
        $columns = array();
        foreach ($select->getPart(Zend_Db_Select::COLUMNS) as $columnEntry) {
            list($correlationName, $column, $alias) = $columnEntry;
            if (empty($alias)) {
                $alias = $column;
            }
            if (!$column instanceof Zend_Db_Expr && !empty($correlationName)) {
                $column = $this->quoteIdentifier(array($correlationName, $column));
            }
            $columns[] = sprintf('%s = %s', $this->quoteIdentifier(array($tableAlias, $alias)), $column);
        }

        if (!$columns) {
            throw new Varien_Db_Exception('The columns for UPDATE statement are not defined');
        }

        $query = sprintf("%s\nSET %s", $query, implode(', ', $columns));

        // render WHERE
        $wherePart = $select->getPart(Zend_Db_Select::WHERE);
        if ($wherePart) {
            $query = sprintf("%s\nWHERE %s", $query, implode(' ', $wherePart));
        }

        return $query;
    }

    /**
     * Get delete from select object query
     *
     * @param Varien_Db_Select $select
     * @param string $table the table name or alias used in select
     * @return string|int
     */
    public function deleteFromSelect(Varien_Db_Select $select, $table)
    {
        $select = clone $select;
        $select->reset(Zend_Db_Select::DISTINCT);
        $select->reset(Zend_Db_Select::COLUMNS);

        $query = sprintf('DELETE %s %s', $this->quoteIdentifier($table), $select->assemble());

        return $query;
    }

    /**
     * Calculate checksum for table or for group of tables
     *
     * @param array|string $tableNames array of tables names | table name
     * @param string $schemaName schema name
     * @return arrray
     */
    public function getTablesChecksum($tableNames, $schemaName = null)
    {
        $result     = array();
        $tableNames = is_array($tableNames) ? $tableNames : array($tableNames);

        foreach ($tableNames as $tableName) {
            $query = 'CHECKSUM TABLE ' . $this->_getTableName($tableName, $schemaName);
            $checkSumArray      = $this->fetchRow($query);
            $result[$tableName] = $checkSumArray['Checksum'];
        }

        return $result;
    }

    /**
     * Check if the database support STRAIGHT JOIN
     *
     * @return boolean
     */
    public function supportStraightJoin()
    {
        return true;
    }

    /**
     * Adds order by random to select object
     * Possible using integer field for optimization
     *
     * @param Varien_Db_Select $select
     * @param string $field
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function orderRand(Varien_Db_Select $select, $field = null)
    {
        if ($field !== null) {
            $expression = new Zend_Db_Expr(sprintf('RAND() * %s', $this->quoteIdentifier($field)));
            $select->columns(array('mage_rand' => $expression));
            $spec = new Zend_Db_Expr('mage_rand');
        } else {
            $spec = new Zend_Db_Expr('RAND()');
        }
        $select->order($spec);

        return $this;
    }

    /**
     * Render SQL FOR UPDATE clause
     *
     * @param string $sql
     * @return string
     */
    public function forUpdate($sql)
    {
        return sprintf('%s FOR UPDATE', $sql);
    }

    /**
     * Prepare insert data
     *
     * @param mixed $row
     * @param array $bind
     * @return string
     */
    protected function _prepareInsertData($row, &$bind)
    {
        if (is_array($row)) {
            $line = array();
            foreach ($row as $value) {
                if ($value instanceof Zend_Db_Expr) {
                    $line[] = $value->__toString();
                } else {
                    $line[] = '?';
                    $bind[] = $value;
                }
            }
            $line = implode(', ', $line);
        } elseif ($row instanceof Zend_Db_Expr) {
            $line = $row->__toString();
        } else {
            $line = '?';
            $bind[] = $row;
        }

        return sprintf('(%s)', $line);
    }

    /**
     * Return insert sql query
     *
     * @param string $tableName
     * @param array $columns
     * @param array $values
     * @return string
     */
    protected function _getInsertSqlQuery($tableName, array $columns, array $values)
    {
        $tableName = $this->quoteIdentifier($tableName, true);
        $columns   = array_map(array($this, 'quoteIdentifier'), $columns);
        $columns   = implode(',', $columns);
        $values    = implode(', ', $values);

        $insertSql = sprintf('INSERT INTO %s (%s) VALUES %s', $tableName, $columns, $values);

        return $insertSql;
    }

    /**
     * Return ddl type
     *
     * @param array $options
     * @return string
     */
    protected function _getDdlType($options)
    {
        $ddlType = null;
        if (isset($options['TYPE'])) {
            $ddlType = $options['TYPE'];
        } elseif (isset($options['COLUMN_TYPE'])) {
            $ddlType = $options['COLUMN_TYPE'];
        }

        return $ddlType;
    }

    /**
     * Return DDL action
     *
     * @param string $action
     * @return string
     */
    protected function _getDdlAction($action)
    {
        switch ($action) {
            case Varien_Db_Adapter_Interface::FK_ACTION_CASCADE:
                return Varien_Db_Ddl_Table::ACTION_CASCADE;
            case Varien_Db_Adapter_Interface::FK_ACTION_SET_NULL:
                return Varien_Db_Ddl_Table::ACTION_SET_NULL;
            case Varien_Db_Adapter_Interface::FK_ACTION_RESTRICT:
                return Varien_Db_Ddl_Table::ACTION_RESTRICT;
            default:
                return Varien_Db_Ddl_Table::ACTION_NO_ACTION;
        }
    }

    /**
     * Prepare sql date condition
     *
     * @param array $condition
     * @param string $key
     * @return string
     */
    protected function _prepareSqlDateCondition($condition, $key)
    {
        if (empty($condition['date'])) {
            if (empty($condition['datetime'])) {
                $result = $condition[$key];
            } else {
                $result = $this->formatDate($condition[$key]);
            }
        } else {
            $result = $this->formatDate($condition[$key]);
        }

        return $result;
    }

    /**
     * Try to find installed primary key name, if not - formate new one.
     *
     * @param string $tableName Table name
     * @param string $schemaName OPTIONAL
     * @return string Primary Key name
     */
    public function getPrimaryKeyName($tableName, $schemaName = null)
    {
        $indexes = $this->getIndexList($tableName, $schemaName);
        if (isset($indexes['PRIMARY'])) {
            return $indexes['PRIMARY']['KEY_NAME'];
        } else {
            return 'PK_' . strtoupper($tableName);
        }
    }

    /**
     * Parse text size
     * Returns max allowed size if value great it
     *
     * @param string|int $size
     * @return int
     */
    protected function _parseTextSize($size)
    {
        $size = trim($size);
        $last = strtolower(substr($size, -1));

        switch ($last) {
            case 'k':
                $size = intval($size) * 1024;
                break;
            case 'm':
                $size = intval($size) * 1024 * 1024;
                break;
            case 'g':
                $size = intval($size) * 1024 * 1024 * 1024;
                break;
        }

        if (empty($size)) {
            return Varien_Db_Ddl_Table::DEFAULT_TEXT_SIZE;
        }
        if ($size >= Varien_Db_Ddl_Table::MAX_TEXT_SIZE) {
            return Varien_Db_Ddl_Table::MAX_TEXT_SIZE;
        }

        return intval($size);
    }

    /**
     * Converts fetched blob into raw binary PHP data.
     * The MySQL drivers do it nice, no processing required.
     *
     * @mixed $value
     * @return mixed
     */
    public function decodeVarbinary($value)
    {
        return $value;
    }





    /**
     * Returns date that fits into TYPE_DATETIME range and is suggested to act as default 'zero' value
     * for a column for current RDBMS. Deprecated and left for compatibility only.
     * In Magento at MySQL there was zero date used for datetime columns. However, zero date it is not supported across
     * different RDBMS. Thus now it is recommended to use same default value equal for all RDBMS - either NULL
     * or specific date supported by all RDBMS.
     *
     * @deprecated after 1.5.1.0
     * @return string
     */
    public function getSuggestedZeroDate()
    {
        return '0000-00-00 00:00:00';
    }

    /**
     * Retrieve Foreign Key name
     *
     * @deprecated after 1.6.0.0
     *
     * @param  string $fkName
     * @return string
     */
    protected function _getForeignKeyName($fkName)
    {
        if (substr($fkName, 0, 3) != 'FK_') {
            $fkName = 'FK_' . $fkName;
        }

        return $fkName;
    }

    /**
     * Drop trigger
     *
     * @param string $triggerName
     * @return Varien_Db_Adapter_Interface
     */
    public function dropTrigger($triggerName)
    {
        $query = sprintf(
            'DROP TRIGGER IF EXISTS %s',
            $this->_getTableName($triggerName)
        );
        $this->query($query);
        return $this;
    }

    /**
     * Create new table from provided select statement
     *
     * @param string $tableName
     * @param Zend_Db_Select $select
     * @param bool $temporary
     * @return mixed
     */
    public function createTableFromSelect($tableName, Zend_Db_Select $select, $temporary = false)
    {
        $query = sprintf(
            'CREATE' . ($temporary ? ' TEMPORARY' : '') . ' TABLE `%s` AS (%s)',
            $this->_getTableName($tableName),
            (string)$select
        );
        $this->query($query);
    }

    /**
     * Check if all transactions have been committed
     */
    public function __destruct()
    {
        if ($this->_transactionLevel > 0) {
            trigger_error('Some transactions have not been committed or rolled back', E_USER_ERROR);
        }
    }
}
