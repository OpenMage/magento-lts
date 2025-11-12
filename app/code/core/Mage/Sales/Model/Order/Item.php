<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Class Mage_Sales_Model_Order_Item
 *
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Resource_Order_Item _getResource()
 * @method string getAdditionalData()
 * @method float getAmountRefunded()
 * @method null|string getAppliedRuleIds()
 *
 * @method float getBaseAmountRefunded()
 * @method float getBaseCost()
 * @method float getBaseDiscountAmount()
 * @method float getBaseDiscountInvoiced()
 * @method float getBaseDiscountRefunded()
 * @method float getBaseHiddenTaxAmount()
 * @method float getBaseHiddenTaxInvoiced()
 * @method float getBaseHiddenTaxRefunded()
 * @method float getBaseOriginalPrice()
 * @method float getBasePrice()
 * @method float getBasePriceInclTax()
 * @method float getBaseRowInvoiced()
 * @method float getBaseRowTotal()
 * @method float getBaseRowTotalInclTax()
 * @method float getBaseTaxAmount()
 * @method float getBaseTaxBeforeDiscount()
 * @method float getBaseTaxInvoiced()
 * @method float getBaseTaxRefunded()
 * @method float getBaseWeeeTaxAppliedAmount()
 * @method float getBaseWeeeTaxAppliedRowAmount()
 * @method float getBaseWeeeTaxDisposition()
 * @method float getBaseWeeeTaxRowDisposition()
 * @method Mage_Sales_Model_Resource_Order_Item_Collection getCollection()
 * @method string getCreatedAt()
 * @method string getDescription()
 * @method float getDiscountAmount()
 * @method float getDiscountInvoiced()
 * @method float getDiscountPercent()
 * @method float getDiscountRefunded()
 * @method string getExtOrderItemId()
 * @method int getFreeShipping()
 * @method int getGiftMessageAvailable()
 * @method int getGiftMessageId()
 * @method bool getHasChildren()
 * @method float getHiddenTaxAmount()
 * @method float getHiddenTaxCanceled()
 * @method float getHiddenTaxInvoiced()
 * @method float getHiddenTaxRefunded()
 * @method int getIsNominal()
 * @method int getIsQtyDecimal()
 * @method int getIsVirtual()
 * @method int getLockedDoInvoice()
 * @method int getLockedDoShip()
 * @method string getName()
 * @method int getNoDiscount()
 * @method int getOrderId()
 * @method int getParentItemId()
 * @method int getParentProductId()
 * @method float getPrice()
 * @method float getPriceInclTax()
 * @method int getProductId()
 * @method string getProductType()
 * @method float getQtyBackordered()
 * @method float getQtyCanceled()
 * @method float getQtyInvoiced()
 * @method float getQtyOrdered()
 * @method float getQtyRefunded()
 * @method float getQtyShipped()
 * @method int getQuoteItemId()
 * @method int getQuoteParentItemId()
 * @method Mage_Sales_Model_Resource_Order_Item getResource()
 * @method Mage_Sales_Model_Resource_Order_Item_Collection getResourceCollection()
 * @method float getRowInvoiced()
 * @method float getRowTotal()
 * @method float getRowTotalInclTax()
 * @method float getRowWeight()
 * @method string getSku()
 * @method int getStoreId()
 * @method float getTaxAmount()
 * @method float getTaxBeforeDiscount()
 * @method float getTaxCanceled()
 * @method float getTaxInvoiced()
 * @method float getTaxPercent()
 * @method float getTaxRefunded()
 * @method string getUpdatedAt()
 * @method string getWeeeTaxApplied()
 * @method float getWeeeTaxAppliedAmount()
 * @method float getWeeeTaxAppliedRowAmount()
 * @method float getWeeeTaxDisposition()
 * @method float getWeeeTaxRowDisposition()
 * @method float getWeight()
 * @method $this setAdditionalData(string $value)
 * @method $this setAmountRefunded(float $value)
 * @method $this setAppliedRuleIds(string $value)
 * @method $this setBaseAmountRefunded(float $value)
 * @method $this setBaseCost(float $value)
 * @method $this setBaseDiscountAmount(float $value)
 * @method $this setBaseDiscountInvoiced(float $value)
 * @method $this setBaseDiscountRefunded(float $value)
 * @method $this setBaseHiddenTaxAmount(float $value)
 * @method $this setBaseHiddenTaxInvoiced(float $value)
 * @method $this setBaseHiddenTaxRefunded(float $value)
 * @method $this setBaseOriginalPrice(float $value)
 * @method $this setBasePrice(float $value)
 * @method $this setBasePriceInclTax(float $value)
 * @method $this setBaseRowInvoiced(float $value)
 * @method $this setBaseRowTotal(float $value)
 * @method $this setBaseRowTotalInclTax(float $value)
 * @method $this setBaseTaxAmount(float $value)
 * @method $this setBaseTaxBeforeDiscount(float $value)
 * @method $this setBaseTaxInvoiced(float $value)
 * @method $this setBaseTaxRefunded(float $value)
 * @method $this setBaseWeeeTaxAppliedAmount(float $value)
 * @method $this setBaseWeeeTaxAppliedRowAmount(float $value)
 * @method $this setBaseWeeeTaxDisposition(float $value)
 * @method $this setBaseWeeeTaxRowDisposition(float $value)
 * @method $this setCreatedAt(string $value)
 * @method $this setDescription(string $value)
 * @method $this setDiscountAmount(float $value)
 * @method $this setDiscountInvoiced(float $value)
 * @method $this setDiscountPercent(float $value)
 * @method $this setDiscountRefunded(float $value)
 * @method $this setExtOrderItemId(string $value)
 * @method $this setFreeShipping(int $value)
 * @method $this setGiftMessage(string $value)
 * @method $this setGiftMessageAvailable(int $value)
 * @method $this setGiftMessageId(int $value)
 * @method $this setHasChildren(bool $value)
 * @method $this setHiddenTaxAmount(float $value)
 * @method $this setHiddenTaxCanceled(float $value)
 * @method $this setHiddenTaxInvoiced(float $value)
 * @method $this setHiddenTaxRefunded(float $value)
 * @method $this setIsNominal(int $value)
 * @method $this setIsQtyDecimal(int $value)
 * @method $this setIsVirtual(int $value)
 * @method $this setLockedDoInvoice(int $value)
 * @method $this setLockedDoShip(int $value)
 * @method $this setName(string $value)
 * @method $this setNoDiscount(int $value)
 * @method $this setOrderId(int $value)
 * @method $this setOriginalPrice(float $value)
 * @method $this setParentItemId(int $value)
 * @method $this setPrice(float $value)
 * @method $this setPriceInclTax(float $value)
 * @method $this setProduct(Mage_Catalog_Model_Product $value)
 * @method $this setProductId(int $value)
 * @method $this setProductType(string $value)
 * @method $this setQtyBackordered(float $value)
 * @method $this setQtyCanceled(float $value)
 * @method $this setQtyInvoiced(float $value)
 * @method $this setQtyOrdered(float $value)
 * @method $this setQtyRefunded(float $value)
 * @method $this setQtyShipped(float $value)
 * @method $this setQuoteItemId(int $value)
 * @method $this setQuoteParentItemId(int $value)
 * @method $this setRowInvoiced(float $value)
 * @method $this setRowTotal(float $value)
 * @method $this setRowTotalInclTax(float $value)
 * @method $this setRowWeight(float $value)
 * @method $this setShippingAmount(float $value)
 * @method $this setSku(string $value)
 * @method $this setStoreId(int $value)
 * @method $this setTaxAmount(float $value)
 * @method $this setTaxBeforeDiscount(float $value)
 * @method $this setTaxCanceled(float $value)
 * @method $this setTaxInvoiced(float $value)
 * @method $this setTaxPercent(float $value)
 * @method $this setTaxRefunded(float $value)
 * @method $this setUpdatedAt(string $value)
 * @method $this setWeeeTaxApplied(string $value)
 * @method $this setWeeeTaxAppliedAmount(float $value)
 * @method $this setWeeeTaxAppliedRowAmount(float $value)
 * @method $this setWeeeTaxDisposition(float $value)
 * @method $this setWeeeTaxRowDisposition(float $value)
 * @method $this setWeight(float $value)
 */
