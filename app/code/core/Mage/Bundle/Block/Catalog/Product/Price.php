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
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bundle product price block
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method string getMAPTemplate()
 * @method $this unsWithoutPrice()
 * @method $this setWithoutPrice(bool $value)
 */
class Mage_Bundle_Block_Catalog_Product_Price extends Mage_Catalog_Block_Product_Price
{
    /**
     * @return bool
     */
    public function isRatesGraterThenZero()
    {
        $_request = Mage::getSingleton('tax/calculation')->getDefaultRateRequest();
        $_request->setProductClassId($this->getProduct()->getTaxClassId());
        $defaultTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        $_request = Mage::getSingleton('tax/calculation')->getRateRequest();
        $_request->setProductClassId($this->getProduct()->getTaxClassId());
        $currentTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        return (floatval($defaultTax) > 0 || floatval($currentTax) > 0);
    }

    /**
     * Check if we have display prices including and excluding tax
     * With corrections for Dynamic prices
     *
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     */
    public function displayBothPrices()
    {
        $product = $this->getProduct();
        if ($product->getPriceType() == Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC &&
            $product->getPriceModel()->getIsPricesCalculatedByIndex() !== false) {
            return false;
        }

        /** @var Mage_Tax_Helper_Data $helper */
        $helper = $this->helper('tax');
        return $helper->displayBothPrices(Mage::app()->getStore()->getId());
    }

    /**
     * Convert block to html string
     *
     * @return string
     */
    protected function _toHtml()
    {
        $product = $this->getProduct();
        if ($this->getMAPTemplate() && Mage::helper('catalog')->canApplyMsrp($product)
                && $product->getPriceType() != Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC
        ) {
            $hiddenPriceHtml = parent::_toHtml();
            if (Mage::helper('catalog')->isShowPriceOnGesture($product)) {
                $this->setWithoutPrice(true);
            }
            $realPriceHtml = parent::_toHtml();
            $this->unsWithoutPrice();
            $addToCartUrl  = $this->getLayout()->getBlock('product.info.bundle')->getAddToCartUrl($product);
            $product->setAddToCartUrl($addToCartUrl);
            $html = $this->getLayout()
                ->createBlock('catalog/product_price')
                ->setTemplate($this->getMAPTemplate())
                ->setRealPriceHtml($hiddenPriceHtml)
                ->setPriceElementIdPrefix('bundle-price-')
                ->setIdSuffix($this->getIdSuffix())
                ->setProduct($product)
                ->toHtml();

            return $realPriceHtml . $html;
        }

        return parent::_toHtml();
    }
}
