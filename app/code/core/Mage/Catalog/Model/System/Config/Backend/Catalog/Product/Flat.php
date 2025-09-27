<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Flat product on/off backend
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_System_Config_Backend_Catalog_Product_Flat extends Mage_Core_Model_Config_Data
{
    /**
     * After enable flat products required reindex
     *
     * @return $this
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged() && $this->getValue()) {
            Mage::getSingleton('index/indexer')->getProcessByCode('catalog_product_flat')
                ->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        }

        return $this;
    }
}
