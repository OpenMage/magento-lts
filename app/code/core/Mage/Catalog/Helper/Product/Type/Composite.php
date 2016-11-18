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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper for preparing properties for configurable product
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Helper_Product_Type_Composite extends Mage_Core_Helper_Abstract
{
    /**
     * Calculation real price
     *
     * @param Mage_Catalog_Model_Product $product
     * @param float $price
     * @param bool $isPercent
     * @param null|int $storeId
     * @return mixed
     */
    public function preparePrice($product, $price, $isPercent = false, $storeId = null)
    {
        if ($isPercent && !empty($price)) {
            $price = $product->getFinalPrice() * $price / 100;
        }

        return $this->registerJsPrice($this->convertPrice($price, true, $storeId));
    }

    /**
     * Calculation price before special price
     *
     * @param Mage_Catalog_Model_Product $product
     * @param float $price
     * @param bool $isPercent
     * @param null|int $storeId
     * @return mixed
     */
    public function prepareOldPrice($product, $price, $isPercent = false, $storeId = null)
    {
        if ($isPercent && !empty($price)) {
            $price = $product->getPrice() * $price / 100;
        }

        return $this->registerJsPrice($this->convertPrice($price, true, $storeId));
    }

    /**
     * Replace ',' on '.' for js
     *
     * @param float $price
     * @return string
     */
    public function registerJsPrice($price)
    {
        return str_replace(',', '.', $price);
    }

    /**
     * Convert price from default currency to current currency
     *
     * @param float $price
     * @param boolean $round
     * @param null|int $storeId
     * @return float
     */
    public function convertPrice($price, $round = false, $storeId = null)
    {
        if (empty($price)) {
            return 0;
        }

        $price = $this->getCurrentStore($storeId)->convertPrice($price);
        if ($round) {
            $price = $this->getCurrentStore($storeId)->roundPrice($price);
        }

        return $price;
    }

    /**
     * Retrieve current store
     *
     * @param null $storeId
     * @return Mage_Core_Model_Store
     */
    public function getCurrentStore($storeId = null)
    {
        return Mage::app()->getStore($storeId);
    }

    /**
     * Prepare general params for product to be used in getJsonConfig()
     * @see Mage_Catalog_Block_Product_View::getJsonConfig()
     * @see Mage_ConfigurableSwatches_Block_Catalog_Product_List_Price::getJsonConfig()
     *
     * @return array
     */
    public function prepareJsonGeneralConfig()
    {
        return array(
            'priceFormat'       => Mage::app()->getLocale()->getJsPriceFormat(),
            'includeTax'        => Mage::helper('tax')->priceIncludesTax() ? 'true' : 'false',
            'showIncludeTax'    => Mage::helper('tax')->displayPriceIncludingTax(),
            'showBothPrices'    => Mage::helper('tax')->displayBothPrices(),
            'idSuffix'            => '_clone',
            'oldPlusDisposition'  => 0,
            'plusDisposition'     => 0,
            'plusDispositionTax'  => 0,
            'oldMinusDisposition' => 0,
            'minusDisposition'    => 0,
        );
    }



    /**
     * Prepare product specific params to be used in getJsonConfig()
     * @see Mage_Catalog_Block_Product_View::getJsonConfig()
     * @see Mage_ConfigurableSwatches_Block_Catalog_Product_List_Price::getJsonConfig()
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function prepareJsonProductConfig($product)
    {
        $_request = Mage::getSingleton('tax/calculation')->getDefaultRateRequest();
        $_request->setProductClassId($product->getTaxClassId());
        $defaultTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        $_request = Mage::getSingleton('tax/calculation')->getRateRequest();
        $_request->setProductClassId($product->getTaxClassId());
        $currentTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        $_regularPrice = $product->getPrice();
        $_finalPrice = $product->getFinalPrice();
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            $_priceInclTax = Mage::helper('tax')->getPrice($product, $_finalPrice, true,
                null, null, null, null, null, false);
            $_priceExclTax = Mage::helper('tax')->getPrice($product, $_finalPrice, false,
                null, null, null, null, null, false);
        } else {
            $_priceInclTax = Mage::helper('tax')->getPrice($product, $_finalPrice, true);
            $_priceExclTax = Mage::helper('tax')->getPrice($product, $_finalPrice);
        }
        $_tierPrices = array();
        $_tierPricesInclTax = array();
        foreach ($product->getTierPrice() as $tierPrice) {
            $_tierPrices[] = Mage::helper('core')->currency(
                Mage::helper('tax')->getPrice($product, (float)$tierPrice['website_price'], false) - $_priceExclTax
                , false, false);
            $_tierPricesInclTax[] = Mage::helper('core')->currency(
                Mage::helper('tax')->getPrice($product, (float)$tierPrice['website_price'], true) - $_priceInclTax
                , false, false);
        }

        return array(
            'productId'           => $product->getId(),
            'productPrice'        => Mage::helper('core')->currency($_finalPrice, false, false),
            'productOldPrice'     => Mage::helper('core')->currency($_regularPrice, false, false),
            'priceInclTax'        => Mage::helper('core')->currency($_priceInclTax, false, false),
            'priceExclTax'        => Mage::helper('core')->currency($_priceExclTax, false, false),
            'skipCalculate'       => ($_priceExclTax != $_priceInclTax ? 0 : 1),
            'defaultTax'          => $defaultTax,
            'currentTax'          => $currentTax,
            'tierPrices'          => $_tierPrices,
            'tierPricesInclTax'   => $_tierPricesInclTax,
            'swatchPrices'        => $product->getSwatchPrices(),
        );
    }
}
