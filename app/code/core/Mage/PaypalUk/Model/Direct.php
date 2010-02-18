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
 * @package     Mage_PaypalUk
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * PayPalUk Direct Module
 */
class Mage_PaypalUk_Model_Direct extends Mage_Paypal_Model_Direct
{
    protected $_code  = 'paypaluk_direct';

    /**
     * Website Payments Pro instance type
     *
     * @var string
     */
    protected $_proType = 'paypaluk/pro';

    /**
     * Ipn notify action
     *
     * @var string
     */
    protected $_notifyAction = 'paypaluk/ipn/direct';

    /**
     * Import direct payment results to payment
     *
     * @param Mage_Paypal_Model_Api_Nvp
     * @param Mage_Sales_Model_Order_Payment
     */
    protected function _importResultToPayment($api, $payment)
    {
        $payment->setTransactionId($api->getPaypalTransactionId())->setIsTransactionClosed(0)
            ->setIsTransactionPending($api->getIsPaymentPending())
            ->setTransactionAdditionalInfo(Mage_PaypalUk_Model_Pro::TRANSPORT_PAYFLOW_TXN_ID, $api->getTransactionId())
            ;
        $payment->setPreparedMessage(Mage::helper('paypaluk')->__('Payflow PNREF: #%s.', $api->getTransactionId()));
        Mage::getModel($this->_infoType)->importToPayment($api, $payment);
    }
}
