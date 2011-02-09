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
 * @category    Mage
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Directory storage database resource model class
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Mysql4_File_Storage_Directory_Database  extends Mage_Core_Model_Mysql4_File_Storage_Abstract
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
     * @return Mage_Core_Model_Mysql4_File_Storage_Database
     */
    public function createDatabaseScheme()
    {
        $this->_getWriteAdapter()->multi_query("CREATE TABLE IF NOT EXISTS {$this->getMainTable()} (
          `directory_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `name` varchar(255) NOT NULL DEFAULT '',
          `path` varchar(255) NOT NULL DEFAULT '',
          `upload_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          `parent_id` int(10) unsigned DEFAULT NULL,
          PRIMARY KEY (`directory_id`),
          UNIQUE KEY `IDX_DIRECTORY_PATH` (`name`, `path`),
          KEY `parent_id` (`parent_id`),
          CONSTRAINT `FK_DIRECTORY_PARENT_ID` FOREIGN KEY (`parent_id`)
          REFERENCES {$this->getMainTable()} (`directory_id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Directory storage'");

        return $this;
    }

    /**
     * Load entity by path
     *
     * @param  Mage_Core_Model_File_Storage_Directory_Database $object
     * @param  string $path
     * @return Mage_Core_Model_Mysql4_File_Storage_Directory_Database
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
            ->from(array('e' => $this->getMainTable()))
            ->where('name = ?', $name)
            ->where('path = ?', $path);

        if ($data = $adapter->fetchRow($select)) {
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
                array('e' => $this->getMainTable()),
                array('directory_id')
            )
            ->where('name = ?', $name)
            ->where('path = ?', $path);

        return $adapter->fetchOne($select);
    }

    /**
     * Delete all directories from storage
     *
     * @return Mage_Core_Model_Mysql4_File_Storage_Database
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
                array('e' => $this->getMainTable()),
                array('name', 'path')
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
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from(
                array('e' => $this->getMainTable()),
                array('name', 'path')
            )
            ->where('path = ?', trim($directory, '/'))
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
        $this->_getWriteAdapter()
            ->delete($this->getMainTable(), array('name = ?' => $name, 'path = ?' => $path));
    }
}
