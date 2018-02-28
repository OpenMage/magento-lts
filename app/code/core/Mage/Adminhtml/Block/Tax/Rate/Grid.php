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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Adminhtml_Block_Tax_Rate_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('region_name');
        $this->setDefaultDir('asc');
        $this->setId('tax_rate_grid');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $rateCollection = Mage::getModel('tax/calculation_rate')->getCollection()
            ->joinRegionTable();

        $this->setCollection($rateCollection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('code', array(
            'header'        => Mage::helper('tax')->__('Tax Identifier'),
            'header_export' => Mage::helper('tax')->__('Code'),
            'align'         =>'left',
            'index'         => 'code',
            'filter_index'  => 'main_table.code',
        ));

        $this->addColumn('tax_country_id', array(
            'header'        => Mage::helper('tax')->__('Country'),
            'type'          => 'country',
            'align'         => 'left',
            'index'         => 'tax_country_id',
            'filter_index'  => 'main_table.tax_country_id',
            'renderer'      => 'adminhtml/tax_rate_grid_renderer_country',
            'sortable'      => false
        ));

        $this->addColumn('region_name', array(
            'header'        => Mage::helper('tax')->__('State/Region'),
            'header_export' => Mage::helper('tax')->__('State'),
            'align'         =>'left',
            'index'         => 'region_name',
            'filter_index'  => 'region_table.code',
            'default'       => '*',
        ));

        $this->addColumn('tax_postcode', array(
            'header'        => Mage::helper('tax')->__('Zip/Post Code'),
            'align'         =>'left',
            'index'         => 'tax_postcode',
            'default'       => '*',
        ));

        $this->addColumn('rate', array(
            'header'        => Mage::helper('tax')->__('Rate'),
            'align'         =>'right',
            'index'         => 'rate',
            'type'          => 'number',
            'default'       => '0.00',
            'renderer'      => 'adminhtml/tax_rate_grid_renderer_data',
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('tax')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('tax')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('rate' => $row->getTaxCalculationRateId()));
    }

}

