<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog Product Eav Indexer Resource Model
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav
    extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Abstract
{
    /**
     * Define main index table
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/product_index_eav', 'entity_id');
    }

    /**
     * Reindex by entities
     *
     * @param int|array $processIds
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price
     */
    protected function _reindexEntities($processIds)
    {
        $write = $this->_getWriteAdapter();

        $this->cloneIndexTable(true);

        if (!is_array($processIds)) {
            $processIds = array($processIds);
        }

        $parentIds = $this->getRelationsByChild($processIds);
        if ($parentIds) {
            $processIds = array_unique(array_merge($processIds, $parentIds));
        }
        $childIds  = $this->getRelationsByParent($parentIds);
        if ($childIds) {
            $processIds = array_unique(array_merge($processIds, $childIds));
        }

        $this->_prepareSelectIndex($processIds);
        $this->_prepareMultiselectIndex($processIds);
        $this->_prepareRelationIndex($processIds);
        $this->_removeNotVisibleEntityFromIndex();

        $write->beginTransaction();
        try {
            // remove old index
            $where = $write->quoteInto('entity_id IN(?)', $processIds);
            $write->delete($this->getMainTable(), $where);

            // insert new index
            $this->insertFromTable($this->getIdxTable(), $this->getMainTable());

            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Process product save.
     * Method is responsible for index support
     * when product was saved and assigned categories was changed.
     *
     * @param   Mage_Index_Model_Event $event
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav
     */
    public function catalogProductSave(Mage_Index_Model_Event $event)
    {
        $productId = $event->getEntityPk();
        $data = $event->getNewData();

        /**
         * Check if filterable attribute values were updated
         */
        if (!isset($data['reindex_eav'])) {
            return $this;
        }

        return $this->_reindexEntities($productId);
    }

    /**
     * Process Product Delete
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav
     */
    public function catalogProductDelete(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['reindex_eav_parent_ids'])) {
            return $this;
        }

        return $this->_reindexEntities($data['reindex_eav_parent_ids']);
    }

    /**
     * Process Product Mass Update
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav
     */
    public function catalogProductMassAction(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['reindex_eav_product_ids'])) {
            return $this;
        }

        return $this->_reindexEntities($data['reindex_eav_product_ids']);
    }

    /**
     * Process Catalog Eav Attribute Save
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav
     */
    public function catalogEavAttributeSave(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['reindex_attribute'])) {
            return $this;
        }

        $attributeId = $event->getEntityPk();
        $write = $this->_getWriteAdapter();

        if (empty($data['is_indexable'])) {
            // remove attribute data from main index
            $write->beginTransaction();
            try {

                $where = $write->quoteInto('attribute_id=?', $attributeId);
                $write->delete($this->getMainTable(), $where);
                $write->commit();
            } catch (Exception $e) {
                $write->rollback();
                throw $e;
            }
        } else {
            $this->cloneIndexTable(true);

            $this->_prepareSelectIndex(null, $attributeId);
            $this->_prepareMultiselectIndex(null, $attributeId);
            $this->_prepareRelationIndex();
            $this->_removeNotVisibleEntityFromIndex();

            $this->beginTransaction();
            try {
                // remove index by attribute
                $where = $write->quoteInto('attribute_id=?', $attributeId);
                $write->delete($this->getMainTable(), $where);

                // insert new index
                $this->insertFromTable($this->getIdxTable(), $this->getMainTable());

                $write->commit();
            } catch (Exception $e) {
                $write->rollback();
                throw $e;
            }
        }

        return $this;
    }

    /**
     * Prepare temporary data index for select filtrable attribute
     *
     * @param array $entityIds      the entity ids limitation
     * @param int $attributeId      the attribute id limitation
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav
     */
    protected function _prepareSelectIndex($entityIds = null, $attributeId = null)
    {
        $write      = $this->_getWriteAdapter();
        $idxTable   = $this->getIdxTable();
        // prepare select attributes
        if (is_null($attributeId)) {
            $attrIds    = $this->_getFilterableAttributeIds(false);
        } else {
            $attrIds    = array($attributeId);
        }

        $select = $write->select()
            ->from(
                array('pid' => $this->getValueTable('catalog/product', 'int')),
                array('entity_id', 'attribute_id'))
            ->join(
                array('cs' => $this->getTable('core/store')),
                '',
                array('store_id'))
            ->joinLeft(
                array('pis' => $this->getValueTable('catalog/product', 'int')),
                'pis.entity_id = pid.entity_id AND pis.attribute_id = pid.attribute_id'
                    . ' AND pis.store_id=cs.store_id',
                array('value' => new Zend_Db_Expr('IF(pis.value_id > 0, pis.value, pid.value)')))
            ->where('pid.store_id=?', 0)
            ->where('cs.store_id!=?', 0)
            ->where('pid.attribute_id IN(?)', $attrIds)
            ->where('IF(pis.value_id > 0, pis.value, pid.value) IS NOT NULL');

        $statusCond = $write->quoteInto('=?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        $this->_addAttributeToSelect($select, 'status', 'pid.entity_id', 'cs.store_id', $statusCond);

        if (!is_null($entityIds)) {
            $select->where('pid.entity_id IN(?)', $entityIds);
        }

        /**
         * Add additional external limitation
         */
        Mage::dispatchEvent('prepare_catalog_product_index_select', array(
            'select'        => $select,
            'entity_field'  => new Zend_Db_Expr('pid.entity_id'),
            'website_field' => new Zend_Db_Expr('cs.website_id'),
            'store_field'   => new Zend_Db_Expr('cs.store_id')
        ));

        $query = $select->insertFromSelect($idxTable);
        $write->query($query);

        return $this;
    }

    /**
     * Prepare temporary data index for multiselect filtrable attribute
     *
     * @param array $entityIds      the entity ids limitation
     * @param int $attributeId      the attribute id limitation
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav
     */
    protected function _prepareMultiselectIndex($entityIds = null, $attributeId = null)
    {
        $write      = $this->_getWriteAdapter();
        $idxTable   = $this->getIdxTable();

        // prepare multiselect attributes
        if (is_null($attributeId)) {
            $attrIds    = $this->_getFilterableAttributeIds(true);
        } else {
            $attrIds    = array($attributeId);
        }

        $select = $write->select()
            ->from(
                array('pvd' => $this->getValueTable('catalog/product', 'varchar')),
                array('entity_id', 'attribute_id'))
            ->join(
                array('cs' => $this->getTable('core/store')),
                '',
                array('store_id'))
            ->joinLeft(
                array('pvs' => $this->getValueTable('catalog/product', 'varchar')),
                'pvs.entity_id = pvd.entity_id AND pvs.attribute_id = pvd.attribute_id'
                    . ' AND pvs.store_id=cs.store_id',
                array('value' => new Zend_Db_Expr('IF(pvs.value_id > 0, pvs.value, pvd.value)')))
            ->join(
                array('eo' => $this->getTable('eav/attribute_option')),
                'FIND_IN_SET(eo.option_id, IF(pvs.value_id, pvs.value, pvd.value))',
                array()
            )
            ->where('pvd.store_id=?', 0)
            ->where('cs.store_id!=?', 0)
            ->where('pvd.attribute_id IN(?)', $attrIds);

        $statusCond = $write->quoteInto('=?', '=' . Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        $this->_addAttributeToSelect($select, 'status', 'pvd.entity_id', 'cs.store_id', $statusCond);

        if (!is_null($entityIds)) {
            $select->where('pvd.entity_id IN(?)', $entityIds);
        }

        /**
         * Add additional external limitation
         */
        Mage::dispatchEvent('prepare_catalog_product_index_select', array(
            'select'        => $select,
            'entity_field'  => new Zend_Db_Expr('pvd.entity_id'),
            'website_field' => new Zend_Db_Expr('cs.website_id'),
            'store_field'   => new Zend_Db_Expr('cs.store_id')
        ));

        $query = $select->insertFromSelect($idxTable);
        $write->query($query);

        return $this;
    }

    /**
     * Prepare temporary data index for product relations
     *
     * @param array $parentIds  the parent entity ids limitation
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav
     */
    protected function _prepareRelationIndex($parentIds = null)
    {
        $write      = $this->_getWriteAdapter();
        $idxTable   = $this->getIdxTable();

        $select = $write->select()
            ->from(array('l' => $this->getTable('catalog/product_relation')), 'parent_id')
            ->join(
                array('cs' => $this->getTable('core/store')),
                '',
                array())
            ->join(
                array('i' => $idxTable),
                'l.child_id=i.entity_id AND cs.store_id = i.store_id',
                array('attribute_id', 'store_id', 'value'));
        if (!is_null($parentIds)) {
            $select->where('l.parent_id IN(?)', $parentIds);
        }

        /**
         * Add additional external limitation
         */
        Mage::dispatchEvent('prepare_catalog_product_index_select', array(
            'select'        => $select,
            'entity_field'  => new Zend_Db_Expr('l.parent_id'),
            'website_field' => new Zend_Db_Expr('cs.website_id'),
            'store_field'   => new Zend_Db_Expr('cs.store_id')
        ));

        $query = $select->insertIgnoreFromSelect($idxTable);
        $write->query($query);

        return $this;
    }

    /**
     * Remove Not Visible products from temporary data index
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav
     */
    protected function _removeNotVisibleEntityFromIndex()
    {
        $write      = $this->_getWriteAdapter();
        $idxTable   = $this->getIdxTable();

        $select = $write->select()
            ->from($idxTable, null);

        $condition = $write->quoteInto('=?',Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
        $this->_addAttributeToSelect($select, 'visibility', $idxTable.'.entity_id', $idxTable.'.store_id', $condition);

        $query = $select->deleteFromSelect($idxTable);
        $write->query($query);

        return $this;
    }

    /**
     * Rebuild all index data
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav
     */
    public function reindexAll()
    {
        $this->cloneIndexTable(true);

        $this->_prepareSelectIndex();
        $this->_prepareMultiselectIndex();
        $this->_prepareRelationIndex();
        $this->_removeNotVisibleEntityFromIndex();

        $this->syncData();
        return $this;
    }

    /**
     * Retrieve filterable (used in LN) attribute ids
     *
     * @param bool $multiSelect
     * @return array
     */
    protected function _getFilterableAttributeIds($multiSelect)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('ca' => $this->getTable('catalog/eav_attribute')), 'attribute_id')
            ->join(
                array('ea' => $this->getTable('eav/attribute')),
                'ca.attribute_id = ea.attribute_id',
                array())
            ->where('ca.is_filterable_in_search>0 OR ca.is_filterable>0');

        if ($multiSelect == true) {
            $select->where('ea.backend_type = ?', 'varchar')
                ->where('ea.frontend_input = ?', 'multiselect');
        } else {
            $select->where('ea.backend_type = ?', 'int')
                ->where('ea.frontend_input = ?', 'select');
        }

        return $this->_getReadAdapter()->fetchCol($select);
    }
}
