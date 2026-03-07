<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogInventory
 */

/**
 * Catalog inventory module observer
 *
 * @package    Mage_CatalogInventory
 */
final class Mage_CatalogInventory_Model_Observer_UpdateConfigurableStockStatus implements Mage_Core_Observer_Interface
{
    /**
     * @throws Mage_Core_Exception
     * @throws Throwable
     * @throws Zend_Db_Select_Exception
     */
    public function execute(Varien_Event_Observer $observer): void
    {
        /** @var Mage_CatalogInventory_Model_Stock_Item $stockItem */
        $stockItem = $observer->getEvent()->getItem();
        $product   = $stockItem->getProduct();

        if (!$product instanceof Mage_Catalog_Model_Product) {
            return;
        }

        if ($product->getTypeId() !== Mage_Catalog_Model_Product_Type::TYPE_SIMPLE) {
            return;
        }

        $parentIds = Mage::getModel('catalog/product_type_configurable')
            ->getParentIdsByChild($product->getId());

        $parentPooducts = Mage::getResourceModel('catalog/product_collection')
            ->setFlag('require_stock_items', true)
            ->addAttributeToFilter('type_id', Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE)
            ->addIdFilter($parentIds);

        foreach ($parentPooducts as $parentProduct) {
            $parentStockItem = $parentProduct->getStockItem();
            $childProducts   = $parentProduct->getTypeInstance(true)
                ->getUsedProducts(null, $parentProduct);

            $isInStock = false;
            foreach ($childProducts as $child) {
                if ($child->getIsInStock()) {
                    $isInStock = true;
                    break;
                }
            }

            if ((bool) $parentStockItem->getIsInStock() !== $isInStock) {
                $parentStockItem->setIsInStock($isInStock)->save();
            }
        }
    }
}
