<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2021-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
        $html .= '</div>';

        return $html;
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
