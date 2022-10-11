<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * PayPal module observer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Paypal_Model_Observer
{
    /**
     * Goes to reports.paypal.com and fetches Settlement reports.
     * @return $this
     */
    public function fetchReports()
    {
        try {
            $reports = Mage::getModel('paypal/report_settlement');
            /** @var Mage_Paypal_Model_Report_Settlement $reports */
            $credentials = $reports->getSftpCredentials(true);
            foreach ($credentials as $config) {
                try {
                    $reports->fetchAndSave($config);
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Clean unfinished transaction
     *
     * @deprecated since 1.6.2.0
     * @return $this
     */
    public function cleanTransactions()
    {
        return $this;
    }

    /**
     * Save order into registry to use it in the overloaded controller.
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function saveOrderAfterSubmit(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getEvent()->getData('order');
        Mage::register('hss_order', $order, true);

        return $this;
    }

    /**
     * Set data for response of frontend saveOrder action
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function setResponseAfterSaveOrder(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::registry('hss_order');

        if ($order && $order->getId()) {
            $payment = $order->getPayment();
            if ($payment && in_array($payment->getMethod(), Mage::helper('paypal/hss')->getHssMethods())) {
                /** @var Mage_Core_Controller_Varien_Action $controller */
                $controller = $observer->getEvent()->getData('controller_action');
                $result = Mage::helper('core')->jsonDecode(
                    $controller->getResponse()->getBody('default'),
                    Zend_Json::TYPE_ARRAY
                );

                if (empty($result['error'])) {
                    $controller->loadLayout('checkout_onepage_review');
                    $html = $controller->getLayout()->getBlock('paypal.iframe')->toHtml();
                    $result['update_section'] = [
                        'name' => 'paypaliframe',
                        'html' => $html
                    ];
                    $result['redirect'] = false;
                    $result['success'] = false;
                    $controller->getResponse()->clearHeader('Location');
                    $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                }
            }
        }

        return $this;
    }

    /**
     * Load country dependent PayPal solutions system configuration
     *
     * @param Varien_Event_Observer $observer
     */
    public function loadCountryDependentSolutionsConfig(Varien_Event_Observer $observer)
    {
        $countryCode = Mage::helper('paypal')->getConfigurationCountryCode();
        $paymentGroups   = $observer->getEvent()->getConfig()->getNode('sections/payment/groups');
        $paymentsConfigs = $paymentGroups->xpath('paypal_payments/*/backend_config/' . $countryCode);
        if ($paymentsConfigs) {
            foreach ($paymentsConfigs as $config) {
                $parent = $config->getParent()->getParent();
                $parent->extend($config, true);
            }
        }

        $payments = $paymentGroups->xpath('paypal_payments/*');
        foreach ($payments as $payment) {
            if ((int)$payment->include) {
                $fields = $paymentGroups->xpath((string)$payment->group . '/fields');
                if (isset($fields[0])) {
                    $fields[0]->appendChild($payment, true);
                }
            }
        }
    }

    /**
     * Update transaction with HTML representation of txn_id
     *
     * @param Varien_Event_Observer $observer
     */
    public function observeHtmlTransactionId(Varien_Event_Observer $observer)
    {
        /** @var Varien_Object $transaction */
        $transaction = $observer->getEvent()->getTransaction();
        $transaction->setHtmlTxnId(Mage::helper('paypal')->getHtmlTransactionId(
            $observer->getEvent()->getPayment()->getMethodInstance()->getCode(),
            $transaction->getTxnId()
        ));
    }
}
