<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Backend model for uploading transactional emails custom logo image
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
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
        $uploadDir  = $uploadRoot . DS . $uploadDir;
        return $uploadDir;
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
