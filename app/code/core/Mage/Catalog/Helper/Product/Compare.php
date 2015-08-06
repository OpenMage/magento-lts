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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog Product Compare Helper
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Helper_Product_Compare extends Mage_Core_Helper_Url
{
    /**
     * Product Compare Items Collection
     *
     * @var Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Compare_Item_Collection
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

    public function __construct(array $data = array())
    {
        $this->_logCondition = isset($data['log_condition'])
            ? $data['log_condition'] : Mage::helper('log');
        $this->_catalogSession = isset($data['catalog_session'])
            ? $data['catalog_session'] : Mage::getSingleton('catalog/session');
        $this->_customerSession = isset($data['customer_session'])
            ? $data['customer_session'] : Mage::getSingleton('customer/session');
        $this->_coreSession = isset($data['core_session'])
            ? $data['core_session'] :  Mage::getSingleton('core/session');
        $this->_productVisibility = isset($data['product_visibility'])
            ? $data['product_visibility'] : Mage::getSingleton('catalog/product_visibility');
        $this->_logVisitor = isset($data['log_visitor'])
            ? $data['log_visitor'] : Mage::getSingleton('log/visitor');
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
        $itemIds = array();
        foreach ($this->getItemCollection() as $item) {
            $itemIds[] = $item->getId();
        }

         $params = array(
            'items' => implode(',', $itemIds),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
        );

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
        return array(
            'product' => $product->getId(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl(),
            Mage_Core_Model_Url::FORM_KEY => $this->_coreSession->getFormKey()
        );
    }

    /**
     * Retrieve url for adding product to conpare list
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  string
     */
    public function getAddUrl($product)
    {
        if ($this->_logCondition->isVisitorLogEnabled() || $this->_customerSession->isLoggedIn()) {
            return $this->_getUrl('catalog/product_compare/add', $this->_getUrlParams($product));
        }
        return '';
    }

    /**
     * Retrive add to wishlist url
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getAddToWishlistUrl($product)
    {
        $beforeCompareUrl = $this->_catalogSession->getBeforeCompareUrl();

        $params = array(
            'product' => $product->getId(),
            Mage_Core_Model_Url::FORM_KEY => $this->_coreSession->getFormKey(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl($beforeCompareUrl)
        );

        return $this->_getUrl('wishlist/index/add', $params);
    }

    /**
     * Retrive add to cart url
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getAddToCartUrl($product)
    {
        $beforeCompareUrl = $this->_catalogSession->getBeforeCompareUrl();
        $params = array(
            'product' => $product->getId(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl($beforeCompareUrl),
            Mage_Core_Model_Url::FORM_KEY => $this->_coreSession->getFormKey()
        );

        return $this->_getUrl('checkout/cart/add', $params);
    }

    /**
     * Retrieve remove item from compare list url
     *
     * @param   $item
     * @return  string
     */
    public function getRemoveUrl($item)
    {
        $params = array(
            'product' => $item->getId(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
        );
        return $this->_getUrl('catalog/product_compare/remove', $params);
    }

    /**
     * Retrieve clear compare list url
     *
     * @return string
     */
    public function getClearListUrl()
    {
        $params = array(
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
        );
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
            /** @var Mage_Catalog_Model_Resource_Product_Compare_Item_Collection _itemCollection */
            $this->_itemCollection = Mage::getResourceModel('catalog/product_compare_item_collection')
                ->useProductItem(true)
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
     * @return Mage_Catalog_Helper_Product_Compare
     */
    public function calculate($logout = false)
    {
        // first visit
        if (!$this->_catalogSession->hasCatalogCompareItemsCount() && !$this->_customerId) {
            $count = 0;
        } else {
            /** @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Compare_Item_Collection */
            $collection = Mage::getResourceModel('catalog/product_compare_item_collection')
                ->useProductItem(true);
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
     * @return Mage_Catalog_Helper_Product_Compare
     */
    public function setAllowUsedFlat($flag)
    {
        $this->_allowUsedFlat = (bool)$flag;
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
     * @return Mage_Catalog_Helper_Product_Compare
     */
    public function setCustomerId($id)
    {
        $this->_customerId = $id;
        return $this;
    }
}
