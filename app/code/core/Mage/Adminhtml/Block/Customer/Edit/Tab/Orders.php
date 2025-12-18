<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml customer orders grid block
 *
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
            ->addFieldToSelect('entity_id')
            ->addFieldToSelect('increment_id')
            ->addFieldToSelect('customer_id')
            ->addFieldToSelect('created_at')
            ->addFieldToSelect('grand_total')
            ->addFieldToSelect('order_currency_code')
            ->addFieldToSelect('status')
            ->addFieldToSelect('store_id')
            ->addFieldToSelect('billing_name')
            ->addFieldToSelect('shipping_name')
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
            'width'     => '100',
            'index'     => 'increment_id',
        ]);

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', [
                'header'    => Mage::helper('customer')->__('Bought From'),
                'type'      => 'store',
            ]);
        }

        $this->addColumn('created_at', [
            'header'    => Mage::helper('customer')->__('Purchase On'),
            'index'     => 'created_at',
            'type'      => 'datetime',
        ]);

        $this->addColumn('billing_name', [
            'header'    => Mage::helper('customer')->__('Bill to Name'),
            'index'     => 'billing_name',
        ]);

        $this->addColumn('shipping_name', [
            'header'    => Mage::helper('customer')->__('Shipped to Name'),
            'index'     => 'shipping_name',
        ]);

        $this->addColumn('grand_total', [
            'header'    => Mage::helper('customer')->__('Order Total'),
            'index'     => 'grand_total',
            'type'      => 'currency',
            'currency'  => 'order_currency_code',
        ]);

        $this->addColumn('status', [
            'header' => Mage::helper('customer')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '150px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ]);

        if (Mage::helper('sales/reorder')->isAllow()) {
            $this->addColumn('action', [
                'type'      => 'action',
                'header'    => ' ',
                'width'     => '100',
                'renderer'  => 'adminhtml/sales_reorder_renderer_action',
            ]);
        }

        return parent::_prepareColumns();
    }

    /**
     * @param  Varien_Object $row
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
        return $this->getUrl('*/*/orders', ['_current' => true]);
    }
}
