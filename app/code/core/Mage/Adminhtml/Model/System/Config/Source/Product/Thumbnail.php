<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Catalog products per page on Grid mode source
 *
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
