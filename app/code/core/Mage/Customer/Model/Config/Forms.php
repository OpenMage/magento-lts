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
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Used in creating options for use_in_forms selection
 *
 */
class Mage_Customer_Model_Config_Forms
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'adminhtml_checkout',
                'label' => Mage::helper('customer')->__('Adminhtml Checkout')
            ),
            array(
                'value' => 'adminhtml_customer',
                'label' => Mage::helper('customer')->__('Adminhtml Customer')
            ),
            array(
                'value' => 'checkout_register',
                'label' => Mage::helper('customer')->__('Checkout Register')
            ),
            array(
                'value' => 'customer_account_create',
                'label' => Mage::helper('customer')->__('Customer Account Create')
            ),
            array(
                'value' => 'customer_account_edit',
                'label' => Mage::helper('customer')->__('Customer Account Edit')
            ),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toOptionHash()
    {
        return array_combine(
            array_column($this->toOptionArray(), 'value'),
            array_column($this->toOptionArray(), 'label')
        );
    }
}
