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
 * @package     Mage_Payment
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Base container block for payment methods forms
 *
 * @method Mage_Sales_Model_Quote getQuote()
 *
 * @category   Mage
 * @package    Mage_Payment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Payment_Block_Form_Container extends Mage_Core_Block_Template
{
    /**
     * Prepare children blocks
     */
    protected function _prepareLayout()
    {
        /**
         * Create child blocks for payment methods forms
         */
        foreach ($this->getMethods() as $method) {
            $this->setChild(
               'payment.method.'.$method->getCode(),
               $this->helper('payment')->getMethodFormBlock($method)
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * Check payment method model
     *
     * @param Mage_Payment_Model_Method_Abstract $method
     * @return bool
     */
    protected function _canUseMethod($method)
    {
        return $method->isApplicableToQuote($this->getQuote(), Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_COUNTRY
            | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_CURRENCY
            | Mage_Payment_Model_Method_Abstract::CHECK_ORDER_TOTAL_MIN_MAX
        );
    }

    /**
     * Check and prepare payment method model
     *
     * Redeclare this method in child classes for declaring method info instance
     *
     * @param Mage_Payment_Model_Method_Abstract $method
     * @return bool
     */
    protected function _assignMethod($method)
    {
        $method->setInfoInstance($this->getQuote()->getPayment());
        return $this;
    }

    /**
     * Declare template for payment method form block
     *
     * @param   string $method
     * @param   string $template
     * @return  Mage_Payment_Block_Form_Container
     */
    public function setMethodFormTemplate($method='', $template='')
    {
        if (!empty($method) && !empty($template)) {
            if ($block = $this->getChild('payment.method.'.$method)) {
                $block->setTemplate($template);
            }
        }
        return $this;
    }

    /**
     * Retrieve available payment methods
     *
     * @return array
     */
    public function getMethods()
    {
        $methods = $this->getData('methods');
        if ($methods === null) {
            $quote = $this->getQuote();
            $store = $quote ? $quote->getStoreId() : null;
            $methods = array();
            foreach ($this->helper('payment')->getStoreMethods($store, $quote) as $method) {
                if ($this->_canUseMethod($method) && $method->isApplicableToQuote(
                    $quote,
                    Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL
                )) {
                    $this->_assignMethod($method);
                    $methods[] = $method;
                }
            }
            $this->setData('methods', $methods);
        }
        return $methods;
    }

    /**
     * Retrieve code of current payment method
     *
     * @return mixed
     */
    public function getSelectedMethodCode()
    {
        $methods = $this->getMethods();
        if (!empty($methods)) {
            reset($methods);
            return current($methods)->getCode();
        }
        return false;
    }
}
