<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2018-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *  Website Payments Pro Hosted Solution request model to get token.
 *
 * @category   Mage
 * @package    Mage_Paypal
 */
class Mage_Paypal_Model_Hostedpro_Request extends Varien_Object
{
    /**
     * Request's order model
     *
     * @var Mage_Sales_Model_Order
     */
    protected $_order;

    /**
     * Request's Hosted Pro payment method model
     *
     * @var Mage_Paypal_Model_Hostedpro
     */
    protected $_paymentMethod;

    /**
     * Name format for button variables
     *
     * @var string
     */
    protected $_buttonVarFormat = 'L_BUTTONVAR%d';

    /**
     * Request Parameters which don't have to wrap as button vars
     *
     * @var array
     */
    protected $_notButtonVars = [
        'METHOD', 'BUTTONCODE', 'BUTTONTYPE'];

    /**
     * Build and return request array from object data
     *
     * @return array
     */
    public function getRequestData()
    {
        $requestData = [];
        if (!empty($this->_data)) {
            // insert params to request as additional button variables,
            // except special params from _notButtonVars list
            $i = 0;
            foreach ($this->_data as $key => $value) {
                if (in_array($key, $this->_notButtonVars)) {
                    $requestData[$key] = $value;
                } else {
                    $varKey = sprintf($this->_buttonVarFormat, $i);
                    $requestData[$varKey] = $key . '=' . $value;
                    $i++;
                }
            }
        }

        return $requestData;
    }

    /**
     * Append payment data to request
     *
     * @param Mage_Paypal_Model_Hostedpro $paymentMethod
     * @return $this
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->_paymentMethod = $paymentMethod;
        $requestData = $this->_getPaymentData($paymentMethod);
        $this->addData($requestData);

        return $this;
    }

    /**
     * Append order data to request
     *
     * @param Mage_Sales_Model_Order $order
     * @return $this
     */
    public function setOrder($order)
    {
        $this->_order = $order;
        $requestData = $this->_getOrderData($order);
        $this->addData($requestData);

        return $this;
    }

    /**
     * Get peymet request data as array
     *
     * @return array
     */
    protected function _getPaymentData(Mage_Paypal_Model_Hostedpro $paymentMethod)
    {
        return [
            'paymentaction' => strtolower($paymentMethod->getConfigData('payment_action')),
            'notify_url'    => $paymentMethod->getNotifyUrl(),
            'cancel_return' => $paymentMethod->getCancelUrl(),
            'return'        => $paymentMethod->getReturnUrl(),
            'lc'            => substr(Mage::app()->getLocale()->getLocaleCode(), -2), //gets language from locale code

            'template'              => $paymentMethod->getTemplate(),
            'showBillingAddress'    => 'false',
            'showShippingAddress'   => 'true',
            'showBillingEmail'      => 'false',
            'showBillingPhone'      => 'false',
            'showCustomerName'      => 'false',
            'showCardInfo'          => 'true',
            'showHostedThankyouPage' => 'false'
        ];
    }

    /**
     * Get order request data as array
     *
     * @return array
     */
    protected function _getOrderData(Mage_Sales_Model_Order $order)
    {
        $request = [
            'subtotal'      => $this->_formatPrice($order->getBaseSubtotal()),
            'tax'           => $this->_formatPrice($order->getBaseTaxAmount() + $order->getHiddenTaxAmount()),
            'shipping'      => $this->_formatPrice($order->getBaseShippingAmount()),
            'invoice'       => $order->getIncrementId(),
            'address_override' => 'true',
            'currency_code'    => $order->getBaseCurrencyCode(),
            'buyer_email'      => $order->getCustomerEmail(),
            'discount'         => $this->_formatPrice(
                $order->getBaseGiftCardsAmount()
                + abs($order->getBaseDiscountAmount())
                + $order->getBaseCustomerBalanceAmount()
            ),
        ];

        // append to request billing address data
        if ($billingAddress = $order->getBillingAddress()) {
            $request = array_merge($request, $this->_getBillingAddress($billingAddress));
        }

        // append to request shipping address data
        if ($shippingAddress = $order->getShippingAddress()) {
            $request = array_merge($request, $this->_getShippingAddress($shippingAddress));
        }

        return $request;
    }

    /**
     * Get shipping address request data
     *
     * @return array
     */
    protected function _getShippingAddress(Varien_Object $address)
    {
        $request = [
            'first_name' => $address->getFirstname(),
            'last_name' => $address->getLastname(),
            'city'      => $address->getCity(),
            'state'     => $address->getRegionCode() ? $address->getRegionCode() : $address->getCity(),
            'zip'       => $address->getPostcode(),
            'country'   => $address->getCountry(),
        ];

        // convert streets to tow lines format
        $street = Mage::helper('customer/address')
            ->convertStreetLines($address->getStreet(), 2);

        $request['address1'] = $street[0] ?? '';
        $request['address2'] = $street[1] ?? '';

        return $request;
    }

    /**
     * Get billing address request data
     *
     * @return array
     */
    protected function _getBillingAddress(Varien_Object $address)
    {
        $request = [
            'billing_first_name' => $address->getFirstname(),
            'billing_last_name' => $address->getLastname(),
            'billing_city'      => $address->getCity(),
            'billing_state'     => $address->getRegionCode() ? $address->getRegionCode() : $address->getCity(),
            'billing_zip'       => $address->getPostcode(),
            'billing_country'   => $address->getCountry(),
        ];

        // convert streets to tow lines format
        $street = Mage::helper('customer/address')
            ->convertStreetLines($address->getStreet(), 2);

        $request['billing_address1'] = $street[0] ?? '';
        $request['billing_address2'] = $street[1] ?? '';

        return $request;
    }

    /**
     * Format price string
     *
     * @param mixed $string
     * @return mixed
     */
    protected function _formatPrice($string)
    {
        return sprintf('%.2F', $string);
    }
}
