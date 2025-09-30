<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Coupon codes grid "Used" column renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Coupons_Grid_Column_Renderer_Used extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    public function render(Varien_Object $row)
    {
        $value = (int) $row->getData($this->getColumn()->getIndex());
        return empty($value) ? Mage::helper('adminhtml')->__('No') : Mage::helper('adminhtml')->__('Yes');
    }
}
