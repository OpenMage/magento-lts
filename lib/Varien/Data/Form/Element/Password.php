<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Data
 */

/**
 * Form password element
 *
 * @package    Varien_Data
 */
class Varien_Data_Form_Element_Password extends Varien_Data_Form_Element_Abstract
{
    /**
     * Varien_Data_Form_Element_Password constructor.
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setType('password');
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
}
