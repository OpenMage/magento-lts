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
 * Configurable Products Price Indexer Resource model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Configurable
    extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Default
{
    /**
     * Reindex temporary (price result data) for all products
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Interface
     */
    public function reindexAll()
    {
        $this->_prepareFinalPriceData();
        $this->_applyCustomOption();
        $this->_applyConfigurableOption();
        $this->_movePriceDataToIndexTable();

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
        return $this->getIdxTable() . '_cfg_opt_aggregate';
    }

    /**
     * Retrieve table name for custom option prices data
     *
     * @return string
     */
    protected function _getConfigurableOptionPriceTable()
    {
        return $this->getIdxTable() . '_cfg_option';
    }

    /**
     * Prepare table structure for custom option temporary aggregation data
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Default
     */
    protected function _prepareConfigurableOptionAggregateTable()
    {
        $write = $this->_getWriteAdapter();
        $table = $this->_getConfigurableOptionAggregateTable();

        $query = sprintf('DROP TABLE IF EXISTS %s', $write->quoteIdentifier($table));
        $write->query($query);

        $query = sprintf('CREATE TABLE %s ('
            . ' `parent_id` INT(10) UNSIGNED NOT NULL,'
            . ' `child_id` INT(10) UNSIGNED NOT NULL,'
            . ' `customer_group_id` SMALLINT(5) UNSIGNED NOT NULL,'
            . ' `website_id` SMALLINT(5) UNSIGNED NOT NULL,'
            . ' `price` DECIMAL(12,4) DEFAULT NULL,'
            . ' `tier_price` DECIMAL(12,4) DEFAULT NULL,'
            . ' PRIMARY KEY (`parent_id`, `child_id`, `customer_group_id`, `website_id`)'
            . ') ENGINE=MYISAM DEFAULT CHARSET=utf8',
            $write->quoteIdentifier($table));
        $write->query($query);

        return $this;
    }

    /**
     * Prepare table structure for custom option prices data
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Default
     */
    protected function _prepareConfigurableOptionPriceTable()
    {
        $write = $this->_getWriteAdapter();
        $table = $this->_getConfigurableOptionPriceTable();

        $query = sprintf('DROP TABLE IF EXISTS %s', $write->quoteIdentifier($table));
        $write->query($query);

        $query = sprintf('CREATE TABLE %s ('
            . ' `entity_id` INT(10) UNSIGNED NOT NULL,'
            . ' `customer_group_id` SMALLINT(5) UNSIGNED NOT NULL,'
            . ' `website_id` SMALLINT(5) UNSIGNED NOT NULL,'
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
     * Calculate minimal and maximal prices for configurable product options
     * and apply it to final price
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Configurable
     */
    protected function _applyConfigurableOption()
    {
        $write      = $this->_getWriteAdapter();
        $coaTable   = $this->_getConfigurableOptionAggregateTable();
        $copTable   = $this->_getConfigurableOptionPriceTable();

        $this->_prepareConfigurableOptionAggregateTable();
        $this->_prepareConfigurableOptionPriceTable();

        $select = $write->select()
            ->from(array('i' => $this->_getDefaultFinalPriceTable()), null)
            ->join(
                array('l' => $this->getTable('catalog/product_super_link')),
                'l.parent_id = i.entity_id',
                array('parent_id', 'product_id'))
            ->columns(array('customer_group_id', 'website_id'), 'i')
            ->join(
                array('a' => $this->getTable('catalog/product_super_attribute')),
                'l.parent_id = a.product_id',
                array())
            ->join(
                array('cp' => $this->getValueTable('catalog/product', 'int')),
                'l.product_id = cp.entity_id AND cp.attribute_id = a.attribute_id AND cp.store_id = 0',
                array())
            ->joinLeft(
                array('apd' => $this->getTable('catalog/product_super_attribute_pricing')),
                'a.product_super_attribute_id = apd.product_super_attribute_id'
                    . ' AND apd.website_id = 0 AND cp.value = apd.value_index',
                array())
            ->joinLeft(
                array('apw' => $this->getTable('catalog/product_super_attribute_pricing')),
                'a.product_super_attribute_id = apw.product_super_attribute_id'
                    . ' AND apw.website_id = i.website_id AND cp.value = apw.value_index',
                array())
            ->join(
                array('le' => $this->getTable('catalog/product')),
                'le.entity_id = l.product_id',
                array())
            ->columns(new Zend_Db_Expr("SUM(IF((@price:=IF(apw.value_id, apw.pricing_value, apd.pricing_value))"
                . " IS NULL, 0, IF(IF(apw.value_id, apw.is_percent, apd.is_percent) = 1, "
                . "ROUND(i.price * (@price / 100), 4), @price)))"))
            ->columns(new Zend_Db_Expr("IF(i.tier_price IS NOT NULL, SUM(IF((@tier_price:="
                . "IF(apw.value_id, apw.pricing_value, apd.pricing_value)) IS NULL, 0, IF("
                . "IF(apw.value_id, apw.is_percent, apd.is_percent) = 1, "
                . "ROUND(i.price * (@tier_price / 100), 4), @tier_price))), NULL)"))
            ->where('le.required_options=0')
            ->group(array('l.parent_id', 'i.customer_group_id', 'i.website_id', 'l.product_id'));

        $query = $select->insertFromSelect($coaTable);
        $write->query($query);

        $select = $write->select()
            ->from(
                array($coaTable),
                array('parent_id', 'customer_group_id', 'website_id', 'MIN(price)', 'MAX(price)', 'MIN(tier_price)'))
            ->group('parent_id', 'customer_group_id', 'website_id');

        $query = $select->insertFromSelect($copTable);
        $write->query($query);

        $table  = array('i' => $this->_getDefaultFinalPriceTable());
        $select = $write->select()
            ->join(
                array('io' => $copTable),
                'i.entity_id = io.entity_id AND i.customer_group_id = io.customer_group_id'
                    .' AND i.website_id = io.website_id',
                array());
        $select->columns(array(
            'min_price'  => new Zend_Db_Expr('i.min_price + io.min_price'),
            'max_price'  => new Zend_Db_Expr('i.max_price + io.max_price'),
            'tier_price' => new Zend_Db_Expr('IF(i.tier_price IS NOT NULL, i.tier_price + io.tier_price, NULL)'),
        ));
        $query = $select->crossUpdateFromSelect($table);
        $write->query($query);

        $write->truncate($coaTable);
        $write->truncate($copTable);

        return $this;
    }
}
