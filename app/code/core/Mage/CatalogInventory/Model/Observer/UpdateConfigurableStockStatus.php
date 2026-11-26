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

        $parentProducts = Mage::getResourceModel('catalog/product_collection')
            ->setFlag('require_stock_items', true)
            ->addAttributeToFilter('type_id', Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE)
            ->addIdFilter($parentIds);

        /** @var Mage_Catalog_Model_Product $parentProduct */
        foreach ($parentProducts as $parentProduct) {
            $typeInstance = $parentProduct->getTypeInstance(true);
            if (!$typeInstance instanceof Mage_Catalog_Model_Product_Type_Configurable) {
                continue;
            }

            $isInStock       = 0;
            $childIds        = [];
            $childrenIds     = $typeInstance->getChildrenIds($parentProduct->getId());

            foreach ($childrenIds as $ids) {
                if (is_array($ids)) {
                    $childIds = array_merge($childIds, $ids);
                }
            }

            $childIds = array_values(array_unique($childIds));

            if ($childIds !== []) {
                /** @var Mage_CatalogInventory_Model_Resource_Stock_Item_Collection $stockItemCollection */
                $stockItemCollection = Mage::getResourceModel('cataloginventory/stock_item_collection');
                $firstStockItem = $stockItemCollection
                    ->addFieldToFilter('product_id', ['in' => $childIds])
                    ->addFieldToFilter('is_in_stock', 1)
                    ->setPageSize(1)
                    ->getFirstItem();
                $isInStock = $firstStockItem->getData() !== [] ? 1 : 0;
            }

            $parentStockItem = $parentProduct->getStockItem();
            if ((int) $parentStockItem->getIsInStock() !== $isInStock) {
                $parentStockItem->setIsInStock($isInStock)->save();
            }
        }
    }
}
