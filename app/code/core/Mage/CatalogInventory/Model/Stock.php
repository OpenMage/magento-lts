<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogInventory
 */

/**
 * Stock model
 *
 * @package    Mage_CatalogInventory
 *
 * @method Mage_CatalogInventory_Model_Resource_Stock _getResource()
 * @method Mage_CatalogInventory_Model_Resource_Stock getResource()
 * @method string getStockName()
 * @method $this setStockName(string $value)
 */
class Mage_CatalogInventory_Model_Stock extends Mage_Core_Model_Abstract
{
    public const BACKORDERS_NO             = 0;

    public const BACKORDERS_YES_NONOTIFY   = 1;

    public const BACKORDERS_YES_NOTIFY     = 2;

    /* @deprecated */
    public const BACKORDERS_BELOW          = 1;

    public const BACKORDERS_YES            = 2;

    public const STOCK_OUT_OF_STOCK        = 0;

    public const STOCK_IN_STOCK            = 1;

    public const DEFAULT_STOCK_ID          = 1;

    protected function _construct()
    {
        $this->_init('cataloginventory/stock');
    }

    /**
     * Retrieve stock identifier
     *
     * @return int
     */
    public function getId()
    {
        return self::DEFAULT_STOCK_ID;
    }

    /**
     * Add stock item objects to products
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $productCollection
     * @return  Mage_CatalogInventory_Model_Stock
     */
    public function addItemsToProducts($productCollection)
    {
        $items = $this->getItemCollection()
            ->addProductsFilter($productCollection)
            ->joinStockStatus($productCollection->getStoreId())
            ->load();
        $stockItems = [];
        /** @var Mage_CatalogInventory_Model_Stock_Item $item */
        foreach ($items as $item) {
            $stockItems[$item->getProductId()] = $item;
        }

        /** @var Mage_Catalog_Model_Product $product */
        foreach ($productCollection as $product) {
            if (isset($stockItems[$product->getId()])) {
                $stockItems[$product->getId()]->assignProduct($product);
            }
        }

        return $this;
    }

    /**
     * Retrieve items collection object with stock filter
     *
     * @return Mage_CatalogInventory_Model_Resource_Stock_Item_Collection
     */
    public function getItemCollection()
    {
        return Mage::getResourceModel('cataloginventory/stock_item_collection')
            ->addStockFilter($this->getId());
    }

    /**
     * Prepare array($productId=>$qty) based on array($productId => array('qty'=>$qty, 'item'=>$stockItem))
     *
     * @param array $items
     * @return array
     */
    protected function _prepareProductQtys($items)
    {
        $qtys = [];
        foreach ($items as $productId => $item) {
            if (empty($item['item'])) {
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
            } else {
                $stockItem = $item['item'];
            }

            $canSubtractQty = $stockItem->getId() && $stockItem->canSubtractQty();
            if ($canSubtractQty && Mage::helper('cataloginventory')->isQty($stockItem->getTypeId())) {
                $qtys[$productId] = $item['qty'];
            }
        }

        return $qtys;
    }

    /**
     * Subtract product qtys from stock.
     * Return array of items that require full save
     *
     * @param array $items
     * @return array
     */
    public function registerProductsSale($items)
    {
        $qtys = $this->_prepareProductQtys($items);
        $item = Mage::getModel('cataloginventory/stock_item');
        $this->_getResource()->beginTransaction();
        try {
            $stockInfo = $this->_getResource()->getProductsStock($this, array_keys($qtys), true);
            $fullSaveItems = [];
            foreach ($stockInfo as $itemInfo) {
                $item->setData($itemInfo);
                if (!$item->checkQty($qtys[$item->getProductId()])) {
                    Mage::throwException(Mage::helper('cataloginventory')->__('Not all products are available in the requested quantity'));
                }

                $item->subtractQty($qtys[$item->getProductId()]);
                if (!$item->verifyStock() || $item->verifyNotification()) {
                    $fullSaveItems[] = clone $item;
                }
            }

            $this->_getResource()->correctItemsQty($this, $qtys, '-');
            $this->_getResource()->commit();
        } catch (Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }

        return $fullSaveItems;
    }

    /**
     *
     * @param array $items
     * @return Mage_CatalogInventory_Model_Stock
     */
    public function revertProductsSale($items)
    {
        $qtys = $this->_prepareProductQtys($items);
        $this->_getResource()->correctItemsQty($this, $qtys, '+');
        return $this;
    }

    /**
     * Subtract ordered qty for product
     *
     * @return  Mage_CatalogInventory_Model_Stock
     */
    public function registerItemSale(Varien_Object $item)
    {
        $productId = $item->getProductId();
        if ($productId) {
            $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
            if (Mage::helper('cataloginventory')->isQty($stockItem->getTypeId())) {
                if ($item->getStoreId()) {
                    $stockItem->setStoreId($item->getStoreId());
                }

                if ($stockItem->checkQty($item->getQtyOrdered()) || Mage::app()->getStore()->isAdmin()) {
                    $stockItem->subtractQty($item->getQtyOrdered());
                    $stockItem->save();
                }
            }
        } else {
            Mage::throwException(Mage::helper('cataloginventory')->__('Cannot specify product identifier for the order item.'));
        }

        return $this;
    }

    /**
     * Get back to stock (when order is canceled or whatever else)
     *
     * @param int $productId
     * @param float $qty
     * @return $this
     */
    public function backItemQty($productId, $qty)
    {
        $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
        if ($stockItem->getId() && Mage::helper('cataloginventory')->isQty($stockItem->getTypeId())) {
            $stockItem->addQty($qty);
            if ($stockItem->getCanBackInStock() && $stockItem->getQty() > $stockItem->getMinQty()) {
                $stockItem->setIsInStock(true)
                    ->setStockStatusChangedAutomaticallyFlag(true);
            }

            $stockItem->save();
        }

        return $this;
    }

    /**
     * Lock stock items for product ids array
     *
     * @param   array $productIds
     * @return  Mage_CatalogInventory_Model_Stock
     */
    public function lockProductItems($productIds)
    {
        $this->_getResource()->lockProductItems($this, $productIds);
        return $this;
    }

    /**
     * Adds filtering for collection to return only in stock products
     *
     * @param Mage_Catalog_Model_Resource_Product_Link_Product_Collection $collection
     * @return $this $this
     */
    public function addInStockFilterToCollection($collection)
    {
        $this->getResource()->setInStockFilterToCollection($collection);
        return $this;
    }
}
