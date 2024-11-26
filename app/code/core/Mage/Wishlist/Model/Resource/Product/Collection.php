<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Wishlist Product collection
 * Deprecated because after Magento 1.4.2.0 it's impossible
 * to use product collection in wishlist
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @deprecated after 1.4.2.0
 */
class Mage_Wishlist_Model_Resource_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{
    /**
     * Add days in wishlist filter of product collection
     *
     * @var bool
     */
    protected $_addDaysInWishlist  = false;

    /**
     * Wishlist item table alias
     * @var string
     */
    protected $_wishlistItemTableAlias         = 't_wi';

    /**
     * Get add days in wishlist filter of product collection flag
     *
     * @return bool
     */
    public function getDaysInWishlist()
    {
        return $this->_addDaysInWishlist;
    }

    /**
     * Set add days in wishlist filter of product collection flag
     *
     * @param bool $flag
     * @return $this
     */
    public function setDaysInWishlist($flag)
    {
        $this->_addDaysInWishlist = (bool) $flag;
        return $this;
    }

    /**
     * Add wishlist filter to collection
     *
     * @return $this
     */
    public function addWishlistFilter(Mage_Wishlist_Model_Wishlist $wishlist)
    {
        $this->joinTable(
            [$this->_wishlistItemTableAlias => 'wishlist/item'],
            'product_id=entity_id',
            [
                'product_id'                => 'product_id',
                'wishlist_item_description' => 'description',
                'item_store_id'             => 'store_id',
                'added_at'                  => 'added_at',
                'wishlist_id'               => 'wishlist_id',
                'wishlist_item_id'          => 'wishlist_item_id',
            ],
            [
                'wishlist_id'               => $wishlist->getId(),
                'store_id'                  => ['in' => $wishlist->getSharedStoreIds()]
            ]
        );

        $this->_productLimitationFilters['store_table']  = $this->_wishlistItemTableAlias;

        $this->setFlag('url_data_object', true);
        $this->setFlag('do_not_use_category_id', true);

        return $this;
    }

    /**
     * Add wishlist sort order
     *
     * @param string $attribute
     * @param string $dir
     * @return $this
     */
    public function addWishListSortOrder($attribute = 'added_at', $dir = 'desc')
    {
        $this->setOrder($attribute, $dir);
        return $this;
    }

    /**
     * Reset sort order
     *
     * @return $this
     */
    public function resetSortOrder()
    {
        $this->getSelect()->reset(Zend_Db_Select::ORDER);
        return $this;
    }

    /**
     * Add store data (days in wishlist)
     *
     * @return $this
     */
    public function addStoreData()
    {
        $adapter = $this->getConnection();
        if (!$this->getDaysInWishlist()) {
            return $this;
        }

        $this->setDaysInWishlist(false);

        /** @var Mage_Core_Model_Resource_Helper_Mysql4 $resourceHelper */
        $resourceHelper = Mage::getResourceHelper('core');
        $nowDate = $adapter->formatDate(Mage::getSingleton('core/date')->date());

        $this->joinField('store_name', 'core/store', 'name', 'store_id=item_store_id');
        $this->joinField(
            'days_in_wishlist',
            'wishlist/item',
            $resourceHelper->getDateDiff($this->_wishlistItemTableAlias . '.added_at', $nowDate),
            'wishlist_item_id=wishlist_item_id'
        );

        return $this;
    }

    /**
     * Rewrite retrieve attribute field name for wishlist attributes
     *
     * @inheritDoc
     */
    protected function _getAttributeFieldName($attributeCode)
    {
        if ($attributeCode === 'days_in_wishlist') {
            return $this->_joinFields[$attributeCode]['field'];
        }
        return parent::_getAttributeFieldName($attributeCode);
    }

    /**
     * Prevent loading collection because after Magento 1.4.2.0 it's impossible
     * to use product collection in wishlist
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return Mage_Wishlist_Model_Resource_Product_Collection
     */
    public function load($printQuery = false, $logQuery = false)
    {
        return $this;
    }
}
