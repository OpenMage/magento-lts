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
 * Bundle Stock Status Indexer Resource Model
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Model_Mysql4_Indexer_Stock extends Mage_CatalogInventory_Model_Mysql4_Indexer_Stock_Default
{
/**
     * Reindex temporary (price result data) for all products
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Interface
     */
    public function reindexAll()
    {
        $this->_prepareIndexTable();
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
        $this->_prepareIndexTable($entityIds);

        return $this;
    }

    /**
     * Retrieve table name for temporary bundle option stock index
     *
     * @return string
     */
    protected function _getBundleOptionTable()
    {
        return $this->getMainTable() . '_bundle_option';
    }

    /**
     * Prepare table structure for temporary bundle option stock index
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
            . ' `website_id` SMALLINT(5) UNSIGNED NOT NULL,'
            . ' `stock_id` SMALLINT(5) UNSIGNED NOT NULL,'
            . ' `option_id` INT(10) UNSIGNED DEFAULT \'0\','
            . ' `stock_status` TINYINT(1) DEFAULT 0,'
            . ' PRIMARY KEY (`entity_id`,`stock_id`,`website_id`, `option_id`)'
            . ') ENGINE=MYISAM DEFAULT CHARSET=utf8',
            $write->quoteIdentifier($table));
        $write->query($query);

        return $this;
    }

    /**
     * Prepare stock status per Bundle options, website and stock
     *
     * @param int|array $entityIds
     * @return Mage_Bundle_Model_Mysql4_Indexer_Stock
     */
    protected function _prepareBundleOptionStockData($entityIds = null)
    {
        $write = $this->_getWriteAdapter();
        $this->_prepareBundleOptionTable();

        $select = $write->select()
            ->from(array('bo' => $this->getTable('bundle/option')), array('parent_id'));
        $this->_addWebsiteJoinToSelect($select, false);
        $select->columns('website_id', 'cw')
            ->join(
                array('cis' => $this->getTable('cataloginventory/stock')),
                '',
                array('stock_id'))
            ->join(
                array('bs' => $this->getTable('bundle/selection')),
                'bs.option_id = bo.option_id',
                array())
            ->joinLeft(
                array('i' => $this->getIdxTable()),
                'i.product_id = bs.product_id AND i.website_id = cw.website_id AND i.stock_id = cis.stock_id',
                array())
            ->where('cw.website_id != 0')
            ->where('bo.required = ?', 1)
            ->group(array('bo.parent_id', 'cw.website_id', 'cis.stock_id', 'bo.option_id'))
            ->columns(array(
                'option_id' => 'bo.option_id',
                'status'    => new Zend_Db_Expr("MAX(i.stock_status)")
            ));

        if (!is_null($entityIds)) {
            $select->where('bo.parent_id IN(?)', $entityIds);
        }

        $query = $select->insertFromSelect($this->_getBundleOptionTable());
        $write->query($query);

        return $this;
    }

    /**
     * Prepare stock status data in temporary index table
     *
     * @param int|array $entityIds  the product limitation
     * @return Mage_CatalogInventory_Model_Mysql4_Indexer_Stock_Configurable
     */
    protected function _prepareIndexTable($entityIds = null)
    {
        $this->_prepareBundleOptionStockData($entityIds);
        $write  = $this->_getWriteAdapter();

        $select = $write->select()
            ->from(array('e' => $this->getTable('catalog/product')), array('entity_id'));
        $this->_addWebsiteJoinToSelect($select, true);
        $this->_addProductWebsiteJoinToSelect($select, 'cw.website_id', 'e.entity_id');
        $select->columns('cw.website_id')
            ->join(
                array('cis' => $this->getTable('cataloginventory/stock')),
                '',
                array('stock_id'))
            ->joinLeft(
                array('cisi' => $this->getTable('cataloginventory/stock_item')),
                'cisi.stock_id = cis.stock_id AND cisi.product_id = e.entity_id',
                array())
            ->joinLeft(
                array('o' => $this->_getBundleOptionTable()),
                'o.entity_id = e.entity_id AND o.website_id = cw.website_id AND o.stock_id = cis.stock_id',
                array())
            ->columns(array('qty' => new Zend_Db_Expr('0')))
            ->where('cw.website_id != 0')
            ->where('e.type_id = ?', $this->getTypeId())
            ->group(array('e.entity_id', 'cw.website_id', 'cis.stock_id'));

        // add limitation of status
        $condition = $write->quoteInto('=?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        $this->_addAttributeToSelect($select, 'status', 'e.entity_id', 'cs.store_id', $condition);

        if ($this->_isManageStock()) {
            $statusExpr = new Zend_Db_Expr('IF(cisi.use_config_manage_stock = 0 AND cisi.manage_stock = 0,'
                . ' 1, cisi.is_in_stock)');
        } else {
            $statusExpr = new Zend_Db_Expr('IF(cisi.use_config_manage_stock = 0 AND cisi.manage_stock = 1,'
                . 'cisi.is_in_stock, 1)');
        }

        $select->columns(array('status' => new Zend_Db_Expr("LEAST(MAX(o.stock_status), {$statusExpr})")));

        if (!is_null($entityIds)) {
            $select->where('e.entity_id IN(?)', $entityIds);
        }

        $query = $select->insertFromSelect($this->getIdxTable());
        $write->query($query);

        return $this;
    }
}
