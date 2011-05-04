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
 * @package     Mage_GoogleCheckout
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * GoogleCheckout data helper
 */
class Mage_GoogleCheckout_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Google Checkout settings
     */
    const XML_PATH_REQUEST_PHONE                     = 'google/checkout/request_phone';
    const XML_PATH_DISABLE_DEFAULT_TAX_TABLES        = 'google/checkout/disable_default_tax_tables';

    /**
     * Google Checkout Shipping - Digital Delivery settings
     */
    const XML_PATH_SHIPPING_VIRTUAL_ACTIVE           = 'google/checkout_shipping_virtual/active';
    const XML_PATH_SHIPPING_VIRTUAL_SCHEDULE         = 'google/checkout_shipping_virtual/schedule';
    const XML_PATH_SHIPPING_VIRTUAL_METHOD           = 'google/checkout_shipping_virtual/method';

    /**
     * Google Checkout Shipping - Carrier Calculated settings
     */
    const XML_PATH_SHIPPING_CARRIER_ACTIVE           = 'google/checkout_shipping_carrier/active';
    const XML_PATH_SHIPPING_CARRIER_METHODS          = 'google/checkout_shipping_carrier/methods';
    const XML_PATH_SHIPPING_CARRIER_DEFAULT_PRICE    = 'google/checkout_shipping_carrier/default_price';
    const XML_PATH_SHIPPING_CARRIER_DEFAULT_WIDTH    = 'google/checkout_shipping_carrier/default_width';
    const XML_PATH_SHIPPING_CARRIER_DEFAULT_HEIGHT   = 'google/checkout_shipping_carrier/default_height';
    const XML_PATH_SHIPPING_CARRIER_DEFAULT_LENGTH   = 'google/checkout_shipping_carrier/default_length';
    const XML_PATH_SHIPPING_CARRIER_ADDRESS_CATEGORY = 'google/checkout_shipping_carrier/address_category';

    /**
     * Google Checkout Shipping - Flat Rate settings
     */
    const XML_PATH_SHIPPING_FLATRATE_ACTIVE          = 'google/checkout_shipping_flatrate/active';

    /**
     * Google Checkout Shipping - Merchant Calculated settings
     */
    const XML_PATH_SHIPPING_MERCHANT_ACTIVE          = 'google/checkout_shipping_merchant/active';
    const XML_PATH_SHIPPING_MERCHANT_ALLOWED_METHODS = 'google/checkout_shipping_merchant/allowed_methods';

    /**
     * Google Checkout Shipping - Pickup settings
     */
    const XML_PATH_SHIPPING_PICKUP_ACTIVE            = 'google/checkout_shipping_pickup/active';
    const XML_PATH_SHIPPING_PICKUP_TITLE             = 'google/checkout_shipping_pickup/title';
    const XML_PATH_SHIPPING_PICKUP_PRICE             = 'google/checkout_shipping_pickup/price';
}
