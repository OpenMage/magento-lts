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
class Mage_Customer_Model_Config_Address_Forms
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'adminhtml_customer_address',
                'label' => Mage::helper('customer')->__('Adminhtml Customer Address')
            ],
            [
                'value' => 'customer_address_edit',
                'label' => Mage::helper('customer')->__('Customer Address Edit')
            ],
            [
                'value' => 'customer_register_address',
                'label' => Mage::helper('customer')->__('Customer Register Address')
            ],
        ];
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
