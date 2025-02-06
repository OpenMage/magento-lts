<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Catalog
 */

/**
 * Grouped product price model
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Type_Grouped_Price extends Mage_Catalog_Model_Product_Type_Price
{
    /**
     * Returns product final price depending on options chosen
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

        $finalPrice = parent::getFinalPrice($qty, $product);
        if ($product->hasCustomOptions()) {
            /** @var Mage_Catalog_Model_Product_Type_Grouped $typeInstance */
            $typeInstance = $product->getTypeInstance(true);
            $associatedProducts = $typeInstance->setStoreFilter($product->getStore(), $product)
                ->getAssociatedProducts($product);
            foreach ($associatedProducts as $childProduct) {
                /** @var Mage_Catalog_Model_Product $childProduct */
                $option = $product->getCustomOption('associated_product_' . $childProduct->getId());
                if (!$option) {
                    continue;
                }
                $childQty = $option->getValue();
                if (!$childQty) {
                    continue;
                }
                $finalPrice += $childProduct->getFinalPrice($childQty) * $childQty;
            }
        }

        $product->setFinalPrice($finalPrice);
        Mage::dispatchEvent('catalog_product_type_grouped_price', ['product' => $product]);

        return max(0, $product->getData('final_price'));
    }
}
