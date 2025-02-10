<?php
/**
 * Backend for qty increments
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_CatalogInventory
 */
class Mage_CatalogInventory_Model_System_Config_Backend_Qtyincrements extends Mage_Core_Model_Config_Data
{
    /**
     * Validate data before save
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (floor($value) != $value) {
            throw new Mage_Core_Exception('Decimal qty increments is not allowed.');
        }
        return $this;
    }
}
