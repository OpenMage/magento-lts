<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml reviews by customers report grid block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Review_Customer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customers_grid');
        $this->setDefaultSort('review_cnt');
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('reports/review_customer_collection')
            ->joinCustomers();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('customer_name', [
            'header'    => Mage::helper('reports')->__('Customer Name'),
            'index'     => 'customer_name',
            'default'   => Mage::helper('reports')->__('Guest'),
        ]);

        $this->addColumn('review_cnt', [
            'header'    => Mage::helper('reports')->__('Number Of Reviews'),
            'width'     => '40px',
            'align'     => 'right',
            'index'     => 'review_cnt',
        ]);

        $this->addColumn('action', [
            'type'      => 'action',
            'width'     => '100',
            'align'     => 'center',
            'renderer'  => 'adminhtml/report_grid_column_renderer_customer',
            'is_system' => true,
        ]);

        $this->setFilterVisibility(false);

        $this->addExportType('*/*/exportCustomerCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportCustomerExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_product_review', ['customerId' => $row->getCustomerId()]);
    }
}
