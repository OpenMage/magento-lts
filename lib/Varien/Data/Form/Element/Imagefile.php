<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Data
 */

/**
 * Form image file element
 *
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