class Mage_Sales_Model_Order_Item extends Mage_Core_Model_Abstract
{
    public const STATUS_PENDING        = 1;

    // No items shipped, invoiced, canceled, refunded nor backordered
    public const STATUS_SHIPPED        = 2;

    // When qty ordered - [qty canceled + qty returned] = qty shipped
    public const STATUS_INVOICED       = 9;

    // When qty ordered - [qty canceled + qty returned] = qty invoiced
    public const STATUS_BACKORDERED    = 3;

    // When qty ordered - [qty canceled + qty returned] = qty backordered
    public const STATUS_CANCELED       = 5;

    // When qty ordered = qty canceled
    public const STATUS_PARTIAL        = 6; // If [qty shipped or(max of two) qty invoiced + qty canceled + qty returned]
    // < qty ordered
    public const STATUS_MIXED          = 7;

    // All other combinations
    public const STATUS_REFUNDED       = 8; // When qty ordered = qty refunded

    public const STATUS_RETURNED       = 4; // When qty ordered = qty returned // not used at the moment

    protected $_eventPrefix = 'sales_order_item';

    protected $_eventObject = 'item';

    protected static $_statuses = null;

    /**
     * Order instance
     *
     * @var null|Mage_Sales_Model_Order
     */
    protected $_order       = null;

