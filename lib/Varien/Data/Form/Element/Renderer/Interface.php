<?php

/**
 * @category   Varien
 * @package    Varien_Data
 */

/**
 * Form field renderer
 *
 * @category   Varien
 * @package    Varien_Data
 */
interface Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * @return mixed
     */
    public function render(Varien_Data_Form_Element_Abstract $element);
}
