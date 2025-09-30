<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml additional helper block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Config extends Varien_Data_Form_Element_Select
{
    /**
     * Retrieve element html
     *
     * @return string
     */
    public function getElementHtml()
    {
        $value = $this->getValue();
        if ($value == '') {
            $this->setValue($this->_getValueFromConfig());
        }
        $html = parent::getElementHtml();

        $htmlId   = 'use_config_' . $this->getHtmlId();
        $checked  = ($value == '') ? ' checked="checked"' : '';
        $disabled = ($this->getReadonly()) ? ' disabled="disabled"' : '';

        $html .= '<input id="' . $htmlId . '" name="product[' . $htmlId . ']" ' . $disabled . ' value="1" ' . $checked;
        $html .= ' onclick="toggleValueElements(this, this.parentNode);" class="checkbox" type="checkbox" />';
        $html .= ' <label for="' . $htmlId . '">' . Mage::helper('adminhtml')->__('Use Config Settings') . '</label>';

        return $html . ('<script type="text/javascript">toggleValueElements($(\'' . $htmlId . '\'), $(\'' . $htmlId . '\').parentNode);</script>');
    }

    /**
     * Get config value data
     *
     * @return mixed
     */
    protected function _getValueFromConfig()
    {
        return '';
    }
}
