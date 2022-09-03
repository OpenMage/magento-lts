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
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Model for synchronization from DB to filesystem
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
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

    /** @var null|string[] */
    protected $_createdDirectories;

    /**
     * Files at storage
     *
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
     * @param string $dir
     * @return array
     */
    public function getStorageData($dir = '')
    {
        $files          = [];
        $directories    = [];
        $currentDir     = $this->getMediaBaseDirectory() . $dir;
        $ignoredFiles = array_merge(['.', '..'], $this->_getIgnoredFiles());

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
                        $directories[] = [
                            'name' => $file,
                            'path' => str_replace(DS, '/', ltrim($dir, DS))
                        ];

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

        return ['files' => $files, 'directories' => $directories];
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
        $ignoredFiles = array_merge(['.', '..'], $this->_getIgnoredFiles());

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
        if ($this->_ignoredFiles === null) {
            $ignored = (string)Mage::app()->getConfig()
                ->getNode(Mage_Core_Model_File_Storage::XML_PATH_MEDIA_RESOURCE_IGNORED);
            $this->_ignoredFiles = $ignored ? explode(',', $ignored) : [];
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
                $this->filePointer = null;
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
     * @param string $filePath
     * @return bool
     */
    public function lockCreateFile($filePath)
    {
        $filename = basename($filePath);
        $path = $this->getMediaBaseDirectory() . DS . str_replace('/', DS , dirname($filePath));

        // Create parent directories as needed and track so they can be cleaned up after
        if (!is_dir($path)) {
            $created = [];
            $parent = $path;
            while ($parent != $this->getMediaBaseDirectory() && !is_dir($parent)) {
                $created[] = $parent;
                $parent = dirname($parent);
            }
            if ($created) {
                $this->_createdDirectories = $created;
            }
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
     * @param string $filePath
     */
    public function removeLockedFile($filePath)
    {
        $filename = basename($filePath);
        $path = $this->getMediaBaseDirectory() . DS . str_replace('/', DS , dirname($filePath));
        $fullPath = $path . DS . $filename;
        if ($this->filePointer) {
            $fp = $this->filePointer;
            $this->filePointer = null;
            @flock($fp, LOCK_UN);
            @fclose($fp);
        }
        @unlink($fullPath);

        // Clean up empty directories created by this process when the file was locked
        if ($this->_createdDirectories) {
            foreach ($this->_createdDirectories as $directory) {
                @rmdir($directory); // Allowed to fail when the directory cannot be removed (non-empty)
            }
            $this->_createdDirectories = null;
        }

        // Clean up all empty directories
        if (rand() % 1000 === 0) {
            @exec("find {$this->getMediaBaseDirectory()} -empty -type d -delete"); // TODO - replace with native PHP?
        }
    }
}
