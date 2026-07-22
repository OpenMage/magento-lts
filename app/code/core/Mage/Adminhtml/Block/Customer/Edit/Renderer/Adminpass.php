<?php

declare(strict_types=1);

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
(function() {
    var newPassEl  = document.getElementById('_accountnew_password');
    var sendPassEl = document.getElementById('account-send-pass');

    function getContainer() { return document.getElementById('{$element->getHtmlId()}_container'); }
    function getInput()     { return document.getElementById('{$element->getHtmlId()}'); }

    function showAdminPass() {
        var c = getContainer(), i = getInput();
        if (c) { c.style.display = ''; }
        if (i) { i.disabled = false; }
    }

    function hideAdminPass() {
        var c = getContainer(), i = getInput();
        if (c) { c.style.display = 'none'; }
        if (i) { i.disabled = true; }
    }

    function onChange() {
        var hasNewPass = newPassEl && newPassEl.value;
        var sendPass   = sendPassEl && sendPassEl.checked;
        if (hasNewPass || sendPass) {
            showAdminPass();
        } else {
            hideAdminPass();
        }
        var warning = document.getElementById('email-passowrd-warning');
        if (warning) {
            warning.style.display = (!hasNewPass || sendPass) ? 'none' : '';
        }
    }

    function onBlur() {
        if ((!newPassEl || !newPassEl.value) && (!sendPassEl || !sendPassEl.checked)) {
            hideAdminPass();
        }
    }

    [newPassEl, sendPassEl].forEach(function(elem) {
        if (!elem) { return; }
        elem.addEventListener('change', onChange);
        elem.addEventListener('focus', showAdminPass);
        elem.addEventListener('blur', onBlur);
    });

    document.addEventListener('DOMContentLoaded', hideAdminPass);
})();
//]]></script>
SCRIPT;
    }
}
