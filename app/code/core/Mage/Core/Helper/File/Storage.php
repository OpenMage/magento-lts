<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * File storage helper
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Helper_File_Storage extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Core';

    /**
     * Current storage code
     *
     * @var int|null
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
     * @param  int|null $storage
     * @return bool
     */
    public function isInternalStorage($storage = null)
    {
        $storage = (!is_null($storage)) ? (int) $storage : $this->getCurrentStorageCode();

        return in_array($storage, $this->_internalStorageList);
    }

    /**
     * Retrieve storage model
     *
     * @param  int|null $storage
     * @param  array $params
     * @return Mage_Core_Model_File_Storage_File|Mage_Core_Model_File_Storage_Database
     */
    public function getStorageModel($storage = null, $params = [])
    {
        return Mage::getSingleton('core/file_storage')->getStorageModel($storage, $params);
    }

    /**
     * Check if needed to copy file from storage to file system and
     * if file exists in the storage
     *
     * @param  string $filename
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
