<?php

/**
 * @category   Varien
 * @package    Varien_Data
 */

/**
 * Form column
 *
 * @category   Varien
 * @package    Varien_Data
 */
class Varien_Data_Form_Element_Column extends Varien_Data_Form_Element_Abstract
{
    /**
     * Varien_Data_Form_Element_Column constructor.
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setType('column');
    }
}
