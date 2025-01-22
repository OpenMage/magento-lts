<?php

/**
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
