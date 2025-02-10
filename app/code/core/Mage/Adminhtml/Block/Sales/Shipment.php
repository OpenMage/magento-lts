<?php
/**
 * Adminhtml sales shipments block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Shipment extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'sales_shipment';
        $this->_headerText = Mage::helper('sales')->__('Shipments');
        parent::__construct();
        $this->_removeButton('add');
    }
}
