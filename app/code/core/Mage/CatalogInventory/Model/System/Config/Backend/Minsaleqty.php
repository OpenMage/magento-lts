<?php
/**
 * Backend for serialized array data
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_CatalogInventory
 */
class Mage_CatalogInventory_Model_System_Config_Backend_Minsaleqty extends Mage_Core_Model_Config_Data
{
    /**
     * Process data after load
     * @return $this
     */
    protected function _afterLoad()
    {
        $value = $this->getValue();
        $value = Mage::helper('cataloginventory/minsaleqty')->makeArrayFieldValue($value);
        $this->setValue($value);
        return $this;
    }

    /**
     * Prepare data before save
     * @return $this
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        $value = Mage::helper('cataloginventory/minsaleqty')->makeStorableArrayFieldValue($value);
        $this->setValue($value);
        return $this;
    }
}
