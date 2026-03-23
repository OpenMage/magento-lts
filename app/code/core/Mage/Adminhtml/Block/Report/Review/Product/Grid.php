<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml reviews by products report grid block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Review_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('gridProducts');
        $this->setDefaultSort('review_cnt');
    }

    /**
     * @inheritDoc
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('reports/review_product_collection')
            ->joinReview();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
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

        $this->addColumn('review_cnt', [
            'header'    => Mage::helper('reports')->__('Number of Reviews'),
            'width'     => '50px',
            'align'     => 'right',
            'index'     => 'review_cnt',
        ]);

        $this->addColumn('avg_rating', [
            'header'    => Mage::helper('reports')->__('Avg. Rating'),
            'width'     => '50px',
            'align'     => 'right',
            'index'     => 'avg_rating',
        ]);

        $this->addColumn('avg_rating_approved', [
            'header'    => Mage::helper('reports')->__('Avg. Approved Rating'),
            'width'     => '50px',
            'align'     => 'right',
            'index'     => 'avg_rating_approved',
        ]);

        $this->addColumn('last_created', [
            'header'    => Mage::helper('reports')->__('Last Review'),
            'width'     => '150px',
            'index'     => 'last_created',
            'type'      => 'datetime',
        ]);

        $this->addColumn('action', [
            'type'      => 'action',
            'width'     => '100',
            'align'     => 'center',
            'renderer'  => 'adminhtml/report_grid_column_renderer_product',
            'is_system' => true,
        ]);

        $this->setFilterVisibility(false);

        $this->addExportType('*/*/exportProductCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportProductExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * @inheritDoc
     * @param  Mage_Catalog_Model_Product $row
     * @throws Mage_Core_Exception
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_product_review/', ['productId' => $row->getId()]);
    }
}
