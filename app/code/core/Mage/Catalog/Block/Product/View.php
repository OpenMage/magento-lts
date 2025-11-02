<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Product View block
 *
 * @package    Mage_Catalog
 *
 * @method string getCustomAddToCartPostUrl()
 * @method string getCustomAddToCartUrl()
 * @method int getProductId()
 * @method bool hasCustomAddToCartPostUrl()
 * @method bool hasCustomAddToCartUrl()
 * @method $this setCustomAddToCartUrl(string $value)
 */
class Mage_Catalog_Block_Product_View extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_item';

    /**
     * Add meta information from product to head block
     *
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->createBlock('catalog/breadcrumbs');

        /** @var Mage_Page_Block_Html_Head $headBlock */
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $product = $this->getProduct();
            $title = $product->getMetaTitle();
            if ($title) {
                $headBlock->setTitle($title);
            }

            $keyword = $product->getMetaKeyword();
            $currentCategory = Mage::registry('current_category');
            if ($keyword) {
                $headBlock->setKeywords($keyword);
            } elseif ($currentCategory) {
                $headBlock->setKeywords($product->getName());
            }

            $description = $product->getMetaDescription();
            if ($description) {
                $headBlock->setDescription(($description));
            } else {
                $headBlock->setDescription(Mage::helper('core/string')->substr($product->getDescription(), 0, 255));
            }

            /** @var Mage_Catalog_Helper_Product $helper */
            $helper = $this->helper('catalog/product');
            if ($helper->canUseCanonicalTag()) {
                $params = ['_ignore_category' => true];
                $headBlock->addLinkRel('canonical', $product->getUrlModel()->getUrl($product, $params));
            }
        }

        return parent::_prepareLayout();
    }

    /**
     * Retrieve current product model
     *
     * @throws Mage_Core_Exception
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!Mage::registry('product') && $this->getProductId()) {
            $product = Mage::getModel('catalog/product')->load($this->getProductId());
            Mage::register('product', $product);
        }

        return Mage::registry('product');
    }

    /**
     * Check if product can be emailed to friend
     *
     * @return bool
     */
    public function canEmailToFriend()
    {
        $sendToFriendModel = Mage::registry('send_to_friend_model');
        return $sendToFriendModel && $sendToFriendModel->canEmailToFriend();
    }

    /**
     * Retrieve url for direct adding product to cart
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $additional
     * @throws Exception
     * @return string
     */
    public function getAddToCartUrl($product, $additional = [])
    {
        return $this->getAddToCartUrlCustom($product, $additional);
    }

    /**
     * Get JSON encoded configuration array which can be used for JS dynamic
     * price calculation depending on product options
     *
     * @throws Mage_Core_Exception
     * @return string
     */
    public function getJsonConfig()
    {
        $config = [];
        if (!$this->hasOptions()) {
            return Mage::helper('core')->jsonEncode($config);
        }

        $product = $this->getProduct();

        /** @var Mage_Catalog_Helper_Product_Type_Composite $compositeProductHelper */
        $compositeProductHelper = $this->helper('catalog/product_type_composite');
        $config = array_merge(
            $compositeProductHelper->prepareJsonGeneralConfig(),
            $compositeProductHelper->prepareJsonProductConfig($product),
        );

        $responseObject = new Varien_Object();
        Mage::dispatchEvent('catalog_product_view_config', ['response_object' => $responseObject]);
        if (is_array($responseObject->getAdditionalOptions())) {
            foreach ($responseObject->getAdditionalOptions() as $option => $value) {
                $config[$option] = $value;
            }
        }

        return Mage::helper('core')->jsonEncode($config);
    }

    /**
     * Return true if product has options
     *
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function hasOptions()
    {
        if ($this->getProduct()->getTypeInstance(true)->hasOptions($this->getProduct())) {
            return true;
        }

        return false;
    }

    /**
     * Check if product has required options
     *
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function hasRequiredOptions()
    {
        return $this->getProduct()->getTypeInstance(true)->hasRequiredOptions($this->getProduct());
    }

    /**
     * Define if setting of product options must be shown instantly.
     * Used in case when options are usually hidden and shown only when user
     * presses some button or link. In editing mode we better show these options
     * instantly.
     *
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function isStartCustomization()
    {
        return $this->getProduct()->getConfigureMode() || Mage::app()->getRequest()->getParam('startcustomization');
    }

    /**
     * Get default qty - either as preconfigured, or as 1.
     * Also restricts it by minimal qty.
     *
     * @param null|Mage_Catalog_Model_Product $product
     * @throws Mage_Core_Exception
     * @return float|int
     */
    public function getProductDefaultQty($product = null)
    {
        if (!$product) {
            $product = $this->getProduct();
        }

        return $this->getProductHelper()->getDefaultQty($product);
    }

    /**
     * Retrieve block cache tags
     *
     * @throws Mage_Core_Exception
     * @return array
     */
    public function getCacheTags()
    {
        return array_merge(parent::getCacheTags(), $this->getProduct()->getCacheIdTags());
    }

    /**
     * Retrieve url for direct adding product to cart with or without Form Key
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $additional
     * @param bool $addFormKey
     * @throws Exception
     * @return string
     */
    public function getAddToCartUrlCustom($product, $additional = [], $addFormKey = true)
    {
        if (!$addFormKey && $this->hasCustomAddToCartPostUrl()) {
            return $this->getCustomAddToCartPostUrl();
        } elseif ($this->hasCustomAddToCartUrl()) {
            return $this->getCustomAddToCartUrl();
        }

        if ($this->getRequest()->getParam('wishlist_next')) {
            $additional['wishlist_next'] = 1;
        }

        $addUrlValue = Mage::getUrl('*/*/*', ['_use_rewrite' => true, '_current' => true]);
        $additional[Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED] =
            Mage::helper('core')->urlEncode($addUrlValue);

        /** @var Mage_Checkout_Helper_Cart $helper */
        $helper = $this->helper('checkout/cart');

        if (!$addFormKey) {
            return $helper->getAddUrlCustom($product, $additional, false);
        }

        return $helper->getAddUrl($product, $additional);
    }
}
