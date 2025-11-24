<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog Product Compare Helper
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Helper_Product_Compare extends Mage_Core_Helper_Url
{
    protected $_moduleName = 'Mage_Catalog';

    /**
     * Product Compare Items Collection
     *
     * @var Mage_Catalog_Model_Resource_Product_Compare_Item_Collection
     */
    protected $_itemCollection;

    /**
     * Product Comapare Items Collection has items flag
     *
     * @var bool
     */
    protected $_hasItems;

    /**
     * Allow used Flat catalog product for product compare items collection
     *
     * @var bool
     */
    protected $_allowUsedFlat = true;

    /**
     * Customer id
     *
     * @var null|int
     */
    protected $_customerId = null;

    /**
     * @var Mage_Log_Helper_Data
     */
    protected $_logCondition;

    /**
     * @var Mage_Catalog_Model_Session
     */
    protected $_catalogSession;

    /**
     * @var Mage_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @var Mage_Core_Model_Session
     */
    protected $_coreSession;

    /**
     * @var Mage_Log_Model_Visitor
     */
    protected $_logVisitor;

    /**
     * @var Mage_Catalog_Model_Product_Visibility
     */
    protected $_productVisibility;

    /**
     * Mage_Catalog_Helper_Product_Compare constructor.
     */
    public function __construct(array $data = [])
    {
        $this->_logCondition = $data['log_condition'] ?? Mage::helper('log');
        $this->_catalogSession = $data['catalog_session'] ?? Mage::getSingleton('catalog/session');
        $this->_customerSession = $data['customer_session'] ?? Mage::getSingleton('customer/session');
        $this->_coreSession = $data['core_session'] ?? Mage::getSingleton('core/session');
        $this->_productVisibility = $data['product_visibility'] ?? Mage::getSingleton('catalog/product_visibility');
        $this->_logVisitor = $data['log_visitor'] ?? Mage::getSingleton('log/visitor');
    }

    /**
     * Retrieve Catalog Session instance
     *
     * @return Mage_Catalog_Model_Session
     */
    protected function _getSession()
    {
        return $this->_catalogSession;
    }

    /**
     * Retrieve compare list url
     *
     * @return string
     */
    public function getListUrl()
    {
        $itemIds = [];
        foreach ($this->getItemCollection() as $item) {
            $itemIds[] = $item->getId();
        }

        $params = [
            'items' => implode(',', $itemIds),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl(),
        ];

        return $this->_getUrl('catalog/product_compare', $params);
    }

    /**
     * Get parameters used for build add product to compare list urls
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  array
     */
    protected function _getUrlParams($product)
    {
        return $this->_getUrlCustomParams($product);
    }

    /**
     * Retrieve url for adding product to conpare list
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  string
     */
    public function getAddUrl($product)
    {
        return $this->getAddUrlCustom($product);
    }

    /**
     * Retrieve add to wishlist url
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getAddToWishlistUrl($product)
    {
        return $this->getAddToWishlistUrlCustom($product);
    }

    /**
     * Retrieve add to cart url
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getAddToCartUrl($product)
    {
        return $this->getAddToCartUrlCustom($product);
    }

    /**
     * Retrieve remove item from compare list url
     *
     * @param   Mage_Catalog_Model_Product $item
     * @return  string
     */
    public function getRemoveUrl($item)
    {
        $params = [
            'product' => $item->getId(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl(),
        ];
        return $this->_getUrl('catalog/product_compare/remove', $params);
    }

    /**
     * Retrieve clear compare list url
     *
     * @return string
     */
    public function getClearListUrl()
    {
        $params = [
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl(),
        ];
        return $this->_getUrl('catalog/product_compare/clear', $params);
    }

    /**
     * Retrieve compare list items collection
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Compare_Item_Collection
     */
    public function getItemCollection()
    {
        if (!$this->_itemCollection) {
            $this->_itemCollection = Mage::getResourceModel('catalog/product_compare_item_collection')
                ->useProductItem()
                ->setStoreId(Mage::app()->getStore()->getId());

            if ($this->_customerSession->isLoggedIn()) {
                $this->_itemCollection->setCustomerId($this->_customerSession->getCustomerId());
            } elseif ($this->_customerId) {
                $this->_itemCollection->setCustomerId($this->_customerId);
            } else {
                $this->_itemCollection->setVisitorId($this->_logVisitor->getId());
            }

            $this->_productVisibility->addVisibleInSiteFilterToCollection($this->_itemCollection);

            /* Price data is added to consider item stock status using price index */
            $this->_itemCollection->addPriceData();

            $this->_itemCollection->addAttributeToSelect('name')
                ->addUrlRewrite()
                ->load();

            /* update compare items count */
            $this->_catalogSession->setCatalogCompareItemsCount(count($this->_itemCollection));
        }

        return $this->_itemCollection;
    }

    /**
     * Calculate cache product compare collection
     *
     * @param  bool $logout
     * @return $this
     */
    public function calculate($logout = false)
    {
        // first visit
        if (!$this->_catalogSession->hasCatalogCompareItemsCount() && !$this->_customerId) {
            $count = 0;
        } else {
            $collection = Mage::getResourceModel('catalog/product_compare_item_collection')
                ->useProductItem();
            if (!$logout && $this->_customerSession->isLoggedIn()) {
                $collection->setCustomerId($this->_customerSession->getCustomerId());
            } elseif ($this->_customerId) {
                $collection->setCustomerId($this->_customerId);
            } else {
                $collection->setVisitorId($this->_logVisitor->getId());
            }

            /* Price data is added to consider item stock status using price index */
            $collection->addPriceData();

            $this->_productVisibility->addVisibleInSiteFilterToCollection($collection);

            $count = $collection->getSize();
        }

        $this->_catalogSession->setCatalogCompareItemsCount($count);

        return $this;
    }

    /**
     * Retrieve count of items in compare list
     *
     * @return int
     */
    public function getItemCount()
    {
        if (!$this->_catalogSession->hasCatalogCompareItemsCount()) {
            $this->calculate();
        }

        return $this->_catalogSession->getCatalogCompareItemsCount();
    }

    /**
     * Check has items
     *
     * @return bool
     */
    public function hasItems()
    {
        return $this->getItemCount() > 0;
    }

    /**
     * Set is allow used flat (for collection)
     *
     * @param bool $flag
     * @return $this
     */
    public function setAllowUsedFlat($flag)
    {
        $this->_allowUsedFlat = (bool) $flag;
        return $this;
    }

    /**
     * Retrieve is allow used flat (for collection)
     *
     * @return bool
     */
    public function getAllowUsedFlat()
    {
        return $this->_allowUsedFlat;
    }

    /**
     * Setter for customer id
     *
     * @param int $id
     * @return $this
     */
    public function setCustomerId($id)
    {
        $this->_customerId = $id;
        return $this;
    }

    /**
     * Retrieve url for adding product to conpare list with or without Form Key
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool $addFormKey
     * @return string
     */
    public function getAddUrlCustom($product, $addFormKey = true)
    {
        if ($this->_logCondition->isVisitorLogEnabled() || $this->_customerSession->isLoggedIn()) {
            return $this->_getUrl('catalog/product_compare/add', $this->_getUrlCustomParams($product, $addFormKey));
        }

        return '';
    }

    /**
     * Retrieve add to wishlist url with or without Form Key
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool $addFormKey
     * @return string
     */
    public function getAddToWishlistUrlCustom($product, $addFormKey = true)
    {
        $beforeCompareUrl = $this->_catalogSession->getBeforeCompareUrl();
        $params = $this->_getUrlCustomParams($product, $addFormKey, $beforeCompareUrl);

        return $this->_getUrl('wishlist/index/add', $params);
    }

    /**
     * Retrieve add to cart url with or without Form Key
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool $addFormKey
     * @return string
     */
    public function getAddToCartUrlCustom($product, $addFormKey = true)
    {
        $beforeCompareUrl = $this->_catalogSession->getBeforeCompareUrl();
        $params = [
            'product' => $product->getId(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl($beforeCompareUrl),
        ];
        if ($addFormKey) {
            $params[Mage_Core_Model_Url::FORM_KEY] = $this->_coreSession->getFormKey();
        }

        return $this->_getUrl('checkout/cart/add', $params);
    }

    /**
     * Get parameters used for build add product to compare list urls with or without Form Key
     *
     * @param   Mage_Catalog_Model_Product $product
     * @param bool $addFormKey
     * @return  array
     */
    protected function _getUrlCustomParams($product, $addFormKey = true, $url = null)
    {
        $params = [
            'product' => $product->getId(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl($url),
        ];
        if ($addFormKey) {
            $params[Mage_Core_Model_Url::FORM_KEY] = $this->_coreSession->getFormKey();
        }

        return $params;
    }
}
