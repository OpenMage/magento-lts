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
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Paypal expess checkout shortcut link
 */
class Mage_Paypal_Block_Express_Shortcut extends Mage_Core_Block_Template
{
    /**
     * Model type
     *
     * @var string
     */
    protected $_modelType = 'paypal/express';

    /**
     * Pro model type
     *
     * @var string
     */
    protected $_proModelType = 'paypal/pro';

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_paymentMethod = Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS;

    /**
     * Start express action
     *
     * @var string
     */
    protected $_startAction = 'paypal/express/start';

    /**
     * PayPal Pro instance
     *
     * @var Mage_Paypal_Model_Pro
     */
    protected $_pro = null;

    /**
     * Express checkout URL getter
     *
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->getUrl($this->_startAction);
    }

    /**
     * Get checkout button image url
     *
     * @return string
     */
    public function getImageUrl()
    {
        return Mage::getModel('paypal/express_checkout', array(
            'quote'  => Mage::getSingleton('checkout/session')->getQuote(),
            'config' => $this->_getProInstance()->getConfig(),
        ))->getCheckoutShortcutImageUrl();
    }

    /**
     * Payment method model type setter
     *
     * @param string
     */
    public function setPaymentModelType($type)
    {
        $this->_modelType = $type;
    }

    /**
     * Pro model type setter
     *
     * @param string
     */
    public function setProModelType($type)
    {
        $this->_proModelType = $type;
    }

    /**
     * Payment method setter
     *
     * @param string
     */
    public function setPaymentMethod($method)
    {
        $this->_paymentMethod = $method;
    }

    /**
     * Start action setter
     *
     * @param string
     */
    public function setStartAction($action)
    {
        $this->_startAction = $action;
    }

    /**
     * Shortcut text setter
     *
     * @param string
     */
    public function setShortcutText($text)
    {
        $this->_shortcutText = $text;
    }

    /**
     * Check whether method is available and render HTML
     * TODO: payment method instance is not supposed to know about quote.
     * The block also is not supposed to know about payment method instance
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_getProInstance()->getConfig()->visibleOnCart) {
            return '';
        }
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if (!$quote->validateMinimumAmount()
            || !Mage::getModel($this->_modelType, array($this->_pro))->isAvailable($quote)) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * PayPal Pro instance getter
     *
     * @return Mage_Paypal_Model_Pro
     */
    protected function _getProInstance()
    {
        if (null === $this->_pro) {
            $this->_pro = Mage::getModel($this->_proModelType)->setMethod($this->_paymentMethod);
        }
        return $this->_pro;
    }
}
