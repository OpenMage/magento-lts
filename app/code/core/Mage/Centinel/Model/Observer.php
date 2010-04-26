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
 * @package     Mage_Centinel
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * 3D Secure Validation Model
 *
 * @category   Mage
 * @package    Mage_Centinel
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Centinel_Model_Observer extends Varien_Object
{
    /**
     * Set cmpi data to payment
     *
     * @param Varien_Object $observer
     * @return Mage_Centinel_Model_Observer
     */
    public function salesEventConvertQuoteToOrder($observer)
    {
        $payment = $observer->getEvent()->getQuote()->getPayment();

        if ($payment->getMethodInstance()->getIsCentinelValidationEnabled()) {
            $to = array($payment, 'setAdditionalInformation');
            $payment->getMethodInstance()->getCentinelValidator()->exportCmpiData($to);
        }
        return $this;
    }

    /**
     * Add cmpi data to info block
     *
     * @param Varien_Object $observer
     * @return Mage_Centinel_Model_Observer
     */
    public function paymentInfoBlockPrepareSpecificInformation($observer)
    {
        if ($observer->getEvent()->getBlock()->getIsSecureMode()) {
            return;
        }

        $payment = $observer->getEvent()->getPayment();
        $transport = $observer->getEvent()->getTransport();
        $helper = Mage::helper('centinel');

        $info = array(
            Mage_Centinel_Model_Service::CMPI_PARES,
            Mage_Centinel_Model_Service::CMPI_ENROLLED,
            Mage_Centinel_Model_Service::CMPI_ECI,
            Mage_Centinel_Model_Service::CMPI_CAVV,
            Mage_Centinel_Model_Service::CMPI_XID
        );
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
     * @return Mage_Centinel_Model_Observer
     */
    public function paymentFormBlockToHtmlBefore($observer)
    {
        $paymentFormBlock = $observer->getEvent()->getBlock();
        $method = $paymentFormBlock->getMethod();

        if ($method && $method->getIsCentinelValidationEnabled()) {
            $paymentFormBlock->setChild(
               'payment.method.' . $method->getCode() . 'centinel.logo',
                Mage::helper('centinel')->getMethodFormBlock($method)
            );
        }
        return $this;
    }

    /**
     * Reset validation data  
     *
     * @param Varien_Object $observer
     * @return Mage_Centinel_Model_Observer
     */
    public function salesOrderPaymentPlaceEnd($observer)
    {
        $payment = $observer->getPayment();
        $method = $payment->getMethodInstance();
        if ($method && $method->getIsCentinelValidationEnabled()) {
            $method->getCentinelValidator()->reset();
        }
        return $this;
    }
}

