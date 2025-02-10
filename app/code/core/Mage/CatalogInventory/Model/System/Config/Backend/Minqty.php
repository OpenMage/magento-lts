<?php
/**
 * Minimum product qty backend model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_CatalogInventory
 */
class Mage_CatalogInventory_Model_System_Config_Backend_Minqty extends Mage_Core_Model_Config_Data
{
    /**
    * Validate minimum product qty value
    *
    * @return $this
    */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $minQty = (int) $this->getValue() >= 0 ? (int) $this->getValue() : (int) $this->getOldValue();
        $this->setValue((string) $minQty);
        return $this;
    }
}
