<?php
/**
 * Catalog Search change Search Type backend model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Catalog_Search_Type extends Mage_Core_Model_Config_Data
{
    /**
     * After change Catalog Search Type process
     *
     * @return $this
     */
    protected function _afterSave()
    {
        $newValue = $this->getValue();
        $oldValue = Mage::getConfig()->getNode(
            Mage_CatalogSearch_Model_Fulltext::XML_PATH_CATALOG_SEARCH_TYPE,
            $this->getScope(),
            $this->getScopeId(),
        );
        if ($newValue != $oldValue) {
            Mage::getSingleton('catalogsearch/fulltext')->resetSearchResults();
        }

        return $this;
    }
}
