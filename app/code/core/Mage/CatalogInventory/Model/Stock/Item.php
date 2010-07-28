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
 * Catalog Inventory Stock Model
 *
 * @category   Mage
 * @package    Mage_CatalogInvemtory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogInventory_Model_Stock_Item extends Mage_Core_Model_Abstract
{
    const XML_PATH_GLOBAL            = 'cataloginventory/options/';
    const XML_PATH_CAN_SUBTRACT      = 'cataloginventory/options/can_subtract';
    const XML_PATH_CAN_BACK_IN_STOCK = 'cataloginventory/options/can_back_in_stock';

    const XML_PATH_ITEM                  = 'cataloginventory/item_options/';
    const XML_PATH_MIN_QTY               = 'cataloginventory/item_options/min_qty';
    const XML_PATH_MIN_SALE_QTY          = 'cataloginventory/item_options/min_sale_qty';
    const XML_PATH_MAX_SALE_QTY          = 'cataloginventory/item_options/max_sale_qty';
    const XML_PATH_BACKORDERS            = 'cataloginventory/item_options/backorders';
    const XML_PATH_NOTIFY_STOCK_QTY      = 'cataloginventory/item_options/notify_stock_qty';
    const XML_PATH_MANAGE_STOCK          = 'cataloginventory/item_options/manage_stock';
    const XML_PATH_ENABLE_QTY_INCREMENTS = 'cataloginventory/item_options/enable_qty_increments';
    const XML_PATH_QTY_INCREMENTS        = 'cataloginventory/item_options/qty_increments';

    const ENTITY                    = 'cataloginventory_stock_item';

    /**
     * @var array
     */
    private $_minSaleQtyCache = array();

    /**
     * @var float|false
     */
    protected $_qtyIncrements;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'cataloginventory_stock_item';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getItem() in this case
     *
     * @var string
     */
    protected $_eventObject = 'item';

    /**
     * Associated product instance
     *
     * @var Mage_Catalog_Model_Product
     */
    protected $_productInstance = null;

    /**
     * Customer group id
     *
     * @var int|null
     */
    protected $_customerGroupId;

    /**
     * Initialize resource model
     *
     */
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

    /**
     * Retrieve Product Id data wraper
     *
     * @return int
     */
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
        if ($this->canSubtractQty()) {
            $this->setQty($this->getQty()-$qty);
        }
        return $this;
    }

    /**
     * Check if is possible subtract value from item qty
     *
     * @return bool
     */
    public function canSubtractQty()
    {
        return $this->getManageStock() && Mage::getStoreConfigFlag(self::XML_PATH_CAN_SUBTRACT);
    }

    /**
     * Add quantity process
     *
     * @param float $qty
     * @return Mage_CatalogInventory_Model_Stock_Item
     */
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

    /**
     * Retrieve Store Id (product or current)
     *
     * @return int
     */
    public function getStoreId()
    {
        $storeId = $this->getData('store_id');
        if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
            $this->setData('store_id', $storeId);
        }
        return $storeId;
    }

    /**
     * Adding stock data to product
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_CatalogInventory_Model_Stock_Item
     */
    public function assignProduct(Mage_Catalog_Model_Product $product)
    {
        if (!$this->getId() || !$this->getProductId()) {
            $this->_getResource()->loadByProductId($this, $product->getId());
            $this->setOrigData();
        }

        $this->setProduct($product);
        $product->setStockItem($this);

        $product->setIsInStock($this->getIsInStock());
        Mage::getSingleton('cataloginventory/stock_status')
            ->assignProduct($product, $this->getStockId(), $this->getStockStatus());
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

    /**
     * Getter for customer group id
     *
     * @return int
     */
    public function getCustomerGroupId()
    {
        return $this->_customerGroupId;
    }

    /**
     * Setter for customer group id
     *
     * @param int Value of customer group id
     * @return Mage_CatalogInventory_Model_Stock_Item
     */
    public function setCustomerGroupId($value)
    {
        $this->_customerGroupId = $value;
        return $this;
    }

    /**
     * Retrieve Minimum Qty Allowed in Shopping Cart or NULL when there is no limitation
     *
     * @return float|null
     */
    public function getMinSaleQty()
    {
        if ($this->getCustomerGroupId()) {
            $customerGroupId = $this->getCustomerGroupId();
        } else if (Mage::app()->getStore()->isAdmin()) {
            $customerGroupId = Mage_Customer_Model_Group::CUST_GROUP_ALL;
        } else {
            $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        }
        if (!array_key_exists($customerGroupId, $this->_minSaleQtyCache)) {
            $minSaleQty = null;
            if ($this->getUseConfigMinSaleQty()) {
                $minSaleQty = Mage::helper('cataloginventory/minsaleqty')->getConfigValue($customerGroupId);
            } else {
                $minSaleQty = $this->getData('min_sale_qty');
            }
            $minSaleQty = (!empty($minSaleQty) ? (float)$minSaleQty : null);
            $this->_minSaleQtyCache[$customerGroupId] = $minSaleQty;
        }
        return $this->_minSaleQtyCache[$customerGroupId];
    }

    /**
     * Retrieve Maximum Qty Allowed in Shopping Cart data wraper
     *
     * @return unknown
     */
    public function getMaxSaleQty()
    {
        if ($this->getUseConfigMaxSaleQty()) {
            return (float) Mage::getStoreConfig(self::XML_PATH_MAX_SALE_QTY);
        }
        return $this->getData('max_sale_qty');
    }

    /**
     * Retrieve Notify for Quantity Below data wraper
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
     * Retrieve whether Quantity Increments is enabled
     *
     * @return bool
     */
    public function getEnableQtyIncrements()
    {
        if ($this->getUseConfigEnableQtyIncrements()) {
            return Mage::getStoreConfigFlag(self::XML_PATH_ENABLE_QTY_INCREMENTS);
        }
        return (bool)$this->getData('enable_qty_increments');
    }

    /**
     * Retrieve Quantity Increments data wraper
     *
     * @return float|false
     */
    public function getQtyIncrements()
    {
        if ($this->_qtyIncrements === null) {
            $this->_qtyIncrements = false;
            if ($this->getEnableQtyIncrements()) {
                if ($this->getUseConfigQtyIncrements()) {
                    $this->_qtyIncrements = Mage::getStoreConfig(self::XML_PATH_QTY_INCREMENTS);
                } else {
                    $this->_qtyIncrements = $this->getData('qty_increments');
                }
                $this->_qtyIncrements = (float)$this->_qtyIncrements;
                if ($this->_qtyIncrements <= 0) {
                    $this->_qtyIncrements = false;
                }
            }
        }
        return $this->_qtyIncrements;
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

    /**
     * Retrieve Manage Stock data wraper
     *
     * @return int
     */
    public function getManageStock()
    {
        if ($this->getUseConfigManageStock()) {
            return (int) Mage::getStoreConfigFlag(self::XML_PATH_MANAGE_STOCK);
        }
        return $this->getData('manage_stock');
    }

    /**
     * Retrieve can Back in stock
     *
     * @return bool
     */
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
        if (!$this->getManageStock()) {
            return true;
        }
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

        if ($this->getMinSaleQty() && ($qty) < $this->getMinSaleQty()) {
            $result->setHasError(true)
                ->setMessage(Mage::helper('cataloginventory')->__('The minimum quantity allowed for purchase is %s.', $this->getMinSaleQty() * 1))
                ->setQuoteMessage(Mage::helper('cataloginventory')->__('Some of the products cannot be ordered in requested quantity.'))
                ->setQuoteMessageIndex('qty');
            return $result;
        }

        if ($this->getMaxSaleQty() && ($qty) > $this->getMaxSaleQty()) {
            $result->setHasError(true)
                ->setMessage(Mage::helper('cataloginventory')->__('The maximum quantity allowed for purchase is %s.', $this->getMaxSaleQty() * 1))
                ->setQuoteMessage(Mage::helper('cataloginventory')->__('Some of the products cannot be ordered in requested quantity.'))
                ->setQuoteMessageIndex('qty');
            return $result;
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

        $result->addData($this->checkQtyIncrements($qty)->getData());
        if ($result->getHasError()) {
            return $result;
        }

        if (!$this->checkQty($summaryQty)) {
            $message = Mage::helper('cataloginventory')->__('The requested quantity for "%s" is not available.', $this->getProductName());
            $result->setHasError(true)
                ->setMessage($message)
                ->setQuoteMessage($message)
                ->setQuoteMessageIndex('qty');
            return $result;
        }
        else {
            if (($this->getQty() - $summaryQty) < 0) {
                if ($this->getProductName()) {
                    $backorderQty = ($this->getQty() > 0) ? ($summaryQty - $this->getQty()) * 1 : $qty * 1;
                    if ($backorderQty>$qty) {
                        $backorderQty = $qty;
                    }
                    $result->setItemBackorders($backorderQty);
                    if ($this->getBackorders() == Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NOTIFY) {
                        $result->setMessage(Mage::helper('cataloginventory')->__('This product is not available in the requested quantity. %s of the items will be backordered.', ($backorderQty * 1), $this->getProductName()));
                    }
                }
            }
            // no return intentionally
        }

        return $result;
    }

    /**
     * Check qty increments
     *
     * @param int|float $qty
     * @return Varien_Object
     */
    public function checkQtyIncrements($qty)
    {
        $result = new Varien_Object();
        if (!$this->getManageStock() || $this->getSuppressCheckQtyIncrements()) {
            return $result;
        }
        $qtyIncrements = $this->getQtyIncrements();
        if ($qtyIncrements && ($qty % $qtyIncrements != 0)) {
            $result->setHasError(true)
                ->setMessage(Mage::helper('cataloginventory')->__('This product is available for purchase in increments of %s only.', $qtyIncrements * 1))
                ->setQuoteMessage(Mage::helper('cataloginventory')->__('Some of the products cannot be ordered in the requested quantity.'))
                ->setQuoteMessageIndex('qty');
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

    /**
     * Add error to Quote Item
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @param string $itemError
     * @param string $quoteError
     * @param string $errorIndex
     * @return Mage_CatalogInventory_Model_Stock_Item
     */
    protected function _addQuoteItemError(Mage_Sales_Model_Quote_Item $item, $itemError, $quoteError, $errorIndex='error')
    {
        $item->setHasError(true);
        $item->setMessage($itemError);
        $item->setQuoteMessage($quoteError);
        $item->setQuoteMessageIndex($errorIndex);
        return $this;
    }

    /**
     * Before save prepare process
     *
     * @return Mage_CatalogInventory_Model_Stock_Item
     */
    protected function _beforeSave()
    {
        // see if quantity is defined for this item type
        $typeId = $this->getTypeId();
        if ($productTypeId = $this->getProductTypeId()) {
            $typeId = $productTypeId;
        }

        $isQty = Mage::helper('catalogInventory')->isQty($typeId);

        if ($isQty) {
            if (!$this->verifyStock()) {
                $this->setIsInStock(false)
                    ->setStockStatusChangedAutomaticallyFlag(true);
            }

            // if qty is below notify qty, update the low stock date to today date otherwise set null
            $this->setLowStockDate(null);
            if ($this->verifyNotification()) {
                $this->setLowStockDate(Mage::app()->getLocale()->date(null, null, null, false)
                    ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)
                );
            }

            $this->setStockStatusChangedAutomatically(0);
            if ($this->hasStockStatusChangedAutomaticallyFlag()) {
                $this->setStockStatusChangedAutomatically((int)$this->getStockStatusChangedAutomaticallyFlag());
            }
        } else {
            $this->setQty(0);
        }

        return $this;
    }

    /**
     * Chceck if item should be in stock or out of stock based on $qty param of existing item qty
     *
     * @param float|null $qty
     * @return bool true - item in stock | false - item out of stock
     */
    public function verifyStock($qty = null)
    {
        if ($qty === null) {
            $qty = $this->getQty();
        }
        if ($this->getBackorders() == Mage_CatalogInventory_Model_Stock::BACKORDERS_NO && $qty <= $this->getMinQty()) {
            return false;
        }
        return true;
    }

    /**
     * Check if item qty require stock status notification
     *
     * @param float | null $qty
     * @return bool (true - if require, false - if not require)
     */
    public function verifyNotification($qty = null)
    {
        if ($qty === null) {
            $qty = $this->getQty();
        }
        return (float)$qty < $this->getNotifyStockQty();
    }

    /**
     * Process stock status index on item after commit
     *
     * @return Mage_CatalogInventory_Model_Stock_Item
     */
    public function afterCommitCallback()
    {
        parent::afterCommitCallback();
        Mage::getSingleton('index/indexer')->processEntityAction(
            $this, self::ENTITY, Mage_Index_Model_Event::TYPE_SAVE
        );
        return $this;
    }


    /**
     * Retrieve Stock Availability
     *
     * @return bool|int
     */
    public function getIsInStock()
    {
        if (!$this->getManageStock()) {
            return true;
        }
        return $this->_getData('is_in_stock');
    }

    /**
     * Add product data to stock item
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_CatalogInventory_Model_Stock_Item
     */
    public function setProduct($product)
    {
        $this->setProductId($product->getId())
            ->setProductName($product->getName())
            ->setStoreId($product->getStoreId())
            ->setProductName($product->getName())
            ->setProductTypeId($product->getTypeId())
            ->setProductStatusChanged($product->dataHasChangedFor('status'))
            ->setProductChangedWebsites($product->getIsChangedWebsites());

        $this->_productInstance = $product;

        return $this;
    }

    /**
     * Retrieve stock qty whether product is composite or no
     *
     * @return float
     */
    public function getStockQty()
    {
        if (!$this->hasStockQty()) {
            $this->setStockQty(0);  // prevent possible recursive loop
            $product = $this->_productInstance;
            if (!$product || !$product->isComposite()) {
                $stockQty = $this->getQty();
            } else {
                $stockQty = null;
                $productsByGroups = $product->getTypeInstance(true)->getProductsToPurchaseByReqGroups($product);
                foreach ($productsByGroups as $productsInGroup) {
                    $qty = 0;
                    foreach ($productsInGroup as $childProduct) {
                        if ($childProduct->hasStockItem()) {
                            $qty += $childProduct->getStockItem()->getStockQty();
                        }
                    }
                    if (is_null($stockQty) || $qty < $stockQty) {
                        $stockQty = $qty;
                    }
                }
            }
            $stockQty = (float) $stockQty;
            if ($stockQty < 0 || !$this->getManageStock() || !$this->getIsInStock() || ($product && !$product->isSaleable())) {
                $stockQty = 0;
            }
            $this->setStockQty($stockQty);
        }
        return $this->getData('stock_qty');
    }
}
