<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Core file uploader model
 *
 * @package    Mage_Core
 */
class Mage_Core_Model_File_Uploader extends Varien_File_Uploader
{
    /**
     * Flag, that defines should DB processing be skipped
     *
     * @var bool
     */
    protected $_skipDbProcessing = false;

    /**
     * Max file name length
     *
     * @var int
     */
    protected $_fileNameMaxLength = 200;

    /**
     * Save file to storage
     *
     * @param  array $result
     * @return $this
     */
    protected function _afterSave($result)
    {
        if (empty($result['path']) || empty($result['file'])) {
            return $this;
        }

        /** @var Mage_Core_Helper_File_Storage $helper */
        $helper = Mage::helper('core/file_storage');

        if ($helper->isInternalStorage() || $this->skipDbProcessing()) {
            return $this;
        }

        /** @var Mage_Core_Helper_File_Storage_Database $dbHelper */
        $dbHelper = Mage::helper('core/file_storage_database');
        $this->_result['file'] = $dbHelper->saveUploadedFile($result);

        return $this;
    }

    /**
     * Getter/Setter for _skipDbProcessing flag
     *
     * @param null|bool $flag
     * @return bool|Mage_Core_Model_File_Uploader
     */
    public function skipDbProcessing($flag = null)
    {
        if (is_null($flag)) {
            return $this->_skipDbProcessing;
        }

        $this->_skipDbProcessing = (bool) $flag;
        return $this;
    }

    /**
     * Check protected/allowed extension
     *
     * @param string $extension
     * @return bool
     */
    public function checkAllowedExtension($extension)
    {
        //validate with protected file types
        /** @var Mage_Core_Model_File_Validator_NotProtectedExtension $validator */
        $validator = Mage::getSingleton('core/file_validator_notProtectedExtension');
        if (!$validator->isValid($extension)) {
            return false;
        }

        return parent::checkAllowedExtension($extension);
    }

    /**
     * Used to save uploaded file into destination folder with
     * original or new file name (if specified).
     * Added file name length validation.
     *
     * @param string $destinationFolder
     * @param string|null $newFileName
     * @return array|bool
     * @throws Exception
     */
    public function save($destinationFolder, $newFileName = null)
    {
        $fileName = $newFileName ?? $this->_file['name'];
        if (strlen($fileName) > $this->_fileNameMaxLength) {
            throw new Exception(
                Mage::helper('core')->__('File name is too long. Maximum length is %s.', $this->_fileNameMaxLength),
            );
        }

        return parent::save($destinationFolder, $newFileName);
    }
}