    protected $_parentItem  = null;

    protected $_children    = [];

    /**
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init('sales/order_item');
    }

    /**
     * Init mapping array of short fields to
     * its full names
     *
     * @return Varien_Object
     */
    protected function _initOldFieldsMap()
    {
        // pre 1.6 fields names, old => new
        $this->_oldFieldsMap = [
            'base_weee_tax_applied_row_amount' => 'base_weee_tax_applied_row_amnt',
        ];
        return $this;
    }

    /**
     * Prepare data before save
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if (!$this->getOrderId() && $this->getOrder()) {
            $this->setOrderId($this->getOrder()->getId());
        }

        if ($this->getParentItem()) {
            $this->setParentItemId($this->getParentItem()->getId());
        }

        return $this;
    }

    /**
     * Set parent item
     *
     * @param   Mage_Sales_Model_Order_Item $item
     * @return  $this
     */
    public function setParentItem($item)
    {
        if ($item) {
            $this->_parentItem = $item;
            $item->setHasChildren(true);
            $item->addChildItem($this);
        }

        return $this;
    }

    /**
     * Get parent item
     *
     * @return null|$this
     */
    public function getParentItem()
    {
        return $this->_parentItem;
    }

    /**
     * Check item invoice availability
     *
     * @return bool
     */
    public function canInvoice()
    {
        return $this->getQtyToInvoice() > 0;
    }

    /**
     * Check item ship availability
     *
     * @return bool
     */
    public function canShip()
    {
        return $this->getQtyToShip() > 0;
    }

    /**
     * Check item refund availability
     *
     * @return bool
     */
    public function canRefund()
    {
        return $this->getQtyToRefund() > 0;
    }

    /**
     * Retrieve item qty available for ship
     *
     * @return float|int
     */
    public function getQtyToShip()
    {
        if ($this->isDummy(true)) {
            return 0;
        }

        return $this->getSimpleQtyToShip();
    }

    /**
     * Retrieve item qty available for ship
     *
     * @return float|int
     */
    public function getSimpleQtyToShip()
    {
        $qty = $this->getQtyOrdered()
            - $this->getQtyShipped()
            - $this->getQtyRefunded()
            - $this->getQtyCanceled();
        return max($qty, 0);
    }

    /**
     * Retrieve item qty available for invoice
     *
     * @return float|int
     */
    public function getQtyToInvoice()
    {
        if ($this->isDummy()) {
            return 0;
        }

        $qty = $this->getQtyOrdered()
            - $this->getQtyInvoiced()
            - $this->getQtyCanceled();
        return max($qty, 0);
    }

