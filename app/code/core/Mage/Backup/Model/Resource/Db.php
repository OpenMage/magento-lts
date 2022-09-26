<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Backup
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Database backup resource model
 *
 * @category   Mage
 * @package    Mage_Backup
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backup_Model_Resource_Db
{
    /**
     * Database connection adapter
     *
     * @var Varien_Db_Adapter_Pdo_Mysql
     */
    protected $_write;

    /**
     * tables Foreign key data array
     * [tbl_name] = array(create foreign key strings)
     *
     * @var array
     */
    protected $_foreignKeys    = [];

    /**
     * Initialize Backup DB resource model
     *
     */
    public function __construct()
    {
        $this->_write = Mage::getSingleton('core/resource')->getConnection('backup_write');
    }

    /**
     * @deprecated after 1.4.0.0-alpha2
     *
     */
    public function crear()
    {
        $this->clear();
    }

    /**
     * Clear data
     *
     */
    public function clear()
    {
        $this->_foreignKeys = [];
    }

    /**
     * Retrieve table list
     *
     * @return array
     */
    public function getTables()
    {
        return $this->_write->listTables();
    }

    /**
     * Retrieve SQL fragment for drop table
     *
     * @param string $tableName
     * @return string
     */
    public function getTableDropSql($tableName)
    {
        /** @var Mage_Backup_Model_Resource_Helper_Mysql4 $helper */
        $helper = Mage::getResourceHelper('backup');
        return $helper->getTableDropSql($tableName);
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
        /** @var Mage_Backup_Model_Resource_Helper_Mysql4 $helper */
        $helper = Mage::getResourceHelper('backup');
        return $helper->getTableCreateSql($tableName, $withForeignKeys = false);
    }

    /**
     * Retrieve foreign keys for table(s)
     *
     * @param string|null $tableName
     * @return string
     */
    public function getTableForeignKeysSql($tableName = null)
    {
        $fkScript = '';
        if (!$tableName) {
            $tables = $this->getTables();
            /** @var Mage_Backup_Model_Resource_Helper_Mysql4 $helper */
            $helper = Mage::getResourceHelper('backup');
            foreach($tables as $table) {
                $tableFkScript = $helper->getTableForeignKeysSql($table);
                if (!empty($tableFkScript)) {
                    $fkScript .= "\n" . $tableFkScript;
                }
            }
        } else {
            $fkScript = $this->getTableForeignKeysSql($tableName);
        }
        return $fkScript;
    }

    /**
     * Retrieve table status
     *
     * @param string $tableName
     * @return Varien_Object|false
     */
    public function getTableStatus($tableName)
    {
        $row = $this->_write->showTableStatus($tableName);

        if ($row) {
            $statusObject = new Varien_Object();
            $statusObject->setIdFieldName('name');
            foreach ($row as $field => $value) {
                $statusObject->setData(strtolower($field), $value);
            }

            $cntRow = $this->_write->fetchRow(
                    $this->_write->select()->from($tableName, 'COUNT(1) as rows'));
            $statusObject->setRows($cntRow['rows']);

            return $statusObject;
        }

        return false;
    }

    /**
     * Quote Table Row
     *
     * @deprecated
     *
     * @param string $tableName
     * @param array $row
     * @return array
     */
    protected function _quoteRow($tableName, array $row)
    {
        return $row;
    }

    /**
     * Retrieve table partical data SQL insert
     *
     * @param string $tableName
     * @param int $count
     * @param int $offset
     * @return string
     */
    public function getTableDataSql($tableName, $count = null, $offset = null)
    {
        /** @var Mage_Backup_Model_Resource_Helper_Mysql4 $helper */
        $helper = Mage::getResourceHelper('backup');
        return $helper->getPartInsertSql($tableName, $count, $offset);
    }

    /**
     * @param string $tableName
     * @param bool $addDropIfExists
     * @return string
     */
    public function getTableCreateScript($tableName, $addDropIfExists = false)
    {
        /** @var Mage_Backup_Model_Resource_Helper_Mysql4 $helper */
        $helper = Mage::getResourceHelper('backup');
        return $helper->getTableCreateScript($tableName, $addDropIfExists);
    }

    /**
     * Retrieve table header comment
     *
     * @param string $tableName
     * @return string
     */
    public function getTableHeader($tableName)
    {
        $quotedTableName = $this->_write->quoteIdentifier($tableName);
        return "\n--\n"
            . "-- Table structure for table {$quotedTableName}\n"
            . "--\n\n";
    }

    /**
     * Return table data dump
     *
     * @param string $tableName
     * @param bool $step
     * @return string
     */
    public function getTableDataDump($tableName, $step = false)
    {
        return $this->getTableDataSql($tableName);
    }

    /**
     * Returns SQL header data
     *
     * @return string
     */
    public function getHeader()
    {
        /** @var Mage_Backup_Model_Resource_Helper_Mysql4 $helper */
        $helper = Mage::getResourceHelper('backup');
        return $helper->getHeader();
    }

    /**
     * Returns SQL footer data
     *
     * @return string
     */
    public function getFooter()
    {
        /** @var Mage_Backup_Model_Resource_Helper_Mysql4 $helper */
        $helper = Mage::getResourceHelper('backup');
        return $helper->getFooter();
    }

    /**
     * Retrieve before insert data SQL fragment
     *
     * @param string $tableName
     * @return string
     */
    public function getTableDataBeforeSql($tableName)
    {
        /** @var Mage_Backup_Model_Resource_Helper_Mysql4 $helper */
        $helper = Mage::getResourceHelper('backup');
        return $helper->getTableDataBeforeSql($tableName);
    }

    /**
     * Retrieve after insert data SQL fragment
     *
     * @param string $tableName
     * @return string
     */
    public function getTableDataAfterSql($tableName)
    {
        /** @var Mage_Backup_Model_Resource_Helper_Mysql4 $helper */
        $helper = Mage::getResourceHelper('backup');
        return $helper->getTableDataAfterSql($tableName);
    }

    /**
     * Start transaction mode
     *
     * @return $this
     */
    public function beginTransaction()
    {
        /** @var Mage_Backup_Model_Resource_Helper_Mysql4 $helper */
        $helper = Mage::getResourceHelper('backup');
        $helper->turnOnSerializableMode();
        $this->_write->beginTransaction();
        return $this;
    }

    /**
     * Commit transaction
     *
     * @return $this
     */
    public function commitTransaction()
    {
        $this->_write->commit();
        /** @var Mage_Backup_Model_Resource_Helper_Mysql4 $helper */
        $helper = Mage::getResourceHelper('backup');
        $helper->turnOnReadCommittedMode();
        return $this;
    }

    /**
     * Rollback transaction
     *
     * @return $this
     */
    public function rollBackTransaction()
    {
        $this->_write->rollBack();
        return $this;
    }

    /**
     * Run sql code
     *
     * @param string|Zend_Db_Select $command
     * @return $this
     */
    public function runCommand($command){
        $this->_write->query($command);
        return $this;
    }
}
