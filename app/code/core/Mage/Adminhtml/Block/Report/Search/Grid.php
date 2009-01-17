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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml search report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Search_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('searchReportGrid');
        $this->setDefaultSort('query_id');
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection()
    {

        $collection = Mage::getResourceModel('catalogsearch/query_collection');
        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('query_id', array(
            'header'    =>Mage::helper('reports')->__('ID'),
            'width'     =>'50px',
            'filter'    =>false,
            'index'     =>'query_id',
            'type'      =>'number'
        ));

        $this->addColumn('query_text', array(
            'header'    =>__('Search Query'),
            'filter'    =>false,
            'index'     =>'query_text'
        ));

        $this->addColumn('num_results', array(
            'header'    =>Mage::helper('reports')->__('Results'),
            'width'     =>'50px',
            'align'     =>'right',
            'type'      =>'number',
            'index'     =>'num_results'
        ));

        $this->addColumn('popularity', array(
            'header'    =>Mage::helper('reports')->__('Hits'),
            'width'     =>'50px',
            'align'     =>'right',
            'type'      =>'number',
            'index'     =>'popularity'
        ));

        $this->addExportType('*/*/exportSearchCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportSearchExcel', Mage::helper('reports')->__('Excel'));

        return parent::_prepareColumns();
    }

}

