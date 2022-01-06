<?php

class Mage_Adminhtml_Model_System_Config_Backend_Catalog_Search_Separator extends Mage_Core_Model_Config_Data
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
            Mage_CatalogSearch_Model_Fulltext::XML_PATH_CATALOG_SEARCH_SEPARATOR,
            $this->getScope(),
            $this->getScopeId()
        );
        if ($newValue != $oldValue) {
            Mage::getSingleton('catalogsearch/fulltext')->resetSearchResults();
        }

        return $this;
    }
}
