<?php

/**
 * @category   Varien
 * @package    Varien_Data
 */

/**
 * Form button element
 *
 * @category   Varien
 * @package    Varien_Data
 */
class Varien_Data_Form_Element_Button extends Varien_Data_Form_Element_Abstract
{
    /**
     * Varien_Data_Form_Element_Button constructor.
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setType('button');
        $this->setExtType('textfield');
    }
}
