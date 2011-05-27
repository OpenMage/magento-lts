<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogInventory
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Stock model
 *
 * @method Mage_CatalogInventory_Model_Resource_Stock _getResource()
 * @method Mage_CatalogInventory_Model_Resource_Stock getResource()
 * @method string getStockName()
 * @method Mage_CatalogInventory_Model_Stock setStockName(string $value)
 *
 * @category    Mage
 * @package     Mage_CatalogInventory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogInventory_Model_Stock extends Mage_Core_Model_Abstract
{
    const BACKORDERS_NO             = 0;
    const BACKORDERS_YES_NONOTIFY   = 1;
    const BACKORDERS_YES_NOTIFY     = 2;

    /* deprecated */
    const BACKORDERS_BELOW          = 1;
    const BACKORDERS_YES            = 2;

    const STOCK_OUT_OF_STOCK        = 0;
    const STOCK_IN_STOCK            = 1;

    const DEFAULT_STOCK_ID          = 1;

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
     * @param   collection $products
     * @return  Mage_CatalogInventory_Model_Stock
     */
    public function addItemsToProducts($productCollection)
    {
        $items = $this->getItemCollection()
            ->addProductsFilter($productCollection)
            ->joinStockStatus($productCollection->getStoreId())
            ->load();
        $stockItems = array();
        foreach ($items as $item) {
            $stockItems[$item->getProductId()] = $item;
        }
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
     * @return unknown
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
     */
    protected function _prepareProductQtys($items)
    {
        $qtys = array();
        foreach ($items as $productId => $item) {
            if (empty($item['item'])) {
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
            } else {
                $stockItem = $item['item'];
            }
            $canSubtractQty = $stockItem->getId() && $stockItem->canSubtractQty();
            if ($canSubtractQty && Mage::helper('catalogInventory')->isQty($stockItem->getTypeId())) {
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
        $stockInfo = $this->_getResource()->getProductsStock($this, array_keys($qtys), true);
        $fullSaveItems = array();
        foreach ($stockInfo as $itemInfo) {
            $item->setData($itemInfo);
            if (!$item->checkQty($qtys[$item->getProductId()])) {
                $this->_getResource()->commit();
                Mage::throwException(Mage::helper('cataloginventory')->__('Not all products are available in the requested quantity'));
            }
            $item->subtractQty($qtys[$item->getProductId()]);
            if (!$item->verifyStock() || $item->verifyNotification()) {
                $fullSaveItems[] = clone $item;
            }
        }
        $this->_getResource()->correctItemsQty($this, $qtys, '-');
        $this->_getResource()->commit();
        return $fullSaveItems;
    }

    /**
     *
     * @param unknown_type $items
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
     * @param   Varien_Object $item
     * @return  Mage_CatalogInventory_Model_Stock
     */
    public function registerItemSale(Varien_Object $item)
    {
        $productId = $item->getProductId();
        if ($productId) {
            $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
            if (Mage::helper('catalogInventory')->isQty($stockItem->getTypeId())) {
                if ($item->getStoreId()) {
                    $stockItem->setStoreId($item->getStoreId());
                }
                if ($stockItem->checkQty($item->getQtyOrdered()) || Mage::app()->getStore()->isAdmin()) {
                    $stockItem->subtractQty($item->getQtyOrdered());
                    $stockItem->save();
                }
            }
        }
        else {
            Mage::throwException(Mage::helper('cataloginventory')->__('Cannot specify product identifier for the order item.'));
        }
        return $this;
    }

    /**
     * Get back to stock (when order is canceled or whatever else)
     *
     * @param int $productId
     * @param numeric $qty
     * @return Mage_CatalogInventory_Model_Stock
     */
    public function backItemQty($productId, $qty)
    {
        $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
        if ($stockItem->getId() && Mage::helper('catalogInventory')->isQty($stockItem->getTypeId())) {
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
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Link_Product_Collection $collection
     * @return Mage_CatalogInventory_Model_Stock $this
     */
    public function addInStockFilterToCollection($collection)
    {
        $this->getResource()->setInStockFilterToCollection($collection);
        return $this;
    }
}
