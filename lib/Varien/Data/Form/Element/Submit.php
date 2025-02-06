<?php

/**
 * @category   Varien
 * @package    Varien_Data
 */

/**
 * Form submit element
 *
 * @category   Varien
 * @package    Varien_Data
 */
class Varien_Data_Form_Element_Submit extends Varien_Data_Form_Element_Abstract
{
    /**
     * Varien_Data_Form_Element_Submit constructor.
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setExtType('submit');
        $this->setType('submit');
    }

    /**
     * @return string
     */
    public function getHtml()
    {
        $this->addClass('submit');
        return parent::getHtml();
    }
}
