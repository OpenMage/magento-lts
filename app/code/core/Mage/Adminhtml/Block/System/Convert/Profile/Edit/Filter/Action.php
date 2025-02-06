<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * System Convert History action filter
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Convert_Profile_Edit_Filter_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Abstract
{
    public function getHtml()
    {
        $values = [
            ''       => '',
            'create' => Mage::helper('adminhtml')->__('Create'),
            'run'    => Mage::helper('adminhtml')->__('Run'),
            'update' => Mage::helper('adminhtml')->__('Update'),
        ];
        $value = $this->getValue();

        $html  = '<select name="' . ($this->getColumn()->getName() ? $this->getColumn()->getName() : $this->getColumn()->getId()) . '" ' . $this->getColumn()->getValidateClass() . '>';
        foreach ($values as $k => $v) {
            $html .= '<option value="' . $k . '"' . ($value == $k ? ' selected="selected"' : '') . '>' . $v . '</option>';
        }
        return $html . '</select>';
    }
}
