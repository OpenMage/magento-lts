<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * System config image field backend model for Zend PDF generator
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Image_Favicon extends Mage_Adminhtml_Model_System_Config_Backend_Image
{
    /**
     * The tail part of directory path for uploading
     *
     */
    public const UPLOAD_DIR = 'favicon';

    /**
     * Token for the root part of directory path for uploading
     *
     */
    public const UPLOAD_ROOT = 'media';

    /**
     * Return path to directory for upload file
     *
     * @return string
     * @throw Mage_Core_Exception
     */
    protected function _getUploadDir()
    {
        $uploadDir = $this->_appendScopeInfo(self::UPLOAD_DIR);
        $uploadRoot = $this->_getUploadRoot(self::UPLOAD_ROOT);
        return $uploadRoot . '/' . $uploadDir;
    }

    /**
     * Makes a decision about whether to add info about the scope.
     *
     * @return bool
     */
    protected function _addWhetherScopeInfo()
    {
        return true;
    }

    /**
     * Getter for allowed extensions of uploaded files.
     *
     * @return array
     */
    protected function _getAllowedExtensions()
    {
        return ['ico', 'png', 'gif', 'jpg', 'jpeg', 'apng'];
    }

    /**
     * Get real media dir path
     *
     * @param string $token
     * @return string
     */
    protected function _getUploadRoot($token)
    {
        return Mage::getBaseDir($token);
    }
}
