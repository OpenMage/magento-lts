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
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * PayPalUk Direct Module
 *
 * @category   Mage
 * @package    Mage_PaypalUk
 */
class Mage_PaypalUk_Model_Direct extends Mage_Paypal_Model_Direct
{
    protected $_code  = Mage_Paypal_Model_Config::METHOD_WPP_PE_DIRECT;

    /**
     * Transaction info fetching is not implemented in PayPal Uk
     */
    protected $_canFetchTransactionInfo = false;

    /**
     * Website Payments Pro instance type
     *
     * @var string
     */
    protected $_proType = 'paypaluk/pro';

    /**
     * Return available CC types for gateway based on merchant country
     *
     * @return string
     */
    public function getAllowedCcTypes()
    {
        return $this->_pro->getConfig()->cctypes;
    }

    /**
     * Merchant country limitation for 3d secure feature, rewrite for parent implementation
     *
     * @return bool
     */
    public function getIsCentinelValidationEnabled()
    {
        if (!parent::getIsCentinelValidationEnabled()) {
            return false;
        }
        // available only for US and UK merchants
        if (in_array($this->_pro->getConfig()->getMerchantCountry(), ['US', 'GB'])) {
            return true;
        }
        return false;
    }

    /**
     * Import direct payment results to payment
     *
     * @param Mage_Paypal_Model_Api_Nvp $api
     * @param Mage_Sales_Model_Order_Payment $payment
     */
    protected function _importResultToPayment($api, $payment)
    {
        $payment->setTransactionId($api->getPaypalTransactionId())->setIsTransactionClosed(0)
            ->setIsTransactionPending($api->getIsPaymentPending())
            ->setTransactionAdditionalInfo(Mage_PaypalUk_Model_Pro::TRANSPORT_PAYFLOW_TXN_ID, $api->getTransactionId())
        ;
        $payment->setPreparedMessage(Mage::helper('paypaluk')->__('Payflow PNREF: #%s.', $api->getTransactionId()));
        $this->_pro->importPaymentInfo($api, $payment);
    }

    /**
     * Format credit card expiration date based on month and year values
     * Format: mmyy
     *
     * @param string|int $month
     * @param string|int $year
     * @return string
     */
    protected function _getFormattedCcExpirationDate($month, $year)
    {
        return sprintf('%02d', $month) . sprintf('%02d', substr($year, -2, 2));
    }
}
