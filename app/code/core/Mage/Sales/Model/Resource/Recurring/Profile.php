<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Recurring payment profiles resource model
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Recurring_Profile extends Mage_Sales_Model_Resource_Abstract
{
    /**
     * Initialize main table and column
     *
     */
    protected function _construct()
    {
        $this->_init('sales/recurring_profile', 'profile_id');

        $this->_serializableFields = [
            'profile_vendor_info'    => [null, []],
            'additional_info' => [null, []],

            'order_info' => [null, []],
            'order_item_info' => [null, []],
            'billing_address_info' => [null, []],
            'shipping_address_info' => [null, []],
        ];
    }

    /**
     * Unserialize Varien_Object field in an object
     *
     * @param string $field
     * @param mixed $defaultValue
     */
    protected function _unserializeField(Varien_Object $object, $field, $defaultValue = null)
    {
        if ($field != 'additional_info') {
            return parent::_unserializeField($object, $field, $defaultValue);
        }
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

    /**
     * Return recurring profile child Orders Ids
     *
     *
     * @param Varien_Object $object
     * @return array
     */
    public function getChildOrderIds($object)
    {
        $adapter = $this->_getReadAdapter();
        $bind    = [':profile_id' => $object->getId()];
        $select  = $adapter->select()
            ->from(
                ['main_table' => $this->getTable('sales/recurring_profile_order')],
                ['order_id'],
            )
            ->where('profile_id=:profile_id');

        return $adapter->fetchCol($select, $bind);
    }

    /**
     * Add order relation to recurring profile
     *
     * @param int $recurringProfileId
     * @param int $orderId
     * @return $this
     */
    public function addOrderRelation($recurringProfileId, $orderId)
    {
        $this->_getWriteAdapter()->insert(
            $this->getTable('sales/recurring_profile_order'),
            [
                'profile_id' => $recurringProfileId,
                'order_id'   => $orderId,
            ],
        );
        return $this;
    }
}
