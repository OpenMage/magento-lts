<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml shipment create
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Shipment_Create extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'sales_order_shipment';
        $this->_mode = 'create';

        parent::__construct();

        $this->_removeButton('save');
        $this->_removeButton('delete');
    }

    /**
     * Retrieve shipment model instance
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function getShipment()
    {
        return Mage::registry('current_shipment');
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('sales')->__(
            'New Shipment for Order #%s',
            $this->escapeHtml($this->getShipment()->getOrder()->getRealOrderId()),
        );
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/sales_order/view', ['order_id' => $this->getShipment()->getOrderId()]);
    }
}
