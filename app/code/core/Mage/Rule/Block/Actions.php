<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @license Open Software License (OSL 3.0)
 * @package    Mage_Rule
 */
class Mage_Rule_Block_Actions implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        if ($element->getRule() && $element->getRule()->getActions()) {
            return $element->getRule()->getActions()->asHtmlRecursive();
        }
        return '';
    }
}
