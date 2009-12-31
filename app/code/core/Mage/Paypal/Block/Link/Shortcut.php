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
 * Paypal shortcut link
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Block_Link_Shortcut extends Mage_Core_Block_Template
{
    protected $_method = null;

    /**
     * Return checkout url as click action to button
     *
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->getUrl('paypal/express/shortcut', array('_secure'=>true));
    }


    /**
     * Return payment model object
     *
     * @return Mage_Paypal_Model_Express
     */
    public function getPayment()
    {
        if (empty($this->_method)) {
            $this->_method = Mage::getModel('paypal/express');
        }
        return $this->_method;
    }

    /**
     * Return image url based on configuration data
     *
     * @return string
     */
    public function getImageUrl()
    {
        $locale = Mage::app()->getLocale()->getLocaleCode();
        $quote = Mage::getSingleton('checkout/session')->getQuote();


        if ($this->getPayment()->getApi()->getStyleConfigData('button_flavor') == Mage_Paypal_Model_Api_Abstract::BUTTON_FLAVOR_DYNAMIC) {
            if ($this->getPayment()->getApi()->getSandboxFlag()) {
                $url = 'https://fpdbs.sandbox.paypal.com/dynamicimageweb?cmd=_dynamic-image&locale=' . $locale;
            } else {
                $url = 'https://fpdbs.paypal.com/dynamicimageweb?cmd=_dynamic-image&locale=' . $locale;
            }

            $orderTotal = $quote->getGrandTotal();
            if ($orderTotal) {
                $url .= '&ordertotal=' . $orderTotal;
            }

            $pal = $this->getPayment()->getPalDetails();
            if ($pal) {
                $url .= '&pal=' . $pal;
            }

            $buttonType = $this->getPayment()->getApi()->getStyleConfigData('button_type');
            if ($buttonType) {
                $url .= '&buttontype=' . $buttonType;
            }

            return $url;
        } else {
            if (strpos('en_GB', $locale)===false) {
                $locale = 'en_US';
            }
            return 'https://www.paypal.com/'.$locale.'/i/btn/btn_xpressCheckout.gif';
        }
    }

    /**
     * Check whether method is available and render HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $paypalModel = Mage::getModel('paypal/express');
        if (!$paypalModel->isAvailable($quote) || !$paypalModel->isVisibleOnCartPage()
            || !$quote->validateMinimumAmount()) {
            return '';
        }
        return parent::_toHtml();
    }
}