    /**
     * Retrieve item qty available for refund
     *
     * @return float|int
     */
    public function getQtyToRefund()
    {
        if ($this->isDummy()) {
            return 0;
        }

        return max($this->getQtyInvoiced() - $this->getQtyRefunded(), 0);
    }

    /**
     * Retrieve item qty available for cancel
     *
     * @return float|int
     */
    public function getQtyToCancel()
    {
        if ($this->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            $qtyToCancel = $this->getQtyToCancelBundle();
        } elseif ($this->getParentItem()
            && $this->getParentItem()->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE
        ) {
            $qtyToCancel = $this->getQtyToCancelBundleItem();
        } else {
            $qtyToCancel = min($this->getQtyToInvoice(), $this->getQtyToShip());
        }

        return max($qtyToCancel, 0);
    }

    /**
     * Retrieve Bundle item qty available for cancel
     * getQtyToInvoice() will always deliver 0 for Bundle
     *
     * @return float|int
     */
    public function getQtyToCancelBundle()
    {
        if ($this->isDummy()) {
            $qty = $this->getQtyOrdered()
                - $this->getQtyInvoiced()
                - $this->getQtyCanceled();
            return min(max($qty, 0), $this->getQtyToShip());
        }

        return min($this->getQtyToInvoice(), $this->getQtyToShip());
    }

    /**
     * Retrieve Bundle child item qty available for cancel
     * getQtyToShip() always returns 0 for BundleItems that ship together
     *
     * @return float|int
     */
    public function getQtyToCancelBundleItem()
    {
        if ($this->isDummy(true)) {
            return min($this->getQtyToInvoice(), $this->getSimpleQtyToShip());
        }

        return min($this->getQtyToInvoice(), $this->getQtyToShip());
    }

    /**
     * Declare order
     *
     * @return  $this
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        if ($this->getOrderId() != $order->getId()) {
            $this->setOrderId($order->getId());
        }

        return $this;
    }

    /**
     * Retrieve order model object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (is_null($this->_order) && ($orderId = $this->getOrderId())) {
            $order = Mage::getModel('sales/order');
            $order->load($orderId);
            $this->setOrder($order);
        }

        return $this->_order;
    }

    /**
     * Retrieve item status identifier
     *
     * @return int
     */
    public function getStatusId()
    {
        $backordered = (float) $this->getQtyBackordered();
        if (!$backordered && $this->getHasChildren()) {
            $backordered = (float) $this->_getQtyChildrenBackordered();
        }

        $canceled    = (float) $this->getQtyCanceled();
        $invoiced    = (float) $this->getQtyInvoiced();
        $ordered     = (float) $this->getQtyOrdered();
        $refunded    = (float) $this->getQtyRefunded();
        $shipped     = (float) $this->getQtyShipped();

        $actuallyOrdered = $ordered - $canceled - $refunded;

        if (!$invoiced && !$shipped && !$refunded && !$canceled && !$backordered) {
            return self::STATUS_PENDING;
        }

        if ($shipped && $invoiced && ($actuallyOrdered == $shipped)) {
            return self::STATUS_SHIPPED;
        }

        if ($invoiced && !$shipped && ($actuallyOrdered == $invoiced)) {
            return self::STATUS_INVOICED;
        }

        if ($backordered && ($actuallyOrdered == $backordered)) {
            return self::STATUS_BACKORDERED;
        }

        if ($refunded && $ordered == $refunded) {
            return self::STATUS_REFUNDED;
        }

        if ($canceled && $ordered == $canceled) {
            return self::STATUS_CANCELED;
        }

        if (max($shipped, $invoiced) < $actuallyOrdered) {
            return self::STATUS_PARTIAL;
        }

        return self::STATUS_MIXED;
    }

    /**
     * Retrieve backordered qty of children items
     *
     * @return null|float
     */
    protected function _getQtyChildrenBackordered()
    {
        $backordered = null;
        foreach ($this->_children as $childItem) {
            $backordered += (float) $childItem->getQtyBackordered();
        }

        return $backordered;
    }

    /**
     * Retrieve status
     *
     * @return string
     */
    public function getStatus()
    {
        return self::getStatusName($this->getStatusId());
    }

