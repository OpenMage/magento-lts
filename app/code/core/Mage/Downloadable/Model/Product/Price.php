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
 * @package    Mage_Downloadable
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Downloadable products price model
 *
 * @category   Mage
 * @package    Mage_Downloadable
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Model_Product_Price extends Mage_Catalog_Model_Product_Type_Price
{
    /**
     * Retrieve product final price
     *
     * @param int|null $qty
     * @param Mage_Catalog_Model_Product $product
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
