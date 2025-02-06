<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Button widget
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Button extends Mage_Adminhtml_Block_Widget
{
    public function getType()
    {
        return ($type = $this->getData('type')) ? $type : 'button';
    }

    public function getOnClick()
    {
        if (!$this->getData('on_click')) {
            return $this->getData('onclick');
        }
        return $this->getData('on_click');
    }

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
            . ' style="' . $this->getStyle() . '"'
            . ($this->getValue() ? ' value="' . $this->getValue() . '"' : '')
            . ($this->getDisabled() ? ' disabled="disabled"' : '')
            . '><span><span><span>' . $this->getLabel() . '</span></span></span></button>' . $this->getAfterHtml();
    }
}
