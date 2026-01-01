<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogInventory
 */

/**
 * MinSaleQty value manipulation helper
 *
 * @package    Mage_CatalogInventory
 */
class Mage_CatalogInventory_Helper_Minsaleqty
{
    protected $_moduleName = 'Mage_CatalogInventory';

    /**
     * Retrieve fixed qty value
     *
     * @param  mixed      $qty
     * @return null|float
     */
    protected function _fixQty($qty)
    {
        return (empty($qty) ? null : (float) $qty);
    }

    /**
     * Generate a storable representation of a value
     *
     * @param  mixed  $value
     * @return string
     */
    protected function _serializeValue($value)
    {
        if (is_numeric($value)) {
            $data = (float) $value;
            return (string) $data;
        }

        if (is_array($value)) {
            $data = [];
            foreach ($value as $groupId => $qty) {
                if (!array_key_exists($groupId, $data)) {
                    $data[$groupId] = $this->_fixQty($qty);
                }
            }

            if (count($data) == 1 && array_key_exists(Mage_Customer_Model_Group::CUST_GROUP_ALL, $data)) {
                return (string) $data[Mage_Customer_Model_Group::CUST_GROUP_ALL];
            }

            return serialize($data);
        }

        return '';
    }

    /**
     * Create a value from a storable representation
     *
     * @param  mixed $value
     * @return array
     */
    protected function _unserializeValue($value)
    {
        if (is_numeric($value)) {
            return [
                Mage_Customer_Model_Group::CUST_GROUP_ALL => $this->_fixQty($value),
            ];
        }

        if (is_string($value) && !empty($value)) {
            try {
                return Mage::helper('core/unserializeArray')->unserialize($value);
            } catch (Exception) {
                return [];
            }
        } else {
            return [];
        }
    }

    /**
     * Check whether value is in form retrieved by _encodeArrayFieldValue()
     *
     * @param  mixed $value
     * @return bool
     */
    protected function _isEncodedArrayFieldValue($value)
    {
        if (!is_array($value)) {
            return false;
        }

        unset($value['__empty']);
        foreach ($value as $row) {
            if (!is_array($row) || !array_key_exists('customer_group_id', $row) || !array_key_exists('min_sale_qty', $row)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Encode value to be used in Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
     *
     * @return array
     */
    protected function _encodeArrayFieldValue(array $value)
    {
        $result = [];
        foreach ($value as $groupId => $qty) {
            $_id = Mage::helper('core')->uniqHash('_');
            $result[$_id] = [
                'customer_group_id' => $groupId,
                'min_sale_qty' => $this->_fixQty($qty),
            ];
        }

        return $result;
    }

    /**
     * Decode value from used in Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
     *
     * @return array
     */
    protected function _decodeArrayFieldValue(array $value)
    {
        $result = [];
        unset($value['__empty']);
        foreach ($value as $row) {
            if (!is_array($row) || !array_key_exists('customer_group_id', $row) || !array_key_exists('min_sale_qty', $row)) {
                continue;
            }

            $groupId = $row['customer_group_id'];
            $qty = $this->_fixQty($row['min_sale_qty']);
            $result[$groupId] = $qty;
        }

        return $result;
    }

    /**
     * Retrieve min_sale_qty value from config
     *
     * @param  int        $customerGroupId
     * @param  mixed      $store
     * @return null|float
     */
    public function getConfigValue($customerGroupId, $store = null)
    {
        $value = Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MIN_SALE_QTY, $store);
        $value = $this->_unserializeValue($value);
        if ($this->_isEncodedArrayFieldValue($value)) {
            $value = $this->_decodeArrayFieldValue($value);
        }

        $result = null;
        foreach ($value as $groupId => $qty) {
            if ($groupId == $customerGroupId) {
                $result = $qty;
                break;
            } elseif ($groupId == Mage_Customer_Model_Group::CUST_GROUP_ALL) {
                $result = $qty;
            }
        }

        return $this->_fixQty($result);
    }

    /**
     * Make value readable by Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
     *
     * @param  mixed $value
     * @return array
     */
    public function makeArrayFieldValue($value)
    {
        $value = $this->_unserializeValue($value);
        if (!$this->_isEncodedArrayFieldValue($value)) {
            return $this->_encodeArrayFieldValue($value);
        }

        return $value;
    }

    /**
     * Make value ready for store
     *
     * @param  mixed  $value
     * @return string
     */
    public function makeStorableArrayFieldValue($value)
    {
        if ($this->_isEncodedArrayFieldValue($value)) {
            $value = $this->_decodeArrayFieldValue($value);
        }

        return $this->_serializeValue($value);
    }
}
