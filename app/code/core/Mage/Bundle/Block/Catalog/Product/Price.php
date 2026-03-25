<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

/**
 * Bundle product price block
 *
 * @package    Mage_Bundle
 *
 * @method string getMAPTemplate()
 * @method $this  setWithoutPrice(bool $value)
 * @method $this  unsWithoutPrice()
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

        return ((float) $defaultTax > 0 || (float) $currentTax > 0);
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
        if ($product->getPriceType() == Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC
            && $product->getPriceModel()->getIsPricesCalculatedByIndex() !== false
        ) {
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
