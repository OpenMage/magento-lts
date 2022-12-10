<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer dashboard addresses section
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
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
     * @return string|null
     */
    public function getPrimaryShippingAddressHtml()
    {
        $address = $this->getCustomer()->getPrimaryShippingAddress();

        if ($address instanceof Varien_Object) {
            return $address->format('html');
        } else {
            return Mage::helper('customer')->__('You have not set a default shipping address.');
        }
    }

    /**
     * @return string|null
     */
    public function getPrimaryBillingAddressHtml()
    {
        $address = $this->getCustomer()->getPrimaryBillingAddress();

        if ($address instanceof Varien_Object) {
            return $address->format('html');
        } else {
            return Mage::helper('customer')->__('You have not set a default billing address.');
        }
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
