<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * HSS iframe block
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Iframe extends Mage_Payment_Block_Form
{
    /**
     * Whether the block should be eventually rendered
     * @var bool
     */
    protected $_shouldRender = false;

    /**
     * Order object
     * @var Mage_Sales_Model_Order
     */
    protected $_order;

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_paymentMethodCode;

    /**
     * Current iframe block instance
     *
     * @var Mage_Paypal_Block_Iframe
     */
    protected $_block;

    /**
     * Internal constructor
     * Set info template for payment step
     */
    protected function _construct()
    {
        parent::_construct();
        $paymentCode = $this->_getCheckout()
            ->getQuote()
            ->getPayment()
            ->getMethod();
        /** @var Mage_Paypal_Helper_Hss $helper */
        $helper = $this->helper('paypal/hss');
        if (in_array($paymentCode, $helper->getHssMethods())) {
            $this->_paymentMethodCode = $paymentCode;
            $templatePath = str_replace('_', '', $paymentCode);
            $templateFile = "paypal/{$templatePath}/iframe.phtml";
            if (file_exists(Mage::getDesign()->getTemplateFilename($templateFile))) {
                $this->setTemplate($templateFile);
            } else {
                $this->setTemplate('paypal/hss/iframe.phtml');
            }
        }
    }

    /**
     * Get current block instance
     *
     * @return Mage_Paypal_Block_Iframe
     * @throws Mage_Core_Exception
     */
    protected function _getBlock()
    {
        if (!$this->_block) {
            $block = $this->getAction()
                ->getLayout()
                ->createBlock('paypal/' . $this->_paymentMethodCode . '_iframe');
            if (!$block instanceof Mage_Paypal_Block_Iframe) {
                Mage::throwException('Invalid block type');
            }

            $this->_block = $block;
        }

        return $this->_block;
    }

    /**
     * Get order object
     *
     * @return Mage_Sales_Model_Order
     */
    protected function _getOrder()
    {
        if (!$this->_order) {
            $incrementId = $this->_getCheckout()->getLastRealOrderId();
            $this->_order = Mage::getModel('sales/order')
                ->loadByIncrementId($incrementId);
        }

        return $this->_order;
    }

    /**
     * Get frontend checkout session object
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Before rendering html, check if is block rendering needed
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        if ($this->_getOrder()->getId()
            && $this->_getOrder()->getQuoteId() == $this->_getCheckout()->getLastQuoteId()
            && $this->_paymentMethodCode
        ) {
            $this->_shouldRender = true;
        }

        if ($this->getGotoSection() || $this->getGotoSuccessPage()) {
            $this->_shouldRender = true;
        }

        return parent::_beforeToHtml();
    }

    /**
     * Render the block if needed
     *
     * @return string
     * @throws Exception
     */
    protected function _toHtml()
    {
        if ($this->_isAfterPaymentSave()) {
            $this->setTemplate('paypal/hss/js.phtml');
            return parent::_toHtml();
        }

        if (!$this->_shouldRender) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * Check whether block is rendering after save payment
     *
     * @return bool
     * @throws Exception
     */
    protected function _isAfterPaymentSave()
    {
        $quote = $this->_getCheckout()->getQuote();
        if ($quote->getPayment()->getMethod() == $this->_paymentMethodCode
            && $quote->getIsActive()
            && $this->getTemplate()
            && $this->getRequest()->getActionName() == 'savePayment'
        ) {
            return true;
        }

        return false;
    }

    /**
     * Get iframe action URL
     *
     * @return string
     * @throws Mage_Core_Exception
     */
    public function getFrameActionUrl()
    {
        return $this->_getBlock()->getFrameActionUrl();
    }

    /**
     * Get secure token
     *
     * @return string
     * @throws Mage_Core_Exception
     */
    public function getSecureToken()
    {
        return $this->_getBlock()->getSecureToken();
    }

    /**
     * Get secure token ID
     *
     * @return string
     * @throws Mage_Core_Exception
     */
    public function getSecureTokenId()
    {
        return $this->_getBlock()->getSecureTokenId();
    }

    /**
     * Get payflow transaction URL
     *
     * @return string
     * @throws Mage_Core_Exception
     */
    public function getTransactionUrl()
    {
        return $this->_getBlock()->getTransactionUrl();
    }

    /**
     * Check sandbox mode
     *
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function isTestMode()
    {
        return $this->_getBlock()->isTestMode();
    }
}
