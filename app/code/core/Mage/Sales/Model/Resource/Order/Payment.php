<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Flat sales order payment resource
 *
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
        'additional_information' => [null, []],
    ];

    /**
     * @var string
     */
    protected $_eventPrefix          = 'sales_order_payment_resource';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sales/order_payment', 'entity_id');
    }

    /**
     * Unserialize Varien_Object field in an object
     *
     * @param string $field
     * @param mixed  $defaultValue
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
