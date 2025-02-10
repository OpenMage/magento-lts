<?php
/**
 * Widget Instance template chooser
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Widget
 * @method string getSelected()
 * @method $this setSelected(string $value)
 * @method array getWidgetTemplates()
 * @method $this setWidgetTemplates(array $value)
 */
class Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Chooser_Template extends Mage_Adminhtml_Block_Widget
{
    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->getWidgetTemplates()) {
            $html = '<p class="nm"><small>' . Mage::helper('widget')->__('Please Select Block Reference First') . '</small></p>';
        } elseif (count($this->getWidgetTemplates()) == 1) {
            $widgetTemplate = current($this->getWidgetTemplates());
            $html = '<input type="hidden" name="template" value="' . $widgetTemplate['value'] . '" />';
            $html .= $widgetTemplate['label'];
        } else {
            $html = $this->getLayout()->createBlock('core/html_select')
                ->setName('template')
                ->setClass('select')
                ->setOptions($this->getWidgetTemplates())
                ->setValue($this->getSelected())->toHtml();
        }
        return parent::_toHtml() . $html;
    }
}
