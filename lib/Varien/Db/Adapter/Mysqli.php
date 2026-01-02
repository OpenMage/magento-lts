<?php

use Carbon\Carbon;

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Db
 */

class Varien_Db_Adapter_Mysqli extends Zend_Db_Adapter_Mysqli
{
    public const ISO_DATE_FORMAT       = 'yyyy-MM-dd';

    public const ISO_DATETIME_FORMAT   = 'yyyy-MM-dd HH-mm-ss';

    /**
     * Creates a real connection to the database with multi-query capability.
     *
     * @return void
     * @throws Zend_Db_Adapter_Exception
     * @throws Zend_Db_Adapter_Mysqli_Exception
     *
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    protected function _connect()
    {
        if ($this->_connection) {
            return;
        }

        if (!extension_loaded('mysqli')) {
            throw new Zend_Db_Adapter_Exception('mysqli extension is not installed');
        }

        // Suppress connection warnings here.
        // Throw an exception instead.
        @$conn = new mysqli();
        if (mysqli_connect_errno()) {
            throw new Zend_Db_Adapter_Mysqli_Exception(mysqli_connect_errno());
        }

        $conn->init();
        $conn->options(MYSQLI_OPT_LOCAL_INFILE, 1);
        #$conn->options(MYSQLI_CLIENT_MULTI_QUERIES, 1);

        $port = empty($this->_config['port']) ? null : $this->_config['port'];
        $socket = empty($this->_config['unix_socket']) ? null : $this->_config['unix_socket'];
        // socket specified in host config
        if (str_contains($this->_config['host'], '/')) {
            $socket = $this->_config['host'];
            $this->_config['host'] = null;
        } elseif (str_contains($this->_config['host'], ':')) {
            [$this->_config['host'], $port] = explode(':', $this->_config['host']);
        }

        $connectionSuccessful = @$conn->real_connect(
            $this->_config['host'],
            $this->_config['username'],
            $this->_config['password'],
            $this->_config['dbname'],
            $port,
            $socket,
        );
        if (!$connectionSuccessful) {
            throw new Zend_Db_Adapter_Mysqli_Exception(mysqli_connect_error());
        }

        $this->_connection = $conn;

        /** @link http://bugs.mysql.com/bug.php?id=18551 */
        $this->_connection->query("SET SQL_MODE=''");
    }

    /**
     * Run RAW Query
     *
     * @param  string                           $sql
     * @return mysqli_result
     * @throws Zend_Db_Adapter_Mysqli_Exception
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function raw_query($sql)
    {
        $timeoutMessage = 'SQLSTATE[HY000]: General error: 1205 Lock wait timeout exceeded; try restarting transaction';
        $tries = 0;
        do {
            $retry = false;
            try {
                $this->clear_result();
                /** @var mysqli $connection */
                $connection = $this->getConnection();
                $result = $connection->query($sql);
                $this->clear_result();
            } catch (Exception $exception) {
                if ($tries < 10 && $exception->getMessage() == $timeoutMessage) {
                    $retry = true;
                    $tries++;
                } else {
                    throw $exception;
                }
            }
        } while ($retry);

        return $result;
    }

    public function convertDate($date)
    {
        if ($date instanceof Zend_Date) {
            return $date->toString(self::ISO_DATE_FORMAT);
        }

        return Carbon::parse($date)->format(Varien_Db_Adapter_Pdo_Mysql::DATE_FORMAT);
    }

    public function convertDateTime($datetime)
    {
        if ($datetime instanceof Zend_Date) {
            return $datetime->toString(self::ISO_DATETIME_FORMAT);
        }

        return Carbon::parse($datetime)->format(Varien_Db_Adapter_Pdo_Mysql::TIMESTAMP_FORMAT);
    }

    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps

    /**
     * @throws Exception
     */
    public function raw_fetchRow($sql, $field = null)
    {
        if (!$result = $this->raw_query($sql)) {
            return false;
        }

        if (!$row = $result->fetch_assoc()) {
            return false;
        }

        if (empty($field)) {
            return $row;
        }

        return $row[$field] ?? false;
    }

    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps

    /**
     * @throws Zend_Db_Adapter_Mysqli_Exception
     */
    public function multi_query($sql)
    {
        $this->beginTransaction();
        try {
            $this->clear_result();
            /** @var mysqli $connection */
            $connection = $this->getConnection();
            if ($connection->multi_query($sql)) {
                $this->clear_result();
                $this->commit();
            } else {
                throw new Zend_Db_Adapter_Mysqli_Exception('multi_query: ' . $connection->error);
            }
        } catch (Exception $exception) {
            $this->rollBack();
            throw $exception;
        }

        return true;
    }

    /**
     * @throws Zend_Db_Adapter_Mysqli_Exception
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function clear_result()
    {
        /** @var mysqli $connection */
        $connection = $this->getConnection();
        while ($connection->next_result()) {
            if ($result = $connection->store_result()) {
                $result->free_result();
            } elseif ($connection->error) {
                throw new Zend_Db_Adapter_Mysqli_Exception('clear_result: ' . $connection->error);
            }
        }
    }

    /**
     * @throws Exception
     * @throws Zend_Db_Adapter_Mysqli_Exception
     */
    public function dropForeignKey($table, $fk)
    {
        $create = $this->raw_fetchRow("show create table `$table`", 'Create Table');
        if (str_contains($create, "CONSTRAINT `$fk` FOREIGN KEY (")) {
            return $this->raw_query("ALTER TABLE `$table` DROP FOREIGN KEY `$fk`");
        }

        return true;
    }

    /**
     * @throws Exception
     * @throws Zend_Db_Adapter_Mysqli_Exception
     */
    public function dropKey($table, $key)
    {
        $create = $this->raw_fetchRow("show create table `$table`", 'Create Table');
        if (str_contains($create, "KEY `$key` (")) {
            return $this->raw_query("ALTER TABLE `$table` DROP KEY `$key`");
        }

        return true;
    }

    /**
     * ADD CONSTRAINT
     *
     * @param  string    $fkName
     * @param  string    $tableName
     * @param  string    $keyName
     * @param  string    $refTableName
     * @param  string    $refKeyName
     * @param  string    $onDelete
     * @param  string    $onUpdate
     * @throws Exception
     */
    public function addConstraint(
        $fkName,
        $tableName,
        $keyName,
        $refTableName,
        $refKeyName,
        $onDelete = 'cascade',
        $onUpdate = 'cascade'
    ) {
        if (!str_starts_with($fkName, 'FK_')) {
            $fkName = 'FK_' . $fkName;
        }

        $sql = 'ALTER TABLE `' . $tableName . '` ADD CONSTRAINT `' . $fkName . '`'
            . 'FOREIGN KEY (`' . $keyName . '`) REFERENCES `' . $refTableName . '` (`' . $refKeyName . '`)';
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
        foreach ($this->fetchAll('DESCRIBE `' . $tableName . '`') as $row) {
            if ($row['Field'] == $columnName) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws Exception
     */
    public function addColumn($tableName, $columnName, $definition)
    {
        if ($this->tableColumnExists($tableName, $columnName)) {
            return true;
        }

        return $this->raw_query("alter table `$tableName` add column `$columnName` " . $definition);
    }

    /**
     * @throws Exception
     */
    public function dropColumn($tableName, $columnName)
    {
        if (!$this->tableColumnExists($tableName, $columnName)) {
            return true;
        }

        $create = $this->raw_fetchRow('SHOW CREATE TABLE `' . $tableName . '`', 'Create Table');

        $alterDrop = [];
        $alterDrop[] = 'DROP COLUMN `' . $columnName . '`';

        /**
         * find foreign keys for column
         */
        $matches = [];
        preg_match_all('/CONSTRAINT `([^`]*)` FOREIGN KEY \(`([^`]*)`\)/', $create, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            if ($match[2] == $columnName) {
                $alterDrop[] = 'DROP FOREIGN KEY `' . $match[1] . '`';
            }
        }

        return $this->raw_query('ALTER TABLE `' . $tableName . '` ' . implode(', ', $alterDrop));
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
}
