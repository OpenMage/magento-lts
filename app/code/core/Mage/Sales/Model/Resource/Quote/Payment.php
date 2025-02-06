<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Quote payment resource model
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Quote_Payment extends Mage_Sales_Model_Resource_Abstract
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
     * Main table and field initialization
     *
     */
    protected function _construct()
    {
        $this->_init('sales/quote_payment', 'payment_id');
    }

    /**
     * @param string $field
     * @param mixed $defaultValue
     * @see Mage_Core_Model_Resource_Abstract::_unserializeField()
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
