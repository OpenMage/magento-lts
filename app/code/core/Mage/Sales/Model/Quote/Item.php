<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Sales Quote Item Model
 *
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Resource_Quote_Item            _getResource()
 * @method Mage_Sales_Model_Resource_Quote_Item_Collection getCollection()
 * @method bool                                            getHasConfigurationUnavailableError()
 * @method bool                                            getHasError()
 * @method Mage_Sales_Model_Resource_Quote_Item            getResource()
 * @method Mage_Sales_Model_Resource_Quote_Item_Collection getResourceCollection()
 * @method bool                                            getUseOldQty()
 * @method $this                                           setBackorders(float $value)
 * @method $this                                           setHasConfigurationUnavailableError(bool $value)
 * @method $this                                           setProductOrderOptions(array $value)
 * @method $this                                           unsHasConfigurationUnavailableError()
 */
class Mage_Sales_Model_Quote_Item extends Mage_Sales_Model_Quote_Item_Abstract
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'sales_quote_item';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'item';

    /**
     * Quote model object
     *
     * @var null|Mage_Sales_Model_Quote
     */
    protected $_quote;

    /**
     * Item options array
     *
     * @var array
     */
    protected $_options = [];

    /**
     * Item options by code cache
     *
     * @var array
     */
    protected $_optionsByCode = [];

    /**
     * Not Represent options
     *
     * @var array
     */
    protected $_notRepresentOptions = ['info_buyRequest'];

    /**
     * Flag stating that options were successfully saved
     */
    protected $_flagOptionsSaved = null;

    /**
     * Array of errors associated with this quote item
     *
     * @var Mage_Sales_Model_Status_List
     */
    protected $_errorInfos = null;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sales/quote_item');
        $this->_errorInfos = Mage::getModel('sales/status_list');
    }

    /**
     * Init mapping array of short fields to
     * its full names
     *
     * @return $this
     */
    protected function _initOldFieldsMap()
    {
        return $this;
    }

    /**
     * Quote Item Before Save prepare data process
     *
     * @return $this
     */
    #[Override]
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $this->setIsVirtual($this->getProduct()->getIsVirtual());
        if ($this->getQuote()) {
            $this->setQuoteId($this->getQuote()->getId());
        }

        return $this;
    }

    /**
     * Declare quote model object
     *
     * @return $this
     */
    public function setQuote(Mage_Sales_Model_Quote $quote)
    {
        $this->_quote = $quote;
        if ($this->getQuoteId() != $quote->getId()) {
            $this->setQuoteId($quote->getId());
        }

        return $this;
    }

    /**
     * Retrieve quote model object
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        if (is_null($this->_quote)) {
            $this->_quote = Mage::getModel('sales/quote')->load($this->getQuoteId());
        }

        return $this->_quote;
    }

    /**
     * Prepare quantity
     *
     * @param  float|int $qty
     * @return float|int
     */
    protected function _prepareQty($qty)
    {
        $qty = Mage::app()->getLocale()->getNumber($qty);
        return ($qty > 0) ? $qty : 1;
    }

    /**
     * Get Magento App instance
     *
     * @return Mage_Core_Model_App
     */
    protected function _getApp()
    {
        return Mage::app();
    }

    /**
     * Adding quantity to quote item
     *
     * @param  float $qty
     * @return $this
     */
    public function addQty($qty)
    {
        $oldQty = $this->getQty();
        $qty = $this->_prepareQty($qty);

        /**
         * We can't modify quontity of existing items which have parent
         * This qty declared just once duering add process and is not editable
         */
        if (!$this->getParentItem() || !$this->getId()) {
            $this->setQtyToAdd($qty);
            $this->setQty($oldQty + $qty);
        }

        return $this;
    }

    /**
     * Declare quote item quantity
     *
     * @param  float $qty
     * @return $this
     */
    public function setQty($qty)
    {
        $qty = $this->_prepareQty($qty);
        $oldQty = $this->_getData('qty');
        $this->setData('qty', $qty);

        Mage::dispatchEvent('sales_quote_item_qty_set_after', ['item' => $this]);

        if ($this->getQuote() && $this->getQuote()->getIgnoreOldQty()) {
            return $this;
        }

        if ($this->getUseOldQty()) {
            $this->setData('qty', $oldQty);
        }

        return $this;
    }

    /**
     * Retrieve option product with Qty
     *
     * Return array
     * 'qty'        => the qty
     * 'product'    => the product model
     *
     * @return array
     */
    public function getQtyOptions()
    {
        $qtyOptions = $this->getDataByKey('qty_options');
        if (is_null($qtyOptions)) {
            $productIds = [];
            $qtyOptions = [];
            foreach ($this->getOptions() as $option) {
                /** @var Mage_Sales_Model_Quote_Item_Option $option */
                if (is_object($option->getProduct())
                    && $option->getProduct()->getId() != $this->getProduct()->getId()
                ) {
                    $productIds[$option->getProduct()->getId()] = $option->getProduct()->getId();
                }
            }

            foreach ($productIds as $productId) {
                $option = $this->getOptionByCode('product_qty_' . $productId);
                if ($option) {
                    $qtyOptions[$productId] = $option;
                }
            }

            $this->setData('qty_options', $qtyOptions);
        }

        return $qtyOptions;
    }

    /**
     * Set option product with Qty
     *
     * @param  array $qtyOptions
     * @return $this
     */
    public function setQtyOptions($qtyOptions)
    {
        return $this->setData('qty_options', $qtyOptions);
    }

    /**
     * Setup product for quote item
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return $this
     */
    public function setProduct($product)
    {
        if ($this->getQuote()) {
            $product->setStoreId($this->getQuote()->getStoreId());
            $product->setCustomerGroupId($this->getQuote()->getCustomerGroupId());
        }

        $this->setData('product', $product)
            ->setProductId($product->getId())
            ->setProductType($product->getTypeId())
            ->setSku($this->getProduct()->getSku())
            ->setName($product->getName())
            ->setWeight($this->getProduct()->getWeight())
            ->setTaxClassId($product->getTaxClassId())
            ->setBaseCost($product->getCost())
            ->setIsRecurring($product->getIsRecurring());

        if ($product->getStockItem()) {
            $this->setIsQtyDecimal($product->getStockItem()->getIsQtyDecimal());
        }

        Mage::dispatchEvent('sales_quote_item_set_product', [
            'product' => $product,
            'quote_item' => $this,
        ]);

        return $this;
    }

    /**
     * Check product representation in item
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function representProduct($product)
    {
        $itemProduct = $this->getProduct();
        if (!$product || $itemProduct->getId() != $product->getId()) {
            return false;
        }

        /**
         * Check maybe product is planned to be a child of some quote item - in this case we limit search
         * only within same parent item
         */
        $stickWithinParent = $product->getStickWithinParent();
        if ($stickWithinParent && $this->getParentItem() !== $stickWithinParent) {
            return false;
        }

        // Check options
        $itemOptions = $this->getOptionsByCode();
        $productOptions = $product->getCustomOptions();

        if (!$this->compareOptions($itemOptions, $productOptions)) {
            return false;
        }

        return $this->compareOptions($productOptions, $itemOptions);
    }

    /**
     * Check if two options array are identical
     * First options array is prerogative
     * Second options array checked against first one
     *
     * @param  array $options1
     * @param  array $options2
     * @return bool
     */
    public function compareOptions($options1, $options2)
    {
        foreach ($options1 as $option) {
            $code = $option->getCode();
            if (in_array($code, $this->_notRepresentOptions)) {
                continue;
            }

            if (!isset($options2[$code])
                || ($options2[$code]->getValue() === null)
                || $options2[$code]->getValue() != $option->getValue()
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Compare item
     *
     * @param  Mage_Sales_Model_Quote_Item_Abstract $item
     * @return bool
     */
    public function compare($item)
    {
        if ($this->getProductId() != $item->getProductId()) {
            return false;
        }

        foreach ($this->getOptions() as $option) {
            if (in_array($option->getCode(), $this->_notRepresentOptions)
                && !$item->getProduct()->hasCustomOptions()
            ) {
                continue;
            }

            if ($itemOption = $item->getOptionByCode($option->getCode())) {
                $itemOptionValue = $itemOption->getValue();
                $optionValue = $option->getValue();

                // dispose of some options params, that can cramp comparing of arrays
                if (is_string($itemOptionValue) && is_string($optionValue)) {
                    try {
                        /**
                         * @var Mage_Core_Helper_UnserializeArray $parser
                         * @var Mage_Core_Helper_String           $stringHelper
                         */
                        $parser = Mage::helper('core/unserializeArray');
                        $stringHelper = Mage::helper('core/string');

                        // only ever try to unserialize, if it looks like a serialized array
                        $_itemOptionValue = $stringHelper->isSerializedArrayOrObject($itemOptionValue) ? $parser->unserialize($itemOptionValue) : $itemOptionValue;
                        $_optionValue = $stringHelper->isSerializedArrayOrObject($optionValue) ? $parser->unserialize($optionValue) : $optionValue;

                        if (is_array($_itemOptionValue) && is_array($_optionValue)) {
                            $itemOptionValue = $_itemOptionValue;
                            $optionValue = $_optionValue;
                            // looks like it does not break bundle selection qty
                            foreach (['qty', 'uenc', 'form_key', 'item', 'original_qty'] as $key) {
                                unset($itemOptionValue[$key], $optionValue[$key]);
                            }
                        }
                    } catch (Exception $exception) {
                        Mage::logException($exception);
                    }
                }

                if ($itemOptionValue != $optionValue) {
                    return false;
                }
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Get item product type
     *
     * @return string
     */
    public function getProductType()
    {
        if ($option = $this->getOptionByCode('product_type')) {
            return $option->getValue();
        }

        if ($product = $this->getProduct()) {
            return $product->getTypeId();
        }

        return $this->_getData('product_type');
    }

    /**
     * Return real product type of item
     *
     * @return string
     */
    public function getRealProductType()
    {
        return $this->_getData('product_type');
    }

    /**
     * Convert Quote Item to array
     *
     * @return array
     */
    #[Override]
    public function toArray(array $arrAttributes = [])
    {
        $data = parent::toArray($arrAttributes);

        if ($product = $this->getProduct()) {
            $data['product'] = $product->toArray();
        }

        return $data;
    }

    /**
     * Initialize quote item options
     *
     * @param  array $options
     * @return $this
     */
    public function setOptions($options)
    {
        foreach ($options as $option) {
            $this->addOption($option);
        }

        return $this;
    }

    /**
     * Get all item options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Get all item options as array with codes in array key
     *
     * @return array
     */
    public function getOptionsByCode()
    {
        return $this->_optionsByCode;
    }

    /**
     * Add option to item
     *
     * @param  array|Mage_Sales_Model_Quote_Item_Option|Varien_Object $option
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function addOption($option)
    {
        if (is_array($option)) {
            $option = Mage::getModel('sales/quote_item_option')->setData($option)
                ->setItem($this);
        } elseif (($option instanceof Varien_Object) && !($option instanceof Mage_Sales_Model_Quote_Item_Option)) {
            $option = Mage::getModel('sales/quote_item_option')->setData($option->getData())
                ->setProduct($option->getProduct())
                ->setItem($this);
        } elseif ($option instanceof Mage_Sales_Model_Quote_Item_Option) {
            $option->setItem($this);
        } else {
            Mage::throwException(Mage::helper('sales')->__('Invalid item option format.'));
        }

        if ($exOption = $this->getOptionByCode($option->getCode())) {
            $exOption->addData($option->getData());
        } else {
            $this->_addOptionCode($option);
            $this->_options[] = $option;
        }

        return $this;
    }

    /**
     * Can specify specific actions for ability to change given quote options values
     * Example: cataloginventory decimal qty validation may change qty to int,
     * so need to change quote item qty option value.
     *
     * @param  null|float|int $value
     * @return $this
     */
    public function updateQtyOption(Varien_Object $option, $value)
    {
        $optionProduct = $option->getProduct();
        $options = $this->getQtyOptions();

        if (isset($options[$optionProduct->getId()])) {
            $options[$optionProduct->getId()]->setValue($value);
        }

        $this->getProduct()->getTypeInstance(true)
            ->updateQtyOption($this->getOptions(), $option, $value, $this->getProduct());

        return $this;
    }

    /**
     *Remove option from item options
     *
     * @param  string $code
     * @return $this
     */
    public function removeOption($code)
    {
        $option = $this->getOptionByCode($code);
        if ($option) {
            $option->isDeleted(true);
        }

        return $this;
    }

    /**
     * Register option code
     *
     * @param  Mage_Sales_Model_Quote_Item_Option $option
     * @return $this
     */
    protected function _addOptionCode($option)
    {
        if (!isset($this->_optionsByCode[$option->getCode()])) {
            $this->_optionsByCode[$option->getCode()] = $option;
        } else {
            Mage::throwException(Mage::helper('sales')->__('An item option with code %s already exists.', $option->getCode()));
        }

        return $this;
    }

    /**
     * Get item option by code
     *
     * @param  string                                  $code
     * @return null|Mage_Sales_Model_Quote_Item_Option
     */
    public function getOptionByCode($code)
    {
        if (isset($this->_optionsByCode[$code]) && !$this->_optionsByCode[$code]->isDeleted()) {
            return $this->_optionsByCode[$code];
        }

        return null;
    }

    /**
     * Checks that item model has data changes.
     * Call save item options if model isn't need to save in DB
     *
     * @return bool
     */
    #[Override]
    protected function _hasModelChanged()
    {
        if (!$this->hasDataChanges()) {
            return false;
        }

        return $this->_getResource()->hasDataChanged($this);
    }

    /**
     * Save item options
     *
     * @return $this
     */
    protected function _saveItemOptions()
    {
        foreach ($this->_options as $index => $option) {
            if ($option->isDeleted()) {
                // phpcs:ignore Ecg.Performance.Loop.ModelLSD
                $option->delete();
                unset($this->_options[$index]);
                unset($this->_optionsByCode[$option->getCode()]);
            } else {
                // phpcs:ignore Ecg.Performance.Loop.ModelLSD
                $option->save();
            }
        }

        $this->_flagOptionsSaved = true; // Report to watchers that options were saved

        return $this;
    }

    /**
     * Save model plus its options
     * Ensures saving options in case when resource model was not changed
     */
    #[Override]
    public function save()
    {
        $hasDataChanges = $this->hasDataChanges();
        $this->_flagOptionsSaved = false;

        parent::save();

        if ($hasDataChanges && !$this->_flagOptionsSaved) {
            $this->_saveItemOptions();
        }

        return $this;
    }

    /**
     * Save item options after item saved
     *
     * @inheritDoc
     */
    #[Override]
    protected function _afterSave()
    {
        $this->_saveItemOptions();
        return parent::_afterSave();
    }

    /**
     * Clone quote item
     */
    #[Override]
    public function __clone()
    {
        parent::__clone();
        $options = $this->getOptions();
        $this->_quote = null;
        $this->_options = [];
        $this->_optionsByCode = [];
        foreach ($options as $option) {
            $this->addOption(clone $option);
        }
    }

    /**
     * Returns formatted buy request - object, holding request received from
     * product view page with keys and options for configured product
     *
     * @return Varien_Object
     */
    public function getBuyRequest()
    {
        $option = $this->getOptionByCode('info_buyRequest');
        $buyRequest = new Varien_Object($option ? unserialize($option->getValue(), ['allowed_classes' => false]) : null);

        // Overwrite standard buy request qty, because item qty could have changed since adding to quote
        $buyRequest->setOriginalQty($buyRequest->getQty())
            ->setQty($this->getQty() * 1);

        return $buyRequest;
    }

    /**
     * Sets flag, whether this quote item has some error associated with it.
     *
     * @param  bool  $flag
     * @return $this
     */
    protected function _setHasError($flag)
    {
        return $this->setData('has_error', $flag);
    }

    /**
     * Sets flag, whether this quote item has some error associated with it.
     * When TRUE - also adds 'unknown' error information to list of quote item errors.
     * When FALSE - clears whole list of quote item errors.
     * It's recommended to use addErrorInfo() instead - to be able to remove error statuses later.
     *
     * @param  bool  $flag
     * @return $this
     * @see addErrorInfo()
     */
    public function setHasError($flag)
    {
        if ($flag) {
            $this->addErrorInfo();
        } else {
            $this->_clearErrorInfo();
        }

        return $this;
    }

    /**
     * Clears list of errors, associated with this quote item.
     * Also automatically removes error-flag from oneself.
     *
     * @return $this
     */
    protected function _clearErrorInfo()
    {
        $this->_errorInfos->clear();
        $this->_setHasError(false);
        return $this;
    }

    /**
     * Adds error information to the quote item.
     * Automatically sets error flag.
     *
     * @param  null|string        $origin         Usually a name of module, that embeds error
     * @param  null|int           $code           Error code, unique for origin, that sets it
     * @param  null|string        $message        Error message
     * @param  null|Varien_Object $additionalData Any additional data, that caller would like to store
     * @return $this
     */
    public function addErrorInfo($origin = null, $code = null, $message = null, $additionalData = null)
    {
        $this->_errorInfos->addItem($origin, $code, $message, $additionalData);
        if ($message !== null) {
            $this->setMessage($message);
        }

        $this->_setHasError(true);

        return $this;
    }

    /**
     * Retrieves all error infos, associated with this item
     *
     * @return array
     */
    public function getErrorInfos()
    {
        return $this->_errorInfos->getItems();
    }

    /**
     * Removes error infos, that have parameters equal to passed in $params.
     * $params can have following keys (if not set - then any item is good for this key):
     *   'origin', 'code', 'message'
     *
     * @param  array $params
     * @return $this
     */
    public function removeErrorInfosByParams($params)
    {
        $removedItems = $this->_errorInfos->removeItemsByParams($params);
        foreach ($removedItems as $item) {
            if ($item['message'] !== null) {
                $this->removeMessageByText($item['message']);
            }
        }

        if (!$this->_errorInfos->getItems()) {
            $this->_setHasError(false);
        }

        return $this;
    }

    public function getAdditionalData(): string
    {
        return (string) $this->_getData('additional_data');
    }

    public function getAppliedRuleIds(): ?string
    {
        $value = $this->_getData('applied_rule_ids');
        return $v === null ? null : (string) $v;
    }

    public function getBaseCost(): float
    {
        return (float) $this->_getData('base_cost');
    }

    public function getBaseDiscountAmount(): float
    {
        return (float) $this->_getData('base_discount_amount');
    }

    public function getBaseHiddenTaxAmount(): float
    {
        return (float) $this->_getData('base_hidden_tax_amount');
    }

    public function getBasePrice(): float
    {
        return (float) $this->_getData('base_price');
    }

    public function getBasePriceInclTax(): float
    {
        return (float) $this->_getData('base_price_incl_tax');
    }

    public function getBaseRowTotal(): float
    {
        return (float) $this->_getData('base_row_total');
    }

    public function getBaseRowTotalInclTax(): float
    {
        return (float) $this->_getData('base_row_total_incl_tax');
    }

    public function getBaseTaxBeforeDiscount(): float
    {
        return (float) $this->_getData('base_tax_before_discount');
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

    public function getCost(): float
    {
        return (float) $this->_getData('cost');
    }

    public function getCustomPrice(): float
    {
        return (float) $this->_getData('custom_price');
    }

    public function getDescription(): string
    {
        return (string) $this->_getData('description');
    }

    public function getDiscountAmount(): float
    {
        return (float) $this->_getData('discount_amount');
    }

    public function getDiscountPercent(): float
    {
        return (float) $this->_getData('discount_percent');
    }

    public function getFreeShipping(): int
    {
        return (int) $this->_getData('free_shipping');
    }

    public function getGiftMessageId(): int
    {
        return (int) $this->_getData('gift_message_id');
    }

    public function getHiddenTaxAmount(): float
    {
        return (float) $this->_getData('hidden_tax_amount');
    }

    public function getIsQtyDecimal(): int
    {
        return (int) $this->_getData('is_qty_decimal');
    }

    public function getIsVirtual(): int
    {
        return (int) $this->_getData('is_virtual');
    }

    public function getItemId(): int
    {
        return (int) $this->_getData('item_id');
    }

    public function getMultishippingQty(): int
    {
        return (int) $this->_getData('multishipping_qty');
    }

    public function getName(): string
    {
        return (string) $this->_getData('name');
    }

    public function getNoDiscount(): int
    {
        return (int) $this->_getData('no_discount');
    }

    public function getOriginalCustomPrice(): float
    {
        return (float) $this->_getData('original_custom_price');
    }

    public function getParentItemId(): ?int
    {
        $value = $this->_getData('parent_item_id');
        return $v === null ? null : (int) $v;
    }

    public function getParentProductId(): int
    {
        return (int) $this->_getData('parent_product_id');
    }

    public function getPriceInclTax(): float
    {
        return (float) $this->_getData('price_incl_tax');
    }

    public function getProductId(): int
    {
        return (int) $this->_getData('product_id');
    }

    public function getQtyToAdd(): float
    {
        return (float) $this->_getData('qty_to_add');
    }

    public function getQuoteId(): int
    {
        return (int) $this->_getData('quote_id');
    }

    public function getQuoteItemId(): int
    {
        return (int) $this->_getData('quote_item_id');
    }

    public function getRedirectUrl(): string
    {
        return (string) $this->_getData('redirect_url');
    }

    public function getRowTotal(): float
    {
        return (float) $this->_getData('row_total');
    }

    public function getRowTotalInclTax(): float
    {
        return (float) $this->_getData('row_total_incl_tax');
    }

    public function getRowTotalWithDiscount(): float
    {
        return (float) $this->_getData('row_total_with_discount');
    }

    public function getRowWeight(): float
    {
        return (float) $this->_getData('row_weight');
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

    public function getTaxClassId(): int
    {
        return (int) $this->_getData('tax_class_id');
    }

    public function getTaxPercent(): float
    {
        return (float) $this->_getData('tax_percent');
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

    public function setAppliedRuleIds(string $value): static
    {
        return $this->setData('applied_rule_ids', $value);
    }

    public function setBaseCost(float $value): static
    {
        return $this->setData('base_cost', $value);
    }

    public function setBaseDiscountAmount(float $value): static
    {
        return $this->setData('base_discount_amount', $value);
    }

    public function setBaseHiddenTaxAmount(float $value): static
    {
        return $this->setData('base_hidden_tax_amount', $value);
    }

    public function setBasePrice(float $value): static
    {
        return $this->setData('base_price', $value);
    }

    public function setBasePriceInclTax(float $value): static
    {
        return $this->setData('base_price_incl_tax', $value);
    }

    public function setBaseRowTotal(float $value): static
    {
        return $this->setData('base_row_total', $value);
    }

    public function setBaseRowTotalInclTax(float $value): static
    {
        return $this->setData('base_row_total_incl_tax', $value);
    }

    public function setBaseRowTotalWithDiscount(float $value): static
    {
        return $this->setData('base_row_total_with_discount', $value);
    }

    public function setBaseTaxAmount(float $value): static
    {
        return $this->setData('base_tax_amount', $value);
    }

    public function setBaseTaxBeforeDiscount(float $value): static
    {
        return $this->setData('base_tax_before_discount', $value);
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

    public function setDiscountPercent(float $value): static
    {
        return $this->setData('discount_percent', $value);
    }

    public function setFreeShipping(int $value): static
    {
        return $this->setData('free_shipping', $value);
    }

    public function setGiftMessage(string $value): static
    {
        return $this->setData('gift_message', $value);
    }

    public function setGiftMessageId(int $value): static
    {
        return $this->setData('gift_message_id', $value);
    }

    public function setHiddenTaxAmount(float $value): static
    {
        return $this->setData('hidden_tax_amount', $value);
    }

    public function setIsQtyDecimal(int $value): static
    {
        return $this->setData('is_qty_decimal', $value);
    }

    public function setIsRecurring(int $value): static
    {
        return $this->setData('is_recurring', $value);
    }

    public function setIsVirtual(int $value): static
    {
        return $this->setData('is_virtual', $value);
    }

    public function setMultishippingQty(int $value): static
    {
        return $this->setData('multishipping_qty', $value);
    }

    public function setName(string $value): static
    {
        return $this->setData('name', $value);
    }

    public function setNoDiscount(int $value): static
    {
        return $this->setData('no_discount', $value);
    }

    public function setOriginalCustomPrice(float $value): static
    {
        return $this->setData('original_custom_price', $value);
    }

    public function setParentItemId(int $value): static
    {
        return $this->setData('parent_item_id', $value);
    }

    public function setParentProductId(int $value): static
    {
        return $this->setData('parent_product_id', $value);
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

    public function setQtyToAdd(float $value): static
    {
        return $this->setData('qty_to_add', $value);
    }

    public function setQuoteId(int $value): static
    {
        return $this->setData('quote_id', $value);
    }

    public function setQuoteItemId(int $value): static
    {
        return $this->setData('quote_item_id', $value);
    }

    public function setQuoteMessage(string $value): static
    {
        return $this->setData('quote_message', $value);
    }

    public function setQuoteMessageIndex(string $value): static
    {
        return $this->setData('quote_message_index', $value);
    }

    public function setRedirectUrl(string $value): static
    {
        return $this->setData('redirect_url', $value);
    }

    public function setRowTotal(float $value): static
    {
        return $this->setData('row_total', $value);
    }

    public function setRowTotalInclTax(float $value): static
    {
        return $this->setData('row_total_incl_tax', $value);
    }

    public function setRowTotalWithDiscount(float $value): static
    {
        return $this->setData('row_total_with_discount', $value);
    }

    public function setRowWeight(float $value): static
    {
        return $this->setData('row_weight', $value);
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

    public function setTaxClassId(int $value): static
    {
        return $this->setData('tax_class_id', $value);
    }

    public function setTaxPercent(float $value): static
    {
        return $this->setData('tax_percent', $value);
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
