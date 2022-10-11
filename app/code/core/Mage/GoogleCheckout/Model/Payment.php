<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_GoogleCheckout
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_GoogleCheckout
 * @author     Magento Core Team <core@magentocommerce.com>
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
     *  @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        return '';
    }

    /**
     * Authorize
     *
     * @param Varien_Object $payment
     * @param float $amount
     * @return void
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        Mage::throwException(Mage::helper('payment')->__('Google Checkout has been deprecated.'));
    }

    /**
     * Capture payment
     *
     * @param Varien_Object $payment
     * @param float $amount
     * @throws Exception
     * @return void
     */
    public function capture(Varien_Object $payment, $amount)
    {
        Mage::throwException(Mage::helper('payment')->__('Google Checkout has been deprecated.'));
    }

    /**
     * Refund money
     *
     * @param Varien_Object $payment
     * @param float $amount
     * @throws Exception
     * @return void
     */
    public function refund(Varien_Object $payment, $amount)
    {
        Mage::throwException(Mage::helper('payment')->__('Google Checkout has been deprecated.'));
    }

    /**
     * @param Varien_Object $payment
     * @throws Exception
     * @return void
     */
    public function void(Varien_Object $payment)
    {
        Mage::throwException(Mage::helper('payment')->__('Google Checkout has been deprecated.'));
    }

    /**
     * Void payment
     *
     * @param Varien_Object $payment
     * @throws Exception
     * @return void
     */
    public function cancel(Varien_Object $payment)
    {
        Mage::throwException(Mage::helper('payment')->__('Google Checkout has been deprecated.'));
    }

    /**
     * Retrieve information from payment configuration
     *
     * @param string $field
     * @param int|string|null|Mage_Core_Model_Store $storeId
     *
     * @return  null
     */
    public function getConfigData($field, $storeId = null)
    {
        return null;
    }

    /**
     * Check void availability
     *
     * @param   Varien_Object $payment
     * @return  bool
     */
    public function canVoid(Varien_Object $payment)
    {
        return false;
    }
}
