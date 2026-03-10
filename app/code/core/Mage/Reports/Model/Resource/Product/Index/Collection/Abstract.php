<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * Reports Product Index Abstract Product Resource Collection
 *
 * @package    Mage_Reports
 */
abstract class Mage_Reports_Model_Resource_Product_Index_Collection_Abstract extends Mage_Catalog_Model_Resource_Product_Collection
{
    /**
     * Customer id
     *
     * @var null|int
     */
    protected $_customerId = null;

    /**
     * List of ids
     * After collection will be loaded Items will be sorted as this list
     *
     * @var array
     */
    protected $_sortIds = [];

    /**
     * Retrieve Product Index table name
     */
    abstract protected function _getTableName();

    /**
     * Join index table
     *
     * @return Mage_Reports_Model_Resource_Product_Index_Collection_Abstract
     */
    protected function _joinIdxTable()
    {
        if (!$this->getFlag('is_idx_table_joined')) {
            $this->joinTable(
                ['idx_table' => $this->_getTableName()],
                'product_id=entity_id',
                [
                    'product_id'    => 'product_id',
                    'item_store_id' => 'store_id',
                    'added_at'      => 'added_at',
                ],
                $this->_getWhereCondition(),
            );
            $this->setFlag('is_idx_table_joined', true);
        }

        return $this;
    }

    /**
     * Add Viewed Products Index to Collection
     *
     * @return Mage_Reports_Model_Resource_Product_Index_Collection_Abstract
     */
    public function addIndexFilter()
    {
        $this->_joinIdxTable();
        $this->_productLimitationFilters['store_table'] = 'idx_table';
        $this->setFlag('url_data_object', true);
        $this->setFlag('do_not_use_category_id', true);
        return $this;
    }

    /**
     * Add filter by product ids
     *
     * @param  array                                                         $ids
     * @return Mage_Reports_Model_Resource_Product_Index_Collection_Abstract
     */
    public function addFilterByIds($ids)
    {
        if (empty($ids)) {
            $this->getSelect()->where('1=0');
        } else {
            $this->getSelect()->where('e.entity_id IN(?)', $ids);
        }

        return $this;
    }

    /**
     * Retrieve Where Condition to Index table
     *
     * @return array
     */
    protected function _getWhereCondition()
    {
        $condition = [];

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $condition['customer_id'] = Mage::getSingleton('customer/session')->getCustomerId();
        } elseif ($this->_customerId) {
            $condition['customer_id'] = $this->_customerId;
        } else {
            $condition['visitor_id'] = Mage::getSingleton('log/visitor')->getId();
        }

        return $condition;
    }

    /**
     * Set customer id, that will be used in 'whereCondition'
     *
     * @param  int                                                           $id
     * @return Mage_Reports_Model_Resource_Product_Index_Collection_Abstract
     */
    public function setCustomerId($id)
    {
        $this->_customerId = (int) $id;
        return $this;
    }

    /**
     * Add order by "added at"
     *
     * @param  string                                                        $dir
     * @return Mage_Reports_Model_Resource_Product_Index_Collection_Abstract
     */
    public function setAddedAtOrder($dir = self::SORT_ORDER_DESC)
    {
        if ($this->getFlag('is_idx_table_joined')) {
            $this->getSelect()->order('added_at ' . $dir);
        }

        return $this;
    }

    /**
     * Set list of ids with expected order
     *
     * @return Mage_Reports_Model_Resource_Product_Index_Collection_Abstract
     */
    public function setSortIds(array $ids)
    {
        $this->_sortIds = $ids;
        return $this;
    }

    /**
     * Sort loaded collection by predefined items ids
     *
     * @return Mage_Reports_Model_Resource_Product_Index_Collection_Abstract
     */
    protected function _sort()
    {
        if (!empty($this->_sortIds)) {
            $orderedItems = [];
            foreach ($this->_sortIds as $id) {
                if (isset($this->_items[$id])) {
                    $orderedItems[$id] = $this->_items[$id];
                }
            }

            $this->_items = $orderedItems;
        }

        return $this;
    }

    /**
     * Sort items
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _afterLoad()
    {
        $result = parent::_afterLoad();
        $this->_sort();
        return $result;
    }

    /**
     * Add exclude Product Ids
     *
     * @param  array|int                                                     $productIds
     * @return Mage_Reports_Model_Resource_Product_Index_Collection_Abstract
     */
    public function excludeProductIds($productIds)
    {
        if (empty($productIds)) {
            return $this;
        }

        $this->_joinIdxTable();
        $this->getSelect()->where('idx_table.product_id NOT IN(?)', $productIds);
        return $this;
    }
}
