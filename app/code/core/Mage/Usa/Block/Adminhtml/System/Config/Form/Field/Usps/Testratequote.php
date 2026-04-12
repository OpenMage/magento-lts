<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

class Mage_Usa_Block_Adminhtml_System_Config_Form_Field_Usps_Testratequote extends Mage_Usa_Block_Adminhtml_System_Config_Form_Field_Usps_AbstractTestButton
{
    protected function _getButtonId(): string
    {
        return 'usps-test-rate-button';
    }

    protected function _getButtonLabel(): string
    {
        return Mage::helper('usa')->__('Test Rate Quote');
    }

    protected function _getAjaxRoute(): string
    {
        return 'adminhtml/usps/testRateQuote';
    }

    protected function _getResultDivId(): string
    {
        return 'usps-rate-test-result';
    }

    protected function _getLoadingText(): string
    {
        return 'Fetching rates...';
    }

    protected function _getOnSuccessJs(): string
    {
        return <<<'JS'
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
JS;
    }
}
