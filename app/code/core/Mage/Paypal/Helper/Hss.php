<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Hosted Sole Solution helper
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Helper_Hss extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Paypal';

    /**
     * Hosted Sole Solution methods
     *
     * @var array
     */
    protected $_hssMethods = [
        Mage_Paypal_Model_Config::METHOD_HOSTEDPRO,
        Mage_Paypal_Model_Config::METHOD_PAYFLOWLINK,
        Mage_Paypal_Model_Config::METHOD_PAYFLOWADVANCED,
    ];

    /**
     * Get template for button in order review page if HSS method was selected
     *
     * @param  string $name  template name
     * @param  string $block buttons block name
     * @return string
     */
    public function getReviewButtonTemplate($name, $block)
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if ($quote) {
            $payment = $quote->getPayment();
            if ($payment && in_array($payment->getMethod(), $this->_hssMethods)) {
                return $name;
            }
        }

        if ($blockObject = Mage::getSingleton('core/layout')->getBlock($block)) {
            return $blockObject->getTemplate();
        }

        return '';
    }

    /**
     * Get methods
     *
     * @return array
     */
    public function getHssMethods()
    {
        return $this->_hssMethods;
    }
}