    /**
     * Retrieve status name
     *
     * @param int $statusId
     * @return string
     */
    public static function getStatusName($statusId)
    {
        if (is_null(self::$_statuses)) {
            self::getStatuses();
        }

        return self::$_statuses[$statusId] ?? Mage::helper('sales')->__('Unknown Status');
    }

    /**
     * Cancel order item
     *
     * @return $this
     */
    public function cancel()
    {
        if ($this->getStatusId() !== self::STATUS_CANCELED) {
            Mage::dispatchEvent('sales_order_item_cancel', ['item' => $this]);
            $this->setQtyCanceled($this->getQtyToCancel());
            $this->setTaxCanceled(
                $this->getTaxCanceled()
                + $this->getBaseTaxAmount() * $this->getQtyCanceled() / $this->getQtyOrdered(),
            );
            $this->setHiddenTaxCanceled(
                $this->getHiddenTaxCanceled()
                + $this->getHiddenTaxAmount() * $this->getQtyCanceled() / $this->getQtyOrdered(),
            );
        }

        return $this;
    }

    /**
     * Retrieve order item statuses array
     *
     * @return array
     */
    public static function getStatuses()
    {
        if (is_null(self::$_statuses)) {
            self::$_statuses = [
                self::STATUS_PENDING        => Mage::helper('sales')->__('Ordered'),
                self::STATUS_SHIPPED        => Mage::helper('sales')->__('Shipped'),
                self::STATUS_INVOICED       => Mage::helper('sales')->__('Invoiced'),
                self::STATUS_BACKORDERED    => Mage::helper('sales')->__('Backordered'),
                self::STATUS_RETURNED       => Mage::helper('sales')->__('Returned'),
                self::STATUS_REFUNDED       => Mage::helper('sales')->__('Refunded'),
                self::STATUS_CANCELED       => Mage::helper('sales')->__('Canceled'),
                self::STATUS_PARTIAL        => Mage::helper('sales')->__('Partial'),
                self::STATUS_MIXED          => Mage::helper('sales')->__('Mixed'),
            ];
        }

        return self::$_statuses;
    }

    /**
     * Redeclare getter for back compatibility
     *
     * @return float
     */
    public function getOriginalPrice()
    {
        $price = $this->getData('original_price');
        if (is_null($price)) {
            return $this->getPrice();
        }

        return $price;
    }

    /**
     * Set product options
     *
     * @return  $this
     */
    public function setProductOptions(array $options)
    {
        $this->setData('product_options', serialize($options));
        return $this;
    }

    /**
     * Get product options array
     *
     * @return array
     */
    public function getProductOptions()
    {
        if ($options = $this->_getData('product_options')) {
            return unserialize($options, ['allowed_classes' => false]);
        }

        return [];
    }

    /**
     * Get product options array by code.
     * If code is null return all options
     *
     * @param string $code
     * @return null|array|string
     */
    public function getProductOptionByCode($code = null)
    {
        $options = $this->getProductOptions();
        if (is_null($code)) {
            return $options;
        }

        return $options[$code] ?? null;
    }

    /**
     * Return real product type of item or NULL if item is not composite
     *
     * @return null|array
     */
    public function getRealProductType()
    {
        if ($productType = $this->getProductOptionByCode('real_product_type')) {
            return $productType;
        }

        return null;
    }

    /**
     * Adds child item to this item
     *
     * @param Mage_Sales_Model_Order_Item $item
     */
    public function addChildItem($item)
    {
        if ($item instanceof Mage_Sales_Model_Order_Item) {
            $this->_children[] = $item;
        } elseif (is_array($item)) {
            $this->_children = array_merge($this->_children, $item);
        }
    }

    /**
     * Return chilgren items of this item
     *
     * @return array
     */
    public function getChildrenItems()
    {
        return $this->_children;
    }

