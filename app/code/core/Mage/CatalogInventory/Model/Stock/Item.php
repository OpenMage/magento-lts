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
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Stock item model
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogInventory_Model_Stock_Item extends Mage_Core_Model_Abstract
{
    const XML_PATH_GLOBAL            = 'cataloginventory/options/';
    const XML_PATH_CAN_SUBTRACT      = 'cataloginventory/options/can_subtract';
    const XML_PATH_CAN_BACK_IN_STOCK = 'cataloginventory/options/can_back_in_stock';

    const XML_PATH_ITEM             = 'cataloginventory/item_options/';
    const XML_PATH_MIN_QTY          = 'cataloginventory/item_options/min_qty';
    const XML_PATH_MIN_SALE_QTY     = 'cataloginventory/item_options/min_sale_qty';
    const XML_PATH_MAX_SALE_QTY     = 'cataloginventory/item_options/max_sale_qty';
    const XML_PATH_BACKORDERS       = 'cataloginventory/item_options/backorders';
    const XML_PATH_NOTIFY_STOCK_QTY = 'cataloginventory/item_options/notify_stock_qty';
    const XML_PATH_MANAGE_STOCK     = 'cataloginventory/item_options/manage_stock';

    // cataloginventory/cart_options/...

    protected function _construct()
    {
        $this->_init('cataloginventory/stock_item');
    }

    /**
     * Retrieve stock identifier
     *
     * @todo multi stock
     * @return int
     */
    public function getStockId()
    {
        return 1;
    }

    public function getProductId()
    {
        return $this->_getData('product_id');
    }

    /**
     * Load item data by product
     *
     * @param   mixed $product
     * @return  Mage_CatalogInventory_Model_Stock_Item
     */
    public function loadByProduct($product)
    {
        if ($product instanceof Mage_Catalog_Model_Product) {
            $product = $product->getId();
        }
        $this->_getResource()->loadByProductId($this, $product);
        $this->setOrigData();
        return $this;
    }

    /**
     * Subtract quote item quantity
     *
     * @param   decimal $qty
     * @return  Mage_CatalogInventory_Model_Stock_Item
     */
    public function subtractQty($qty)
    {
        if (!$this->getManageStock()) {
            return $this;
        }
        $config = Mage::getStoreConfigFlag(self::XML_PATH_CAN_SUBTRACT);
        if (!$config) {
            return $this;
        }
        $this->setQty($this->getQty()-$qty);
        return $this;
    }

    public function addQty($qty)
    {
        if (!$this->getManageStock()) {
            return $this;
        }
        $config = Mage::getStoreConfigFlag(self::XML_PATH_CAN_SUBTRACT);
        if (!$config) {
            return $this;
        }

        $this->setQty($this->getQty()+$qty);
        return $this;
    }

    public function getStoreId()
    {
        $storeId = $this->getData('store_id');
        if (is_null($storeId)) {
            if ($this->getProduct()) {
                $storeId = $this->getProduct()->getStoreId();
            }
            else {
                $storeId = Mage::app()->getStore()->getId();
            }
            $this->setData('store_id', $storeId);
        }
        return $storeId;
    }

    /**
     * Adding stoc data to product
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_CatalogInventory_Model_Stock_Item
     */
    public function assignProduct(Mage_Catalog_Model_Product $product)
    {
        if (!$this->getId() || !$this->getProductId()) {
            $this->_getResource()->loadByProductId($this, $product->getId());
        }

        $product->setStockItem($this);
        $this->setProduct($product);
        $product->setIsSalable($this->getIsInStock());
        return $this;
    }

    /**
     * Retrieve minimal quantity available for item status in stock
     *
     * @return decimal
     */
    public function getMinQty()
    {
        if ($this->getUseConfigMinQty()) {
            return (float) Mage::getStoreConfig(self::XML_PATH_MIN_QTY);
        }
        return $this->getData('min_qty');
    }

    public function getMinSaleQty()
    {
        if ($this->getUseConfigMinSaleQty()) {
            return (float) Mage::getStoreConfig(self::XML_PATH_MIN_SALE_QTY);
        }
        return $this->getData('min_sale_qty');
    }

    public function getMaxSaleQty()
    {
        if ($this->getUseConfigMaxSaleQty()) {
            return (float) Mage::getStoreConfig(self::XML_PATH_MAX_SALE_QTY);
        }
        return $this->getData('max_sale_qty');
    }

    /**
     *
     * @return float
     */
    public function getNotifyStockQty()
    {
        if ($this->getUseConfigNotifyStockQty()) {
            return (float) Mage::getStoreConfig(self::XML_PATH_NOTIFY_STOCK_QTY);
        }
        return (float)$this->getData('notify_stock_qty');
    }

    /**
     * Retrieve backorders status
     *
     * @return int
     */
    public function getBackorders()
    {
        if ($this->getUseConfigBackorders()) {
            return (int) Mage::getStoreConfig(self::XML_PATH_BACKORDERS);
        }
        return $this->getData('backorders');
    }

    public function getManageStock()
    {
        if ($this->getUseConfigManageStock()) {
            return (int) Mage::getStoreConfigFlag(self::XML_PATH_MANAGE_STOCK);
        }
        return $this->getData('manage_stock');
    }

    public function getCanBackInStock()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CAN_BACK_IN_STOCK);
    }

    /**
     * Check quantity
     *
     * @param   decimal $qty
     * @exception Mage_Core_Exception
     * @return  bool
     */
    public function checkQty($qty)
    {
        if ($this->getQty() - $qty < 0) {
            switch ($this->getBackorders()) {
                case Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NONOTIFY:
                case Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NOTIFY:
                    break;
                default:
                    /*if ($this->getProduct()) {
                        Mage::throwException(
                            Mage::helper('cataloginventory')->__('The requested quantity for "%s" is not available.', $this->getProduct()->getName())
                        );
                    }
                    else {
                        Mage::throwException(Mage::helper('cataloginventory')->__('The requested quantity is not available.'));
                    }*/
                    return false;
                    break;
            }
        }
        return true;
    }

    /**
     * Checking quote item quantity
     *
     * @param mixed $qty quantity of this item (item qty x parent item qty)
     * @param mixed $summaryQty quantity of this product in whole shopping cart which should be checked for stock availability
     * @param mixed $origQty original qty of item (not multiplied on parent item qty)
     * @return Varien_Object
     */
    public function checkQuoteItemQty($qty, $summaryQty, $origQty = 0)
    {
        $result = new Varien_Object();
        $result->setHasError(false);

        if (!is_numeric($qty)) {
            $qty = Mage::app()->getLocale()->getNumber($qty);
        }

        /**
         * Check quantity type
         */
        $result->setItemIsQtyDecimal($this->getIsQtyDecimal());

        if (!$this->getIsQtyDecimal()) {
            $result->setHasQtyOptionUpdate(true);
            $qty = intval($qty);

            /**
              * Adding stock data to quote item
              */
            $result->setItemQty($qty);

            if (!is_numeric($qty)) {
                $qty = Mage::app()->getLocale()->getNumber($qty);
            }
            $origQty = intval($origQty);
            $result->setOrigQty($origQty);
        }

        if (!$this->getManageStock()) {
            return $result;
        }

        if (!$this->getIsInStock()) {
            $result->setHasError(true)
                ->setMessage(Mage::helper('cataloginventory')->__('This product is currently out of stock.'))
                ->setQuoteMessage(Mage::helper('cataloginventory')->__('Some of the products are currently out of stock'))
                ->setQuoteMessageIndex('stock');
            $result->setItemUseOldQty(true);
            return $result;
        }

        if ($this->getMinSaleQty() && ($qty) < $this->getMinSaleQty()) {
            $result->setHasError(true)
                ->setMessage(Mage::helper('cataloginventory')->__('The minimum quantity allowed for purchase is %s.', $this->getMinSaleQty() * 1))
                ->setQuoteMessage(Mage::helper('cataloginventory')->__('Some of the products cannot be ordered in the requested quantity'))
                ->setQuoteMessageIndex('qty');
            return $result;
        }

        if ($this->getMaxSaleQty() && ($qty) > $this->getMaxSaleQty()) {
            $result->setHasError(true)
                ->setMessage(Mage::helper('cataloginventory')->__('The maximum quantity allowed for purchase is %s.', $this->getMaxSaleQty() * 1))
                ->setQuoteMessage(Mage::helper('cataloginventory')->__('Some of the products can not be ordered in requested quantity'))
                ->setQuoteMessageIndex('qty');
            return $result;
        }

        if (!$this->checkQty($summaryQty)) {
            $message = Mage::helper('cataloginventory')->__('The requested quantity for "%s" is not available.', $this->getProduct()->getName());
            $result->setHasError(true)
                ->setMessage($message)
                ->setQuoteMessage($message)
                ->setQuoteMessageIndex('qty');
            return $result;
        }
        else {
            if (($this->getQty() - $summaryQty) < 0) {
                if ($this->getProduct()) {
                    $backorderQty = ($this->getQty() > 0) ? ($summaryQty - $this->getQty()) * 1 : $qty * 1;
                    if ($backorderQty>$qty) {
                        $backorderQty = $qty;
                    }
                    $result->setItemBackorders($backorderQty);
                    if ($this->getBackorders() == Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NOTIFY) {
                        $result->setMessage(Mage::helper('cataloginventory')->__('This product is not available in the requested quantity. %d of the items will be backordered.',
                            $backorderQty,
                            $this->getProduct()->getName())
                            )
                        ;
                    }
                }
            }
            // no return intentionally
        }

        return $result;
    }

    /**
     * Add join for catalog in stock field to product collection
     *
     * @param Mage_Catalog_Model_Entity_Product_Collection $productCollection
     * @return Mage_CatalogInventory_Model_Stock_Item
     */
    public function addCatalogInventoryToProductCollection($productCollection)
    {
        $this->_getResource()->addCatalogInventoryToProductCollection($productCollection);
        return $this;
    }

    protected function _addQuoteItemError(Mage_Sales_Model_Quote_Item $item, $itemError, $quoteError, $errorIndex='error')
    {
        $item->setHasError(true);
        $item->setMessage($itemError);
        $item->setQuoteMessage($quoteError);
        $item->setQuoteMessageIndex($errorIndex);
        return $this;
    }

    protected function _beforeSave()
    {
        // see if quantity is defined for this item type
        $typeId = $this->getTypeId();
        if ($product = $this->getProduct()) {
            $typeId = $product->getTypeId();
        }
        $isQty = Mage::helper('catalogInventory')->isQty($typeId);

        if ($isQty) {
            if ($this->getBackorders() == Mage_CatalogInventory_Model_Stock::BACKORDERS_NO
                && $this->getQty() <= $this->getMinQty()) {
                $this->setIsInStock(false)
                    ->setStockStatusChangedAutomaticallyFlag(true);
            }

            // if qty is below notify qty, update the low stock date to today date otherwise set null
            $this->setLowStockDate(null);
            if ((float)$this->getQty() < $this->getNotifyStockQty()) {
                $this->setLowStockDate(Mage::app()->getLocale()->date(null, null, null, false)
                    ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)
                );
            }

            $this->setStockStatusChangedAutomatically(0);
            if ($this->hasStockStatusChangedAutomaticallyFlag()) {
                $this->setStockStatusChangedAutomatically((int)$this->getStockStatusChangedAutomaticallyFlag());
            }
        }
        else {
            $this->setQty(0);
        }

        Mage::dispatchEvent('cataloginventory_stock_item_save_before', array('item' => $this));
        return $this;
    }

    public function getIsInStock()
    {
        if (!$this->getManageStock()) {
            return true;
        }
        return $this->_getData('is_in_stock');
    }
}
