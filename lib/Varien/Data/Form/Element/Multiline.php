<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Data
 */

/**
 * Form multiline text elements
 *
 *
 * @method int getLineCount()
 * @method $this setLineCount(int $value)
 */
class Varien_Data_Form_Element_Multiline extends Varien_Data_Form_Element_Abstract
{
    /**
     * Varien_Data_Form_Element_Multiline constructor.
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setType('text');
        $this->setLineCount(2);
    }

    /**
     * @return array
     */
    public function getHtmlAttributes()
    {
        return ['type', 'title', 'class', 'style', 'onclick', 'onchange', 'disabled', 'maxlength'];
    }

    /**
     * @param int $suffix
     * @return string
     */
    public function getLabelHtml($suffix = 0)
    {
        return parent::getLabelHtml($suffix);
    }

    /**
     * Get element HTML
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = '';
        $lineCount = $this->getLineCount();

        for ($i = 0; $i < $lineCount; $i++) {
            if ($i == 0 && $this->getRequired()) {
                $this->setClass('input-text required-entry');
            } else {
                $this->setClass('input-text');
            }
            $html .= '<div class="multi-input"><input id="' . $this->getHtmlId() . $i . '" name="' . $this->getName()
                . '[' . $i . ']' . '" value="' . $this->getEscapedValue($i) . '" '
                . $this->serialize($this->getHtmlAttributes()) . ' />' . "\n";
            if ($i == 0) {
                $html .= $this->getAfterElementHtml();
            }
            $html .= '</div>';
        }
        return $html;
    }

    /**
     * @return string
     */
    public function getDefaultHtml()
    {
        $html = '';
        $lineCount = $this->getLineCount();

        for ($i = 0; $i < $lineCount; $i++) {
            $html .= ($this->getNoSpan() === true) ? '' : '<span class="field-row">' . "\n";
            if ($i == 0) {
                $html .= '<label for="' . $this->getHtmlId() . $i . '">' . $this->getLabel()
                    . ($this->getRequired() ? ' <span class="required">*</span>' : '') . '</label>' . "\n";
                if ($this->getRequired()) {
                    $this->setClass('input-text required-entry');
                }
            } else {
                $this->setClass('input-text');
                $html .= '<label>&nbsp;</label>' . "\n";
            }
            $html .= '<input id="' . $this->getHtmlId() . $i . '" name="' . $this->getName() . '[' . $i . ']'
                . '" value="' . $this->getEscapedValue($i) . '"' . $this->serialize($this->getHtmlAttributes()) . ' />' . "\n";
            if ($i == 0) {
                $html .= $this->getAfterElementHtml();
            }
            $html .= ($this->getNoSpan() === true) ? '' : '</span>' . "\n";
        }
        return $html;
    }
}
