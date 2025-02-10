<?php
/**
 * Form field renderer
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @license Open Software License (OSL 3.0)
 * @package    Varien_Data
 */
/**
 * @package    Varien_Data
 */


interface Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * @return mixed
     */
    public function render(Varien_Data_Form_Element_Abstract $element);
}
