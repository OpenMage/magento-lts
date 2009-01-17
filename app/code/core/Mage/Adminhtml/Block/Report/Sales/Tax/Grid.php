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
 * Adminhtml tax report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Sales_Tax_Grid extends Mage_Adminhtml_Block_Report_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('gridTax');
    }

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()->initReport('reports/tax_collection');
    }

    protected function _prepareColumns()
    {
        $this->addColumn('tax_title', array(
            'header'    =>Mage::helper('reports')->__('Tax'),
            'index'     =>'tax_title',
            'type'      =>'string'
        ));

        $this->addColumn('tax_rate', array(
            'header'    =>Mage::helper('reports')->__('Rate'),
            'index'     =>'tax_rate',
            'type'      =>'number',
            'width'     =>'100'
        ));

        $this->addColumn('orders', array(
            'header'    =>Mage::helper('reports')->__('Number of Orders'),
            'index'     =>'orders',
            'total'     =>'sum',
            'type'      =>'number',
            'width'     =>'100'
        ));

        $this->addColumn('tax', array(
            'header'    =>Mage::helper('reports')->__('Tax Amount'),
            'type'      =>'currency',
            'currency_code'=>(string) Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode(),
            'index'     =>'tax',
            'total'     =>'sum',
            'renderer'  =>'adminhtml/report_grid_column_renderer_currency'
        ));

        $this->addExportType('*/*/exportTaxCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportTaxExcel', Mage::helper('reports')->__('Excel'));

        return parent::_prepareColumns();
    }
}