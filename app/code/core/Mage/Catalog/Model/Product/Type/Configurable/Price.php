<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Product type price model
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Type_Configurable_Price extends Mage_Catalog_Model_Product_Type_Price
{
    /**
     * Get product final price
     *
     * @param float|null $qty
     * @param Mage_Catalog_Model_Product $product
     * @return  double
     */
    public function getFinalPrice($qty, $product)
    {
        if (is_null($qty) && !is_null($product->getCalculatedFinalPrice())) {
            return $product->getCalculatedFinalPrice();
        }

        $basePrice = $this->getBasePrice($product, $qty);
        $finalPrice = $basePrice;
        $product->setFinalPrice($finalPrice);
        Mage::dispatchEvent('catalog_product_get_final_price', ['product' => $product, 'qty' => $qty]);
        $finalPrice = $product->getData('final_price');

        $finalPrice += $this->getTotalConfigurableItemsPrice($product, $finalPrice);
        $finalPrice += $this->_applyOptionsPrice($product, $qty, $basePrice) - $basePrice;
        $finalPrice = max(0, $finalPrice);

        $product->setFinalPrice($finalPrice);
        return $finalPrice;
    }

    /**
     * Get Total price for configurable items
     *
     * @param Mage_Catalog_Model_Product $product
     * @param float $finalPrice
     * @return float
     */
    public function getTotalConfigurableItemsPrice($product, $finalPrice)
    {
        $price = 0.0;

        /** @var Mage_Catalog_Model_Product_Type_Configurable $productType */
        $productType = $product->getTypeInstance(true);
        $productType->setStoreFilter($product->getStore(), $product);
        $attributes = $productType->getConfigurableAttributes($product);

        $selectedAttributes = [];
        if ($product->getCustomOption('attributes')) {
            $selectedAttributes = unserialize($product->getCustomOption('attributes')->getValue(), ['allowed_classes' => false]);
        }

        /** @var Mage_Catalog_Model_Product_Type_Configurable_Attribute $attribute */
        foreach ($attributes as $attribute) {
            $attributeId = $attribute->getProductAttribute()->getId();
            $value = $this->_getValueByIndex(
                $attribute->getPrices() ? $attribute->getPrices() : [],
                $selectedAttributes[$attributeId] ?? null,
            );
            $product->setParentId(true);
            if ($value) {
                if ($value['pricing_value'] != 0) {
                    $product->setConfigurablePrice($this->_calcSelectionPrice($value, $finalPrice));
                    Mage::dispatchEvent(
                        'catalog_product_type_configurable_price',
                        ['product' => $product],
                    );
                    $price += $product->getConfigurablePrice();
                }
            }
        }
        return $price;
    }

    /**
     * Calculate configurable product selection price
     *
     * @param   array $priceInfo
     * @param   float $productPrice
     * @return  float
     */
    protected function _calcSelectionPrice($priceInfo, $productPrice)
    {
        if ($priceInfo['is_percent']) {
            $ratio = $priceInfo['pricing_value'] / 100;
            $price = $productPrice * $ratio;
        } else {
            $price = $priceInfo['pricing_value'];
        }
        return $price;
    }

    /**
     * @param array $values
     * @param string $index
     * @return array|false
     */
    protected function _getValueByIndex($values, $index)
    {
        foreach ($values as $value) {
            if ($value['value_index'] == $index) {
                return $value;
            }
        }
        return false;
    }
}
