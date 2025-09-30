<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

/**
 * Bundle Special Price Attribute Block
 *
 * @package    Mage_Bundle
 *
 * @method $this setDisableChild(bool $value)
 */
class Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes_Special extends Mage_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element
{
    /**
     * @return string
     */
    public function getElementHtml()
    {
        return '<input id="' . $this->getElement()->getHtmlId() . '" name="' . $this->getElement()->getName()
             . '" value="' . $this->getElement()->getEscapedValue() . '" ' . $this->getElement()->serialize($this->getElement()->getHtmlAttributes()) . '/>' . "\n"
             . '<strong>[%]</strong>';
    }
}
