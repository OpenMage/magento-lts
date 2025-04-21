<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Data
 */

/**
 * Data form abstract class
 *
 * @package    Varien_Data
 *
 * @method bool getBold()
 */
class Varien_Data_Form_Element_Label extends Varien_Data_Form_Element_Abstract
{
    /**
     * Assigns attributes for Element
     *
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setType('label');
    }

    /**
     * Retrieve Element HTML
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = $this->getBold() ? '<strong>' : '';
        $html .= $this->getEscapedValue();
        $html .= $this->getBold() ? '</strong>' : '';
        return $html . $this->getAfterElementHtml();
    }
}
