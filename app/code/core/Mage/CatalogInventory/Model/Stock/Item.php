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
    const XML_PATH_MIN_QTY              = 'cataloginventory/options/min_qty';
    const XML_PATH_MIN_SALE_QTY         = 'cataloginventory/options/min_sale_qty';
    const XML_PATH_MAX_SALE_QTY         = 'cataloginventory/options/max_sale_qty';
    const XML_PATH_BACKORDERS           = 'cataloginventory/options/backorders';
    const XML_PATH_CAN_SUBTRACT         = 'cataloginventory/options/can_subtract';
    const XML_PATH_CAN_BACK_IN_STOCK    = 'cataloginventory/options/can_back_in_stock';
    const XML_PATH_NOTIFY_STOCK_QTY     = 'cataloginventory/options/notify_stock_qty';
    const XML_PATH_MANAGE_STOCK         = 'cataloginventory/options/manage_stock';

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

    public function getNotifyStockQty()
    {
        if ($this->getUseConfigNotifyStockQty()) {
            return (float) Mage::getStoreConfig(self::XML_PATH_NOTIFY_STOCK_QTY);
        }
        return $this->getData('notify_stock_qty');
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
                case Mage_CatalogInventory_Model_Stock::BACKORDERS_BELOW:
                case Mage_CatalogInventory_Model_Stock::BACKORDERS_YES:
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
     * @param   mixed $qty
     * @return  Varien_Object
     */
    public function checkQuoteItemQty($qty, $summaryQty)
    {
        $result = new Varien_Object();
        $result->setHasError(false);

        $helper = Mage::helper('cataloginventory');

        if (!is_numeric($qty)) {
            $qty = Mage::app()->getLocale()->getNumber($qty);
        }

        if (!$this->getManageStock()) {
            /**
             * Check quantity type
             */
            $result->setItemIsQtyDecimal($this->getIsQtyDecimal());

            if (!$this->getIsQtyDecimal()) {
                $qty = intval($qty);
            }

            /**
             * Adding stock data to quote item
             */
            $result->setItemQty($qty);

            return $result;
        }

        if (!$this->getIsInStock()) {
            $result->setHasError(true)
                ->setMessage($helper->__('This product is currently out of stock.'))
                ->setQuoteMessage($helper->__('Some of the products are currently out of stock'))
                ->setQuoteMessageIndex('stock');
            $result->setItemUseOldQty(true);
            return $result;
        }

        if ($this->getMinSaleQty() && $summaryQty < $this->getMinSaleQty()) {
            $result->setHasError(true)
                ->setMessage($helper->__('The minimum quantity allowed for purchase is %s.', $this->getMinSaleQty() * 1))
                ->setQuoteMessage($helper->__('Some of the products cannot be ordered in the requested quantity'))
                ->setQuoteMessageIndex('qty');
            return $result;
        }

        if ($this->getMaxSaleQty() && $summaryQty>$this->getMaxSaleQty()) {
            $result->setHasError(true)
                ->setMessage($helper->__('The maximum quantity allowed for purchase is %s.', $this->getMaxSaleQty() * 1))
                ->setQuoteMessage($helper->__('Some of the products can not be ordered in requested quantity'))
                ->setQuoteMessageIndex('qty');
            return $result;
        }

        if ($this->checkQty($summaryQty)) {
            if (($this->getQty() - $summaryQty < 0) && ($this->getBackorders() == Mage_CatalogInventory_Model_Stock::BACKORDERS_YES)) {
                if ($this->getProduct()) {
                    $result->setMessage('set_data', $helper->__('This product is not available in the requested quantity. %s of the items will be backordered.',
                        ($this->getQty() > 0) ? ($qty - $this->getQty()) * 1 : $qty * 1,
                        $this->getProduct()->getName()));
                }
            }
        }
        else {
            $result->setHasError(true)
                ->setMessage($helper->__('The requested quantity is not available'))
                ->setQuoteMessage($helper->__('The requested quantity for "%s" is not available.', $this->getProduct()->getName()))
                ->setQuoteMessageIndex('qty');
            return $result;
        }

        /**
         * Check quantity type
         */

        $result->setItemIsQtyDecimal($this->getIsQtyDecimal());

        if (!$this->getIsQtyDecimal()) {
            $qty = intval($qty);
        }

        /**
         * Adding stock data to quote item
         */
        $result->setItemQty($qty);
        $result->setItemBackorders($qty);

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
        if ($this->getBackorders() == Mage_CatalogInventory_Model_Stock::BACKORDERS_NO
            && $this->getQty() <= $this->getMinQty()) {
            if(!$this->getProduct() || !$this->getProduct()->isComposite()) {
                $this->setIsInStock(false);
            }
        }

        /**
         * if qty is below notify qty, update the low stock date to today date otherwise set null
         */
        if ($this->getNotifyStockQty() && $this->getQty()<$this->getNotifyStockQty()
            && (!$this->getProduct() || !$this->getProduct()->isSuper())) {
            $this->setLowStockDate($this->_getResource()->formatDate(time()));
        } else {
            $this->setLowStockDate(false);
        }

        Mage::dispatchEvent('cataloginventory_stock_item_save_before', array('item'=>$this));
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
