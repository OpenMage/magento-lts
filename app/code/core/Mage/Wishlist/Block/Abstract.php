<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Wishlist
 */

/**
 * Wishlist Product Items abstract Block
 *
 * @package    Mage_Wishlist
 */
abstract class Mage_Wishlist_Block_Abstract extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * Wishlist Product Items Collection
     *
     * @var Mage_Wishlist_Model_Resource_Item_Collection|null
     */
    protected $_collection;

    /**
     * Store wishlist Model
     *
     * @var Mage_Wishlist_Model_Wishlist|null
     */
    protected $_wishlist;

    /**
     * List of block settings to render prices for different product types
     *
     * @var array
     */
    protected $_itemPriceBlockTypes = [];

    /**
     * List of block instances to render prices for different product types
     *
     * @var array
     */
    protected $_cachedItemPriceBlocks = [];

    /**
     * Internal constructor, that is called from real constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addItemPriceBlockType('default', 'wishlist/render_item_price', 'wishlist/render/item/price.phtml');
    }

    /**
     * Retrieve Wishlist Data Helper
     *
     * @return Mage_Wishlist_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('wishlist');
    }

    /**
     * Retrieve Customer Session instance
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Retrieve Wishlist model
     *
     * @return Mage_Wishlist_Model_Wishlist
     */
    protected function _getWishlist()
    {
        return $this->_getHelper()->getWishlist();
    }

    /**
     * Prepare additional conditions to collection
     *
     * @param Mage_Wishlist_Model_Resource_Item_Collection $collection
     * @return $this
     */
    protected function _prepareCollection($collection)
    {
        return $this;
    }

    /**
     * Create wishlist item collection
     *
     * @return Mage_Wishlist_Model_Resource_Item_Collection
     */
    protected function _createWishlistItemCollection()
    {
        return $this->_getWishlist()->getItemCollection();
    }

    /**
     * Retrieve Wishlist Product Items collection
     *
     * @return Mage_Wishlist_Model_Resource_Item_Collection
     */
    public function getWishlistItems()
    {
        if (is_null($this->_collection)) {
            $this->_collection = $this->_createWishlistItemCollection();
            $this->_prepareCollection($this->_collection);
        }

        return $this->_collection;
    }

    /**
     * Retrieve wishlist instance
     *
     * @return Mage_Wishlist_Model_Wishlist
     */
    public function getWishlistInstance()
    {
        return $this->_getWishlist();
    }

    /**
     * Back compatibility retrieve wishlist product items
     *
     * @return Mage_Wishlist_Model_Resource_Item_Collection
     * @deprecated after 1.4.2.0
     */
    public function getWishlist()
    {
        return $this->getWishlistItems();
    }

    /**
     * Retrieve URL for Removing item from wishlist
     *
     * @param Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item $item
     *
     * @return string
     */
    public function getItemRemoveUrl($item)
    {
        return $this->getItemRemoveUrlCustom($item);
    }

    /**
     * Retrieve Add Item to shopping cart URL
     *
     * @param string|Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item $item
     * @return string
     */
    public function getItemAddToCartUrl($item)
    {
        return $this->getItemAddToCartUrlCustom($item);
    }

    /**
     * Retrieve Add Item to shopping cart URL from shared wishlist
     *
     * @param string|Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item $item
     * @return string
     */
    public function getSharedItemAddToCartUrl($item)
    {
        return $this->_getHelper()->getSharedAddToCartUrl($item);
    }

    /**
     * Retrieve URL for adding Product to wishlist
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getAddToWishlistUrl($product)
    {
        return $this->getAddToWishlistUrlCustom($product);
    }

    /**
     * Returns item configure url in wishlist
     *
     * @param Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item $product
     *
     * @return string
     */
    public function getItemConfigureUrl($product)
    {
        if ($product instanceof Mage_Catalog_Model_Product) {
            $id = $product->getWishlistItemId();
        } else {
            $id = $product->getId();
        }

        $params = ['id' => $id];

        return $this->getUrl('wishlist/index/configure/', $params);
    }

    /**
     * Retrieve Escaped Description for Wishlist Item
     *
     * @param Mage_Wishlist_Model_Item $item
     * @return string
     */
    public function getEscapedDescription($item)
    {
        if ($item->getDescription()) {
            return $this->escapeHtml($item->getDescription());
        }

        return '&nbsp;';
    }

    /**
     * Check Wishlist item has description
     *
     * @param Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item $item
     * @return bool
     */
    public function hasDescription($item)
    {
        return trim($item->getDescription() ?? '') != '';
    }

    /**
     * Retrieve formatted Date
     *
     * @param string $date
     * @return string
     */
    public function getFormatedDate($date)
    {
        return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
    }

    /**
     * Check is the wishlist has a salable product(s)
     *
     * @return bool
     */
    public function isSaleable()
    {
        foreach ($this->getWishlistItems() as $item) {
            if ($item->getProduct()->isSaleable()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve wishlist loaded items count
     *
     * @return int
     */
    public function getWishlistItemsCount()
    {
        return $this->_getWishlist()->getItemsCount();
    }

    /**
     * Retrieve Qty from item
     *
     * @param Mage_Wishlist_Model_Item|Mage_Catalog_Model_Product $item
     * @return float
     */
    public function getQty($item)
    {
        $qty = $item->getQty() * 1;
        if (!$qty) {
            $qty = 1;
        }

        return $qty;
    }

    /**
     * Check is the wishlist has items
     *
     * @return bool
     */
    public function hasWishlistItems()
    {
        return $this->getWishlistItemsCount() > 0;
    }

    /**
     * Adds special block to render price for item with specific product type
     *
     * @param string $type
     * @param string $block
     * @param string $template
     */
    public function addItemPriceBlockType($type, $block = '', $template = '')
    {
        if ($type) {
            $this->_itemPriceBlockTypes[$type] = [
                'block' => $block,
                'template' => $template,
            ];
        }
    }

    /**
     * Returns block to render item with some product type
     *
     * @param string $productType
     * @return Mage_Core_Block_Template
     */
    protected function _getItemPriceBlock($productType)
    {
        if (!isset($this->_itemPriceBlockTypes[$productType])) {
            $productType = 'default';
        }

        if (!isset($this->_cachedItemPriceBlocks[$productType])) {
            $blockType = $this->_itemPriceBlockTypes[$productType]['block'];
            $template = $this->_itemPriceBlockTypes[$productType]['template'];
            $block = $this->getLayout()->createBlock($blockType)
                ->setTemplate($template);
            $this->_cachedItemPriceBlocks[$productType] = $block;
        }

        return $this->_cachedItemPriceBlocks[$productType];
    }

    /**
     * Returns product price block html
     * Overwrites parent price html return to be ready to show configured, partially configured and
     * non-configured products
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool $displayMinimalPrice
     * @param string $idSuffix
     *
     * @return string
     */
    public function getPriceHtml($product, $displayMinimalPrice = false, $idSuffix = '')
    {
        $typeId = $product->getTypeId();
        if (Mage::helper('catalog')->canApplyMsrp($product)) {
            $realPriceHtml = $this->_preparePriceRenderer($typeId)
                ->setProduct($product)
                ->setDisplayMinimalPrice($displayMinimalPrice)
                ->setIdSuffix($idSuffix)
                ->setIsEmulateMode(true)
                ->toHtml();
            $product->setAddToCartUrl($this->getAddToCartUrl($product));
            $product->setRealPriceHtml($realPriceHtml);
            $typeId = $this->_mapRenderer;
        }

        return $this->_preparePriceRenderer($typeId)
            ->setProduct($product)
            ->setDisplayMinimalPrice($displayMinimalPrice)
            ->setIdSuffix($idSuffix)
            ->toHtml();
    }

    /**
     * Retrieve URL to item Product
     *
     * @param  Mage_Wishlist_Model_Item|Mage_Catalog_Model_Product $item
     * @param  array $additional
     * @return string
     */
    public function getProductUrl($item, $additional = [])
    {
        if ($item instanceof Mage_Catalog_Model_Product) {
            $product = $item;
        } else {
            $product = $item->getProduct();
        }

        $buyRequest = $item->getBuyRequest();
        if (is_object($buyRequest)) {
            $config = $buyRequest->getSuperProductConfig();
            if ($config && !empty($config['product_id'])) {
                $product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getStoreId())
                    ->load($config['product_id']);
            }
        }

        return parent::getProductUrl($product, $additional);
    }

    /**
     * Retrieve URL for adding Product to wishlist with or without Form Key
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool $addFormKey
     * @return string
     */
    public function getAddToWishlistUrlCustom($product, $addFormKey = true)
    {
        if (!$addFormKey) {
            return $this->_getHelper()->getAddUrlWithCustomParams($product, [], false);
        }

        return $this->_getHelper()->getAddUrl($product);
    }

    /**
     * Retrieve URL for Removing item from wishlist with or without Form Key
     *
     * @param Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item $item
     * @param bool $addFormKey
     * @return string
     */
    public function getItemRemoveUrlCustom($item, $addFormKey = true)
    {
        if (!$addFormKey) {
            return $this->_getHelper()->getRemoveUrlCustom($item, false);
        }

        return $this->_getHelper()->getRemoveUrl($item);
    }

    /**
     * Retrieve Add Item to shopping cart URL with or without Form Key
     *
     * @param string|Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item $item
     * @param bool $addFormKey
     * @return string
     */
    public function getItemAddToCartUrlCustom($item, $addFormKey = true)
    {
        if (!$addFormKey) {
            return $this->_getHelper()->getAddToCartUrlCustom($item, false);
        }

        return $this->_getHelper()->getAddToCartUrl($item);
    }
}
