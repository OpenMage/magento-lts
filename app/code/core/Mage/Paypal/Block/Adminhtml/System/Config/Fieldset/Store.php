<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Renderer for service JavaScript code that disables corresponding paypal methods on page load
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Block_Adminhtml_System_Config_Fieldset_Store extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Path to template file
     *
     * @var string
     */
    protected $_template = 'paypal/system/config/fieldset/store.phtml';

    /**
     * Render service JavaScript code
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->toHtml();
    }

    /**
     * Returns list of disabled (in the Default or the Website Scope) paypal methods
     *
     * @return array
     */
    protected function getPaypalDisabledMethods()
    {
        // Assoc array that contains info about paypal methods (their IDs and corresponding Config Paths)
        $methods = [
            'express'   => 'payment/paypal_express/active',
            'wps'       => 'payment/paypal_standard/active',
            'wpp'       => 'payment/paypal_direct/active',
            'wpppe'     => 'payment/paypaluk_direct/active',
            'verisign'  => 'payment/verisign/active',
            'expresspe' => 'payment/paypaluk_express/active'
        ];
        // Retrieve a code of the current website
        $website = $this->getRequest()->getParam('website');

        $configRoot = Mage::getConfig()->getNode(null, 'website', $website);

        $disabledMethods = [];
        foreach ($methods as $methodId => $methodPath) {
            $isEnabled = (int) $configRoot->descend($methodPath);
            if ($isEnabled === 0) {
                $disabledMethods[$methodId] = $isEnabled;
            }
        }

        return $disabledMethods;
    }
}
