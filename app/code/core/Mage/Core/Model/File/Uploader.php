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
 * Core file uploader model
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
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

        /** @var $helper Mage_Core_Helper_File_Storage */
        $helper = Mage::helper('core/file_storage');

        if ($helper->isInternalStorage() || $this->skipDbProcessing()) {
            return $this;
        }

        /** @var $dbHelper Mage_Core_Helper_File_Storage_Database */
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
        $this->_skipDbProcessing = (bool)$flag;
        return $this;
    }

    /**
     * Check protected/allowed extension
     *
     * @param string $extension
     * @return boolean
     */
    public function checkAllowedExtension($extension)
    {
        //validate with protected file types
        /** @var $validator Mage_Core_Model_File_Validator_NotProtectedExtension */
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
     * @return bool|void
     * @throws Exception
     */
    public function save($destinationFolder, $newFileName = null)
    {
        $fileName = isset($newFileName) ? $newFileName : $this->_file['name'];
        if (strlen($fileName) > $this->_fileNameMaxLength) {
            throw new Exception(
                Mage::helper('core')->__("File name is too long. Maximum length is %s.", $this->_fileNameMaxLength)
            );
        }
        return parent::save($destinationFolder, $newFileName);
    }
}
