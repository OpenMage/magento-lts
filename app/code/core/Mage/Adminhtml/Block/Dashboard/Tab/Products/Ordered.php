<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml dashboard most ordered products grid
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Dashboard_Tab_Products_Ordered extends Mage_Adminhtml_Block_Dashboard_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('productsOrderedGrid');
    }

    protected function _prepareCollection()
    {
        if (!$this->isModuleEnabled('Mage_Sales')) {
            return $this;
        }

        if ($this->getParam('website')) {
            $storeIds = Mage::app()->getWebsite($this->getParam('website'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } elseif ($this->getParam('group')) {
            $storeIds = Mage::app()->getGroup($this->getParam('group'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } else {
            $storeId = (int) $this->getParam('store');
        }

        $collection = Mage::getResourceModel('sales/report_bestsellers_collection')
            ->setModel('catalog/product')
            ->addStoreFilter($storeId)
        ;

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('name', [
            'header'    => Mage::helper('sales')->__('Product Name'),
            'sortable'  => false,
            'index'     => 'product_name',
        ]);

        $this->addColumn('price', [
            'header'    => Mage::helper('sales')->__('Price'),
            'width'     => '120px',
            'type'      => 'currency',
            'currency_code' => (string) Mage::app()->getStore((int) $this->getParam('store'))->getBaseCurrencyCode(),
            'sortable'  => false,
            'index'     => 'product_price',
        ]);

        $this->addColumn('ordered_qty', [
            'header'    => Mage::helper('sales')->__('Quantity Ordered'),
            'width'     => '120px',
            'sortable'  => false,
            'index'     => 'qty_ordered',
            'type'      => 'number',
        ]);

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }

    /**
     * Returns row url to show in admin dashboard
     * $row is bestseller row wrapped in Product model
     *
     * @param Mage_Catalog_Model_Product $row
     * @return string
     * @throws Exception
     */
    public function getRowUrl($row)
    {
        // getId() would return id of bestseller row, and product id we get by getProductId()
        $productId = $row->getProductId();

        // No url is possible for non-existing products
        if (!$productId) {
            return '';
        }

        $params = ['id' => $productId];
        if ($this->getRequest()->getParam('store')) {
            $params['store'] = $this->getRequest()->getParam('store');
        }

        return $this->getUrl('*/catalog_product/edit', $params);
    }
}
