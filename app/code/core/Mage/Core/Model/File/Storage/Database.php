<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * File storage database model class
 *
 * @package    Mage_Core
 *
 * @method Mage_Core_Model_Resource_File_Storage_Database _getResource()
 * @method string                                         getConnectionName()
 * @method Mage_Core_Model_Resource_File_Storage_Database getResource()
 * @method $this                                          setDirectoryId(int $value)
 */
class Mage_Core_Model_File_Storage_Database extends Mage_Core_Model_File_Storage_Database_Abstract
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'core_file_storage_database';

    /**
     * Directory singleton
     *
     * @var null|Mage_Core_Model_File_Storage_Directory_Database
     */
    protected $_directoryModel = null;

    /**
     * Collect errors during sync process
     *
     * @var array
     */
    protected $_errors = [];

    /**
     * Class construct
     *
     * @param string $connectionName
     */
    public function __construct($connectionName = null)
    {
        $this->_init('core/file_storage_database');

        parent::__construct($connectionName);
    }

    /**
     * Retrieve directory model
     *
     * @return Mage_Core_Model_File_Storage_Directory_Database
     */
    public function getDirectoryModel()
    {
        if (is_null($this->_directoryModel)) {
            $this->_directoryModel = Mage::getModel(
                'core/file_storage_directory_database',
                ['connection' => $this->getConnectionName()],
            );
        }

        return $this->_directoryModel;
    }

    /**
     * Create tables for file and directory storages
     *
     * @return $this
     */
    public function init()
    {
        $this->getDirectoryModel()->prepareStorage();
        $this->prepareStorage();

        return $this;
    }

    /**
     * Return storage name
     *
     * @return string
     */
    public function getStorageName()
    {
        return Mage::helper('core')->__('database "%s"', $this->getConnectionName());
    }

    /**
     * Load object data by filename
     *
     * @param  string $filePath
     * @return $this
     */
    public function loadByFilename($filePath)
    {
        $filename = basename($filePath);
        $path = dirname($filePath);
        $this->_getResource()->loadByFilename($this, $filename, $path);
        return $this;
    }

    /**
     * Check if there was errors during sync process
     *
     * @return bool
     */
    public function hasErrors()
    {
        return (!empty($this->_errors) || $this->getDirectoryModel()->hasErrors());
    }

    /**
     * Clear files and directories in storage
     *
     * @return $this
     */
    public function clear()
    {
        $this->getDirectoryModel()->clearDirectories();
        $this->_getResource()->clearFiles();
        return $this;
    }

    /**
     * Export directories from storage
     *
     * @param  int        $offset
     * @param  int        $count
     * @return array|bool
     */
    public function exportDirectories($offset = 0, $count = 100)
    {
        return $this->getDirectoryModel()->exportDirectories($offset, $count);
    }

    /**
     * Import directories to storage
     *
     * @param  array                                           $dirs
     * @return Mage_Core_Model_File_Storage_Directory_Database
     */
    public function importDirectories($dirs)
    {
        return $this->getDirectoryModel()->importDirectories($dirs);
    }

    /**
     * Export files list in defined range
     *
     * @param  int        $offset
     * @param  int        $count
     * @return array|bool
     */
    public function exportFiles($offset = 0, $count = 100)
    {
        $offset = max((int) $offset, 0);
        $count  = max((int) $count, 1);

        $result = $this->_getResource()->getFiles($offset, $count);
        if (empty($result)) {
            return false;
        }

        return $result;
    }

    /**
     * Import files list
     *
     * @param  array $files
     * @return $this
     */
    public function importFiles($files)
    {
        if (!is_array($files)) {
            return $this;
        }

        $dateSingleton = Mage::getSingleton('core/date');
        foreach ($files as $file) {
            if (!isset($file['filename']) || !strlen($file['filename']) || !isset($file['content'])) {
                continue;
            }

            try {
                $file['update_time'] = $dateSingleton->date();
                $file['directory_id'] = (isset($file['directory']) && strlen($file['directory']))
                    ? Mage::getModel(
                        'core/file_storage_directory_database',
                        ['connection' => $this->getConnectionName()],
                    )
                            ->loadByPath($file['directory'])->getId()
                    : null;

                $this->_getResource()->saveFile($file);
            } catch (Exception $e) {
                $this->_errors[] = $e->getMessage();
                Mage::logException($e);
            }
        }

        return $this;
    }

    /**
     * Store file into database
     *
     * @param  string $filename
     * @return $this
     */
    public function saveFile($filename)
    {
        $fileInfo = $this->collectFileInfo($filename);
        $filePath = $fileInfo['directory'];

        $directory = Mage::getModel('core/file_storage_directory_database')->loadByPath($filePath);

        if (!$directory->getId()) {
            $directory = $this->getDirectoryModel()->createRecursive($filePath);
        }

        $fileInfo['directory_id'] = $directory->getId();
        $this->_getResource()->saveFile($fileInfo);

        return $this;
    }

    /**
     * Check whether file exists in DB
     *
     * @param  string $filePath
     * @return bool
     */
    public function fileExists($filePath)
    {
        return $this->_getResource()->fileExists(basename($filePath), dirname($filePath));
    }

    /**
     * Copy files
     *
     * @param  string $oldFilePath
     * @param  string $newFilePath
     * @return $this
     */
    public function copyFile($oldFilePath, $newFilePath)
    {
        $this->_getResource()->copyFile(
            basename($oldFilePath),
            dirname($oldFilePath),
            basename($newFilePath),
            dirname($newFilePath),
        );

        return $this;
    }

    /**
     * Rename files in database
     *
     * @param  string $oldFilePath
     * @param  string $newFilePath
     * @return $this
     */
    public function renameFile($oldFilePath, $newFilePath)
    {
        $this->_getResource()->renameFile(
            basename($oldFilePath),
            dirname($oldFilePath),
            basename($newFilePath),
            dirname($newFilePath),
        );

        $newPath = dirname($newFilePath);
        $directory = Mage::getModel('core/file_storage_directory_database')->loadByPath($newPath);

        if (!$directory->getId()) {
            $directory = $this->getDirectoryModel()->createRecursive($newPath);
        }

        $this->loadByFilename($newFilePath);
        if ($this->getId()) {
            $this->setDirectoryId($directory->getId())->save();
        }

        return $this;
    }

    /**
     * Return directory listing
     *
     * @param  string $directory
     * @return mixed
     */
    public function getDirectoryFiles($directory)
    {
        $directory = Mage::helper('core/file_storage_database')->getMediaRelativePath($directory);
        return $this->_getResource()->getDirectoryFiles($directory);
    }

    /**
     * Delete file from database
     *
     * @param  string $path
     * @return $this
     */
    public function deleteFile($path)
    {
        $filename = basename($path);
        $directory = dirname($path);
        $this->_getResource()->deleteFile($filename, $directory);

        return $this;
    }
}
