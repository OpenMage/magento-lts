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
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bundle products Price indexer resource model
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Model_Resource_Indexer_Price extends Mage_Catalog_Model_Resource_Product_Indexer_Price_Default
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
            $this->_prepareBundlePrice();
            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Reindex temporary (price result data) for defined product(s)
     *
     * @param int|array $entityIds
     * @return $this
     */
    public function reindexEntity($entityIds)
    {
        $this->_prepareBundlePrice($entityIds);

        return $this;
    }

    /**
     * Retrieve temporary price index table name for fixed bundle products
     *
     * @return string
     */
    protected function _getBundlePriceTable()
    {
        if ($this->useIdxTable()) {
            return $this->getTable('bundle/price_indexer_idx');
        }
        return $this->getTable('bundle/price_indexer_tmp');
    }

    /**
     * Retrieve table name for temporary bundle selection prices index
     *
     * @return string
     */
    protected function _getBundleSelectionTable()
    {
        if ($this->useIdxTable()) {
            return $this->getTable('bundle/selection_indexer_idx');
        }
        return $this->getTable('bundle/selection_indexer_tmp');
    }

    /**
     * Retrieve table name for temporary bundle option prices index
     *
     * @return string
     */
    protected function _getBundleOptionTable()
    {
        if ($this->useIdxTable()) {
            return $this->getTable('bundle/option_indexer_idx');
        }
        return $this->getTable('bundle/option_indexer_tmp');
    }

    /**
     * Prepare temporary price index table for fixed bundle products
     *
     * @return $this
     */
    protected function _prepareBundlePriceTable()
    {
        $this->_getWriteAdapter()->delete($this->_getBundlePriceTable());
        return $this;
    }

    /**
     * Prepare table structure for temporary bundle selection prices index
     *
     * @return $this
     */
    protected function _prepareBundleSelectionTable()
    {
        $this->_getWriteAdapter()->delete($this->_getBundleSelectionTable());
        return $this;
    }

    /**
     * Prepare table structure for temporary bundle option prices index
     *
     * @return $this
     */
    protected function _prepareBundleOptionTable()
    {
        $this->_getWriteAdapter()->delete($this->_getBundleOptionTable());
        return $this;
    }

    /**
     * Prepare temporary price index data for bundle products by price type
     *
     * @param int $priceType
     * @param int|array $entityIds the entity ids limitatation
     * @return $this
     */
    protected function _prepareBundlePriceByType($priceType, $entityIds = null)
    {
        $write = $this->_getWriteAdapter();
        $table = $this->_getBundlePriceTable();

        $select = $write->select()
            ->from(['e' => $this->getTable('catalog/product')], ['entity_id'])
            ->join(
                ['cg' => $this->getTable('customer/customer_group')],
                '',
                ['customer_group_id']
            );
        $this->_addWebsiteJoinToSelect($select, true);
        $this->_addProductWebsiteJoinToSelect($select, 'cw.website_id', 'e.entity_id');
        $select->columns('website_id', 'cw')
            ->join(
                ['cwd' => $this->_getWebsiteDateTable()],
                'cw.website_id = cwd.website_id',
                []
            )
            ->joinLeft(
                ['tp' => $this->_getTierPriceIndexTable()],
                'tp.entity_id = e.entity_id AND tp.website_id = cw.website_id'
                    . ' AND tp.customer_group_id = cg.customer_group_id',
                []
            )
            ->joinLeft(
                ['gp' => $this->_getGroupPriceIndexTable()],
                'gp.entity_id = e.entity_id AND gp.website_id = cw.website_id'
                    . ' AND gp.customer_group_id = cg.customer_group_id',
                []
            )
            ->where('e.type_id=?', $this->getTypeId());

        // add enable products limitation
        $statusCond = $write->quoteInto('=?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        $this->_addAttributeToSelect($select, 'status', 'e.entity_id', 'cs.store_id', $statusCond, true);
        if (Mage::helper('core')->isModuleEnabled('Mage_Tax')) {
            $taxClassId = $this->_addAttributeToSelect($select, 'tax_class_id', 'e.entity_id', 'cs.store_id');
        } else {
            $taxClassId = new Zend_Db_Expr('0');
        }

        if ($priceType == Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC) {
            $select->columns(['tax_class_id' => new Zend_Db_Expr('0')]);
        } else {
            $select->columns(
                ['tax_class_id' => $write->getCheckSql($taxClassId . ' IS NOT NULL', $taxClassId, 0)]
            );
        }

        $priceTypeCond = $write->quoteInto('=?', $priceType);
        $this->_addAttributeToSelect($select, 'price_type', 'e.entity_id', 'cs.store_id', $priceTypeCond);

        $price          = $this->_addAttributeToSelect($select, 'price', 'e.entity_id', 'cs.store_id');
        $specialPrice   = $this->_addAttributeToSelect($select, 'special_price', 'e.entity_id', 'cs.store_id');
        $specialFrom    = $this->_addAttributeToSelect($select, 'special_from_date', 'e.entity_id', 'cs.store_id');
        $specialTo      = $this->_addAttributeToSelect($select, 'special_to_date', 'e.entity_id', 'cs.store_id');
        $curentDate     = new Zend_Db_Expr('cwd.website_date');

        $specialExpr    = $write->getCheckSql(
            $write->getCheckSql(
                $specialFrom . ' IS NULL',
                '1',
                $write->getCheckSql(
                    $specialFrom . ' <= ' . $curentDate,
                    '1',
                    '0'
                )
            ) . " > 0 AND " .
            $write->getCheckSql(
                $specialTo . ' IS NULL',
                '1',
                $write->getCheckSql(
                    $specialTo . ' >= ' . $curentDate,
                    '1',
                    '0'
                )
            )
            . " > 0 AND {$specialPrice} > 0 AND {$specialPrice} < 100 ",
            $specialPrice,
            '0'
        );

        $groupPriceExpr = $write->getCheckSql(
            'gp.price IS NOT NULL AND gp.price > 0 AND gp.price < 100',
            'gp.price',
            '0'
        );

        $tierExpr       = new Zend_Db_Expr("tp.min_price");

        if ($priceType == Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED) {
            $finalPrice = $write->getCheckSql(
                $specialExpr . ' > 0',
                'ROUND(' . $price . ' * (' . $specialExpr . '  / 100), 2)',
                $price
            );
            $tierPrice = $write->getCheckSql(
                $tierExpr . ' IS NOT NULL',
                'ROUND(' . $price . ' - ' . '(' . $price . ' * (' . $tierExpr . ' / 100)), 2)',
                'NULL'
            );
            $groupPrice = $write->getCheckSql(
                $groupPriceExpr . ' > 0',
                'ROUND(' . $price . ' - ' . '(' . $price . ' * (' . $groupPriceExpr . ' / 100)), 2)',
                'NULL'
            );
            $finalPrice = $write->getCheckSql(
                "{$groupPrice} IS NOT NULL AND {$groupPrice} < {$finalPrice}",
                $groupPrice,
                $finalPrice
            );
        } else {
            $finalPrice     = new Zend_Db_Expr("0");
            $tierPrice      = $write->getCheckSql($tierExpr . ' IS NOT NULL', '0', 'NULL');
            $groupPrice     = $write->getCheckSql($groupPriceExpr . ' > 0', $groupPriceExpr, 'NULL');
        }

        $select->columns([
            'price_type'          => new Zend_Db_Expr($priceType),
            'special_price'       => $specialExpr,
            'tier_percent'        => $tierExpr,
            'orig_price'          => $write->getCheckSql($price . ' IS NULL', '0', $price),
            'price'               => $finalPrice,
            'min_price'           => $finalPrice,
            'max_price'           => $finalPrice,
            'tier_price'          => $tierPrice,
            'base_tier'           => $tierPrice,
            'group_price'         => $groupPrice,
            'base_group_price'    => $groupPrice,
            'group_price_percent' => new Zend_Db_Expr('gp.price'),
        ]);

        if (!is_null($entityIds)) {
            $select->where('e.entity_id IN(?)', $entityIds);
        }

        /**
         * Add additional external limitation
         */
        Mage::dispatchEvent('catalog_product_prepare_index_select', [
            'select'        => $select,
            'entity_field'  => new Zend_Db_Expr('e.entity_id'),
            'website_field' => new Zend_Db_Expr('cw.website_id'),
            'store_field'   => new Zend_Db_Expr('cs.store_id')
        ]);

        $query = $select->insertFromSelect($table);
        $write->query($query);

        return $this;
    }

    /**
     * Calculate fixed bundle product selections price
     *
     * @return $this
     */
    protected function _calculateBundleOptionPrice()
    {
        $write = $this->_getWriteAdapter();

        $this->_prepareBundleSelectionTable();
        $this->_calculateBundleSelectionPrice(Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED);
        $this->_calculateBundleSelectionPrice(Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC);

        $this->_prepareBundleOptionTable();

        $select = $write->select()
            ->from(
                ['i' => $this->_getBundleSelectionTable()],
                ['entity_id', 'customer_group_id', 'website_id', 'option_id']
            )
            ->group(['entity_id', 'customer_group_id', 'website_id', 'option_id', 'is_required', 'group_type'])
            ->columns([
                'min_price' => $write->getCheckSql('i.is_required = 1', 'MIN(i.price)', '0'),
                'alt_price' => $write->getCheckSql('i.is_required = 0', 'MIN(i.price)', '0'),
                'max_price' => $write->getCheckSql('i.group_type = 1', 'SUM(i.price)', 'MAX(i.price)'),
                'tier_price' => $write->getCheckSql('i.is_required = 1', 'MIN(i.tier_price)', '0'),
                'alt_tier_price' => $write->getCheckSql('i.is_required = 0', 'MIN(i.tier_price)', '0'),
                'group_price' => $write->getCheckSql('i.is_required = 1', 'MIN(i.group_price)', '0'),
                'alt_group_price' => $write->getCheckSql('i.is_required = 0', 'MIN(i.group_price)', '0'),
            ]);

        $query = $select->insertFromSelect($this->_getBundleOptionTable());
        $write->query($query);

        $this->_prepareDefaultFinalPriceTable();

        $minPrice  = new Zend_Db_Expr($write->getCheckSql(
            'SUM(io.min_price) = 0',
            'MIN(io.alt_price)',
            'SUM(io.min_price)'
        ) . ' + i.price');
        $maxPrice  = new Zend_Db_Expr("SUM(io.max_price) + i.price");
        $tierPrice = $write->getCheckSql(
            'MIN(i.tier_percent) IS NOT NULL',
            $write->getCheckSql(
                'SUM(io.tier_price) = 0',
                'SUM(io.alt_tier_price)',
                'SUM(io.tier_price)'
            ) . ' + MIN(i.tier_price)',
            'NULL'
        );
        $groupPrice = $write->getCheckSql(
            'MIN(i.group_price_percent) IS NOT NULL',
            $write->getCheckSql(
                'SUM(io.group_price) = 0',
                'SUM(io.alt_group_price)',
                'SUM(io.group_price)'
            ) . ' + MIN(i.group_price)',
            'NULL'
        );

        $select = $write->select()
            ->from(
                ['io' => $this->_getBundleOptionTable()],
                ['entity_id', 'customer_group_id', 'website_id']
            )
            ->join(
                ['i' => $this->_getBundlePriceTable()],
                'i.entity_id = io.entity_id AND i.customer_group_id = io.customer_group_id'
                . ' AND i.website_id = io.website_id',
                []
            )
            ->group(['io.entity_id', 'io.customer_group_id', 'io.website_id',
                'i.tax_class_id', 'i.orig_price', 'i.price'])
            ->columns(['i.tax_class_id',
                'orig_price'       => 'i.orig_price',
                'price'            => 'i.price',
                'min_price'        => $minPrice,
                'max_price'        => $maxPrice,
                'tier_price'       => $tierPrice,
                'base_tier'        => 'MIN(i.base_tier)',
                'group_price'      => $groupPrice,
                'base_group_price' => 'MIN(i.base_group_price)',
            ]);

        $query = $select->insertFromSelect($this->_getDefaultFinalPriceTable());
        $write->query($query);

        return $this;
    }

    /**
     * Calculate bundle product selections price by product type
     *
     * @param int $priceType
     * @return $this
     */
    protected function _calculateBundleSelectionPrice($priceType)
    {
        $write = $this->_getWriteAdapter();

        if ($priceType == Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED) {
            $selectionPriceValue = $write->getCheckSql(
                'bsp.selection_price_value IS NULL',
                'bs.selection_price_value',
                'bsp.selection_price_value'
            );
            $selectionPriceType = $write->getCheckSql(
                'bsp.selection_price_type IS NULL',
                'bs.selection_price_type',
                'bsp.selection_price_type'
            );
            $priceExpr = new Zend_Db_Expr(
                $write->getCheckSql(
                    $selectionPriceType . ' = 1',
                    'ROUND(i.price * (' . $selectionPriceValue . ' / 100),2)',
                    $write->getCheckSql(
                        'i.special_price > 0 AND i.special_price < 100',
                        'ROUND(' . $selectionPriceValue . ' * (i.special_price / 100),2)',
                        $selectionPriceValue
                    )
                ) . '* bs.selection_qty'
            );

            $tierExpr = $write->getCheckSql(
                'i.base_tier IS NOT NULL',
                $write->getCheckSql(
                    $selectionPriceType . ' = 1',
                    'ROUND(i.base_tier - (i.base_tier * (' . $selectionPriceValue . ' / 100)),2)',
                    $write->getCheckSql(
                        'i.tier_percent > 0',
                        'ROUND(' . $selectionPriceValue
                        . ' - (' . $selectionPriceValue . ' * (i.tier_percent / 100)),2)',
                        $selectionPriceValue
                    )
                ) . ' * bs.selection_qty',
                'NULL'
            );

            $groupExpr = $write->getCheckSql(
                'i.base_group_price IS NOT NULL',
                $write->getCheckSql(
                    $selectionPriceType . ' = 1',
                    $priceExpr,
                    $write->getCheckSql(
                        'i.group_price_percent > 0',
                        'ROUND(' . $selectionPriceValue
                        . ' - (' . $selectionPriceValue . ' * (i.group_price_percent / 100)),2)',
                        $selectionPriceValue
                    )
                ) . ' * bs.selection_qty',
                'NULL'
            );
            $priceExpr = new Zend_Db_Expr(
                $write->getCheckSql("{$groupExpr} < {$priceExpr}", $groupExpr, $priceExpr)
            );
        } else {
            $priceExpr = new Zend_Db_Expr(
                $write->getCheckSql(
                    'i.special_price > 0 AND i.special_price < 100',
                    'ROUND(idx.min_price * (i.special_price / 100), 2)',
                    'idx.min_price'
                ) . ' * bs.selection_qty'
            );
            $tierExpr = $write->getCheckSql(
                'i.base_tier IS NOT NULL',
                'ROUND(idx.min_price * (i.base_tier / 100), 2)* bs.selection_qty',
                'NULL'
            );
            $groupExpr = $write->getCheckSql(
                'i.base_group_price IS NOT NULL',
                'ROUND(idx.min_price * (i.base_group_price / 100), 2)* bs.selection_qty',
                'NULL'
            );
            $groupPriceExpr = new Zend_Db_Expr(
                $write->getCheckSql(
                    'i.base_group_price IS NOT NULL AND i.base_group_price > 0 AND i.base_group_price < 100',
                    'ROUND(idx.min_price - idx.min_price * (i.base_group_price / 100), 2)',
                    'idx.min_price'
                ) . ' * bs.selection_qty'
            );
            $priceExpr = new Zend_Db_Expr(
                $write->getCheckSql("{$groupPriceExpr} < {$priceExpr}", $groupPriceExpr, $priceExpr)
            );
        }

        $select = $write->select()
            ->from(
                ['i' => $this->_getBundlePriceTable()],
                ['entity_id', 'customer_group_id', 'website_id']
            )
            ->join(
                ['bo' => $this->getTable('bundle/option')],
                'bo.parent_id = i.entity_id',
                ['option_id']
            )
            ->join(
                ['bs' => $this->getTable('bundle/selection')],
                'bs.option_id = bo.option_id',
                ['selection_id']
            )
            ->joinLeft(
                ['bsp' => $this->getTable('bundle/selection_price')],
                'bs.selection_id = bsp.selection_id AND bsp.website_id = i.website_id',
                ['']
            )
            ->join(
                ['idx' => $this->getIdxTable()],
                'bs.product_id = idx.entity_id AND i.customer_group_id = idx.customer_group_id'
                . ' AND i.website_id = idx.website_id',
                []
            )
            ->join(
                ['e' => $this->getTable('catalog/product')],
                'bs.product_id = e.entity_id AND e.required_options=0',
                []
            )
            ->where('i.price_type=?', $priceType)
            ->columns([
                'group_type'    => $write->getCheckSql(
                    "bo.type = 'select' OR bo.type = 'radio'",
                    '0',
                    '1'
                ),
                'is_required'   => 'bo.required',
                'price'         => $priceExpr,
                'tier_price'    => $tierExpr,
                'group_price'   => $groupExpr,
            ]);

        $query = $select->insertFromSelect($this->_getBundleSelectionTable());
        $write->query($query);

        return $this;
    }

    /**
     * Prepare temporary index price for bundle products
     *
     * @param int|array $entityIds  the entity ids limitation
     * @return $this
     */
    protected function _prepareBundlePrice($entityIds = null)
    {
        $this->_prepareTierPriceIndex($entityIds);
        $this->_prepareGroupPriceIndex($entityIds);
        $this->_prepareBundlePriceTable();
        $this->_prepareBundlePriceByType(Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED, $entityIds);
        $this->_prepareBundlePriceByType(Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC, $entityIds);

        /**
         * Add possibility modify prices from external events
         */
        $select = $this->_getWriteAdapter()->select()
            ->join(
                ['wd' => $this->_getWebsiteDateTable()],
                'i.website_id = wd.website_id',
                []
            );
        Mage::dispatchEvent('prepare_catalog_product_price_index_table', [
            'index_table'       => ['i' => $this->_getBundlePriceTable()],
            'select'            => $select,
            'entity_id'         => 'i.entity_id',
            'customer_group_id' => 'i.customer_group_id',
            'website_id'        => 'i.website_id',
            'website_date'      => 'wd.website_date',
            'update_fields'     => ['price', 'min_price', 'max_price']
        ]);

        $this->_calculateBundleOptionPrice();
        $this->_applyCustomOption();

        $this->_movePriceDataToIndexTable();

        return $this;
    }

    /**
     * Prepare percentage tier price for bundle products
     *
     * @see Mage_Catalog_Model_Resource_Product_Indexer_Price::_prepareTierPriceIndex
     *
     * @param int|array $entityIds
     * @return $this
     */
    protected function _prepareTierPriceIndex($entityIds = null)
    {
        $adapter = $this->_getWriteAdapter();

        // remove index by bundle products
        $select  = $adapter->select()
            ->from(['i' => $this->_getTierPriceIndexTable()], null)
            ->join(
                ['e' => $this->getTable('catalog/product')],
                'i.entity_id=e.entity_id',
                []
            )
            ->where('e.type_id=?', $this->getTypeId());
        $query   = $select->deleteFromSelect('i');
        $adapter->query($query);

        $select  = $adapter->select()
            ->from(
                ['tp' => $this->getValueTable('catalog/product', 'tier_price')],
                ['entity_id']
            )
            ->join(
                ['e' => $this->getTable('catalog/product')],
                'tp.entity_id=e.entity_id',
                []
            )
            ->join(
                ['cg' => $this->getTable('customer/customer_group')],
                'tp.all_groups = 1 OR (tp.all_groups = 0 AND tp.customer_group_id = cg.customer_group_id)',
                ['customer_group_id']
            )
            ->join(
                ['cw' => $this->getTable('core/website')],
                'tp.website_id = 0 OR tp.website_id = cw.website_id',
                ['website_id']
            )
            ->where('cw.website_id != 0')
            ->where('e.type_id=?', $this->getTypeId())
            ->columns(new Zend_Db_Expr('MIN(tp.value)'))
            ->group(['tp.entity_id', 'cg.customer_group_id', 'cw.website_id']);

        if (!empty($entityIds)) {
            $select->where('tp.entity_id IN(?)', $entityIds);
        }

        $query   = $select->insertFromSelect($this->_getTierPriceIndexTable());
        $adapter->query($query);

        return $this;
    }

    /**
     * Prepare percentage group price for bundle products
     *
     * @see Mage_Catalog_Model_Resource_Product_Indexer_Price::_prepareGroupPriceIndex
     *
     * @param int|array $entityIds
     * @return $this
     */
    protected function _prepareGroupPriceIndex($entityIds = null)
    {
        $adapter = $this->_getWriteAdapter();

        // remove index by bundle products
        $select  = $adapter->select()
            ->from(['i' => $this->_getGroupPriceIndexTable()], null)
            ->join(
                ['e' => $this->getTable('catalog/product')],
                'i.entity_id=e.entity_id',
                []
            )
            ->where('e.type_id=?', $this->getTypeId());
        $query   = $select->deleteFromSelect('i');
        $adapter->query($query);

        $select  = $adapter->select()
            ->from(
                ['gp' => $this->getValueTable('catalog/product', 'group_price')],
                ['entity_id']
            )
            ->join(
                ['e' => $this->getTable('catalog/product')],
                'gp.entity_id=e.entity_id',
                []
            )
            ->join(
                ['cg' => $this->getTable('customer/customer_group')],
                'gp.all_groups = 1 OR (gp.all_groups = 0 AND gp.customer_group_id = cg.customer_group_id)',
                ['customer_group_id']
            )
            ->join(
                ['cw' => $this->getTable('core/website')],
                'gp.website_id = 0 OR gp.website_id = cw.website_id',
                ['website_id']
            )
            ->where('cw.website_id != 0')
            ->where('e.type_id=?', $this->getTypeId())
            ->columns(new Zend_Db_Expr('MIN(gp.value)'))
            ->group(['gp.entity_id', 'cg.customer_group_id', 'cw.website_id']);

        if (!empty($entityIds)) {
            $select->where('gp.entity_id IN(?)', $entityIds);
        }

        $query   = $select->insertFromSelect($this->_getGroupPriceIndexTable());
        $adapter->query($query);

        return $this;
    }
}
