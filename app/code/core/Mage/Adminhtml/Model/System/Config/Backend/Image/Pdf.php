<?php
/**
 * System config image field backend model for Zend PDF generator
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Image_Pdf extends Mage_Adminhtml_Model_System_Config_Backend_Image
{
    protected function _getAllowedExtensions()
    {
        return ['tif', 'tiff', 'png', 'jpg', 'jpe', 'jpeg'];
    }
}
