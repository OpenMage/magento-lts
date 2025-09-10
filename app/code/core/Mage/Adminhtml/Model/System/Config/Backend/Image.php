<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * System config image field backend model
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Image extends Mage_Adminhtml_Model_System_Config_Backend_File
{
    /**
     * Getter for allowed extensions of uploaded files
     * @return array
     */
    protected function _getAllowedExtensions()
    {
        return Varien_Io_File::ALLOWED_IMAGES_EXTENSIONS;
    }

    /**
     * Overwritten parent method for adding validators
     */
    protected function addValidators(Mage_Core_Model_File_Uploader $uploader)
    {
        parent::addValidators($uploader);
        $validator = Mage::getModel('core/file_validator_image');
        $validator->setAllowedImageTypes($this->_getAllowedExtensions());
        $uploader->addValidateCallback(Mage_Core_Model_File_Validator_Image::NAME, $validator, 'validate');
    }
}
