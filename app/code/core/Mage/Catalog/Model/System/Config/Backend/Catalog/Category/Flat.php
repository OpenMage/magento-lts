<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Flat category on/off backend
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_System_Config_Backend_Catalog_Category_Flat extends Mage_Core_Model_Config_Data
{
    /**
     * After enable flat category required reindex
     *
     * @return $this
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged() && $this->getValue()) {
            Mage::getModel('index/indexer')
                ->getProcessByCode(Mage_Catalog_Helper_Category_Flat::CATALOG_CATEGORY_FLAT_PROCESS_CODE)
                ->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        }

        return $this;
    }
}
