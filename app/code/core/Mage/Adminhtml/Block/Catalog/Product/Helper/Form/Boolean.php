<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Product form boolean field helper
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Boolean extends Varien_Data_Form_Element_Select
{
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setValues([
            [
                'label' => Mage::helper('catalog')->__('No'),
                'value' => 0,
            ],
            [
                'label' => Mage::helper('catalog')->__('Yes'),
                'value' => 1,
            ],
        ]);
    }
}
