<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Authorizenet
 */

/**
 * DirectPost form block
 *
 * @package    Mage_Authorizenet
 */
class Mage_Authorizenet_Block_Directpost_Form extends Mage_Payment_Block_Form_Cc
{
    /**
     * Internal constructor
     * Set info template for payment step
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('authorizenet/directpost/info.phtml');
    }

    /**
     * Render block HTML
     * If method is not directpost - nothing to return
     *
     * @return string|null
     */
    protected function _toHtml()
    {
        if ($this->getMethod()->getCode() != Mage::getSingleton('authorizenet/directpost')->getCode()) {
            return null;
        }

        return parent::_toHtml();
    }

    /**
     * Set method info
     *
     * @return $this
     */
    public function setMethodInfo()
    {
        $payment = Mage::getSingleton('checkout/type_onepage')
            ->getQuote()
            ->getPayment();
        $this->setMethod($payment->getMethodInstance());

        return $this;
    }

    /**
     * Get type of request
     *
     * @return bool
     */
    public function isAjaxRequest()
    {
        return $this->getAction()
            ->getRequest()
            ->getParam('isAjax');
    }
}
