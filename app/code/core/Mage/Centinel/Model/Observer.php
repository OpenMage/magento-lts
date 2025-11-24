<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Centinel
 */

/**
 * 3D Secure Validation Model
 *
 * @package    Mage_Centinel
 */
class Mage_Centinel_Model_Observer extends Varien_Object
{
    /**
     * Set cmpi data to payment
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function salesEventConvertQuoteToOrder($observer)
    {
        $payment = $observer->getEvent()->getQuote()->getPayment();

        if ($payment->getMethodInstance()->getIsCentinelValidationEnabled()) {
            $to = [$payment, 'setAdditionalInformation'];
            $payment->getMethodInstance()->getCentinelValidator()->exportCmpiData($to);
        }

        return $this;
    }

    /**
     * Add cmpi data to info block
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function paymentInfoBlockPrepareSpecificInformation($observer)
    {
        if ($observer->getEvent()->getBlock()->getIsSecureMode()) {
            return $this;
        }

        $payment = $observer->getEvent()->getPayment();
        $transport = $observer->getEvent()->getTransport();
        $helper = Mage::helper('centinel');

        $info = [
            Mage_Centinel_Model_Service::CMPI_PARES,
            Mage_Centinel_Model_Service::CMPI_ENROLLED,
            Mage_Centinel_Model_Service::CMPI_ECI,
            Mage_Centinel_Model_Service::CMPI_CAVV,
            Mage_Centinel_Model_Service::CMPI_XID,
        ];
        foreach ($info as $key) {
            if ($value = $payment->getAdditionalInformation($key)) {
                $transport->setData($helper->getCmpiLabel($key), $helper->getCmpiValue($key, $value));
            }
        }

        return $this;
    }

    /**
     * Add centinel logo block into payment form
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function paymentFormBlockToHtmlBefore($observer)
    {
        $paymentFormBlock = $observer->getEvent()->getBlock();
        $method = $paymentFormBlock->getMethod();

        if ($method && $method->getIsCentinelValidationEnabled()) {
            $paymentFormBlock->setChild(
                'payment.method.' . $method->getCode() . 'centinel.logo',
                Mage::helper('centinel')->getMethodFormBlock($method),
            );
        }

        return $this;
    }

    /**
     * Reset validation data
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function checkoutSubmitAllAfter($observer)
    {
        $method = false;

        if ($order = $observer->getEvent()->getOrder()) {
            $method = $order->getPayment()->getMethodInstance();
        } elseif ($orders = $observer->getEvent()->getOrders()) {
            if ($order = array_shift($orders)) {
                $method = $order->getPayment()->getMethodInstance();
            }
        }

        if ($method && $method->getIsCentinelValidationEnabled()) {
            $method->getCentinelValidator()->reset();
        }

        return $this;
    }

    /**
     * Reset validation data
     *
     * @param Varien_Object $observer
     * @return $this
     * @deprecated back compatibility alias for checkoutSubmitAllAfter
     */
    public function salesOrderPaymentPlaceEnd($observer)
    {
        $this->checkoutSubmitAllAfter($observer);
        return $this;
    }
}
