<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

class Mage_Usa_Block_Adminhtml_System_Config_Form_Field_Usps_Testratequote
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $buttonLabel = Mage::helper('usa')->__('Test Rate Quote');
        $ajaxUrl = Mage::helper('adminhtml')->getUrl('adminhtml/usps/testRateQuote');
        $website = $this->getRequest()->getParam('website', '');
        $store = $this->getRequest()->getParam('store', '');
        
        $html = '<button type="button" id="usps-test-rate-button" onclick="testUspsRateQuote(\'' . $ajaxUrl . '\', \'' . $website . '\', \'' . $store . '\')" class="scalable">' 
              . '<span>' . $buttonLabel . '</span></button>';
        $html .= '<div id="usps-rate-test-result" style="margin-top:10px;"></div>';
        
        $html .= <<<'JAVASCRIPT'
<script type="text/javascript">
//<![CDATA[
function testUspsRateQuote(url, website, store) {
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
        document.getElementById('usps-rate-test-result').innerHTML = 
            '<span style="color:red;">Please fill in Client ID, Client Secret, and Environment.</span>';
        return;
    }
    
    var resultDiv = document.getElementById('usps-rate-test-result');
    resultDiv.innerHTML = '<span style="color:gray;">Fetching rates...</span>';
    
    var button = document.getElementById('usps-test-rate-button');
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
                if (result.success) {
                    var html = '<div style="color:green; font-weight:bold;">' + result.message + '</div>';
                    if (result.rates && result.rates.length > 0) {
                        html += '<table style="margin-top:10px; border-collapse:collapse; font-size:12px;">';
                        html += '<tr style="background:#f5f5f5;"><th style="padding:5px; border:1px solid #ddd;">Method</th><th style="padding:5px; border:1px solid #ddd;">Price</th></tr>';
                        for (var i = 0; i < result.rates.length && i < 10; i++) {
                            html += '<tr><td style="padding:5px; border:1px solid #ddd;">' + result.rates[i].method + '</td>';
                            html += '<td style="padding:5px; border:1px solid #ddd;">$' + result.rates[i].price + '</td></tr>';
                        }
                        if (result.rates.length > 10) {
                            html += '<tr><td colspan="2" style="padding:5px; border:1px solid #ddd; font-style:italic;">... and ' + (result.rates.length - 10) + ' more</td></tr>';
                        }
                        html += '</table>';
                    }
                    resultDiv.innerHTML = html;
                } else {
                    resultDiv.innerHTML = '<span style="color:red; font-weight:bold;">' + result.message + '</span>';
                    if (result.debug) {
                        resultDiv.innerHTML += '<pre style="margin-top:5px; padding:10px; background:#f9f9f9; font-size:11px; overflow:auto; max-height:200px;">' + result.debug + '</pre>';
                    }
                }
            } catch(e) {
                resultDiv.innerHTML = '<span style="color:red;">Error: ' + e.message + '</span>';
            }
        },
        onFailure: function() {
            button.disabled = false;
            resultDiv.innerHTML = '<span style="color:red;">Request failed.</span>';
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
