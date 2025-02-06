<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Backend model for uploading transactional emails custom logo image
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Email_Logo extends Mage_Adminhtml_Model_System_Config_Backend_Image
{
    /**
     * The tail part of directory path for uploading
     */
    public const UPLOAD_DIR                = 'email/logo';

    /**
     * Token for the root part of directory path for uploading
     */
    public const UPLOAD_ROOT_TOKEN         = 'system/filesystem/media';

    /**
     * Upload max file size in kilobytes
     *
     * @var int
     */
    protected $_maxFileSize         = 2048;

    /**
     * Return path to directory for upload file
     *
     * @return string
     */
    protected function _getUploadDir()
    {
        $uploadDir  = $this->_appendScopeInfo(self::UPLOAD_DIR);
        $uploadRoot = $this->_getUploadRoot(self::UPLOAD_ROOT_TOKEN);
        return $uploadRoot . DS . $uploadDir;
    }

    /**
     * Makes a decision about whether to add info about the scope
     *
     * @return bool
     */
    protected function _addWhetherScopeInfo()
    {
        return true;
    }
}
