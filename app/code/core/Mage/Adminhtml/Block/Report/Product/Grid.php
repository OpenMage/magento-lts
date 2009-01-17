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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml products report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('productsReportGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection()
    {

        $collection = Mage::getResourceModel('reports/product_collection');
        $collection->getEntity()->setStore(0);

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _afterLoadCollection()
    {
        $totalObj = new Mage_Reports_Model_Totals();
        $this->setTotals($totalObj->countTotals($this));
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    =>Mage::helper('reports')->__('ID'),
            'width'     =>'50px',
            'index'     =>'entity_id',
            'total'     =>'Total'
        ));

        $this->addColumn('name', array(
            'header'    =>Mage::helper('reports')->__('Name'),
            'index'     =>'name'
        ));

        $this->addColumn('viewed', array(
            'header'    =>Mage::helper('reports')->__('Number Viewed'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'viewed',
            'total'     =>'sum'
        ));

        $this->addColumn('added', array(
            'header'    =>Mage::helper('reports')->__('Number Added'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'added',
            'total'     =>'sum'
        ));

        $this->addColumn('purchased', array(
            'header'    =>Mage::helper('reports')->__('Number Purchased'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'purchased',
            'total'     =>'sum'
        ));

        $this->addColumn('fulfilled', array(
            'header'    =>Mage::helper('reports')->__('Number Fulfilled'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'fulfilled',
            'total'     =>'sum'
        ));

        $this->addColumn('revenue', array(
            'header'    =>Mage::helper('reports')->__('Revenue'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'revenue',
            'total'     =>'sum'
        ));

        $this->setCountTotals(true);

        $this->addExportType('*/*/exportProductsCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportProductsExcel', Mage::helper('reports')->__('Excel'));

        return parent::_prepareColumns();
    }

}

