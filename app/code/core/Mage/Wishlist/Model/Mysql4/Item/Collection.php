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
 * Wishlist item collection
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Model_Mysql4_Item_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Product Visibility Filter to product collection flag
     *
     * @var bool
     */
    protected $_productVisible = false;

    /**
     * Product Salable Filter to product collection flag
     *
     * @var bool
     */
    protected $_productSalable = false;

    /**
     * If product out of stock, its item will be removed after load
     *
     * @var bool
     */
    protected $_productInStock = false;

    /**
     * Product Ids array
     *
     * @var array
     */
    protected $_productIds = array();

    /**
     * Store Ids array
     *
     * @var array
     */
    protected $_storeIds = array();

    /**
     * Add days in whishlist filter of product collection
     *
     * @var boolean
     */
    protected $_addDaysInWishlist = false;

    /**
     * Sum of items collection qty
     *
     * @var int
     */
    protected $_itemsQty;

    /**
     * Initialize resource model for collection
     *
     */
    public function _construct()
    {
        $this->_init('wishlist/item');
    }

    /**
     * After load processing
     *
     * @return Mage_Wishlist_Model_Mysql4_Item_Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        /**
         * Assign products
         */
        $this->_assignOptions();
        $this->_assignProducts();
        $this->resetItemsDataChanged();


        $this->getPageSize();

        return $this;
    }

    /**
     * Add options to items
     *
     * @return Mage_Wishlist_Model_Mysql4_Item_Collection
     */
    protected function _assignOptions()
    {
        $itemIds = array_keys($this->_items);
        $optionCollection = Mage::getModel('wishlist/item_option')->getCollection()
            ->addItemFilter($itemIds);
        foreach ($this as $item) {
            $item->setOptions($optionCollection->getOptionsByItem($item));
        }
        $productIds = $optionCollection->getProductIds();
        $this->_productIds = array_merge($this->_productIds, $productIds);

        return $this;
    }

    /**
     * Add products to items and item options
     *
     * @return Mage_Wishlist_Model_Mysql4_Item_Collection
     */
    protected function _assignProducts()
    {
        Varien_Profiler::start('WISHLIST:'.__METHOD__);
        $productIds = array();
        foreach ($this as $item) {
            $productIds[$item->getProductId()]=1;
        }
        $this->_productIds = array_merge($this->_productIds, array_keys($productIds));
        $attributes = Mage::getSingleton('catalog/config')->getProductAttributes();

        $productCollection = Mage::getModel('catalog/product')->getCollection()
            ->addIdFilter($this->_productIds)
            ->addAttributeToSelect($attributes)
            ->addOptionsToResult()
            ->addPriceData()
            ->addUrlRewrite();

        if ($this->_productVisible) {
            Mage::getSingleton('catalog/product_visibility')->addVisibleInSiteFilterToCollection($productCollection);
        }
        if ($this->_productSalable) {
            $productCollection = Mage::helper('adminhtml/sales')->applySalableProductTypesFilter($productCollection);
        }

        foreach ($this->_storeIds as $id) {
            $productCollection->addStoreFilter($id);
        }

        Mage::dispatchEvent('wishlist_item_collection_products_after_load', array(
            'product_collection'    => $productCollection
        ));

        foreach ($this as $item) {
            $product = $productCollection->getItemById($item->getProductId());
            if ($product) {
                if ($this->_productInStock &&
                    !$product->isSalable() &&
                    !Mage::helper('cataloginventory')->isShowOutOfStock()) {
                        $this->removeItemByKey($item->getId());
                } else {
                    $product->setCustomOptions(array());
                    $item->setProduct($product);
                    $item->setProductName($product->getName());
                    $item->setName($product->getName());
                    $item->setPrice($product->getPrice());
                }
            } else {
                $item->isDeleted(true);
            }
        }

        Varien_Profiler::stop('WISHLIST:'.__METHOD__);

        return $this;
    }

    /**
     * Add filter by wishlist object
     *
     * @param Mage_Wishlist_Model_Wishlist $wishlist
     * @return Mage_Wishlist_Model_Mysql4_Item_Collection
     */
    public function addWishlistFilter(Mage_Wishlist_Model_Wishlist $wishlist)
    {
        $this->addFieldToFilter('wishlist_id', $wishlist->getId());

        return $this;
    }

    /**
     * Add filter by shared stores
     *
     * @param int|array $store
     * @return Mage_Wishlist_Model_Mysql4_Item_Collection
     */
    public function addStoreFilter($store = null)
    {
        if (!is_array($store)) {
            $store = array($store);
        }
        $this->_storeIds = $store;

        return $this;
    }

    /**
     * Add items store data to collection
     *
     * @return Mage_Wishlist_Model_Mysql4_Item_Collection
     */
    public function addStoreData()
    {
        $storeTable = Mage::getSingleton('core/resource')->getTableName('core/store');
        $this->getSelect()->join(array('store'=>$storeTable), 'main_table.store_id=store.store_id', array(
            'store_name'=>'name',
            'item_store_id' => 'store_id'
        ));
        return $this;
    }

    /**
     * Add wishlist sort order
     *
     * @param string $attribute
     * @param string $dir
     * @return Mage_Wishlist_Model_Mysql4_Item_Collection
     */
    public function addWishListSortOrder($attribute = 'added_at', $dir = 'desc')
    {
        $this->setOrder($attribute, $dir);
        return $this;
    }

    /**
     * Reset sort order
     *
     * @return Mage_Wishlist_Model_Mysql4_Item_Collection
     */
    public function resetSortOrder()
    {
        $this->getSelect()->reset(Zend_Db_Select::ORDER);
        return $this;
    }

    /**
     * Set product Visibility Filter to product collection flag
     *
     * @param bool $flag
     * @return Mage_Wishlist_Model_Mysql4_Item_Collection
     */
    public function setVisibilityFilter($flag = true)
    {
        $this->_productVisible = (bool)$flag;
        return $this;
    }

    /**
     * Set Salable Filter.
     * This filter apply Salable Product Types Filter to product collection.
     *
     * @param bool $flag
     * @return Mage_Wishlist_Model_Mysql4_Item_Collection
     */
    public function setSalableFilter($flag = true)
    {
        $this->_productSalable = (bool)$flag;
        return $this;
    }

    /**
     * Set In Stock Filter.
     * This filter remove items with no salable product.
     *
     * @param bool $flag
     * @return Mage_Wishlist_Model_Mysql4_Item_Collection
     */
    public function setInStockFilter($flag = true)
    {
        $this->_productInStock = (bool)$flag;
        return $this;
    }

    /**
     * Set add days in whishlist
     *
     * @return Mage_Wishlist_Model_Mysql4_Item_Collection
     */
    public function addDaysInWishlist($flag = null)
    {
        $this->getSelect()->columns(array('days_in_wishlist' =>
            "(TO_DAYS('" . (substr(Mage::getSingleton('core/date')->date(), 0, -2) . '00') . "') ".
            "- TO_DAYS(DATE_ADD(added_at, INTERVAL " .(int) Mage::getSingleton('core/date')->getGmtOffset() . " SECOND)))"));
        return $this;
    }

    /**
     * Get sum of items collection qty
     *
     * @return int
     */
    public function getItemsQty(){
        if (is_null($this->_itemsQty)) {
            $sizeQuery = $this->getSelectCountSql();
            $sizeQuery->reset(Zend_Db_Select::COLUMNS);
            $sizeQuery->columns('SUM(IF(qty = 0, 1, qty))');
            $this->_itemsQty = $this->getConnection()->fetchOne($sizeQuery, $this->_bindParams);
        }

        return (int)$this->_itemsQty;
    }
}
