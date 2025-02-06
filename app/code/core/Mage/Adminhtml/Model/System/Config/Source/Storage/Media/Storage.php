<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Generate options for media storage selection
 *
 * @category   Mage
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
