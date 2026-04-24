<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Button widget
 *
 * @package    Mage_Adminhtml
 *
 * @method string getAfterHtml()
 * @method string getBeforeHtml()
 * @method string getClass()
 * @method bool   getDisabled()
 * @method string getElementName()
 * @method string getLabel()
 * @method string getStyle()
 * @method string getTitle()
 * @method string getValue()
 */
class Mage_Adminhtml_Block_Widget_Button extends Mage_Adminhtml_Block_Widget
{
    public function getType()
    {
        return ($type = $this->getDataByKey('type')) ? $type : 'button';
    }

    public function getOnClick()
    {
        if (!$this->getDataByKey('on_click')) {
            return $this->getDataByKey('onclick');
        }

        return $this->getDataByKey('on_click');
    }

    public function getTestId(): ? string
    {
        if ($this->getDataByKey('id')) {
            return 'admin-button-' . str_replace('_', '-', $this->getDataByKey('id'));
        }

        return null;
    }

    #[Override]
    protected function _toHtml()
    {
        return $this->getBeforeHtml() . '<button '
            . ($this->getId() ? ' id="' . $this->getId() . '"' : '')
            . ($this->getElementName() ? ' name="' . $this->getElementName() . '"' : '')
            . ' title="'
            . Mage::helper('core')->quoteEscape($this->getTitle() ? $this->getTitle() : $this->getLabel())
            . '"'
            . ' type="' . $this->getType() . '"'
            . ' class="scalable ' . $this->getClass() . ($this->getDisabled() ? ' disabled' : '') . '"'
            . ' onclick="' . $this->getOnClick() . '"'
            . ($this->getStyle() ? ' style="' . $this->getStyle() . '"' : '')
            . ($this->getTestId() ? ' data-test="' . $this->getTestId() . '"' : '')
            . ($this->getValue() ? ' value="' . $this->getValue() . '"' : '')
            . ($this->getDisabled() ? ' disabled="disabled"' : '')
            . '><span><span><span>' . $this->getLabel() . '</span></span></span></button>' . $this->getAfterHtml();
    }
}
