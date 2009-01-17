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
 * @category   Mage
 * @package    Mage_Wishlist
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Wishlist model
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Model_Wishlist extends Mage_Core_Model_Abstract
{

    protected $_itemCollection = null;

    /**
     * Enter description here...
     *
     * @var Mage_Core_Model_Store
     */
    protected $_store = null;

    protected $_storeIds = null;

    protected function _construct()
    {
        $this->_init('wishlist/wishlist');
    }

    public function loadByCustomer($customer, $create=false)
    {
        if ($customer instanceof Mage_Customer_Model_Customer) {
            $customer = $customer->getId();
        }

        $this->_getResource()->load($this,
            $customer,
            $this->_getResource()->getCustomerIdFieldName());
        if(!$this->getId() && $create) {
            $this->setCustomerId($customer);
            $this->setSharingCode($this->_getSharingRandomCode());
            $this->save();
        }

        return $this;
    }

    public function loadByCode($code)
    {
        $this->_getResource()->load($this, $code, 'sharing_code');
        if(!$this->getShared()) {
            $this->setId(null);
        }
        return $this;
    }

    protected function _getSharingRandomCode()
    {
        return md5(microtime() . rand());
    }

    public function getItemCollection()
    {
        if(is_null($this->_itemCollection)) {
            $this->_itemCollection =  Mage::getResourceModel('wishlist/item_collection')
                ->setStoreId($this->getStore()->getId())
                ->addWishlistFilter($this);
        }

        return $this->_itemCollection;
    }

    public function getProductCollection()
    {
        $collection = $this->getData('product_collection');
        if (is_null($collection)) {
            $collection = Mage::getResourceModel('wishlist/product_collection')
                ->setStoreId($this->getStore()->getId())
                ->addWishlistFilter($this)
                ->addWishListSortOrder()
	        ;
            $this->setData('product_collection', $collection);
        }
        return $collection;
    }

    public function addNewItem($productId)
    {
        $item = Mage::getModel('wishlist/item');

        $item->loadByProductWishlist($this->getId(), $productId, $this->getSharedStoreIds());

        if($item->getId()) {
            return $item;
        }

        $item->setProductId($productId)
            ->setWishlistId($this->getId())
            ->setAddedAt(now())
            ->setStoreId($this->getStore()->getId())
            ->save();

        return $item;
    }

    public function setCustomerId($customerId)
    {
        return $this->setData($this->_getResource()->getCustomerIdFieldName(), $customerId);
    }

    public function getCustomerId()
    {
        return $this->getData($this->_getResource()->getCustomerIdFieldName());
    }

    public function getDataForSave()
    {
        $data = array();
        $data[$this->_getResource()->getCustomerIdFieldName()] = $this->getCustomerId();
        $data['shared']      = (int) $this->getShared();
        $data['sharing_code']= $this->getSharingCode();
        return $data;
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public function getSharedStoreIds()
    {
        if (is_null($this->_storeIds)) {
            $this->_storeIds = Mage::app()->getStore()->getWebsite()->getStoreIds();
        }
        return $this->_storeIds;
    }

    /**
     * Set store ids
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
     * Enter description here...
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
     * Enter description here...
     *
     * @param Mage_Core_Model_Store $store
     * @return Mage_Wishlist_Model_Wishlist
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    public function getItemsCount()
    {
        return $this->_getResource()->fetchItemsCount($this);
    }

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
