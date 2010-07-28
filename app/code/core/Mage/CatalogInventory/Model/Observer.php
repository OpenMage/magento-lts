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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog inventory module observer
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogInventory_Model_Observer
{
    /**
     * Product qty's checked
     * data is valid if you check quote item qty and use singleton instance
     *
     * @var array
     */
    protected $_checkedProductsQty = array();

    protected $_itemsForReindex = array();

    /**
     * Add stock information to product
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_CatalogInventory_Model_Observer
     */
    public function addInventoryData($observer)
    {
        $product = $observer->getEvent()->getProduct();
        if ($product instanceof Mage_Catalog_Model_Product) {
            Mage::getModel('cataloginventory/stock_item')->assignProduct($product);
        }
        return $this;
    }

    /**
     * Add information about producs stock status to collection
     * Used in for product collection after load
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_CatalogInventory_Model_Observer
     */
    public function addStockStatusToCollection($observer)
    {
        $productCollection = $observer->getEvent()->getCollection();
        if ($productCollection->hasFlag('require_stock_items')) {
            Mage::getModel('cataloginventory/stock')->addItemsToProducts($productCollection);
        } else {
            Mage::getModel('cataloginventory/stock_status')->addStockStatusToProducts($productCollection);
        }
        return $this;
    }

    /**
     * Add Stock items to product collection
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_CatalogInventory_Model_Observer
     */
    public function addInventoryDataToCollection($observer)
    {
        $productCollection = $observer->getEvent()->getProductCollection();
        Mage::getModel('cataloginventory/stock')->addItemsToProducts($productCollection);
        return $this;
    }

    /**
     * Saving product inventory data. Product qty calculated dynamically.
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_CatalogInventory_Model_Observer
     */
    public function saveInventoryData($observer)
    {
        $product = $observer->getEvent()->getProduct();

        if (is_null($product->getStockData())) {
            if ($product->getIsChangedWebsites() || $product->dataHasChangedFor('status')) {
                Mage::getSingleton('cataloginventory/stock_status')
                    ->updateStatus($product->getId());
            }
            return $this;
        }

        $item = $product->getStockItem();
        if (!$item) {
            $item = Mage::getModel('cataloginventory/stock_item');
        }
        $this->_prepareItemForSave($item, $product);
        $item->save();
        return $this;
    }

    /**
     * Copy product inventory data (used for product duplicate functionality)
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_CatalogInventory_Model_Observer
     */
    public function copyInventoryData($observer)
    {
        $newProduct = $observer->getEvent()->getNewProduct();

        $newProduct->unsStockItem();
        $newProduct->setStockData(array(
            'use_config_min_qty'        => 1,
            'use_config_min_sale_qty'   => 1,
            'use_config_max_sale_qty'   => 1,
            'use_config_backorders'     => 1,
            'use_config_notify_stock_qty'=> 1
        ));

        return $this;
    }

    /**
     * Prepare stock item data for save
     *
     * @param Mage_CatalogInventory_Model_Stock_Item $item
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_CatalogInventory_Model_Observer
     */
    protected function _prepareItemForSave($item, $product)
    {
        $item->addData($product->getStockData())
            ->setProduct($product)
            ->setProductId($product->getId())
            ->setStockId($item->getStockId());
        if (!is_null($product->getData('stock_data/min_qty'))
            && is_null($product->getData('stock_data/use_config_min_qty'))) {
            $item->setData('use_config_min_qty', false);
        }
        if (!is_null($product->getData('stock_data/min_sale_qty'))
            && is_null($product->getData('stock_data/use_config_min_sale_qty'))) {
            $item->setData('use_config_min_sale_qty', false);
        }
        if (!is_null($product->getData('stock_data/max_sale_qty'))
            && is_null($product->getData('stock_data/use_config_max_sale_qty'))) {
            $item->setData('use_config_max_sale_qty', false);
        }
        if (!is_null($product->getData('stock_data/backorders'))
            && is_null($product->getData('stock_data/use_config_backorders'))) {
            $item->setData('use_config_backorders', false);
        }
        if (!is_null($product->getData('stock_data/notify_stock_qty'))
            && is_null($product->getData('stock_data/use_config_notify_stock_qty'))) {
            $item->setData('use_config_notify_stock_qty', false);
        }
        $originalQty = $product->getData('stock_data/original_inventory_qty');
        if (strlen($originalQty)>0) {
            $item->setQtyCorrection($item->getQty()-$originalQty);
        }
        if (!is_null($product->getData('stock_data/enable_qty_increments'))
            && is_null($product->getData('stock_data/use_config_enable_qty_increments'))) {
            $item->setData('use_config_enable_qty_increments', false);
        }
        if (!is_null($product->getData('stock_data/qty_increments'))
            && is_null($product->getData('stock_data/use_config_qty_increments'))) {
            $item->setData('use_config_qty_increments', false);
        }
        return $this;

    }

    /**
     * Check product inventory data when quote item quantity declaring
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_CatalogInventory_Model_Observer
     */
    public function checkQuoteItemQty($observer)
    {
        $quoteItem = $observer->getEvent()->getItem();
        /* @var $quoteItem Mage_Sales_Model_Quote_Item */
        if (!$quoteItem || !$quoteItem->getProductId() || $quoteItem->getQuote()->getIsSuperMode()) {
            return $this;
        }

        /**
         * Get Qty
         */
        $qty = $quoteItem->getQty();

        /**
         * Check item for options
         */
        if (($options = $quoteItem->getQtyOptions()) && $qty > 0) {
            $qty = $quoteItem->getProduct()->getTypeInstance(true)->prepareQuoteItemQty($qty, $quoteItem->getProduct());
            $quoteItem->setData('qty', $qty);

            $stockItem = $quoteItem->getProduct()->getStockItem();
            if ($stockItem) {
                $result = $stockItem->checkQtyIncrements($qty);
                if ($result->getHasError()) {
                    $quoteItem->setHasError(true)
                        ->setMessage($result->getMessage());
                    $quoteItem->getQuote()->setHasError(true)
                        ->addMessage($result->getQuoteMessage(), $result->getQuoteMessageIndex());
                }
            }

            foreach ($options as $option) {
                /* @var $option Mage_Sales_Model_Quote_Item_Option */
                $optionQty = $qty * $option->getValue();
                $increaseOptionQty = ($quoteItem->getQtyToAdd() ? $quoteItem->getQtyToAdd() : $qty) * $option->getValue();

                $stockItem = $option->getProduct()->getStockItem();
                /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
                if (!$stockItem instanceof Mage_CatalogInventory_Model_Stock_Item) {
                    Mage::throwException(Mage::helper('cataloginventory')->__('The stock item for Product in option is not valid.'));
                }
                /**
                 * don't check qty increments value for option product
                 */
                $stockItem->setSuppressCheckQtyIncrements(true);

                $qtyForCheck = $this->_getProductQtyForCheck($option->getProduct()->getId(), $increaseOptionQty);

                $result = $stockItem->checkQuoteItemQty($optionQty, $qtyForCheck, $option->getValue());

                if (!is_null($result->getItemIsQtyDecimal())) {
                    $option->setIsQtyDecimal($result->getItemIsQtyDecimal());
                }

                if ($result->getHasQtyOptionUpdate()) {
                    $option->setHasQtyOptionUpdate(true);
                    $quoteItem->updateQtyOption($option, $result->getOrigQty());
                    $option->setValue($result->getOrigQty());
                    /**
                     * if option's qty was updates we also need to update quote item qty
                     */
                    $quoteItem->setData('qty', intval($qty));
                }
                if (!is_null($result->getMessage())) {
                    $option->setMessage($result->getMessage());
                }
                if (!is_null($result->getItemBackorders())) {
                    $option->setBackorders($result->getItemBackorders());
                }

                if ($result->getHasError()) {
                    $option->setHasError(true);
                    $quoteItem->setHasError(true)
                        ->setMessage($result->getQuoteMessage());
                    $quoteItem->getQuote()->setHasError(true)
                        ->addMessage($result->getQuoteMessage(), $result->getQuoteMessageIndex());
                }
            }
        }
        else {
            $stockItem = $quoteItem->getProduct()->getStockItem();
            /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
            if (!$stockItem instanceof Mage_CatalogInventory_Model_Stock_Item) {
                Mage::throwException(Mage::helper('cataloginventory')->__('The stock item for Product is not valid.'));
            }


            /**
             * When we work with subitem (as subproduct of bundle or configurable product)
             */
            if ($quoteItem->getParentItem()) {
                $rowQty = $quoteItem->getParentItem()->getQty()*$qty;
                /**
                 * we are using 0 because original qty was processed
                 */
                $qtyForCheck = $this->_getProductQtyForCheck($quoteItem->getProduct()->getId(), 0);
            }
            else {
                $increaseQty = $quoteItem->getQtyToAdd() ? $quoteItem->getQtyToAdd() : $qty;
                $rowQty = $qty;
                $qtyForCheck = $this->_getProductQtyForCheck($quoteItem->getProduct()->getId(), $increaseQty);
            }

            $result = $stockItem->checkQuoteItemQty($rowQty, $qtyForCheck, $qty);

            if (!is_null($result->getItemIsQtyDecimal())) {
                $quoteItem->setIsQtyDecimal($result->getItemIsQtyDecimal());
                if ($quoteItem->getParentItem()) {
                    $quoteItem->getParentItem()->setIsQtyDecimal($result->getItemIsQtyDecimal());
                }
            }

            /**
             * Just base (parent) item qty can be changed
             * qty of child products are declared just during add process
             * exception for updating also managed by product type
             */
            if ($result->getHasQtyOptionUpdate()
                && (!$quoteItem->getParentItem()
                    || $quoteItem->getParentItem()->getProduct()->getTypeInstance(true)
                        ->getForceChildItemQtyChanges($quoteItem->getParentItem()->getProduct()))) {
                $quoteItem->setData('qty', $result->getOrigQty());
            }

            if (!is_null($result->getItemUseOldQty())) {
                $quoteItem->setUseOldQty($result->getItemUseOldQty());
            }
            if (!is_null($result->getMessage())) {
                $quoteItem->setMessage($result->getMessage());
                if ($quoteItem->getParentItem()) {
                    $quoteItem->getParentItem()->setMessage($result->getMessage());
                }
            }
            if (!is_null($result->getItemBackorders())) {
                $quoteItem->setBackorders($result->getItemBackorders());
            }

            if ($result->getHasError()) {
                $quoteItem->setHasError(true);
                $quoteItem->getQuote()->setHasError(true)
                    ->addMessage($result->getQuoteMessage(), $result->getQuoteMessageIndex());
            }
        }

        return $this;
    }

    /**
     * Get product qty includes information from all quote items
     * Need be used only in sungleton mode
     *
     * @param int $productId
     * @param float $itemQty
     */
    protected function _getProductQtyForCheck($productId, $itemQty)
    {
        $qty = $itemQty;
        if (isset($this->_checkedProductsQty[$productId])) {
            $qty += $this->_checkedProductsQty[$productId];
        }
        $this->_checkedProductsQty[$productId] = $qty;
        return $qty;
    }

    /**
     * Subtrack quote items qtys from stock items related with quote items products.
     * Used before order placing to make order save/place transaction smaller
     *
     * @param Varien_Event_Observer $observer
     */
    public function subtractQuoteInventory(Varien_Event_Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $items = $this->_getProductsQty($quote->getAllItems());
        /**
         * Remember items
         */
        $this->_itemsForReindex = Mage::getSingleton('cataloginventory/stock')->registerProductsSale($items);
        return $this;
    }

    /**
     * Revert quote items inventory data (cover not success order place case)
     * @param $observer
     */
    public function revertQuoteInventory($observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $items = $this->_getProductsQty($quote->getAllItems());
        Mage::getSingleton('cataloginventory/stock')->revertProductsSale($items);
    }

    /**
     * Prepare array with iformation about used product qty and product stock item
     * result is:
     * array(
     *  $productId  => array(
     *      'qty'   => $qty,
     *      'item'  => $stockItems|null
     *  )
     * )
     * @param array $relatedItems
     * @return array
     */
    protected function _getProductsQty($relatedItems)
    {
        $items = array();
        foreach ($relatedItems as $item) {
            $productId  = $item->getProductId();
            if (!$productId) {
                continue;
            }
            $children   = $item->getChildrenItems();
            if ($children) {
                foreach ($children as $childItem) {
                    $childProductId = $childItem->getProductId();
                    if (!$childProductId) {
                        continue;
                    }
                    $childStockItem = null;
                    if ($childItem->getProduct()) {
                        $childStockItem = $childItem->getProduct()->getStockItem();
                    }
                    if (isset($item[$childProductId])) {
                        $items[$childProductId]['qty'] += $childItem->getTotalQty();
                    } else {
                        $items[$childProductId] = array(
                            'item'=> $childStockItem,
                            'qty' => $childItem->getTotalQty()
                        );
                    }
                }
            } else {
                $stockItem = null;
                if ($item->getProduct()) {
                    $stockItem = $item->getProduct()->getStockItem();
                }
                if (isset($item[$productId])) {
                    $items[$productId]['qty'] += $item->getTotalQty();
                } else {
                    $items[$productId] = array(
                        'item'=> $stockItem,
                        'qty' => $item->getTotalQty()
                    );
                }
            }
        }
        return $items;
    }

    /**
     * Refresh stock index for specific stock items after succesful order placement
     *
     * @param $observer
     */
    public function reindexQuoteInventory($observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $productIds = array();
        foreach ($quote->getAllItems() as $item) {
            $productIds[$item->getProductId()] = $item->getProductId();
            $children   = $item->getChildrenItems();
            if ($children) {
                foreach ($children as $childItem) {
                    $productIds[$childItem->getProductId()] = $childItem->getProductId();
                }
            }
        }
        Mage::getResourceSingleton('cataloginventory/indexer_stock')->reindexProducts($productIds);
        $productIds = array();
        foreach ($this->_itemsForReindex as $item) {
            $item->save();
            $productIds[] = $item->getProductId();
        }
        Mage::getResourceSingleton('catalog/product_indexer_price')->reindexProductIds($productIds);
        return $this;
    }

    /**
     * Return creditmemo items qty to stock
     *
     * @param Varien_Event_Observer $observer
     */
    public function refundOrderInventory($observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $items = array();
        foreach ($creditmemo->getAllItems() as $item) {
            $return = false;
            if ($item->hasBackToStock()) {
                if ($item->getBackToStock() && $item->getQty()) {
                    $return = true;
                }
            } elseif (Mage::helper('cataloginventory')->isAutoReturnEnabled()) {
                $return = true;
            }
            if ($return) {
                if (isset($items[$item->getProductId()])) {
                    $items[$item->getProductId()]['qty'] += $item->getQty();
                } else {
                    $items[$item->getProductId()] = array(
                        'qty' => $item->getQty(),
                        'item'=> null,
                    );
                }
            }
        }
        Mage::getSingleton('cataloginventory/stock')->revertProductsSale($items);
    }

    /**
     * Cancel order item
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_CatalogInventory_Model_Observer
     */
    public function cancelOrderItem($observer)
    {
        $item = $observer->getEvent()->getItem();

        $children = $item->getChildrenItems();
        $qty = $item->getQtyOrdered() - max($item->getQtyShipped(), $item->getQtyInvoiced()) - $item->getQtyCanceled();

        if ($item->getId() && ($productId = $item->getProductId()) && empty($children) && $qty) {
            Mage::getSingleton('cataloginventory/stock')->backItemQty($productId, $qty);
        }

        return $this;
    }

    /**
     * Update items stock status and low stock date.
     *
     * @param Varien_Event_Observer $observer
     * @return  Mage_CatalogInventory_Model_Observer
     */
    public function updateItemsStockUponConfigChange($observer)
    {
        Mage::getResourceSingleton('cataloginventory/stock')->updateSetOutOfStock();
        Mage::getResourceSingleton('cataloginventory/stock')->updateSetInStock();
        Mage::getResourceSingleton('cataloginventory/stock')->updateLowStockDate();
        return $this;
    }

    /**
     * Update Only product status observer
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_CatalogInventory_Model_Observer
     */
    public function productStatusUpdate(Varien_Event_Observer $observer)
    {
        $productId = $observer->getEvent()->getProductId();
        Mage::getSingleton('cataloginventory/stock_status')
            ->updateStatus($productId);
        return $this;
    }

    /**
     * Catalog Product website update
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_CatalogInventory_Model_Observer
     */
    public function catalogProductWebsiteUpdate(Varien_Event_Observer $observer)
    {
        $websiteIds = $observer->getEvent()->getWebsiteIds();
        $productIds = $observer->getEvent()->getProductIds();

        foreach ($websiteIds as $websiteId) {
            foreach ($productIds as $productId) {
                Mage::getSingleton('cataloginventory/stock_status')
                    ->updateStatus($productId, null, $websiteId);
            }
        }

        return $this;
    }

    /**
     * Add stock status to prepare index select
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_CatalogInventory_Model_Observer
     */
    public function addStockStatusToPrepareIndexSelect(Varien_Event_Observer $observer)
    {
        $website    = $observer->getEvent()->getWebsite();
        $select     = $observer->getEvent()->getSelect();

        Mage::getSingleton('cataloginventory/stock_status')
            ->addStockStatusToSelect($select, $website);

        return $this;
    }

    /**
     * Add stock status limitation to catalog product price index select object
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_CatalogInventory_Model_Observer
     */
    public function prepareCatalogProductIndexSelect(Varien_Event_Observer $observer)
    {
        $select     = $observer->getEvent()->getSelect();
        $entity     = $observer->getEvent()->getEntityField();
        $website    = $observer->getEvent()->getWebsiteField();

        Mage::getSingleton('cataloginventory/stock_status')
            ->prepareCatalogProductIndexSelect($select, $entity, $website);

        return $this;
    }








    /**
     * Lock DB rows for order products
     *
     * We need do it for resolving problems with inventory on placing
     * some orders in one time
     * @deprecated after 1.4
     * @param   Varien_Event_Observer $observer
     * @return  Mage_CatalogInventory_Model_Observer
     */
    public function lockOrderInventoryData($observer)
    {
        $order = $observer->getEvent()->getOrder();
        $productIds = array();

        /**
         * Do lock only for new order
         */
        if ($order->getId()) {
            return $this;
        }

        if ($order) {
            foreach ($order->getAllItems() as $item) {
                $productIds[] = $item->getProductId();
            }
        }

        if (!empty($productIds)) {
            Mage::getSingleton('cataloginventory/stock')->lockProductItems($productIds);
        }

        return $this;
    }

    /**
     * Register saving order item
     *
     * @deprecated after 1.4
     * @param   Varien_Event_Observer $observer
     * @return  Mage_CatalogInventory_Model_Observer
     */
    public function createOrderItem($observer)
    {
        $item = $observer->getEvent()->getItem();
        /**
         * Before creating order item need subtract ordered qty from product stock
         */

        $children = $item->getChildrenItems();

        if (!$item->getId() && empty($children)) {
            Mage::getSingleton('cataloginventory/stock')->registerItemSale($item);
        }

        return $this;
    }

    /**
     * Back refunded item qty to stock
     *
     * @deprecated after 1.4
     * @param   Varien_Event_Observer $observer
     * @return  Mage_CatalogInventory_Model_Observer
     */
    public function refundOrderItem($observer)
    {
        $item = $observer->getEvent()->getCreditmemoItem();
        if ($item->getId() && $item->getBackToStock() && ($productId = $item->getProductId()) && ($qty = $item->getQty())) {
            Mage::getSingleton('cataloginventory/stock')->backItemQty($productId, $qty);
        }
        return $this;
    }

}
