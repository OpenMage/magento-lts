<?php
/**
 * Category url rewrite interface
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Catalog
 */
interface Mage_Catalog_Helper_Category_Url_Rewrite_Interface
{
    /**
     * Join url rewrite table to eav collection
     *
     * @param int $storeId
     * @return Mage_Catalog_Helper_Category_Url_Rewrite
     */
    public function joinTableToEavCollection(Mage_Eav_Model_Entity_Collection_Abstract $collection, $storeId);

    /**
     * Join url rewrite table to flat collection
     *
     * @param int $storeId
     * @return Mage_Catalog_Helper_Category_Url_Rewrite_Interface
     */
    public function joinTableToCollection(Mage_Catalog_Model_Resource_Category_Flat_Collection $collection, $storeId);

    /**
     * Join url rewrite to select
     *
     * @param int $storeId
     * @return Mage_Catalog_Helper_Category_Url_Rewrite
     */
    public function joinTableToSelect(Varien_Db_Select $select, $storeId);
}
