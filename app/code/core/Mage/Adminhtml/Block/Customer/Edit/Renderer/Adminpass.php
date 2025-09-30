<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Current admin password field renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Edit_Renderer_Adminpass extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Render block
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html  = '<tr id="' . $element->getHtmlId() . '_container">';
        $html .= '<td class="label">' . $element->getLabelHtml() . '</td>';
        $html .= '<td class="value">' . $element->getElementHtml() . ' ' . $this->_getScriptHtml($element) . '</td>';
        $html .= '</tr>' . "\n";

        return $html . '<tr>';
    }

    /**
     * @return string
     */
    protected function _getScriptHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return <<<SCRIPT
<script type="text/javascript">
//<![CDATA[
    $$('#_accountnew_password,#account-send-pass').each(function(elem) {
        $(elem).on('change', function() {
            if ($('_accountnew_password').getValue() || $('account-send-pass').checked) {
                $('{$element->getHtmlId()}_container').show();
                $('{$element->getHtmlId()}').enable();
            } else {
                $('{$element->getHtmlId()}_container').hide();
                $('{$element->getHtmlId()}').disable();
            }
            if ($('email-passowrd-warning')) {
                if (!$('_accountnew_password').getValue() || $('account-send-pass').checked) {
                    $('email-passowrd-warning').hide();
                } else if ($('_accountnew_password').getValue()) {
                    $('email-passowrd-warning').show();
                }
            }
        });
        $(elem).on('focus', function() {
            $('{$element->getHtmlId()}_container').show();
            $('{$element->getHtmlId()}').enable();
        });
        $(elem).on('blur', function() {
            if (!$('_accountnew_password').getValue() && !$('account-send-pass').checked) {
                $('{$element->getHtmlId()}_container').hide();
                $('{$element->getHtmlId()}').disable();
            }
        });
        document.observe("dom:loaded", function() {
            $('{$element->getHtmlId()}_container').hide();
            $('{$element->getHtmlId()}').disable();
        });
    });
//]]></script>
SCRIPT;
    }
}
