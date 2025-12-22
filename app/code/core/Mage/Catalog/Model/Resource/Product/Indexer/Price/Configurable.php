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
class Mage_Catalog_Model_Resource_Product_Indexer_Price_Configurable extends Mage_Catalog_Model_Resource_Product_Indexer_Price_Default
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
            $this->_prepareFinalPriceData();
            $this->_applyCustomOption();
            $this->_applyConfigurableOption();
            $this->_movePriceDataToIndexTable();
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
        $this->_prepareFinalPriceData($entityIds);
        $this->_applyCustomOption();
        $this->_applyConfigurableOption();
        $this->_movePriceDataToIndexTable();

        return $this;
    }

    /**
     * Retrieve table name for custom option temporary aggregation data
     *
     * @return string
     */
    protected function _getConfigurableOptionAggregateTable()
    {
        if ($this->useIdxTable()) {
            return $this->getTable('catalog/product_price_indexer_cfg_option_aggregate_idx');
        }

        return $this->getTable('catalog/product_price_indexer_cfg_option_aggregate_tmp');
    }

    /**
     * Retrieve table name for custom option prices data
     *
     * @return string
     */
    protected function _getConfigurableOptionPriceTable()
    {
        if ($this->useIdxTable()) {
            return $this->getTable('catalog/product_price_indexer_cfg_option_idx');
        }

        return $this->getTable('catalog/product_price_indexer_cfg_option_tmp');
    }

    /**
     * Prepare table structure for custom option temporary aggregation data
     *
     * @return $this
     */
    protected function _prepareConfigurableOptionAggregateTable()
    {
        $this->_getWriteAdapter()->delete($this->_getConfigurableOptionAggregateTable());
        return $this;
    }

    /**
     * Prepare table structure for custom option prices data
     *
     * @return $this
     */
    protected function _prepareConfigurableOptionPriceTable()
    {
        $this->_getWriteAdapter()->delete($this->_getConfigurableOptionPriceTable());
        return $this;
    }

    /**
     * Calculate minimal and maximal prices for configurable product options
     * and apply it to final price
     *
     * @return $this
     */
    protected function _applyConfigurableOption()
    {
        $write      = $this->_getWriteAdapter();
        $coaTable   = $this->_getConfigurableOptionAggregateTable();
        $copTable   = $this->_getConfigurableOptionPriceTable();

        $this->_prepareConfigurableOptionAggregateTable();
        $this->_prepareConfigurableOptionPriceTable();

        $select = $write->select()
            ->from(['i' => $this->_getDefaultFinalPriceTable()], [])
            ->join(
                ['l' => $this->getTable('catalog/product_super_link')],
                'l.parent_id = i.entity_id',
                ['parent_id', 'product_id'],
            )
            ->columns(['customer_group_id', 'website_id'], 'i')
            ->join(
                ['a' => $this->getTable('catalog/product_super_attribute')],
                'l.parent_id = a.product_id',
                [],
            )
            ->join(
                ['cp' => $this->getValueTable('catalog/product', 'int')],
                'l.product_id = cp.entity_id AND cp.attribute_id = a.attribute_id AND cp.store_id = 0',
                [],
            )
            ->joinLeft(
                ['apd' => $this->getTable('catalog/product_super_attribute_pricing')],
                'a.product_super_attribute_id = apd.product_super_attribute_id'
                    . ' AND apd.website_id = 0 AND cp.value = apd.value_index',
                [],
            )
            ->joinLeft(
                ['apw' => $this->getTable('catalog/product_super_attribute_pricing')],
                'a.product_super_attribute_id = apw.product_super_attribute_id'
                    . ' AND apw.website_id = i.website_id AND cp.value = apw.value_index',
                [],
            )
            ->join(
                ['le' => $this->getTable('catalog/product')],
                'le.entity_id = l.product_id',
                [],
            )
            ->where('le.required_options=0')
            ->group(['l.parent_id', 'i.customer_group_id', 'i.website_id', 'l.product_id']);
        $this->_addWebsiteJoinToSelect($select, true, 'i.website_id');
        $this->_addProductWebsiteJoinToSelect($select, 'cw.website_id', 'le.entity_id');

        $priceExpression = $write->getCheckSql('apw.value_id IS NOT NULL', 'apw.pricing_value', 'apd.pricing_value');
        $percentExpr = $write->getCheckSql('apw.value_id IS NOT NULL', 'apw.is_percent', 'apd.is_percent');
        $roundExpr = "ROUND(i.price * ({$priceExpression} / 100), 4)";
        $roundPriceExpr = $write->getCheckSql("{$percentExpr} = 1", $roundExpr, $priceExpression);
        $priceColumn = $write->getCheckSql("{$priceExpression} IS NULL", '0', $roundPriceExpr);
        $priceColumn = new Zend_Db_Expr("SUM({$priceColumn})");

        $tierPrice = $priceExpression;
        $tierRoundPriceExp = $write->getCheckSql("{$percentExpr} = 1", $roundExpr, $tierPrice);
        $tierPriceExp = $write->getCheckSql("{$tierPrice} IS NULL", '0', $tierRoundPriceExp);
        $tierPriceColumn = $write->getCheckSql('MIN(i.tier_price) IS NOT NULL', "SUM({$tierPriceExp})", 'NULL');

        $groupPrice = $priceExpression;
        $groupRoundPriceExp = $write->getCheckSql("{$percentExpr} = 1", $roundExpr, $groupPrice);
        $groupPriceExp = $write->getCheckSql("{$groupPrice} IS NULL", '0', $groupRoundPriceExp);
        $groupPriceColumn = $write->getCheckSql('MIN(i.group_price) IS NOT NULL', "SUM({$groupPriceExp})", 'NULL');

        $select->columns([
            'price'       => $priceColumn,
            'tier_price'  => $tierPriceColumn,
            'group_price' => $groupPriceColumn,
        ]);

        $statusCond = $write->quoteInto(' = ?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        $this->_addAttributeToSelect($select, 'status', 'le.entity_id', 'cs.store_id', $statusCond);

        $query = $select->insertFromSelect($coaTable);
        $write->query($query);

        $select = $write->select()
            ->from(
                [$coaTable],
                [
                    'parent_id', 'customer_group_id', 'website_id',
                    'MIN(price)', 'MAX(price)', 'MIN(tier_price)', 'MIN(group_price)',
                ],
            )
            ->group(['parent_id', 'customer_group_id', 'website_id']);

        $query = $select->insertFromSelect($copTable);
        $write->query($query);

        $table  = ['i' => $this->_getDefaultFinalPriceTable()];
        $select = $write->select()
            ->join(
                ['io' => $copTable],
                'i.entity_id = io.entity_id AND i.customer_group_id = io.customer_group_id'
                    . ' AND i.website_id = io.website_id',
                [],
            );
        $select->columns([
            'min_price'   => new Zend_Db_Expr('i.min_price + io.min_price'),
            'max_price'   => new Zend_Db_Expr('i.max_price + io.max_price'),
            'tier_price'  => $write->getCheckSql('i.tier_price IS NOT NULL', 'i.tier_price + io.tier_price', 'NULL'),
            'group_price' => $write->getCheckSql(
                'i.group_price IS NOT NULL',
                'i.group_price + io.group_price',
                'NULL',
            ),
        ]);

        $query = $select->crossUpdateFromSelect($table);
        $write->query($query);

        $select = $write->select()
            ->from($table)
            ->join(
                ['e' => $this->getTable('catalog/product')],
                'e.entity_id = i.entity_id',
                [],
            )
            ->joinLeft(
                ['coa' => $coaTable],
                'coa.parent_id = i.entity_id',
                [],
            )
            ->where('e.type_id = ?', $this->getTypeId())
            ->where('coa.parent_id IS NULL');

        $query = $select->deleteFromSelect('i');
        $write->query($query);

        $write->delete($coaTable);
        $write->delete($copTable);

        return $this;
    }
}
