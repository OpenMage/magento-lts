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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Varien
 * @package    Varien_File
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * File upload class
 *
 * @category   Varien
 * @package    Varien_File
 * @author      Magento Core Team <core@magentocommerce.com>
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
     * @var Varien_File_Uploader::SINGLE_STYLE|Varien_File_Uploader::MULTIPLE_STYLE
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

    const SINGLE_STYLE = 0;
    const MULTIPLE_STYLE = 1;

    function __construct($fileId)
    {
        $this->_setUploadFileId($fileId);
        if( !file_exists($this->_file['tmp_name']) ) {
            throw new Exception('File was not uploaded.');
            return;
        } else {
            $this->_fileExists = true;
        }
    }

    /**
     * Used to save uploaded file into destination folder with
     * original or new file name (if specified)
     *
     * @param string $destinationFolder
     * @param string $newFileName
     * @access public
     * @return void|bool
     */
    public function save($destinationFolder, $newFileName=null)
    {
        if( $this->_fileExists === false ) {
            return;
        }

        if( $this->_allowCreateFolders ) {
            $this->_createDestinationFolder($destinationFolder);
        }

        if( !is_writable($destinationFolder) ) {
            throw new Exception('Destination folder is not writable or does not exists.');
        }

        $result = false;

        $destFile = $destinationFolder;
        $fileName = ( isset($newFileName) ) ? $newFileName : self::getCorrectFileName($this->_file['name']);
        $fileExtension = substr($fileName, strrpos($fileName, '.')+1);

        if( !$this->chechAllowedExtension($fileExtension) ) {
            throw new Exception('Disallowed file type.');
        }

        if( $this->_enableFilesDispersion ) {
            $fileName = $this->correctFileNameCase($fileName);
            $this->setAllowCreateFolders(true);
            $this->_dispretionPath = self::getDispretionPath($fileName);
            $destFile.= $this->_dispretionPath;
            $this->_createDestinationFolder($destFile);
        }

        if( $this->_allowRenameFiles ) {
            $fileName = self::getNewFileName(self::_addDirSeparator($destFile).$fileName);
        }

        $destFile = self::_addDirSeparator($destFile) . $fileName;

        $result = move_uploaded_file($this->_file['tmp_name'], $destFile);

        if( $result ) {
            chmod($destFile, 0777);
            if ( $this->_enableFilesDispersion ) {
                $fileName = str_replace(DIRECTORY_SEPARATOR, '/', self::_addDirSeparator($this->_dispretionPath)) . $fileName;
            }
            $this->_uploadedFileName = $fileName;
            $this->_uploadedFileDir = $destinationFolder;
            $result = $this->_file;
            $result['path'] = $destinationFolder;
            $result['file'] = $fileName;
            return $result;
        } else {
            return $result;
        }
    }

    static public function getCorrectFileName($fileName)
    {
        if (preg_match('/[^a-z0-9_\\-\\.]/i', $fileName)) {
            $fileName = 'file' . substr($fileName, strrpos($fileName, '.'));
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
    public function checkMimeType($validTypes=Array())
    {
        if( count($validTypes) > 0 ) {
            if( !in_array($this->_getMimeType(), $validTypes) ) {
                return false;
            }
        }
        return true;
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

    public function setAllowedExtensions($extensions=array())
    {
        foreach ((array)$extensions as $extension) {
            $this->_allowedExtensions[] = strtolower($extension);
        }
        return $this;
    }

    public function chechAllowedExtension($extension)
    {
        if (is_null($this->_allowedExtensions)) {
            return true;
        }
        elseif (in_array(strtolower($extension), $this->_allowedExtensions)) {
            return true;
        }
        return false;
    }

    private function _getMimeType()
    {
        return $this->_file['type'];
    }

    private function _setUploadFileId($fileId)
    {
        if (empty($_FILES)) {
            throw new Exception('$_FILES array is empty');
        }

        if (is_array($fileId)) {
            $this->_uploadType = self::MULTIPLE_STYLE;
            $this->_file = $fileId;
        } else {
            preg_match("/^(.*?)\[(.*?)\]$/", $fileId, $file);

            if( count($file) > 0 && (count($file[0]) > 0) && (count($file[1]) > 0) ) {
                array_shift($file);
                $this->_uploadType = self::MULTIPLE_STYLE;

                $fileAttributes = $_FILES[$file[0]];
                $tmp_var = array();

                foreach( $fileAttributes as $attributeName => $attributeValue ) {
                    $tmp_var[$attributeName] = $attributeValue[$file[1]];
                }

                $fileAttributes = $tmp_var;
                $this->_file = $fileAttributes;
            } elseif( count($fileId) > 0 && isset($_FILES[$fileId])) {
                $this->_uploadType = self::SINGLE_STYLE;
                $this->_file = $_FILES[$fileId];
            } elseif( $fileId == '' ) {
                throw new Exception('Invalid parameter given. A valid $_FILES[] identifier is expected.');
            }
        }
    }

    private function _createDestinationFolder($destinationFolder)
    {
        if( !$destinationFolder ) {
            return $this;
        }

        if (substr($destinationFolder, -1) == DIRECTORY_SEPARATOR) {
            $destinationFolder = substr($destinationFolder, 0, -1);
        }

        if (!(@is_dir($destinationFolder) || @mkdir($destinationFolder, 0777, true))) {
            throw new Exception("Unable to create directory '{$destinationFolder}'.");
        }
        return $this;

        $destinationFolder = str_replace('/', DIRECTORY_SEPARATOR, $destinationFolder);
        $path = explode(DIRECTORY_SEPARATOR, $destinationFolder);
        $newPath = null;
        $oldPath = null;
        foreach( $path as $key => $directory ) {
            if (trim($directory)=='') {
                continue;
            }
            if (strlen($directory)===2 && $directory{1}===':') {
                $newPath = $directory;
                continue;
            }
            $newPath.= ( $newPath != DIRECTORY_SEPARATOR ) ? DIRECTORY_SEPARATOR . $directory : $directory;
            if( is_dir($newPath) ) {
                $oldPath = $newPath;
                continue;
            } else {
                if( is_writable($oldPath) ) {
                    mkdir($newPath, 0777);
                } else {
                    throw new Exception("Unable to create directory '{$newPath}'. Access forbidden.");
                }
            }
            $oldPath = $newPath;
        }
        return $this;
    }

    static public function getNewFileName($destFile)
    {
        $fileInfo = pathinfo($destFile);
        if( file_exists($destFile) ) {
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
        while( ($char < 2) && ($char < strlen($fileName)) ) {
            if (empty($dispretionPath)) {
                $dispretionPath = DIRECTORY_SEPARATOR.('.' == $fileName[$char] ? '_' : $fileName[$char]);
            }
            else {
                $dispretionPath = self::_addDirSeparator($dispretionPath) . ('.' == $fileName[$char] ? '_' : $fileName[$char]);
            }
            $char ++;
        }
        return $dispretionPath;
    }
}
