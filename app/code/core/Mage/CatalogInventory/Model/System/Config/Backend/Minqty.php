<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 */

/**
 * Minimum product qty backend model
 *
 * @category   Mage
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
