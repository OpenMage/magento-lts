<?php
/**
 * Renderer for sub-heading in fieldset
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Config_Form_Field_Heading extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Render element html
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return sprintf(
            '<tr class="system-fieldset-sub-head" id="row_%s"><td colspan="5"><h4 id="%s">%s</h4></td></tr>',
            $element->getHtmlId(),
            $element->getHtmlId(),
            $element->getLabel(),
        );
    }
}