    /**
     * Return checking of what calculation
     * type was for this product
     *
     * @return bool
     */
    public function isChildrenCalculated()
    {
        if ($parentItem = $this->getParentItem()) {
            $options = $parentItem->getProductOptions();
        } else {
            $options = $this->getProductOptions();
        }

        if (isset($options['product_calculations'])
             && $options['product_calculations'] == Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check if discount has to be applied to parent item
     *
     * @return bool
     */
    public function getForceApplyDiscountToParentItem()
    {
        if ($this->getParentItem()) {
            $product = $this->getParentItem()->getProduct();
        } else {
            $product = $this->getProduct();
        }

        return $product->getTypeInstance()->getForceApplyDiscountToParentItem();
    }

    /**
     * Return checking of what shipment
     * type was for this product
     *
     * @return bool
     */
    public function isShipSeparately()
    {
        if ($parentItem = $this->getParentItem()) {
            $options = $parentItem->getProductOptions();
        } else {
            $options = $this->getProductOptions();
        }

        if (isset($options['shipment_type'])
            && $options['shipment_type'] == Mage_Catalog_Model_Product_Type_Abstract::SHIPMENT_SEPARATELY
        ) {
            return true;
        }

        return false;
    }

    /**
     * This is Dummy item or not
     * if $shipment is true then we checking this for shipping situation if not
     * then we checking this for calculation
     *
     * @param bool $shipment
     * @return bool
     */
    public function isDummy($shipment = false)
    {
        if ($shipment) {
            if ($this->getHasChildren() && $this->isShipSeparately()) {
                return true;
            }

            if ($this->getHasChildren() && !$this->isShipSeparately()) {
                return false;
            }

            if ($this->getParentItem() && $this->isShipSeparately()) {
                return false;
            }

            if ($this->getParentItem() && !$this->isShipSeparately()) {
                return true;
            }
        } else {
            if ($this->getHasChildren() && $this->isChildrenCalculated()) {
                return true;
            }

            if ($this->getHasChildren() && !$this->isChildrenCalculated()) {
                return false;
            }

            if ($this->getParentItem() && $this->isChildrenCalculated()) {
                return false;
            }

            if ($this->getParentItem() && !$this->isChildrenCalculated()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns formatted buy request - object, holding request received from
     * product view page with keys and options for configured product
     *
     * @return Varien_Object
     */
    public function getBuyRequest()
    {
        $option = $this->getProductOptionByCode('info_buyRequest');
        if (!$option) {
            $option = [];
        }

        $buyRequest = new Varien_Object($option);
        $buyRequest->setQty($this->getQtyOrdered() * 1);
        return $buyRequest;
    }

    /**
     * Retrieve product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->getData('product')) {
            $product = Mage::getModel('catalog/product')->setStoreId($this->getStoreId())->load($this->getProductId());
            $this->setProduct($product);
        }

        return $this->getData('product');
    }

    /**
     * Get the discount amount applied on weee in base
     *
     * @return float
     */
    public function getBaseDiscountAppliedForWeeeTax()
    {
        $weeeTaxAppliedAmounts = unserialize($this->getWeeeTaxApplied(), ['allowed_classes' => false]);
        $totalDiscount = 0;
        if (!is_array($weeeTaxAppliedAmounts)) {
            return $totalDiscount;
        }

        foreach ($weeeTaxAppliedAmounts as $weeeTaxAppliedAmount) {
            if (isset($weeeTaxAppliedAmount['total_base_weee_discount'])) {
                return $weeeTaxAppliedAmount['total_base_weee_discount'];
            } else {
                $totalDiscount += $weeeTaxAppliedAmount['base_weee_discount'] ?? 0;
            }
        }

        return $totalDiscount;
    }

    /**
     * Get the discount amount applied on Weee
     *
     * @return float
     */
    public function getDiscountAppliedForWeeeTax()
    {
        $weeeTaxAppliedAmounts = unserialize($this->getWeeeTaxApplied(), ['allowed_classes' => false]);
        $totalDiscount = 0;
        if (!is_array($weeeTaxAppliedAmounts)) {
            return $totalDiscount;
        }

        foreach ($weeeTaxAppliedAmounts as $weeeTaxAppliedAmount) {
            if (isset($weeeTaxAppliedAmount['total_weee_discount'])) {
                return $weeeTaxAppliedAmount['total_weee_discount'];
            } else {
                $totalDiscount += $weeeTaxAppliedAmount['weee_discount'] ?? 0;
            }
        }

        return $totalDiscount;
    }
}
