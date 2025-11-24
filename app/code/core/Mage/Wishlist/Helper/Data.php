<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Wishlist
 */

/**
 * @package    Mage_Wishlist
 */
class Mage_Wishlist_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Config key 'Display Wishlist Summary'
     */
    public const XML_PATH_WISHLIST_LINK_USE_QTY = 'wishlist/wishlist_link/use_qty';

    /**
     * Config key 'Display Out of Stock Products'
     */
    public const XML_PATH_CATALOGINVENTORY_SHOW_OUT_OF_STOCK = 'cataloginventory/options/show_out_of_stock';

    protected $_moduleName = 'Mage_Wishlist';

    /**
     * Currently logged in customer
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $_currentCustomer = null;

    /**
     * Customer Wishlist instance
     *
     * @var null|Mage_Wishlist_Model_Wishlist
     */
    protected $_wishlist = null;

    /**
     * Wishlist Product Items Collection
     *
     * @var null|Mage_Wishlist_Model_Resource_Product_Collection
     */
    protected $_productCollection = null;

    /**
     * Wishlist Items Collection
     *
     * @var null|Mage_Wishlist_Model_Resource_Item_Collection
     */
    protected $_wishlistItemCollection = null;

    /**
     * Retrieve customer session
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Retrieve customer login status
     *
     * @return bool
     */
    protected function _isCustomerLogIn()
    {
        return $this->_getCustomerSession()->isLoggedIn();
    }

    /**
     * Retrieve logged in customer
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function _getCurrentCustomer()
    {
        return $this->getCustomer();
    }

    /**
     * Set current customer
     */
    public function setCustomer(Mage_Customer_Model_Customer $customer)
    {
        $this->_currentCustomer = $customer;
    }

    /**
     * Retrieve current customer
     *
     * @return null|Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (!$this->_currentCustomer && $this->_getCustomerSession()->isLoggedIn()) {
            $this->_currentCustomer = $this->_getCustomerSession()->getCustomer();
        }

        return $this->_currentCustomer;
    }

    /**
     * Retrieve wishlist by logged in customer
     *
     * @return Mage_Wishlist_Model_Wishlist
     */
    public function getWishlist()
    {
        if (is_null($this->_wishlist)) {
            if (Mage::registry('shared_wishlist')) {
                $this->_wishlist = Mage::registry('shared_wishlist');
            } elseif (Mage::registry('wishlist')) {
                $this->_wishlist = Mage::registry('wishlist');
            } else {
                $this->_wishlist = Mage::getModel('wishlist/wishlist');
                if ($this->getCustomer()) {
                    $this->_wishlist->loadByCustomer($this->getCustomer());
                }
            }
        }

        return $this->_wishlist;
    }

    /**
     * Retrieve wishlist items availability
     *
     * @return bool
     * @deprecated after 1.6.0.0
     */
    public function hasItems()
    {
        return $this->getWishlist()->getItemsCount() > 0;
    }

    /**
     * Retrieve wishlist item count (include config settings)
     * Used in top link menu only
     *
     * @return int
     */
    public function getItemCount()
    {
        $storedDisplayType = $this->_getCustomerSession()->getWishlistDisplayType();
        $currentDisplayType = Mage::getStoreConfig(self::XML_PATH_WISHLIST_LINK_USE_QTY);

        $storedDisplayOutOfStockProducts = $this->_getCustomerSession()->getDisplayOutOfStockProducts();
        $currentDisplayOutOfStockProducts = Mage::getStoreConfig(self::XML_PATH_CATALOGINVENTORY_SHOW_OUT_OF_STOCK);
        if (!$this->_getCustomerSession()->hasWishlistItemCount()
                || ($currentDisplayType != $storedDisplayType)
                || $this->_getCustomerSession()->hasDisplayOutOfStockProducts()
                || ($currentDisplayOutOfStockProducts != $storedDisplayOutOfStockProducts)
        ) {
            $this->calculate();
        }

        return $this->_getCustomerSession()->getWishlistItemCount();
    }

    /**
     * Retrieve wishlist product items collection
     *
     * alias for getProductCollection
     *
     * @return Mage_Wishlist_Model_Resource_Product_Collection
     * @deprecated after 1.4.2.0
     * @see Mage_Wishlist_Model_Wishlist::getItemCollection()
     */
    public function getItemCollection()
    {
        return $this->getProductCollection();
    }

    /**
     * Create wishlist item collection
     *
     * @return Mage_Wishlist_Model_Resource_Item_Collection
     */
    protected function _createWishlistItemCollection()
    {
        return $this->getWishlist()->getItemCollection();
    }

    /**
     * Retrieve wishlist items collection
     *
     * @return Mage_Wishlist_Model_Resource_Item_Collection
     */
    public function getWishlistItemCollection()
    {
        if (is_null($this->_wishlistItemCollection)) {
            $this->_wishlistItemCollection = $this->_createWishlistItemCollection();
        }

        return $this->_wishlistItemCollection;
    }

    /**
     * Retrieve wishlist product items collection
     *
     * @return Mage_Wishlist_Model_Resource_Product_Collection
     * @deprecated after 1.4.2.0
     * @see Mage_Wishlist_Model_Wishlist::getItemCollection()
     */
    public function getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $this->_productCollection = $this->getWishlist()
                ->getProductCollection();

            Mage::getSingleton('catalog/product_visibility')
                ->addVisibleInSiteFilterToCollection($this->_productCollection);
        }

        return $this->_productCollection;
    }

    /**
     * Retrieve Item Store for URL
     *
     * @param Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item $item
     * @return Mage_Core_Model_Store
     */
    protected function _getUrlStore($item)
    {
        $storeId = null;
        $product = null;
        if ($item instanceof Mage_Wishlist_Model_Item) {
            $product = $item->getProduct();
        } elseif ($item instanceof Mage_Catalog_Model_Product) {
            $product = $item;
        }

        if ($product) {
            if ($product->isVisibleInSiteVisibility()) {
                $storeId = $product->getStoreId();
            } elseif ($product->hasUrlDataObject()) {
                $storeId = $product->getUrlDataObject()->getStoreId();
            }
        }

        return Mage::app()->getStore($storeId);
    }

    /**
     * Retrieve URL for removing item from wishlist
     *
     * @param Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item $item
     * @return string
     */
    public function getRemoveUrl($item)
    {
        return $this->getRemoveUrlCustom($item);
    }

    /**
     * Retrieve URL for removing item from wishlist
     *
     * @param Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item $item
     * @return string
     */
    public function getConfigureUrl($item)
    {
        return $this->_getUrl('wishlist/index/configure', [
            'item' => $item->getWishlistItemId(),
        ]);
    }

    /**
     * Retrieve url for adding product to wishlist
     *
     * @param Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item $item
     *
     * @return  bool|string
     */
    public function getAddUrl($item)
    {
        return $this->getAddUrlWithParams($item);
    }

    /**
     * Retrieve url for adding product to wishlist
     *
     * @param int $itemId
     *
     * @return  string
     */
    public function getMoveFromCartUrl($itemId)
    {
        return $this->_getUrl('wishlist/index/fromcart', ['item' => $itemId]);
    }

    /**
     * Retrieve url for updating product in wishlist
     *
     * @param Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item $item
     *
     * @return  bool|string
     */
    public function getUpdateUrl($item)
    {
        $itemId = null;
        if ($item instanceof Mage_Catalog_Model_Product) {
            $itemId = $item->getWishlistItemId();
        }

        if ($item instanceof Mage_Wishlist_Model_Item) {
            $itemId = $item->getId();
        }

        if ($itemId) {
            return $this->_getUrl('wishlist/index/updateItemOptions', ['id' => $itemId]);
        }

        return false;
    }

    /**
     * Retrieve url for adding product to wishlist with params
     *
     * @param Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item $item
     *
     * @return  bool|string
     */
    public function getAddUrlWithParams($item, array $params = [])
    {
        return $this->getAddUrlWithCustomParams($item, $params);
    }

    /**
     * Retrieve URL for adding item to shopping cart
     *
     * @param Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item|string $item
     * @return  string
     */
    public function getAddToCartUrl($item)
    {
        return $this->getAddToCartUrlCustom($item);
    }

    /**
     * Return helper instance
     *
     * @param string $helperName
     * @return Mage_Core_Helper_Abstract
     */
    protected function _getHelperInstance($helperName)
    {
        return Mage::helper($helperName);
    }

    /**
     * Return model instance
     *
     * @param string $className
     * @param array $arguments
     * @return Mage_Core_Model_Abstract
     */
    protected function _getSingletonModel($className, $arguments = [])
    {
        return Mage::getSingleton($className, $arguments);
    }

    /**
     * Retrieve URL for adding item to shoping cart from shared wishlist
     *
     * @param Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item|string $item
     * @return  string
     */
    public function getSharedAddToCartUrl($item)
    {
        $continueUrl  = Mage::helper('core')->urlEncode(Mage::getUrl('*/*/*', [
            '_current'      => true,
            '_use_rewrite'  => true,
            '_store_to_url' => true,
        ]));

        $params = [
            'item' => is_string($item) ? $item : $item->getWishlistItemId(),
            'code' => $this->getWishlist()->getSharingCode(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $continueUrl,
        ];
        return $this->_getUrlStore($item)->getUrl('wishlist/shared/cart', $params);
    }

    /**
     * Retrieve url for adding item to shoping cart with b64 referer
     *
     * @param   Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item $item
     * @return  string
     * @deprecated
     */
    public function getAddToCartUrlBase64($item)
    {
        return $this->getAddToCartUrl($item);
    }

    /**
     * Retrieve customer wishlist url
     *
     * @param null|int $wishlistId
     * @return string
     */
    public function getListUrl($wishlistId = null)
    {
        $params = [];
        if ($wishlistId) {
            $params['wishlist_id'] = $wishlistId;
        }

        return $this->_getUrl('wishlist', $params);
    }

    /**
     * Check is allow wishlist module
     *
     * @return bool
     */
    public function isAllow()
    {
        if ($this->isModuleOutputEnabled() && Mage::getStoreConfig('wishlist/general/active')) {
            return true;
        }

        return false;
    }

    /**
     * Check is allow wishlist action in shopping cart
     *
     * @return bool
     */
    public function isAllowInCart()
    {
        return $this->isAllow() && $this->getCustomer();
    }

    /**
     * Retrieve customer name
     *
     * @return null|string
     */
    public function getCustomerName()
    {
        $customer = $this->_getCurrentCustomer();
        if ($customer) {
            return $customer->getName();
        }

        return null;
    }

    /**
     * Retrieve RSS URL
     *
     * @param int $wishlistId
     * @return string
     */
    public function getRssUrl($wishlistId = null)
    {
        $params = [];
        $customer = $this->_getCurrentCustomer();
        if ($customer) {
            $key = $customer->getId() . ',' . $customer->getEmail();
            $params = [
                'data' => Mage::helper('core')->urlEncode($key),
                '_secure' => false,
            ];
        }

        if ($wishlistId) {
            $params['wishlist_id'] = $wishlistId;
        }

        return $this->_getUrl(
            'rss/index/wishlist',
            $params,
        );
    }

    /**
     * Is allow RSS
     *
     * @return bool
     */
    public function isRssAllow()
    {
        return Mage::getStoreConfigFlag('rss/wishlist/active');
    }

    /**
     * Retrieve default empty comment message
     *
     * @return string
     */
    public function defaultCommentString()
    {
        return $this->__('Please, enter your comments...');
    }

    /**
     * Retrieve default empty comment message
     *
     * @return string
     */
    public function getDefaultWishlistName()
    {
        return $this->__('Wishlist');
    }

    /**
     * Calculate count of wishlist items and put value to customer session.
     * Method called after wishlist modifications and trigger 'wishlist_items_renewed' event.
     * Depends from configuration.
     *
     * @return $this
     */
    public function calculate()
    {
        $session = $this->_getCustomerSession();
        $count = 0;
        if ($this->getCustomer()) {
            $collection = $this->getWishlistItemCollection()->setInStockFilter(true);
            if (Mage::getStoreConfig(self::XML_PATH_WISHLIST_LINK_USE_QTY)) {
                $count = $collection->getItemsQty();
            } else {
                $count = $collection->getSize();
            }

            $session->setWishlistDisplayType(Mage::getStoreConfig(self::XML_PATH_WISHLIST_LINK_USE_QTY));
            $session->setDisplayOutOfStockProducts(
                Mage::getStoreConfig(self::XML_PATH_CATALOGINVENTORY_SHOW_OUT_OF_STOCK),
            );
        }

        $session->setWishlistItemCount($count);
        Mage::dispatchEvent('wishlist_items_renewed');
        return $this;
    }

    /**
     * Should display item quantities in my wishlist link
     *
     * @return bool
     */
    public function isDisplayQty()
    {
        return Mage::getStoreConfig(self::XML_PATH_WISHLIST_LINK_USE_QTY);
    }

    /**
     * Retrieve url for adding product to wishlist with params with or without Form Key
     *
     * @param Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item $item
     * @param bool $addFormKey
     * @return bool|string
     */
    public function getAddUrlWithCustomParams($item, array $params = [], $addFormKey = true)
    {
        $productId = null;
        if ($item instanceof Mage_Catalog_Model_Product) {
            $productId = $item->getEntityId();
        }

        if ($item instanceof Mage_Wishlist_Model_Item) {
            $productId = $item->getProductId();
        }

        if ($productId) {
            $params['product'] = $productId;
            if ($addFormKey) {
                $params[Mage_Core_Model_Url::FORM_KEY] = $this->_getSingletonModel('core/session')->getFormKey();
            }

            return $this->_getUrlStore($item)->getUrl('wishlist/index/add', $params);
        }

        return false;
    }

    /**
     * Retrieve URL for removing item from wishlist with params with or without Form Key
     *
     * @param Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item $item
     * @param bool $addFormKey
     * @return string
     */
    public function getRemoveUrlCustom($item, $addFormKey = true)
    {
        $params = [
            'item' => $item->getWishlistItemId(),
        ];
        if ($addFormKey) {
            $params[Mage_Core_Model_Url::FORM_KEY] = $this->_getSingletonModel('core/session')->getFormKey();
        }

        return $this->_getUrl('wishlist/index/remove', $params);
    }

    /**
     * Retrieve URL for adding item to shopping cart with or without Form Key
     *
     * @param Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item|string $item
     * @param bool $addFormKey
     * @return  string
     */
    public function getAddToCartUrlCustom($item, $addFormKey = true)
    {
        $continueUrl  = $this->_getHelperInstance('core')->urlEncode(
            $this->_getUrl('*/*/*', [
                '_current'      => true,
                '_use_rewrite'  => true,
                '_store_to_url' => true,
            ]),
        );
        $params = [
            'item' => is_string($item) ? $item : $item->getWishlistItemId(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $continueUrl,
        ];
        if ($addFormKey) {
            $params[Mage_Core_Model_Url::FORM_KEY] = $this->_getSingletonModel('core/session')->getFormKey();
        }

        return $this->_getUrlStore($item)->getUrl('wishlist/index/cart', $params);
    }
}
