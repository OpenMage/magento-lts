<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * System config file field backend model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_File extends Mage_Core_Model_Config_Data
{
    public const SYSTEM_FILESYSTEM_REGEX = '/{{([a-z_]+)}}(.*)/';

    /**
     * Upload max file size in kilobytes
     *
     * @var int
     */
    protected $_maxFileSize = 0;

    /**
     * Save uploaded file before saving config value
     *
     * @return $this
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (!empty($_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value'])) {
            $uploadDir = $this->_getUploadDir();

            try {
                $file = [];
                $tmpName = $_FILES['groups']['tmp_name'];
                $file['tmp_name'] = $tmpName[$this->getGroupId()]['fields'][$this->getField()]['value'];
                $name = $_FILES['groups']['name'];
                $file['name'] = $name[$this->getGroupId()]['fields'][$this->getField()]['value'];
                $uploader = Mage::getModel('core/file_uploader', $file);
                $uploader->setAllowedExtensions($this->_getAllowedExtensions());
                $uploader->setAllowRenameFiles(true);
                $this->addValidators($uploader);
                $result = $uploader->save($uploadDir);
            } catch (Exception $e) {
                Mage::throwException($e->getMessage());
            }

            $filename = $result['file'];
            if ($filename) {
                if ($this->_addWhetherScopeInfo()) {
                    $filename = $this->_prependScopeInfo($filename);
                }
                $this->setValue($filename);
            }
        } else {
            if (is_array($value) && !empty($value['delete'])) {
                // Delete record before it is saved
                $this->delete();
                // Prevent record from being saved, since it was just deleted
                $this->_dataSaveAllowed = false;
            } else {
                $this->unsValue();
            }
        }

        return $this;
    }

    /**
     * Validation callback for checking max file size
     *
     * @param  string $filePath Path to temporary uploaded file
     * @throws Mage_Core_Exception
     */
    public function validateMaxSize($filePath)
    {
        if ($this->_maxFileSize > 0 && filesize($filePath) > ($this->_maxFileSize * 1024)) {
            throw Mage::exception('Mage_Core', Mage::helper('adminhtml')->__('Uploaded file is larger than %.2f kilobytes allowed by server', $this->_maxFileSize));
        }
    }

    /**
     * Makes a decision about whether to add info about the scope.
     *
     * @return bool
     */
    protected function _addWhetherScopeInfo()
    {
        $fieldConfig = $this->getFieldConfig();
        $el = $fieldConfig->descend('upload_dir');
        return (!empty($el['scope_info']));
    }

    /**
     * Return path to directory for upload file
     *
     * @return string
     * @throw Mage_Core_Exception
     */
    protected function _getUploadDir()
    {
        $fieldConfig = $this->getFieldConfig();
        /** @var Varien_Simplexml_Element $fieldConfig */

        if (empty($fieldConfig->upload_dir)) {
            Mage::throwException(Mage::helper('catalog')->__('The base directory to upload file is not specified.'));
        }

        $uploadDir = (string)$fieldConfig->upload_dir;

        $el = $fieldConfig->descend('upload_dir');

        /**
         * Add scope info
         */
        if (!empty($el['scope_info'])) {
            $uploadDir = $this->_appendScopeInfo($uploadDir);
        }

        /**
         * Take root from config
         */
        if (!empty($el['config'])) {
            $uploadRoot = $this->_getUploadRoot((string)$el['config']);
            $uploadDir = $uploadRoot . '/' . $uploadDir;
        }
        return $uploadDir;
    }

    /**
     * Return the root part of directory path for uploading
     *
     * @param string $token
     * @return string
     */
    protected function _getUploadRoot($token)
    {
        $value = Mage::getStoreConfig($token) ?? '';
        if (strlen($value) && preg_match(self::SYSTEM_FILESYSTEM_REGEX, $value, $matches) !== false) {
            $dir = str_replace('root_dir', 'base_dir', $matches[1]);
            $path = str_replace('/', DS, $matches[2]);
            return Mage::getConfig()->getOptions()->getData($dir) . $path;
        }
        return Mage::getBaseDir('media');
    }

    /**
     * Prepend path with scope info
     *
     * E.g. 'stores/2/path' , 'websites/3/path', 'default/path'
     *
     * @param string $path
     * @return string
     */
    protected function _prependScopeInfo($path)
    {
        $scopeInfo = $this->getScope();
        if ($this->getScope() != 'default') {
            $scopeInfo .= '/' . $this->getScopeId();
        }
        return $scopeInfo . '/' . $path;
    }

    /**
     * Add scope info to path
     *
     * E.g. 'path/stores/2' , 'path/websites/3', 'path/default'
     *
     * @param string $path
     * @return string
     */
    protected function _appendScopeInfo($path)
    {
        $path .= '/' . $this->getScope();
        if ($this->getScope() != 'default') {
            $path .= '/' . $this->getScopeId();
        }
        return $path;
    }

    /**
     * Getter for allowed extensions of uploaded files
     *
     * @return array
     */
    protected function _getAllowedExtensions()
    {
        /** @var Varien_Simplexml_Element $fieldConfig */
        $fieldConfig = $this->getFieldConfig();
        $el = $fieldConfig->descend('upload_dir');
        if (!empty($el['allowed_extensions'])) {
            $allowedExtensions = (string)$el['allowed_extensions'];
            return explode(',', $allowedExtensions);
        }
        return [];
    }

    /**
     * Add validators for uploading
     */
    protected function addValidators(Mage_Core_Model_File_Uploader $uploader)
    {
        $uploader->addValidateCallback('size', $this, 'validateMaxSize');
    }
}
