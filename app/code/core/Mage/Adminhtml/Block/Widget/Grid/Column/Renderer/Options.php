<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Grid column widget for rendering grid cells that contains mapped values
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    /**
     * Render a grid cell as options
     *
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $options = $this->getColumn()->getOptions();
        $showMissingOptionValues = (bool) $this->getColumn()->getShowMissingOptionValues();
        if (!empty($options) && is_array($options)) {
            $value = $row->getData($this->getColumn()->getIndex());
            if (is_array($value)) {
                $res = [];
                foreach ($value as $item) {
                    if (isset($options[$item])) {
                        $res[] = $this->escapeHtml($options[$item]);
                    } elseif ($showMissingOptionValues) {
                        $res[] = $this->escapeHtml($item);
                    }
                }
                return implode(', ', $res);
            } elseif (isset($options[$value])) {
                return $this->escapeHtml($options[$value]);
            } elseif (in_array($value, $options)) {
                return $this->escapeHtml($value);
            }
        }
        return '';
    }
}
