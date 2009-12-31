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
 * @package     Mage_CatalogInventory
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * CatalogInventory Grouped Products Stock Status Indexer Resource Model
 *
 * @category    Mage
 * @package     Mage_CatalogInventory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogInventory_Model_Mysql4_Indexer_Stock_Grouped
    extends Mage_CatalogInventory_Model_Mysql4_Indexer_Stock_Default
{
    /**
     * Reindex all stock status data for configurable products
     *
     * @return Mage_CatalogInventory_Model_Mysql4_Indexer_Stock_Grouped
     */
    public function reindexAll()
    {
        $this->_prepareIndexTable();
        return $this;
    }

    /**
     * Reindex stock data for defined configurable product ids
     *
     * @param int|array $entityIds
     * @return Mage_CatalogInventory_Model_Mysql4_Indexer_Stock_Grouped
     */
    public function reindexEntity($entityIds)
    {
        $this->_prepareIndexTable($entityIds);
        return $this;
    }

    /**
     * Prepare stock status data in temporary index table
     *
     * @param int|array $entityIds  the product limitation
     * @return Mage_CatalogInventory_Model_Mysql4_Indexer_Stock_Grouped
     */
    protected function _prepareIndexTable($entityIds = null)
    {
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
                array('l' => $this->getTable('catalog/product_link')),
                'l.product_id = e.entity_id',
                array())
            ->joinLeft(
                array('i' => $this->getIdxTable()),
                'i.product_id = l.linked_product_id AND cw.website_id = i.website_id AND cis.stock_id = i.stock_id',
                array())
            ->columns(array('qty' => new Zend_Db_Expr('0')))
            ->where('cw.website_id != 0')
            ->where('e.type_id = ?', $this->getTypeId())
            ->where('l.link_type_id=?', Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED)
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

        $select->columns(array('status' => new Zend_Db_Expr("LEAST(MAX(i.stock_status), {$statusExpr})")));

        if (!is_null($entityIds)) {
            $select->where('e.entity_id IN(?)', $entityIds);
        }

        $query = $select->insertFromSelect($this->getIdxTable());
        $write->query($query);

        return $this;
    }
}
