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
 * @package     Mage_Wishlist
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Wishlist Product collection
 * Deprecated because after Magento 1.4.2.0 it's impossible
 * to use product collection in wishlist
 *
 * @deprecated after 1.4.2.0
 * @category   Mage
 * @package    Mage_Wishlist
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Model_Mysql4_Product_Collection
    extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{
    /**
     * Add days in whishlist filter of product collection
     *
     * @var boolean
     */
    protected $_addDaysInWishlist = false;



    /**
     * Get add days in whishlist filter of product collection flag
     *
     * @return boolean
     */
    public function getDaysInWishlist()
    {
        return $this->_addDaysInWishlist;
    }

    /**
     * Set add days in whishlist filter of product collection flag
     *
     * @return Mage_Wishlist_Model_Mysql4_Product_Collection
     */
    public function setDaysInWishlist($flag)
    {
        $this->_addDaysInWishlist = (bool) $flag;
        return $this;
    }

    /**
     * Add wishlist filter to collection
     *
     * @param Mage_Wishlist_Model_Wishlist $wishlist
     * @return Mage_Wishlist_Model_Mysql4_Product_Collection
     */
    public function addWishlistFilter(Mage_Wishlist_Model_Wishlist $wishlist)
    {
        $this->joinTable(
            array('t_wi' => 'wishlist/item'),
            'product_id=entity_id',
            array(
                'product_id'                => 'product_id',
                'wishlist_item_description' => 'description',
                'item_store_id'             => 'store_id',
                'added_at'                  => 'added_at',
                'wishlist_id'               => 'wishlist_id',
                'wishlist_item_id'          => 'wishlist_item_id',
            ),
            array(
                'wishlist_id'               => $wishlist->getId(),
                'store_id'                  => array('in' => $wishlist->getSharedStoreIds())
            )
        );

        $this->_productLimitationFilters['store_table']  = 't_wi';

        $this->setFlag('url_data_object', true);
        $this->setFlag('do_not_use_category_id', true);

        return $this;
    }

    /**
     * Add wishlist sort order
     *
     * @param string $attribute
     * @param string $dir
     * @return Mage_Wishlist_Model_Mysql4_Product_Collection
     */
    public function addWishListSortOrder($attribute = 'added_at', $dir = 'desc')
    {
        $this->setOrder($attribute, $dir);
        return $this;
    }

    /**
     * Reset sort order
     *
     * @return Mage_Wishlist_Model_Mysql4_Product_Collection
     */
    public function resetSortOrder()
    {
        $this->getSelect()->reset(Zend_Db_Select::ORDER);
        return $this;
    }

    /**
     * Add store data (days in wishlist)
     *
     * @return Mage_Wishlist_Model_Mysql4_Product_Collection
     */
    public function addStoreData()
    {
        if (!$this->getDaysInWishlist()) {
            return $this;
        }

        $this->setDaysInWishlist(false);

        $dayTable = 't_wi'; //$this->_getAttributeTableAlias('days_in_wishlist');

        $this->joinField('store_name', 'core/store', 'name', 'store_id=item_store_id');
        $this->joinField('days_in_wishlist',
            'wishlist/item',
            "(TO_DAYS('" . (substr(Mage::getSingleton('core/date')->date(), 0, -2) . '00') . "') - TO_DAYS(DATE_ADD(".$dayTable.".added_at, INTERVAL " .(int) Mage::getSingleton('core/date')->getGmtOffset() . " SECOND)))",
            'wishlist_item_id=wishlist_item_id'
        );

        return $this;
    }

    /**
     * Rewrite retrieve attribute field name for wishlist attributes
     *
     * @param string $attributeCode
     * @return Mage_Wishlist_Model_Mysql4_Product_Collection
     */
    protected function _getAttributeFieldName($attributeCode)
    {
        if ($attributeCode == 'days_in_wishlist') {
            return $this->_joinFields[$attributeCode]['field'];
        }
        return parent::_getAttributeFieldName($attributeCode);
    }

    /**
     * Prevent loading collection because since Magento 1.4.2.0 it's impossible
     * to use product collection in wishlist
     *
     * @return bool
     */
    public function load($printQuery = false, $logQuery = false)
    {
        return $this;
    }
}
