<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Config Resource Model
 *
 * @category   Mage
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
     * @var int
     */
    protected $_storeId          = null;

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
        $this->_storeId = (int)$storeId;
        return $this;
    }

    /**
     * Return store id.
     * If is not set return current app store
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->_storeId ?? Mage::app()->getStore()->getId();
    }

    /**
     * Retrieve catalog_product entity type id
     *
     * @return int
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
     */
    public function getAttributesUsedInListing()
    {
        $adapter = $this->_getReadAdapter();
        $storeLabelExpr = $adapter->getCheckSql('al.value IS NOT NULL', 'al.value', 'main_table.frontend_label');

        $select  = $adapter->select()
            ->from(['main_table' => $this->getTable('eav/attribute')])
            ->join(
                ['additional_table' => $this->getTable('catalog/eav_attribute')],
                'main_table.attribute_id = additional_table.attribute_id'
            )
            ->joinLeft(
                ['al' => $this->getTable('eav/attribute_label')],
                'al.attribute_id = main_table.attribute_id AND al.store_id = ' . (int)$this->getStoreId(),
                ['store_label' => $storeLabelExpr]
            )
            ->where('main_table.entity_type_id = ?', (int)$this->getEntityTypeId())
            ->where('additional_table.used_in_product_listing = ?', 1);

        return $adapter->fetchAll($select);
    }

    /**
     * Retrieve Used Product Attributes for Catalog Product Listing Sort By
     *
     * @return array
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
                []
            )
            ->joinLeft(
                ['al' => $this->getTable('eav/attribute_label')],
                'al.attribute_id = main_table.attribute_id AND al.store_id = ' . (int)$this->getStoreId(),
                ['store_label' => $storeLabelExpr]
            )
            ->where('main_table.entity_type_id = ?', (int)$this->getEntityTypeId())
            ->where('additional_table.used_for_sort_by = ?', 1);

        return $adapter->fetchAll($select);
    }
}
