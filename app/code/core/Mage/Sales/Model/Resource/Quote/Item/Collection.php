<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Quote item resource collection
 *
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Quote_Item getItemById(int $value)
 * @method Mage_Sales_Model_Quote_Item[] getItems()
 */
class Mage_Sales_Model_Resource_Quote_Item_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Collection quote instance
     *
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote;

    /**
     * Product Ids array
     *
     * @var array
     */
    protected $_productIds   = [];

    protected function _construct()
    {
        $this->_init('sales/quote_item');
    }

    /**
     * Retrieve store Id (From Quote)
     *
     * @return int
     */
    public function getStoreId()
    {
        return (int) $this->_quote->getStoreId();
    }

    /**
     * Set Quote object to Collection
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return $this
     */
    public function setQuote($quote)
    {
        $this->_quote = $quote;
        $quoteId      = $quote->getId();
        if ($quoteId) {
            $this->addFieldToFilter('quote_id', $quote->getId());
        } else {
            $this->_totalRecords = 0;
            $this->_setIsLoaded(true);
        }

        return $this;
    }

    /**
     * Reset the collection and inner join it to quotes table
     * Optionally can select items with specified product id only
     *
     * @param string $quotesTableName
     * @param int $productId
     * @return $this
     */
    public function resetJoinQuotes($quotesTableName, $productId = null)
    {
        $this->getSelect()->reset()
            ->from(
                ['qi' => $this->getResource()->getMainTable()],
                ['item_id', 'qty', 'quote_id'],
            )
            ->joinInner(
                ['q' => $quotesTableName],
                'qi.quote_id = q.entity_id',
                ['store_id', 'items_qty', 'items_count'],
            );
        if ($productId) {
            $this->getSelect()->where('qi.product_id = ?', (int) $productId);
        }

        return $this;
    }

    /**
     * After load processing
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        /**
         * Assign parent items
         */
        foreach ($this as $item) {
            if ($item->getParentItemId()) {
                $item->setParentItem($this->getItemById($item->getParentItemId()));
            }

            if ($this->_quote) {
                $item->setQuote($this->_quote);
            }
        }

        /**
         * Assign options and products
         */
        $this->_assignOptions();
        $this->_assignProducts();
        $this->resetItemsDataChanged();

        return $this;
    }

    /**
     * Add options to items
     *
     * @return $this
     */
    protected function _assignOptions()
    {
        $itemIds          = array_keys($this->_items);
        $optionCollection = Mage::getModel('sales/quote_item_option')->getCollection()
            ->addItemFilter($itemIds);
        foreach ($this as $item) {
            $item->setOptions($optionCollection->getOptionsByItem($item));
        }

        $productIds        = $optionCollection->getProductIds();
        $this->_productIds = array_merge($this->_productIds, $productIds);

        return $this;
    }

    /**
     * Add products to items and item options
     *
     * @return $this
     */
    protected function _assignProducts()
    {
        Varien_Profiler::start('QUOTE:' . __METHOD__);
        $productFlatHelper = Mage::helper('catalog/product_flat');
        $productFlatHelper->disableFlatCollection();

        $productIds = [];
        foreach ($this as $item) {
            $productIds[] = (int) $item->getProductId();
        }

        $this->_productIds = array_merge($this->_productIds, $productIds);

        $productCollection = Mage::getModel('catalog/product')->getCollection()
            ->setStoreId($this->getStoreId())
            ->addIdFilter($this->_productIds)
            ->addAttributeToSelect(Mage::getSingleton('sales/quote_config')->getProductAttributes())
            ->addOptionsToResult()
            ->addStoreFilter()
            ->addUrlRewrite()
            ->addTierPriceData();

        Mage::dispatchEvent('prepare_catalog_product_collection_prices', [
            'collection'            => $productCollection,
            'store_id'              => $this->getStoreId(),
        ]);
        Mage::dispatchEvent('sales_quote_item_collection_products_after_load', [
            'product_collection'    => $productCollection,
        ]);

        $recollectQuote = false;
        foreach ($this as $item) {
            $product = $productCollection->getItemById($item->getProductId());
            if ($product) {
                $product->setCustomOptions([]);
                $qtyOptions         = [];
                $optionProductIds   = [];
                foreach ($item->getOptions() as $option) {
                    /**
                     * Call type-specific logic for product associated with quote item
                     */
                    $product->getTypeInstance(true)->assignProductToOption(
                        $productCollection->getItemById($option->getProductId()),
                        $option,
                        $product,
                    );

                    if (is_object($option->getProduct()) && $option->getProduct()->getId() != $product->getId()) {
                        $optionProductIds[$option->getProduct()->getId()] = $option->getProduct()->getId();
                    }
                }

                foreach ($optionProductIds as $optionProductId) {
                    $qtyOption = $item->getOptionByCode('product_qty_' . $optionProductId);
                    if ($qtyOption) {
                        $qtyOptions[$optionProductId] = $qtyOption;
                    }
                }

                $item->setQtyOptions($qtyOptions)->setProduct($product);
            } else {
                $item->isDeleted(true);
                $recollectQuote = true;
            }

            $item->checkData();
        }

        if ($recollectQuote && $this->_quote) {
            $this->_quote->collectTotals();
        }

        $productFlatHelper->resetFlatCollection();
        Varien_Profiler::stop('QUOTE:' . __METHOD__);
        return $this;
    }
}
