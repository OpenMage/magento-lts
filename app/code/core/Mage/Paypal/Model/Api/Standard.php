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
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * PayPal Standard checkout request API
 */
class Mage_Paypal_Model_Api_Standard extends Mage_Paypal_Model_Api_Abstract
{
    /**
     * Global interface map and export filters
     * @var array
     */
    protected $_globalMap = array(
        // commands
        'business'      => 'business_account',
        'notify_url'    => 'notify_url',
        'return'        => 'return_url',
        'cancel_return' => 'cancel_url',
        'bn'            => 'build_notation_code',
        'paymentaction' => 'payment_action',
        // payment
        'invoice'       => 'order_id',
        'currency_code' => 'currency_code',
        'amount'        => 'amount',
        'shipping'      => 'shipping_amount',
        'tax_cart'      => 'tax_amount',
        'discount_amount_cart' => 'discount_amount',
        // misc
        'item_name'        => 'cart_summary',
        // page design settings
        'page_style'             => 'page_style',
        'cpp_header_image'       => 'hdrimg',
        'cpp_headerback_color'   => 'hdrbackcolor',
        'cpp_headerborder_color' => 'hdrbordercolor',
        'cpp_payflow_color'      => 'payflowcolor',
//        'cs' => '', // TODO
    );
    protected $_exportToRequestFilters = array(
        'amount'   => '_filterAmount',
        'shipping' => '_filterAmount',
    );

    /**
     * Interface for common and "aggregated order" specific fields
     * @var array
     */
    protected $_commonRequestFields = array(
        'business', 'invoice', 'currency_code', 'paymentaction', 'return', 'cancel_return', 'notify_url', 'bn',
        'page_style', 'cpp_header_image', 'cpp_headerback_color', 'cpp_headerborder_color', 'cpp_payflow_color'
    );
    protected $_aggregatedOrderFields = array('item_name', 'amount', 'shipping');

    /**
     * Keys that are not supposed to get into debug dump
     *
     * @var array
     */
    protected $_obscureDebugFor = array('business');

    /**
     * Line items export mapping settings
     * @var array
     */
    protected $_lineItemExportTotals = array(
        'tax'      => 'tax_cart',
        'discount' => 'discount_amount_cart',
    );
    protected $_lineItemExportItemsFormat = array(
        'id'     => 'item_number_%d',
        'name'   => 'item_name_%d',
        'qty'    => 'quantity_%d',
        'amount' => 'amount_%d',
    );

    /**
     * Address export to request map
     * @var array
     */
    protected $_addressMap = array(
        'city'       => 'city',
        'country'    => 'country_id',
        'email'      => 'email',
        'first_name' => 'firstname',
        'last_name'  => 'lastname',
        'zip'        => 'postcode',
        'state'      => 'region',
        'address1'   => 'street',
        'address2'   => 'street2',
    );

    /**
     * Generate PayPal Standard checkout request fields
     * Depending on whether there are cart line items set, will aggregate everything or display items specifically
     * Shipping amount in cart line items is implemented as a separate "fake" line item
     */
    public function getStandardCheckoutRequest()
    {
        $request = $this->_exportToRequest($this->_commonRequestFields);
        $request['charset'] = 'utf-8';
        // cart line items
        if ($this->getLineItems()) {
            $this->_exportLineItems($request, 1);
            $request = array_merge($request, array(
                'cmd'    => '_cart',
                'upload' => 1,
            ));
        }
        // aggregated order
        else {
            $request = $this->_exportToRequest($this->_aggregatedOrderFields, $request);
            $request = array_merge($request, array(
                'cmd'           => '_ext-enter',
                'redirect_cmd'  => '_xclick',
            ));
        }
        // payer address
        $this->_importAddress($request);
        $this->debugRequest($request); // TODO: this is not supposed to be called in getter
        return $request;
    }

    /**
     * Merchant account email getter
     * @return string
     */
    public function getBusinessAccount()
    {
        return $this->_getDataOrConfig('business_account');
    }

    /**
     * Payment action getter
     * @return string
     */
    public function getPaymentAction()
    {
        return strtolower(parent::getPaymentAction());
    }

    public function debugRequest($request)
    {
        if (!$this->_config->debugFlag) {
            return;
        }
        foreach ($this->_obscureDebugFor as $key) {
            if (isset($request[$key])) {
                $request[$key] = '***';
            }
        }
        $debug = Mage::getModel('paypal/api_debug')
            ->setApiEndpoint($this->_config->getPaypalUrl())
            ->setRequestBody(var_export($request, 1))
            ->save()
        ;
    }

    /**
     * Import address object, if set, to the request
     *
     * @param array $request
     */
    protected function _importAddress(&$request)
    {
        $address = $this->getAddress();
        if (!$address) {
            if ($this->getNoShipping()) {
                $request['no_shipping'] = 1;
            }
            return;
        }
        $request = Varien_Object_Mapper::accumulateByMap($address, $request, array_flip($this->_addressMap));
        if ($regionCode = $this->_lookupRegionCodeFromAddress($address)) {
            $request['state'] = $regionCode;
        }
        $this->_importStreetFromAddress($address, $request, 'address1', 'address2');
        $request['address_override'] = 1;
    }
}
