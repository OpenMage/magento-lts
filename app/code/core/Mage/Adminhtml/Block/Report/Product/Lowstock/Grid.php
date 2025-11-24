<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml low stock products report grid block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Product_Lowstock_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('gridLowstock');
        $this->setUseAjax(false);
    }

    protected function _prepareCollection()
    {
        if ($this->getRequest()->getParam('website')) {
            $storeIds = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } elseif ($this->getRequest()->getParam('group')) {
            $storeIds = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } elseif ($this->getRequest()->getParam('store')) {
            $storeId = (int) $this->getRequest()->getParam('store');
        } else {
            $storeId = '';
        }

        /** @var Mage_Reports_Model_Resource_Product_Lowstock_Collection $collection */
        $collection = Mage::getResourceModel('reports/product_lowstock_collection')
            ->addAttributeToSelect('*')
            ->setStoreId($storeId)
            ->filterByIsQtyProductTypes()
            ->joinInventoryItem('qty')
            ->useManageStockFilter($storeId)
            ->useNotifyStockQtyFilter($storeId)
            ->setOrder('qty', Varien_Data_Collection::SORT_ORDER_ASC);

        if ($storeId) {
            $collection->addStoreFilter($storeId);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', [
            'header'    => Mage::helper('reports')->__('Product Name'),
            'sortable'  => false,
            'index'     => 'name',
        ]);

        $this->addColumn('sku', [
            'header'    => Mage::helper('reports')->__('Product SKU'),
            'sortable'  => false,
            'index'     => 'sku',
        ]);

        $this->addColumn('qty', [
            'header'    => Mage::helper('reports')->__('Stock Qty'),
            'width'     => '215px',
            'sortable'  => false,
            'filter'    => 'adminhtml/widget_grid_column_filter_range',
            'index'     => 'qty',
            'type'      => 'number',
        ]);

        $this->addExportType('*/*/exportLowstockCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportLowstockExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
