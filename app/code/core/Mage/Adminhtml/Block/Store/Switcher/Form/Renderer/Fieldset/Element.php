<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Form fieldset renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Store_Switcher_Form_Renderer_Fieldset_Element extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Form element which re-rendering
     *
     * @var Varien_Data_Form_Element_Abstract
     */
    protected $_element;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->setTemplate('store/switcher/form/renderer/fieldset/element.phtml');
    }

    /**
     * Retrieve an element
     *
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Render element
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this->toHtml();
    }
}
