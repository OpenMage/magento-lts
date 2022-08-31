<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Wishlist Report collection
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Resource_Wishlist_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Wishlist table name
     *
     * @var string
     */
    protected $_wishlistTable;

    /**
     * Resource initialization
     *
     */
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
            []
        )
            ->group('wt.wishlist_id');
        $count = $collection->count();
        $resultSelect = $this->getConnection()->select()
            ->union([$customersSelect, $count], Zend_Db_Select::SQL_UNION_ALL);
        list($customers, $count) = $this->getConnection()->fetchCol($resultSelect);

        return [($count*100)/$customers, $count];
    }

    /**
     * Get shared items collection count
     *
     * @return int
     */
    public function getSharedCount()
    {
        /** @var Mage_Customer_Model_Resource_Customer_Collection $collection */
        $collection = Mage::getResourceModel('customer/customer_collection');
        $countSelect = $collection->getSelectCountSql();
        $countSelect->joinLeft(
            ['wt' => $this->getWishlistTable()],
            'wt.customer_id=e.entity_id',
            []
        )
            ->where('wt.shared=1')
            ->group('wt.wishlist_id');
        return $countSelect->getAdapter()->fetchOne($countSelect);
    }
}
