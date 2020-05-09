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
 * @package     Mage_ImportExport
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Import entity product model
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Model_Import_Uploader extends Mage_Core_Model_File_Uploader
{
    protected $_tmpDir  = '';
    protected $_destDir = '';
    protected $_allowedMimeTypes = array(
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'png' => 'image/png'
    );
    const DEFAULT_FILE_TYPE = 'application/octet-stream';

    function __construct($filePath = null)
    {
        if (!is_null($filePath)) {
            $this->_setUploadFile($filePath);
        }
    }

    /**
     * Initiate uploader defoult settings
     */
    public function init()
    {
        $this->setAllowRenameFiles(true);
        $this->setAllowCreateFolders(true);
        $this->setFilesDispersion(true);
        $this->setAllowedExtensions(array_keys($this->_allowedMimeTypes));
        $this->addValidateCallback('catalog_product_image',
                Mage::helper('catalog/image'), 'validateUploadFile');
        $this->addValidateCallback(
            Mage_Core_Model_File_Validator_Image::NAME,
            Mage::getModel('core/file_validator_image'),
            'validate'
        );
        $this->_uploadType = self::SINGLE_STYLE;
    }

    /**
     * Proceed moving a file from TMP to destination folder
     *
     * @param string $fileName
     * @return array
     */
    public function move($fileName)
    {
        $filePath = realpath($this->getTmpDir() . DS . $fileName);
        $this->_setUploadFile($filePath);
        $result = $this->save($this->getDestDir());
        $result['name'] = self::getCorrectFileName($result['name']);
        return $result;
    }

    /**
     * Prepare information about the file for moving
     *
     * @param string $filePath
     */
    protected function _setUploadFile($filePath)
    {
        if (!is_readable($filePath)) {
            Mage::throwException("File '{$filePath}' was not found or has read restriction.");
        }
        $this->_file = $this->_readFileInfo($filePath);

        $this->_validateFile();
    }

    /**
     * Reads file info
     *
     * @param string $filePath
     * @return array
     */
    protected function _readFileInfo($filePath)
    {
        $fileInfo = pathinfo($filePath);

        return array(
            'name' => $fileInfo['basename'],
            'type' => $this->_getMimeTypeByExt($fileInfo['extension']),
            'tmp_name' => $filePath,
            'error' => 0,
            'size' => filesize($filePath)
        );
    }

    /**
     * Validate uploaded file by type and etc.
     */
    protected function _validateFile()
    {
        $filePath = $this->_file['tmp_name'];
        if (is_readable($filePath)) {
            $this->_fileExists = true;
        } else {
            $this->_fileExists = false;
        }

        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
        if (!$this->checkAllowedExtension($fileExtension)) {
            throw new Exception('Disallowed file type.');
        }
        //run validate callbacks
        foreach ($this->_validateCallbacks as $params) {
            if (is_object($params['object']) && method_exists($params['object'], $params['method'])) {
                $params['object']->{$params['method']}($filePath);
            }
        }
    }

    /**
     * Returns file MIME type by extension
     *
     * @param string $ext
     * @return string
     */
    protected function _getMimeTypeByExt($ext)
    {
        if (array_key_exists($ext, $this->_allowedMimeTypes)) {
            return $this->_allowedMimeTypes[$ext];
        }
        return '';
    }

    /**
     * Obtain TMP file path prefix
     *
     * @return string
     */
    public function getTmpDir()
    {
        return $this->_tmpDir;
    }

    /**
     * Set TMP file path prefix
     *
     * @param type $path
     * @return bool
     */
    public function setTmpDir($path)
    {
        if (is_string($path) && is_readable($path)) {
            $this->_tmpDir = $path;
            return true;
        }
        return false;
    }

    /**
     * Obtain destination file path prefix
     *
     * @return string
     */
    public function getDestDir()
    {
        return $this->_destDir;
    }

    /**
     * Set destination file path prefix
     *
     * @param type $path
     * @return bool
     */
    public function setDestDir($path)
    {
        if (is_string($path) && is_writable($path)) {
            $this->_destDir = $path;
            return true;
        }
        return false;
    }

    /**
     * Move files from TMP folder into destination folder
     *
     * @param string $tmpPath
     * @param string $destPath
     * @return bool
     */
    protected function _moveFile($tmpPath, $destPath)
    {
        $sourceFile = realpath($tmpPath);
        if ($sourceFile !== false) {
            return copy($sourceFile, $destPath);
        } else {
            return false;
        }
    }

}
