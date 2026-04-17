<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

/**
 * Product View block (to modify getTierPrices method)
 *
 * @package    Mage_Bundle
 * @module     Catalog
 */
class Mage_Bundle_Block_Catalog_Product_View extends Mage_Catalog_Block_Product_View
{
    /**
     * Get tier prices (formatted)
     *
     * @param  null|Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getTierPrices($product = null)
    {
        if ($product === null) {
            $product = $this->getProduct();
        }

        $res = [];

        $prices = $product->getFormatedTierPrice();
        if (is_array($prices)) {
            $store = Mage::app()->getStore();
            $helper = Mage::helper('tax');
            $specialPrice = $product->getSpecialPrice();
            $defaultDiscount = max($product->getGroupPrice(), $specialPrice ? 100 - $specialPrice : 0);
            foreach ($prices as $price) {
                if ($defaultDiscount < $price['price']) {
                    $price['price_qty'] += 0;
                    $price['savePercent'] = ceil(100 - $price['price']);

                    $priceExclTax = $helper->getPrice($product, $price['website_price']);
                    $price['formated_price'] = $store->formatPrice($store->convertPrice($priceExclTax));

                    $priceInclTax = $helper->getPrice($product, $price['website_price'], true);
                    $price['formated_price_incl_tax'] = $store->formatPrice($store->convertPrice($priceInclTax));

                    $res[] = $price;
                }
            }
        }

        return $res;
    }
}
