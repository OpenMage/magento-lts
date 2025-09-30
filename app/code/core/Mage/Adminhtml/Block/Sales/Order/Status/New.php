<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Status_New extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'status';
        $this->_controller = 'sales_order_status';
        $this->_mode = 'new';

        parent::__construct();
        $this->_updateButton('save', 'label', Mage::helper('sales')->__('Save Status'));
        $this->_removeButton('delete');
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('sales')->__('New Order Status');
    }
}
