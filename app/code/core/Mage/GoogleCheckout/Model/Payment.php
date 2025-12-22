<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_GoogleCheckout
 */

/**
 * @package    Mage_GoogleCheckout
 * @deprecated after 1.13.1.0
 */
class Mage_GoogleCheckout_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
    /**
     * @var string
     */
    protected $_code  = 'googlecheckout';

    /**
     * Can be edit order (renew order)
     *
     * @return bool
     */
    public function canEdit()
    {
        return false;
    }

    /**
     *  Return Order Place Redirect URL
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        return '';
    }

    /**
     * Authorize
     *
     * @param  float               $amount
     * @throws Mage_Core_Exception
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        Mage::throwException(Mage::helper('payment')->__('Google Checkout has been deprecated.'));
    }

    /**
     * Capture payment
     *
     * @param  float               $amount
     * @throws Mage_Core_Exception
     */
    public function capture(Varien_Object $payment, $amount)
    {
        Mage::throwException(Mage::helper('payment')->__('Google Checkout has been deprecated.'));
    }

    /**
     * Refund money
     *
     * @param  float               $amount
     * @throws Mage_Core_Exception
     */
    public function refund(Varien_Object $payment, $amount)
    {
        Mage::throwException(Mage::helper('payment')->__('Google Checkout has been deprecated.'));
    }

    /**
     * @throws Mage_Core_Exception
     */
    public function void(Varien_Object $payment)
    {
        Mage::throwException(Mage::helper('payment')->__('Google Checkout has been deprecated.'));
    }

    /**
     * Void payment
     *
     * @throws Mage_Core_Exception
     */
    public function cancel(Varien_Object $payment)
    {
        Mage::throwException(Mage::helper('payment')->__('Google Checkout has been deprecated.'));
    }

    /**
     * Retrieve information from payment configuration
     *
     * @param string                                $field
     * @param null|int|Mage_Core_Model_Store|string $storeId
     */
    public function getConfigData($field, $storeId = null)
    {
        return null;
    }

    /**
     * Check void availability
     *
     * @return bool
     */
    public function canVoid(Varien_Object $payment)
    {
        return false;
    }
}
