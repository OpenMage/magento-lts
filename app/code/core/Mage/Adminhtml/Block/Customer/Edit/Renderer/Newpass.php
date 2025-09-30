<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Customer new password field renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Edit_Renderer_Newpass extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Render block
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html  = '<tr>';
        $html .= '<td class="label">' . $element->getLabelHtml() . '</td>';
        $html .= '<td class="value">' . $element->getElementHtml();
        if ($element->getNote()) {
            $html .= '<p class="note"><span>' . $element->getNote() . '</span></p>';
        }
        $html .= '<p id="email-passowrd-warning" style="display:none;" class="note"><span>' . Mage::helper('customer')->__('Warning: email containing password in plaintext will be sent.') . '</span></p>';
        $html .= '</td>';
        $html .= '</tr>' . "\n";
        $html .= '<tr>';
        $html .= '<td class="label"><label>&nbsp;</label></td>';
        $html .= '<td class="value">' . Mage::helper('customer')->__('or') . '</td>';
        $html .= '</tr>' . "\n";
        $html .= '<tr>';
        $html .= '<td class="label"><label>&nbsp;</label></td>';
        $html .= '<td class="value"><input type="checkbox" id="account-send-pass" name="'
            . $element->getName()
            . '" value="auto" onclick="setElementDisable(\''
            . $element->getHtmlId()
            . '\', this.checked)"/>&nbsp;';
        $html .= '<label for="account-send-pass">'
            . Mage::helper('customer')->__('Email Link to Set Password')
            . '</label></td>';

        return $html . ('</tr>' . "\n");
    }
}
