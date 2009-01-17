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
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Quote item collection
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Sales_Model_Mysql4_Quote_Item_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Collection quote instance
     *
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote;
    protected $_productIds = array();

    protected function _construct()
    {
        $this->_init('sales/quote_item');
    }

    public function getStoreId()
    {
        return $this->_quote->getStoreId();
    }

    public function setQuote($quote)
    {
        $this->_quote = $quote;
        $this->addFieldToFilter('quote_id', $quote->getId());
        return $this;
    }

    /**
     * Reset the collection and inner join it to quotes table
     *
     * Optionally can select items with specified product id only
     *
     * @param string $quotesTableName
     * @param int $productId
     * @return Mage_Sales_Model_Mysql4_Quote_Item_Collection
     */
    public function resetJoinQuotes($quotesTableName, $productId = null)
    {
        $this->getSelect()
            ->reset()
            ->from(array('qi' => $this->getResource()->getMainTable()), array('item_id', 'qty', 'quote_id'));
        $this->getSelect()
            ->joinInner(array('q' => $quotesTableName), 'qi.quote_id=q.entity_id', array('store_id', 'items_qty', 'items_count'));
        if ($productId) {
            $this->getSelect()->where('qi.product_id=?', $productId);
        }
        return $this;
    }

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
        }

        /**
         * Assign options and products
         */
        $this->_assignOptions()
            ->_assignProducts();
        return $this;
    }

    /**
     * Add options to items
     *
     * @return Mage_Sales_Model_Mysql4_Quote_Item_Collection
     */
    protected function _assignOptions()
    {
        $itemIds = array_keys($this->_items);
        $optionCollection = Mage::getModel('sales/quote_item_option')->getCollection()
            ->addItemFilter($itemIds);
        foreach ($this as $item) {
            $item->setOptions($optionCollection->getOptionsByItem($item));
        }
        $productIds = $optionCollection->getProductIds();
        $this->_productIds = array_merge($this->_productIds, $productIds);
        return $this;
    }

    /**
     * Add products to items and item options
     *
     * @return Mage_Sales_Model_Mysql4_Quote_Item_Collection
     */
    protected function _assignProducts()
    {
        Varien_Profiler::start('QUOTE:'.__METHOD__);
        $productIds = array();
        foreach ($this as $item) {
            $productIds[] = $item->getProductId();
        }
        $this->_productIds = array_merge($this->_productIds, $productIds);

        $productCollection = Mage::getModel('catalog/product')->getCollection()
            ->setStoreId($this->getStoreId())
            ->addIdFilter($this->_productIds)
            ->addAttributeToSelect(Mage::getSingleton('sales/quote_config')->getProductAttributes())
            ->addOptionsToResult()
            ->addStoreFilter()
            ->addUrlRewrite();

        $recollectQuote = false;
        foreach ($this as $item) {
            if ($this->_quote) {
                $item->setQuote($this->_quote);
            }

            if ($product = $productCollection->getItemById($item->getProductId())) {
                $product->setCustomOptions(array());

                foreach ($item->getOptions() as $option) {
                    if ($optionProduct = $productCollection->getItemById($option->getProductId())) {
                        $option->setProduct($optionProduct);
                    }
                    else {
                        $option->setProduct($product);
                    }
                }
                $item->setProduct($product);
            }
            else {
                $item->isDeleted(true);
                $recollectQuote = true;
            }
            $item->checkData();
        }

        if ($recollectQuote && $this->_quote) {
            $this->_quote->collectTotals();
        }
        Varien_Profiler::stop('QUOTE:'.__METHOD__);
        return $this;
    }
}