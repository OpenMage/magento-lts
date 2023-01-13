<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2018-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Product Abstract Block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Catalog_Block_Product_Abstract extends Mage_Core_Block_Template
{
    /**
     * Price block array
     *
     * @var array
     */
    protected $_priceBlock = [];

    /**
     * Default price block
     *
     * @var string
     */
    protected $_block = 'catalog/product_price';

    /**
     * Price template
     *
     * @var string
     */
    protected $_priceBlockDefaultTemplate = 'catalog/product/price.phtml';

    /**
     * Tier price template
     *
     * @var string
     */
    protected $_tierPriceDefaultTemplate = 'catalog/product/view/tierprices.phtml';

    /**
     * Price types
     *
     * @var array
     */
    protected $_priceBlockTypes = [];

    /**
     * Flag which allow/disallow to use link for as low as price
     *
     * @var bool
     */
    protected $_useLinkForAsLowAs = true;

    /**
     * Review block instance
     *
     * @var null|Mage_Review_Block_Helper
     */
    protected $_reviewsHelperBlock;

    /**
     * Default product amount per row
     *
     * @var int
     */
    protected $_defaultColumnCount = 3;

    /**
     * Product amount per row depending on custom page layout of category
     *
     * @var array
     */
    protected $_columnCountLayoutDepend = [];

    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp';

    /**
     * Get catalog product helper
     *
     * @return Mage_Catalog_Helper_Product
     */
    public function getProductHelper()
    {
        return Mage::helper('catalog/product');
    }

    /**
     * Retrieve url for add product to cart
     * Will return product view page URL if product has required options
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product, $additional = [])
    {
        return $this->getAddToCartUrlCustom($product, $additional);
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
     * Retrieves url for form submitting:
     * some objects can use setSubmitRouteData() to set route and params for form submitting,
     * otherwise default url will be used
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $additional
     * @return string
     */
    public function getSubmitUrl($product, $additional = [])
    {
        return $this->getSubmitUrlCustom($product, $additional);
    }

    /**
     * Return link to Add to Wishlist
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getAddToWishlistUrl($product)
    {
        return $this->getAddToWishlistUrlCustom($product);
    }

    /**
     * Retrieve Add Product to Compare Products List URL
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getAddToCompareUrl($product)
    {
        return $this->getAddToCompareUrlCustom($product);
    }

    /**
     * Gets minimal sales quantity
     *
     * @param Mage_Catalog_Model_Product $product
     * @return int|null
     */
    public function getMinimalQty($product)
    {
        return $this->getProductHelper()->getMinimalQty($product);
    }

    /**
     * Return price block
     *
     * @param string $productTypeId
     * @return mixed
     */
    protected function _getPriceBlock($productTypeId)
    {
        if (!isset($this->_priceBlock[$productTypeId])) {
            $block = $this->_block;
            if (isset($this->_priceBlockTypes[$productTypeId])) {
                if ($this->_priceBlockTypes[$productTypeId]['block'] != '') {
                    $block = $this->_priceBlockTypes[$productTypeId]['block'];
                }
            }
            $this->_priceBlock[$productTypeId] = $this->getLayout()->createBlock($block);
        }
        return $this->_priceBlock[$productTypeId];
    }

    /**
     * Return Block template
     *
     * @param string $productTypeId
     * @return string
     */
    protected function _getPriceBlockTemplate($productTypeId)
    {
        if (isset($this->_priceBlockTypes[$productTypeId])) {
            if ($this->_priceBlockTypes[$productTypeId]['template'] != '') {
                return $this->_priceBlockTypes[$productTypeId]['template'];
            }
        }
        return $this->_priceBlockDefaultTemplate;
    }

    /**
     * Prepares and returns block to render some product type
     *
     * @param string $productType
     * @return Mage_Core_Block_Template
     */
    public function _preparePriceRenderer($productType)
    {
        return $this->_getPriceBlock($productType)
            ->setTemplate($this->_getPriceBlockTemplate($productType))
            ->setUseLinkForAsLowAs($this->_useLinkForAsLowAs);
    }

    /**
     * Returns product price block html
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool $displayMinimalPrice
     * @param string $idSuffix
     * @return string
     */
    public function getPriceHtml($product, $displayMinimalPrice = false, $idSuffix = '')
    {
        $type_id = $product->getTypeId();
        if (Mage::helper('catalog')->canApplyMsrp($product)) {
            $realPriceHtml = $this->_preparePriceRenderer($type_id)
                ->setProduct($product)
                ->setDisplayMinimalPrice($displayMinimalPrice)
                ->setIdSuffix($idSuffix)
                ->toHtml();
            $product->setAddToCartUrl($this->getAddToCartUrl($product));
            $product->setRealPriceHtml($realPriceHtml);
            $type_id = $this->_mapRenderer;
        }

        return $this->_preparePriceRenderer($type_id)
            ->setProduct($product)
            ->setDisplayMinimalPrice($displayMinimalPrice)
            ->setIdSuffix($idSuffix)
            ->toHtml();
    }

    /**
     * Adding customized price template for product type
     *
     * @param string $type
     * @param string $block
     * @param string $template
     */
    public function addPriceBlockType($type, $block = '', $template = '')
    {
        if ($type) {
            $this->_priceBlockTypes[$type] = [
                'block' => $block,
                'template' => $template
            ];
        }
    }

    /**
     * Get product reviews summary
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool $templateType
     * @param bool $displayIfNoReviews
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getReviewsSummaryHtml(
        Mage_Catalog_Model_Product $product,
        $templateType = false,
        $displayIfNoReviews = false
    ) {
        if ($this->_initReviewsHelperBlock()) {
            return $this->_reviewsHelperBlock->getSummaryHtml($product, $templateType, $displayIfNoReviews);
        }

        return '';
    }

    /**
     * Add/replace reviews summary template by type
     *
     * @param string $type
     * @param string $template
     * @return string
     */
    public function addReviewSummaryTemplate($type, $template)
    {
        if ($this->_initReviewsHelperBlock()) {
            $this->_reviewsHelperBlock->addTemplate($type, $template);
        }

        return '';
    }

    /**
     * Create reviews summary helper block once
     *
     * @return bool
     */
    protected function _initReviewsHelperBlock()
    {
        if (!$this->_reviewsHelperBlock) {
            if (!Mage::helper('catalog')->isModuleEnabled('Mage_Review')) {
                return false;
            }

            $this->_reviewsHelperBlock = $this->getLayout()->createBlock('review/helper');
        }

        return true;
    }

    /**
     * Retrieve currently viewed product object
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', Mage::registry('product'));
        }
        return $this->getData('product');
    }

    /**
     * Return tier price template
     *
     * @return mixed|string
     */
    public function getTierPriceTemplate()
    {
        if (!$this->hasData('tier_price_template')) {
            return $this->_tierPriceDefaultTemplate;
        }

        return $this->getData('tier_price_template');
    }

    /**
     * Returns product tier price block html
     *
     * @param null|Mage_Catalog_Model_Product $product
     * @param null|Mage_Catalog_Model_Product $parent
     * @return string
     */
    public function getTierPriceHtml($product = null, $parent = null)
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        return $this->_getPriceBlock($product->getTypeId())
            ->setTemplate($this->getTierPriceTemplate())
            ->setProduct($product)
            ->setInGrouped($product->isGrouped())
            ->setParent($parent)
            ->callParentToHtml();
    }

    /**
     * Calls the object's to Html method.
     * This method exists to make the code more testable.
     * By having a protected wrapper for the final method toHtml, we can 'mock' out this method
     * when unit testing
     *
     * @return string
     */
    protected function callParentToHtml()
    {
        return $this->toHtml();
    }

    /**
     * Get tier prices (formatted)
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getTierPrices($product = null)
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        $prices = $product->getFormatedTierPrice();

        $res = [];
        if (is_array($prices)) {
            foreach ($prices as $price) {
                $price['price_qty'] = $price['price_qty'] * 1;

                $_productPrice = $product->getPrice();
                if ($_productPrice != $product->getFinalPrice()) {
                    $_productPrice = $product->getFinalPrice();
                }

                // Group price must be used for percent calculation if it is lower
                $groupPrice = $product->getGroupPrice();
                if ($_productPrice > $groupPrice) {
                    $_productPrice = $groupPrice;
                }

                if ($price['price'] < $_productPrice) {
                    $price['savePercent'] = ceil(100 - round((100 / $_productPrice) * $price['price']));

                    $tierPrice = Mage::app()->getStore()->convertPrice(
                        Mage::helper('tax')->getPrice($product, $price['website_price'])
                    );
                    $price['formated_price'] = Mage::app()->getStore()->formatPrice($tierPrice);
                    $price['formated_price_incl_tax'] = Mage::app()->getStore()->formatPrice(
                        Mage::app()->getStore()->convertPrice(
                            Mage::helper('tax')->getPrice($product, $price['website_price'], true)
                        )
                    );

                    if (Mage::helper('catalog')->canApplyMsrp($product)) {
                        $oldPrice = $product->getFinalPrice();
                        $product->setPriceCalculation(false);
                        $product->setPrice($tierPrice);
                        $product->setFinalPrice($tierPrice);

                        $this->getPriceHtml($product);
                        $product->setPriceCalculation(true);

                        $price['real_price_html'] = $product->getRealPriceHtml();
                        $product->setFinalPrice($oldPrice);
                    }

                    $res[] = $price;
                }
            }
        }

        return $res;
    }

    /**
     * Add all attributes and apply pricing logic to products collection
     * to get correct values in different products lists.
     * E.g. crosssells, upsells, new products, recently viewed
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _addProductAttributesAndPrices(Mage_Catalog_Model_Resource_Product_Collection $collection)
    {
        return $collection
            ->addPriceData()
            ->addTaxPercents()
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addUrlRewrite();
    }

    /**
     * Retrieve given media attribute label or product name if no label
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $mediaAttributeCode
     *
     * @return string
     */
    public function getImageLabel($product = null, $mediaAttributeCode = 'image')
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }

        $label = $product->getData($mediaAttributeCode . '_label');
        if (empty($label)) {
            $label = $product->getName();
        }

        return $label;
    }

    /**
     * Retrieve Product URL using UrlDataObject
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $additional the route params
     * @return string
     */
    public function getProductUrl($product, $additional = [])
    {
        if ($this->hasProductUrl($product)) {
            if (!isset($additional['_escape'])) {
                $additional['_escape'] = true;
            }
            return $product->getUrlModel()->getUrl($product, $additional);
        }
        return '#';
    }

    /**
     * Check Product has URL
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     *
     */
    public function hasProductUrl($product)
    {
        if ($product->getVisibleInSiteVisibilities()) {
            return true;
        }
        return false;
    }

    /**
     * Retrieve product amount per row
     *
     * @return int
     */
    public function getColumnCount()
    {
        if (!$this->_getData('column_count')) {
            $pageLayout = $this->getPageLayout();
            if ($pageLayout && $this->getColumnCountLayoutDepend($pageLayout->getCode())) {
                $this->setData(
                    'column_count',
                    $this->getColumnCountLayoutDepend($pageLayout->getCode())
                );
            } else {
                $this->setData('column_count', $this->_defaultColumnCount);
            }
        }

        return (int) $this->_getData('column_count');
    }

    /**
     * Add row size depends on page layout
     *
     * @param string $pageLayout
     * @param int $columnCount
     * @return $this
     */
    public function addColumnCountLayoutDepend($pageLayout, $columnCount)
    {
        $this->_columnCountLayoutDepend[$pageLayout] = $columnCount;
        return $this;
    }

    /**
     * Remove row size depends on page layout
     *
     * @param string $pageLayout
     * @return $this
     */
    public function removeColumnCountLayoutDepend($pageLayout)
    {
        if (isset($this->_columnCountLayoutDepend[$pageLayout])) {
            unset($this->_columnCountLayoutDepend[$pageLayout]);
        }

        return $this;
    }

    /**
     * Retrieve row size depends on page layout
     *
     * @param string $pageLayout
     * @return int|bool
     */
    public function getColumnCountLayoutDepend($pageLayout)
    {
        return $this->_columnCountLayoutDepend[$pageLayout] ?? false;
    }

    /**
     * Retrieve current page layout
     *
     * @return Varien_Object
     */
    public function getPageLayout()
    {
        /** @var Mage_Page_Helper_Layout $helper */
        $helper = $this->helper('page/layout');
        return $helper->getCurrentPageLayout();
    }

    /**
     * Check whether the price can be shown for the specified product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function getCanShowProductPrice($product)
    {
        return $product->getCanShowPrice() !== false;
    }

    /**
     * Get if it is necessary to show product stock status
     *
     * @return bool
     */
    public function displayProductStockStatus()
    {
        $statusInfo = new Varien_Object(['display_status' => true]);
        Mage::dispatchEvent('catalog_block_product_status_display', ['status' => $statusInfo]);
        return (bool)$statusInfo->getDisplayStatus();
    }

    /**
     * Return link to Add to Wishlist with or without Form Key
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool $addFormKey
     * @return string
     */
    public function getAddToWishlistUrlCustom($product, $addFormKey = true)
    {
        /** @var Mage_Wishlist_Helper_Data $helper */
        $helper = $this->helper('wishlist');

        if (!$addFormKey) {
            return $helper->getAddUrlWithCustomParams($product, [], false);
        }
        return $helper->getAddUrl($product);
    }

    /**
     * Retrieve Add Product to Compare Products List URL with or without Form Key
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool $addFormKey
     * @return string
     */
    public function getAddToCompareUrlCustom($product, $addFormKey = true)
    {
        /** @var Mage_Catalog_Helper_Product_Compare $helper */
        $helper = $this->helper('catalog/product_compare');

        if (!$addFormKey) {
            return $helper->getAddUrlCustom($product, false);
        }
        return $helper->getAddUrl($product);
    }

    /**
     * If exists price template block, retrieve price blocks from it
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        /** @var Mage_Catalog_Block_Product_Price_Template $block */
        $block = $this->getLayout()->getBlock('catalog_product_price_template');
        if ($block) {
            foreach ($block->getPriceBlockTypes() as $type => $priceBlock) {
                $this->addPriceBlockType($type, $priceBlock['block'], $priceBlock['template']);
            }
        }

        return $this;
    }

    /**
     * Retrieve url for add product to cart with or without Form Key
     * Will return product view page URL if product has required options
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $additional
     * @param bool $addFormKey
     * @return string
     */
    public function getAddToCartUrlCustom($product, $additional = [], $addFormKey = true)
    {
        /** @var Mage_Checkout_Helper_Cart $helper */
        $helper = $this->helper('checkout/cart');

        if (!$product->getTypeInstance(true)->hasRequiredOptions($product)) {
            if (!$addFormKey) {
                return $helper->getAddUrlCustom($product, $additional, false);
            }
            return $helper->getAddUrl($product, $additional);
        }

        if ($addFormKey) {
            $additional = array_merge(
                $additional,
                [Mage_Core_Model_Url::FORM_KEY => $this->_getSingletonModel('core/session')->getFormKey()]
            );
        }
        if (!isset($additional['_escape'])) {
            $additional['_escape'] = true;
        }
        if (!isset($additional['_query'])) {
            $additional['_query'] = [];
        }
        $additional['_query']['options'] = 'cart';
        return $this->getProductUrl($product, $additional);
    }

    /**
     * Retrieves url for form submitting:
     * some objects can use setSubmitRouteData() to set route and params for form submitting,
     * otherwise default url will be used with or without Form Key
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $additional
     * @param bool $addFormKey
     * @return string
     */
    public function getSubmitUrlCustom($product, $additional = [], $addFormKey = true)
    {
        $submitRouteData = $this->getData('submit_route_data');
        if ($submitRouteData) {
            $route = $submitRouteData['route'];
            $params = $submitRouteData['params'] ?? [];
            $submitUrl = $this->getUrl($route, array_merge($params, $additional));
        } elseif ($addFormKey) {
            $submitUrl = $this->getAddToCartUrl($product, $additional);
        } else {
            $submitUrl = $this->getAddToCartUrlCustom($product, $additional, false);
        }
        return $submitUrl;
    }
}
