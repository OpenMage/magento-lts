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
 * Order Shipments grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_View_Tab_Shipments extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('order_shipments');
        $this->setUseAjax(true);
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'sales/order_shipment_grid_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass())
            ->addFieldToSelect('entity_id')
            ->addFieldToSelect('created_at')
            ->addFieldToSelect('increment_id')
            ->addFieldToSelect('total_qty')
            ->addFieldToSelect('shipping_name')
            ->setOrderFilter($this->getOrder())
        ;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', [
            'header' => Mage::helper('sales')->__('Shipment #'),
            'index' => 'increment_id',
        ]);

        $this->addColumn('shipping_name', [
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
        ]);

        $this->addColumn('created_at', [
            'header' => Mage::helper('sales')->__('Date Shipped'),
            'index' => 'created_at',
            'type' => 'datetime',
        ]);

        $this->addColumn('total_qty', [
            'header' => Mage::helper('sales')->__('Total Qty'),
            'index' => 'total_qty',
            'type'  => 'number',
        ]);

        return parent::_prepareColumns();
    }

    /**
     * Retrieve order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/sales_order_shipment/view',
            [
                'shipment_id' => $row->getId(),
                'order_id'  => $row->getOrderId()
            ]
        );
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/shipments', ['_current' => true]);
    }

    public function getTabLabel()
    {
        return Mage::helper('sales')->__('Shipments');
    }

    public function getTabTitle()
    {
        return Mage::helper('sales')->__('Shipments');
    }

    public function canShowTab()
    {
        if ($this->getOrder()->getIsVirtual()) {
            return false;
        }
        return Mage::getSingleton('admin/session')->isAllowed('sales/shipment');
    }

    public function isHidden()
    {
        return false;
    }
}
