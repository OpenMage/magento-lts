<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Page
 */

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Image_Logo extends Mage_Adminhtml_Model_System_Config_Backend_Image
{
    /**
     * Upload max file size in kilobytes
     *
     * @var int
     */
    protected $_maxFileSize = 2048;

    /**
     * Fix media dir for all uploads of logo files
     */
    protected function _getUploadDir(): string
    {
        $uploadDir  = $this->_appendScopeInfo(Mage_Page_Helper_Data::LOGO_MEDIA_DIR);
        return  Mage::getBaseDir('media') . DS . $uploadDir;
    }

    /**
     * Always add scope info
     */
    protected function _addWhetherScopeInfo(): bool
    {
        return true;
    }
}
