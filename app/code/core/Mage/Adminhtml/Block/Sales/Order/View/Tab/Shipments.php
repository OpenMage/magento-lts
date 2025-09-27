<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Order Shipments grid
 *
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
                'order_id'  => $row->getOrderId(),
            ],
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
