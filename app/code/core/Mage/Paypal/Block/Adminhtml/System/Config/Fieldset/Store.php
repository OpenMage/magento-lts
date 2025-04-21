<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Renderer for service JavaScript code that disables corresponding paypal methods on page load
 *
 * @package    Mage_Paypal
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
            'expresspe' => 'payment/paypaluk_express/active',
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
