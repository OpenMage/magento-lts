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
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml customer orders grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_Orders extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Mage_Adminhtml_Block_Customer_Edit_Tab_Orders constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_orders_grid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('desc');
        $this->setUseAjax(true);
    }

    /**
     * @inheritDoc
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('sales/order_grid_collection')
            ->addFieldToFilter('customer_id', Mage::registry('current_customer')->getId())
            ->setIsCustomerMode(true);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', [
            'header'    => Mage::helper('customer')->__('Order #'),
            'align'     => 'center',
            'index'     => 'increment_id',
            'width'     => '100px',
        ]);

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', [
                'header'     => Mage::helper('customer')->__('Purchased From (Store)'),
                'index'      => 'store_id',
                'type'       => 'store',
                'store_view' => true,
                'display_deleted' => true,
            ]);
        }

        $this->addColumn('created_at', [
            'header'    => Mage::helper('customer')->__('Purchased On'),
            'index'     => 'created_at',
            'type'      => 'datetime',
        ]);

        $this->addColumn('billing_name', [
            'header'    => Mage::helper('customer')->__('Bill to Name'),
            'index'     => 'billing_name',
        ]);

        $this->addColumn('shipping_name', [
            'header'    => Mage::helper('customer')->__('Ship to Name'),
            'index'     => 'shipping_name',
        ]);

        $this->addColumn('grand_total', [
            'header'    => Mage::helper('customer')->__('Amount'),
            'index'     => 'grand_total',
            'type'      => 'currency',
            'currency'  => 'order_currency_code',
        ]);

        $this->addColumn('status', [
            'header'    => Mage::helper('adminhtml')->__('Status'),
            'index'     => 'status',
            'type'      => 'options',
            'width'     => '150px',
            'options'   => Mage::getSingleton('sales/order_config')->getStatuses(),
            'frame_callback' => [$this, 'decorateStatus'],
        ]);

        if (Mage::helper('sales/reorder')->isAllow()) {
            $this->addColumn('action', [
                'header'    => ' ',
                'filter'    => false,
                'sortable'  => false,
                'width'     => '100px',
                'renderer'  => 'adminhtml/sales_reorder_renderer_action',
            ]);
        }

        return parent::_prepareColumns();
    }

    /**
     * @param Varien_Object $row
     * @return string
     */
    public function getRowClass($row)
    {
        return ($row->getId() == $this->getRequest()->getParam('order_id')) ? 'bold' : '';
    }

    /**
     * @param Varien_Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/sales_order/view', ['order_id' => $row->getId()]);
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl($this->getRequest()->getParam('order_id') ? '*/sales_order/ordersHistory' : '*/*/orders', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function decorateStatus($value, $row, $column, $isExport)
    {
        return $isExport ? $value :
            '<span class="grid-' . $row->getData('status') . '" title="' . $row->getData('status') . '">' . $value . '</span>';
    }
}
