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
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Directory contents block for Wysiwyg Images
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Cms_Wysiwyg_Images_Content_Files extends Mage_Adminhtml_Block_Template
{
    /**
     * Files collection object
     *
     * @var Varien_Data_Collection_Filesystem
     */
    protected $_filesCollection;

    /**
     * Prepared Files collection for current directory
     *
     * @return Varien_Data_Collection_Filesystem
     */
    public function getFiles()
    {
        if (!$this->_filesCollection) {
            $this->_filesCollection = Mage::getSingleton('cms/wysiwyg_images_storage')->getFilesCollection(Mage::helper('cms/wysiwyg_images')->getCurrentPath(), $this->_getMediaType());
        }

        return $this->_filesCollection;
    }

    /**
     * Files collection count getter
     *
     * @return int
     */
    public function getFilesCount()
    {
        return $this->getFiles()->count();
    }

    /**
     * File idetifier getter
     *
     * @return string
     */
    public function getFileId(Varien_Object $file)
    {
        return $file->getId();
    }

    /**
     * File thumb URL getter
     *
     * @return string
     */
    public function getFileThumbUrl(Varien_Object $file)
    {
        return $file->getThumbUrl();
    }

    /**
     * File name URL getter
     *
     * @return string
     */
    public function getFileName(Varien_Object $file)
    {
        return $file->getName();
    }

    /**
     * Image file width getter
     *
     * @return string
     */
    public function getFileWidth(Varien_Object $file)
    {
        return $file->getWidth();
    }

    /**
     * Image file height getter
     *
     * @return string
     */
    public function getFileHeight(Varien_Object $file)
    {
        return $file->getHeight();
    }

    /**
     * File short name getter
     *
     * @return string
     */
    public function getFileShortName(Varien_Object $file)
    {
        return $file->getShortName();
    }

    public function getImagesWidth()
    {
        return Mage::getSingleton('cms/wysiwyg_images_storage')->getConfigData('resize_width');
    }

    public function getImagesHeight()
    {
        return Mage::getSingleton('cms/wysiwyg_images_storage')->getConfigData('resize_height');
    }

    /**
     * Return current media type based on request or data
     * @return string
     */
    protected function _getMediaType()
    {
        if ($this->hasData('media_type')) {
            return $this->_getData('media_type');
        }
        return $this->getRequest()->getParam('type');
    }
}
