<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 */

/**
 * Shopping cart helper
 *
 * @package    Mage_Checkout
 */
class Mage_Checkout_Helper_Cart extends Mage_Core_Helper_Url
{
    /**
     * XML path for redirect to cart value
     */
    public const XML_PATH_REDIRECT_TO_CART = 'checkout/cart/redirect_to_cart';

    /**
     * Maximal coupon code length according to database table definitions (longer codes are truncated)
     */
    public const COUPON_CODE_MAX_LENGTH = 255;

    protected $_moduleName = 'Mage_Checkout';

    /**
     * Retrieve cart instance
     *
     * @return Mage_Checkout_Model_Cart
     */
    public function getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

    /**
     * Retrieve url for add product to cart
     *
     * @param  Mage_Catalog_Model_Product $product
     * @param  array                      $additional
     * @return string
     */
    public function getAddUrl($product, $additional = [])
    {
        return $this->getAddUrlCustom($product, $additional);
    }

    /**
     * Return helper instance
     *
     * @param  string                    $helperName
     * @return Mage_Core_Helper_Abstract
     */
    protected function _getHelperInstance($helperName)
    {
        return Mage::helper($helperName);
    }

    /**
     * Retrieve url for remove product from cart
     *
     * @param  Mage_Sales_Model_Quote_Item $item
     * @return string
     */
    public function getRemoveUrl($item)
    {
        $params = [
            'id' => $item->getId(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_BASE64_URL => $this->getCurrentBase64Url(),
        ];
        return $this->_getUrl('checkout/cart/delete', $params);
    }

    /**
     * Retrieve shopping cart url
     *
     * @return string
     */
    public function getCartUrl()
    {
        return $this->_getUrl('checkout/cart');
    }

    /**
     * Retrieve current quote instance
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    /**
     * Get shopping cart items count
     *
     * @return int
     */
    public function getItemsCount()
    {
        return $this->getCart()->getItemsCount();
    }

    /**
     * Get shopping cart summary qty
     *
     * @return float
     */
    public function getItemsQty()
    {
        return $this->getCart()->getItemsQty();
    }

    /**
     * Get shopping cart items summary (inchlude config settings)
     *
     * @return float
     */
    public function getSummaryCount()
    {
        return $this->getCart()->getSummaryQty();
    }

    /**
     * Check quote for virtual products only
     *
     * @return bool
     */
    public function getIsVirtualQuote()
    {
        return $this->getQuote()->isVirtual();
    }

    /**
     * Checks if customer should be redirected to shopping cart after adding a product
     *
     * @param  int|Mage_Core_Model_Store|string $store
     * @return bool
     */
    public function getShouldRedirectToCart($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_REDIRECT_TO_CART, $store);
    }

    /**
     * Retrieve url for add product to cart with or without Form Key
     *
     * @param  Mage_Catalog_Model_Product $product
     * @param  array                      $additional
     * @param  bool                       $addFormKey
     * @return string
     */
    public function getAddUrlCustom($product, $additional = [], $addFormKey = true)
    {
        $routeParams = [
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->_getHelperInstance('core')
                ->urlEncode($this->getCurrentUrl()),
            'product' => $product->getEntityId(),
        ];
        if ($addFormKey) {
            $routeParams[Mage_Core_Model_Url::FORM_KEY] = $this->_getSingletonModel('core/session')->getFormKey();
        }

        if (!empty($additional)) {
            $routeParams = array_merge($routeParams, $additional);
        }

        if ($product->hasUrlDataObject()) {
            $routeParams['_store'] = $product->getUrlDataObject()->getStoreId();
            $routeParams['_store_to_url'] = true;
        }

        if ($this->_getRequest()->getRouteName() == 'checkout'
            && $this->_getRequest()->getControllerName() == 'cart'
        ) {
            $routeParams['in_cart'] = 1;
        }

        return $this->_getUrl('checkout/cart/add', $routeParams);
    }
}
