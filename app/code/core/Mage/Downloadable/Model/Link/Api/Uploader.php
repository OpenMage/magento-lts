<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

/**
 * File uploader for API
 *
 * @package    Mage_Downloadable
 */
class Mage_Downloadable_Model_Link_Api_Uploader extends Mage_Core_Model_File_Uploader
{
    /**
     * Filename prefix
     *
     * @var string
     */
    protected $_filePrefix = 'Api';

    /**
     * Default file type
     */
    public const DEFAULT_FILE_TYPE = 'application/octet-stream';

    /**
     * Check if the uploaded file exists
     *
     * @param  array     $file
     * @throws Exception
     */
    public function __construct($file)
    {
        $this->_setUploadFile($file);
        if (!file_exists($this->_file['tmp_name'])) {
            throw new Exception('file_not_uploaded');
        }

        $this->_fileExists = true;
    }

    /**
     * Sets uploaded file info and decodes the file
     *
     * @param  array     $fileInfo
     * @throws Exception
     */
    private function _setUploadFile($fileInfo)
    {
        if (!is_array($fileInfo)) {
            throw new Exception('file_data_not_correct');
        }

        $this->_file = $this->_decodeFile($fileInfo);
        $this->_uploadType = self::SINGLE_STYLE;
    }

    /**
     * Decode uploaded file base64 encoded content
     *
     * @param  array $fileInfo
     * @return array
     */
    private function _decodeFile($fileInfo)
    {
        $tmpFileName = $this->_getTmpFilePath();

        $file = new Varien_Io_File();
        $file->open(['path' => sys_get_temp_dir()]);
        $file->streamOpen($tmpFileName);
        $file->streamWrite(base64_decode($fileInfo['base64_content']));
        $file->streamClose();

        return [
            'name' => $fileInfo['name'],
            'type' => $fileInfo['type'] ?? self::DEFAULT_FILE_TYPE,
            'tmp_name' => $tmpFileName,
            'error' => 0,
            'size' => filesize($tmpFileName),
        ];
    }

    /**
     * Generate temporary file name
     *
     * @return string
     */
    private function _getTmpFilePath()
    {
        return tempnam(sys_get_temp_dir(), $this->_filePrefix);
    }

    /**
     * Moves a file
     *
     * @param  string $sourceFile
     * @param  string $destinationFile
     * @return bool
     */
    protected function _moveFile($sourceFile, $destinationFile)
    {
        return rename($sourceFile, $destinationFile);
    }
}
