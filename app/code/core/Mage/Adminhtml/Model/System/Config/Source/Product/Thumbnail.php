<?php
/**
 * Catalog products per page on Grid mode source
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Product_Thumbnail
{
    public function toOptionArray()
    {
        return [
            ['value' => 'itself', 'label' => Mage::helper('adminhtml')->__('Product Thumbnail Itself')],
            ['value' => 'parent', 'label' => Mage::helper('adminhtml')->__('Parent Product Thumbnail')],
        ];
    }
}
