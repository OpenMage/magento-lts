<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Adminhtml
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

    protected function _setCollectionOrder($column)
    {
        $collection = $this->getCollection();
        if ($collection) {
            $columnIndex = $column->getFilterIndex() ?: $column->getIndex();
            $collection->setOrder($columnIndex, strtoupper($column->getDir()));

            if ($columnIndex === 'region_table.code') {
                $collection->addOrder('code', strtoupper($column->getDir()));
            }
        }
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('code', [
            'header'        => Mage::helper('tax')->__('Tax Identifier'),
            'header_export' => Mage::helper('tax')->__('Code'),
            'align'         => 'left',
            'index'         => 'code',
            'filter_index'  => 'main_table.code',
        ]);

        $this->addColumn('tax_country_id', [
            'header'        => Mage::helper('tax')->__('Country'),
            'type'          => 'country',
            'align'         => 'left',
            'index'         => 'tax_country_id',
            'filter_index'  => 'main_table.tax_country_id',
            'renderer'      => 'adminhtml/tax_rate_grid_renderer_country',
            'sortable'      => false
        ]);

        $this->addColumn('region_name', [
            'header'        => Mage::helper('tax')->__('State/Region'),
            'header_export' => Mage::helper('tax')->__('State'),
            'align'         => 'left',
            'index'         => 'region_name',
            'filter_index'  => 'region_table.code',
            'default'       => '*',
        ]);

        $this->addColumn('tax_postcode', [
            'header'        => Mage::helper('tax')->__('Zip/Post Code'),
            'align'         => 'left',
            'index'         => 'tax_postcode',
            'default'       => '*',
        ]);

        $this->addColumn('rate', [
            'header'        => Mage::helper('tax')->__('Rate'),
            'index'         => 'rate',
            'type'          => 'number',
            'default'       => '0.00',
            'renderer'      => 'adminhtml/tax_rate_grid_renderer_data',
        ]);

        $this->addExportType('*/*/exportCsv', Mage::helper('tax')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('tax')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * @param Mage_Tax_Model_Calculation_Rate $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['rate' => $row->getTaxCalculationRateId()]);
    }
}
