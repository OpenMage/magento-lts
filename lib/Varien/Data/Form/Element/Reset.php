<?php

/**
 * @category   Varien
 * @package    Varien_Data
 */

/**
 * Form relset element
 *
 * @category   Varien
 * @package    Varien_Data
 */
class Varien_Data_Form_Element_Reset extends Varien_Data_Form_Element_Abstract
{
    /**
     * Varien_Data_Form_Element_Reset constructor.
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setType('text');
        $this->setExtType('textfield');
    }
}
