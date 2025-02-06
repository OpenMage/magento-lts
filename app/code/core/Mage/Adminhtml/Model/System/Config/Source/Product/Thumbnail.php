<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Catalog products per page on Grid mode source
 *
 * @category   Mage
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
