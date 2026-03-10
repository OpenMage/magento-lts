<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Quote resource model
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Quote extends Mage_Sales_Model_Resource_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sales/quote', 'entity_id');
    }

    /**
     * Retrieve select object for load object data
     *
     * @param  string                                          $field
     * @param  mixed                                           $value
     * @param  Mage_Core_Model_Abstract|Mage_Sales_Model_Quote $object
     * @return Varien_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select   = parent::_getLoadSelect($field, $value, $object);
        $storeIds = $object->getSharedStoreIds();
        if ($storeIds) {
            $select->where('store_id IN (?)', $storeIds);
        } else {
            /**
             * For empty result
             */
            $select->where('store_id < ?', 0);
        }

        return $select;
    }

    /**
     * Load quote data by customer identifier
     *
     * @param  Mage_Sales_Model_Quote $quote
     * @param  int                    $customerId
     * @return $this
     */
    public function loadByCustomerId($quote, $customerId)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $this->_getLoadSelect('customer_id', $customerId, $quote)
            ->where('is_active = ?', 1)
            ->order('updated_at ' . Varien_Db_Select::SQL_DESC)
            ->limit(1);

        $data    = $adapter->fetchRow($select);

        if ($data) {
            $quote->setData($data);
        }

        $this->_afterLoad($quote);

        return $this;
    }

    /**
     * Load only active quote
     *
     * @param  Mage_Sales_Model_Quote $quote
     * @param  int                    $quoteId
     * @return $this
     */
    public function loadActive($quote, $quoteId)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $this->_getLoadSelect('entity_id', $quoteId, $quote)
            ->where('is_active = ?', 1);

        $data    = $adapter->fetchRow($select);
        if ($data) {
            $quote->setData($data);
        }

        $this->_afterLoad($quote);

        return $this;
    }

    /**
     * Load quote data by identifier without store
     *
     * @param  Mage_Sales_Model_Quote $quote
     * @param  int                    $quoteId
     * @return $this
     */
    public function loadByIdWithoutStore($quote, $quoteId)
    {
        $read = $this->_getReadAdapter();
        if ($read) {
            $select = parent::_getLoadSelect('entity_id', $quoteId, $quote);

            $data = $read->fetchRow($select);

            if ($data) {
                $quote->setData($data);
            }
        }

        $this->_afterLoad($quote);
        return $this;
    }

    /**
     * Get reserved order id
     *
     * @param  Mage_Sales_Model_Quote $quote
     * @return string
     */
    public function getReservedOrderId($quote)
    {
        $storeId = (int) $quote->getStoreId();
        return Mage::getSingleton('eav/config')->getEntityType(Mage_Sales_Model_Order::ENTITY)
            ->fetchNewIncrementId($storeId);
    }

    /**
     * Check is order increment id use in sales/order table
     *
     * @param int|string $orderIncrementId
     *
     * @return bool
     */
    public function isOrderIncrementIdUsed($orderIncrementId)
    {
        $adapter   = $this->_getReadAdapter();
        $bind      = [':increment_id' => (string) $orderIncrementId];
        $select    = $adapter->select();
        $select->from($this->getTable('sales/order'), 'entity_id')
            ->where('increment_id = :increment_id');
        $entityId = $adapter->fetchOne($select, $bind);
        if ($entityId > 0) {
            return true;
        }

        return false;
    }

    /**
     * Mark quotes - that depend on catalog price rules - to be recollected on demand
     *
     * @param null|array $productIdList
     *
     * @return $this
     */
    public function markQuotesRecollectByAffectedProduct($productIdList = null)
    {
        $writeAdapter = $this->_getWriteAdapter();
        $select = $writeAdapter->select();
        $subSelect = clone $select;

        $subSelect
            ->distinct()
            ->from(
                ['qi' => $this->getTable('sales/quote_item')],
                ['entity_id' => 'quote_id'],
            )
            ->join(
                ['pp' => $this->getTable('catalogrule/rule_product_price')],
                'qi.product_id = pp.product_id',
                [],
            );
        if ($productIdList !== null) {
            $subSelect->where('qi.product_id IN (?)', $productIdList);
        }

        $select
            ->join(
                ['tmp' => $subSelect],
                'q.entity_id = tmp.entity_id',
                ['trigger_recollect' => new Zend_Db_Expr('1')],
            )
             ->where('q.is_active = ?', 1);
        $sql = $writeAdapter->updateFromSelect($select, ['q' => $this->getTable('sales/quote')]);
        $writeAdapter->query($sql);

        return $this;
    }

    /**
     * Mark quotes - that depend on catalog price rules - to be recollected on demand
     *
     * @return $this
     */
    public function markQuotesRecollectOnCatalogRules()
    {
        return $this->markQuotesRecollectByAffectedProduct();
    }

    /**
     * Subtract product from all quotes quantities
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return $this
     */
    public function substractProductFromQuotes($product)
    {
        $productId = (int) $product->getId();
        if (!$productId) {
            return $this;
        }

        $adapter   = $this->_getWriteAdapter();
        $subSelect = $adapter->select();

        $subSelect->from(false, [
            'items_qty'   => new Zend_Db_Expr(
                $adapter->quoteIdentifier('q.items_qty') . ' - ' . $adapter->quoteIdentifier('qi.qty'),
            ),
            'items_count' => new Zend_Db_Expr($adapter->quoteIdentifier('q.items_count') . ' - 1'),
        ])
        ->where('q.items_count > 0')
        ->join(
            ['qi' => $this->getTable('sales/quote_item')],
            implode(' AND ', [
                'q.entity_id = qi.quote_id',
                'qi.parent_item_id IS NULL',
                $adapter->quoteInto('qi.product_id = ?', $productId),
            ]),
            [],
        );

        $updateQuery = $adapter->updateFromSelect($subSelect, ['q' => $this->getTable('sales/quote')]);

        $adapter->query($updateQuery);

        return $this;
    }

    /**
     * Mark recollect contain product(s) quotes
     *
     * @param  array|int|Zend_Db_Expr $productIds
     * @return $this
     */
    public function markQuotesRecollect($productIds)
    {
        $tableQuote = $this->getTable('sales/quote');
        $tableItem = $this->getTable('sales/quote_item');
        $subSelect = $this->_getReadAdapter()
            ->select()
            ->from($tableItem, ['entity_id' => 'quote_id'])
            ->where('product_id IN (?)', $productIds)
            ->group('quote_id');

        $select = $this->_getReadAdapter()->select()->join(
            ['t2' => $subSelect],
            't1.entity_id = t2.entity_id',
            ['trigger_recollect' => new Zend_Db_Expr('1')],
        );
        $updateQuery = $select->crossUpdateFromSelect(['t1' => $tableQuote]);
        $this->_getWriteAdapter()->query($updateQuery);

        return $this;
    }
}
