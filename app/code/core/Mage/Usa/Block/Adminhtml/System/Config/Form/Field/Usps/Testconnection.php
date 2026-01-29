<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

class Mage_Usa_Block_Adminhtml_System_Config_Form_Field_Usps_Testconnection 
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $buttonLabel = Mage::helper('usa')->__('Test Connection');
        $ajaxUrl = Mage::helper('adminhtml')->getUrl('adminhtml/usps/testconnection');
        $website = $this->getRequest()->getParam('website', '');
        $store = $this->getRequest()->getParam('store', '');
        
        $html = '<button type="button" id="usps-test-connection-button" onclick="testUspsConnection(\'' . $ajaxUrl . '\', \'' . $website . '\', \'' . $store . '\')" class="scalable">' 
              . '<span>' . $buttonLabel . '</span></button>';
        $html .= '<div id="usps-test-result" style="margin-top:10px; font-weight:bold;"></div>';
        
        $html .= <<<'JAVASCRIPT'
<script type="text/javascript">
//<![CDATA[
function testUspsConnection(url, website, store) {
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
        document.getElementById('usps-test-result').innerHTML = 
            '<span style="color:red;">Please fill in Client ID, Client Secret, and Environment.</span>';
        return;
    }
    
    var resultDiv = document.getElementById('usps-test-result');
    resultDiv.innerHTML = '<span style="color:gray;">Testing...</span>';
    
    var button = document.getElementById('usps-test-connection-button');
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
                var result = eval('(' + response.responseText + ')');
                if (result.success) {
                    resultDiv.innerHTML = '<span style="color:green;">' + result.message + '</span>';
                } else {
                    resultDiv.innerHTML = '<span style="color:red;">' + result.message + '</span>';
                }
            } catch(e) {
                resultDiv.innerHTML = '<span style="color:red;">Error: ' + e.message + '</span>';
            }
        },
        onFailure: function() {
            button.disabled = false;
            resultDiv.innerHTML = '<span style="color:red;">Connection failed.</span>';
        }
    });
}
//]]>
</script>
JAVASCRIPT;
        
        return $html;
    }
    
    protected function _renderScopeLabel(Varien_Data_Form_Element_Abstract $element)
    {
        return '';
    }
}
