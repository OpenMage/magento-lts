<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/**
 * Abstract base class for USPS admin config test buttons
 *
 * Provides shared HTML button generation, credential gathering JS,
 * and AJAX call infrastructure. Subclasses define only the unique
 * parts (button label, URL, success handler).
 *
 * @package    Mage_Usa
 */
abstract class Mage_Usa_Block_Adminhtml_System_Config_Form_Field_Usps_AbstractTestButton extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * HTML id attribute for the button element
     */
    abstract protected function _getButtonId(): string;

    /**
     * Translated button label text
     */
    abstract protected function _getButtonLabel(): string;

    /**
     * Admin route for the AJAX endpoint (e.g. 'adminhtml/usps/testconnection')
     */
    abstract protected function _getAjaxRoute(): string;

    /**
     * HTML id attribute for the result container div
     */
    abstract protected function _getResultDivId(): string;

    /**
     * Text shown while AJAX request is in-flight
     */
    abstract protected function _getLoadingText(): string;

    /**
     * JavaScript function body for AJAX onSuccess callback.
     * Available variables: response, result (parsed JSON), resultDiv, button.
     */
    abstract protected function _getOnSuccessJs(): string;

    /**
     * Text shown when AJAX request fails. Override for custom message.
     */
    protected function _getFailureText(): string
    {
        return 'Request failed.';
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element): string
    {
        $buttonId = $this->_getButtonId();
        $buttonLabel = $this->_getButtonLabel();
        $ajaxUrl = Mage::helper('adminhtml')::getUrl($this->_getAjaxRoute());
        $resultDivId = $this->_getResultDivId();
        $loadingText = $this->jsQuoteEscape($this->_getLoadingText());
        $failureText = $this->jsQuoteEscape($this->_getFailureText());
        $website = $this->getRequest()->getParam('website', '');
        $store = $this->getRequest()->getParam('store', '');

        $html = '<button type="button" id="' . $this->escapeHtml($buttonId) . '"'
              . ' data-ajax-url="' . $this->escapeUrl($ajaxUrl) . '"'
              . ' data-website="' . $this->escapeHtml($website) . '"'
              . ' data-store="' . $this->escapeHtml($store) . '"'
              . ' class="scalable">'
              . '<span>' . $buttonLabel . '</span></button>';
        $html .= '<div id="' . $this->escapeHtml($resultDivId) . '" style="margin-top:10px;"></div>';

        $onSuccessJs = $this->_getOnSuccessJs();

        return $html . <<<JAVASCRIPT
<script type="text/javascript">
//<![CDATA[
document.observe('dom:loaded', function() {
    var button = document.getElementById('{$buttonId}');
    if (button) {
        button.onclick = function() {
            var url = this.getAttribute('data-ajax-url');
            var website = this.getAttribute('data-website');
            var store = this.getAttribute('data-store');

            var clientId = '';
            var clientSecret = '';
            var environment = '';

            var clientIdField = document.getElementById('carriers_usps_client_id');
            var clientSecretField = document.getElementById('carriers_usps_client_secret');
            var environmentField = document.getElementById('carriers_usps_environment');

            if (clientIdField) clientId = clientIdField.value;
            if (clientSecretField) clientSecret = clientSecretField.value;
            if (environmentField) environment = environmentField.value;

            if (!clientId || !clientSecret || !environment) {
                document.getElementById('{$resultDivId}').innerHTML =
                    '<span style="color:red;">Please fill in Client ID, Client Secret, and Environment.</span>';
                return;
            }

            var resultDiv = document.getElementById('{$resultDivId}');
            resultDiv.innerHTML = '<span style="color:gray;">{$loadingText}</span>';
            button.disabled = true;

            new Ajax.Request(url, {
                parameters: {
                    client_id: clientId,
                    client_secret: clientSecret,
                    environment: environment,
                    website: website,
                    store: store,
                    form_key: FORM_KEY
                },
                onSuccess: function(response) {
                    button.disabled = false;
                    try {
                        var result = JSON.parse(response.responseText);
                        {$onSuccessJs}
                    } catch(e) {
                        resultDiv.innerHTML = '<span style="color:red;">Error: ' + e.message + '</span>';
                    }
                },
                onFailure: function() {
                    button.disabled = false;
                    resultDiv.innerHTML = '<span style="color:red;">{$failureText}</span>';
                }
            });
        };
    }
});
//]]>
</script>
JAVASCRIPT;
    }

    protected function _renderScopeLabel(Varien_Data_Form_Element_Abstract $element): string
    {
        return '';
    }
}
