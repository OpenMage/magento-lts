<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

/**
 * Downloadable products price model
 *
 * @package    Mage_Downloadable
 */
class Mage_Downloadable_Model_Product_Price extends Mage_Catalog_Model_Product_Type_Price
{
    /**
     * Retrieve product final price
     *
     * @param  null|int                   $qty
     * @param  Mage_Catalog_Model_Product $product
     * @return float
     */
    public function getFinalPrice($qty, $product)
    {
        if (is_null($qty) && !is_null($product->getCalculatedFinalPrice())) {
            return $product->getCalculatedFinalPrice();
        }

        $finalPrice = parent::getFinalPrice($qty, $product);

        /**
         * links prices are added to base product price only if they can be purchased separately
         */
        if ($product->getLinksPurchasedSeparately()) {
            if ($linksIds = $product->getCustomOption('downloadable_link_ids')) {
                $linkPrice = 0;
                /** @var Mage_Downloadable_Model_Product_Type $productType */
                $productType = $product->getTypeInstance(true);
                /** @var Mage_Downloadable_Model_Link[] $links */
                $links = $productType->getLinks($product);
                foreach (explode(',', $linksIds->getValue()) as $linkId) {
                    if (isset($links[$linkId])) {
                        $linkPrice += $links[$linkId]->getPrice();
                    }
                }

                $finalPrice += $linkPrice;
            }
        }

        $product->setData('final_price', $finalPrice);
        return max(0, $product->getData('final_price'));
    }
}
