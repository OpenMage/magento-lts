<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

/**
 * Downloadable Products File Helper
 *
 * @package    Mage_Downloadable
 */
class Mage_Downloadable_Helper_File extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Downloadable';

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
                        $baseTmpPath,
                        $basePath,
                        $file[0]['file'],
                    );
                } catch (Exception) {
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
            $ioObject->open(['path' => $destDirectory]);
        } catch (Exception) {
            $ioObject->mkdir($destDirectory, 0777, true);
            $ioObject->open(['path' => $destDirectory]);
        }

        if (strrpos($file, '.tmp') == strlen($file) - 4) {
            $file = substr($file, 0, -4);
        }

        $destFile = dirname($file) . $ioObject->dirsep()
                  . Mage_Core_Model_File_Uploader::getNewFileName($this->getFilePath($basePath, $file));

        Mage::helper('core/file_storage_database')->copyFile(
            $this->getFilePath($baseTmpPath, $file),
            $this->getFilePath($basePath, $destFile),
        );

        $result = $ioObject->mv(
            $this->getFilePath($baseTmpPath, $file),
            $this->getFilePath($basePath, $destFile),
        );
        return str_replace($ioObject->dirsep(), '/', $destFile);
    }

    /**
     * Return full path to file
     *
     * @param string $path
     * @param null|string $file
     * @return string
     */
    public function getFilePath($path, $file)
    {
        if ($file === null || $file === '') {
            return $path . DS;
        }

        $file = $this->_prepareFileForPath($file);

        if (substr($file, 0, 1) == DS) {
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

        return substr($pathFile, strrpos($this->_prepareFileForPath($pathFile), DS) + 1);
    }

    /**
     * Get MIME type for $filePath
     *
     * @param string $filePath
     * @return string
     */
    public function getFileType($filePath)
    {
        $ext = substr($filePath, strrpos($filePath, '.') + 1);
        return $this->_getFileTypeByExt($ext);
    }

    /**
     * Get MIME type by file extension
     *
     * @param string $ext
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
