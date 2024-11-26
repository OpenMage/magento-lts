<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Wishlist Report collection
 *
 * @category   Mage
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Wishlist_Product_Collection extends Mage_Wishlist_Model_Resource_Product_Collection
{
    protected function _construct()
    {
        $this->_init('wishlist/wishlist');
    }

    /**
     * @return $this
     */
    public function addWishlistCount()
    {
        $wishlistItemTable = $this->getTable('wishlist/item');
        $this->getSelect()
            ->join(
                ['wi' => $wishlistItemTable],
                'wi.product_id = e.entity_id',
                ['wishlists' => new Zend_Db_Expr('COUNT(wi.wishlist_item_id)')]
            )
            ->where('wi.product_id = e.entity_id')
            ->group('wi.product_id');
        /*
         * Allow Analytic Functions Usage
         */
        $this->_useAnalyticFunction = true;

        $this->getEntity()->setStore(0);
        return $this;
    }

    /**
     * add customer count to result
     *
     * @return $this
     */
    public function getCustomerCount()
    {
        $this->getSelect()->reset();

        $this->getSelect()
            ->from(
                ['wishlist' => $this->getTable('wishlist/wishlist')],
                [
                    'wishlist_cnt' => new Zend_Db_Expr('COUNT(wishlist.wishlist_id)'),
                    'wishlist.customer_id'
                ]
            )
            ->group('wishlist.customer_id');
        return $this;
    }

    /**
     * Get select count sql
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->columns('COUNT(*)');

        return $countSelect;
    }

    /**
     * Set order to result
     *
     * @param string $attribute
     * @param string $dir
     * @return $this
     */
    public function setOrder($attribute, $dir = self::SORT_ORDER_DESC)
    {
        if ($attribute == 'wishlists') {
            $this->getSelect()->order($attribute . ' ' . $dir);
        } else {
            parent::setOrder($attribute, $dir);
        }

        return $this;
    }
}
