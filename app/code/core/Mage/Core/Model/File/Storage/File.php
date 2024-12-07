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
 * @copyright  Copyright (c) 2016-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract model class
 *
 * @category   Mage
 * @package    Mage_Core
 *
 * @method Mage_Core_Model_Resource_File_Storage_File _getResource()
 * @method Mage_Core_Model_Resource_File_Storage_File getResource()
 */
class Mage_Core_Model_File_Storage_File extends Mage_Core_Model_File_Storage_Abstract
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'core_file_storage_file';

    /**
     * Data at storage
     *
     * @var array|null
     */
    protected $_data = null;

    /**
     * Collect errors during sync process
     *
     * @var array
     */
    protected $_errors = [];

    /**
     * Class construct
     */
    public function __construct()
    {
        $this->_init('core/file_storage_file');
    }

    /**
     * Initialization
     *
     * @return $this
     */
    public function init()
    {
        return $this;
    }

    /**
     * Return storage name
     *
     * @return string
     */
    public function getStorageName()
    {
        return Mage::helper('core')->__('File system');
    }

    /**
     * Get files and directories from storage
     *
     * @return array
     */
    public function getStorageData()
    {
        return $this->_getResource()->getStorageData();
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
     * Clear files and directories in storage
     *
     * @return $this
     */
    public function clear()
    {
        $this->_getResource()->clear();
        return $this;
    }

    /**
     * Collect files and directories from storage
     *
     * @param  int $offset
     * @param  int $count
     * @param  string $type
     * @return array|bool
     */
    public function collectData($offset = 0, $count = 100, $type = 'files')
    {
        if (!in_array($type, ['files', 'directories'])) {
            return false;
        }

        $offset = ((int) $offset >= 0) ? (int) $offset : 0;
        $count  = ((int) $count >= 1) ? (int) $count : 1;

        if (is_null($this->_data)) {
            $this->_data = $this->getStorageData();
        }

        $slice = array_slice($this->_data[$type], $offset, $count);
        if (empty($slice)) {
            return false;
        }

        return $slice;
    }

    /**
     * Export directories list from storage
     *
     * @param  int $offset
     * @param  int $count
     * @return array|bool
     */
    public function exportDirectories($offset = 0, $count = 100)
    {
        return $this->collectData($offset, $count, 'directories');
    }

    /**
     * Export files list in defined range
     *
     * @param  int $offset
     * @param  int $count
     * @return array|bool
     */
    public function exportFiles($offset = 0, $count = 1)
    {
        $slice = $this->collectData($offset, $count, 'files');

        if (!$slice) {
            return false;
        }

        $result = [];
        foreach ($slice as $fileName) {
            try {
                $fileInfo = $this->collectFileInfo($fileName);
            } catch (Exception $e) {
                Mage::logException($e);
                continue;
            }

            $result[] = $fileInfo;
        }

        return $result;
    }

    /**
     * Import entities to storage
     *
     * @param  array $data
     * @param  string $callback
     * @return $this
     */
    public function import($data, $callback)
    {
        if (!is_array($data) || !method_exists($this, $callback)) {
            return $this;
        }

        foreach ($data as $part) {
            try {
                $this->$callback($part);
            } catch (Exception $e) {
                $this->_errors[] = $e->getMessage();
                Mage::logException($e);
            }
        }

        return $this;
    }

    /**
     * Import directories to storage
     *
     * @param  array $dirs
     * @return $this
     */
    public function importDirectories($dirs)
    {
        return $this->import($dirs, 'saveDir');
    }

    /**
     * Import files list
     *
     * @param  array $files
     * @return $this
     */
    public function importFiles($files)
    {
        return $this->import($files, 'saveFile');
    }

    /**
     * Save directory to storage
     *
     * @param  array $dir
     * @return bool
     */
    public function saveDir($dir)
    {
        return $this->_getResource()->saveDir($dir);
    }

    /**
     * Save file to storage
     *
     * @param  array|Mage_Core_Model_File_Storage_Database $file
     * @param  bool $overwrite
     * @return bool
     */
    public function saveFile($file, $overwrite = true)
    {
        if (isset($file['filename']) && !empty($file['filename'])
            && isset($file['content'])
        ) {
            try {
                $filename = (isset($file['directory']) && !empty($file['directory']))
                    ? $file['directory'] . DS . $file['filename']
                    : $file['filename'];

                return $this->_getResource()
                    ->saveFile($filename, $file['content'], $overwrite);
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::throwException(Mage::helper('core')->__('Unable to save file "%s" at "%s"', $file['filename'], $file['directory']));
            }
        } else {
            Mage::throwException(Mage::helper('core')->__('Wrong file info format'));
        }
    }

    /**
     * @param string $filePath
     * @return bool
     */
    public function lockCreateFile($filePath)
    {
        return $this->getResource()->lockCreateFile($filePath);
    }

    /**
     * @param string $filePath
     */
    public function removeLockedFile($filePath)
    {
        $this->getResource()->removeLockedFile($filePath);
    }
}
