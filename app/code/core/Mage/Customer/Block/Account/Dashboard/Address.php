<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer dashboard addresses section
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Block_Account_Dashboard_Address extends Mage_Core_Block_Template
{
    /**
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    /**
     * @return null|string
     */
    public function getPrimaryShippingAddressHtml()
    {
        $address = $this->getCustomer()->getPrimaryShippingAddress();

        if ($address instanceof Varien_Object) {
            return $address->format('html');
        }

        return Mage::helper('customer')->__('You have not set a default shipping address.');
    }

    /**
     * @return null|string
     */
    public function getPrimaryBillingAddressHtml()
    {
        $address = $this->getCustomer()->getPrimaryBillingAddress();

        if ($address instanceof Varien_Object) {
            return $address->format('html');
        }

        return Mage::helper('customer')->__('You have not set a default billing address.');
    }

    /**
     * @return string
     */
    public function getPrimaryShippingAddressEditUrl()
    {
        return Mage::getUrl('customer/address/edit', ['id' => $this->getCustomer()->getDefaultShipping()]);
    }

    /**
     * @return string
     */
    public function getPrimaryBillingAddressEditUrl()
    {
        return Mage::getUrl('customer/address/edit', ['id' => $this->getCustomer()->getDefaultBilling()]);
    }

    /**
     * @return string
     */
    public function getAddressBookUrl()
    {
        return $this->getUrl('customer/address/');
    }
}
