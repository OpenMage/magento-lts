<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Catalog_ImageFileHandle
{
    public function toOptionArray()
    {
        return [
            ['value' => Mage_Catalog_Model_Product_Image::ON_REMOVAL_KEEP, 'label' => Mage::helper('adminhtml')->__('Keep image file(s) on the filesystem')],
            ['value' => Mage_Catalog_Model_Product_Image::ON_REMOVAL_DELETE, 'label' => Mage::helper('adminhtml')->__('Permanently deletes image file(s)')],
        ];
    }
}
