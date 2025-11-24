<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Template text preview field renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_Newsletter_Renderer_Text implements Varien_Data_Form_Element_Renderer_Interface
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '<tr><td class="label">' . "\n";
        if ($element->getLabel()) {
            $html .= '<label for="' . $element->getHtmlId() . '">' . $element->getLabel() . '</label>' . "\n";
        }

        $html .= '</td><td class="value">
<iframe src="' . $element->getValue() . '" id="' . $element->getHtmlId() . '" frameborder="0" class="template-preview"> </iframe>';

        return $html . ('</td><td></td></tr>' . "\n");
    }
}
