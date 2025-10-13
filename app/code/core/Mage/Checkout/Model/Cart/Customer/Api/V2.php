<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 */

/**
 * Shoping cart api for customer data
 *
 * @package    Mage_Checkout
 */
class Mage_Checkout_Model_Cart_Customer_Api_V2 extends Mage_Checkout_Model_Cart_Customer_Api
{
    /**
     * Prepare customer entered data for implementing
     *
     * @param  object $data
     * @return array
     */
    protected function _prepareCustomerData($data)
    {
        if (($_data = get_object_vars($data)) !== null) {
            return parent::_prepareCustomerData($_data);
        }

        return [];
    }

    /**
     * Prepare customer entered data for implementing
     *
     * @param  object $data
     * @return array|null
     */
    protected function _prepareCustomerAddressData($data)
    {
        if (is_array($data)) {
            $dataAddresses = [];
            foreach ($data as $addressItem) {
                if (($_addressItem = get_object_vars($addressItem)) !== null) {
                    $dataAddresses[] = $_addressItem;
                }
            }

            return parent::_prepareCustomerAddressData($dataAddresses);
        }

        return null;
    }
}
