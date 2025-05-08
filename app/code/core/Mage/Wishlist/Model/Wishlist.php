<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Wishlist
 */

/**
 * Wishlist model
 *
 * @package    Mage_Wishlist
 *
 * @method Mage_Wishlist_Model_Resource_Wishlist _getResource()
 * @method Mage_Wishlist_Model_Resource_Wishlist getResource()
 * @method Mage_Wishlist_Model_Resource_Wishlist_Collection getCollection()
 *
 * @method int getShared()
 * @method $this setShared(int $value)
 * @method string getSharingCode()
 * @method $this setSharingCode(string $value)
 * @method string getUpdatedAt()
 * @method $this setUpdatedAt(string $value)
 * @method string getVisibility()
 */
class Mage_Wishlist_Model_Wishlist extends Mage_Core_Model_Abstract
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'wishlist';
    /**
     * Wishlist item collection
     *
     * @var Mage_Wishlist_Model_Resource_Item_Collection|null
     */
    protected $_itemCollection = null;

    /**
     * Store filter for wishlist
     *
     * @var Mage_Core_Model_Store|null
     */
    protected $_store = null;

    /**
     * Shared store ids (website stores)
     *
     * @var array|null
     */
    protected $_storeIds = null;

    /**
     * Entity cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'wishlist';

    /**
     * Initialize resource model
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
     * @return $this
     */
    public function loadByCustomer($customer, $create = false)
    {
        if ($customer instanceof Mage_Customer_Model_Customer) {
            $customer = $customer->getId();
        }

        $customer = (int) $customer;
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
     * Retrieve wishlist name
     *
     * @return string
     */
    public function getName()
    {
        $name = $this->_getData('name');
        if (!strlen($name)) {
            return Mage::helper('wishlist')->getDefaultWishlistName();
        }
        return $name;
    }

    /**
     * Set random sharing code
     *
     * @return $this
     */
    public function generateSharingCode()
    {
        $this->setSharingCode($this->_getSharingRandomCode());
        return $this;
    }

    /**
     * Load by sharing code
     *
     * @param string $code
     * @return $this
     */
    public function loadByCode($code)
    {
        $this->_getResource()->load($this, $code, 'sharing_code');
        if (!$this->getShared()) {
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
     * Set date of last update for wishlist
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $this->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());
        return $this;
    }

    /**
     * Save related items
     *
     * @return $this
     */
    protected function _afterSave()
    {
        parent::_afterSave();

        if ($this->_itemCollection !== null) {
            $this->getItemCollection()->save();
        }
        return $this;
    }

    /**
     * Add catalog product object data to wishlist
     *
     * @param   int $qty
     * @param   bool $forciblySetQty
     * @return  Mage_Wishlist_Model_Item
     */
    protected function _addCatalogProduct(Mage_Catalog_Model_Product $product, $qty = 1, $forciblySetQty = false)
    {
        $item = null;
        foreach ($this->getItemCollection() as $wishlistItem) {
            if ($wishlistItem->representProduct($product)) {
                $item = $wishlistItem;
                break;
            }
        }

        if ($item === null) {
            $storeId = $product->hasWishlistStoreId() ? $product->getWishlistStoreId() : $this->getStore()->getId();
            $item = Mage::getModel('wishlist/item');
            $item->setProductId($product->getId())
                ->setWishlistId($this->getId())
                ->setAddedAt(Varien_Date::now())
                ->setStoreId($storeId)
                ->setOptions($product->getCustomOptions())
                ->setProduct($product)
                ->setQty($qty)
                ->save();

            Mage::dispatchEvent('wishlist_item_add_after', ['wishlist' => $this]);

            if ($item->getId()) {
                $this->getItemCollection()->addItem($item);
            }
        } else {
            $qty = $forciblySetQty ? $qty : $item->getQty() + $qty;
            $item->setQty($qty)
                ->save();
        }

        $this->addItem($item);

        return $item;
    }

    /**
     * Retrieve wishlist item collection
     *
     * @return Mage_Wishlist_Model_Resource_Item_Collection
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getItemCollection()
    {
        if (is_null($this->_itemCollection)) {
            $currentWebsiteOnly = !Mage::app()->getStore()->isAdmin();
            $this->_itemCollection =  Mage::getResourceModel('wishlist/item_collection')
                ->addWishlistFilter($this)
                ->addStoreFilter($this->getSharedStoreIds($currentWebsiteOnly))
                ->setVisibilityFilter();

            if (Mage::app()->getStore()->isAdmin()) {
                $customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
                $this->_itemCollection->setWebsiteId($customer->getWebsiteId());
                $this->_itemCollection->setCustomerGroupId($customer->getGroupId());
            }
        }

        return $this->_itemCollection;
    }

    /**
     * Retrieve wishlist item collection
     *
     * @param int $itemId
     * @return Mage_Wishlist_Model_Item|false
     */
    public function getItem($itemId)
    {
        if (!$itemId) {
            return false;
        }
        return $this->getItemCollection()->getItemById($itemId);
    }

    /**
     * Retrieve Product collection
     *
     * @deprecated after 1.4.2.0
     * @see Mage_Wishlist_Model_Wishlist::getItemCollection()
     *
     * @return Mage_Wishlist_Model_Resource_Product_Collection
     */
    public function getProductCollection()
    {
        $collection = $this->getData('product_collection');
        if (is_null($collection)) {
            $collection = Mage::getResourceModel('wishlist/product_collection');
            $this->setData('product_collection', $collection);
        }
        return $collection;
    }

    /**
     * Adding item to wishlist
     *
     * @return  $this
     */
    public function addItem(Mage_Wishlist_Model_Item $item)
    {
        $item->setWishlist($this);
        if (!$item->getId()) {
            $this->getItemCollection()->addItem($item);
            Mage::dispatchEvent('wishlist_add_item', ['item' => $item]);
        }
        return $this;
    }

    /**
     * Adds new product to wishlist.
     * Returns new item or string on error.
     *
     * @param int|Mage_Catalog_Model_Product $product
     * @param mixed $buyRequest
     * @param bool $forciblySetQty
     * @return Mage_Wishlist_Model_Item|string
     */
    public function addNewItem($product, $buyRequest = null, $forciblySetQty = false)
    {
        /*
         * Always load product, to ensure:
         * a) we have new instance and do not interfere with other products in wishlist
         * b) product has full set of attributes
         */
        if ($product instanceof Mage_Catalog_Model_Product) {
            $productId = $product->getId();
            // Maybe force some store by wishlist internal properties
            $storeId = $product->hasWishlistStoreId() ? $product->getWishlistStoreId() : $product->getStoreId();
        } else {
            $productId = (int) $product;
            if ($buyRequest->getStoreId()) {
                $storeId = $buyRequest->getStoreId();
            } else {
                $storeId = Mage::app()->getStore()->getId();
            }
        }

        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product')
            ->setStoreId($storeId)
            ->load($productId);

        if ($buyRequest instanceof Varien_Object) {
            $request = $buyRequest;
        } elseif (is_string($buyRequest)) {
            $request = new Varien_Object(unserialize($buyRequest, ['allowed_classes' => false]));
        } elseif (is_array($buyRequest)) {
            $request = new Varien_Object($buyRequest);
        } else {
            $request = new Varien_Object();
        }

        $cartCandidates = $product->getTypeInstance(true)
            ->processConfiguration($request, $product);

        /**
         * Error message
         */
        if (is_string($cartCandidates)) {
            return $cartCandidates;
        }

        /**
         * If prepare process return one object
         */
        if (!is_array($cartCandidates)) {
            $cartCandidates = [$cartCandidates];
        }

        $errors = [];
        $items = [];
        $item = null;

        foreach ($cartCandidates as $candidate) {
            if ($candidate->getParentProductId()) {
                continue;
            }
            $candidate->setWishlistStoreId($storeId);

            $qty = $candidate->getQty() ? $candidate->getQty() : 1; // No null values as qty. Convert zero to 1.
            $item = $this->_addCatalogProduct($candidate, $qty, $forciblySetQty);
            $items[] = $item;

            // Collect errors instead of throwing first one
            if ($item->getHasError()) {
                $errors[] = $item->getMessage();
            }
        }

        Mage::dispatchEvent('wishlist_product_add_after', ['items' => $items]);

        return $item;
    }

    /**
     * Set customer id
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this->setData($this->_getResource()->getCustomerIdFieldName(), $customerId);
    }

    /**
     * Retrieve customer id
     *
     * @return int
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
        $data = [];
        $data[$this->_getResource()->getCustomerIdFieldName()] = $this->getCustomerId();
        $data['shared']      = (int) $this->getShared();
        $data['sharing_code'] = $this->getSharingCode();
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
        if (is_null($this->_storeIds) || !is_array($this->_storeIds)) {
            if ($current) {
                $this->_storeIds = $this->getStore()->getWebsite()->getStoreIds();
            } else {
                $storeIds = [];
                $stores = Mage::app()->getStores();
                foreach ($stores as $store) {
                    $storeIds[] = $store->getId();
                }
                $this->_storeIds = $storeIds;
            }
        }
        return $this->_storeIds;
    }

    /**
     * Set shared store ids
     *
     * @param array $storeIds
     * @return $this
     */
    public function setSharedStoreIds($storeIds)
    {
        $this->_storeIds = (array) $storeIds;
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
     * @return $this
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
        return $this->getItemCollection()->getSize();
    }

    /**
     * Retrieve wishlist has salable item(s)
     *
     * @return bool
     */
    public function isSalable()
    {
        foreach ($this->getItemCollection() as $item) {
            if ($item->getProduct()->getIsSalable()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check customer is owner this wishlist
     *
     * @param int $customerId
     * @return bool
     */
    public function isOwner($customerId)
    {
        return $customerId == $this->getCustomerId();
    }

    /**
     * Update wishlist Item and set data from request
     *
     * $params sets how current item configuration must be taken into account and additional options.
     * It's passed to Mage_Catalog_Helper_Product->addParamsToBuyRequest() to compose resulting buyRequest.
     *
     * Basically it can hold
     * - 'current_config', Varien_Object or array - current buyRequest that configures product in this item,
     *   used to restore currently attached files
     * - 'files_prefix': string[a-z0-9_] - prefix that was added at frontend to names of file options (file inputs), so they won't
     *   intersect with other submitted options
     *
     * For more options see Mage_Catalog_Helper_Product->addParamsToBuyRequest()
     *
     * @param int|Mage_Wishlist_Model_Item $itemId
     * @param Varien_Object $buyRequest
     * @param null|array|Varien_Object $params
     * @return $this
     *
     * @see Mage_Catalog_Helper_Product::addParamsToBuyRequest()
     */
    public function updateItem($itemId, $buyRequest, $params = null)
    {
        $item = null;
        if ($itemId instanceof Mage_Wishlist_Model_Item) {
            $item = $itemId;
        } else {
            $item = $this->getItem((int) $itemId);
        }
        if (!$item) {
            Mage::throwException(Mage::helper('wishlist')->__('Cannot specify wishlist item.'));
        }

        $product = $item->getProduct();
        $productId = $product->getId();
        if ($productId) {
            if (!$params) {
                $params = new Varien_Object();
            } elseif (is_array($params)) {
                $params = new Varien_Object($params);
            }
            $params->setCurrentConfig($item->getBuyRequest());
            $buyRequest = Mage::helper('catalog/product')->addParamsToBuyRequest($buyRequest, $params);

            $product->setWishlistStoreId($item->getStoreId());
            $wishlistItems = $this->getItemCollection();
            $isForceSetQuantity = true;
            foreach ($wishlistItems as $wishlistItem) {
                /** @var Mage_Wishlist_Model_Item $wishlistItem */
                if ($wishlistItem->getProductId() == $product->getId()
                    && $wishlistItem->representProduct($product)
                    && $wishlistItem->getId() != $item->getId()
                ) {
                    // We do not add new wishlist item, but updating the existing one
                    $isForceSetQuantity = false;
                }
            }
            $resultItem = $this->addNewItem($product, $buyRequest, $isForceSetQuantity);
            /**
             * Error message
             */
            if (is_string($resultItem)) {
                Mage::throwException(Mage::helper('checkout')->__($resultItem));
            }

            if ($resultItem->getId() != $itemId) {
                if ($resultItem->getDescription() != $item->getDescription()) {
                    $resultItem->setDescription($item->getDescription())->save();
                }
                $item->isDeleted(true);
                $this->setDataChanges(true);
            } else {
                $resultItem->setQty($buyRequest->getQty() * 1);
                $resultItem->setOrigData('qty', 0);
            }
        } else {
            Mage::throwException(Mage::helper('checkout')->__('The product does not exist.'));
        }
        return $this;
    }

    /**
     * Save wishlist.
     *
     * @inheritDoc
     */
    public function save()
    {
        $this->_hasDataChanges = true;
        return parent::save();
    }
}
