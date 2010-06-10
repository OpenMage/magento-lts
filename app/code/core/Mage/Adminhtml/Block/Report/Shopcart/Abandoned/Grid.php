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
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml abandoned shopping carts report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Shopcart_Abandoned_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('gridAbandoned');
    }

    protected function _prepareCollection()
    {
        if ($this->getRequest()->getParam('website')) {
            $storeIds = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
        } else if ($this->getRequest()->getParam('group')) {
            $storeIds = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
        } else if ($this->getRequest()->getParam('store')) {
            $storeIds = array((int)$this->getRequest()->getParam('store'));
        } else {
            $storeIds = '';
        }

        $collection = Mage::getResourceModel('reports/quote_collection');

        $filter = $this->getParam($this->getVarNameFilter(), array());
        if ($filter) {
            $filter = base64_decode($filter);
            parse_str(urldecode($filter), $data);
        }

        if (!empty($data)) {
            $collection->prepareForAbandonedReport($storeIds, $data);
        } else {
            $collection->prepareForAbandonedReport($storeIds);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _addColumnFilterToCollection($column)
    {
        $field = ( $column->getFilterIndex() ) ? $column->getFilterIndex() : $column->getIndex();
        $skip = array('subtotal', 'customer_name', 'email'/*, 'created_at', 'updated_at'*/);

        if (in_array($field, $skip)) {
            return $this;
        }

        parent::_addColumnFilterToCollection($column);
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('customer_name', array(
            'header'    =>Mage::helper('reports')->__('Customer Name'),
            'index'     =>'customer_name',
            'sortable'  =>false
        ));

        $this->addColumn('email', array(
            'header'    =>Mage::helper('reports')->__('Email'),
            'index'     =>'email',
            'sortable'  =>false
        ));

        $this->addColumn('items_count', array(
            'header'    =>Mage::helper('reports')->__('Number of Items'),
            'width'     =>'80px',
            'align'     =>'right',
            'index'     =>'items_count',
            'sortable'  =>false,
            'type'      =>'number'
        ));

        $this->addColumn('items_qty', array(
            'header'    =>Mage::helper('reports')->__('Quantity of Items'),
            'width'     =>'80px',
            'align'     =>'right',
            'index'     =>'items_qty',
            'sortable'  =>false,
            'type'      =>'number'
        ));

        $this->addColumn('subtotal', array(
            'header'    =>Mage::helper('reports')->__('Subtotal'),
            'width'     =>'80px',
            'type'      =>'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'index'     =>'subtotal',
            'sortable'  =>false,
            'renderer'  =>'adminhtml/report_grid_column_renderer_currency'
        ));

        $this->addColumn('coupon_code', array(
            'header'    =>Mage::helper('reports')->__('Applied Coupon'),
            'width'     =>'80px',
            'index'     =>'coupon_code',
            'sortable'  =>false
        ));

        $this->addColumn('created_at', array(
            'header'    =>Mage::helper('reports')->__('Created At'),
            'width'     =>'170px',
            'type'      =>'datetime',
            'index'     =>'created_at',
            'filter_index'=>'main_table.created_at',
            'sortable'  =>false
        ));

        $this->addColumn('updated_at', array(
            'header'    =>Mage::helper('reports')->__('Updated At'),
            'width'     =>'170px',
            'type'      =>'datetime',
            'index'     =>'updated_at',
            'filter_index'=>'main_table.updated_at',
            'sortable'  =>false
        ));

        $this->addColumn('remote_ip', array(
            'header'    =>Mage::helper('reports')->__('IP Address'),
            'width'     =>'80px',
            'index'     =>'remote_ip',
            'sortable'  =>false
        ));

        $this->addExportType('*/*/exportAbandonedCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportAbandonedExcel', Mage::helper('reports')->__('Excel'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/customer/edit', array('id'=>$row->getCustomerId(), 'active_tab'=>'cart'));
    }
}
