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
 * @category    Mage
 * @package     Mage_Backup
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Backup_Model_Resource_Helper_Mysql4 extends Mage_Core_Model_Resource_Helper_Mysql4
{
    /**
     * Tables foreign key data array
     * [tbl_name] = array(create foreign key strings)
     *
     * @var array
     */
    protected $_foreignKeys    = array();

    /**
     * Retrieve SQL fragment for drop table
     *
     * @param string $tableName
     * @return string
     */
    public function getTableDropSql($tableName)
    {
        $quotedTableName = $this->_getReadAdapter()->quoteIdentifier($tableName);
        return sprintf('DROP TABLE IF EXISTS %s;', $quotedTableName);
    }

    /**
     * Retrieve foreign keys for table(s)
     *
     * @param string|null $tableName
     * @return string|false
     */
    public function getTableForeignKeysSql($tableName = null)
    {
        $sql = false;

        if ($tableName === null) {
            $sql = '';
            foreach ($this->_foreignKeys as $table => $foreignKeys) {
                $sql .= $this->_buildForeignKeysAlterTableSql($table, $foreignKeys);
            }
        } else if (isset($this->_foreignKeys[$tableName])) {
            $foreignKeys = $this->_foreignKeys[$tableName];
            $sql = $this->_buildForeignKeysAlterTableSql($tableName, $foreignKeys);
        }

        return $sql;
    }

    /**
     * Build sql that will add foreign keys to it
     *
     * @param string $tableName
     * @param array $foreignKeys
     * @return string
     */
    protected function _buildForeignKeysAlterTableSql($tableName, $foreignKeys)
    {
        if (!is_array($foreignKeys) || empty($foreignKeys)) {
            return '';
        }

        return sprintf("ALTER TABLE %s\n  %s;\n",
            $this->_getReadAdapter()->quoteIdentifier($tableName),
            join(",\n  ", $foreignKeys)
        );
    }

     /**
     * Get create script for table
     *
     * @param string $tableName
     * @param boolean $addDropIfExists
     * @return string
     */
    public function getTableCreateScript($tableName, $addDropIfExists = false)
    {
        $script = '';
        $quotedTableName = $this->_getReadAdapter()->quoteIdentifier($tableName);

        if ($addDropIfExists) {
            $script .= 'DROP TABLE IF EXISTS ' . $quotedTableName .";\n";
        }
        //TODO fix me
        $sql     = 'SHOW CREATE TABLE ' . $quotedTableName;
        $data    = $this->_getReadAdapter()->fetchRow($sql);
        $script .= isset($data['Create Table']) ? $data['Create Table'].";\n" : '';

        return $script;
    }
    /**
     * Retrieve SQL fragment for create table
     *
     * @param string $tableName
     * @param bool $withForeignKeys
     * @return string
     */
    public function getTableCreateSql($tableName, $withForeignKeys = false)
    {
        $adapter         = $this->_getReadAdapter();
        $quotedTableName = $adapter->quoteIdentifier($tableName);
        $query           = 'SHOW CREATE TABLE ' . $quotedTableName;
        $row             = $adapter->fetchRow($query);

        if (!$row || !isset($row['Table']) || !isset($row['Create Table'])) {
            return false;
        }

        $regExp  = '/,\s+CONSTRAINT `([^`]*)` FOREIGN KEY \(`([^`]*)`\) '
            . 'REFERENCES `([^`]*)` \(`([^`]*)`\)'
            . '( ON DELETE (RESTRICT|CASCADE|SET NULL|NO ACTION))?'
            . '( ON UPDATE (RESTRICT|CASCADE|SET NULL|NO ACTION))?/';
        $matches = array();
        preg_match_all($regExp, $row['Create Table'], $matches, PREG_SET_ORDER);

        if (is_array($matches)) {
            foreach ($matches as $match) {
                $this->_foreignKeys[$tableName][] = sprintf('ADD CONSTRAINT %s FOREIGN KEY (%s) REFERENCES %s (%s)%s%s',
                    $adapter->quoteIdentifier($match[1]),
                    $adapter->quoteIdentifier($match[2]),
                    $adapter->quoteIdentifier($match[3]),
                    $adapter->quoteIdentifier($match[4]),
                    isset($match[5]) ? $match[5] : '',
                    isset($match[7]) ? $match[7] : ''
                );
            }
        }

        if ($withForeignKeys) {
            $sql = $row['Create Table'];
        } else {
            $sql = preg_replace($regExp, '', $row['Create Table']);
        }

        return $sql . ';';
    }
    /**
     * Returns SQL header data, move from original resource model
     *
     * @return string
     */
    public function getHeader()
    {
        $dbConfig = $this->_getReadAdapter()->getConfig();

        $versionRow = $this->_getReadAdapter()->fetchRow('SHOW VARIABLES LIKE \'version\'');
        $hostName   = !empty($dbConfig['unix_socket']) ? $dbConfig['unix_socket']
            : (!empty($dbConfig['host']) ? $dbConfig['host'] : 'localhost');

        $header = "-- Magento DB backup\n"
            . "--\n"
            . "-- Host: {$hostName}    Database: {$dbConfig['dbname']}\n"
            . "-- ------------------------------------------------------\n"
            . "-- Server version: {$versionRow['Value']}\n\n"
            . "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n"
            . "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n"
            . "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n"
            . "/*!40101 SET NAMES utf8 */;\n"
            . "/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;\n"
            . "/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;\n"
            . "/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;\n"
            . "/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;\n";

        return $header;
    }

    /**
     * Returns SQL footer data, move from original resource model
     *
     * @return string
     */
    public function getFooter()
    {
        $footer = "\n/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;\n"
            . "/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */; \n"
            . "/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;\n"
            . "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n"
            . "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n"
            . "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n"
            . "/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;\n"
            . "\n-- Dump completed on " . Mage::getSingleton('core/date')->gmtDate() . " GMT";

        return $footer;
    }

    /**
     * Retrieve before insert data SQL fragment
     *
     * @param string $tableName
     * @return string
     */
    public function getTableDataBeforeSql($tableName)
    {
        $quotedTableName = $this->_getReadAdapter()->quoteIdentifier($tableName);
        return "\n--\n"
            . "-- Dumping data for table {$quotedTableName}\n"
            . "--\n\n"
            . "LOCK TABLES {$quotedTableName} WRITE;\n"
            . "/*!40000 ALTER TABLE {$quotedTableName} DISABLE KEYS */;\n";
    }

    /**
     * Retrieve after insert data SQL fragment
     *
     * @param string $tableName
     * @return string
     */
    public function getTableDataAfterSql($tableName)
    {
        $quotedTableName = $this->_getReadAdapter()->quoteIdentifier($tableName);
        return "/*!40000 ALTER TABLE {$quotedTableName} ENABLE KEYS */;\n"
            . "UNLOCK TABLES;\n";
    }

    /**
     * Return table part data SQL insert
     *
     * @param string $tableName
     * @param int $count
     * @param int $offset
     * @return string
     */
    public function getPartInsertSql($tableName, $count = null, $offset = null)
    {
        $sql = null;
        $adapter = $this->_getWriteAdapter();
        $select = $adapter->select()
            ->from($tableName)
            ->limit($count, $offset);
        $query  = $adapter->query($select);

        while ($row = $query->fetch()) {
            if ($sql === null) {
                $sql = sprintf('INSERT INTO %s VALUES ', $adapter->quoteIdentifier($tableName));
            } else {
                $sql .= ',';
            }

            $sql .= $this->_quoteRow($tableName, $row);
        }

        if ($sql !== null) {
            $sql .= ';' . "\n";
        }

        return $sql;
    }
    /**
     * Return table data SQL insert
     *
     * @param string $tableName
     * @return string
     */
    public function getInsertSql($tableName)
    {
        return $this->getPartInsertSql($tableName);
    }
    /**
     * Quote Table Row
     *
     * @param string $tableName
     * @param array $row
     * @return string
     */
    protected function _quoteRow($tableName, array $row)
    {
        $adapter   = $this->_getReadAdapter();
        $describe  = $adapter->describeTable($tableName);
        $dataTypes = array('bigint', 'mediumint', 'smallint', 'tinyint');
        $rowData   = array();
        foreach ($row as $k => $v) {
            if ($v === null) {
                $value = 'NULL';
            } elseif (in_array(strtolower($describe[$k]['DATA_TYPE']), $dataTypes)) {
                $value = $v;
            } else {
                $value = $adapter->quoteInto('?', $v);
            }
            $rowData[] = $value;
        }

        return sprintf('(%s)', implode(',', $rowData));
    }

    /**
     * Turn on serializable mode
     */
    public function turnOnSerializableMode()
    {
        $this->_getReadAdapter()->query("SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE");
    }

    /**
     * Turn on read committed mode
     */
    public function turnOnReadCommittedMode()
    {
        $this->_getReadAdapter()->query("SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED");
    }
}
