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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
     * Pre-defined array of methods that we are going to render
     *
     * @var array
     */
    protected $_methodArray = array(
        'payment_ccsave'            => 'Mage_Payment_Model_Method_Cc',
        'payment_checkmo'           => 'Mage_Payment_Model_Method_Checkmo',
        'payment_purchaseorder'     => 'Mage_Payment_Model_Method_Purchaseorder'
    );

    /**
     * Payment bridge methods array
     *
     * Core block renderer by method code
     * - 'pbridge_authorizenet'  => 'Enterprise_Pbridge_Model_Payment_Method_Authorizenet',
     * - 'pbridge_paypal'        => 'Enterprise_Pbridge_Model_Payment_Method_Paypal',
     * - 'pbridge_verisign'      => 'Enterprise_Pbridge_Model_Payment_Method_Payflow_Pro',
     * - 'pbridge_paypaluk'      => 'Enterprise_Pbridge_Model_Payment_Method_Paypaluk',
     *
     * @var array
     */
    protected $_pbridgeMethodArray = array(
        'pbridge_authorizenet', 'pbridge_paypal', 'pbridge_verisign', 'pbridge_paypaluk'
    );

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
     * Add gift card details to XML object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $methodsXmlObj
     */
    public function addGiftcardToXmlObj(Mage_XmlConnect_Model_Simplexml_Element $methodsXmlObj)
    {
        $giftcardInfoBlock = $this->getLayout()->addBlock(
            'enterprise_giftcardaccount/checkout_onepage_payment_additional', 'giftcard_info'
        );

        if (intval($giftcardInfoBlock->getAppliedGiftCardAmount())) {
            $amount = $this->getQuote()->getStore()->formatPrice($giftcardInfoBlock->getAppliedGiftCardAmount(), false);
            $amount = $this->__('Gift Card amount applied to order: %s', $amount);

            $methodsXmlObj->addCustomChild('information', null, array('label' => $amount, 'disabled' => '1'));

            $this->setIsUsedGiftCard(true);
        }
    }

    /**
     * Check is payment required for a quote
     *
     * @return bool
     */
    protected function _isPaymentRequired()
    {
        $this->getQuote()->collectTotals();
        return !intval($this->getQuote()->getGrandTotal()) && !$this->getQuote()->hasNominalItems();
    }

    /**
     * Get payment methods array as code => renderer and set payment blocks to layout
     *
     * @return array
     */
    protected function _getPaymentMethodArray()
    {
        $methodArray = $this->_methodArray;

        /**
         * Check is available Payment Bridge and add methods for rendering
         */
        if (is_object(Mage::getConfig()->getNode('modules/Enterprise_Pbridge'))) {

            $pbBlockRenderer = 'xmlconnect/checkout_payment_method_';
            $pbBlockName = 'xmlconnect.checkout.payment.method.';

            foreach ($this->_pbridgeMethodArray as $block) {
                $currentBlockRenderer = $pbBlockRenderer . $block;
                $currentBlockName = $pbBlockName . $block;
                $this->getLayout()->addBlock($currentBlockRenderer, $currentBlockName);
                $this->setChild($block, $currentBlockName);
            }
            $methodArray = $methodArray + $this->_pbridgeMethodArray;
        }
        return $methodArray;
    }

    /**
     * Render payment methods xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $methodsXmlObj Mage_XmlConnect_Model_Simplexml_Element */
        $methodsXmlObj = Mage::getModel('xmlconnect/simplexml_element', '<payment_methods></payment_methods>');

        if ($this->_addGiftCard($methodsXmlObj)) {
            return $methodsXmlObj->asNiceXml();
        }

        $this->addCustomerBalanceToXmlObj($methodsXmlObj)->_buildPaymentMethods($methodsXmlObj);
        return $methodsXmlObj->asNiceXml();
    }

    /**
     * Add free payment method xml to payment method list
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $methodsXmlObj
     * @return Mage_XmlConnect_Block_Checkout_Payment_Method_List
     */
    protected function _addFreePaymentToXmlObj(Mage_XmlConnect_Model_Simplexml_Element $methodsXmlObj)
    {
        $methodsXmlObj->addCustomChild('method', null, array(
            'id' => 'free',
            'code' => 'free',
            'post_name' => 'payment[method]',
            'label' => $this->__('No Payment Information Required'),
            'selected' => '1',
            'disabled' => '1'
        ));
        return $this;
    }

    /**
     * Check and prepare payment method model
     *
     * @param mixed $method
     * @return bool
     */
    protected function _canUseMethod($method)
    {
        if (!($method instanceof Mage_Payment_Model_Method_Abstract) || !$method->canUseCheckout()
            || !$method->isAvailable($this->getQuote())
        ) {
            return false;
        }
        return parent::_canUseMethod($method);
    }

    /**
     * Add gift card info to xml and check is covered a quote
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $methodsXmlObj
     * @return bool
     */
    protected function _addGiftCard(Mage_XmlConnect_Model_Simplexml_Element $methodsXmlObj)
    {
        if (is_object(Mage::getConfig()->getNode('modules/Enterprise_GiftCardAccount'))) {
            $this->addGiftcardToXmlObj($methodsXmlObj);
            if ($this->getIsUsedGiftCard() && $this->_isPaymentRequired()) {
                $this->_addFreePaymentToXmlObj($methodsXmlObj);
                return true;
            }
        }
        return false;
    }

    /**
     * Add customer balance details to XML object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $methodsXmlObj
     * @return Mage_XmlConnect_Block_Checkout_Payment_Method_List
     */
    public function addCustomerBalanceToXmlObj(Mage_XmlConnect_Model_Simplexml_Element $methodsXmlObj)
    {
        if (is_object(Mage::getConfig()->getNode('modules/Enterprise_CustomerBalance'))) {
            /** @var $customerBalanceBlock Enterprise_CustomerBalance_Block_Checkout_Onepage_Payment_Additional */
            $customerBalanceBlock = $this->getLayout()
                ->addBlock('enterprise_customerbalance/checkout_onepage_payment_additional', 'customer_balance');
            $storeCreditFlag = (int) Mage::getStoreConfig(Enterprise_CustomerBalance_Helper_Data::XML_PATH_ENABLED);
            if ($storeCreditFlag && $customerBalanceBlock->isDisplayContainer()) {
                $balance = $this->getQuote()->getStore()->formatPrice($customerBalanceBlock->getBalance(), false);
                $methodsXmlObj->addCustomChild('customer_balance', null, array(
                    'post_name' => 'payment[use_customer_balance]',
                    'code'      => 1,
                    'label'     => $this->__('Use Store Credit (%s available)', $balance),
                    'is_cover_a_quote' => intval($customerBalanceBlock->isFullyPaidAfterApplication()),
                    'selected'  => intval($customerBalanceBlock->isCustomerBalanceUsed())
                ));
            }
        }
        return $this;
    }

    /**
     * Add payment methods info to xml object
     *
     * @throw Mage_Core_Exception
     * @param Mage_XmlConnect_Model_Simplexml_Element $methodsXmlObj
     * @return Mage_XmlConnect_Block_Checkout_Payment_Method_List
     */
    protected function _buildPaymentMethods(Mage_XmlConnect_Model_Simplexml_Element $methodsXmlObj)
    {
        $methodArray = $this->_getPaymentMethodArray();
        $usedMethods = $sortedAvailableMethodCodes = $usedCodes = array();

        /**
         * Receive available methods for checkout
         */
        $allAvailableMethods  = Mage::helper('payment')->getStoreMethods(
            Mage::app()->getStore(), $this->getQuote()
        );

        $total = $this->getQuote()->getGrandTotal();
        foreach ($allAvailableMethods as $key => $method) {
            if ($this->_canUseMethod($method) && ($total != 0 || $method->getCode() == 'free'
                || ($this->getQuote()->hasRecurringItems() && $method->canManageRecurringProfiles()))
            ) {
                $this->_assignMethod($method);
            } else {
                unset($allAvailableMethods[$key]);
            }
        }

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

            $options = array(
                'id' => $method->getCode(),
                'code' => $method->getCode(),
                'post_name' => 'payment[method]',
                'label' => $methodsXmlObj->escapeXml($method->getTitle()),
            );

            if ($this->getQuote()->getPayment()->getMethod() == $method->getCode()) {
                $options['selected'] = 1;
            }

            $methodItemXmlObj = $methodsXmlObj->addCustomChild('method', null, $options);
            $renderer->addPaymentFormToXmlObj($methodItemXmlObj);
        }

        if (count($allAvailableMethods) == 1 && isset($sortedAvailableMethodCodes[0])
            && $sortedAvailableMethodCodes[0] == 'free') {
            if ($this->_isPaymentRequired()) {
                $this->_addFreePaymentToXmlObj($methodsXmlObj);
            }
        }

        if (!count($allAvailableMethods)) {
            Mage::throwException($this->__('Your order cannot be completed at this time as there is no payment methods available for it.'));
        }
        return $this;
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
