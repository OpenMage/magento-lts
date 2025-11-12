<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog Config Resource Model
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Resource_Config extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * catalog_product entity type id
     *
     * @var int
     */
    protected $_entityTypeId;

    /**
     * Store id
     *
     * @var null|int
     */
    protected $_storeId = null;

    protected function _construct()
    {
        $this->_init('eav/attribute', 'attribute_id');
    }

    /**
     * Set store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = (int) $storeId;
        return $this;
    }

    /**
     * Return store id.
     * If is not set return current app store
     *
     * @return int
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getStoreId()
    {
        return $this->_storeId ?? Mage::app()->getStore()->getId();
    }

    /**
     * Retrieve catalog_product entity type id
     *
     * @return int
     * @throws Mage_Core_Exception
     */
    public function getEntityTypeId()
    {
        if ($this->_entityTypeId === null) {
            $this->_entityTypeId = Mage::getSingleton('eav/config')->getEntityType(Mage_Catalog_Model_Product::ENTITY)->getId();
        }

        return $this->_entityTypeId;
    }

    /**
     * Retrieve Product Attributes Used in Catalog Product listing
     *
     * @return array
     * @throws Mage_Core_Exception
     */
    public function getAttributesUsedInListing()
    {
        $adapter = $this->_getReadAdapter();
        $storeLabelExpr = $adapter->getCheckSql('al.value IS NOT NULL', 'al.value', 'main_table.frontend_label');

        $select  = $adapter->select()
            ->from(['main_table' => $this->getTable('eav/attribute')])
            ->join(
                ['additional_table' => $this->getTable('catalog/eav_attribute')],
                'main_table.attribute_id = additional_table.attribute_id',
            )
            ->joinLeft(
                ['al' => $this->getTable('eav/attribute_label')],
                'al.attribute_id = main_table.attribute_id AND al.store_id = ' . (int) $this->getStoreId(),
                ['store_label' => $storeLabelExpr],
            )
            ->where('main_table.entity_type_id = ?', (int) $this->getEntityTypeId())
            ->where('additional_table.used_in_product_listing = ?', 1);

        return $adapter->fetchAll($select);
    }

    /**
     * Retrieve Used Product Attributes for Catalog Product Listing Sort By
     *
     * @return array
     * @throws Mage_Core_Exception
     */
    public function getAttributesUsedForSortBy()
    {
        $adapter = $this->_getReadAdapter();
        $storeLabelExpr = $adapter->getCheckSql('al.value IS NULL', 'main_table.frontend_label', 'al.value');
        $select = $adapter->select()
            ->from(['main_table' => $this->getTable('eav/attribute')])
            ->join(
                ['additional_table' => $this->getTable('catalog/eav_attribute')],
                'main_table.attribute_id = additional_table.attribute_id',
                [],
            )
            ->joinLeft(
                ['al' => $this->getTable('eav/attribute_label')],
                'al.attribute_id = main_table.attribute_id AND al.store_id = ' . (int) $this->getStoreId(),
                ['store_label' => $storeLabelExpr],
            )
            ->where('main_table.entity_type_id = ?', (int) $this->getEntityTypeId())
            ->where('additional_table.used_for_sort_by = ?', 1);

        return $adapter->fetchAll($select);
    }
}
