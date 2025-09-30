<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

/**
 * Catalog product related items block
 *
 * @package    Mage_Bundle
 */
class Mage_Bundle_Block_Catalog_Product_List_Partof extends Mage_Catalog_Block_Product_Abstract
{
    protected $_columnCount = 4;
    protected $_items;
    protected $_itemCollection;
    protected $_product = null;

    /**
     * @return $this
     */
    protected function _prepareData()
    {
        $collection = Mage::getModel('catalog/product')->getResourceCollection()
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addAttributeToSort('position', 'asc')
            ->addStoreFilter()
            ->addAttributeToFilter('status', [
                'in' => Mage::getSingleton('catalog/product_status')->getSaleableStatusIds(),
            ])
            ->addMinimalPrice()
            ->joinTable('bundle/option', 'parent_id=entity_id', ['option_id' => 'option_id'])
            ->joinTable('bundle/selection', 'option_id=option_id', ['product_id' => 'product_id'], '{{table}}.product_id=' . $this->getProduct()->getId());

        $ids = Mage::getSingleton('checkout/cart')->getProductIds();

        if (count($ids)) {
            $collection->addIdFilter(Mage::getSingleton('checkout/cart')->getProductIds(), true);
        }

        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        $collection->getSelect()->group('entity_id');

        $collection->load();
        $this->_itemCollection = $collection;

        return $this;
    }

    /**
     * @return Mage_Catalog_Block_Product_Abstract
     */
    protected function _beforeToHtml()
    {
        $this->_prepareData();
        return parent::_beforeToHtml();
    }

    /**
     * @return mixed
     */
    public function getItemCollection()
    {
        return $this->_itemCollection;
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        if (is_null($this->_items)) {
            $this->_items = $this->getItemCollection()->getItems();
        }
        return $this->_items;
    }

    /**
     * @return float
     */
    public function getRowCount()
    {
        return ceil($this->getItemCollection()->getSize() / $this->getColumnCount());
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function setColumnCount($columns)
    {
        if ((int) $columns > 0) {
            $this->_columnCount = (int) $columns;
        }
        return $this;
    }

    /**
     * @return int
     */
    public function getColumnCount()
    {
        return $this->_columnCount;
    }

    public function resetItemsIterator()
    {
        $this->getItems();
        reset($this->_items);
    }

    /**
     * @return mixed
     */
    public function getIterableItem()
    {
        $item = current($this->_items);
        next($this->_items);
        return $item;
    }

    /**
     * Get current product from registry
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = Mage::registry('product');
        }
        return $this->_product;
    }
}
