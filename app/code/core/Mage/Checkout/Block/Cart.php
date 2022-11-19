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
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart block
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method string getCartTemplate()
 * @method string getEmptyTemplate()
 * @method $this setIsWishlistActive(bool $value)
 * @method int getItemsCount()
 * @method Mage_Sales_Model_Quote_Item[] getCustomItems()
 */
class Mage_Checkout_Block_Cart extends Mage_Checkout_Block_Cart_Abstract
{
    /**
     * Prepare cart items URLs
     *
     * @deprecated after 1.7.0.2
     */
    public function prepareItemUrls()
    {
        $products = [];
        foreach ($this->getItems() as $item) {
            $product    = $item->getProduct();
            $option     = $item->getOptionByCode('product_type');
            if ($option) {
                $product = $option->getProduct();
            }

            if ($item->getStoreId() != Mage::app()->getStore()->getId()
                && !$item->getRedirectUrl()
                && !$product->isVisibleInSiteVisibility()) {
                $products[$product->getId()] = $item->getStoreId();
            }
        }

        if ($products) {
            $products = Mage::getResourceSingleton('catalog/url')
                ->getRewriteByProductStore($products);
            foreach ($this->getItems() as $item) {
                $product    = $item->getProduct();
                $option     = $item->getOptionByCode('product_type');
                if ($option) {
                    $product = $option->getProduct();
                }

                if (isset($products[$product->getId()])) {
                    $object = new Varien_Object($products[$product->getId()]);
                    $item->getProduct()->setUrlDataObject($object);
                }
            }
        }
    }

    public function chooseTemplate()
    {
        $itemsCount = $this->getItemsCount() ?: $this->getQuote()->getItemsCount();
        if ($itemsCount) {
            $this->setTemplate($this->getCartTemplate());
        } else {
            $this->setTemplate($this->getEmptyTemplate());
        }
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return $this->getQuote()->getHasError();
    }

    /**
     * @return float|int|mixed
     */
    public function getItemsSummaryQty()
    {
        return $this->getQuote()->getItemsSummaryQty();
    }

    /**
     * @return bool|mixed
     */
    public function isWishlistActive()
    {
        $isActive = $this->_getData('is_wishlist_active');
        if ($isActive === null) {
            $isActive = Mage::getStoreConfig('wishlist/general/active')
                && Mage::getSingleton('customer/session')->isLoggedIn();
            $this->setIsWishlistActive($isActive);
        }
        return $isActive;
    }

    /**
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->getUrl('checkout/onepage', ['_secure' => true]);
    }

    /**
     * Return "cart" form action url
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('checkout/cart/updatePost', ['_secure' => $this->_isSecure()]);
    }

    /**
     * @return mixed|string
     */
    public function getContinueShoppingUrl()
    {
        $url = $this->getData('continue_shopping_url');
        if (is_null($url)) {
            $url = Mage::getSingleton('checkout/session')->getContinueShoppingUrl(true);
            if (!$url) {
                $url = Mage::getUrl();
            }
            $this->setData('continue_shopping_url', $url);
        }
        return $url;
    }

    /**
     * @return bool
     */
    public function getIsVirtual()
    {
        /** @var Mage_Checkout_Helper_Cart $helper */
        $helper = $this->helper('checkout/cart');
        return $helper->getIsVirtualQuote();
    }

    /**
     * Return list of available checkout methods
     *
     * @param string $nameInLayout Container block alias in layout
     * @return array
     */
    public function getMethods($nameInLayout)
    {
        if ($this->getChild($nameInLayout) instanceof Mage_Core_Block_Abstract) {
            return $this->getChild($nameInLayout)->getSortedChildren();
        }
        return [];
    }

    /**
     * Return HTML of checkout method (link, button etc.)
     *
     * @param string $name Block name in layout
     * @return string
     * @throws Mage_Core_Exception
     */
    public function getMethodHtml($name)
    {
        $block = $this->getLayout()->getBlock($name);
        if (!$block) {
            Mage::throwException(Mage::helper('checkout')->__('Invalid method: %s', $name));
        }
        return $block->toHtml();
    }

    /**
     * Return customer quote items
     *
     * @return Mage_Sales_Model_Quote_Item[]
     */
    public function getItems()
    {
        if ($this->getCustomItems()) {
            return $this->getCustomItems();
        }

        return parent::getItems();
    }
}
