<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml customer cart items grid block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_View_Cart extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Mage_Adminhtml_Block_Customer_Edit_Tab_View_Cart constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_view_cart_grid');
        $this->setDefaultSort('added_at');
        $this->setDefaultDir('desc');
        $this->setSortable(false);
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
        $this->setEmptyText(Mage::helper('customer')->__('There are no items in customer\'s shopping cart at the moment'));
    }

    /**
     * @inheritDoc
     * @throws Mage_Core_Exception
     */
    protected function _prepareCollection()
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = Mage::getModel('sales/quote');
        // set website to quote, if any
        if ($this->getWebsiteId()) {
            $quote->setWebsite(Mage::app()->getWebsite($this->getWebsiteId()));
        }

        $quote->loadByCustomer(Mage::registry('current_customer'));

        $collection = $quote ? $quote->getItemsCollection(false) : new Varien_Data_Collection();

        $collection->addFieldToFilter('parent_item_id', ['null' => true]);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $currencyCode = Mage_Directory_Helper_Data::getConfigCurrencyBase();
        $this->addColumn('product_id', [
            'header' => Mage::helper('customer')->__('Product ID'),
            'index' => 'product_id',
            'width' => '100px',
        ])->addColumn('name', [
            'header' => Mage::helper('customer')->__('Product Name'),
            'index' => 'name',
        ])->addColumn('sku', [
            'header' => Mage::helper('customer')->__('SKU'),
            'index' => 'sku',
            'width' => '100px',
        ])->addColumn('qty', [
            'header' => Mage::helper('customer')->__('Qty'),
            'index' => 'qty',
            'type'  => 'number',
            'width' => '60px',
        ])->addColumn('price', [
            'type'  => 'currency',
            'currency_code' => $currencyCode,
        ])->addColumn('total', [
            'header' => Mage::helper('customer')->__('Total'),
            'index' => 'row_total',
            'type'  => 'currency',
            'currency_code' => $currencyCode,
        ]);

        return parent::_prepareColumns();
    }

    /**
     * Retrieve row url
     *
     * @param Mage_Sales_Model_Quote_Item $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_product/edit', ['id' => $row->getProductId()]);
    }

    /**
     * Check weather header should be shown
     *
     * @return bool
     */
    public function getHeadersVisibility()
    {
        return ($this->getCollection()->getSize() > 0);
    }
}
