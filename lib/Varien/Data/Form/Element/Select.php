<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Varien
 * @package    Varien_Data
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Form select element
 *
 * @category   Varien
 * @package    Varien_Data
 *
 * @method array getOptions()
 */
class Varien_Data_Form_Element_Select extends Varien_Data_Form_Element_Abstract
{
    /**
     * Varien_Data_Form_Element_Select constructor.
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setType('select');
        $this->setExtType('combobox');
        $this->_prepareOptions();
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        $this->addClass('select');
        $html = '<select id="' . $this->getHtmlId() . '" name="' . $this->getName() . '" ' . $this->serialize($this->getHtmlAttributes()) . '>' . "\n";

        $value = $this->getValue();
        if (!is_array($value)) {
            $value = [$value];
        }

        if ($values = $this->getValues()) {
            foreach ($values as $key => $option) {
                if (!is_array($option)) {
                    $html .= $this->_optionToHtml(
                        [
                            'value' => $key,
                            'label' => $option,
                        ],
                        $value,
                    );
                } elseif (is_array($option['value'])) {
                    $html .= '<optgroup label="' . $option['label'] . '">' . "\n";
                    foreach ($option['value'] as $groupItem) {
                        $html .= $this->_optionToHtml($groupItem, $value);
                    }
                    $html .= '</optgroup>' . "\n";
                } else {
                    $html .= $this->_optionToHtml($option, $value);
                }
            }
        }

        $html .= '</select>' . "\n";
        return $html . $this->getAfterElementHtml();
    }

    /**
     * @param array $option
     * @param string|array $selected
     * @return string
     */
    protected function _optionToHtml($option, $selected)
    {
        if (is_array($option['value'])) {
            $html = '<optgroup label="' . $option['label'] . '">' . "\n";
            foreach ($option['value'] as $groupItem) {
                $html .= $this->_optionToHtml($groupItem, $selected);
            }
            $html .= '</optgroup>' . "\n";
        } else {
            $html = '<option value="' . $this->_escape($option['value']) . '"';
            $html .= isset($option['title']) ? 'title="' . $this->_escape($option['title']) . '"' : '';
            $html .= isset($option['style']) ? 'style="' . $option['style'] . '"' : '';
            if (in_array($option['value'], $selected)) {
                $html .= ' selected="selected"';
            }
            $html .= '>' . $this->_escape($option['label']) . '</option>' . "\n";
        }
        return $html;
    }

    protected function _prepareOptions()
    {
        $values = $this->getValues();
        if (empty($values)) {
            $options = $this->getOptions();
            if (is_array($options)) {
                $values = [];
                foreach ($options as $value => $label) {
                    $values[] = ['value' => $value, 'label' => $label];
                }
            } elseif (is_string($options)) {
                $values = [['value' => $options, 'label' => $options]];
            }
            $this->setValues($values);
        }
    }

    /**
     * @return array
     */
    public function getHtmlAttributes()
    {
        return ['title', 'class', 'style', 'onclick', 'onchange', 'disabled', 'readonly', 'tabindex'];
    }
}
