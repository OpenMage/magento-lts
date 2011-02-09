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
 * File storage database resource resource model class
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Mysql4_File_Storage_Database  extends Mage_Core_Model_Mysql4_File_Storage_Abstract
{
    /**
     * Define table name and id field for resource
     */
    protected function _construct()
    {
        $this->_init('core/file_storage', 'file_id');
    }

    /**
     * Create database scheme for storing files
     *
     * @return Mage_Core_Model_Mysql4_File_Storage_Database
     */
    public function createDatabaseScheme()
    {
        $this->_getWriteAdapter()->multi_query("CREATE TABLE IF NOT EXISTS {$this->getMainTable()} (
          `file_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `content` LONGBLOB NOT NULL,
          `upload_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          `filename` varchar(255) NOT NULL DEFAULT '',
          `directory_id` int(10) unsigned DEFAULT NULL,
          `directory` varchar(255) DEFAULT NULL,
          PRIMARY KEY (`file_id`),
          UNIQUE KEY `IDX_FILENAME` (`filename`, `directory`),
          KEY (`directory_id`),
          CONSTRAINT `FK_FILE_DIRECTORY` FOREIGN KEY (`directory_id`)
            REFERENCES {$this->getTable('core/directory_storage')} (`directory_id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='File storage'");

        return $this;
    }

    /**
     * Load entity by filename
     *
     * @param  Mage_Core_Model_File_Storage_Database $object
     * @param  string $filename
     * @param  string $path
     * @return Mage_Core_Model_Mysql4_File_Storage_Database
     */
    public function loadByFilename(Mage_Core_Model_File_Storage_Database $object, $filename, $path)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from(array('e' => $this->getMainTable()))
            ->where('filename = ?', $filename)
            ->where('directory = ?', $path);

        if ($data = $adapter->fetchRow($select)) {
            $object->setData($data);
            $this->_afterLoad($object);
        }

        return $this;
    }

    /**
     * Clear files in storage
     *
     * @return Mage_Core_Model_Mysql4_File_Storage_Database
     */
    public function clearFiles()
    {
        $adapter = $this->_getWriteAdapter();
        $adapter->delete($this->getMainTable());

        return $this;
    }

    /**
     * Get files from storage at defined range
     *
     * @param  int $offset
     * @param  int $count
     * @return array
     */
    public function getFiles($offset = 0, $count = 100)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from(
                array('e' => $this->getMainTable()),
                array('filename', 'content', 'directory')
            )
            ->order('file_id')
            ->limit($count, $offset);

        return $adapter->fetchAll($select);
    }

    /**
     * Save matched product Ids
     *
     * @param  Mage_Core_Model_File_Storage_Database|array $object
     * @return Mage_Core_Model_Mysql4_File_Storage_Database
     */
    public function saveFile($file)
    {
        $adapter = $this->_getWriteAdapter();
        $data    = array(
            'content'        => $file['content'],
            'upload_time'    => $file['update_time'],
            'filename'       => $file['filename'],
            'directory_id'   => $file['directory_id'],
            'directory'      => $file['directory']
        );

        $adapter->insertOnDuplicate($this->getMainTable(), $data, array('content', 'upload_time'));

        return $this;
    }

    /**
     * Rename files in database
     *
     * @param  string $oldFilename
     * @param  string $oldPath
     * @param  string $newFilename
     * @param  string $newPath
     * @return Mage_Core_Model_Mysql4_File_Storage_Database
     */
    public function renameFile($oldFilename, $oldPath, $newFilename, $newPath)
    {
        $adapter    = $this->_getWriteAdapter();
        $dataUpdate = array('filename' => $newFilename, 'directory' => $newPath);
        $dataWhere  = array('filename = ?' => $oldFilename, 'directory = ?' => $oldPath);

        $adapter->update($this->getMainTable(), $dataUpdate, $dataWhere);

        return $this;
    }

    /**
     * Copy files in database
     *
     * @param  string $oldFilename
     * @param  string $oldPath
     * @param  string $newFilename
     * @param  string $newPath
     * @return Mage_Core_Model_Mysql4_File_Storage_Database
     */
    public function copyFile($oldFilename, $oldPath, $newFilename, $newPath)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from(array('e' => $this->getMainTable()))
            ->where('filename = ?', $oldFilename)
            ->where('directory = ?', $oldPath);

        $data = $adapter->fetchRow($select);
        if (!$data) {
            return $this;
        }

        if (isset($data['file_id']) && isset($data['filename'])) {
            unset($data['file_id']);
            $data['filename'] = $newFilename;
            $data['directory'] = $newPath;

            $writeAdapter = $this->_getWriteAdapter();
            $writeAdapter->insertOnDuplicate($this->getMainTable(), $data, array('content', 'upload_time'));
        }

        return $this;
    }

    /**
     * Check whether file exists in DB
     *
     * @param string $filename
     * @param string $path
     * @return bool
     */
    public function fileExists($filename, $path)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from(array('e' => $this->getMainTable()))
            ->where('filename = ?', $filename)
            ->where('directory = ?', $path)
            ->limit(1);

        $data = $adapter->fetchRow($select);
        return (bool)$data;
    }

    /**
     * Delete files that starts with given $folderName
     *
     * @param string $folderName
     */
    public function deleteFolder($folderName = '')
    {
        if ($folderName && ($folderName !== '/')) {
            $folderName = str_replace(array('%','_'), array('\%','\_'), $folderName);
            $folderName = rtrim($folderName, '/');

            $adapter = $this->_getWriteAdapter();
            $adapter->delete($this->getMainTable(), new Zend_Db_Expr('filename LIKE "' . $folderName . '/%"'));
        }
    }

    /**
     * Delete file
     *
     * @param string $filename
     * @param string $directory
     */
    public function deleteFile($filename, $directory)
    {
        $this->_getWriteAdapter()
            ->delete($this->getMainTable(), array('filename = ?' => $filename, 'directory = ?' => $directory));
    }

    /**
     * Return directory file listing
     *
     * @param string $directory
     * @return mixed
     */
    public function getDirectoryFiles($directory)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from(
                array('e' => $this->getMainTable()),
                array(
                    'filename',
                    'directory',
                    'content'
                )
            )
            ->where('directory = ?', trim($directory, '/'))
            ->order('file_id');

        return $adapter->fetchAll($select);
    }
}
