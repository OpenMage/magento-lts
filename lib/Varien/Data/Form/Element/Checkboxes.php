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
class Varien_Data_Form_Element_Checkboxes extends Varien_Data_Form_Element_Abstract
{
    /**
     * Init Element
     *
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setType('checkbox');
        $this->setExtType('checkbox');
    }

    /**
     * Retrieve allow attributes
     *
     * @return array
     */
    public function getHtmlAttributes()
    {
        return ['type', 'name', 'class', 'style', 'checked', 'onclick', 'onchange', 'disabled'];
    }

    /**
     * Prepare value list
     *
     * @return array
     */
    protected function _prepareValues()
    {
        $options = [];
        $values  = [];

        if ($this->getValues()) {
            if (!is_array($this->getValues())) {
                $options = [$this->getValues()];
            } else {
                $options = $this->getValues();
            }
        } elseif ($this->getOptions() && is_array($this->getOptions())) {
            $options = $this->getOptions();
        }
        foreach ($options as $k => $v) {
            if (is_string($v)) {
                $values[] = [
                    'label' => $v,
                    'value' => $k
                ];
            } elseif (isset($v['value'])) {
                if (!isset($v['label'])) {
                    $v['label'] = $v['value'];
                }
                $values[] = [
                    'label' => $v['label'],
                    'value' => $v['value']
                ];
            }
        }

        return $values;
    }

    /**
     * Retrieve HTML
     *
     * @return string
     */
    public function getElementHtml()
    {
        $values = $this->_prepareValues();

        if (!$values) {
            return '';
        }

        $html  = '<ul class="checkboxes">';
        foreach ($values as $value) {
            $html .= $this->_optionToHtml($value);
        }
        $html .= '</ul>'
            . $this->getAfterElementHtml();

        return $html;
    }

    /**
     * @param string $value
     * @return string|void
     */
    public function getChecked($value)
    {
        if ($checked = $this->getValue()) {
        } elseif ($checked = $this->getData('checked')) {
        } else {
            return;
        }
        if (!is_array($checked)) {
            $checked = [(string) $checked];
        } else {
            foreach ($checked as $k => $v) {
                $checked[$k] = (string) $v;
            }
        }
        if (in_array((string) $value, $checked)) {
            return 'checked';
        }
        return ;
    }

    /**
     * @param string $value
     * @return string|void
     */
    public function getDisabled($value)
    {
        if ($disabled = $this->getData('disabled')) {
            if (!is_array($disabled)) {
                $disabled = [(string) $disabled];
            } else {
                foreach ($disabled as $k => $v) {
                    $disabled[$k] = (string) $v;
                }
            }
            if (in_array((string) $value, $disabled)) {
                return 'disabled';
            }
        }
        return;
    }

    /**
     * @param string $value
     * @return string|void
     */
    public function getOnclick($value)
    {
        if ($onclick = $this->getData('onclick')) {
            return str_replace('$value', $value, $onclick);
        }
        return;
    }

    /**
     * @param string $value
     * @return string|void
     */
    public function getOnchange($value)
    {
        if ($onchange = $this->getData('onchange')) {
            return str_replace('$value', $value, $onchange);
        }
        return;
    }

//    public function getName($value)
//    {
//        if ($name = $this->getData('name')) {
//            return str_replace('$value', $value, $name);
//        }
//        return ;
//    }

    /**
     * @param array $option
     * @return string
     */
    protected function _optionToHtml($option)
    {
        $id = $this->getHtmlId() . '_' . $this->_escape($option['value']);

        $html = '<li><input id="' . $id . '"';
        foreach ($this->getHtmlAttributes() as $attribute) {
            if ($value = $this->getDataUsingMethod($attribute, $option['value'])) {
                $html .= ' ' . $attribute . '="' . $value . '"';
            }
        }
        $html .= ' value="' . $option['value'] . '" />'
            . ' <label for="' . $id . '">' . $option['label'] . '</label></li>'
            . "\n";
        return $html;
    }
}
