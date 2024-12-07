<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Payment
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Payment Observer
 *
 * @category   Mage
 * @package    Mage_Payment
 */
class Mage_Payment_Model_Observer
{
    /**
     * Set forced canCreditmemo flag
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function salesOrderBeforeSave($observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getEvent()->getOrder();

        if ($order->getPayment() && $order->getPayment()->getMethodInstance()->getCode() != 'free') {
            return $this;
        }

        if ($order->canUnhold()) {
            return $this;
        }

        if ($order->isCanceled() || $order->getState() === Mage_Sales_Model_Order::STATE_CLOSED) {
            return $this;
        }
        /**
         * Allow forced creditmemo just in case if it wasn't defined before
         */
        if (!$order->hasForcedCanCreditmemo()) {
            $order->setForcedCanCreditmemo(true);
        }
        return $this;
    }

    /**
     * Collect buy request and set it as custom option
     *
     * Also sets the collected information and schedule as informational static options
     *
     * @param Varien_Event_Observer $observer
     */
    public function prepareProductRecurringProfileOptions($observer)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $observer->getEvent()->getProduct();
        $buyRequest = $observer->getEvent()->getBuyRequest();

        if (!$product->isRecurring()) {
            return;
        }

        $profile = Mage::getModel('payment/recurring_profile')
            ->setLocale(Mage::app()->getLocale())
            ->setStore(Mage::app()->getStore())
            ->importBuyRequest($buyRequest)
            ->importProduct($product);
        if (!$profile) {
            return;
        }

        // add the start datetime as product custom option
        $product->addCustomOption(
            Mage_Payment_Model_Recurring_Profile::PRODUCT_OPTIONS_KEY,
            serialize(['start_datetime' => $profile->getStartDatetime()]),
        );

        // duplicate as 'additional_options' to render with the product statically
        $infoOptions = [[
            'label' => $profile->getFieldLabel('start_datetime'),
            'value' => $profile->exportStartDatetime(true),
        ]];

        foreach ($profile->exportScheduleInfo() as $info) {
            $infoOptions[] = [
                'label' => $info->getTitle(),
                'value' => $info->getSchedule(),
            ];
        }
        $product->addCustomOption('additional_options', serialize($infoOptions));
    }

    /**
     * Sets current instructions for bank transfer account
     */
    public function beforeOrderPaymentSave(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order_Payment $payment */
        $payment = $observer->getEvent()->getPayment();
        if ($payment->getMethod() === Mage_Payment_Model_Method_Banktransfer::PAYMENT_METHOD_BANKTRANSFER_CODE) {
            $payment->setAdditionalInformation(
                'instructions',
                $payment->getMethodInstance()->setStore($payment->getOrder()->getStoreId())->getInstructions(),
            );
        }
    }

    /**
     * Will veto the unassignment of the order status if it is currently configured in any of the payment method
     * configurations.
     *
     * @param Varien_Event_Observer $observer
     * @throws Mage_Core_Exception
     */
    public function beforeSalesOrderStatusUnassign($observer)
    {
        $state = $observer->getEvent()->getState();
        if ($state == Mage_Sales_Model_Order::STATE_NEW) {
            /** @var Mage_Sales_Model_Order_Status|false $statusModel */
            $statusModel = $observer->getEvent()->getStatus();
            $status      = $statusModel->getStatus();
            $used        = 0;
            $titles      = [];
            foreach (Mage::app()->getWebsites(true) as $website) {
                $store = current($website->getStores()); // just need one store from each website
                if (!$store) {
                    continue; // no store is associated with the website
                }
                foreach (Mage::helper('payment')->getPaymentMethods($store) as $value) {
                    if (isset($value['order_status']) && $value['order_status'] == $status && $value['active']) {
                        ++$used;

                        // Remember the payment's information
                        $title       = $value['title'];
                        $websiteName = $website->getName();
                        if (array_key_exists($title, $titles)) {
                            $titles[$title][] = $websiteName;
                        } else {
                            $titles[$title]   = [$websiteName];
                        }
                    }
                }
            }
            if ($used > 0) {
                // build the error message, and throw it
                $methods = '';
                $spacer  = '';
                foreach ($titles as $key => $values) {
                    $methods = $methods . $spacer . $key . ' [' . implode(', ', $values) . ']';
                    $spacer = ', ';
                }
                throw new Mage_Core_Exception(Mage::helper('sales')->__(
                    'Status "%s" cannot be unassigned. It is in used in %d payment method configuration(s): %s',
                    $statusModel->getLabel(),
                    $used,
                    $methods,
                ));
            }
        }
    }
}
