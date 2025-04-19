<?php

declare(strict_types=1);

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Mage_Sales Model Observer
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Observer_MarkQuotesRecollectOnCatalogRules implements Mage_Core_Observer_Interface
{
    /**
     * When applying a catalog price rule, make related quotes recollect on demand
     */
    public function execute(Varien_Event_Observer $observer): self
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $observer->getEvent()->getDataByKey('product');

        if (is_numeric($product)) {
            $product = Mage::getModel('catalog/product')->load($product);
        }
        if ($product instanceof Mage_Catalog_Model_Product) {
            $childrenProductList = Mage::getSingleton('catalog/product_type')->factory($product)
                ->getChildrenIds($product->getId(), false);

            $productIdList = [$product->getId()];
            foreach ($childrenProductList as $groupData) {
                $productIdList = array_merge($productIdList, $groupData);
            }
        } else {
            $productIdList = null;
        }

        Mage::getResourceSingleton('sales/quote')->markQuotesRecollectByAffectedProduct($productIdList);
        return $this;
    }
}
