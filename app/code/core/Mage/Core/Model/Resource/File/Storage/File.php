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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Model for synchronization from DB to filesystem
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Resource_File_Storage_File
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_mediaBaseDirectory = null;

    /**
     * List of files/directories that should be ignored when cleaning and reading files from the filesystem
     * @var array
     */
    protected $_ignoredFiles;

    /** @var resource */
    protected $filePointer;

    /**
     * Files at storage
     *
     * @var array
     * @return string
     */
    public function getMediaBaseDirectory()
    {
        if (is_null($this->_mediaBaseDirectory)) {
            $this->_mediaBaseDirectory = Mage::helper('core/file_storage_database')->getMediaBaseDir();
        }

        return $this->_mediaBaseDirectory;
    }

    /**
     * Collect files and directories recursively
     *
     * @param  string$dir
     * @return array
     */
    public function getStorageData($dir = '')
    {
        $files          = array();
        $directories    = array();
        $currentDir     = $this->getMediaBaseDirectory() . $dir;
        $ignoredFiles = array_merge(array('.', '..'), $this->_getIgnoredFiles());

        if (is_dir($currentDir)) {
            $dh = opendir($currentDir);
            if ($dh) {
                while (($file = readdir($dh)) !== false) {
                    if (in_array($file, $ignoredFiles)) {
                        continue;
                    }

                    $fullPath = $currentDir . DS . $file;
                    $relativePath = $dir . DS . $file;
                    if (is_dir($fullPath)) {
                        $directories[] = array(
                            'name' => $file,
                            'path' => str_replace(DS, '/', ltrim($dir, DS))
                        );

                        $data = $this->getStorageData($relativePath);
                        $directories = array_merge($directories, $data['directories']);
                        $files = array_merge($files, $data['files']);
                    } else {
                        $files[] = $relativePath;
                    }
                }
                closedir($dh);
            }
        }

        return array('files' => $files, 'directories' => $directories);
    }

    /**
     * Clear files and directories in storage
     *
     * @param  string $dir
     * @return $this
     */
    public function clear($dir = '')
    {
        $currentDir = $this->getMediaBaseDirectory() . $dir;
        $ignoredFiles = array_merge(array('.', '..'), $this->_getIgnoredFiles());

        if (is_dir($currentDir)) {
            $dh = opendir($currentDir);
            if ($dh) {
                while (($file = readdir($dh)) !== false) {
                    if (in_array($file, $ignoredFiles)) {
                        continue;
                    }

                    $fullPath = $currentDir . DS . $file;
                    if (is_dir($fullPath)) {
                        $this->clear($dir . DS . $file);
                    } else {
                        @unlink($fullPath);
                    }
                }
                closedir($dh);
                @rmdir($currentDir);
            }
        }

        return $this;
    }

    /**
     * Returns list of files/directories that should be ignored when cleaning and reading files from the filesystem
     * @return array
     */
    protected function _getIgnoredFiles()
    {
        if (null === $this->_ignoredFiles) {
            $ignored = (string)Mage::app()->getConfig()
                ->getNode(Mage_Core_Model_File_Storage::XML_PATH_MEDIA_RESOURCE_IGNORED);
            $this->_ignoredFiles = $ignored ? explode(',', $ignored) : array();
        }
        return $this->_ignoredFiles;
    }

    /**
     * Save directory to storage
     *
     * @param  array $dir
     * @return bool
     */
    public function saveDir($dir)
    {
        if (!isset($dir['name']) || !strlen($dir['name'])
            || !isset($dir['path'])
        ) {
            return false;
        }

        $path = (strlen($dir['path']))
            ? $dir['path'] . DS . $dir['name']
            : $dir['name'];
        $path = Mage::helper('core/file_storage_database')->getMediaBaseDir() . DS . str_replace('/', DS, $path);

        if (!file_exists($path) || !is_dir($path)) {
            if (!@mkdir($path, 0777, true)) {
                Mage::throwException(Mage::helper('core')->__('Unable to create directory: %s', $path));
            }
        }

        return true;
    }

    /**
     * Save file to storage
     *
     * @param  string $filePath
     * @param  string $content
     * @param  bool $overwrite
     * @return bool true if file written, otherwise false
     * @throws Mage_Core_Exception
     */
    public function saveFile($filePath, $content, $overwrite = false)
    {
        $filename = basename($filePath);
        $path = $this->getMediaBaseDirectory() . DS . str_replace('/', DS, dirname($filePath));

        if (!is_dir($path)) {
            @mkdir($path, 0777, true);
        }

        $fullPath = $path . DS . $filename;
        if ($this->filePointer || !file_exists($fullPath) || $overwrite) {
            // If we already opened the file using lockCreateFile method
            if ($this->filePointer) {
                $fp = $this->filePointer;
                $this->filePointer = NULL;
                if (@fwrite($fp, $content) !== false && @fflush($fp) && @flock($fp, LOCK_UN) && @fclose($fp)) {
                    return true;
                }
            }
            // If overwrite is not required then return if file could not be locked (assume it is being written by another process)
            // Exception is only thrown if file was opened but could not be written.
            else if (!$overwrite) {
                if (!($fp = @fopen($fullPath, 'x'))) {
                    return false;
                }
                if (@fwrite($fp, $content) !== false && @fflush($fp) && @fclose($fp)) {
                    return true;
                }
            } // If overwrite is required, throw exception on failure to write file
            elseif (@file_put_contents($fullPath, $content, LOCK_EX) !== false) {
                return true;
            }

            Mage::throwException(Mage::helper('core')->__('Unable to save file: %s', $filePath));
        }

        return false;
    }

    /**
     * Create a new file already locked by this process and save the handle for later writing by saveFile method.
     *
     * @param $filePath
     * @return bool
     */
    public function lockCreateFile($filePath)
    {
        $filename = basename($filePath);
        $path = $this->getMediaBaseDirectory() . DS . str_replace('/', DS ,dirname($filePath));

        if (!is_dir($path)) {
            @mkdir($path, 0777, true);
        }

        $fullPath = $path . DS . $filename;

        // Get exclusive lock on new or existing file
        if ($fp = @fopen($fullPath, 'c')) {
            @flock($fp, LOCK_EX);
            @fseek($fp, 0, SEEK_END);
            if (@ftell($fp) === 0) { // If the file is empty we can write to it
                $this->filePointer = $fp;
                return true;
            } else { // Otherwise we should not write to it
                @flock($fp, LOCK_UN);
                @fclose($fp);
                return false;
            }
        }

        return false;
    }

    /**
     * Unlock, close and remove a locked file (in case the file could not be read from remote storage)
     *
     * @param $filePath
     */
    public function removeLockedFile($filePath)
    {
        $filename = basename($filePath);
        $path = $this->getMediaBaseDirectory() . DS . str_replace('/', DS ,dirname($filePath));
        $fullPath = $path . DS . $filename;
        if ($this->filePointer) {
            $fp = $this->filePointer;
            $this->filePointer = NULL;
            @flock($fp, LOCK_UN);
            @fclose($fp);
        }
        @unlink($fullPath);
    }

}
