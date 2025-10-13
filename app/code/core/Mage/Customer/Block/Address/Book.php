<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer address book block
 *
 * @package    Mage_Customer
 *
 * @method string getRefererUrl()
 * @method $this setRefererUrl(string $url)
 */
class Mage_Customer_Block_Address_Book extends Mage_Core_Block_Template
{
    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')
            ->setTitle(Mage::helper('customer')->__('Address Book'));

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getAddAddressUrl()
    {
        return $this->getUrl('customer/address/new', ['_secure' => true]);
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }

        return $this->getUrl('customer/account/', ['_secure' => true]);
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl(
            'customer/address/delete',
            [Mage_Core_Model_Url::FORM_KEY => Mage::getSingleton('core/session')->getFormKey()],
        );
    }

    /**
     * @param Mage_Customer_Model_Address $address
     * @return string
     */
    public function getAddressEditUrl($address)
    {
        return $this->getUrl('customer/address/edit', ['_secure' => true, 'id' => $address->getId()]);
    }

    /**
     * @return Mage_Customer_Model_Address
     */
    public function getPrimaryBillingAddress()
    {
        return $this->getCustomer()->getPrimaryBillingAddress();
    }

    /**
     * @return Mage_Customer_Model_Address
     */
    public function getPrimaryShippingAddress()
    {
        return $this->getCustomer()->getPrimaryShippingAddress();
    }

    /**
     * @return bool
     */
    public function hasPrimaryAddress()
    {
        return $this->getPrimaryBillingAddress() || $this->getPrimaryShippingAddress();
    }

    /**
     * @return bool|Mage_Customer_Model_Address[]
     */
    public function getAdditionalAddresses()
    {
        $addresses = $this->getCustomer()->getAdditionalAddresses();
        return empty($addresses) ? false : $addresses;
    }

    /**
     * @param Mage_Customer_Model_Address $address
     * @return string|null
     */
    public function getAddressHtml($address)
    {
        return $address->format('html');
        //return $address->toString($address->getHtmlFormat());
    }

    /**
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        $customer = $this->getData('customer');
        if (is_null($customer)) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $this->setData('customer', $customer);
        }

        return $customer;
    }
}
