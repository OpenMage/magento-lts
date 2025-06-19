<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * Wishlist Report collection
 *
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Wishlist_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Wishlist table name
     *
     * @var string
     */
    protected $_wishlistTable;

    protected function _construct()
    {
        $this->_init('wishlist/wishlist');
        $this->setWishlistTable($this->getTable('wishlist/wishlist'));
    }
    /**
     * Set wishlist table name
     *
     * @param string $value
     * @return $this
     */
    public function setWishlistTable($value)
    {
        $this->_wishlistTable = $value;
        return $this;
    }

    /**
     * retrieve wishlist table name
     *
     * @return string
     */
    public function getWishlistTable()
    {
        return $this->_wishlistTable;
    }

    /**
     * Retrieve wishlist customer count
     *
     * @return array
     */
    public function getWishlistCustomerCount()
    {
        /** @var Mage_Customer_Model_Resource_Customer_Collection $collection */
        $collection = Mage::getResourceModel('customer/customer_collection');

        $customersSelect = $collection->getSelectCountSql();

        $countSelect = clone $customersSelect;
        $countSelect->joinLeft(
            ['wt' => $this->getWishlistTable()],
            'wt.customer_id = e.entity_id',
            [],
        )
            ->group('wt.wishlist_id');
        $count = $collection->count();
        $resultSelect = $this->getConnection()->select()
            ->union([$customersSelect, $count], Zend_Db_Select::SQL_UNION_ALL);
        [$customers, $count] = $this->getConnection()->fetchCol($resultSelect);

        return [($count * 100) / $customers, $count];
    }

    /**
     * Get shared items collection count
     *
     * @return false|string|null
     */
    public function getSharedCount()
    {
        /** @var Mage_Customer_Model_Resource_Customer_Collection $collection */
        $collection = Mage::getResourceModel('customer/customer_collection');
        $countSelect = $collection->getSelectCountSql();
        $countSelect->joinLeft(
            ['wt' => $this->getWishlistTable()],
            'wt.customer_id=e.entity_id',
            [],
        )
            ->where('wt.shared=1')
            ->group('wt.wishlist_id');
        return $countSelect->getAdapter()->fetchOne($countSelect);
    }
}
