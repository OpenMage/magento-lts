<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml products in carts report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
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
            'width'     => '50px',
            'align'     => 'right',
            'index'     => 'entity_id'
        ]);

        $this->addColumn('name', [
            'header'    => Mage::helper('reports')->__('Product Name'),
            'index'     => 'name'
        ]);

        $currencyCode = $this->getCurrentCurrencyCode();

        $this->addColumn('price', [
            'header'    => Mage::helper('reports')->__('Price'),
            'width'     => '80px',
            'type'      => 'currency',
            'currency_code' => $currencyCode,
            'index'     => 'price',
            'renderer'  => 'adminhtml/report_grid_column_renderer_currency',
            'rate'          => $this->getRate($currencyCode),
        ]);

        $this->addColumn('carts', [
            'header'    => Mage::helper('reports')->__('Carts'),
            'width'     => '80px',
            'align'     => 'right',
            'index'     => 'carts'
        ]);

        $this->addColumn('orders', [
            'header'    => Mage::helper('reports')->__('Orders'),
            'width'     => '80px',
            'align'     => 'right',
            'index'     => 'orders'
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
