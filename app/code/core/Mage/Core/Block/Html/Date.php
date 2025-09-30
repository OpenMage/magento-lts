<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * HTML select element block
 *
 * @package    Mage_Core
 *
 * @method string getClass()
 * @method $this setClass(string $value)
 * @method string getExtraParams()
 * @method $this setExtraParams(string $value)
 * @method string getFormat()
 * @method $this setFormat(string $value)
 * @method string getImage()
 * @method $this setImage(string $value)
 * @method string getName()
 * @method $this setName(string $value)
 * @method string getTime()
 * @method $this setTime(string $value)
 * @method $this setTitle(string $value)
 * @method string getValue()
 * @method $this setValue(string $value)
 * @method string getYearsRange()
 * @method $this setYearsRange(string $value)
 */
class Mage_Core_Block_Html_Date extends Mage_Core_Block_Template
{
    /**
     * @return string
     */
    protected function _toHtml()
    {
        $displayFormat = Varien_Date::convertZendToStrftime($this->getFormat(), true, (bool) $this->getTime());

        $html  = '<input type="text" name="' . $this->getName() . '" id="' . $this->getId() . '" ';
        $html .= 'value="' . $this->escapeHtml($this->getValue()) . '" class="' . $this->getClass() . '" ' . $this->getExtraParams() . '/> ';

        $html .= '<img src="' . $this->getImage() . '" alt="' . $this->helper('core')->__('Select Date') . '" class="v-middle" ';
        $html .= 'title="' . $this->helper('core')->__('Select Date') . '" id="' . $this->getId() . '_trig" />';

        $html .=
        '<script type="text/javascript">
        //<![CDATA[
            var calendarSetupObject = {
                inputField  : "' . $this->getId() . '",
                ifFormat    : "' . $displayFormat . '",
                showsTime   : ' . ($this->getTime() ? 'true' : 'false') . ',
                button      : "' . $this->getId() . '_trig",
                align       : "Bl",
                singleClick : true
            }';

        $calendarYearsRange = $this->getYearsRange();
        if ($calendarYearsRange) {
            $html .= '
                calendarSetupObject.range = ' . $calendarYearsRange . '
                ';
        }

        return $html . '
            Calendar.setup(calendarSetupObject);
        //]]>
        </script>';
    }

    /**
     * @param null $index deprecated
     * @return string
     */
    public function getEscapedValue($index = null)
    {
        if ($this->getFormat() && $this->getValue()) {
            return date($this->getFormat(), strtotime($this->getValue()));
        }

        return htmlspecialchars($this->getValue());
    }

    /**
     * @return string
     */
    public function getHtml()
    {
        return $this->toHtml();
    }
}
