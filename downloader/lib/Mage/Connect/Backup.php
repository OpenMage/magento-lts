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
 * @package     Mage_Connect
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class to backup files before extension installation
 *
 * @category    Mage
 * @package     Mage_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Connect_Backup
{
    /**
     * Prefix for backuped files
     *
     * @var string
     */
    protected $_prefix = '_backup_';

    /**
     * Array of available to overwrite type of files
     *
     * @var array
     */
    protected $_fileTypes = array();

    /**
     * List of files to backup files
     *
     * @var array
     */
    protected $_fileList = array();

    /**
     * Get available file types for backup
     *
     * @return array
     */
    public function getFileTypes()
    {
        return $this->_fileTypes;
    }

    /**
     * Set available file types for backup
     *
     * @param array $types
     */
    public function setFileTypes(array $types)
    {
        foreach ($types as $type) {
            $this->_fileTypes[] = $type;
        }
    }

    /**
     * Add file to files list for backup
     *
     * @param string $file
     * @param string $rootPath
     * @return void
     */
    public function addFile($file, $rootPath)
    {
        $dest = $rootPath . DS . $file;
        $type = $this->getFileType($file);
        if (file_exists($dest) && in_array($type, $this->getFileTypes())) {
            $this->_fileList[] = $file;
        }
    }

    /**
     * Get count of files
     *
     * @return int
     */
    public function getFilesCount()
    {
        return count($this->_fileList);
    }

    /**
     * Clear list of files
     *
     * @return void
     */
    public function unsetAllFiles()
    {
        $this->_fileList = array();
    }

    /**
     * Get list of files
     *
     * @return array
     */
    public function getAllFiles()
    {
       return $this->_fileList;
    }

    /**
     * Run backup process
     *
     * @param boolean $cleanUpQueue
     * @return void
     */
    public function run($cleanUpQueue = false)
    {
        if ($this->getFilesCount() > 0) {
            $fileList = $this->getAllFiles();
            foreach($fileList as $file) {
                $this->_backupFile($file);
            }
            if ($cleanUpQueue) {
                $this->unsetAllFiles();
            }
        }
    }

    /**
     * Get File type
     *
     * @param string $file
     * @return string
     */
    public function getFileType($file)
    {
        return pathinfo($file, PATHINFO_EXTENSION);
    }

    /**
     * Backup file
     *
     * @param string $file
     * @return void
     */
    private function _backupFile($file)
    {
        $type = $this->getFileType($file);
        if ($type && $type != '') {
            $newName = $this->_prefix . time() . '.' . $type;
            @rename($file, str_replace('.' . $type, $newName, $file));
        }
    }
}
