<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml products in carts report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Shopcart_Product_Grid extends Mage_Adminhtml_Block_Report_Grid_Shopcart
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('gridProducts');
    }

    protected function _prepareCollection()
    {
        /** @var Mage_Reports_Model_Resource_Quote_Collection $collection */
        $collection = Mage::getResourceModel('reports/quote_collection');
        $collection->prepareForProductsInCarts()
            ->setSelectCountSqlType(Mage_Reports_Model_Resource_Quote_Collection::SELECT_COUNT_SQL_TYPE_CART);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', [
            'header'    => Mage::helper('reports')->__('ID'),
            'index'     => 'entity_id',
        ]);

        $this->addColumn('name', [
            'header'    => Mage::helper('reports')->__('Product Name'),
            'index'     => 'name',
        ]);

        $currencyCode = $this->getCurrentCurrencyCode();

        $this->addColumn('price', [
            'type'      => 'currency',
            'currency_code' => $currencyCode,
            'renderer'  => 'adminhtml/report_grid_column_renderer_currency',
            'rate'          => $this->getRate($currencyCode),
        ]);

        $this->addColumn('carts', [
            'header'    => Mage::helper('reports')->__('Carts'),
            'width'     => '80px',
            'align'     => 'right',
            'index'     => 'carts',
        ]);

        $this->addColumn('orders', [
            'header'    => Mage::helper('reports')->__('Orders'),
            'width'     => '80px',
            'align'     => 'right',
            'index'     => 'orders',
        ]);

        $this->setFilterVisibility(false);

        $this->addExportType('*/*/exportProductCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportProductExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_product/edit', ['id' => $row->getEntityId()]);
    }
}
