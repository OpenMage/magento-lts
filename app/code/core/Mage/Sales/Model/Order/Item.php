<?php

declare(strict_types=1);

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
 *
 * @method Mage_Sales_Model_Resource_Order_Item_Collection getCollection()
 * @method bool                                            getHasChildren()
 * @method int                                             getParentProductId()
 * @method Mage_Sales_Model_Resource_Order_Item            getResource()
 * @method Mage_Sales_Model_Resource_Order_Item_Collection getResourceCollection()
 * @method $this                                           setGiftMessage(string $value)
 * @method $this                                           setHasChildren(bool $value)
 * @method $this                                           setProduct(Mage_Catalog_Model_Product $value)
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
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sales/order_item');
    }

    /**
     * Init mapping array of short fields to
     * its full names
     *
     * @return $this
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
    #[Override]
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
     * @param  Mage_Sales_Model_Order_Item $item
     * @return $this
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
     * @return $this
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
     * @param  int    $statusId
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
        $price = $this->getDataByKey('original_price');
        if (is_null($price)) {
            return $this->getPrice();
        }

        return $price;
    }

    /**
     * Set product options
     *
     * @return $this
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
     * @param  string            $code
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
        $options = ($parentItem = $this->getParentItem()) ? $parentItem->getProductOptions() : $this->getProductOptions();

        return isset($options['product_calculations'])
             && $options['product_calculations'] == Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD;
    }

    /**
     * Check if discount has to be applied to parent item
     *
     * @return bool
     */
    public function getForceApplyDiscountToParentItem()
    {
        $product = $this->getParentItem() ? $this->getParentItem()->getProduct() : $this->getProduct();

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
        $options = ($parentItem = $this->getParentItem()) ? $parentItem->getProductOptions() : $this->getProductOptions();

        return isset($options['shipment_type'])
            && $options['shipment_type'] == Mage_Catalog_Model_Product_Type_Abstract::SHIPMENT_SEPARATELY;
    }

    /**
     * This is Dummy item or not
     * if $shipment is true then we checking this for shipping situation if not
     * then we checking this for calculation
     *
     * @param  bool $shipment
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
        if (!$this->getDataByKey('product')) {
            $product = Mage::getModel('catalog/product')->setStoreId($this->getStoreId())->load($this->getProductId());
            $this->setProduct($product);
        }

        return $this->getDataByKey('product');
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
            }

            $totalDiscount += $weeeTaxAppliedAmount['base_weee_discount'] ?? 0;
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
            }

            $totalDiscount += $weeeTaxAppliedAmount['weee_discount'] ?? 0;
        }

        return $totalDiscount;
    }

    public function getAdditionalData(): string
    {
        return (string) $this->_getData('additional_data');
    }

    public function getAmountRefunded(): float
    {
        return (float) $this->_getData('amount_refunded');
    }

    public function getAppliedRuleIds(): ?string
    {
        $value = $this->_getData('applied_rule_ids');
        return $v === null ? null : (string) $v;
    }

    public function getBaseAmountRefunded(): float
    {
        return (float) $this->_getData('base_amount_refunded');
    }

    public function getBaseCost(): float
    {
        return (float) $this->_getData('base_cost');
    }

    public function getBaseDiscountAmount(): float
    {
        return (float) $this->_getData('base_discount_amount');
    }

    public function getBaseDiscountInvoiced(): float
    {
        return (float) $this->_getData('base_discount_invoiced');
    }

    public function getBaseDiscountRefunded(): float
    {
        return (float) $this->_getData('base_discount_refunded');
    }

    public function getBaseHiddenTaxAmount(): float
    {
        return (float) $this->_getData('base_hidden_tax_amount');
    }

    public function getBaseHiddenTaxInvoiced(): float
    {
        return (float) $this->_getData('base_hidden_tax_invoiced');
    }

    public function getBaseHiddenTaxRefunded(): float
    {
        return (float) $this->_getData('base_hidden_tax_refunded');
    }

    public function getBaseOriginalPrice(): float
    {
        return (float) $this->_getData('base_original_price');
    }

    public function getBasePrice(): float
    {
        return (float) $this->_getData('base_price');
    }

    public function getBasePriceInclTax(): float
    {
        return (float) $this->_getData('base_price_incl_tax');
    }

    public function getBaseRowInvoiced(): float
    {
        return (float) $this->_getData('base_row_invoiced');
    }

    public function getBaseRowTotal(): float
    {
        return (float) $this->_getData('base_row_total');
    }

    public function getBaseRowTotalInclTax(): float
    {
        return (float) $this->_getData('base_row_total_incl_tax');
    }

    public function getBaseTaxAmount(): float
    {
        return (float) $this->_getData('base_tax_amount');
    }

    public function getBaseTaxBeforeDiscount(): float
    {
        return (float) $this->_getData('base_tax_before_discount');
    }

    public function getBaseTaxInvoiced(): float
    {
        return (float) $this->_getData('base_tax_invoiced');
    }

    public function getBaseTaxRefunded(): float
    {
        return (float) $this->_getData('base_tax_refunded');
    }

    public function getBaseWeeeTaxAppliedAmount(): float
    {
        return (float) $this->_getData('base_weee_tax_applied_amount');
    }

    public function getBaseWeeeTaxAppliedRowAmount(): float
    {
        return (float) $this->_getData('base_weee_tax_applied_row_amount');
    }

    public function getBaseWeeeTaxDisposition(): float
    {
        return (float) $this->_getData('base_weee_tax_disposition');
    }

    public function getBaseWeeeTaxRowDisposition(): float
    {
        return (float) $this->_getData('base_weee_tax_row_disposition');
    }

    public function getDescription(): string
    {
        return (string) $this->_getData('description');
    }

    public function getDiscountAmount(): float
    {
        return (float) $this->_getData('discount_amount');
    }

    public function getDiscountInvoiced(): float
    {
        return (float) $this->_getData('discount_invoiced');
    }

    public function getDiscountPercent(): float
    {
        return (float) $this->_getData('discount_percent');
    }

    public function getDiscountRefunded(): float
    {
        return (float) $this->_getData('discount_refunded');
    }

    public function getExtOrderItemId(): string
    {
        return (string) $this->_getData('ext_order_item_id');
    }

    public function getFreeShipping(): int
    {
        return (int) $this->_getData('free_shipping');
    }

    public function getGiftMessageAvailable(): int
    {
        return (int) $this->_getData('gift_message_available');
    }

    public function getGiftMessageId(): int
    {
        return (int) $this->_getData('gift_message_id');
    }

    public function getHiddenTaxAmount(): float
    {
        return (float) $this->_getData('hidden_tax_amount');
    }

    public function getHiddenTaxCanceled(): float
    {
        return (float) $this->_getData('hidden_tax_canceled');
    }

    public function getHiddenTaxInvoiced(): float
    {
        return (float) $this->_getData('hidden_tax_invoiced');
    }

    public function getHiddenTaxRefunded(): float
    {
        return (float) $this->_getData('hidden_tax_refunded');
    }

    public function getIsNominal(): int
    {
        return (int) $this->_getData('is_nominal');
    }

    public function getIsQtyDecimal(): int
    {
        return (int) $this->_getData('is_qty_decimal');
    }

    public function getIsVirtual(): int
    {
        return (int) $this->_getData('is_virtual');
    }

    public function getLockedDoInvoice(): int
    {
        return (int) $this->_getData('locked_do_invoice');
    }

    public function getLockedDoShip(): int
    {
        return (int) $this->_getData('locked_do_ship');
    }

    public function getName(): string
    {
        return (string) $this->_getData('name');
    }

    public function getNoDiscount(): int
    {
        return (int) $this->_getData('no_discount');
    }

    public function getOrderId(): int
    {
        return (int) $this->_getData('order_id');
    }

    public function getParentItemId(): ?int
    {
        $value = $this->_getData('parent_item_id');
        return $v === null ? null : (int) $v;
    }

    public function getPrice(): float
    {
        return (float) $this->_getData('price');
    }

    public function getPriceInclTax(): float
    {
        return (float) $this->_getData('price_incl_tax');
    }

    public function getProductId(): int
    {
        return (int) $this->_getData('product_id');
    }

    public function getProductType(): string
    {
        return (string) $this->_getData('product_type');
    }

    public function getQtyBackordered(): float
    {
        return (float) $this->_getData('qty_backordered');
    }

    public function getQtyCanceled(): float
    {
        return (float) $this->_getData('qty_canceled');
    }

    public function getQtyInvoiced(): float
    {
        return (float) $this->_getData('qty_invoiced');
    }

    public function getQtyOrdered(): float
    {
        return (float) $this->_getData('qty_ordered');
    }

    public function getQtyRefunded(): float
    {
        return (float) $this->_getData('qty_refunded');
    }

    public function getQtyShipped(): float
    {
        return (float) $this->_getData('qty_shipped');
    }

    public function getQuoteItemId(): int
    {
        return (int) $this->_getData('quote_item_id');
    }

    public function getQuoteParentItemId(): ?int
    {
        $value = $this->_getData('quote_parent_item_id');
        return $v === null ? null : (int) $v;
    }

    public function getRowInvoiced(): float
    {
        return (float) $this->_getData('row_invoiced');
    }

    public function getRowTotal(): float
    {
        return (float) $this->_getData('row_total');
    }

    public function getRowTotalInclTax(): float
    {
        return (float) $this->_getData('row_total_incl_tax');
    }

    public function getRowWeight(): float
    {
        return (float) $this->_getData('row_weight');
    }

    public function getShippingAmount(): float
    {
        return (float) $this->_getData('shipping_amount');
    }

    public function getSku(): string
    {
        return (string) $this->_getData('sku');
    }

    public function getStoreId(): int
    {
        return (int) $this->_getData('store_id');
    }

    public function getTaxAmount(): float
    {
        return (float) $this->_getData('tax_amount');
    }

    public function getTaxBeforeDiscount(): float
    {
        return (float) $this->_getData('tax_before_discount');
    }

    public function getTaxCanceled(): float
    {
        return (float) $this->_getData('tax_canceled');
    }

    public function getTaxInvoiced(): float
    {
        return (float) $this->_getData('tax_invoiced');
    }

    public function getTaxPercent(): float
    {
        return (float) $this->_getData('tax_percent');
    }

    public function getTaxRefunded(): float
    {
        return (float) $this->_getData('tax_refunded');
    }

    public function getWeeeTaxApplied(): string
    {
        return (string) $this->_getData('weee_tax_applied');
    }

    public function getWeeeTaxAppliedAmount(): float
    {
        return (float) $this->_getData('weee_tax_applied_amount');
    }

    public function getWeeeTaxAppliedRowAmount(): float
    {
        return (float) $this->_getData('weee_tax_applied_row_amount');
    }

    public function getWeeeTaxDisposition(): float
    {
        return (float) $this->_getData('weee_tax_disposition');
    }

    public function getWeeeTaxRowDisposition(): float
    {
        return (float) $this->_getData('weee_tax_row_disposition');
    }

    public function getWeight(): float
    {
        return (float) $this->_getData('weight');
    }

    public function setAdditionalData(string $value): static
    {
        return $this->setData('additional_data', $value);
    }

    public function setAmountRefunded(float $value): static
    {
        return $this->setData('amount_refunded', $value);
    }

    public function setAppliedRuleIds(string $value): static
    {
        return $this->setData('applied_rule_ids', $value);
    }

    public function setBaseAmountRefunded(float $value): static
    {
        return $this->setData('base_amount_refunded', $value);
    }

    public function setBaseCost(float $value): static
    {
        return $this->setData('base_cost', $value);
    }

    public function setBaseDiscountAmount(float $value): static
    {
        return $this->setData('base_discount_amount', $value);
    }

    public function setBaseDiscountInvoiced(float $value): static
    {
        return $this->setData('base_discount_invoiced', $value);
    }

    public function setBaseDiscountRefunded(float $value): static
    {
        return $this->setData('base_discount_refunded', $value);
    }

    public function setBaseHiddenTaxAmount(float $value): static
    {
        return $this->setData('base_hidden_tax_amount', $value);
    }

    public function setBaseHiddenTaxInvoiced(float $value): static
    {
        return $this->setData('base_hidden_tax_invoiced', $value);
    }

    public function setBaseHiddenTaxRefunded(float $value): static
    {
        return $this->setData('base_hidden_tax_refunded', $value);
    }

    public function setBaseOriginalPrice(float $value): static
    {
        return $this->setData('base_original_price', $value);
    }

    public function setBasePrice(float $value): static
    {
        return $this->setData('base_price', $value);
    }

    public function setBasePriceInclTax(float $value): static
    {
        return $this->setData('base_price_incl_tax', $value);
    }

    public function setBaseRowInvoiced(float $value): static
    {
        return $this->setData('base_row_invoiced', $value);
    }

    public function setBaseRowTotal(float $value): static
    {
        return $this->setData('base_row_total', $value);
    }

    public function setBaseRowTotalInclTax(float $value): static
    {
        return $this->setData('base_row_total_incl_tax', $value);
    }

    public function setBaseTaxAmount(float $value): static
    {
        return $this->setData('base_tax_amount', $value);
    }

    public function setBaseTaxBeforeDiscount(float $value): static
    {
        return $this->setData('base_tax_before_discount', $value);
    }

    public function setBaseTaxInvoiced(float $value): static
    {
        return $this->setData('base_tax_invoiced', $value);
    }

    public function setBaseTaxRefunded(float $value): static
    {
        return $this->setData('base_tax_refunded', $value);
    }

    public function setBaseWeeeTaxAppliedAmount(float $value): static
    {
        return $this->setData('base_weee_tax_applied_amount', $value);
    }

    public function setBaseWeeeTaxAppliedRowAmount(float $value): static
    {
        return $this->setData('base_weee_tax_applied_row_amount', $value);
    }

    public function setBaseWeeeTaxDisposition(float $value): static
    {
        return $this->setData('base_weee_tax_disposition', $value);
    }

    public function setBaseWeeeTaxRowDisposition(float $value): static
    {
        return $this->setData('base_weee_tax_row_disposition', $value);
    }

    public function setDescription(string $value): static
    {
        return $this->setData('description', $value);
    }

    public function setDiscountAmount(float $value): static
    {
        return $this->setData('discount_amount', $value);
    }

    public function setDiscountInvoiced(float $value): static
    {
        return $this->setData('discount_invoiced', $value);
    }

    public function setDiscountPercent(float $value): static
    {
        return $this->setData('discount_percent', $value);
    }

    public function setDiscountRefunded(float $value): static
    {
        return $this->setData('discount_refunded', $value);
    }

    public function setExtOrderItemId(string $value): static
    {
        return $this->setData('ext_order_item_id', $value);
    }

    public function setFreeShipping(int $value): static
    {
        return $this->setData('free_shipping', $value);
    }

    public function setGiftMessageAvailable(int $value): static
    {
        return $this->setData('gift_message_available', $value);
    }

    public function setGiftMessageId(int $value): static
    {
        return $this->setData('gift_message_id', $value);
    }

    public function setHiddenTaxAmount(float $value): static
    {
        return $this->setData('hidden_tax_amount', $value);
    }

    public function setHiddenTaxCanceled(float $value): static
    {
        return $this->setData('hidden_tax_canceled', $value);
    }

    public function setHiddenTaxInvoiced(float $value): static
    {
        return $this->setData('hidden_tax_invoiced', $value);
    }

    public function setHiddenTaxRefunded(float $value): static
    {
        return $this->setData('hidden_tax_refunded', $value);
    }

    public function setIsNominal(int $value): static
    {
        return $this->setData('is_nominal', $value);
    }

    public function setIsQtyDecimal(int $value): static
    {
        return $this->setData('is_qty_decimal', $value);
    }

    public function setIsVirtual(int $value): static
    {
        return $this->setData('is_virtual', $value);
    }

    public function setLockedDoInvoice(int $value): static
    {
        return $this->setData('locked_do_invoice', $value);
    }

    public function setLockedDoShip(int $value): static
    {
        return $this->setData('locked_do_ship', $value);
    }

    public function setName(string $value): static
    {
        return $this->setData('name', $value);
    }

    public function setNoDiscount(int $value): static
    {
        return $this->setData('no_discount', $value);
    }

    public function setOrderId(int $value): static
    {
        return $this->setData('order_id', $value);
    }

    public function setOriginalPrice(float $value): static
    {
        return $this->setData('original_price', $value);
    }

    public function setParentItemId(?int $value): static
    {
        return $this->setData('parent_item_id', $value);
    }

    public function setPrice(float $value): static
    {
        return $this->setData('price', $value);
    }

    public function setPriceInclTax(float $value): static
    {
        return $this->setData('price_incl_tax', $value);
    }

    public function setProductId(int $value): static
    {
        return $this->setData('product_id', $value);
    }

    public function setProductType(string $value): static
    {
        return $this->setData('product_type', $value);
    }

    public function setQtyBackordered(float $value): static
    {
        return $this->setData('qty_backordered', $value);
    }

    public function setQtyCanceled(float $value): static
    {
        return $this->setData('qty_canceled', $value);
    }

    public function setQtyInvoiced(float $value): static
    {
        return $this->setData('qty_invoiced', $value);
    }

    public function setQtyOrdered(float $value): static
    {
        return $this->setData('qty_ordered', $value);
    }

    public function setQtyRefunded(float $value): static
    {
        return $this->setData('qty_refunded', $value);
    }

    public function setQtyShipped(float $value): static
    {
        return $this->setData('qty_shipped', $value);
    }

    public function setQuoteItemId(int $value): static
    {
        return $this->setData('quote_item_id', $value);
    }

    public function setQuoteParentItemId(?int $value): static
    {
        return $this->setData('quote_parent_item_id', $value);
    }

    public function setRowInvoiced(float $value): static
    {
        return $this->setData('row_invoiced', $value);
    }

    public function setRowTotal(float $value): static
    {
        return $this->setData('row_total', $value);
    }

    public function setRowTotalInclTax(float $value): static
    {
        return $this->setData('row_total_incl_tax', $value);
    }

    public function setRowWeight(float $value): static
    {
        return $this->setData('row_weight', $value);
    }

    public function setShippingAmount(float $value): static
    {
        return $this->setData('shipping_amount', $value);
    }

    public function setSku(string $value): static
    {
        return $this->setData('sku', $value);
    }

    public function setStoreId(int $value): static
    {
        return $this->setData('store_id', $value);
    }

    public function setTaxAmount(float $value): static
    {
        return $this->setData('tax_amount', $value);
    }

    public function setTaxBeforeDiscount(float $value): static
    {
        return $this->setData('tax_before_discount', $value);
    }

    public function setTaxCanceled(float $value): static
    {
        return $this->setData('tax_canceled', $value);
    }

    public function setTaxInvoiced(float $value): static
    {
        return $this->setData('tax_invoiced', $value);
    }

    public function setTaxPercent(float $value): static
    {
        return $this->setData('tax_percent', $value);
    }

    public function setTaxRefunded(float $value): static
    {
        return $this->setData('tax_refunded', $value);
    }

    public function setWeeeTaxApplied(string $value): static
    {
        return $this->setData('weee_tax_applied', $value);
    }

    public function setWeeeTaxAppliedAmount(float $value): static
    {
        return $this->setData('weee_tax_applied_amount', $value);
    }

    public function setWeeeTaxAppliedRowAmount(float $value): static
    {
        return $this->setData('weee_tax_applied_row_amount', $value);
    }

    public function setWeeeTaxDisposition(float $value): static
    {
        return $this->setData('weee_tax_disposition', $value);
    }

    public function setWeeeTaxRowDisposition(float $value): static
    {
        return $this->setData('weee_tax_row_disposition', $value);
    }

    public function setWeight(float $value): static
    {
        return $this->setData('weight', $value);
    }
}
