<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

class Mage_Usa_Block_Adminhtml_System_Config_Form_Field_Usps_Testconnection extends Mage_Usa_Block_Adminhtml_System_Config_Form_Field_Usps_AbstractTestButton
{
    protected function _getButtonId(): string
    {
        return 'usps-test-connection-button';
    }

    protected function _getButtonLabel(): string
    {
        return Mage::helper('usa')->__('Test Connection');
    }

    protected function _getAjaxRoute(): string
    {
        return 'adminhtml/usps/testconnection';
    }

    protected function _getResultDivId(): string
    {
        return 'usps-test-result';
    }

    protected function _getLoadingText(): string
    {
        return 'Testing...';
    }

    protected function _getFailureText(): string
    {
        return 'Connection failed.';
    }

    protected function _getOnSuccessJs(): string
    {
        return <<<'JS'
if (result.success) {
    resultDiv.innerHTML = '<span style="color:green;">' + result.message + '</span>';
} else {
    resultDiv.innerHTML = '<span style="color:red;">' + result.message + '</span>';
}
JS;
    }
}
