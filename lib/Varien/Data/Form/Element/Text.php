<?php

/**
 * @category   Varien
 * @package    Varien_Data
 */

/**
 * Form text element
 *
 * @category   Varien
 * @package    Varien_Data
 */
class Varien_Data_Form_Element_Text extends Varien_Data_Form_Element_Abstract
{
    /**
     * Varien_Data_Form_Element_Text constructor.
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setType('text');
        $this->setExtType('textfield');
    }

    /**
     * @return string
     */
    public function getHtml()
    {
        $this->addClass('input-text');
        return parent::getHtml();
    }

    /**
     * @return array
     */
    public function getHtmlAttributes()
    {
        return ['type', 'title', 'class', 'style', 'onclick', 'onchange', 'onkeyup', 'disabled', 'readonly', 'maxlength', 'tabindex'];
    }
}
