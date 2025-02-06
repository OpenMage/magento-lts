<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Grid select input column renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Select extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $name = $this->getColumn()->getName() ? $this->getColumn()->getName() : $this->getColumn()->getId();
        $html = '<select name="' . $this->escapeHtml($name) . '" ' . $this->getColumn()->getValidateClass() . '>';
        $value = $row->getData($this->getColumn()->getIndex());
        foreach ($this->getColumn()->getOptions() as $val => $label) {
            $selected = (($val == $value && (!is_null($value))) ? ' selected="selected"' : '');
            $html .= '<option value="' . $this->escapeHtml($val) . '"' . $selected . '>';
            $html .= $this->escapeHtml($label) . '</option>';
        }
        return $html . '</select>';
    }
}
