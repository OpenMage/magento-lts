<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Custom renderer for PayPal BML credentials wizard popup
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Adminhtml_System_Config_BmlApiWizard extends Mage_Paypal_Block_Adminhtml_System_Config_ApiWizard
{
    /**
     * @var string
     */
    protected $_wizardTemplate = 'paypal/system/config/bml_api_wizard.phtml';

    /**
     * No sandbox button for BmlApiWizard
     *
     * @param string $elementHtmlId
     * @param array $originalData
     * @return array
     */
    protected function _getSandboxButtonData($elementHtmlId, $originalData)
    {
        return [];
    }
}
