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
 * @package     Mage_Downloadable
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Downloadable Products File Helper
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Helper_File extends Mage_Core_Helper_Abstract
{
    /**
     * @see Mage_Uploader_Helper_File::getMimeTypes
     * @var array
     */
    protected $_mimeTypes;

    /**
     * @var Mage_Uploader_Helper_File
     */
    protected $_fileHelper;

    /**
     * Populate self::_mimeTypes array with values that set in config or pre-defined
     */
    public function __construct()
    {
        $this->_mimeTypes = $this->_getFileHelper()->getMimeTypes();
    }

    /**
     * @return Mage_Uploader_Helper_File
     */
    protected function _getFileHelper()
    {
        if (!$this->_fileHelper) {
            $this->_fileHelper = Mage::helper('uploader/file');
        }

        return $this->_fileHelper;
    }

    /**
     * Checking file for moving and move it
     *
     * @param string $baseTmpPath
     * @param string $basePath
     * @param array $file
     * @return string
     */
    public function moveFileFromTmp($baseTmpPath, $basePath, $file)
    {
        if (isset($file[0])) {
            $fileName = $file[0]['file'];
            if ($file[0]['status'] == 'new') {
                try {
                    $fileName = $this->_moveFileFromTmp(
                        $baseTmpPath, $basePath, $file[0]['file']
                    );
                } catch (Exception $e) {
                    Mage::throwException(Mage::helper('downloadable')->__('An error occurred while saving the file(s).'));
                }
            }
            return $fileName;
        }
        return '';
    }

    /**
     * Move file from tmp path to base path
     *
     * @param string $baseTmpPath
     * @param string $basePath
     * @param string $file
     * @return string
     */
    protected function _moveFileFromTmp($baseTmpPath, $basePath, $file)
    {
        $ioObject = new Varien_Io_File();
        $destDirectory = dirname($this->getFilePath($basePath, $file));
        try {
            $ioObject->open(array('path'=>$destDirectory));
        } catch (Exception $e) {
            $ioObject->mkdir($destDirectory, 0777, true);
            $ioObject->open(array('path'=>$destDirectory));
        }

        if (strrpos($file, '.tmp') == strlen($file)-4) {
            $file = substr($file, 0, strlen($file)-4);
        }

        $destFile = dirname($file) . $ioObject->dirsep()
                  . Mage_Core_Model_File_Uploader::getNewFileName($this->getFilePath($basePath, $file));

        Mage::helper('core/file_storage_database')->copyFile(
            $this->getFilePath($baseTmpPath, $file),
            $this->getFilePath($basePath, $destFile)
        );

        $result = $ioObject->mv(
            $this->getFilePath($baseTmpPath, $file),
            $this->getFilePath($basePath, $destFile)
        );
        return str_replace($ioObject->dirsep(), '/', $destFile);
    }

    /**
     * Return full path to file
     *
     * @param string $path
     * @param string $file
     * @return string
     */
    public function getFilePath($path, $file)
    {
        $file = $this->_prepareFileForPath($file);

        if(substr($file, 0, 1) == DS) {
            return $path . DS . substr($file, 1);
        }

        return $path . DS . $file;
    }

    /**
     * Replace slashes with directory separator
     *
     * @param string $file
     * @return string
     */
    protected function _prepareFileForPath($file)
    {
        return str_replace('/', DS, $file);
    }

    /**
     * Return file name form file path
     *
     * @param string $pathFile
     * @return string
     */
    public function getFileFromPathFile($pathFile)
    {
        $file = '';

        $file = substr($pathFile, strrpos($this->_prepareFileForPath($pathFile), DS)+1);

        return $file;
    }

    /**
     * Get MIME type for $filePath
     *
     * @param $filePath
     * @return string
     */
    public function getFileType($filePath)
    {
        $ext = substr($filePath, strrpos($filePath, '.')+1);
        return $this->_getFileTypeByExt($ext);
    }

    /**
     * Get MIME type by file extension
     *
     * @param $ext
     * @return string
     * @deprecated
     */
    protected function _getFileTypeByExt($ext)
    {
        return $this->_getFileHelper()->getMimeTypeByExtension($ext);
    }

    /**
     * Get all MIME types
     *
     * @return array
     */
    public function getAllFileTypes()
    {
        return array_values($this->getAllMineTypes());
    }

    /**
     * Get list of all MIME types
     *
     * @return array
     */
    public function getAllMineTypes()
    {
        return $this->_mimeTypes;
    }

}
