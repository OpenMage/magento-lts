<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Configurable Products Price Indexer Resource model
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Resource_Product_Indexer_Price_Grouped extends Mage_Catalog_Model_Resource_Product_Indexer_Price_Default
{
    /**
     * Reindex temporary (price result data) for all products
     *
     * @return $this
     */
    public function reindexAll()
    {
        $this->useIdxTable(true);
        $this->beginTransaction();
        try {
            $this->_prepareGroupedProductPriceData();
            $this->commit();
        } catch (Exception $exception) {
            $this->rollBack();
            throw $exception;
        }

        return $this;
    }

    /**
     * Reindex temporary (price result data) for defined product(s)
     *
     * @param  array|int $entityIds
     * @return $this
     */
    public function reindexEntity($entityIds)
    {
        $this->_prepareGroupedProductPriceData($entityIds);

        return $this;
    }

    /**
     * Calculate minimal and maximal prices for Grouped products
     * Use calculated price for relation products
     *
     * @param  array|int $entityIds the parent entity ids limitation
     * @return $this
     */
    protected function _prepareGroupedProductPriceData($entityIds = null)
    {
        $write = $this->_getWriteAdapter();
        $table = $this->getIdxTable();

        $select = $write->select()
            ->from(['e' => $this->getTable('catalog/product')], 'entity_id')
            ->join(
                ['l' => $this->getTable('catalog/product_link')],
                'e.entity_id = l.product_id AND l.link_type_id=' . Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED,
                [],
            )
            ->join(
                ['cg' => $this->getTable('customer/customer_group')],
                '',
                ['customer_group_id'],
            );
        $this->_addWebsiteJoinToSelect($select, true);
        $this->_addProductWebsiteJoinToSelect($select, 'cw.website_id', 'e.entity_id');
        $minCheckSql = $write->getCheckSql('le.required_options = 0', 'i.min_price', '0');
        $maxCheckSql = $write->getCheckSql('le.required_options = 0', 'i.max_price', '0');
        $select->columns('website_id', 'cw')
            ->join(
                ['le' => $this->getTable('catalog/product')],
                'le.entity_id = l.linked_product_id',
                [],
            )
            ->join(
                ['i' => $table],
                'i.entity_id = l.linked_product_id AND i.website_id = cw.website_id'
                    . ' AND i.customer_group_id = cg.customer_group_id',
                [
                    'tax_class_id' => $this->_getReadAdapter()
                        ->getCheckSql('MIN(i.tax_class_id) IS NULL', '0', 'MIN(i.tax_class_id)'),
                    'price'        => new Zend_Db_Expr('NULL'),
                    'final_price'  => new Zend_Db_Expr('NULL'),
                    'min_price'    => new Zend_Db_Expr('MIN(' . $minCheckSql . ')'),
                    'max_price'    => new Zend_Db_Expr('MAX(' . $maxCheckSql . ')'),
                    'tier_price'   => new Zend_Db_Expr('NULL'),
                    'group_price'  => new Zend_Db_Expr('NULL'),
                ],
            )
            ->group(['e.entity_id', 'cg.customer_group_id', 'cw.website_id'])
            ->where('e.type_id=?', $this->getTypeId());

        $statusCond = $write->quoteInto(' = ?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        $this->_addAttributeToSelect($select, 'status', 'e.entity_id', 'cs.store_id', $statusCond);

        if (!is_null($entityIds)) {
            $select->where('l.product_id IN(?)', $entityIds);
        }

        /**
         * Add additional external limitation
         */
        Mage::dispatchEvent('catalog_product_prepare_index_select', [
            'select'        => $select,
            'entity_field'  => new Zend_Db_Expr('e.entity_id'),
            'website_field' => new Zend_Db_Expr('cw.website_id'),
            'store_field'   => new Zend_Db_Expr('cs.store_id'),
        ]);

        $query = $select->insertFromSelect($table);
        $write->query($query);

        return $this;
    }
}
