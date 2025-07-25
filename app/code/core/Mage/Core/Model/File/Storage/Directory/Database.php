<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Directory database storage model class
 *
 * @package    Mage_Core
 *
 * @method Mage_Core_Model_Resource_File_Storage_Directory_Database _getResource()
 * @method string getConnectionName()
 * @method $this setName(string $value)
 * @method string getPath()
 * @method $this setPath(string $value)
 * @method $this setParentId(string $value)
 * @method $this setUploadTime(string $value)
 */
class Mage_Core_Model_File_Storage_Directory_Database extends Mage_Core_Model_File_Storage_Database_Abstract
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'core_file_storage_directory_database';

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
        $this->_init('core/file_storage_directory_database');

        parent::__construct($connectionName);
    }

    /**
     * Load object data by path
     *
     * @param  string $path
     * @return $this
     */
    public function loadByPath($path)
    {
        /**
         * Clear model data
         * addData() is used because it's needed to clear only db storaged data
         */
        $this->addData(
            [
                'directory_id'  => null,
                'name'          => null,
                'path'          => null,
                'upload_time'   => null,
                'parent_id'     => null,
            ],
        );

        $this->_getResource()->loadByPath($this, $path);
        return $this;
    }

    /**
     * Check if there was errors during sync process
     *
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->_errors);
    }

    /**
     * Retrieve directory parent id
     *
     * @return string|null
     */
    public function getParentId()
    {
        $parentId = null;
        if (!$this->getData('parent_id')) {
            $parentId = $this->_getResource()->getParentId($this->getPath());
            if (empty($parentId)) {
                $parentId = null;
            }

            $this->setData('parent_id', $parentId);
        }

        return $parentId;
    }

    /**
     * Create directories recursively
     *
     * @param  string $path
     * @return Mage_Core_Model_File_Storage_Directory_Database
     */
    public function createRecursive($path)
    {
        $directory = Mage::getModel('core/file_storage_directory_database')->loadByPath($path);

        if (!$directory->getId()) {
            $dirName = basename($path);
            $dirPath = dirname($path);

            if ($dirPath != '.') {
                $parentDir = $this->createRecursive($dirPath);
                $parentId = $parentDir->getId();
            } else {
                $dirPath = '';
                $parentId = null;
            }

            $directory->setName($dirName);
            $directory->setPath($dirPath);
            $directory->setParentId($parentId);
            $directory->save();
        }

        return $directory;
    }

    /**
     * Export directories from storage
     *
     * @param  int $offset
     * @param  int $count
     * @return bool
     */
    public function exportDirectories($offset = 0, $count = 100)
    {
        $offset = max((int) $offset, 0);
        $count  = max((int) $count, 1);

        $result = $this->_getResource()->exportDirectories($offset, $count);

        if (empty($result)) {
            return false;
        }

        return $result;
    }

    /**
     * Import directories to storage
     *
     * @param  array $dirs
     * @return $this
     */
    public function importDirectories($dirs)
    {
        if (!is_array($dirs)) {
            return $this;
        }

        $dateSingleton = Mage::getSingleton('core/date');
        foreach ($dirs as $dir) {
            if (!is_array($dir) || !isset($dir['name']) || !strlen($dir['name'])) {
                continue;
            }

            try {
                $directory = Mage::getModel(
                    'core/file_storage_directory_database',
                    ['connection' => $this->getConnectionName()],
                );
                $directory->setPath($dir['path']);

                $parentId = $directory->getParentId();
                if ($parentId || $dir['path'] == '') {
                    $directory->setName($dir['name']);
                    $directory->setUploadTime($dateSingleton->date());
                    $directory->save();
                } else {
                    Mage::throwException(Mage::helper('core')->__('Parent directory does not exist: %s', $dir['path']));
                }
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        return $this;
    }

    /**
     * Clean directories at storage
     *
     * @return $this
     */
    public function clearDirectories()
    {
        $this->_getResource()->clearDirectories();
        return $this;
    }

    /**
     * Return subdirectories
     *
     * @param string $directory
     * @return mixed
     */
    public function getSubdirectories($directory)
    {
        $directory = Mage::helper('core/file_storage_database')->getMediaRelativePath($directory);

        return $this->_getResource()->getSubdirectories($directory);
    }

    /**
     * Delete directory from database
     *
     * @param string $dirPath
     * @return $this
     */
    public function deleteDirectory($dirPath)
    {
        $dirPath = Mage::helper('core/file_storage_database')->getMediaRelativePath($dirPath);
        $name = basename($dirPath);
        $path = dirname($dirPath);

        if ($path == '.') {
            $path = '';
        }

        $this->_getResource()->deleteDirectory($name, $path);

        return $this;
    }
}
