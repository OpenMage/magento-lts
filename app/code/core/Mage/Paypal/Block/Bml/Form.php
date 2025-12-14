<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Bill Me Later payment form
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Bml_Form extends Mage_Paypal_Block_Express_Form
{
    /**
     * Payment method code
     * @var string
     */
    protected $_methodCode = Mage_Paypal_Model_Config::METHOD_BML;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_config = Mage::getModel('paypal/config')->setMethod($this->getMethodCode());
        $mark = Mage::getConfig()->getBlockClassName('core/template');
        $mark = new $mark();
        $mark->setTemplate('paypal/payment/mark.phtml')
            ->setPaymentAcceptanceMarkHref('https://www.securecheckout.billmelater.com/paycapture-content/'
                . 'fetch?hash=AU826TU8&content=/bmlweb/ppwpsiw.html')
            ->setPaymentAcceptanceMarkSrc('https://www.paypalobjects.com/webstatic/en_US/i/buttons/'
                . 'ppc-acceptance-medium.png')
            ->setPaymentWhatIs('See terms');
        $this->setTemplate('paypal/payment/redirect.phtml')
            ->setRedirectMessage(
                Mage::helper('paypal')->__('You will be redirected to the PayPal website.'),
            )
            ->setMethodTitle('') // Output PayPal mark, omit title
            ->setMethodLabelAfterHtml($mark->toHtml());
    }
}
