<?php

/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Product Eav Decimal Attributes Indexer resource model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Product_Indexer_Eav_Decimal extends Mage_Catalog_Model_Resource_Product_Indexer_Eav_Abstract
{
    protected function _construct()
    {
        $this->_init('catalog/product_index_eav_decimal', 'entity_id');
    }

    /**
     * Prepare data index for indexable attributes
     *
     * @param array $entityIds      the entity ids limitation
     * @param int $attributeId      the attribute id limitation
     * @return $this
     */
    protected function _prepareIndex($entityIds = null, $attributeId = null)
    {
        $write      = $this->_getWriteAdapter();
        $idxTable   = $this->getIdxTable();
        // prepare select attributes
        if (is_null($attributeId)) {
            $attrIds    = $this->_getIndexableAttributes();
        } else {
            $attrIds    = [$attributeId];
        }

        if (!$attrIds) {
            return $this;
        }

        $productValueExpression = $write->getCheckSql('pds.value_id > 0', 'pds.value', 'pdd.value');
        $select = $write->select()
            ->from(
                ['pdd' => $this->getValueTable('catalog/product', 'decimal')],
                ['entity_id', 'attribute_id']
            )
            ->join(
                ['cs' => $this->getTable('core/store')],
                '',
                ['store_id']
            )
            ->joinLeft(
                ['pds' => $this->getValueTable('catalog/product', 'decimal')],
                'pds.entity_id = pdd.entity_id AND pds.attribute_id = pdd.attribute_id'
                    . ' AND pds.store_id=cs.store_id',
                ['value' => $productValueExpression]
            )
            ->where('pdd.store_id = ?', Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID)
            ->where('cs.store_id != ? AND s.is_active=1', Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID)
            ->where('pdd.attribute_id IN (?)', $attrIds)
            ->where("{$productValueExpression} IS NOT NULL");

        $statusCond = $write->quoteInto('=?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        $this->_addAttributeToSelect($select, 'status', 'pdd.entity_id', 'cs.store_id', $statusCond);

        if (!is_null($entityIds)) {
            $select->where('pdd.entity_id IN(?)', $entityIds);
        }

        /**
         * Add additional external limitation
         */
        Mage::dispatchEvent('prepare_catalog_product_index_select', [
            'select'        => $select,
            'entity_field'  => new Zend_Db_Expr('pdd.entity_id'),
            'website_field' => new Zend_Db_Expr('cs.website_id'),
            'store_field'   => new Zend_Db_Expr('cs.store_id')
        ]);

        $query = $select->insertFromSelect($idxTable);
        $write->query($query);

        return $this;
    }

    /**
     * Retrieve decimal indexable attributes
     *
     * @return array
     */
    protected function _getIndexableAttributes()
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from(['ca' => $this->getTable('catalog/eav_attribute')], 'attribute_id')
            ->join(
                ['ea' => $this->getTable('eav/attribute')],
                'ca.attribute_id = ea.attribute_id',
                []
            )
            ->where('ea.attribute_code != ?', 'price')
            ->where($this->_getIndexableAttributesCondition())
            ->where('ea.backend_type=?', 'decimal');

        return $adapter->fetchCol($select);
    }

    /**
     * Retrieve temporary decimal index table name
     *
     * @param string $table
     * @return string
     */
    public function getIdxTable($table = null)
    {
        if ($this->useIdxTable()) {
            return $this->getTable('catalog/product_eav_decimal_indexer_idx');
        }
        return $this->getTable('catalog/product_eav_decimal_indexer_tmp');
    }
}
