<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Fieldset element renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element extends Mage_Adminhtml_Block_Template implements Varien_Data_Form_Element_Renderer_Interface
{
    protected $_element;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->setTemplate('widget/form/renderer/fieldset/element.phtml');
    }

    public function getElement()
    {
        return $this->_element;
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this->toHtml();
    }
}
