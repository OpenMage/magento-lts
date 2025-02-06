<?php

/**
 * @category   Varien
 * @package    Varien_Data
 */

/**
 * Form image file element
 *
 * @category   Varien
 * @package    Varien_Data
 *
 * @method $this setAutosubmit(bool $false)
 */
class Varien_Data_Form_Element_Imagefile extends Varien_Data_Form_Element_Abstract
{
    /**
     * Varien_Data_Form_Element_Imagefile constructor.
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setType('file');
        $this->setExtType('imagefile');
        $this->setAutosubmit(false);
        $this->setData('autoSubmit', false);
        //$this->setExtType('file');
    }
}
