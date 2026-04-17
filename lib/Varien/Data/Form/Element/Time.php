<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Data
 */

/**
 * Form time element
 *
 * @package    Varien_Data
 */
class Varien_Data_Form_Element_Time extends Varien_Data_Form_Element_Abstract
{
    /**
     * Varien_Data_Form_Element_Time constructor.
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setType('time');
    }

    /**
     * @return string
     */
    public function getName()
    {
        $name = parent::getName();
        if (!str_contains($name, '[]')) {
            $name .= '[]';
        }

        return $name;
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        $this->addClass('select');

        $valueHrs = 0;
        $valueMin = 0;
        $valueSec = 0;

        if ($value = $this->getValue()) {
            $values = explode(',', $value);
            if (count($values) === 3) {
                $valueHrs = $values[0];
                $valueMin = $values[1];
                $valueSec = $values[2];
            }
        }

        $html = '<input type="hidden" id="' . $this->getHtmlId() . '" />';
        $html .= '<select name="' . $this->getName() . '" ' . $this->serialize($this->getHtmlAttributes()) . ' style="width:40px">' . "\n";
        for ($index = 0; $index < 24; $index++) {
            $hour = str_pad((string) $index, 2, '0', STR_PAD_LEFT);
            $html .= '<option value="' . $hour . '" ' . (($valueHrs == $index) ? 'selected="selected"' : '') . '>' . $hour . '</option>';
        }

        $html .= '</select>' . "\n";

        $html .= '&nbsp;:&nbsp;<select name="' . $this->getName() . '" ' . $this->serialize($this->getHtmlAttributes()) . ' style="width:40px">' . "\n";
        for ($index = 0; $index < 60; $index++) {
            $hour = str_pad((string) $index, 2, '0', STR_PAD_LEFT);
            $html .= '<option value="' . $hour . '" ' . (($valueMin == $index) ? 'selected="selected"' : '') . '>' . $hour . '</option>';
        }

        $html .= '</select>' . "\n";

        $html .= '&nbsp;:&nbsp;<select name="' . $this->getName() . '" ' . $this->serialize($this->getHtmlAttributes()) . ' style="width:40px">' . "\n";
        for ($index = 0; $index < 60; $index++) {
            $hour = str_pad((string) $index, 2, '0', STR_PAD_LEFT);
            $html .= '<option value="' . $hour . '" ' . (($valueSec == $index) ? 'selected="selected"' : '') . '>' . $hour . '</option>';
        }

        $html .= '</select>' . "\n";
        return $html . $this->getAfterElementHtml();
    }
}
