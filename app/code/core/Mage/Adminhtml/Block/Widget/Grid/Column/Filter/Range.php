<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Range grid column filter
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Range extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Abstract
{
    public function getHtml()
    {
        $fromLabel = Mage::helper('adminhtml')->__('From');
        $toLabel = Mage::helper('adminhtml')->__('To');

        $html  = '<div class="range filter-range">';
        $html .= '<div class="range-line"><span class="label">' . $fromLabel . '</span> <input type="number" placeholder="' . $fromLabel . '" name="' . $this->_getHtmlName() . '[from]" id="' . $this->_getHtmlId() . '_from" value="' . $this->getEscapedValue('from') . '" class="input-text no-changes"/></div>';
        $html .= '<div class="range-line"><span class="label">' . $toLabel . '</span><input type="number" placeholder="' . $toLabel . '" name="' . $this->_getHtmlName() . '[to]" id="' . $this->_getHtmlId() . '_to" value="' . $this->getEscapedValue('to') . '" class="input-text no-changes"/></div>';

        return $html . '</div>';
    }

    public function getValue($index = null)
    {
        if ($index) {
            return $this->getData('value', $index);
        }
        $value = $this->getData('value');
        if ((isset($value['from']) && strlen($value['from']) > 0)
            || (isset($value['to']) && strlen($value['to']) > 0)
        ) {
            return $value;
        }
        return null;
    }

    public function getCondition()
    {
        $value = $this->getValue();

        if (isset($value['from']) && isset($value['to']) && $value['from'] === $value['to']) {
            return ['eq' => $value['from']];
        }

        return $value;
    }
}
