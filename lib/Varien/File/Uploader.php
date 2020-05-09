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
 * @category    Varien
 * @package     Varien_File
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * File upload class
 *
 * ATTENTION! This class must be used like abstract class and must added
 * validation by protected file extension list to extended class
 *
 * @category   Varien
 * @package    Varien_File
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Varien_File_Uploader
{
    /**
     * Uploaded file handle (copy of $_FILES[] element)
     *
     * @var array
     * @access protected
     */
    protected $_file;

    /**
     * Uploaded file mime type
     *
     * @var string
     * @access protected
     */
    protected $_fileMimeType;

    /**
     * Upload type. Used to right handle $_FILES array.
     *
     * @var string Varien_File_Uploader::SINGLE_STYLE|Varien_File_Uploader::MULTIPLE_STYLE
     * @access protected
     */
    protected $_uploadType;

    /**
     * The name of uploaded file. By default it is original file name, but when
     * we will change file name, this variable will be changed too.
     *
     * @var string
     * @access protected
     */
    protected $_uploadedFileName;

    /**
     * The name of destination directory
     *
     * @var string
     * @access protected
     */
    protected $_uploadedFileDir;

    /**
     * If this variable is set to TRUE, our library will be able to automaticaly create
     * non-existant directories.
     *
     * @var bool
     * @access protected
     */
    protected $_allowCreateFolders = true;

    /**
     * If this variable is set to TRUE, uploaded file name will be changed if some file with the same
     * name already exists in the destination directory (if enabled).
     *
     * @var bool
     * @access protected
     */
    protected $_allowRenameFiles = false;

    /**
     * If this variable is set to TRUE, files despersion will be supported.
     *
     * @var bool
     * @access protected
     */
    protected $_enableFilesDispersion = false;

    /**
     * This variable is used both with $_enableFilesDispersion == true
     * It helps to avoid problems after migrating from case-insensitive file system to case-insensitive
     * (e.g. NTFS->ext or ext->NTFS)
     *
     * @var bool
     * @access protected
     */
    protected $_caseInsensitiveFilenames = true;

    /**
     * @var string
     * @access protected
     */
    protected $_dispretionPath = null;

    protected $_fileExists = false;

    protected $_allowedExtensions = null;

    /**
     * List of valid MIME-Types.
     *
     * @var array
     */
    protected $_validMimeTypes = array();

    /**
     * Validate callbacks storage
     *
     * @var array
     * @access protected
     */
    protected $_validateCallbacks = array();

    const SINGLE_STYLE = 0;
    const MULTIPLE_STYLE = 1;
    const TMP_NAME_EMPTY = 666;

    /**
     * Resulting of uploaded file
     *
     * @var array|false     Array with file info keys: path, file. Result is
     *                      FALSE when file not uploaded
     */
    protected $_result;

    function __construct($fileId)
    {
        $this->_setUploadFileId($fileId);
        if(!file_exists($this->_file['tmp_name'])) {
            $code = empty($this->_file['tmp_name']) ? self::TMP_NAME_EMPTY : 0;
            throw new Exception('File was not uploaded.', $code);
        } else {
            $this->_fileExists = true;
        }
    }

    /**
     * After save logic
     *
     * @param  array $result
     * @return Varien_File_Uploader
     */
    protected function _afterSave($result)
    {
        return $this;
    }

    /**
     * Used to save uploaded file into destination folder with
     * original or new file name (if specified)
     *
     * @param string $destinationFolder
     * @param string $newFileName
     * @access public
     * @return array|false
     */
    public function save($destinationFolder, $newFileName = null)
    {
        $this->_validateFile();

        if ($this->_allowCreateFolders) {
            $this->_createDestinationFolder($destinationFolder);
        }

        if (!is_writable($destinationFolder)) {
            throw new Exception('Destination folder is not writable or does not exists.');
        }

        $this->_result = false;

        $destinationFile = $destinationFolder;
        $fileName = isset($newFileName) ? $newFileName : $this->_file['name'];
        $fileName = self::getCorrectFileName($fileName);
        if ($this->_enableFilesDispersion) {
            $fileName = $this->correctFileNameCase($fileName);
            $this->setAllowCreateFolders(true);
            $this->_dispretionPath = self::getDispretionPath($fileName);
            $destinationFile.= $this->_dispretionPath;
            $this->_createDestinationFolder($destinationFile);
        }

        if ($this->_allowRenameFiles) {
            $fileName = self::getNewFileName(self::_addDirSeparator($destinationFile) . $fileName);
        }

        $destinationFile = self::_addDirSeparator($destinationFile) . $fileName;

        $this->_result = $this->_moveFile($this->_file['tmp_name'], $destinationFile);

        if ($this->_result) {
            chmod($destinationFile, 0666);
            if ($this->_enableFilesDispersion) {
                $fileName = str_replace(DIRECTORY_SEPARATOR, '/',
                    self::_addDirSeparator($this->_dispretionPath)) . $fileName;
            }
            $this->_uploadedFileName = $fileName;
            $this->_uploadedFileDir = $destinationFolder;
            $this->_result = $this->_file;
            $this->_result['path'] = $destinationFolder;
            $this->_result['file'] = $fileName;

            $this->_afterSave($this->_result);
        }

        return $this->_result;
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
        return move_uploaded_file($tmpPath, $destPath);
    }

    /**
     * Validate file before save
     *
     * @access public
     */
    protected function _validateFile()
    {
        if ($this->_fileExists === false) {
            return;
        }

        //is file extension allowed
        if (!$this->checkAllowedExtension($this->getFileExtension())) {
            throw new Exception('Disallowed file type.');
        }

        /*
         * Validate MIME-Types.
         */
        if (!$this->checkMimeType($this->_validMimeTypes)) {
            throw new Exception('Invalid MIME type.');
        }

        //run validate callbacks
        foreach ($this->_validateCallbacks as $params) {
            if (is_object($params['object']) && method_exists($params['object'], $params['method'])) {
                $params['object']->{$params['method']}($this->_file['tmp_name']);
            }
        }
    }

    /**
     * Returns extension of the uploaded file
     *
     * @return string
     */
    public function getFileExtension()
    {
        return $this->_fileExists ? pathinfo($this->_file['name'], PATHINFO_EXTENSION) : '';
    }

    /**
     * Add validation callback model for us in self::_validateFile()
     *
     * @param string $callbackName
     * @param object $callbackObject
     * @param string $callbackMethod    Method name of $callbackObject. It must
     *                                  have interface (string $tmpFilePath)
     * @return Varien_File_Uploader
     */
    public function addValidateCallback($callbackName, $callbackObject, $callbackMethod)
    {
        $this->_validateCallbacks[$callbackName] = array(
           'object' => $callbackObject,
           'method' => $callbackMethod
        );
        return $this;
    }

    /**
     * Delete validation callback model for us in self::_validateFile()
     *
     * @param string $callbackName
     * @access public
     * @return Varien_File_Uploader
     */
    public function removeValidateCallback($callbackName)
    {
        if (isset($this->_validateCallbacks[$callbackName])) {
            unset($this->_validateCallbacks[$callbackName]);
        }
        return $this;
    }

    /**
     * Correct filename with special chars and spaces
     *
     * @param string $fileName
     * @return string
     */
    static public function getCorrectFileName($fileName)
    {
        $fileName = preg_replace('/[^a-z0-9_\\-\\.]+/i', '_', $fileName);
        $fileInfo = pathinfo($fileName);

        if (preg_match('/^_+$/', $fileInfo['filename'])) {
            $fileName = 'file.' . $fileInfo['extension'];
        }
        return $fileName;
    }

    /**
     * Convert filename to lowercase in case of case-insensitive file names
     *
     * @param string
     * @return string
     */
    public function correctFileNameCase($fileName)
    {
        if ($this->_caseInsensitiveFilenames) {
            return strtolower($fileName);
        }
        return $fileName;
    }

    static protected function _addDirSeparator($dir)
    {
        if (substr($dir,-1) != DIRECTORY_SEPARATOR) {
            $dir.= DIRECTORY_SEPARATOR;
        }
        return $dir;
    }

    /**
     * Used to check if uploaded file mime type is valid or not
     *
     * @param array $validTypes
     * @access public
     * @return bool
     */
    public function checkMimeType($validTypes = array())
    {
        try {
            if (count($validTypes) > 0) {
                $validator = new Zend_Validate_File_MimeType($validTypes);
                return $validator->isValid($this->_file['tmp_name']);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Returns a name of uploaded file
     *
     * @access public
     * @return string
     */
    public function getUploadedFileName()
    {
        return $this->_uploadedFileName;
    }

    /**
     * Used to set {@link _allowCreateFolders} value
     *
     * @param mixed $flag
     * @access public
     * @return Varien_File_Uploader
     */
    public function setAllowCreateFolders($flag)
    {
        $this->_allowCreateFolders = $flag;
        return $this;
    }

    /**
     * Used to set {@link _allowRenameFiles} value
     *
     * @param mixed $flag
     * @access public
     * @return Varien_File_Uploader
     */
    public function setAllowRenameFiles($flag)
    {
        $this->_allowRenameFiles = $flag;
        return $this;
    }

    /**
     * Used to set {@link _enableFilesDispersion} value
     *
     * @param mixed $flag
     * @access public
     * @return Varien_File_Uploader
     */
    public function setFilesDispersion($flag)
    {
        $this->_enableFilesDispersion = $flag;
        return $this;
    }

    /**
     * Filenames Case-sensitivity  setter
     *
     * @param mixed $flag
     * @return Varien_File_Uploader
     */
    public function setFilenamesCaseSensitivity($flag)
    {
        $this->_caseInsensitiveFilenames = $flag;
        return $this;
    }

    public function setAllowedExtensions($extensions = array())
    {
        foreach ((array)$extensions as $extension) {
            $this->_allowedExtensions[] = strtolower($extension);
        }
        return $this;
    }

    /**
     * Set valid MIME-types.
     *
     * @param array $mimeTypes
     * @return Varien_File_Uploader
     */
    public function setValidMimeTypes($mimeTypes = array())
    {
        $this->_validMimeTypes = array();
        foreach ((array) $mimeTypes as $mimeType) {
            $this->_validMimeTypes[] = $mimeType;
        }
        return $this;
    }

    /**
     * Check if specified extension is allowed
     *
     * @param string $extension
     * @return boolean
     */
    public function checkAllowedExtension($extension)
    {
        if (!is_array($this->_allowedExtensions) || empty($this->_allowedExtensions)) {
            return true;
        }

        return in_array(strtolower($extension), $this->_allowedExtensions);
    }

    /**
     * @deprecated after 1.5.0.0-beta2
     *
     * @param string $extension
     * @return boolean
     */
    public function chechAllowedExtension($extension)
    {
        return $this->checkAllowedExtension($extension);
    }

    private function _getMimeType()
    {
        return $this->_file['type'];
    }

    private function _setUploadFileId($fileId)
    {
        if (empty($_FILES)) {
            throw new Exception('$_FILES array is empty', self::TMP_NAME_EMPTY);
        }

        if (is_array($fileId)) {
            $this->_uploadType = self::MULTIPLE_STYLE;
            $this->_file = $fileId;
        } else {
            preg_match("/^(.*?)\[(.*?)\]$/", $fileId, $file);

            if (count($file) > 0 && (count($file[0]) > 0) && (count($file[1]) > 0)) {
                array_shift($file);
                $this->_uploadType = self::MULTIPLE_STYLE;

                $fileAttributes = $_FILES[$file[0]];
                $tmp_var = array();

                foreach ($fileAttributes as $attributeName => $attributeValue) {
                    $tmp_var[$attributeName] = $attributeValue[$file[1]];
                }

                $fileAttributes = $tmp_var;
                $this->_file = $fileAttributes;
            } elseif(!empty($fileId) && isset($_FILES[$fileId])) {
                $this->_uploadType = self::SINGLE_STYLE;
                $this->_file = $_FILES[$fileId];
            } elseif( $fileId == '' ) {
                throw new Exception('Invalid parameter given. A valid $_FILES[] identifier is expected.');
            }
        }
    }

    private function _createDestinationFolder($destinationFolder)
    {
        if (!$destinationFolder) {
            return $this;
        }

        if (substr($destinationFolder, -1) == DIRECTORY_SEPARATOR) {
            $destinationFolder = substr($destinationFolder, 0, -1);
        }

        if (!(@is_dir($destinationFolder) || @mkdir($destinationFolder, 0777, true))) {
            throw new Exception("Unable to create directory '{$destinationFolder}'.");
        }
        return $this;
    }

    static public function getNewFileName($destFile)
    {
        $fileInfo = pathinfo($destFile);
        if (file_exists($destFile)) {
            $index = 1;
            $baseName = $fileInfo['filename'] . '.' . $fileInfo['extension'];
            while( file_exists($fileInfo['dirname'] . DIRECTORY_SEPARATOR . $baseName) ) {
                $baseName = $fileInfo['filename']. '_' . $index . '.' . $fileInfo['extension'];
                $index ++;
            }
            $destFileName = $baseName;
        } else {
            return $fileInfo['basename'];
        }

        return $destFileName;
    }

    static public function getDispretionPath($fileName)
    {
        $char = 0;
        $dispretionPath = '';
        while (($char < 2) && ($char < strlen($fileName))) {
            if (empty($dispretionPath)) {
                $dispretionPath = DIRECTORY_SEPARATOR
                    . ('.' == $fileName[$char] ? '_' : $fileName[$char]);
            } else {
                $dispretionPath = self::_addDirSeparator($dispretionPath)
                      . ('.' == $fileName[$char] ? '_' : $fileName[$char]);
            }
            $char ++;
        }
        return $dispretionPath;
    }
}
