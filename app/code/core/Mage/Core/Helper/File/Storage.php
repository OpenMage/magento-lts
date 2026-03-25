<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * File storage helper
 *
 * @package    Mage_Core
 */
class Mage_Core_Helper_File_Storage extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Core';

    /**
     * Current storage code
     *
     * @var null|int
     */
    protected $_currentStorage = null;

    /**
     * List of internal storages
     *
     * @var array
     */
    protected $_internalStorageList = [
        Mage_Core_Model_File_Storage::STORAGE_MEDIA_FILE_SYSTEM,
    ];

    /**
     * Return saved storage code
     *
     * @return int
     */
    public function getCurrentStorageCode()
    {
        if (is_null($this->_currentStorage)) {
            $this->_currentStorage = (int) Mage::app()
                ->getConfig()->getNode(Mage_Core_Model_File_Storage::XML_PATH_STORAGE_MEDIA);
        }

        return $this->_currentStorage;
    }

    /**
     * Retrieve file system storage model
     *
     * @return Mage_Core_Model_File_Storage_File
     */
    public function getStorageFileModel()
    {
        return Mage::getSingleton('core/file_storage_file');
    }

    /**
     * Check if storage is internal
     *
     * @param  null|int $storage
     * @return bool
     */
    public function isInternalStorage($storage = null)
    {
        $storage = (is_null($storage)) ? $this->getCurrentStorageCode() : (int) $storage;

        return in_array($storage, $this->_internalStorageList);
    }

    /**
     * Retrieve storage model
     *
     * @param  null|int                                                                $storage
     * @param  array                                                                   $params
     * @return Mage_Core_Model_File_Storage_Database|Mage_Core_Model_File_Storage_File
     */
    public function getStorageModel($storage = null, $params = [])
    {
        return Mage::getSingleton('core/file_storage')->getStorageModel($storage, $params);
    }

    /**
     * Check if needed to copy file from storage to file system and
     * if file exists in the storage
     *
     * @param  string   $filename
     * @return bool|int
     */
    public function processStorageFile($filename)
    {
        if ($this->isInternalStorage()) {
            return false;
        }

        $dbHelper = Mage::helper('core/file_storage_database');

        $relativePath = $dbHelper->getMediaRelativePath($filename);
        $file = $this->getStorageModel()->loadByFilename($relativePath);

        if (!$file->getId()) {
            return false;
        }

        return $this->saveFileToFileSystem($file);
    }

    /**
     * Save file to file system
     *
     * @param  Mage_Core_Model_File_Storage_Database $file
     * @return bool|int
     */
    public function saveFileToFileSystem($file)
    {
        return $this->getStorageFileModel()->saveFile($file, true);
    }
}
