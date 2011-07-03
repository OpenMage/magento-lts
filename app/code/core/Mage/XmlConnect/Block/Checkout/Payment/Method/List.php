<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * One page checkout payment methods xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Payment_Method_List extends Mage_Payment_Block_Form_Container
{
    /**
     * Prevent parent set children
     *
     * @return Mage_XmlConnect_Block_Checkout_Payment_Method_List
     */
    protected function _prepareLayout()
    {
        return $this;
    }

    /**
     * Retrieve quote model object
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    /**
     * Render payment methods xml
     *
     * @return string
     * @throw Mage_Core_Exception
     */
    protected function _toHtml()
    {
        /** @var $methodsXmlObj Mage_XmlConnect_Model_Simplexml_Element */
        $methodsXmlObj = Mage::getModel('xmlconnect/simplexml_element', '<payment_methods></payment_methods>');

        /**
         * Pre-defined array of methods that we are going to render
         */
        $methodArray = array(
            'payment_ccsave'            => 'Mage_Payment_Model_Method_Cc',
            'payment_checkmo'           => 'Mage_Payment_Model_Method_Checkmo',
            'payment_purchaseorder'     => 'Mage_Payment_Model_Method_Purchaseorder',
        );

        /**
         * Check is available Payment Bridge and add methods for rendering
         */
        if (is_object(Mage::getConfig()->getNode('modules/Enterprise_Pbridge'))) {
            $pbridgeMethodArray = array(
                'pbridge_authorizenet'  => 'Enterprise_Pbridge_Model_Payment_Method_Authorizenet',
                'pbridge_paypal'        => 'Enterprise_Pbridge_Model_Payment_Method_Paypal',
                'pbridge_verisign'      => 'Enterprise_Pbridge_Model_Payment_Method_Payflow_Pro',
                'pbridge_paypaluk'      => 'Enterprise_Pbridge_Model_Payment_Method_Paypaluk',
            );

            $pbBlockRenderer = 'xmlconnect/checkout_payment_method_';
            $pbBlockName = 'xmlconnect.checkout.payment.method.';

            foreach ($pbridgeMethodArray as $block => $class) {
                $currentBlockRenderer = $pbBlockRenderer . $block;
                $currentBlockName = $pbBlockName . $block;
                $this->getLayout()->addBlock($currentBlockRenderer, $currentBlockName);
                $this->setChild($block, $currentBlockName);
            }
            $methodArray = $methodArray + $pbridgeMethodArray;
        }

        $usedMethods = $sortedAvailableMethodCodes = $usedCodes = array();

        /**
         * Receive available methods for checkout
         */
        $allAvailableMethods  = Mage::helper('payment')->getStoreMethods(Mage::app()->getStore(), $this->getQuote());

        /**
         * Get sorted codes of available methods
         */
        foreach ($allAvailableMethods as $method) {
            $sortedAvailableMethodCodes[] = $method->getCode();
        }

        /**
         * Get blocks for layout to check available renderers
         */
        $methodBlocks = $this->getChild();

        /**
         * Collect directly supported by xmlconnect methods
         */
        if (!empty($methodBlocks) && is_array($methodBlocks)) {
            foreach ($methodBlocks as $block) {
                if (!$block) {
                    continue;
                }

                $method = $block->getMethod();
                if (!$this->_canUseMethod($method) || in_array($method->getCode(), $usedCodes)) {
                    continue;
                }
                $this->_assignMethod($method);
                $usedCodes[] = $method->getCode();
                $usedMethods[$method->getCode()] = array('renderer' => $block, 'method' => $method);
            }
        }

        /**
         * Collect all "Credit Card" / "CheckMo" / "Purchaseorder" method compatible methods
         */
        foreach ($methodArray as $methodName => $methodModelClassName) {
            $methodRenderer = $this->getChild($methodName);
            if (!empty($methodRenderer)) {
                foreach ($sortedAvailableMethodCodes as $methodCode) {
                    /**
                     * Skip used methods
                     */
                    if (in_array($methodCode, $usedCodes)) {
                        continue;
                    }
                    try {
                        $method = Mage::helper('payment')->getMethodInstance($methodCode);
                        if (!is_subclass_of($method, $methodModelClassName)) {
                            continue;
                        }
                        if (!$this->_canUseMethod($method)) {
                            continue;
                        }

                        $this->_assignMethod($method);
                        $usedCodes[] = $method->getCode();
                        $usedMethods[$method->getCode()] = array('renderer' => $methodRenderer, 'method' => $method);
                    } catch (Exception $e) {
                        Mage::logException($e);
                    }
                }
            }
        }

        /**
         * Generate methods XML according to sort order
         */
        foreach ($sortedAvailableMethodCodes as $code) {
            if (!in_array($code, $usedCodes)) {
                continue;
            }
            $method   = $usedMethods[$code]['method'];
            $renderer = $usedMethods[$code]['renderer'];
            /**
             * Render all Credit Card method compatible methods
             */
            if ($renderer instanceOf Mage_XmlConnect_Block_Checkout_Payment_Method_Ccsave) {
                $renderer->setData('method', $method);
            }

            $methodItemXmlObj = $methodsXmlObj->addChild('method');
            $methodItemXmlObj->addAttribute('post_name', 'payment[method]');
            $methodItemXmlObj->addAttribute('code', $method->getCode());
            $methodItemXmlObj->addAttribute('label', $methodsXmlObj->xmlentities($method->getTitle()));
            if ($this->getQuote()->getPayment()->getMethod() == $method->getCode()) {
                $methodItemXmlObj->addAttribute('selected', 1);
            }
            $renderer->addPaymentFormToXmlObj($methodItemXmlObj);
        }
        if (!count($usedMethods)) {
            Mage::throwException($this->__('Sorry, no payment options are available for this order at this time.'));
        }
        return $methodsXmlObj->asNiceXml();
    }

    /**
     * Check and prepare payment method model
     *
     * @param mixed $method
     * @return bool
     */
    protected function _canUseMethod($method)
    {
        if (!($method instanceof Mage_Payment_Model_Method_Abstract)
            || !$method->canUseCheckout()
            || !$method->isAvailable($this->getQuote())
        ) {
            return false;
        }
        return parent::_canUseMethod($method);
    }

    /**
     * Deprecated function adding Payment method to the xml
     *
     * @deprecated after 1.4.2.0
     * @param Mage_Core_Block_Template $block
     * @param Mage_XmlConnect_Model_Simplexml_Element $methodsXmlObj
     * @param array $usedCodes
     * @return bool
     */
    protected function _addToXml($block, $methodsXmlObj, $usedCodes)
    {
        return false;
    }

    /**
     * Deprecated function check method status
     *
     * @deprecated after 1.4.2.0
     * @param Mage_Payment_Model_Method_Abstract $method
     * @return bool
     */
    public function isAvailable($method)
    {
        return $method->isAvailable($this->getQuote());
    }
}
