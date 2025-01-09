<?php

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml additional helper block for sort by
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Category_Helper_Pricestep extends Varien_Data_Form_Element_Text
{
    /**
     * Returns js code that is used instead of default toggle code for "Use default config" checkbox
     *
     * @return string
     */
    public function getToggleCode()
    {
        $htmlId = 'use_config_' . $this->getHtmlId();
        return 'toggleValueElements(this, this.parentNode.parentNode);'
            . "if (!this.checked) toggleValueElements($('$htmlId'), $('$htmlId').parentNode);";
    }

    /**
     * Retrieve Element HTML fragment
     *
     * @return string
     */
    public function getElementHtml()
    {
        $elementDisabled = $this->getDisabled() == 'disabled';
        $disabled = false;

        if (!$this->getValue() || $elementDisabled) {
            $this->setData('disabled', 'disabled');
            $disabled = true;
        }

        parent::addClass('validate-number validate-number-range number-range-0.01-1000000000');
        $html = parent::getElementHtml();
        $htmlId = 'use_config_' . $this->getHtmlId();
        $html .= '<br/><input id="' . $htmlId . '" name="use_config[]" value="' . $this->getId() . '"';
        $html .= ($disabled ? ' checked="checked"' : '');

        if ($this->getReadonly() || $elementDisabled) {
            $html .= ' disabled="disabled"';
        }

        $html .= ' onclick="toggleValueElements(this, this.parentNode);" class="checkbox" type="checkbox" />';

        $html .= ' <label for="' . $htmlId . '" class="normal">'
            . Mage::helper('adminhtml')->__('Use Config Settings') . '</label>';

        return $html . ('<script type="text/javascript">' . 'toggleValueElements($(\'' . $htmlId . '\'), $(\'' . $htmlId
            . '\').parentNode);' . '</script>');
    }
}
