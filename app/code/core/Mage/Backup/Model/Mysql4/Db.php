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
 * @category   Mage
 * @package    Mage_Backup
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Database backup resource model
 *
 * @category   Mage
 * @package    Mage_Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backup_Model_Mysql4_Db
{
    /**
     * Read connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_read;

    /**
     * tables Foreign key data array
     * [tbl_name] = array(create foreign key strings)
     *
     * @var array
     */
    protected $_foreignKeys = array();

    /**
     * Initialize Backup DB resource model
     *
     */
    public function __construct()
    {
        $this->_read = Mage::getSingleton('core/resource')->getConnection('backup_read');
    }

    /**
     * Clear data
     *
     */
    public function crear()
    {
        $this->_foreignKeys = array();
    }

    /**
     * Retrieve table list
     *
     * @return array
     */
    public function getTables()
    {
        return $this->_read->listTables();
    }

    /**
     * Retrieve SQL fragment for drop table
     *
     * @param string $tableName
     * @return string
     */
    public function getTableDropSql($tableName)
    {
        $quotedTableName = $this->_read->quoteIdentifier($tableName);
        return 'DROP TABLE IF EXISTS ' . $quotedTableName . ';';
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
        $quotedTableName = $this->_read->quoteIdentifier($tableName);
        $sql = 'SHOW CREATE TABLE ' . $quotedTableName;
        $row = $this->_read->fetchRow($sql);

        if (!$row) {
            return false;
        }

        $regExp  = '/,\s+CONSTRAINT `([^`]*)` FOREIGN KEY \(`([^`]*)`\) '
            . 'REFERENCES `([^`]*)` \(`([^`]*)`\)'
            . '( ON DELETE (RESTRICT|CASCADE|SET NULL|NO ACTION))?'
            . '( ON UPDATE (RESTRICT|CASCADE|SET NULL|NO ACTION))?/';
        $matches = array();
        preg_match_all($regExp, $row['Create Table'], $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $this->_foreignKeys[$tableName][] = sprintf('ADD CONSTRAINT %s FOREIGN KEY (%s) REFERENCES %s (%s)%s%s',
                $this->_read->quoteIdentifier($match[1]),
                $this->_read->quoteIdentifier($match[2]),
                $this->_read->quoteIdentifier($match[3]),
                $this->_read->quoteIdentifier($match[4]),
                isset($match[5]) ? $match[5] : '',
                isset($match[7]) ? $match[7] : ''
            );
        }

        if ($withForeignKeys) {
            return $row['Create Table'] . ';';
        }
        else {
            return preg_replace($regExp, '', $row['Create Table']) . ';';
        }
    }

    /**
     * Retrieve foreign keys for table(s)
     *
     * @param string|null $tableName
     * @return string
     */
    public function getTableForeignKeysSql($tableName = null)
    {
        if (is_null($tableName)) {
            $sql = '';
            foreach ($this->_foreignKeys as $table => $foreignKeys) {
                $sql .= sprintf("ALTER TABLE %s\n  %s;\n",
                    $this->_read->quoteIdentifier($table),
                    join(",\n  ", $foreignKeys)
                );
            }
            return $sql;
        }
        if (isset($this->_foreignKeys[$tableName]) && ($foreignKeys = $this->_foreignKeys[$tableName])) {

        }
        return false;
    }

    /**
     * Retrieve table status
     *
     * @param string $tableName
     * @return Varien_Object
     */
    public function getTableStatus($tableName)
    {
        $sql = $this->_read->quoteInto('SHOW TABLE STATUS LIKE ?', $tableName);
        $row = $this->_read->fetchRow($sql);

        if ($row) {
            $statusObject = new Varien_Object();
            $statusObject->setIdFieldName('name');
            foreach ($row as $field => $value) {
                $statusObject->setData(strtolower($field), $value);
            }

            $cntRow = $this->_read->fetchRow( $this->_read->select()->from($tableName, 'COUNT(*) as rows'));
            $statusObject->setRows($cntRow['rows']);

            return $statusObject;
        }

        return false;
    }

    /**
     * Retrive table partical data SQL insert
     *
     * @param string $tableName
     * @param int $count
     * @param int $offset
     * @return string
     */
    public function getTableDataSql($tableName, $count, $offset = 0)
    {
        $sql = null;
        $quotedTableName = $this->_read->quoteIdentifier($tableName);
        $select = $this->_read->select()
            ->from($tableName)
            ->limit($count, $offset);
        $query  = $this->_read->query($select);

        while ($row = $query->fetch()) {
            if (is_null($sql)) {
                $sql = 'INSERT INTO ' . $quotedTableName . ' VALUES ';
            }
            else {
                $sql .= ',';
            }

            //$sql .= $this->_read->quoteInto('(?)', $row);
            $rowData = array();
            foreach ($row as $v) {
                if (is_null($v)) {
                    $value = 'NULL';
                }
                elseif (is_numeric($v) && $v == intval($v)) {
                    $value = $v;
                }
                else {
                    $value = $this->_read->quoteInto('?', $v);
                }
                $rowData[] = $value;
            }
            $sql .= '('.join(',', $rowData).')';
        }

        if (!is_null($sql)) {
            $sql .= ';' . "\n";
        }

        return $sql;
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $tableName
     * @param unknown_type $addDropIfExists
     * @return unknown
     */
    public function getTableCreateScript($tableName, $addDropIfExists=false)
    {
        $script = '';
        if ($this->_read) {
            $quotedTableName = $this->_read->quoteIdentifier($tableName);

            if ($addDropIfExists) {
                $script .= 'DROP TABLE IF EXISTS ' . $quotedTableName .";\n";
            }
            $sql = 'SHOW CREATE TABLE ' . $quotedTableName;
            $data = $this->_read->fetchRow($sql);
            $script.= isset($data['Create Table']) ? $data['Create Table'].";\n" : '';
        }

        return $script;
    }

    /**
     * Retrieve table header comment
     *
     * @return string
     */
    public function getTableHeader($tableName)
    {
        $quotedTableName = $this->_read->quoteIdentifier($tableName);
        return "\n--\n"
            . "-- Table structure for table {$quotedTableName}\n"
            . "--\n\n";
    }

    public function getTableDataDump($tableName, $step=100)
    {
        $sql = '';
        if ($this->_read) {
            $quotedTableName = $this->_read->quoteIdentifier($tableName);
            $colunms = $this->_read->fetchRow('SELECT * FROM '.$quotedTableName.' LIMIT 1');
            if ($colunms) {
                $arrSql = array();

                $colunms = array_keys($colunms);
                $quote = $this->_read->getQuoteIdentifierSymbol();
                $sql = 'INSERT INTO ' . $quotedTableName . ' (' .$quote . implode($quote.', '.$quote,$colunms).$quote.')';
                $sql.= ' VALUES ';

                $startRow = 0;
                $select = $this->_read->select();
                $select->from($tableName)
                    ->limit($step, $startRow);
                while ($data = $this->_read->fetchAll($select)) {
                    $dataSql = array();
                    foreach ($data as $row) {
                    	$dataSql[] = $this->_read->quoteInto('(?)', $row);
                    }
                    $arrSql[] = $sql.implode(', ', $dataSql).';';
                    $startRow += $step;
                    $select->limit($step, $startRow);
                }

                $sql = implode("\n", $arrSql)."\n";
            }

        }

        return $sql;
    }

    /**
     * Returns SQL header data
     */
    public function getHeader()
    {
        $dbConfig = $this->_read->getConfig();

        $versionRow = $this->_read->fetchRow('SHOW VARIABLES LIKE \'version\'');

        $header = "-- Magento DB backup\n"
            . "--\n"
            . "-- Host: {$dbConfig['host']}    Database: {$dbConfig['dbname']}\n"
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
     * Returns SQL footer data
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
        $quotedTableName = $this->_read->quoteIdentifier($tableName);
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
        $quotedTableName = $this->_read->quoteIdentifier($tableName);
        return "/*!40000 ALTER TABLE {$quotedTableName} ENABLE KEYS */;\n"
            . "UNLOCK TABLES;\n";
    }

    public function beginTransaction()
    {
        $this->_read->beginTransaction();
    }

    public function commitTransaction()
    {
        $this->_read->commit();
    }

    public function rollBackTransaction()
    {
        $this->_read->rollBack();
    }
}