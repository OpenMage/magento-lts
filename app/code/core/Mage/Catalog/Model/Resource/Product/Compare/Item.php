<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog compare item resource model
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Resource_Product_Compare_Item extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('catalog/compare_item', 'catalog_compare_item_id');
    }

    /**
     * Load object by product
     *
     * @param  mixed $product
     * @return bool
     */
    public function loadByProduct(Mage_Catalog_Model_Product_Compare_Item $object, $product)
    {
        $read = $this->_getReadAdapter();
        if ($product instanceof Mage_Catalog_Model_Product) {
            $productId = $product->getId();
        } else {
            $productId = $product;
        }

        $select = $read->select()->from($this->getMainTable())
            ->where('product_id = ?', (int) $productId);

        if ($object->getCustomerId()) {
            $select->where('customer_id = ?', (int) $object->getCustomerId());
        } else {
            $select->where('visitor_id = ?', (int) $object->getVisitorId());
        }

        $data = $read->fetchRow($select);

        if (!$data) {
            return false;
        }

        $object->setData($data);

        $this->_afterLoad($object);
        return true;
    }

    /**
     * Resource retrieve count compare items
     *
     * @param  int               $customerId
     * @param  int               $visitorId
     * @return null|false|string
     */
    public function getCount($customerId, $visitorId)
    {
        $bind = ['visitore_id' => (int) $visitorId];
        $select = $this->_getReadAdapter()->select()->from($this->getMainTable(), 'COUNT(*)')
            ->where('visitor_id = :visitore_id');
        if ($customerId) {
            $bind['customer_id'] = (int) $customerId;
            $select->where('customer_id = :customer_id');
        }

        return $this->_getReadAdapter()->fetchOne($select, $bind);
    }

    /**
     * Clean compare table
     *
     * @return $this
     */
    public function clean()
    {
        while (true) {
            $select = $this->_getReadAdapter()->select()
                ->from(['compare_table' => $this->getMainTable()], ['catalog_compare_item_id'])
                ->joinLeft(
                    ['visitor_table' => $this->getTable('log/visitor')],
                    'visitor_table.visitor_id=compare_table.visitor_id AND compare_table.customer_id IS NULL',
                    [],
                )
                ->where('compare_table.visitor_id > ?', 0)
                ->where('visitor_table.visitor_id IS NULL')
                ->limit(100);
            $itemIds = $this->_getReadAdapter()->fetchCol($select);

            if (!$itemIds) {
                break;
            }

            $this->_getWriteAdapter()->delete(
                $this->getMainTable(),
                $this->_getWriteAdapter()->quoteInto('catalog_compare_item_id IN(?)', $itemIds),
            );
        }

        return $this;
    }

    /**
     * Purge visitor data after customer logout
     *
     * @param  Mage_Catalog_Model_Product_Compare_Item $object
     * @return $this
     */
    public function purgeVisitorByCustomer($object)
    {
        if (!$object->getCustomerId()) {
            return $this;
        }

        $where  = $this->_getWriteAdapter()->quoteInto('customer_id=?', $object->getCustomerId());
        $bind   = [
            'visitor_id' => 0,
        ];

        $this->_getWriteAdapter()->update($this->getMainTable(), $bind, $where);

        return $this;
    }

    /**
     * Update (Merge) customer data from visitor
     * After Login process
     *
     * @param  Mage_Catalog_Model_Product_Compare_Item $object
     * @return $this
     */
    public function updateCustomerFromVisitor($object)
    {
        if (!$object->getCustomerId()) {
            return $this;
        }

        // collect visitor compared items
        $select = $this->_getWriteAdapter()->select()
            ->from($this->getMainTable())
            ->where('visitor_id=?', $object->getVisitorId());
        $visitor = $this->_getWriteAdapter()->fetchAll($select);

        // collect customer compared items
        $select = $this->_getWriteAdapter()->select()
            ->from($this->getMainTable())
            ->where('customer_id = ?', $object->getCustomerId())
            ->where('visitor_id != ?', $object->getVisitorId());
        $customer = $this->_getWriteAdapter()->fetchAll($select);

        $products   = [];
        $delete     = [];
        $update     = [];
        foreach ($visitor as $row) {
            $products[$row['product_id']] = [
                'store_id'      => $row['store_id'],
                'customer_id'   => $object->getCustomerId(),
                'visitor_id'    => $object->getVisitorId(),
                'product_id'    => $row['product_id'],
            ];
            $update[$row[$this->getIdFieldName()]] = $row['product_id'];
        }

        foreach ($customer as $row) {
            if (isset($products[$row['product_id']])) {
                $delete[] = $row[$this->getIdFieldName()];
            } else {
                $products[$row['product_id']] = [
                    'store_id'      => $row['store_id'],
                    'customer_id'   => $object->getCustomerId(),
                    'visitor_id'    => $object->getVisitorId(),
                    'product_id'    => $row['product_id'],
                ];
            }
        }

        if ($delete) {
            $this->_getWriteAdapter()->delete(
                $this->getMainTable(),
                $this->_getWriteAdapter()->quoteInto($this->getIdFieldName() . ' IN(?)', $delete),
            );
        }

        foreach ($update as $itemId => $productId) {
            $bind = $products[$productId];
            $this->_getWriteAdapter()->update(
                $this->getMainTable(),
                $bind,
                $this->_getWriteAdapter()->quoteInto($this->getIdFieldName() . '=?', $itemId),
            );
        }

        return $this;
    }

    /**
     * Clear compare items by visitor and/or customer
     *
     * @param  int   $visitorId
     * @param  int   $customerId
     * @return $this
     */
    public function clearItems($visitorId = null, $customerId = null)
    {
        $where = [];
        if ($customerId) {
            $customerId = (int) $customerId;
            $where[] = $this->_getWriteAdapter()->quoteInto('customer_id = ?', $customerId);
        }

        if ($visitorId) {
            $visitorId = (int) $visitorId;
            $where[] = $this->_getWriteAdapter()->quoteInto('visitor_id = ?', $visitorId);
        }

        if (!$where) {
            return $this;
        }

        $this->_getWriteAdapter()->delete($this->getMainTable(), $where);
        return $this;
    }
}
