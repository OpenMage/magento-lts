<?php
/**
 * Generate options for media storage selection
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Storage_Media_Storage
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => Mage_Core_Model_File_Storage::STORAGE_MEDIA_FILE_SYSTEM,
                'label' => Mage::helper('adminhtml')->__('File System'),
            ],
            [
                'value' => Mage_Core_Model_File_Storage::STORAGE_MEDIA_DATABASE,
                'label' => Mage::helper('adminhtml')->__('Database'),
            ],
        ];
    }
}
