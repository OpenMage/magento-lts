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
 * @package    Mage_CatalogInventory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * CatalogInventory Stock Status per website Resource Model
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogInventory_Model_Resource_Stock_Status extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('cataloginventory/stock_status', 'product_id');
    }

    /**
     * Save Product Status per website
     *
     * @param Mage_CatalogInventory_Model_Stock_Status $object
     * @param int $productId
     * @param int $status
     * @param int|float $qty
     * @param int $stockId
     * @param int|null $websiteId
     * @return $this
     * @throws Zend_Db_Adapter_Exception
     */
    public function saveProductStatus(
        Mage_CatalogInventory_Model_Stock_Status $object,
        $productId,
        $status,
        $qty = 0,
        $stockId = 1,
        $websiteId = null
    ) {
        $websites = array_keys($object->getWebsites($websiteId));
        $adapter = $this->_getWriteAdapter();
        foreach ($websites as $websiteId) {
            $select = $adapter->select()
                ->from($this->getMainTable())
                ->where('product_id = :product_id')
                ->where('website_id = :website_id')
                ->where('stock_id = :stock_id');
            $bind = [
                ':product_id' => $productId,
                ':website_id' => $websiteId,
                ':stock_id'   => $stockId
            ];
            if ($row = $adapter->fetchRow($select, $bind)) {
                $bind = [
                    'qty'           => $qty,
                    'stock_status'  => $status
                ];
                $where = [
                    $adapter->quoteInto('product_id=?', (int)$row['product_id']),
                    $adapter->quoteInto('website_id=?', (int)$row['website_id']),
                    $adapter->quoteInto('stock_id=?', (int)$row['stock_id']),
                ];
                $adapter->update($this->getMainTable(), $bind, $where);
            } else {
                $bind = [
                    'product_id'    => $productId,
                    'website_id'    => $websiteId,
                    'stock_id'      => $stockId,
                    'qty'           => $qty,
                    'stock_status'  => $status
                ];
                $adapter->insert($this->getMainTable(), $bind);
            }
        }

        return $this;
    }

    /**
     * Retrieve product status
     * Return array as key product id, value - stock status
     *
     * @param int|array $productIds
     * @param int $websiteId
     * @param int $stockId
     * @return array
     */
    public function getProductStatus($productIds, $websiteId, $stockId = 1)
    {
        if (!is_array($productIds)) {
            $productIds = [$productIds];
        }

        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), ['product_id', 'stock_status'])
            ->where('product_id IN(?)', $productIds)
            ->where('stock_id=?', (int)$stockId)
            ->where('website_id=?', (int)$websiteId);
        return $this->_getReadAdapter()->fetchPairs($select);
    }

    /**
     * Retrieve product(s) data array
     *
     * @param int|array $productIds
     * @param int $websiteId
     * @param int $stockId
     * @return array
     */
    public function getProductData($productIds, $websiteId, $stockId = 1)
    {
        if (!is_array($productIds)) {
            $productIds = [$productIds];
        }

        $result = [];

        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where('product_id IN(?)', $productIds)
            ->where('stock_id=?', (int)$stockId)
            ->where('website_id=?', (int)$websiteId);
        $result = $this->_getReadAdapter()->fetchAssoc($select);
        return $result;
    }

    /**
     * Retrieve websites and default stores
     * Return array as key website_id, value store_id
     *
     * @return array
     */
    public function getWebsiteStores()
    {
        $select = Mage::getModel('core/website')->getDefaultStoresSelect(false);
        return $this->_getReadAdapter()->fetchPairs($select);
    }

    /**
     * Retrieve Product Type
     *
     * @param array|int $productIds
     * @return array
     */
    public function getProductsType($productIds)
    {
        if (!is_array($productIds)) {
            $productIds = [$productIds];
        }

        $select = $this->_getReadAdapter()->select()
            ->from(
                ['e' => $this->getTable('catalog/product')],
                ['entity_id', 'type_id']
            )
            ->where('entity_id IN(?)', $productIds);
        return $this->_getReadAdapter()->fetchPairs($select);
    }

    /**
     * Retrieve Product part Collection array
     * Return array as key product id, value product type
     *
     * @param int $lastEntityId
     * @param int $limit
     * @return array
     */
    public function getProductCollection($lastEntityId = 0, $limit = 1000)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(
                ['e' => $this->getTable('catalog/product')],
                ['entity_id', 'type_id']
            )
            ->order('entity_id ASC')
            ->where('entity_id > :entity_id')
            ->limit($limit);
        return $this->_getReadAdapter()->fetchPairs($select, [':entity_id' => $lastEntityId]);
    }

    /**
     * Add stock status to prepare index select
     *
     * @param Varien_Db_Select $select
     * @param Mage_Core_Model_Website $website
     * @return $this
     */
    public function addStockStatusToSelect(Varien_Db_Select $select, Mage_Core_Model_Website $website)
    {
        $websiteId = $website->getId();
        $select->joinLeft(
            ['stock_status' => $this->getMainTable()],
            'e.entity_id = stock_status.product_id AND stock_status.website_id=' . $websiteId,
            ['salable' => 'stock_status.stock_status']
        );

        return $this;
    }

    /**
     * Add stock status limitation to catalog product price index select object
     *
     * @param Varien_Db_Select $select
     * @param string|Zend_Db_Expr $entityField
     * @param string|Zend_Db_Expr $websiteField
     * @return $this
     */
    public function prepareCatalogProductIndexSelect(Varien_Db_Select $select, $entityField, $websiteField)
    {
        $select->join(
            ['ciss' => $this->getMainTable()],
            "ciss.product_id = {$entityField} AND ciss.website_id = {$websiteField}",
            []
        );
        $select->where('ciss.stock_status = ?', Mage_CatalogInventory_Model_Stock_Status::STATUS_IN_STOCK);

        return $this;
    }

    /**
     * Add only is in stock products filter to product collection
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @return $this
     */
    public function addIsInStockFilterToCollection($collection)
    {
        $websiteId = Mage::app()->getStore($collection->getStoreId())->getWebsiteId();
        $joinCondition = $this->_getReadAdapter()
            ->quoteInto('e.entity_id = stock_status_index.product_id'
                . ' AND stock_status_index.website_id = ?', $websiteId);

        $joinCondition .= $this->_getReadAdapter()->quoteInto(
            ' AND stock_status_index.stock_id = ?',
            Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID
        );

        $collection->getSelect()
            ->join(
                ['stock_status_index' => $this->getMainTable()],
                $joinCondition,
                []
            )
            ->where('stock_status_index.stock_status=?', Mage_CatalogInventory_Model_Stock_Status::STATUS_IN_STOCK);

        return $this;
    }
}
