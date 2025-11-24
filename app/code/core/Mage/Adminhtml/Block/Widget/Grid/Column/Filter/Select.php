<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Select grid column filter
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Abstract
{
    /**
     * @return array[]
     */
    protected function _getOptions()
    {
        $emptyOption = ['value' => null, 'label' => ''];

        $optionGroups = $this->getColumn()->getOptionGroups();
        if ($optionGroups) {
            array_unshift($optionGroups, $emptyOption);
            return $optionGroups;
        }

        $colOptions = $this->getColumn()->getOptions();
        if (!empty($colOptions) && is_array($colOptions)) {
            $options = [$emptyOption];
            foreach ($colOptions as $value => $label) {
                $options[] = ['value' => $value, 'label' => $label];
            }

            return $options;
        }

        return [];
    }

    /**
     * Render an option with selected value
     *
     * @param array $option
     * @param null|string $value
     * @return string
     */
    protected function _renderOption($option, $value)
    {
        $selected = (($option['value'] == $value && (!is_null($value))) ? ' selected="selected"' : '');
        return '<option value="' . $this->escapeHtml($option['value']) . '"' . $selected . '>' . $this->escapeHtml($option['label']) . '</option>';
    }

    /**
     * @return string
     */
    public function getHtml()
    {
        $html = '<select name="' . $this->_getHtmlName() . '" id="' . $this->_getHtmlId() . '" class="no-changes">';
        $value = $this->getValue();
        foreach ($this->_getOptions() as $option) {
            if (is_array($option['value'])) {
                $html .= '<optgroup label="' . $this->escapeHtml($option['label']) . '">';
                foreach ($option['value'] as $subOption) {
                    $html .= $this->_renderOption($subOption, $value);
                }

                $html .= '</optgroup>';
            } else {
                $html .= $this->_renderOption($option, $value);
            }
        }

        return $html . '</select>';
    }

    /**
     * @return null|array
     */
    public function getCondition()
    {
        if (is_null($this->getValue())) {
            return null;
        }

        return ['eq' => $this->getValue()];
    }
}
