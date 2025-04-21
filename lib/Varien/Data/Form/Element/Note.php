<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Data
 */

/**
 * Form note element
 *
 * @package    Varien_Data
 *
 * @method string getText()
 */
class Varien_Data_Form_Element_Note extends Varien_Data_Form_Element_Abstract
{
    /**
     * Varien_Data_Form_Element_Note constructor.
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setType('note');
        //$this->setExtType('textfield');
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        $html = '<span id="' . $this->getHtmlId() . '">' . $this->getText() . '</span>';
        return $html . $this->getAfterElementHtml();
    }
}
