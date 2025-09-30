<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Data
 */

/**
 * Radio buttons collection
 *
 * @package    Varien_Data
 */
class Varien_Data_Form_Element_Radios extends Varien_Data_Form_Element_Abstract
{
    /**
     * Varien_Data_Form_Element_Radios constructor.
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setType('radios');
    }

    /**
     * @return string
     */
    public function getSeparator()
    {
        $separator = $this->getData('separator');
        if (is_null($separator)) {
            $separator = '&nbsp;';
        }
        return $separator;
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        $html = '';
        $value = $this->getValue();
        if ($values = $this->getValues()) {
            foreach ($values as $option) {
                $html .= $this->_optionToHtml($option, $value);
            }
        }
        return $html . $this->getAfterElementHtml();
    }

    /**
     * @param array|Varien_Object $option
     * @param $selected
     * @return string
     */
    protected function _optionToHtml($option, $selected)
    {
        $html = '<input type="radio"' . $this->serialize(['name', 'class', 'style']);
        if (is_array($option)) {
            $html .= 'value="' . $this->_escape($option['value']) . '"  id="' . $this->getHtmlId() . $option['value'] . '"';
            if ($option['value'] == $selected) {
                $html .= ' checked="checked"';
            }
            $html .= ' />';
            $html .= '<label class="inline" for="' . $this->getHtmlId() . $option['value'] . '">' . $option['label'] . '</label>';
        } elseif ($option instanceof Varien_Object) {
            $html .= 'id="' . $this->getHtmlId() . $option->getValue() . '"' . $option->serialize(['label', 'title', 'value', 'class', 'style']);
            if (in_array($option->getValue(), $selected)) {
                $html .= ' checked="checked"';
            }
            $html .= ' />';
            $html .= '<label class="inline" for="' . $this->getHtmlId() . $option->getValue() . '">' . $option->getLabel() . '</label>';
        }
        return $html . ($this->getSeparator() . "\n");
    }
}
