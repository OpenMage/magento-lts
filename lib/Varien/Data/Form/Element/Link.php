<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Data
 */

/**
 * Varien Form element renderer to display link element
 *
 * @package    Varien_Data
 *
 * @method string getBeforeElementHtml()
 */
class Varien_Data_Form_Element_Link extends Varien_Data_Form_Element_Abstract
{
    /**
     * Varien_Data_Form_Element_Link constructor.
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setType('link');
    }

    /**
     * Generates element html
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = $this->getBeforeElementHtml();
        $html .= '<a id="' . $this->getHtmlId() . '" ' . $this->serialize($this->getHtmlAttributes()) . '>' . $this->getEscapedValue() . "</a>\n";
        return $html . $this->getAfterElementHtml();
    }

    /**
     * Prepare array of anchor attributes
     *
     * @return array
     */
    public function getHtmlAttributes()
    {
        return ['charset', 'coords', 'href', 'hreflang', 'rel', 'rev', 'name',
            'shape', 'target', 'accesskey', 'class', 'dir', 'lang', 'style',
            'tabindex', 'title', 'xml:lang', 'onblur', 'onclick', 'ondblclick',
            'onfocus', 'onmousedown', 'onmousemove', 'onmouseout', 'onmouseover',
            'onmouseup', 'onkeydown', 'onkeypress', 'onkeyup'];
    }
}
