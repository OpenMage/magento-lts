<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/**
 * USPS Test Connection Button Backend Model
 *
 * Renders a button in system configuration to test USPS REST API connectivity
 * Tests OAuth2 authentication with provided credentials
 *
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_Usps_Backend_Testconnection
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Generate button HTML for testing USPS API connection
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $buttonLabel = Mage::helper('usa')->__('Test Connection');
        $ajaxUrl = Mage::helper('adminhtml')->getUrl('adminhtml/usps/testconnection');

        $html = '<button type="button" id="usps-test-connection-button" onclick="testUspsConnection(\'' . $ajaxUrl . '\')" class="scalable">'
              . '<span>' . $buttonLabel . '</span></button>';
        $html .= '<div id="usps-test-result" style="margin-top:10px; font-weight:bold;"></div>';

        // JavaScript for AJAX call
        $html .= <<<'JAVASCRIPT'
<script type="text/javascript">
//<![CDATA[
function testUspsConnection(url) {
    // Get credentials from form
    var clientId = '';
    var clientSecret = '';
    var environment = '';

    // Try to get values from obscure inputs (encrypted fields)
    var clientIdField = document.getElementById('carriers_usps_client_id');
    var clientSecretField = document.getElementById('carriers_usps_client_secret');
    var environmentField = document.getElementById('carriers_usps_environment');

    if (clientIdField) {
        clientId = clientIdField.value;
    }
    if (clientSecretField) {
        clientSecret = clientSecretField.value;
    }
    if (environmentField) {
        environment = environmentField.value;
    }

    // Validate required fields
    if (!clientId || !clientSecret || !environment) {
        document.getElementById('usps-test-result').innerHTML =
            '<span style="color:red;">✗ Please fill in Client ID, Client Secret, and Environment before testing.</span>';
        return;
    }

    // Show loading message
    var resultDiv = document.getElementById('usps-test-result');
    resultDiv.innerHTML = '<span style="color:gray;">Testing connection...</span>';

    // Disable button during request
    var button = document.getElementById('usps-test-connection-button');
    button.disabled = true;

    // Make AJAX request
    new Ajax.Request(url, {
        parameters: {
            client_id: clientId,
            client_secret: clientSecret,
            environment: environment,
            form_key: FORM_KEY
        },
        onSuccess: function(response) {
            button.disabled = false;
            try {
                var result = JSON.parse(response.responseText);
                if (result.success) {
                    resultDiv.innerHTML = '<span style="color:green;">✓ ' + result.message + '</span>';
                } else {
                    resultDiv.innerHTML = '<span style="color:red;">✗ ' + result.message + '</span>';
                }
            } catch(e) {
                resultDiv.innerHTML = '<span style="color:red;">✗ Error parsing response: ' + e.message + '</span>';
            }
        },
        onFailure: function(response) {
            button.disabled = false;
            resultDiv.innerHTML = '<span style="color:red;">✗ Connection failed. Check server logs for details.</span>';
        }
    });
}
//]]>
</script>
JAVASCRIPT;

        return $html;
    }

    /**
     * Remove scope info from field (not needed for button)
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _renderScopeLabel(Varien_Data_Form_Element_Abstract $element)
    {
        return '';
    }
}
