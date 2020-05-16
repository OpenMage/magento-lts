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
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Quote item resource collection
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
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
    protected $_productIds   = array();

    /**
     * Initialize resource model
     *
     */
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
        return (int)$this->_quote->getStoreId();
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
                array('qi' => $this->getResource()->getMainTable()),
                array('item_id', 'qty', 'quote_id'))
            ->joinInner(
                array('q' => $quotesTableName),
               'qi.quote_id = q.entity_id',
                array('store_id', 'items_qty', 'items_count')
            );
        if ($productId) {
            $this->getSelect()->where('qi.product_id = ?', (int)$productId);
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
        Varien_Profiler::start('QUOTE:'.__METHOD__);
        $productFlatHelper = Mage::helper('catalog/product_flat');
        $productFlatHelper->disableFlatCollection();

        $productIds = array();
        foreach ($this as $item) {
            $productIds[] = (int)$item->getProductId();
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

        Mage::dispatchEvent('prepare_catalog_product_collection_prices', array(
            'collection'            => $productCollection,
            'store_id'              => $this->getStoreId(),
        ));
        Mage::dispatchEvent('sales_quote_item_collection_products_after_load', array(
            'product_collection'    => $productCollection
        ));

        $recollectQuote = false;
        foreach ($this as $item) {
            $product = $productCollection->getItemById($item->getProductId());
            if ($product) {
                $product->setCustomOptions(array());
                $qtyOptions         = array();
                $optionProductIds   = array();
                foreach ($item->getOptions() as $option) {
                    /**
                     * Call type-specific logic for product associated with quote item
                     */
                    $product->getTypeInstance(true)->assignProductToOption(
                        $productCollection->getItemById($option->getProductId()),
                        $option,
                        $product
                    );

                    if (is_object($option->getProduct()) && $option->getProduct()->getId() != $product->getId()) {
                        $optionProductIds[$option->getProduct()->getId()] = $option->getProduct()->getId();
                    }
                }

                if ($optionProductIds) {
                    foreach ($optionProductIds as $optionProductId) {
                        $qtyOption = $item->getOptionByCode('product_qty_' . $optionProductId);
                        if ($qtyOption) {
                            $qtyOptions[$optionProductId] = $qtyOption;
                        }
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
        Varien_Profiler::stop('QUOTE:'.__METHOD__);
        return $this;
    }
}

