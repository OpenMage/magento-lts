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
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Flat sales order payment resource
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Payment extends Mage_Sales_Model_Resource_Order_Abstract
{
    /**
     * Serializeable field: additional_information
     *
     * @var array
     */
    protected $_serializableFields   = [
        'additional_information' => [null, []]
    ];

    /**
     * @var string
     */
    protected $_eventPrefix          = 'sales_order_payment_resource';

    protected function _construct()
    {
        $this->_init('sales/order_payment', 'entity_id');
    }

    /**
     * Unserialize Varien_Object field in an object
     *
     * @param string $field
     * @param mixed $defaultValue
     */
    protected function _unserializeField(Varien_Object $object, $field, $defaultValue = null)
    {
        $value = $object->getData($field);
        if (empty($value)) {
            $object->setData($field, $defaultValue);
        } elseif (!is_array($value) && !is_object($value)) {
            $unserializedValue = false;
            try {
                $unserializedValue = Mage::helper('core/unserializeArray')
                ->unserialize($value);
            } catch (Exception $e) {
                Mage::logException($e);
            }
            $object->setData($field, $unserializedValue);
        }
    }
}
