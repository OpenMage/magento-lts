<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Custom renderer for PayPal BML credentials wizard popup
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @author     Magento Core Team <core@magentocommerce.com>
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
