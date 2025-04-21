<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml search report grid block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Search_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Initialize Grid Properties
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('searchReportGrid');
        $this->setDefaultSort('query_id');
        $this->setDefaultDir('desc');
    }

    /**
     * Prepare Search Report collection for grid
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('catalogsearch/query_collection');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare Grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('query_id', [
            'header'    => Mage::helper('reports')->__('ID'),
            'width'     => '50px',
            'filter'    => false,
            'index'     => 'query_id',
            'type'      => 'number',
        ]);

        $this->addColumn('query_text', [
            'header'    => Mage::helper('reports')->__('Search Query'),
            'index'     => 'query_text',
        ]);

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', [
                'header'        => Mage::helper('catalog')->__('Store'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_view'    => true,
                'sortable'      => false,
            ]);
        }

        $this->addColumn('num_results', [
            'header'    => Mage::helper('reports')->__('Results'),
            'width'     => '50px',
            'type'      => 'number',
            'index'     => 'num_results',
        ]);

        $this->addColumn('popularity', [
            'header'    => Mage::helper('reports')->__('Hits'),
            'width'     => '50px',
            'type'      => 'number',
            'index'     => 'popularity',
        ]);

        $this->addExportType('*/*/exportSearchCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportSearchExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * Retrieve Row Click callback URL
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_search/edit', ['id' => $row->getId()]);
    }
}
