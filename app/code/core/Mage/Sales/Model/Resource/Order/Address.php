<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Flat sales order address resource
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Address extends Mage_Sales_Model_Resource_Order_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_address_resource';

    protected function _construct()
    {
        $this->_init('sales/order_address', 'entity_id');
    }

    /**
     * Return configuration for all attributes
     *
     * @return array
     */
    public function getAllAttributes()
    {
        $attributes = [
            'city'       => Mage::helper('sales')->__('City'),
            'company'    => Mage::helper('sales')->__('Company'),
            'country_id' => Mage::helper('sales')->__('Country'),
            'email'      => Mage::helper('sales')->__('Email'),
            'firstname'  => Mage::helper('sales')->__('First Name'),
            'middlename' => Mage::helper('sales')->__('Middle Name'),
            'lastname'   => Mage::helper('sales')->__('Last Name'),
            'region_id'  => Mage::helper('sales')->__('State/Province'),
            'street'     => Mage::helper('sales')->__('Street Address'),
            'telephone'  => Mage::helper('sales')->__('Telephone'),
            'postcode'   => Mage::helper('sales')->__('Zip/Postal Code'),
        ];
        asort($attributes);
        return $attributes;
    }

    /**
     * Update related grid table after object save
     *
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $resource = parent::_afterSave($object);
        if ($object->hasDataChanges() && $object->getOrder()) {
            $gridList = [
                'sales/order' => 'entity_id',
                'sales/order_invoice' => 'order_id',
                'sales/order_shipment' => 'order_id',
                'sales/order_creditmemo' => 'order_id'
            ];

            // update grid table after grid update
            foreach ($gridList as $gridResource => $field) {
                Mage::getResourceModel($gridResource)->updateOnRelatedRecordChanged(
                    $field,
                    $object->getParentId()
                );
            }
        }

        return $resource;
    }
}
