<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_PaypalUk
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_PaypalUk
 */
class Mage_PaypalUk_Model_Express extends Mage_Paypal_Model_Express
{
    protected $_code = Mage_Paypal_Model_Config::METHOD_WPP_PE_EXPRESS;
    protected $_formBlockType = 'paypaluk/express_form';
    protected $_canCreateBillingAgreement = false;
    protected $_canManageRecurringProfiles = false;

    /**
     * Website Payments Pro instance type
     *
     * @var string $_proType
     */
    protected $_proType = 'paypaluk/express_pro';

    /**
     * Express Checkout payment method instance
     *
     * @var Mage_Payment_Model_Method_Abstract|false
     */
    protected $_ecInstance = null;

    /**
     * EC PE won't be available if the EC is available
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        if (!parent::isAvailable($quote)) {
            return false;
        }
        if (!$this->_ecInstance) {
            $this->_ecInstance = Mage::helper('payment')
                ->getMethodInstance(Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS);
        }
        if ($quote && $this->_ecInstance) {
            $this->_ecInstance->setStore($quote->getStoreId());
        }
        return $this->_ecInstance ? !$this->_ecInstance->isAvailable() : false;
    }

    /**
     * Import payment info to payment
     *
     * @param Mage_Paypal_Model_Api_Nvp $api
     * @param Mage_Sales_Model_Order_Payment $payment
     */
    protected function _importToPayment($api, $payment)
    {
        $payment->setTransactionId($api->getPaypalTransactionId())->setIsTransactionClosed(0)
            ->setAdditionalInformation(
                Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_REDIRECT,
                $api->getRedirectRequired() || $api->getRedirectRequested()
            )
            ->setIsTransactionPending($api->getIsPaymentPending())
            ->setTransactionAdditionalInfo(Mage_PaypalUk_Model_Pro::TRANSPORT_PAYFLOW_TXN_ID, $api->getTransactionId())
        ;
        $payment->setPreparedMessage(Mage::helper('paypaluk')->__('Payflow PNREF: #%s.', $api->getTransactionId()));
        Mage::getModel('paypal/info')->importToPayment($api, $payment);
    }

    /**
     * Checkout redirect URL getter for onepage checkout (hardcode)
     *
     * @see Mage_Checkout_OnepageController::savePaymentAction()
     * @see Mage_Sales_Model_Quote_Payment::getCheckoutRedirectUrl()
     * @return string
     */
    public function getCheckoutRedirectUrl()
    {
        return Mage::getUrl('paypaluk/express/start');
    }
}
