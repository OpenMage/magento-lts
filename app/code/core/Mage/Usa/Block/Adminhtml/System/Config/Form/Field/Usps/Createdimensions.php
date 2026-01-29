<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/**
 * USPS Create Dimensions Button Backend Model
 *
 * Renders a button in system configuration to create dimension EAV attributes
 * Creates package_length, package_width, package_height attributes for products
 *
 * @package    Mage_Usa
 */
class Mage_Usa_Block_Adminhtml_System_Config_Form_Field_Usps_Createdimensions
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Generate button HTML for creating dimension attributes
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        // Check if attributes already exist
        $attributeCodes = ['package_length', 'package_width', 'package_height'];
        $existingCount = 0;
        $missingAttributes = [];
        
        foreach ($attributeCodes as $code) {
            $attributeId = Mage::getResourceModel('catalog/eav_attribute')
                ->getIdByCode('catalog_product', $code);
            if ($attributeId) {
                $existingCount++;
            } else {
                $missingAttributes[] = $code;
            }
        }
        
        $allExist = ($existingCount === count($attributeCodes));
        $buttonLabel = $allExist 
            ? Mage::helper('usa')->__('Re-create Dimension Attributes')
            : Mage::helper('usa')->__('Create Dimension Attributes');
        
        $ajaxUrl = Mage::helper('adminhtml')->getUrl('adminhtml/usps/createdimensions');
        
        $html = '<button type="button" id="usps-create-dimensions-button" onclick="createUspsAttributes(\'' . $ajaxUrl . '\')" class="scalable">' 
              . '<span>' . $buttonLabel . '</span></button>';
        $html .= '<div id="usps-attr-result" style="margin-top:10px; font-weight:bold;">';
        
        // Show current status
        if ($allExist) {
            $html .= '<span style="color:green;">✓ All dimension attributes exist (package_length, package_width, package_height)</span>';
        } elseif ($existingCount > 0) {
            $html .= '<span style="color:orange;">⚠ Partial setup: ' . $existingCount . '/3 attributes exist. Missing: ' . implode(', ', $missingAttributes) . '</span>';
        } else {
            $html .= '<span style="color:gray;">Attributes not yet created. Click button to create.</span>';
        }
        
        $html .= '</div>';
        $html .= '<p class="note"><span>' . Mage::helper('usa')->__('Creates product attributes: package_length, package_width, package_height (in inches). These attributes are used for accurate dimensional shipping rates.') . '</span></p>';
        
        // JavaScript for AJAX call
        $html .= <<<'JAVASCRIPT'
<script type="text/javascript">
//<![CDATA[
function createUspsAttributes(url) {
    // Show loading message
    var resultDiv = document.getElementById('usps-attr-result');
    resultDiv.innerHTML = '<span style="color:gray;">Creating attributes...</span>';
    
    // Disable button during request
    var button = document.getElementById('usps-create-dimensions-button');
    button.disabled = true;
    
    // Make AJAX request
    new Ajax.Request(url, {
        parameters: {
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
            resultDiv.innerHTML = '<span style="color:red;">✗ Failed to create attributes. Check server logs for details.</span>';
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
