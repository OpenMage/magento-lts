<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Current admin password field renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Renderer_Adminpass
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Render block
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html  = '<tr id="' . $element->getHtmlId() . '_container">';
        $html .= '<td class="label">' . $element->getLabelHtml() . '</td>';
        $html .= '<td class="value">' . $element->getElementHtml() . ' ' . $this->_getScriptHtml($element) . '</td>';
        $html .= '</tr>' . "\n";
        $html .= '<tr>';

        return $html;
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
