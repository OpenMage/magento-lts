<?php

/**
 * @category   Varien
 * @package    Varien_Data
 */

/**
 * Form radio element
 *
 * @category   Varien
 * @package    Varien_Data
 */
class Varien_Data_Form_Element_Radio extends Varien_Data_Form_Element_Abstract
{
    /**
     * Varien_Data_Form_Element_Radio constructor.
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setType('radio');
        $this->setExtType('radio');
    }
}
