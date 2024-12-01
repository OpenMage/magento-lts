<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Backend for serialized array data
 *
 * @category   Mage
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
