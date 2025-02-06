<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Bundle
 */

/**
 * Bundle Special Price Attribute Block
 *
 * @category   Mage
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
