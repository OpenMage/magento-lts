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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogInventory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Inventory Stock Model
 *
 * @method Mage_CatalogInventory_Model_Resource_Stock_Item _getResource()
 * @method Mage_CatalogInventory_Model_Resource_Stock_Item getResource()
 * @method Mage_CatalogInventory_Model_Resource_Stock_Item_Collection getCollection()
 *
 * @method $this setProductId(int $value)
 * @method $this setStockId(int $value)
 * @method float getQty()
 * @method $this setQty(float $value)
 * @method $this setMinQty(float $value)
 * @method int getUseConfigMinQty()
 * @method $this setUseConfigMinQty(int $value)
 * @method int getIsQtyDecimal()
 * @method $this setIsQtyDecimal(int $value)
 * @method $this setBackorders(int $value)
 * @method int getUseConfigBackorders()
 * @method $this setUseConfigBackorders(int $value)
 * @method $this setMinSaleQty(float $value)
 * @method int getUseConfigMinSaleQty()
 * @method $this setUseConfigMinSaleQty(int $value)
 * @method $this setMaxSaleQty(float $value)
 * @method int getUseConfigMaxSaleQty()
 * @method $this setUseConfigMaxSaleQty(int $value)
 * @method $this setIsInStock(int $value)
 * @method string getLowStockDate()
 * @method $this setLowStockDate(string $value)
 * @method $this setNotifyStockQty(float $value)
 * @method int getUseConfigNotifyStockQty()
 * @method $this setUseConfigNotifyStockQty(int $value)
 * @method $this setManageStock(int $value)
 * @method int getUseConfigManageStock()
 * @method $this setUseConfigManageStock(int $value)
 * @method int getStockStatusChangedAutomatically()
 * @method bool hasStockStatusChangedAutomaticallyFlag()
 * @method int getStockStatusChangedAutomaticallyFlag()
 * @method $this setStockStatusChangedAutomatically(int $value)
 * @method int getUseConfigQtyIncrements()
 * @method $this setUseConfigQtyIncrements(int $value)
 * @method $this setQtyIncrements(float $value)
 * @method int getUseConfigEnableQtyIncrements()
 * @method $this setUseConfigEnableQtyIncrements(int $value)
 * @method $this setEnableQtyIncrements(int $value)
 * @method bool getStockStatus()
 * @method $this setStockStatusChangedAutomaticallyFlag(bool $value)
 * @method int getProductTypeId()
 * @method $this setStoreId(int $value)
 * @method $this setParentItem(Mage_Sales_Model_Quote_Item $value)
 * @method $this setProductChangedWebsites(bool $value)
 * @method string getProductName()
 * @method $this setProductName(string $value)
 * @method $this setProductStatusChanged(bool $value)
 * @method $this setProductTypeId(string $value)
 * @method bool getSuppressCheckQtyIncrements()
 * @method $this setSuppressCheckQtyIncrements(bool $value)
 * @method int getTypeId()
 * @method $this hasIsChildItem()
 * @method bool getIsChildItem()
 * @method $this setIsChildItem(bool $value)
 * @method $this unsIsChildItem()
 * @method float getOrderedItems()
 * @method $this setOrderedItems(float $value)
 * @method $this setStockQty(float $value)
 * @method bool hasStockQty()
 * @method float getQtyCorrection()
 *
 * @category    Mage
 * @package     Mage_CatalogInventory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogInventory_Model_Stock_Item extends Mage_Core_Model_Abstract
{
    const XML_PATH_GLOBAL                = 'cataloginventory/options/';
    const XML_PATH_CAN_SUBTRACT          = 'cataloginventory/options/can_subtract';
    const XML_PATH_CAN_BACK_IN_STOCK     = 'cataloginventory/options/can_back_in_stock';

    const XML_PATH_ITEM                  = 'cataloginventory/item_options/';
    const XML_PATH_MIN_QTY               = 'cataloginventory/item_options/min_qty';
    const XML_PATH_MIN_SALE_QTY          = 'cataloginventory/item_options/min_sale_qty';
    const XML_PATH_MAX_SALE_QTY          = 'cataloginventory/item_options/max_sale_qty';
    const XML_PATH_BACKORDERS            = 'cataloginventory/item_options/backorders';
    const XML_PATH_NOTIFY_STOCK_QTY      = 'cataloginventory/item_options/notify_stock_qty';
    const XML_PATH_MANAGE_STOCK          = 'cataloginventory/item_options/manage_stock';
    const XML_PATH_ENABLE_QTY_INCREMENTS = 'cataloginventory/item_options/enable_qty_increments';
    const XML_PATH_QTY_INCREMENTS        = 'cataloginventory/item_options/qty_increments';

    const ENTITY                         = 'cataloginventory_stock_item';

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
     * Whether index events should be processed immediately
     *
     * @var bool
     */
    protected $_processIndexEvents = true;

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('cataloginventory/stock_item');
    }

    /**
     * Init mapping array of short fields to
     * its full names
     *
     * @resturn Varien_Object
     */
    protected function _initOldFieldsMap()
    {
        $this->_oldFieldsMap = array(
            'stock_status_changed_automatically' => 'stock_status_changed_auto',
            'use_config_enable_qty_increments'   => 'use_config_enable_qty_inc'
        );
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
     * Retrieve Product Id data wrapper
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
     * @return  $this
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
     * @param   float $qty
     * @return  $this
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
     * @return $this
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
     * @return  $this
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
     * @return float
     */
    public function getMinQty()
    {
        return (float)($this->getUseConfigMinQty() ? Mage::getStoreConfig(self::XML_PATH_MIN_QTY)
            : $this->getData('min_qty'));
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
     * @param int $value Value of customer group id
     * @return $this
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
        $customerGroupId = $this->getCustomerGroupId();
        if (!$customerGroupId) {
            $customerGroupId = Mage::app()->getStore()->isAdmin()
                ? Mage_Customer_Model_Group::CUST_GROUP_ALL
                : Mage::getSingleton('customer/session')->getCustomerGroupId();
        }

        if (!isset($this->_minSaleQtyCache[$customerGroupId])) {
            $minSaleQty = $this->getUseConfigMinSaleQty()
                ? Mage::helper('cataloginventory/minsaleqty')->getConfigValue($customerGroupId)
                : $this->getData('min_sale_qty');

            $this->_minSaleQtyCache[$customerGroupId] = empty($minSaleQty) ? 0 : (float)$minSaleQty;
        }

        return $this->_minSaleQtyCache[$customerGroupId] ? $this->_minSaleQtyCache[$customerGroupId] : null;
    }

    /**
     * Retrieve Maximum Qty Allowed in Shopping Cart data wrapper
     *
     * @return float
     */
    public function getMaxSaleQty()
    {
        return (float)($this->getUseConfigMaxSaleQty() ? Mage::getStoreConfig(self::XML_PATH_MAX_SALE_QTY)
            : $this->getData('max_sale_qty'));
    }

    /**
     * Retrieve Notify for Quantity Below data wrapper
     *
     * @return float
     */
    public function getNotifyStockQty()
    {
        if ($this->getUseConfigNotifyStockQty()) {
            return (float) Mage::getStoreConfig(self::XML_PATH_NOTIFY_STOCK_QTY);
        }
        return (float) $this->getData('notify_stock_qty');
    }

    /**
     * Retrieve whether Quantity Increments is enabled
     *
     * @return bool
     */
    public function getEnableQtyIncrements()
    {
        return $this->getUseConfigEnableQtyIncrements()
            ? Mage::getStoreConfigFlag(self::XML_PATH_ENABLE_QTY_INCREMENTS)
            : (bool)$this->getData('enable_qty_increments');
    }

    /**
     * Retrieve Quantity Increments data wrapper
     *
     * @return float|false
     */
    public function getQtyIncrements()
    {
        if ($this->_qtyIncrements === null) {
            if ($this->getEnableQtyIncrements()) {
                $this->_qtyIncrements = (float)($this->getUseConfigQtyIncrements()
                    ? Mage::getStoreConfig(self::XML_PATH_QTY_INCREMENTS)
                    : $this->getData('qty_increments'));
                if ($this->_qtyIncrements <= 0) {
                    $this->_qtyIncrements = false;
                }
            } else {
                $this->_qtyIncrements = false;
            }
        }
        return $this->_qtyIncrements;
    }

     /**
     * Retrieve Default Quantity Increments data wrapper
     *
     * @deprecated since 1.7.0.0
     * @return int|false
     */
    public function getDefaultQtyIncrements()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLE_QTY_INCREMENTS)
            ? (int)Mage::getStoreConfig(self::XML_PATH_QTY_INCREMENTS)
            : false;
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
     * Retrieve Manage Stock data wrapper
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
     * @param   float $qty
     * @exception Mage_Core_Exception
     * @return  bool
     */
    public function checkQty($qty)
    {
        if (!$this->getManageStock() || Mage::app()->getStore()->isAdmin()) {
            return true;
        }

        if ($this->getQty() - $this->getMinQty() - $qty < 0) {
            switch ($this->getBackorders()) {
                case Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NONOTIFY:
                case Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NOTIFY:
                    break;
                default:
                    return false;
                    break;
            }
        }
        return true;
    }

    /**
     * Returns suggested qty that satisfies qty increments and minQty/maxQty/minSaleQty/maxSaleQty conditions
     * or original qty if such value does not exist
     *
     * @param int|float $qty
     * @return int|float
     */
    public function suggestQty($qty)
    {
        // We do not manage stock
        if ($qty <= 0 || !$this->getManageStock()) {
            return $qty;
        }

        $qtyIncrements = (int)$this->getQtyIncrements(); // Currently only integer increments supported
        if ($qtyIncrements < 2) {
            return $qty;
        }

        $minQty = max($this->getMinSaleQty(), $qtyIncrements);
        $divisibleMin = ceil($minQty / $qtyIncrements) * $qtyIncrements;

        $maxQty = min($this->getQty() - $this->getMinQty(), $this->getMaxSaleQty());
        $divisibleMax = floor($maxQty / $qtyIncrements) * $qtyIncrements;

        if ($qty < $minQty || $qty > $maxQty || $divisibleMin > $divisibleMax) {
            // Do not perform rounding for qty that does not satisfy min/max conditions to not confuse customer
            return $qty;
        }

        // Suggest value closest to given qty
        $closestDivisibleLeft = floor($qty / $qtyIncrements) * $qtyIncrements;
        $closestDivisibleRight = $closestDivisibleLeft + $qtyIncrements;
        $acceptableLeft = min(max($divisibleMin, $closestDivisibleLeft), $divisibleMax);
        $acceptableRight = max(min($divisibleMax, $closestDivisibleRight), $divisibleMin);
        return abs($acceptableLeft - $qty) < abs($acceptableRight - $qty) ? $acceptableLeft : $acceptableRight;
    }

    /**
     * Checking quote item quantity
     *
     * Second parameter of this method specifies quantity of this product in whole shopping cart
     * which should be checked for stock availability
     *
     * @param mixed $qty quantity of this item (item qty x parent item qty)
     * @param mixed $summaryQty quantity of this product
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
         * Check if child product assigned to parent
         */
        /** @var Mage_Sales_Model_Quote_Item $parentItem */
        $parentItem = $this->getParentItem();
        if ($this->getIsChildItem() && !empty($parentItem)) {
            $typeInstance = $parentItem->getProduct()->getTypeInstance(true);
            $requiredChildrenIds = $typeInstance->getChildrenIds($parentItem->getProductId(), true);
            $childrenIds = array();
            foreach ($requiredChildrenIds as $groupedChildrenIds) {
                $childrenIds = array_merge($childrenIds, $groupedChildrenIds);
            }
            if (!in_array($this->getProductId(), $childrenIds)) {
                $result->setHasError(true)
                    ->setMessage(Mage::helper('cataloginventory')
                        ->__('This product with current option is not available'))
                    ->setQuoteMessage(Mage::helper('cataloginventory')->__('Some of the products are not available'))
                    ->setQuoteMessageIndex('stock');
                return $result;
            }
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

        if ($this->getMinSaleQty() && $qty < $this->getMinSaleQty()) {
            $result->setHasError(true)
                ->setMessage(
                    Mage::helper('cataloginventory')->__('The minimum quantity allowed for purchase is %s.', $this->getMinSaleQty() * 1)
                )
                ->setErrorCode('qty_min')
                ->setQuoteMessage(Mage::helper('cataloginventory')->__('Some of the products cannot be ordered in requested quantity.'))
                ->setQuoteMessageIndex('qty');
            return $result;
        }

        if ($this->getMaxSaleQty() && $qty > $this->getMaxSaleQty()) {
            $result->setHasError(true)
                ->setMessage(
                    Mage::helper('cataloginventory')->__('The maximum quantity allowed for purchase is %s.', $this->getMaxSaleQty() * 1)
                )
                ->setErrorCode('qty_max')
                ->setQuoteMessage(Mage::helper('cataloginventory')->__('Some of the products cannot be ordered in requested quantity.'))
                ->setQuoteMessageIndex('qty');
            return $result;
        }

        $result->addData($this->checkQtyIncrements($qty)->getData());
        if ($result->getHasError()) {
            return $result;
        }

        if (!$this->getManageStock()) {
            return $result;
        }

        if (!$this->getIsInStock()) {
            $result->setHasError(true)
                ->setMessage(Mage::helper('cataloginventory')->__('This product is currently out of stock.'))
                ->setQuoteMessage(Mage::helper('cataloginventory')->__('Some of the products are currently out of stock.'))
                ->setQuoteMessageIndex('stock');
            $result->setItemUseOldQty(true);
            return $result;
        }

        if (!$this->checkQty($summaryQty) || !$this->checkQty($qty)) {
            $message = Mage::helper('cataloginventory')->__('The requested quantity for "%s" is not available.', $this->getProductName());
            $result->setHasError(true)
                ->setMessage($message)
                ->setQuoteMessage($message)
                ->setQuoteMessageIndex('qty');
            return $result;
        } else {
            if (($this->getQty() - $summaryQty) < 0) {
                if ($this->getProductName()) {
                    if ($this->getIsChildItem()) {
                        $backorderQty = ($this->getQty() > 0) ? ($summaryQty - $this->getQty()) * 1 : $qty * 1;
                        if ($backorderQty > $qty) {
                            $backorderQty = $qty;
                        }

                        $result->setItemBackorders($backorderQty);
                    } else {
                        $orderedItems = $this->getOrderedItems();
                        $itemsLeft = ($this->getQty() > $orderedItems) ? ($this->getQty() - $orderedItems) * 1 : 0;
                        $backorderQty = ($itemsLeft > 0) ? ($qty - $itemsLeft) * 1 : $qty * 1;

                        if ($backorderQty > 0) {
                            $result->setItemBackorders($backorderQty);
                        }
                        $this->setOrderedItems($orderedItems + $qty);
                    }

                    if ($this->getBackorders() == Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NOTIFY) {
                        if (!$this->getIsChildItem()) {
                            $result->setMessage(
                                Mage::helper('cataloginventory')->__('This product is not available in the requested quantity. %s of the items will be backordered.', ($backorderQty * 1))
                            );
                        } else {
                            $result->setMessage(
                                Mage::helper('cataloginventory')->__('"%s" is not available in the requested quantity. %s of the items will be backordered.', $this->getProductName(), ($backorderQty * 1))
                            );
                        }
                    } elseif (Mage::app()->getStore()->isAdmin()) {
                        $result->setMessage(
                            Mage::helper('cataloginventory')->__('The requested quantity for "%s" is not available.', $this->getProductName())
                        );
                    }
                }
            } else {
                if (!$this->getIsChildItem()) {
                    $this->setOrderedItems($qty + (int)$this->getOrderedItems());
                }
            }
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
        if ($this->getSuppressCheckQtyIncrements()) {
            return $result;
        }

        $qtyIncrements = $this->getQtyIncrements();
        if ($qtyIncrements && (Mage::helper('core')->getExactDivision($qty, $qtyIncrements) != 0)) {
            $result->setHasError(true)
                ->setQuoteMessage(
                    Mage::helper('cataloginventory')->__('Some of the products cannot be ordered in the requested quantity.')
                )
                ->setErrorCode('qty_increments')
                ->setQuoteMessageIndex('qty');
            if ($this->getIsChildItem()) {
                $result->setMessage(
                    Mage::helper('cataloginventory')->__('%s is available for purchase in increments of %s only.', $this->getProductName(), $qtyIncrements * 1)
                );
            } else {
                $result->setMessage(
                    Mage::helper('cataloginventory')->__('This product is available for purchase in increments of %s only.', $qtyIncrements * 1)
                );
            }
        }

        return $result;
    }

    /**
     * Add join for catalog in stock field to product collection
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $productCollection
     * @return $this
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
     * @return $this
     */
    protected function _addQuoteItemError(
        Mage_Sales_Model_Quote_Item $item,
        $itemError,
        $quoteError,
        $errorIndex = 'error'
    ) {
        $item->setHasError(true);
        $item->setMessage($itemError);
        $item->setQuoteMessage($quoteError);
        $item->setQuoteMessageIndex($errorIndex);
        return $this;
    }

    /**
     * Before save prepare process
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        // see if quantity is defined for this item type
        $typeId = $this->getTypeId();
        if ($productTypeId = $this->getProductTypeId()) {
            $typeId = $productTypeId;
        }

        $isQty = Mage::helper('cataloginventory')->isQty($typeId);

        if ($isQty) {
            if (!$this->verifyStock()) {
                $this->setIsInStock(false)
                    ->setStockStatusChangedAutomaticallyFlag(true);
            }

            // if qty is below notify qty, update the low stock date to today date otherwise set null
            $this->setLowStockDate(null);
            if ($this->verifyNotification()) {
                $this->setLowStockDate(Mage::app()->getLocale()->date(null, null, null, false)
                    ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
            }

            $this->setStockStatusChangedAutomatically(0);
            if ($this->hasStockStatusChangedAutomaticallyFlag()) {
                $this->setStockStatusChangedAutomatically((int)$this->getStockStatusChangedAutomaticallyFlag());
            }
        } else {
            $this->setQty(0);
        }

        if (!$this->hasData('stock_id')) {
            $this->setStockId($this->getStockId());
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
     * @return $this
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
     * Returns product instance
     *
     * @return Mage_Catalog_Model_Product|null
     */
    public function getProduct()
    {
        return $this->_productInstance ? $this->_productInstance : $this->_getData('product');
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
                    /** @var Mage_Catalog_Model_Product $childProduct */
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
            if ($stockQty < 0 || !$this->getManageStock()
                || !$this->getIsInStock() || ($product && !$product->isSaleable())
            ) {
                $stockQty = 0;
            }
            $this->setStockQty($stockQty);
        }
        return $this->getData('stock_qty');
    }

    /**
     * Reset model data
     * @return $this
     */
    public function reset()
    {
        if ($this->_productInstance) {
            $this->_productInstance = null;
        }
        return $this;
    }

    /**
     * Set whether index events should be processed immediately
     *
     * @param bool $process
     * @return $this
     */
    public function setProcessIndexEvents($process = true)
    {
        $this->_processIndexEvents = $process;
        return $this;
    }

    /**
     * Callback function which called after transaction commit in resource model
     *
     * @return $this
     */
    public function afterCommitCallback()
    {
        parent::afterCommitCallback();

        /** @var \Mage_Index_Model_Indexer $indexer */
        $indexer = Mage::getSingleton('index/indexer');

        if ($this->_processIndexEvents) {
            $indexer->processEntityAction($this, self::ENTITY, Mage_Index_Model_Event::TYPE_SAVE);
        } else {
            $indexer->logEvent($this, self::ENTITY, Mage_Index_Model_Event::TYPE_SAVE);
        }
        return $this;
    }
}
