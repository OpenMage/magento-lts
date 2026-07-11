<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Renderer for specific checkbox that is used on Rule Information tab in Shopping cart price rules
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Main_Renderer_Checkbox extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Checkbox render function
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $checkbox = new Varien_Data_Form_Element_Checkbox($element->getData());
        $checkbox->setForm($element->getForm());

        $elementHtml = $checkbox->getElementHtml() . sprintf(
            '<label for="%s"><b>%s</b></label><p class="note">%s</p>',
            $element->getHtmlId(),
            $element->getLabel(),
            $element->getNote(),
        );
        $html  = '<td class="label">&nbsp;</td>';

        return $html . ('<td class="value">' . $elementHtml . '</td>');
    }
}
