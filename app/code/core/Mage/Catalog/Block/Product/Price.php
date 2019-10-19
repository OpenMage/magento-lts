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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Product price block
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Block_Product_Price extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * Price display type
     *
     * @var int
     */
    protected $_priceDisplayType = null;

    /**
     * The id suffix
     *
     * @var string
     */
    protected $_idSuffix = '';

    /**
     * Retrieve product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        $product = $this->_getData('product');
        if (!$product) {
            $product = Mage::registry('product');
        }
        return $product;
    }

    /**
     * Returns the product's minimal price
     *
     * @return float
     */
    public function getDisplayMinimalPrice()
    {
        return $this->_getData('display_minimal_price');
    }

    /**
     * Sets the id suffix
     *
     * @param string $idSuffix
     * @return Mage_Catalog_Block_Product_Price
     */
    public function setIdSuffix($idSuffix)
    {
        $this->_idSuffix = $idSuffix;
        return $this;
    }

    /**
     * Returns the id suffix
     *
     * @return string
     */
    public function getIdSuffix()
    {
        return $this->_idSuffix;
    }

    /**
     * Get tier prices (formatted)
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_Catalog_Model_Product $parent
     * @return array
     */
    public function getTierPrices($product = null, $parent = null)
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        $prices = $product->getFormatedTierPrice();

        // if our parent is a bundle, then we need to further adjust our tier prices
        if (isset($parent) && $parent->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            /* @var $bundlePriceModel Mage_Bundle_Model_Product_Price */
            $bundlePriceModel = Mage::getModel('bundle/product_price');
        }

        $res = array();
        if (is_array($prices)) {
            foreach ($prices as $price) {
                $price['price_qty'] = $price['price_qty'] * 1;

                $productPrice = $product->getPrice();
                if ($product->getPrice() != $product->getFinalPrice()) {
                    $productPrice = $product->getFinalPrice();
                }

                // Group price must be used for percent calculation if it is lower
                $groupPrice = $product->getGroupPrice();
                if ($productPrice > $groupPrice) {
                    $productPrice = $groupPrice;
                }

                if ($price['price'] < $productPrice) {
                    // use the original prices to determine the percent savings
                    $price['savePercent'] = ceil(100 - ((100 / $productPrice) * $price['price']));

                    // if applicable, adjust the tier prices
                    if (isset($bundlePriceModel)) {
                        $price['price']         = $bundlePriceModel->getLowestPrice($parent, $price['price']);
                        $price['website_price'] = $bundlePriceModel->getLowestPrice($parent, $price['website_price']);
                    }

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

                        $this->getLayout()->getBlock('product.info')->getPriceHtml($product);
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
     * Retrieve url for direct adding product to cart
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product, $additional = array())
    {
        return $this->getAddToCartUrlCustom($product, $additional);
    }

    /**
     * Prevent displaying if the price is not available
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->getProduct() || $this->getProduct()->getCanShowPrice() === false) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Get Product Price valid JS string
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getRealPriceJs($product)
    {
        $html = $this->hasRealPriceHtml() ? $this->getRealPriceHtml() : $product->getRealPriceHtml();
        return Mage::helper('core')->jsonEncode($html);
    }

    /**
     * Retrieve block cache tags
     *
     * @return array
     */
    public function getCacheTags()
    {
        return array_merge(parent::getCacheTags(), $this->getProduct()->getCacheIdTags());
    }

    /**
     * Retrieve attribute instance by name, id or config node
     *
     * If attribute is not found false is returned
     *
     * @param string|integer|Mage_Core_Model_Config_Element $attribute
     * @return Mage_Eav_Model_Entity_Attribute_Abstract || false
     */
    public function getProductAttribute($attribute)
    {
        return $this->getProduct()->getResource()->getAttribute($attribute);
    }

    /**
     * Retrieve url for direct adding product to cart with or without Form Key
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $additional
     * @param bool $addFormKey
     * @return string
     */
    public function getAddToCartUrlCustom($product, $additional = array(), $addFormKey = true)
    {
        if (!$addFormKey) {
            return $this->helper('checkout/cart')->getAddUrlCustom($product, $additional, false);
        }
        return $this->helper('checkout/cart')->getAddUrl($product, $additional);
    }
}
