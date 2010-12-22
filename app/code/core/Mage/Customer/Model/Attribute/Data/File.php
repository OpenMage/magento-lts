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
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Customer Attribute File Data Model
 *
 * @category    Mage
 * @package     Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Attribute_Data_File extends Mage_Customer_Model_Attribute_Data_Abstract
{
    /**
     * Extract data from request and return value
     *
     * @param Zend_Controller_Request_Http $request
     * @return array|string
     */
    public function extractValue(Zend_Controller_Request_Http $request)
    {
        if ($this->getIsAjaxRequest()) {
            return false;
        }

        $extend = $this->_getRequestValue($request);

        $attrCode  = $this->getAttribute()->getAttributeCode();
        if ($this->_requestScope) {
            $value  = array();
            if (strpos($this->_requestScope, '/') !== false) {
                $scopes = explode('/', $this->_requestScope);
                $mainScope  = array_shift($scopes);
            } else {
                $mainScope  = $this->_requestScope;
                $scopes     = array();
            }

            if (!empty($_FILES[$mainScope])) {
                foreach ($_FILES[$mainScope] as $fileKey => $scopeData) {
                    foreach ($scopes as $scopeName) {
                        if (isset($scopeData[$scopeName])) {
                            $scopeData = $scopeData[$scopeName];
                        } else {
                            $scopeData[$scopeName] = array();
                        }
                    }

                    if (isset($scopeData[$attrCode])) {
                        $value[$fileKey] = $scopeData[$attrCode];
                    }
                }
            } else {
                $value = array();
            }
        } else {
            if (isset($_FILES[$attrCode])) {
                $value = $_FILES[$attrCode];
            } else {
                $value = array();
            }
        }

        if (!empty($extend['delete'])) {
            $value['delete'] = true;
        }

        return $value;
    }

    /**
     * Validate file by attribute validate rules
     * Return array of errors
     *
     * @param array $value
     * @return array
     */
    protected function _validateByRules($value)
    {
        $label  = $this->getAttribute()->getStoreLabel();
        $rules  = $this->getAttribute()->getValidateRules();
        if (!empty($rules['file_extensions'])) {
            $extension  = pathinfo($value['name'], PATHINFO_EXTENSION);
            $extensions = explode(',', $rules['file_extensions']);
            $extensions = array_map('trim', $extensions);
            if (!in_array($extension, $extensions)) {
                return array(
                    Mage::helper('customer')->__('"%s" is not a valid file extension.', $label)
                );
            }
        }

        if (!is_uploaded_file($value['tmp_name'])) {
            return array(
                Mage::helper('customer')->__('"%s" is not a valid file.', $label)
            );
        }

        if (!empty($rules['max_file_size'])) {
            $size = $value['size'];
            if ($rules['max_file_size'] < $size) {
                return array(
                    Mage::helper('customer')->__('"%s" exceeds the allowed file size.', $label)
                );
            };
        }

        return array();
    }

    /**
     * Validate data
     *
     * @param array|string $value
     * @throws Mage_Core_Exception
     * @return boolean
     */
    public function validateValue($value)
    {
        $errors     = array();
        $attribute  = $this->getAttribute();
        $label      = $attribute->getStoreLabel();

        if (is_array($value)) {
            $toDelete   = !empty($value['delete']) ? true : false;
            $toUpload   = !empty($value['tmp_name']) ? true : false;
        
            if (!$toUpload && !$toDelete && $this->getEntity()->getData($attribute->getAttributeCode())) {
                return true;
            }
        
            if (!$attribute->getIsRequired() && !$toUpload) {
                return true;
            }
        
            if ($attribute->getIsRequired() && !$toUpload) {
                $errors[] = Mage::helper('customer')->__('"%s" is a required value.', $label);
            }
        
            if ($toUpload) {
                $errors = array_merge($errors, $this->_validateByRules($value));
            }
        } else {
            $filePath = Mage::getBaseDir('media') . DS . 'customer' . $value;
            if ($attribute->getIsRequired() && !file_exists($filePath)) {
                $errors[] = Mage::helper('customer')->__('"%s" is a required value.', $label);
            }
        }
        
        if (count($errors) == 0) {
            return true;
        }

        return $errors;
    }

    /**
     * Export attribute value to entity model
     *
     * @param Mage_Core_Model_Abstract $entity
     * @param array|string $value
     * @return Mage_Customer_Model_Attribute_Data_File
     */
    public function compactValue($value)
    {
        if ($this->getIsAjaxRequest()) {
            return $this;
        }

        $attribute = $this->getAttribute();
        $original  = $this->getEntity()->getData($attribute->getAttributeCode());
        $toDelete  = false;
        if ($original) {
            if (!$attribute->getIsRequired() && !empty($value['delete'])) {
                $toDelete  = true;
            }
            if (!empty($value['tmp_name'])) {
                $toDelete  = true;
            }
        }

        $ioFile = new Varien_Io_File();
        $path   = Mage::getBaseDir('media') . DS . 'customer';
        $ioFile->open(array('path' => $path));

        // unlink entity file
        if ($toDelete) {
            $this->getEntity()->setData($attribute->getAttributeCode(), '');
            $file = $path . $original;
            if ($ioFile->fileExists($file)) {
                $ioFile->rm($file);
            }
        }

        if (!empty($value['tmp_name'])) {
            try {
                $uploader = new Mage_Core_Model_File_Uploader($value);
                $uploader->setFilesDispersion(true);
                $uploader->setFilenamesCaseSensitivity(false);
                $uploader->setAllowRenameFiles(true);
                $uploader->save($path, $value['name']);
                $fileName = $uploader->getUploadedFileName();
                $this->getEntity()->setData($attribute->getAttributeCode(), $fileName);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        return $this;
    }

    /**
     * Restore attribute value from SESSION to entity model
     *
     * @param array|string $value
     * @return Mage_Customer_Model_Attribute_Data_Abstract
     */
    public function restoreValue($value)
    {
        return $this;
    }

    /**
     * Return formated attribute value from entity model
     *
     * @return string|array
     */
    public function outputValue($format = Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_TEXT)
    {
        $output = '';
        $value  = $this->getEntity()->getData($this->getAttribute()->getAttributeCode());
        if ($value) {
            switch ($format) {
                case Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_JSON:
                    $output = array(
                        'value'     => $value,
                        'url_key'   => Mage::helper('core')->urlEncode($value)
                    );
                    break;
            }
        }

        return $output;
    }
}
