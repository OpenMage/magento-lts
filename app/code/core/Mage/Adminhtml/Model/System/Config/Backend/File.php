<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * System config file field backend model
 *
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
     * @SuppressWarnings("PHPMD.Superglobals")
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
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('The file %s has been uploaded.', $result['file']),
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('adminhtml')->__('The file %s has not been uploaded.', $file['name']),
                );
                Mage::throwException($e->getMessage());
            }

            $filename = $result['file'];
            if ($filename) {
                if ($this->_addWhetherScopeInfo()) {
                    $filename = $this->_prependScopeInfo($filename);
                }

                $this->setValue($filename);
            }
        } elseif (is_array($value) && !empty($value['delete'])) {
            // When the delete checkbox is checked without a file being uploaded
            // Delete physical file first (before DB record is deleted)
            if ($oldValue = $this->getOldValue()) {
                $this->deleteFile($oldValue);
            }

            // Delete record before it is saved
            $this->delete();
            // Prevent record from being saved, since it was just deleted
            $this->_dataSaveAllowed = false;
        } else {
            $this->unsValue();
        }

        return $this;
    }

    /**
     * Delete file after a file is uploaded
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    protected function _afterSave()
    {
        parent::_afterSave();

        $groupId = $this->getGroupId();
        $field = $this->getField();

        // Check if delete checkbox is checked by looking at raw POST data
        // <input type="checkbox" name="groups[header][fields][logo_src][value][delete]" value="1" class="checkbox" id="design_header_logo_src_delete">
        // <input type="hidden" name="groups[header][fields][logo_src][value][value]" value="default/logo.png">
        $groups = Mage::app()->getRequest()->getPost('groups');
        $fieldData = $groups[$groupId]['fields'][$field]['value'] ?? [];

        $deleteChecked = $fieldData['delete'] ?? false;
        $filename = $fieldData['value'] ?? null;
        if ($deleteChecked && $filename) {
            $this->deleteFile($filename);
        }

        return $this;
    }

    /**
     * Delete file from the same directory as the uploaded file
     *
     * @param string $filename Filename with scope prefix (e.g., 'default/logo.png')
     *
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    protected function deleteFile(string $filename): void
    {
        // Get the upload directory for current scope (e.g., '/var/www/media/logo/default')
        $currentUploadDir = $this->_getUploadDir();

        // Get base upload directory without scope suffix (e.g., '/var/www/media/logo')
        $baseUploadDir = $currentUploadDir;
        if ($this->_addWhetherScopeInfo()) {
            $scopeSuffix = DS . $this->getScope();
            if ($this->getScope() !== 'default') {
                $scopeSuffix .= DS . $this->getScopeId();
            }

            if (str_ends_with($baseUploadDir, $scopeSuffix)) {
                $baseUploadDir = substr($baseUploadDir, 0, -strlen($scopeSuffix));
            }
        }

        // Construct full path: /var/www/media/logo + default/logo.png
        $filePath = $baseUploadDir . DS . $filename;

        // Safety check: only delete if file is in the same directory as current upload directory
        // This prevents deleting inherited files from parent scopes
        $fileDir = dirname($filePath);
        if ($fileDir !== $currentUploadDir) {
            // File is in a different scope directory (e.g., inherited from default)
            // Don't delete it to preserve inheritance
            Mage::getSingleton('adminhtml/session')->addWarning(
                Mage::helper('adminhtml')->__('The file %s is inherited from a parent scope and cannot be deleted.', basename($filename)),
            );
            return;
        }

        if (file_exists($filePath)) {
            @unlink($filePath);
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('adminhtml')->__('The file %s has been deleted.', basename($filename)),
            );
        }
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

        $uploadDir = (string) $fieldConfig->upload_dir;

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
            $uploadRoot = $this->_getUploadRoot((string) $el['config']);
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
            $allowedExtensions = (string) $el['allowed_extensions'];
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
