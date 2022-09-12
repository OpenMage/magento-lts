<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Directory storage database resource model class
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Resource_File_Storage_Directory_Database extends Mage_Core_Model_Resource_File_Storage_Abstract
{
    /**
     * Define table name and id field for resource
     */
    protected function _construct()
    {
        $this->_init('core/directory_storage', 'directory_id');
    }

    /**
     * Create database scheme for storing files
     *
     * @return $this
     */
    public function createDatabaseScheme()
    {
        $adapter = $this->_getWriteAdapter();
        $table = $this->getMainTable();
        if ($adapter->isTableExists($table)) {
            return $this;
        }

        $ddlTable = $adapter->newTable($table)
            ->addColumn('directory_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true
            ], 'Directory Id')
            ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 100, [
                'nullable' => false
            ], 'Directory Name')
            ->addColumn('path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
                'default' => null], 'Path to the Directory')
            ->addColumn('upload_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
                'nullable' => false,
                'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT
            ], 'Upload Timestamp')
            ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
                'nullable' => true,
                'default' => null,
                'unsigned' => true
            ], 'Parent Directory Id')
            ->addIndex(
                $adapter->getIndexName(
                    $table,
                    ['name', 'parent_id'],
                    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
                ),
                ['name', 'parent_id'],
                ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
            )
            ->addIndex($adapter->getIndexName($table, ['parent_id']), ['parent_id'])
            ->addForeignKey(
                $adapter->getForeignKeyName($table, 'parent_id', $table, 'directory_id'),
                'parent_id',
                $table,
                'directory_id',
                Varien_Db_Ddl_Table::ACTION_CASCADE,
                Varien_Db_Ddl_Table::ACTION_CASCADE
            )
            ->setComment('Directory Storage');

        $adapter->createTable($ddlTable);
        return $this;
    }

    /**
     * Load entity by path
     *
     * @param  Mage_Core_Model_File_Storage_Directory_Database $object
     * @param  string $path
     * @return $this
     */
    public function loadByPath(Mage_Core_Model_File_Storage_Directory_Database $object, $path)
    {
        $adapter = $this->_getReadAdapter();

        $name = basename($path);
        $path = dirname($path);
        if ($path == '.') {
            $path = '';
        }

        $select = $adapter->select()
            ->from(['e' => $this->getMainTable()])
            ->where('name = ?', $name)
            ->where($adapter->prepareSqlCondition('path', ['seq' => $path]));

        $data = $adapter->fetchRow($select);
        if ($data) {
            $object->setData($data);
            $this->_afterLoad($object);
        }

        return $this;
    }

    /**
     * Return parent id
     *
     * @param string $path
     * @return int
     */
    public function getParentId($path)
    {
        $adapter = $this->_getReadAdapter();

        $name = basename($path);
        $path = dirname($path);
        if ($path == '.') {
            $path = '';
        }

        $select = $adapter->select()
            ->from(
                ['e' => $this->getMainTable()],
                ['directory_id']
            )
            ->where('name = ?', $name)
            ->where($adapter->prepareSqlCondition('path', ['seq' => $path]));

        return $adapter->fetchOne($select);
    }

    /**
     * Delete all directories from storage
     *
     * @return $this
     */
    public function clearDirectories()
    {
        $adapter = $this->_getWriteAdapter();
        $adapter->delete($this->getMainTable());

        return $this;
    }

    /**
     * Export directories from database
     *
     * @param int $offset
     * @param int $count
     * @return mixed
     */
    public function exportDirectories($offset, $count = 100)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from(
                ['e' => $this->getMainTable()],
                ['name', 'path']
            )
            ->order('directory_id')
            ->limit($count, $offset);

        return $adapter->fetchAll($select);
    }

    /**
     * Return directory file listing
     *
     * @param string $directory
     * @return mixed
     */
    public function getSubdirectories($directory)
    {
        $directory = trim($directory, '/');
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from(
                ['e' => $this->getMainTable()],
                ['name', 'path']
            )
            ->where($adapter->prepareSqlCondition('path', ['seq' => $directory]))
            ->order('directory_id');

        return $adapter->fetchAll($select);
    }

    /**
     * Delete directory
     *
     * @param string $name
     * @param string $path
     */
    public function deleteDirectory($name, $path)
    {
        $adapter = $this->_getWriteAdapter();

        $where = ['name = ?' => $name];
        $where[] = new Zend_Db_Expr($adapter->prepareSqlCondition('path', ['seq' => $path]));

        $adapter->delete($this->getMainTable(), $where);
    }
}
