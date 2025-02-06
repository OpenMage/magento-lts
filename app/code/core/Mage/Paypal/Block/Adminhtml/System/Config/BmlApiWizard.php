<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Paypal
 */

/**
 * Custom renderer for PayPal BML credentials wizard popup
 *
 * @category   Mage
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
