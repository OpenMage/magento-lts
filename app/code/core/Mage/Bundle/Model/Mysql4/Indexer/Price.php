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
 * @package     Mage_Bundle
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Bundle products Price indexer resource model
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Model_Mysql4_Indexer_Price
    extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Default
{
    /**
     * Reindex temporary (price result data) for all products
     *
     * @return Mage_Bundle_Model_Mysql4_Indexer_Price
     */
    public function reindexAll()
    {
        $this->_prepareBundlePrice();

        return $this;
    }

    /**
     * Reindex temporary (price result data) for defined product(s)
     *
     * @param int|array $entityIds
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Interface
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
        return $this->getMainTable() . '_bundle';
    }

    /**
     * Prepare temporary price index table for fixed bundle products
     *
     * @return Mage_Bundle_Model_Mysql4_Indexer_Price
     */
    protected function _prepareBundlePriceTable()
    {
        $write = $this->_getWriteAdapter();
        $table = $this->_getBundlePriceTable();

        $query = sprintf('DROP TABLE IF EXISTS %s', $write->quoteIdentifier($table));
        $write->query($query);

        $query = sprintf('CREATE TABLE %s ('
            . ' `entity_id` INT(10) UNSIGNED NOT NULL,'
            . ' `customer_group_id` SMALLINT(5) UNSIGNED NOT NULL,'
            . ' `website_id` SMALLINT(5) UNSIGNED NOT NULL,'
            . ' `tax_class_id` SMALLINT(5) UNSIGNED DEFAULT \'0\','
            . ' `price_type` TINYINT(1) UNSIGNED NOT NULL,'
            . ' `special_price` DECIMAL(12,4) DEFAULT NULL,'
            . ' `tier_percent` DECIMAL(12,4) DEFAULT NULL,'
            . ' `orig_price` DECIMAL(12,4) DEFAULT NULL,'
            . ' `price` DECIMAL(12,4) DEFAULT NULL,'
            . ' `min_price` DECIMAL(12,4) DEFAULT NULL,'
            . ' `max_price` DECIMAL(12,4) DEFAULT NULL,'
            . ' `tier_price` DECIMAL(12,4) DEFAULT NULL,'
            . ' PRIMARY KEY (`entity_id`,`customer_group_id`,`website_id`)'
            . ') ENGINE=MYISAM DEFAULT CHARSET=utf8',
            $write->quoteIdentifier($table));
        $write->query($query);

        return $this;
    }

    /**
     * Retrieve table name for temporary bundle selection prices index
     *
     * @return string
     */
    protected function _getBundleSelectionTable()
    {
        return $this->getMainTable() . '_bundle_selection';
    }

    /**
     * Retrieve table name for temporary bundle option prices index
     *
     * @return string
     */
    protected function _getBundleOptionTable()
    {
        return $this->getMainTable() . '_bundle_option';
    }

    /**
     * Prepare table structure for temporary bundle selection prices index
     *
     * @return Mage_Bundle_Model_Mysql4_Indexer_Price
     */
    protected function _prepareBundleSelectionTable()
    {
        $write = $this->_getWriteAdapter();
        $table = $this->_getBundleSelectionTable();

        $query = sprintf('DROP TABLE IF EXISTS %s', $write->quoteIdentifier($table));
        $write->query($query);

        $query = sprintf('CREATE TABLE %s ('
            . ' `entity_id` INT(10) UNSIGNED NOT NULL,'
            . ' `customer_group_id` SMALLINT(5) UNSIGNED NOT NULL,'
            . ' `website_id` SMALLINT(5) UNSIGNED NOT NULL,'
            . ' `option_id` INT(10) UNSIGNED DEFAULT \'0\','
            . ' `selection_id` INT(10) UNSIGNED DEFAULT \'0\','
            . ' `group_type` TINYINT(1) UNSIGNED DEFAULT \'0\','
            . ' `is_required` TINYINT(1) UNSIGNED DEFAULT \'0\','
            . ' `price` DECIMAL(12,4) DEFAULT NULL,'
            . ' `tier_price` DECIMAL(12,4) DEFAULT NULL,'
            . ' PRIMARY KEY (`entity_id`,`customer_group_id`,`website_id`, `option_id`, `selection_id`)'
            . ') ENGINE=MYISAM DEFAULT CHARSET=utf8',
            $write->quoteIdentifier($table));
        $write->query($query);

        return $this;
    }

    /**
     * Prepare table structure for temporary bundle option prices index
     *
     * @return Mage_Bundle_Model_Mysql4_Indexer_Price
     */
    protected function _prepareBundleOptionTable()
    {
        $write = $this->_getWriteAdapter();
        $table = $this->_getBundleOptionTable();

        $query = sprintf('DROP TABLE IF EXISTS %s', $write->quoteIdentifier($table));
        $write->query($query);

        $query = sprintf('CREATE TABLE %s ('
            . ' `entity_id` INT(10) UNSIGNED NOT NULL,'
            . ' `customer_group_id` SMALLINT(5) UNSIGNED NOT NULL,'
            . ' `website_id` SMALLINT(5) UNSIGNED NOT NULL,'
            . ' `option_id` INT(10) UNSIGNED DEFAULT \'0\','
            . ' `min_price` DECIMAL(12,4) DEFAULT NULL,'
            . ' `alt_price` DECIMAL(12,4) DEFAULT NULL,'
            . ' `max_price` DECIMAL(12,4) DEFAULT NULL,'
            . ' `tier_price` DECIMAL(12,4) DEFAULT NULL,'
            . ' `alt_tier_price` DECIMAL(12,4) DEFAULT NULL,'
            . ' PRIMARY KEY (`entity_id`,`customer_group_id`,`website_id`, `option_id`)'
            . ') ENGINE=MYISAM DEFAULT CHARSET=utf8',
            $write->quoteIdentifier($table));
        $write->query($query);

        return $this;
    }

    /**
     * Prepare temporary price index data for bundle products by price type
     *
     * @param int $priceType
     * @param int|array $entityIds the entity ids limitatation
     * @return Mage_Bundle_Model_Mysql4_Indexer_Price
     */
    protected function _prepareBundlePriceByType($priceType, $entityIds = null)
    {
        $write = $this->_getWriteAdapter();
        $table = $this->_getBundlePriceTable();

        $select = $write->select()
            ->from(array('e' => $this->getTable('catalog/product')), array('entity_id'))
            ->join(
                array('cg' => $this->getTable('customer/customer_group')),
                '',
                array('customer_group_id'));
        $this->_addWebsiteJoinToSelect($select, true);
        $this->_addProductWebsiteJoinToSelect($select, 'cw.website_id', 'e.entity_id');
        $select->columns('website_id', 'cw')
            ->join(
                array('cwd' => $this->_getWebsiteDateTable()),
                'cw.website_id = cwd.website_id',
                array())
            ->joinLeft(
                array('tp' => $this->_getTierPriceIndexTable()),
                'tp.entity_id = e.entity_id AND tp.website_id = cw.website_id'
                    . ' AND tp.customer_group_id = cg.customer_group_id',
                array())
            ->where('e.type_id=?', $this->getTypeId());

        // add enable products limitation
        $statusCond = $write->quoteInto('=?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        $this->_addAttributeToSelect($select, 'status', 'e.entity_id', 'cs.store_id', $statusCond, true);

        $taxClassId = $this->_addAttributeToSelect($select, 'tax_class_id', 'e.entity_id', 'cs.store_id');
        $select->columns(array('tax_class_id' => new Zend_Db_Expr("IF($taxClassId IS NOT NULL, $taxClassId, 0)")));

        $priceTypeCond = $write->quoteInto('=?', $priceType);
        $this->_addAttributeToSelect($select, 'price_type', 'e.entity_id', 'cs.store_id', $priceTypeCond);

        $price          = $this->_addAttributeToSelect($select, 'price', 'e.entity_id', 'cs.store_id');
        $specialPrice   = $this->_addAttributeToSelect($select, 'special_price', 'e.entity_id', 'cs.store_id');
        $specialFrom    = $this->_addAttributeToSelect($select, 'special_from_date', 'e.entity_id', 'cs.store_id');
        $specialTo      = $this->_addAttributeToSelect($select, 'special_to_date', 'e.entity_id', 'cs.store_id');
        $curentDate     = new Zend_Db_Expr('cwd.date');

        $specialExpr    = new Zend_Db_Expr("IF(IF({$specialFrom} IS NULL, 1, "
            . "IF({$specialFrom} <= {$curentDate}, 1, 0)) > 0 AND IF({$specialTo} IS NULL, 1, "
            . "IF({$specialTo} >= {$curentDate}, 1, 0)) > 0 AND {$specialPrice} > 0, $specialPrice, 0)");
        $tierExpr       = new Zend_Db_Expr("tp.min_price");

        if ($priceType == Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED) {
            $finalPrice  = new Zend_Db_Expr("IF({$specialExpr} > 0,"
                . " ROUND($price * ({$specialExpr} / 100), 4), {$price})");
            $tierPrice      = new Zend_Db_Expr("IF({$tierExpr} IS NOT NULL,"
                . " ROUND({$price} * ({$tierExpr} / 100), 4), NULL)");
        } else {
            $finalPrice     = new Zend_Db_Expr("0");
            $tierPrice      = new Zend_Db_Expr("IF({$tierExpr} IS NOT NULL, 0, NULL)");
        }

        $select->columns(array(
            'price_type'    => new Zend_Db_Expr($priceType),
            'special_price' => $specialExpr,
            'tier_percent'  => $tierExpr,
            'price'         => $finalPrice,
            'orig_price'    => new Zend_Db_Expr("IF({$price} IS NULL, 0, {$price})"),
            'min_price'     => $finalPrice,
            'max_price'     => $finalPrice,
            'tier_price'    => $tierPrice
        ));

        if (!is_null($entityIds)) {
            $select->where('e.entity_id IN(?)', $entityIds);
        }

        /**
         * Add additional external limitation
         */
        Mage::dispatchEvent('prepare_catalog_product_index_select', array(
            'select'        => $select,
            'entity_field'  => new Zend_Db_Expr('e.entity_id'),
            'website_field' => new Zend_Db_Expr('cw.website_id'),
            'store_field'   => new Zend_Db_Expr('cs.store_id')
        ));

        $query = $select->insertFromSelect($table);
        $write->query($query);

        return $this;
    }

    /**
     * Calculate fixed bundle product selections price
     *
     * @return Mage_Bundle_Model_Mysql4_Indexer_Price
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
                array('i' => $this->_getBundleSelectionTable()),
                array('entity_id', 'customer_group_id', 'website_id', 'option_id'))
            ->group(array('entity_id', 'customer_group_id', 'website_id', 'option_id'))
            ->columns(array(
                'min_price' => new Zend_Db_Expr("IF(i.is_required = 1, MIN(i.price), 0)"),
                'alt_price' => new Zend_Db_Expr("IF(i.is_required = 0, MIN(i.price), 0)"),
                'max_price' => new Zend_Db_Expr("IF(i.group_type = 1, SUM(i.price), MAX(i.price))"),
                'tier_price' => new Zend_Db_Expr("IF(i.is_required = 1, MIN(i.tier_price), 0)"),
                'alt_tier_price' => new Zend_Db_Expr("IF(i.is_required = 0, MIN(i.tier_price), 0)"),
            ));

        $query = $select->insertFromSelect($this->_getBundleOptionTable());
        $write->query($query);

        $this->_prepareDefaultFinalPriceTable();

        $minPrice  = new Zend_Db_Expr("IF(SUM(io.min_price) = 0, SUM(io.alt_price), SUM(io.min_price)) + i.price");
        $maxPrice  = new Zend_Db_Expr("SUM(io.max_price) + i.price");
        $tierPrice = new Zend_Db_Expr("IF(i.tier_percent IS NOT NULL, IF(SUM(io.tier_price) = 0, "
            . "SUM(io.alt_tier_price), SUM(io.tier_price)) + i.tier_price, NULL)");

        $select = $write->select()
            ->from(
                array('io' => $this->_getBundleOptionTable()),
                array('entity_id', 'customer_group_id', 'website_id'))
            ->join(
                array('i' => $this->_getBundlePriceTable()),
                'i.entity_id = io.entity_id AND i.customer_group_id = io.customer_group_id'
                    . ' AND i.website_id = io.website_id',
                array())
            ->group(array('io.entity_id', 'io.customer_group_id', 'io.website_id'))
            ->columns(array('i.tax_class_id',
                'orig_price'    => 'i.orig_price',
                'price'         => 'i.price',
                'min_price'     => $minPrice,
                'max_price'     => $maxPrice,
                'tier_price'    => $tierPrice
            ));

        $query = $select->insertFromSelect($this->_getDefaultFinalPriceTable());
        $write->query($query);

        return $this;
    }

    /**
     * Calculate bundle product selections price by product type
     *
     * @param int $priceType
     * @return Mage_Bundle_Model_Mysql4_Indexer_Price
     */
    protected function _calculateBundleSelectionPrice($priceType)
    {
        $write = $this->_getWriteAdapter();

        if ($priceType == Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED) {
            $priceExpr = new Zend_Db_Expr("IF(bs.selection_price_type = 1, "
                . "ROUND(i.price * (bs.selection_price_value / 100), 4), IF(i.special_price > 0, "
                . "ROUND(bs.selection_price_value * (i.special_price / 100), 4), bs.selection_price_value)) "
                . "* bs.selection_qty");
            $tierExpr = new Zend_Db_Expr("IF(i.tier_price IS NOT NULL, IF(bs.selection_price_type = 1, "
                . "ROUND(i.tier_price * (bs.selection_price_value / 100), 4), IF(i.tier_percent > 0, "
                . "ROUND(bs.selection_price_value * (i.tier_percent / 100), 4), bs.selection_price_value)) "
                . "* bs.selection_qty, NULL)");
        } else {
            $priceExpr = new Zend_Db_Expr("IF(i.special_price > 0, ROUND(idx.min_price * (i.special_price / 100), 4), "
                . "idx.min_price) * bs.selection_qty");
            $tierExpr = new Zend_Db_Expr("IF(i.tier_price IS NOT NULL, ROUND(idx.min_price * (i.tier_price / 100), 4) "
                . "* bs.selection_qty, NULL)");
        }

        $select = $write->select()
            ->from(
                array('i' => $this->_getBundlePriceTable()),
                array('entity_id', 'customer_group_id', 'website_id'))
            ->join(
                array('bo' => $this->getTable('bundle/option')),
                'bo.parent_id = i.entity_id',
                array('option_id'))
            ->join(
                array('bs' => $this->getTable('bundle/selection')),
                'bs.option_id = bo.option_id',
                array('selection_id'))
            ->join(
                array('idx' => $this->getIdxTable()),
                'bs.product_id = idx.entity_id AND i.customer_group_id = idx.customer_group_id'
                    . ' AND i.website_id = idx.website_id',
                array())
            ->where('i.price_type=?', $priceType)
            ->columns(array(
                'group_type'    => new Zend_Db_Expr("IF(bo.type = 'select' OR bo.type = 'radio', 0, 1)"),
                'is_required'   => 'bo.required',
                'price'         => $priceExpr,
                'tier_price'    => $tierExpr,
            ));

        $query = $select->insertFromSelect($this->_getBundleSelectionTable());
        $write->query($query);

        return $this;
    }

    /**
     * Prepare temporary index price for bundle products
     *
     * @param int|array $entityIds  the entity ids limitation
     * @return Mage_Bundle_Model_Mysql4_Indexer_Price
     */
    protected function _prepareBundlePrice($entityIds = null)
    {
        $this->_prepareWebsiteDateTable();
        $this->_prepareBundlePriceTable();
        $this->_prepareBundlePriceByType(Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED, $entityIds);
        $this->_prepareBundlePriceByType(Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC, $entityIds);

        /**
         * Add possibility modify prices from external events
         */
        $select = $this->_getWriteAdapter()->select()
            ->join(array('wd' => $this->_getWebsiteDateTable()),
                'i.website_id = wd.website_id',
                array());
        Mage::dispatchEvent('prepare_catalog_product_price_index_table', array(
            'index_table'       => array('i' => $this->_getDefaultFinalPriceTable()),
            'select'            => $select,
            'entity_id'         => 'i.entity_id',
            'customer_group_id' => 'i.customer_group_id',
            'website_id'        => 'i.website_id',
            'website_date'      => 'wd.date',
            'update_fields'     => array('price', 'min_price', 'max_price')
        ));

        $this->_calculateBundleOptionPrice();
        $this->_applyCustomOption();
        $this->_movePriceDataToIndexTable();

        return $this;
    }
}
