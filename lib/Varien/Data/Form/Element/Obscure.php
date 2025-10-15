<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Data
 */

/**
 * Form text element
 *
 * @package    Varien_Data
 */
class Varien_Data_Form_Element_Obscure extends Varien_Data_Form_Element_Password
{
    /**
     * @var string
     */
    protected $_obscuredValue = '******';

    /**
     * Hide value to make sure it will not show in HTML
     *
     * @param string $index
     * @return string
     */
    public function getEscapedValue($index = null)
    {
        $value = parent::getEscapedValue($index);
        if (!empty($value)) {
            return $this->_obscuredValue;
        }

        return $value;
    }

    /**
     * Returns list of html attributes possible to output in HTML
     *
     * @return array
     */
    public function getHtmlAttributes()
    {
        return ['type', 'title', 'class', 'style', 'onclick', 'onchange', 'onkeyup', 'disabled', 'readonly', 'maxlength', 'tabindex'];
    }
}
