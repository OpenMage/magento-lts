<?php
/**
 * Text grid column filter
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Text extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Abstract
{
    public function getHtml()
    {
        return '<div class="field-100"><input type="text" name="' . $this->_getHtmlName() . '" id="' . $this->_getHtmlId() . '" value="' . $this->getEscapedValue() . '" class="input-text no-changes"/></div>';
    }
}
