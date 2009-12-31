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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Wishlist model
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Model_Wishlist extends Mage_Core_Model_Abstract
{
    /**
     * Wishlist item collection
     *
     * @var Mage_Wishlist_Model_Mysql4_Item_Collection
     */
    protected $_itemCollection = null;

    /**
     * Store filter for wishlist
     *
     * @var Mage_Core_Model_Store
     */
    protected $_store = null;

    /**
     * Shared store ids (website stores)
     *
     * @var array
     */
    protected $_storeIds = null;

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('wishlist/wishlist');
    }

    /**
     * Load wishlist by customer
     *
     * @param mixed $customer
     * @param bool $create Create wishlist if don't exists
     * @return Mage_Wishlist_Model_Wishlist
     */
    public function loadByCustomer($customer, $create = false)
    {
        if ($customer instanceof Mage_Customer_Model_Customer) {
            $customer = $customer->getId();
        }

        $customerIdFieldName = $this->_getResource()->getCustomerIdFieldName();
        $this->_getResource()->load($this, $customer, $customerIdFieldName);
        if (!$this->getId() && $create) {
            $this->setCustomerId($customer);
            $this->setSharingCode($this->_getSharingRandomCode());
            $this->save();
        }

        return $this;
    }

    /**
     * Load by sharing code
     *
     * @param string $code
     * @return Mage_Wishlist_Model_Wishlist
     */
    public function loadByCode($code)
    {
        $this->_getResource()->load($this, $code, 'sharing_code');
        if(!$this->getShared()) {
            $this->setId(null);
        }
        return $this;
    }

    /**
     * Retrieve sharing code (random string)
     *
     * @return string
     */
    protected function _getSharingRandomCode()
    {
        return Mage::helper('core')->uniqHash();
    }

    /**
     * Retrieve wishlist item collection
     *
     * @return Mage_Wishlist_Model_Mysql4_Item_Collection
     */
    public function getItemCollection()
    {
        if(is_null($this->_itemCollection)) {
            $this->_itemCollection =  Mage::getResourceModel('wishlist/item_collection')
                ->setStoreId($this->getStore()->getId())
                ->addWishlistFilter($this);
        }

        return $this->_itemCollection;
    }

    /**
     * Retrieve Product collection
     *
     * @return Mage_Wishlist_Model_Mysql4_Product_Collection
     */
    public function getProductCollection()
    {
        $collection = $this->getData('product_collection');
        if (is_null($collection)) {
            $collection = Mage::getResourceModel('wishlist/product_collection')
                ->setStoreId($this->getStore()->getId())
                ->addWishlistFilter($this)
                ->addWishListSortOrder();
            $this->setData('product_collection', $collection);
        }
        return $collection;
    }

    /**
     * Add new item to wishlist
     *
     * @param int $productId
     * @return Mage_Wishlist_Model_Item
     */
    public function addNewItem($productId)
    {
        $item = Mage::getModel('wishlist/item');
        $item->loadByProductWishlist($this->getId(), $productId, $this->getSharedStoreIds());

        if (!$item->getId()) {
            $item->setProductId($productId)
                ->setWishlistId($this->getId())
                ->setAddedAt(now())
                ->setStoreId($this->getStore()->getId())
                ->save();
        }

        return $item;
    }

    /**
     * Set customer id
     *
     * @param int $customerId
     * @return Mage_Wishlist_Model_Wishlist
     */
    public function setCustomerId($customerId)
    {
        return $this->setData($this->_getResource()->getCustomerIdFieldName(), $customerId);
    }

    /**
     * Retrieve customer id
     *
     * @return Mage_Wishlist_Model_Wishlist
     */
    public function getCustomerId()
    {
        return $this->getData($this->_getResource()->getCustomerIdFieldName());
    }

    /**
     * Retrieve data for save
     *
     * @return array
     */
    public function getDataForSave()
    {
        $data = array();
        $data[$this->_getResource()->getCustomerIdFieldName()] = $this->getCustomerId();
        $data['shared']      = (int) $this->getShared();
        $data['sharing_code']= $this->getSharingCode();
        return $data;
    }

    /**
     * Retrieve shared store ids for current website or all stores if $current is false
     *
     * @param bool $current Use current website or not
     * @return array
     */
    public function getSharedStoreIds($current = true)
    {
        if (is_null($this->_storeIds)) {
            if ($current) {
                $this->_storeIds = $this->getStore()->getWebsite()->getStoreIds();
            } else {
                $_storeIds = array();
                foreach (Mage::app()->getStores() as $store) {
                    $_storeIds[] = $store->getId();
                }
                $this->_storeIds = $_storeIds;
            }
        }
        return $this->_storeIds;
    }

    /**
     * Set shared store ids
     *
     * @param array $storeIds
     * @return Mage_Wishlist_Model_Wishlist
     */
    public function setSharedStoreIds($storeIds)
    {
        $this->_storeIds = $storeIds;
        return $this;
    }

    /**
     * Retrieve wishlist store object
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if (is_null($this->_store)) {
            $this->setStore(Mage::app()->getStore());
        }
        return $this->_store;
    }

    /**
     * Set wishlist store
     *
     * @param Mage_Core_Model_Store $store
     * @return Mage_Wishlist_Model_Wishlist
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * Retrieve wishlist items count
     *
     * @return int
     */
    public function getItemsCount()
    {
        return $this->_getResource()->fetchItemsCount($this);
    }

    /**
     * Retrieve wishlist has salable item(s)
     *
     * @return bool
     */
    public function isSalable()
    {
        foreach ($this->getProductCollection() as $product) {
            if ($product->getIsSalable()) {
                return true;
            }
        }
        return false;
    }
}
