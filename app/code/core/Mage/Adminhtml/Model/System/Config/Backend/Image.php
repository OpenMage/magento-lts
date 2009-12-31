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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * System config image field backend model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_System_Config_Backend_Image extends Mage_Core_Model_Config_Data
{

    /**
     * Save uploaded file before saving config value
     *
     * @return Mage_Adminhtml_Model_System_Config_Backend_Image
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value) && !empty($value['delete'])) {
            $this->setValue('');
        }

        if ($_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value']){

            $fieldConfig = $this->getFieldConfig();
            /* @var $fieldConfig Varien_Simplexml_Element */

            if (empty($fieldConfig->upload_dir)) {
                Mage::throwException(Mage::helper('catalog')->__('Base directory to upload image file is not specified'));
            }

            $uploadDir =  (string)$fieldConfig->upload_dir;

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
                $uploadRoot = (string)Mage::getConfig()->getNode((string)$el['config'], $this->getScope(), $this->getScopeId());
                $uploadRoot = Mage::getConfig()->substDistroServerVars($uploadRoot);
                $uploadDir = $uploadRoot . '/' . $uploadDir;
            }

            try {
                $file = array();
                $file['tmp_name'] = $_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value'];
                $file['name'] = $_FILES['groups']['name'][$this->getGroupId()]['fields'][$this->getField()]['value'];
                $uploader = new Varien_File_Uploader($file);
                $uploader->setAllowedExtensions($this->_getAllowedExtensions());
                $uploader->setAllowRenameFiles(true);
                $uploader->save($uploadDir);
            } catch (Exception $e) {
                Mage::throwException($e->getMessage());
                return $this;
            }

            if ($filename = $uploader->getUploadedFileName()) {

                /**
                 * Add scope info
                 */
                if (!empty($el['scope_info'])) {
                    $filename = $this->_prependScopeInfo($filename);
                }

                $this->setValue($filename);
            }
        }

        return $this;
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
        if ('default' != $this->getScope()) {
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
        if ('default' != $this->getScope()) {
            $path .= '/' . $this->getScopeId();
        }
        return $path;
    }

    protected function _getAllowedExtensions()
    {
        return array('jpg', 'jpeg', 'gif', 'png');
    }
}
