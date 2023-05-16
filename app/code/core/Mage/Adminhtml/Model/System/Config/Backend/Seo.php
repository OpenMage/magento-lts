<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Seo extends Mage_Core_Model_Config_Data
{
    /**
     * Refresh categories and products url rewrites if configuration was changed
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            Mage::getModel('index/indexer')
                ->getProcessByCode(Mage_Catalog_Helper_Category_Flat::CATALOG_CATEGORY_FLAT_PROCESS_CODE)
                ->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);

            Mage::getModel('index/indexer')
                ->getProcessByCode(Mage_Catalog_Helper_Product_Flat::CATALOG_FLAT_PROCESS_CODE)
                ->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        }
        return $this;
    }
}
